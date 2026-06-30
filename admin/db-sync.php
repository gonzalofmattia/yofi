<?php
ob_start();
require_once __DIR__ . '/include/session_init.php';
require_once __DIR__ . '/include/includes.php';
require_once __DIR__ . '/check_session.php';
ob_end_flush();

if (!defined('IS_LOCAL') || !IS_LOCAL) {
    http_response_code(403);
    exit('Acceso denegado. Esta herramienta solo está disponible en entorno local.');
}

define('DBSYNC_BACKUP_DIR', __DIR__ . '/backups/');
define('DBSYNC_HISTORY_FILE', DBSYNC_BACKUP_DIR . 'history.jsonl');

if (!is_dir(DBSYNC_BACKUP_DIR)) {
    mkdir(DBSYNC_BACKUP_DIR, 0755, true);
}
if (!file_exists(DBSYNC_BACKUP_DIR . '.htaccess')) {
    file_put_contents(DBSYNC_BACKUP_DIR . '.htaccess', "Options -Indexes\nDeny from all\n");
}

// ── Binaries ─────────────────────────────────────────────────────────────────

function dbsync_find_binary(string $name): string
{
    $laragonBase = 'C:\\laragon\\bin\\mysql';
    if (is_dir($laragonBase)) {
        $dirs = glob($laragonBase . '\\*', GLOB_ONLYDIR) ?: [];
        rsort($dirs, SORT_NATURAL);
        foreach ($dirs as $d) {
            $bin = $d . '\\bin\\' . $name . '.exe';
            if (file_exists($bin)) {
                return $bin;
            }
        }
    }
    $out = [];
    exec('where ' . escapeshellarg($name) . ' 2>NUL', $out);
    return !empty($out[0]) ? trim($out[0]) : $name;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function dbsync_write_defaults_file(): string
{
    $tmp = tempnam(sys_get_temp_dir(), 'yofi_dbsync_');
    file_put_contents($tmp, "[client]\nhost=" . DB_HOST . "\nuser=" . DB_USER . "\npassword=" . DB_PASSWORD . "\n");
    return $tmp;
}

function dbsync_format_size(int $bytes): string
{
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

function dbsync_list_backups(): array
{
    $files = glob(DBSYNC_BACKUP_DIR . '*.sql') ?: [];
    $result = [];
    foreach ($files as $f) {
        $result[] = [
            'path' => $f,
            'name' => basename($f),
            'size' => (int) filesize($f),
            'date' => (int) filemtime($f),
        ];
    }
    usort($result, fn($a, $b) => $b['date'] - $a['date']);
    return $result;
}

function dbsync_append_history(array $entry): void
{
    file_put_contents(DBSYNC_HISTORY_FILE, json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);
}

function dbsync_read_history(int $limit = 30): array
{
    if (!file_exists(DBSYNC_HISTORY_FILE)) return [];
    $lines = file(DBSYNC_HISTORY_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $lines = array_slice(array_reverse($lines), 0, $limit);
    return array_values(array_filter(array_map(fn($l) => json_decode($l, true), $lines)));
}

function dbsync_create_backup(string $label = 'manual'): array
{
    $mysqldump = dbsync_find_binary('mysqldump');
    $ts        = date('Ymd-His');
    $filename  = 'backup-' . $label . '-' . $ts . '.sql';
    $filepath  = DBSYNC_BACKUP_DIR . $filename;

    $tmp     = dbsync_write_defaults_file();
    $tmpNorm = str_replace('\\', '/', $tmp);
    $outNorm = str_replace('\\', '/', $filepath);

    $cmd = sprintf(
        '"%s" "--defaults-extra-file=%s" --single-transaction --quick --routines --triggers %s > "%s" 2>&1',
        $mysqldump,
        $tmpNorm,
        escapeshellarg(DB_DATABASE),
        $outNorm
    );

    $output     = [];
    $returnCode = 0;
    exec($cmd, $output, $returnCode);
    @unlink($tmp);

    $success = $returnCode === 0 && file_exists($filepath) && filesize($filepath) > 0;

    dbsync_append_history([
        'id'     => uniqid('bk_'),
        'ts'     => date('c'),
        'action' => 'create_backup',
        'label'  => $label,
        'file'   => $filename,
        'status' => $success ? 'ok' : 'error',
        'note'   => implode(' | ', array_filter($output)),
    ]);

    return ['success' => $success, 'file' => $filename, 'output' => implode("\n", $output)];
}

function dbsync_drop_all_tables(): string
{
    global $con;
    $result = mysqli_query($con, 'SHOW TABLES');
    if (!$result) return 'No se pudo obtener la lista de tablas.';

    $tables = [];
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = '`' . $row[0] . '`';
    }
    if (empty($tables)) return '';

    mysqli_query($con, 'SET FOREIGN_KEY_CHECKS=0');
    foreach ($tables as $t) {
        mysqli_query($con, 'DROP TABLE IF EXISTS ' . $t);
    }
    mysqli_query($con, 'SET FOREIGN_KEY_CHECKS=1');

    return 'Eliminadas ' . count($tables) . ' tablas antes de importar.';
}

function dbsync_import_file(string $filepath): array
{
    // Drop all existing tables first so CREATE TABLE never conflicts
    $dropNote = dbsync_drop_all_tables();

    $mysql   = dbsync_find_binary('mysql');
    $tmp     = dbsync_write_defaults_file();
    $tmpNorm = str_replace('\\', '/', $tmp);
    $srcNorm = str_replace('\\', '/', $filepath);

    $cmd = sprintf(
        '"%s" "--defaults-extra-file=%s" %s < "%s" 2>&1',
        $mysql,
        $tmpNorm,
        escapeshellarg(DB_DATABASE),
        $srcNorm
    );

    $output     = [];
    $returnCode = 0;
    exec($cmd, $output, $returnCode);
    @unlink($tmp);

    if ($dropNote) {
        array_unshift($output, $dropNote);
    }

    return ['success' => $returnCode === 0, 'output' => implode("\n", $output)];
}

// ── POST handlers ─────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF already validated by check_session.php
    $action      = $_POST['action'] ?? '';
    $redirectBase = app_path('admin/db-sync.php');

    if ($action === 'create_backup') {
        $r   = dbsync_create_backup('manual');
        $msg = $r['success']
            ? 'Backup creado: ' . $r['file']
            : 'Error al crear backup. ' . $r['output'];
        header('Location: ' . $redirectBase . '?status=' . ($r['success'] ? 'ok' : 'err') . '&msg=' . urlencode($msg));
        exit;
    }

    if ($action === 'import_upload') {
        if (empty($_FILES['sqlfile']) || $_FILES['sqlfile']['error'] !== UPLOAD_ERR_OK) {
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode('No se recibió ningún archivo válido.'));
            exit;
        }
        $originalName = basename($_FILES['sqlfile']['name']);
        if (!preg_match('/\.sql$/i', $originalName)) {
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode('Solo se aceptan archivos .sql'));
            exit;
        }
        $safeName    = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $uploadPath  = DBSYNC_BACKUP_DIR . 'upload-' . date('Ymd-His') . '-' . $safeName;

        if (!move_uploaded_file($_FILES['sqlfile']['tmp_name'], $uploadPath)) {
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode('Error al guardar el archivo subido.'));
            exit;
        }

        $backup = dbsync_create_backup('pre-import');
        if (!$backup['success']) {
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode('Error al crear backup previo: ' . $backup['output']));
            exit;
        }

        $import = dbsync_import_file($uploadPath);

        dbsync_append_history([
            'id'            => uniqid('imp_'),
            'ts'            => date('c'),
            'action'        => 'import_upload',
            'file_uploaded' => basename($uploadPath),
            'backup_before' => $backup['file'],
            'status'        => $import['success'] ? 'ok' : 'error',
            'note'          => $import['output'],
        ]);

        if ($import['success']) {
            $msg = 'Importación exitosa. Backup previo guardado: ' . $backup['file'];
            header('Location: ' . $redirectBase . '?status=ok&msg=' . urlencode($msg));
        } else {
            $msg = 'Error en la importación: ' . $import['output'];
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode($msg));
        }
        exit;
    }

    if ($action === 'restore_backup') {
        $filename = basename($_POST['filename'] ?? '');
        if (!preg_match('/^[a-zA-Z0-9._-]+\.sql$/i', $filename)) {
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode('Nombre de archivo inválido.'));
            exit;
        }
        $filepath = DBSYNC_BACKUP_DIR . $filename;
        if (!file_exists($filepath)) {
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode('Backup no encontrado.'));
            exit;
        }

        $backup = dbsync_create_backup('pre-restore');
        if (!$backup['success']) {
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode('Error al crear backup previo: ' . $backup['output']));
            exit;
        }

        $import = dbsync_import_file($filepath);

        dbsync_append_history([
            'id'            => uniqid('rst_'),
            'ts'            => date('c'),
            'action'        => 'restore_backup',
            'file_restored' => $filename,
            'backup_before' => $backup['file'],
            'status'        => $import['success'] ? 'ok' : 'error',
            'note'          => $import['output'],
        ]);

        if ($import['success']) {
            $msg = 'Restauración exitosa desde: ' . $filename;
            header('Location: ' . $redirectBase . '?status=ok&msg=' . urlencode($msg));
        } else {
            $msg = 'Error al restaurar: ' . $import['output'];
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode($msg));
        }
        exit;
    }

    if ($action === 'delete_backup') {
        $filename = basename($_POST['filename'] ?? '');
        if (!preg_match('/^[a-zA-Z0-9._-]+\.sql$/i', $filename)) {
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode('Nombre de archivo inválido.'));
            exit;
        }
        $filepath = DBSYNC_BACKUP_DIR . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
            header('Location: ' . $redirectBase . '?status=ok&msg=' . urlencode('Backup eliminado: ' . $filename));
        } else {
            header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode('Archivo no encontrado.'));
        }
        exit;
    }

    header('Location: ' . $redirectBase . '?status=err&msg=' . urlencode('Acción desconocida.'));
    exit;
}

// ── View ──────────────────────────────────────────────────────────────────────

$pageStatus = $_GET['status'] ?? '';
$pageMsg    = htmlspecialchars($_GET['msg'] ?? '', ENT_QUOTES, 'UTF-8');
$backups    = dbsync_list_backups();
$history    = dbsync_read_history(25);
$csrfToken  = htmlspecialchars($_SESSION['admin_csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');

$pageTitle = 'Sincronización BD';
include __DIR__ . '/header.php';
?>

<div class="admin-section-header">
    <div>
        <h1><i class="bi bi-database-gear me-2"></i>Sincronización de Base de Datos</h1>
        <p class="subtitle">Entorno local &mdash; importá un dump de producción para mantener tu copia local sincronizada.</p>
    </div>
</div>

<?php if ($pageStatus !== '' && $pageMsg !== ''): ?>
<div class="alert alert-<?= $pageStatus === 'ok' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-<?= $pageStatus === 'ok' ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2"></i>
    <?= $pageMsg ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">

    <!-- Importar dump -->
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0"><i class="bi bi-cloud-upload me-2"></i>Importar dump de producción</h5>
            </div>
            <div class="admin-card-body">
                <p class="text-muted small mb-3">
                    Exportá la BD desde producción (estructura + datos, archivo <code>.sql</code>), subila acá y reemplazará la BD local
                    <strong><?= htmlspecialchars(DB_DATABASE, ENT_QUOTES) ?></strong>.
                    Se genera un backup automático antes de importar.
                </p>
                <form method="post" enctype="multipart/form-data" id="formImport">
                    <input type="hidden" name="action" value="import_upload">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Archivo SQL de producción</label>
                        <input type="file" name="sqlfile" accept=".sql" class="form-control" required id="sqlfileInput">
                        <div class="form-text">Máx. <?= ini_get('upload_max_filesize') ?> — si el dump es más grande, aumentá <code>upload_max_filesize</code> y <code>post_max_size</code> en <code>php.ini</code>.</div>
                    </div>
                    <div class="alert alert-warning py-2 px-3 small mb-3 mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Esto <strong>reemplaza toda la base de datos local</strong>. Se crea un backup previo automático por si necesitás revertir.
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" id="btnImport" onclick="return confirmImport()">
                            <i class="bi bi-upload me-1"></i> Importar y sincronizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Backup manual + Info -->
    <div class="col-12 col-md-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="mb-0"><i class="bi bi-floppy me-2"></i>Backup manual</h5>
            </div>
            <div class="admin-card-body">
                <p class="text-muted small mb-3">Creá un snapshot de la BD local en este momento.</p>
                <form method="post">
                    <input type="hidden" name="action" value="create_backup">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-download me-1"></i> Crear backup ahora
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Conexión actual</h5>
            </div>
            <div class="admin-card-body">
                <table class="table table-sm table-borderless mb-0 small">
                    <tr><td class="text-muted" style="width:110px">Host</td><td><code><?= htmlspecialchars(DB_HOST, ENT_QUOTES) ?></code></td></tr>
                    <tr><td class="text-muted">Base de datos</td><td><code><?= htmlspecialchars(DB_DATABASE, ENT_QUOTES) ?></code></td></tr>
                    <tr><td class="text-muted">Usuario</td><td><code><?= htmlspecialchars(DB_USER, ENT_QUOTES) ?></code></td></tr>
                    <tr><td class="text-muted">Backups guardados</td><td><code><?= count($backups) ?></code></td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Lista de backups -->
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-archive me-2"></i>Backups guardados</h5>
                <small class="text-muted"><?= count($backups) ?> archivo(s) en <code>admin/backups/</code></small>
            </div>
            <div class="admin-card-body p-0">
                <?php if (empty($backups)): ?>
                <p class="text-muted p-4 mb-0">No hay backups todavía. Creá uno manualmente o importá un dump.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Archivo</th>
                                <th>Tamaño</th>
                                <th>Fecha</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($backups as $bk): ?>
                            <tr>
                                <td class="small font-monospace"><?= htmlspecialchars($bk['name'], ENT_QUOTES) ?></td>
                                <td class="small text-muted"><?= dbsync_format_size($bk['size']) ?></td>
                                <td class="small text-muted"><?= date('d/m/Y H:i', $bk['date']) ?></td>
                                <td class="text-end pe-3">
                                    <form method="post" class="d-inline" onsubmit="return confirm('¿Restaurar «<?= htmlspecialchars($bk['name'], ENT_QUOTES) ?>»?\n\nSe creará un backup previo automático antes de restaurar.')">
                                        <input type="hidden" name="action" value="restore_backup">
                                        <input type="hidden" name="filename" value="<?= htmlspecialchars($bk['name'], ENT_QUOTES) ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Restaurar esta BD">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    <form method="post" class="d-inline ms-1" onsubmit="return confirm('¿Eliminar «<?= htmlspecialchars($bk['name'], ENT_QUOTES) ?>»?\n\nEsta acción no se puede deshacer.')">
                                        <input type="hidden" name="action" value="delete_backup">
                                        <input type="hidden" name="filename" value="<?= htmlspecialchars($bk['name'], ENT_QUOTES) ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar backup">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Historial -->
    <?php if (!empty($history)): ?>
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de operaciones</h5>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Acción</th>
                                <th>Archivo</th>
                                <th>Backup previo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($history as $h):
                            $hFile = $h['file'] ?? $h['file_uploaded'] ?? $h['file_restored'] ?? '—';
                            $hPrev = $h['backup_before'] ?? '—';
                            $hOk   = ($h['status'] ?? '') === 'ok';
                            $hLabel = match($h['action'] ?? '') {
                                'create_backup'  => 'Backup',
                                'import_upload'  => 'Importación',
                                'restore_backup' => 'Restauración',
                                default          => $h['action'] ?? '—',
                            };
                        ?>
                            <tr>
                                <td class="small text-muted text-nowrap"><?= date('d/m/Y H:i', strtotime($h['ts'] ?? 'now')) ?></td>
                                <td class="small"><?= htmlspecialchars($hLabel, ENT_QUOTES) ?></td>
                                <td class="small font-monospace text-truncate" style="max-width:220px" title="<?= htmlspecialchars($hFile, ENT_QUOTES) ?>"><?= htmlspecialchars($hFile, ENT_QUOTES) ?></td>
                                <td class="small font-monospace text-muted text-truncate" style="max-width:200px" title="<?= htmlspecialchars($hPrev, ENT_QUOTES) ?>"><?= htmlspecialchars($hPrev, ENT_QUOTES) ?></td>
                                <td><span class="badge bg-<?= $hOk ? 'success' : 'danger' ?>"><?= $hOk ? 'OK' : 'Error' ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
function confirmImport() {
    var file = document.getElementById('sqlfileInput');
    if (!file || !file.files.length) return true;
    return confirm(
        'Vas a reemplazar toda la base de datos local con el archivo:\n\n' +
        file.files[0].name + '\n\n' +
        'Se creará un backup automático antes de importar.\n\n¿Continuás?'
    );
}
</script>

<?php include __DIR__ . '/pie.php'; ?>
