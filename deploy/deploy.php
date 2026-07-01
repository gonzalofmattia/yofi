#!/usr/bin/env php
<?php

/**
 * Deploy de Yofi a producción (DonWeb) vía FTP.
 *
 * Detección de cambios, flags y flujo recomendado: ver deploy/README.md
 *
 * Uso:
 *   php deploy/deploy.php                  Deploy completo (dump + archivos)
 *   php deploy/deploy.php --files-only     Solo subir archivos (sin exportar BD)
 *   php deploy/deploy.php --config-only    Solo subir configs .production → .local.php
 *   php deploy/deploy.php --full           Subir todo (ignora manifest incremental)
 *   php deploy/deploy.php --dry-run        Listar qué se subiría, sin FTP ni dump
 *   php deploy/deploy.php --force-db       Omitir confirmación si prod ya tiene datos
 *
 * Requisitos previos:
 *   1. config/deploy.local.php (copiar desde config/deploy.local.php.example)
 *   2. config/*.production.php completados (ver config/*.production.php.example)
 *   3. mysqldump disponible (Laragon lo incluye)
 *   4. Extensión FTP de PHP habilitada
 */

declare(strict_types=1);

const YOFI_ROOT = __DIR__ . '/..';

$options = parseDeployOptions($argv);
$startedAt = date('Y-m-d H:i:s');

echo "=== Deploy Yofi → DonWeb ===\n";
echo "Inicio: {$startedAt}\n\n";

$deployConfig = loadDeployConfig();
validateProductionConfigs();

$dumpResult = ['ok' => false, 'path' => null, 'size' => 0, 'skipped' => false];

if ($options['files_only']) {
    echo "[BD] Omitido (--files-only)\n\n";
    $dumpResult['skipped'] = true;
} else {
    $prodHasData = checkProductionDatabaseHasData($deployConfig);
    if ($prodHasData === true && !$options['force_db']) {
        echo "⚠  ADVERTENCIA: La base de producción parece tener tablas/datos.\n";
        echo "   Importar el dump en phpMyAdmin SOBREESCRIBIRÁ la base remota.\n";
        echo "   Para continuar igual, usá: php deploy/deploy.php --force-db\n";
        echo "   Para solo actualizar archivos: php deploy/deploy.php --files-only\n\n";
        exit(1);
    }
    if ($prodHasData === null) {
        echo "[BD] No se pudo verificar la base remota (acceso MySQL externo bloqueado — normal en DonWeb).\n";
        echo "     Si prod ya tiene datos reales, importá el SQL con cuidado en phpMyAdmin.\n\n";
    }

    $dumpResult = exportLocalDatabase($deployConfig, $options['dry_run']);
}

$uploadStats = ['uploaded' => 0, 'skipped' => 0, 'errors' => 0, 'config_files' => 0];

if ($options['dry_run']) {
    echo "\n[DRY-RUN] Archivos que se subirían:\n";
    $uploadStats = dryRunFileList($deployConfig, $options);
} else {
    echo "\n[FTP] Conectando al servidor...\n";
    $uploadStats = uploadViaFtp($deployConfig, $options);
}

$finishedAt = date('Y-m-d H:i:s');
logDeploySummary($startedAt, $finishedAt, $dumpResult, $uploadStats, $options);

printFinalSummary($dumpResult, $uploadStats, $options);

exit($uploadStats['errors'] > 0 ? 1 : 0);

// ---------------------------------------------------------------------------
// Opciones CLI
// ---------------------------------------------------------------------------

function parseDeployOptions(array $argv): array
{
    return [
        'files_only' => in_array('--files-only', $argv, true),
        'config_only' => in_array('--config-only', $argv, true),
        'force_full' => in_array('--full', $argv, true),
        'dry_run' => in_array('--dry-run', $argv, true),
        'force_db' => in_array('--force-db', $argv, true),
    ];
}

// ---------------------------------------------------------------------------
// Configuración
// ---------------------------------------------------------------------------

function loadDeployConfig(): array
{
    $path = YOFI_ROOT . '/config/deploy.local.php';

    if (!is_file($path)) {
        fwrite(STDERR, "Error: falta config/deploy.local.php\n\n");
        fwrite(STDERR, "Creá el archivo copiando config/deploy.local.php.example y completá:\n");
        fwrite(STDERR, "  - ftp.host, ftp.user, ftp.password, ftp.remote_path\n");
        fwrite(STDERR, "  - local_db (host, user, password, name)\n");
        fwrite(STDERR, "  - production_db (host, user, password, name)\n\n");
        exit(1);
    }

    $config = require $path;

    if (!is_array($config)) {
        fwrite(STDERR, "Error: config/deploy.local.php debe retornar un array.\n");
        exit(1);
    }

    foreach (['ftp', 'local_db', 'production_db'] as $section) {
        if (empty($config[$section]) || !is_array($config[$section])) {
            fwrite(STDERR, "Error: falta la sección '{$section}' en config/deploy.local.php\n");
            exit(1);
        }
    }

    $ftpRequired = ['host', 'user', 'password', 'remote_path'];
    foreach ($ftpRequired as $key) {
        if (!isset($config['ftp'][$key]) || trim((string)$config['ftp'][$key]) === '') {
            fwrite(STDERR, "Error: falta ftp.{$key} en config/deploy.local.php\n");
            exit(1);
        }
    }

    $config['ftp']['port'] = (int)($config['ftp']['port'] ?? 21);
    $config['ftp']['passive'] = (bool)($config['ftp']['passive'] ?? true);
    // DonWeb/Ferozo suele exigir FTPS (TLS explícito) — FileZilla lo usa por defecto.
    $config['ftp']['ssl'] = (bool)($config['ftp']['ssl'] ?? true);
    $config['exclude_tables_from_dump'] = $config['exclude_tables_from_dump'] ?? ['tbl_sessions'];

    return $config;
}

function productionConfigMap(): array
{
    return [
        'config/db.production.php' => 'config/db.local.php',
        'config/mercadopago.production.php' => 'config/mercadopago.local.php',
        'config/smtp.production.php' => 'config/smtp.local.php',
        'config/zipnova.production.php' => 'config/zipnova.local.php',
        'config/app.production.php' => 'config/app.local.php',
    ];
}

function validateProductionConfigs(): void
{
    $missing = [];
    foreach (productionConfigMap() as $local => $remote) {
        if (!is_file(YOFI_ROOT . '/' . $local)) {
            $missing[] = $local;
        }
    }

    if ($missing !== []) {
        fwrite(STDERR, "Error: faltan archivos de producción. Creálos antes del deploy:\n\n");
        foreach ($missing as $file) {
            $example = preg_replace('/\.production\.php$/', '.production.php.example', $file);
            fwrite(STDERR, "  - {$file}");
            if (is_file(YOFI_ROOT . '/' . $example)) {
                fwrite(STDERR, "  (copiá desde {$example})");
            }
            fwrite(STDERR, "\n");
        }
        fwrite(STDERR, "\nCompletá cada uno con credenciales de producción y volvé a correr el deploy.\n");
        exit(1);
    }

    echo "[OK] Archivos .production.php encontrados\n\n";
}

// ---------------------------------------------------------------------------
// Base de datos
// ---------------------------------------------------------------------------

function checkProductionDatabaseHasData(array $config): ?bool
{
    $db = $config['production_db'];
    $host = $db['host'] ?? '';
    $user = $db['user'] ?? '';
    $pass = $db['password'] ?? '';
    $name = $db['name'] ?? '';

    if ($host === '' || $user === '' || $name === '') {
        return null;
    }

    try {
        $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $stmt = $pdo->query(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = " . $pdo->quote($name)
        );
        $count = (int)$stmt->fetchColumn();

        return $count > 0;
    } catch (Throwable) {
        return null;
    }
}

function findMysqldump(): ?string
{
    $candidates = ['mysqldump'];

    $laragonGlob = glob('C:/laragon/bin/mysql/*/bin/mysqldump.exe') ?: [];
    $candidates = array_merge($candidates, $laragonGlob);

    foreach ($candidates as $bin) {
        if ($bin === 'mysqldump') {
            $out = [];
            $code = 0;
            exec('where mysqldump 2>nul', $out, $code);
            if ($code === 0 && !empty($out[0]) && is_file($out[0])) {
                return $out[0];
            }
            continue;
        }
        if (is_file($bin)) {
            return $bin;
        }
    }

    return null;
}

/** DDL para tablas excluidas del dump (solo estructura, sin datos de sesiones locales). */
function excludedTablesSchemaSql(array $tables): string
{
    $schemas = [
        'tbl_sessions' => <<<'SQL'

-- --------------------------------------------------------
-- Estructura tbl_sessions (datos locales excluidos del dump)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tbl_sessions` (
  `id` varchar(128) NOT NULL,
  `data` mediumblob NOT NULL,
  `last_access` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_last_access` (`last_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Sesiones PHP almacenadas en BD';

SQL,
    ];

    $sql = '';
    foreach ($tables as $table) {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        if ($table !== '' && isset($schemas[$table])) {
            $sql .= $schemas[$table];
        }
    }

    return $sql;
}

function exportLocalDatabase(array $config, bool $dryRun): array
{
    $result = ['ok' => false, 'path' => null, 'size' => 0, 'skipped' => false];

    $db = $config['local_db'];
    $host = $db['host'] ?? '127.0.0.1';
    $user = $db['user'] ?? 'root';
    $pass = $db['password'] ?? '';
    $name = $db['name'] ?? 'yofi';

    $dumpDir = YOFI_ROOT . '/deploy/dumps';
    if (!$dryRun && !is_dir($dumpDir)) {
        mkdir($dumpDir, 0755, true);
    }

    $timestamp = date('Y-m-d_His');
    $sqlFile = "{$dumpDir}/yofi_deploy_{$timestamp}.sql";
    $gzFile = $sqlFile . '.gz';

    echo "[BD] Exportando base local '{$name}'...\n";

    if ($dryRun) {
        echo "[DRY-RUN] Se generaría: deploy/dumps/yofi_deploy_{$timestamp}.sql.gz\n\n";
        $result['ok'] = true;
        $result['path'] = $gzFile;
        return $result;
    }

    $mysqldump = findMysqldump();
    if ($mysqldump === null) {
        fwrite(STDERR, "Error: no se encontró mysqldump. Verificá que Laragon/MySQL esté instalado.\n");
        exit(1);
    }

    $ignoreArgs = '';
    foreach ($config['exclude_tables_from_dump'] as $table) {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        if ($table !== '') {
            $ignoreArgs .= ' --ignore-table=' . $name . '.' . $table;
        }
    }

    $passArg = $pass !== '' ? '--password=' . escapeshellarg($pass) : '--password=';
    $cmd = escapeshellarg($mysqldump)
        . ' -h' . escapeshellarg($host)
        . ' -u' . escapeshellarg($user)
        . ' ' . $passArg
        . ' --single-transaction --routines --triggers --set-gtid-purged=OFF'
        . ' --default-character-set=utf8mb4'
        . $ignoreArgs
        . ' ' . escapeshellarg($name);

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($cmd, $descriptors, $pipes);
    if (!is_resource($process)) {
        fwrite(STDERR, "Error: no se pudo ejecutar mysqldump.\n");
        exit(1);
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($exitCode !== 0 || $stdout === false || $stdout === '') {
        fwrite(STDERR, "Error al exportar la base de datos.\n");
        if ($stderr !== '') {
            fwrite(STDERR, trim($stderr) . "\n");
        }
        exit(1);
    }

    $stdout .= excludedTablesSchemaSql($config['exclude_tables_from_dump'] ?? []);

    file_put_contents($sqlFile, $stdout);
    $gzData = gzencode($stdout, 9);
    if ($gzData === false) {
        fwrite(STDERR, "Error al comprimir el dump.\n");
        exit(1);
    }
    file_put_contents($gzFile, $gzData);
    unlink($sqlFile);

    $size = filesize($gzFile);
    $result['ok'] = true;
    $result['path'] = $gzFile;
    $result['size'] = $size ?: 0;

    echo "  ✓ Dump generado: deploy/dumps/" . basename($gzFile) . "\n";
    echo "  ✓ Tamaño: " . number_format($result['size'] / 1024, 1) . " KB\n";
    if ($config['exclude_tables_from_dump'] !== []) {
        echo "  ✓ Tablas excluidas: " . implode(', ', $config['exclude_tables_from_dump']) . "\n";
    }
    echo "\n";

    return $result;
}

// ---------------------------------------------------------------------------
// Exclusiones de archivos
// ---------------------------------------------------------------------------

function shouldExcludeFromDeploy(string $relativePath): bool
{
    $relativePath = str_replace('\\', '/', $relativePath);

    $exactExclude = [
        '.gitignore',
        '.env',
        'error_log.txt',
        'composer.lock',
    ];

    if (in_array($relativePath, $exactExclude, true)) {
        return true;
    }

    $patterns = [
        '#^\.git(/|$)#',
        '#^node_modules(/|$)#',
        '#^vendor(/|$)#',
        '#^deploy(/|$)#',
        '#^config/deploy\.local\.php$#',
        '#^config/deploy\.local\.php\.example$#',
        '#^config/[^/]+\.local\.php$#',
        '#^config/[^/]+\.production\.php$#',
        '#^config/[^/]+\.production\.php\.example$#',
        '#^scripts/test-#',
        '#^scripts/mp-config-check\.php$#',
        '#^db-probe\.php$#',
        '#^db-probe-result\.txt$#',
        '#^logs/.+\.log$#',
        '#^logs/.+\.txt$#',
        '#\.sql$#',
        '#\.sql\.gz$#',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $relativePath)) {
            return true;
        }
    }

    return false;
}

function collectDeployFiles(): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(YOFI_ROOT, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        $fullPath = $file->getPathname();
        $relative = ltrim(str_replace('\\', '/', substr($fullPath, strlen(YOFI_ROOT))), '/');

        if (shouldExcludeFromDeploy($relative)) {
            continue;
        }

        $files[] = $relative;
    }

    sort($files);

    return $files;
}

// ---------------------------------------------------------------------------
// Manifest incremental (solo sube archivos modificados desde el último deploy OK)
//
// El proyecto vive en git y el deploy siempre se hace desde main recién
// mergeado. Por eso la detección de cambios usa `git diff` contra el commit
// del último deploy en vez de mtime: un `git checkout`/`pull`/merge cambia el
// mtime de TODOS los archivos aunque el contenido sea idéntico, lo que hacía
// que el manifest anterior (size+mtime) nunca detectara "sin cambios" y
// terminara subiendo el proyecto entero por FTP en cada deploy.
//
// Los archivos config/*.production.php no están versionados (gitignored), así
// que para ellos se sigue usando un fingerprint propio, pero por hash de
// contenido (no por mtime) para que no dependa del filesystem.
// ---------------------------------------------------------------------------

function deployManifestPath(): string
{
    return YOFI_ROOT . '/deploy/.deploy-manifest.json';
}

function loadDeployManifest(): array
{
    $path = deployManifestPath();
    if (!is_file($path)) {
        return ['last_commit' => null, 'config_files' => []];
    }

    $data = json_decode((string) file_get_contents($path), true);
    if (!is_array($data)) {
        return ['last_commit' => null, 'config_files' => []];
    }

    return [
        'last_commit' => is_string($data['last_commit'] ?? null) ? $data['last_commit'] : null,
        'config_files' => is_array($data['config_files'] ?? null) ? $data['config_files'] : [],
    ];
}

function saveDeployManifest(string $commit, array $configFiles): void
{
    $payload = [
        'updated_at' => date('c'),
        'last_commit' => $commit,
        'config_files' => $configFiles,
    ];
    file_put_contents(
        deployManifestPath(),
        json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
    );
}

function configFileHash(string $absolutePath): ?string
{
    if (!is_file($absolutePath)) {
        return null;
    }

    $hash = hash_file('sha1', $absolutePath);

    return $hash !== false ? $hash : null;
}

// ---------------------------------------------------------------------------
// Git: detección de archivos modificados desde el último deploy
// ---------------------------------------------------------------------------

function runGit(array $args): ?string
{
    $cmd = 'git -C ' . escapeshellarg(YOFI_ROOT) . ' -c core.quotePath=false '
        . implode(' ', array_map('escapeshellarg', $args));

    $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $process = proc_open($cmd, $descriptors, $pipes);
    if (!is_resource($process)) {
        return null;
    }

    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $code = proc_close($process);

    return $code === 0 ? $stdout : null;
}

function getCurrentGitCommit(): ?string
{
    $out = runGit(['rev-parse', 'HEAD']);

    return $out !== null ? trim($out) : null;
}

function gitCommitExists(string $sha): bool
{
    return $sha !== '' && runGit(['cat-file', '-e', $sha . '^{commit}']) !== null;
}

function gitDiffNameOnly(string $fromSha, string $toSha): array
{
    $out = runGit(['diff', '--name-only', $fromSha, $toSha]);
    if ($out === null) {
        return [];
    }

    return array_values(array_filter(array_map('trim', explode("\n", $out)), fn ($l) => $l !== ''));
}

/** Cambios no commiteados (staged, unstaged, untracked) — por si hay edición manual sin commit. */
function gitWorkingTreeChanges(): array
{
    $out = runGit(['status', '--porcelain']);
    if ($out === null) {
        return [];
    }

    $paths = [];
    foreach (explode("\n", $out) as $line) {
        if ($line === '') {
            continue;
        }
        $path = substr($line, 3);
        if (strpos($path, ' -> ') !== false) {
            [$old, $new] = explode(' -> ', $path, 2);
            $paths[] = trim($old, '"');
            $paths[] = trim($new, '"');
            continue;
        }
        $paths[] = trim($path, '"');
    }

    return $paths;
}

/**
 * Determina qué archivos subir/borrar comparando el commit del último deploy
 * exitoso contra HEAD. Si no hay baseline (primer deploy) o se pidió --full,
 * se hace una subida completa con el listado actual del filesystem.
 */
function resolveChangedFiles(array $manifest, string $currentCommit, bool $forceFull): array
{
    $lastCommit = $manifest['last_commit'];

    if ($forceFull || $currentCommit === '' || $lastCommit === null || !gitCommitExists($lastCommit)) {
        return [
            'mode' => 'full',
            'upload' => collectDeployFiles(),
            'delete' => [],
        ];
    }

    $changed = array_unique(array_merge(
        gitDiffNameOnly($lastCommit, $currentCommit),
        gitWorkingTreeChanges()
    ));

    $upload = [];
    $delete = [];
    foreach ($changed as $relative) {
        $relative = str_replace('\\', '/', $relative);
        if ($relative === '' || shouldExcludeFromDeploy($relative)) {
            continue;
        }

        if (is_file(YOFI_ROOT . '/' . $relative)) {
            $upload[] = $relative;
        } else {
            $delete[] = $relative;
        }
    }
    sort($upload);
    sort($delete);

    return [
        'mode' => 'incremental',
        'upload' => $upload,
        'delete' => $delete,
    ];
}

// ---------------------------------------------------------------------------
// FTP
// ---------------------------------------------------------------------------

function connectFtp(array $config)
{
    if (!function_exists('ftp_connect')) {
        fwrite(STDERR, "Error: la extensión FTP de PHP no está habilitada.\n");
        exit(1);
    }

    $ftp = $config['ftp'];
    $host = trim((string)$ftp['host']);
    $port = (int)$ftp['port'];
    $user = trim((string)$ftp['user']);
    $password = (string)$ftp['password'];
    $useSsl = (bool)($ftp['ssl'] ?? false);

    $conn = false;

    if ($useSsl) {
        if (!function_exists('ftp_ssl_connect')) {
            fwrite(STDERR, "Error: este servidor requiere FTPS pero ftp_ssl_connect no está disponible en PHP.\n");
            fwrite(STDERR, "Habilitá openssl y la extensión ftp en php.ini (Laragon → PHP → php.ini).\n");
            exit(1);
        }
        $conn = @ftp_ssl_connect($host, $port, 30);
        if ($conn === false) {
            fwrite(STDERR, "Error: no se pudo conectar por FTPS ({$host}:{$port}).\n");
            exit(1);
        }
    } else {
        $conn = @ftp_connect($host, $port, 30);
        if ($conn === false) {
            fwrite(STDERR, "Error: no se pudo conectar al servidor FTP ({$host}:{$port}).\n");
            exit(1);
        }
    }

    if (!@ftp_login($conn, $user, $password)) {
        fwrite(STDERR, "Error: autenticación FTP fallida.\n");
        if ($useSsl) {
            fwrite(STDERR, "Tip: si FileZilla conecta pero PHP no, verificá que 'ssl' => true en deploy.local.php.\n");
        }
        ftp_close($conn);
        exit(1);
    }

    if ($ftp['passive']) {
        ftp_pasv($conn, true);
    }

    if (defined('FTP_USEPASVADDRESS') && function_exists('ftp_set_option')) {
        @ftp_set_option($conn, FTP_USEPASVADDRESS, false);
    }
    if (defined('FTP_TIMEOUT_SEC') && function_exists('ftp_set_option')) {
        @ftp_set_option($conn, FTP_TIMEOUT_SEC, 180);
    }

    return $conn;
}

function ftpApplyOptions($conn): void
{
    if (defined('FTP_TIMEOUT_SEC') && function_exists('ftp_set_option')) {
        @ftp_set_option($conn, FTP_TIMEOUT_SEC, 180);
    }
    if (defined('FTP_USEPASVADDRESS') && function_exists('ftp_set_option')) {
        @ftp_set_option($conn, FTP_USEPASVADDRESS, false);
    }
}

function ftpGetPwd($conn): string
{
    $pwd = @ftp_pwd($conn);

    return $pwd !== false ? $pwd : '/';
}

/** Navega desde / hasta $path (ej. public_html), creando carpetas si faltan. */
function ftpNavigateTo($conn, string $path): bool
{
    if (!@ftp_chdir($conn, '/')) {
        return false;
    }

    $parts = explode('/', trim(str_replace('\\', '/', $path), '/'));
    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }
        if (@ftp_chdir($conn, $part)) {
            continue;
        }
        if (!@ftp_mkdir($conn, $part) || !@ftp_chdir($conn, $part)) {
            return false;
        }
    }

    return true;
}

function ftpOpenSession(array $config): array|false
{
    $conn = connectFtp($config);
    ftpApplyOptions($conn);
    $remoteBase = trim(str_replace('\\', '/', $config['ftp']['remote_path']), '/');
    if ($remoteBase !== '' && !ftpNavigateTo($conn, $remoteBase)) {
        @ftp_close($conn);
        return false;
    }

    return [
        'conn' => $conn,
        'basePwd' => ftpGetPwd($conn),
    ];
}

function ftpUploadFileInBase($conn, string $basePwd, string $localPath, string $relativePath): bool
{
    if (!@ftp_chdir($conn, $basePwd)) {
        return false;
    }

    $relativePath = str_replace('\\', '/', $relativePath);
    $remoteDir = dirname($relativePath);
    $fileName = basename($relativePath);

    if ($remoteDir !== '.') {
        foreach (explode('/', $remoteDir) as $part) {
            if ($part === '') {
                continue;
            }
            if (@ftp_chdir($conn, $part)) {
                continue;
            }
            if (!@ftp_mkdir($conn, $part) || !@ftp_chdir($conn, $part)) {
                @ftp_chdir($conn, $basePwd);
                return false;
            }
        }
    }

    $ok = @ftp_put($conn, $fileName, $localPath, FTP_BINARY);
    @ftp_chdir($conn, $basePwd);

    return $ok;
}

/** Borra un archivo remoto que ya no existe en local. No falla el deploy si no se puede borrar. */
function ftpDeleteFileInBase($conn, string $basePwd, string $relativePath): bool
{
    if (!@ftp_chdir($conn, $basePwd)) {
        return false;
    }

    $relativePath = str_replace('\\', '/', $relativePath);
    $dir = dirname($relativePath);
    $fileName = basename($relativePath);

    if ($dir !== '.' && !@ftp_chdir($conn, $dir)) {
        @ftp_chdir($conn, $basePwd);
        return true; // el directorio ya no existe remoto: nada que borrar
    }

    $ok = @ftp_delete($conn, $fileName);
    @ftp_chdir($conn, $basePwd);

    return $ok;
}

function uploadProductionConfigs(array $config, array $options, array &$configFiles): array
{
    $stats = ['uploaded' => 0, 'skipped' => 0, 'errors' => 0, 'config_files' => 0];
    $forceFull = $options['force_full'];

    $session = ftpOpenSession($config);
    if ($session === false) {
        fwrite(STDERR, "Error: no se pudo conectar para subir configs.\n");
        exit(1);
    }

    $conn = $session['conn'];
    $basePwd = $session['basePwd'];

    foreach (productionConfigMap() as $localProduction => $remoteLocal) {
        $localPath = YOFI_ROOT . '/' . $localProduction;
        if (!is_file($localPath)) {
            $stats['errors']++;
            fwrite(STDERR, "  ✗ Falta: {$localProduction}\n");
            continue;
        }

        $hash = configFileHash($localPath);
        $unchanged = !$forceFull && $hash !== null && ($configFiles[$remoteLocal]['hash'] ?? null) === $hash;

        if ($unchanged) {
            $stats['skipped']++;
            $stats['config_files']++;
            echo "  ○ {$localProduction} → {$remoteLocal} (sin cambios)\n";
            continue;
        }

        $ok = ftpUploadFileInBase($conn, $basePwd, $localPath, $remoteLocal);
        if (!$ok) {
            @ftp_close($conn);
            $session = ftpOpenSession($config);
            if ($session !== false) {
                $conn = $session['conn'];
                $basePwd = $session['basePwd'];
                $ok = ftpUploadFileInBase($conn, $basePwd, $localPath, $remoteLocal);
            }
        }

        if ($ok) {
            $stats['config_files']++;
            $stats['uploaded']++;
            if ($hash !== null) {
                $configFiles[$remoteLocal] = ['hash' => $hash];
            }
            echo "  ✓ {$localProduction} → {$remoteLocal}\n";
        } else {
            $stats['errors']++;
            fwrite(STDERR, "  ✗ Error subiendo config: {$remoteLocal}\n");
        }
    }

    @ftp_close($conn);

    return $stats;
}

function uploadViaFtp(array $config, array $options): array
{
    $stats = ['uploaded' => 0, 'skipped' => 0, 'errors' => 0, 'config_files' => 0, 'deleted' => 0];
    $manifest = loadDeployManifest();
    $configFiles = $manifest['config_files'];
    $forceFull = $options['force_full'];
    $remoteBase = trim(str_replace('\\', '/', $config['ftp']['remote_path']), '/');
    $currentCommit = getCurrentGitCommit();

    if ($options['config_only']) {
        echo "[FTP] Modo --config-only\n\n";
        echo "[FTP] Configuración de producción (.production.php → .local.php)...\n";
        $cfgStats = uploadProductionConfigs($config, $options, $configFiles);
        saveDeployManifest($currentCommit ?? ($manifest['last_commit'] ?? ''), $configFiles);
        return $cfgStats;
    }

    if ($currentCommit === null) {
        echo "[GIT] No se pudo leer el commit actual (¿no es un repo git?). Se hará subida completa.\n\n";
    }

    $change = resolveChangedFiles($manifest, $currentCommit ?? '', $forceFull);

    $session = ftpOpenSession($config);
    if ($session === false) {
        fwrite(STDERR, "Error: no se pudo acceder al directorio remoto /{$remoteBase}/\n");
        exit(1);
    }

    $conn = $session['conn'];
    $basePwd = $session['basePwd'];

    echo "  ✓ Conexión FTP establecida\n";
    echo "  → Destino: {$basePwd}/\n";
    echo $change['mode'] === 'full'
        ? "  → Modo: subida completa" . ($forceFull ? ' (--full)' : ' (sin baseline previo)') . "\n\n"
        : "  → Modo: incremental (git diff desde el último deploy: " . substr($manifest['last_commit'], 0, 8) . ")\n\n";

    $toUpload = $change['upload'];
    $toDelete = $change['delete'];
    $total = count($toUpload);

    echo "[FTP] {$total} archivo(s) para subir, " . count($toDelete) . " para eliminar\n\n";

    foreach ($toUpload as $index => $relative) {
        $localPath = YOFI_ROOT . '/' . $relative;

        if (!is_file($localPath)) {
            continue;
        }

        $ok = ftpUploadFileInBase($conn, $basePwd, $localPath, $relative);
        if (!$ok) {
            @ftp_close($conn);
            $session = ftpOpenSession($config);
            if ($session === false) {
                $stats['errors']++;
                fwrite(STDERR, "  ✗ Error subiendo (sin reconexión): {$relative}\n");
                continue;
            }
            $conn = $session['conn'];
            $basePwd = $session['basePwd'];
            $ok = ftpUploadFileInBase($conn, $basePwd, $localPath, $relative);
        }

        if ($ok) {
            $stats['uploaded']++;
            echo "  ✓ {$relative}\n";
        } else {
            $stats['errors']++;
            fwrite(STDERR, "  ✗ Error subiendo: {$relative}\n");
        }

        if (($index + 1) % 50 === 0 || $index + 1 === $total) {
            echo "  → Progreso: " . ($index + 1) . "/{$total} (subidos: {$stats['uploaded']})\n";
        }
    }

    foreach ($toDelete as $relative) {
        if (ftpDeleteFileInBase($conn, $basePwd, $relative)) {
            $stats['deleted']++;
            echo "  ✗ (borrado remoto) {$relative}\n";
        }
    }

    @ftp_close($conn);

    echo "\n[FTP] Configuración de producción (.production.php → .local.php)...\n";
    $cfgStats = uploadProductionConfigs($config, $options, $configFiles);
    $stats['uploaded'] += $cfgStats['uploaded'];
    $stats['skipped'] += $cfgStats['skipped'];
    $stats['errors'] += $cfgStats['errors'];
    $stats['config_files'] = $cfgStats['config_files'];

    if ($stats['errors'] === 0 && $currentCommit !== null) {
        saveDeployManifest($currentCommit, $configFiles);
    } else {
        echo "\n⚠  Manifest no actualizado por errores — el próximo deploy reintentará el rango de cambios completo.\n";
    }

    return $stats;
}

function dryRunFileList(array $config, array $options): array
{
    $manifest = loadDeployManifest();
    $forceFull = $options['force_full'];
    $currentCommit = getCurrentGitCommit();
    $change = resolveChangedFiles($manifest, $currentCommit ?? '', $forceFull);
    $stats = ['uploaded' => 0, 'skipped' => 0, 'errors' => 0, 'config_files' => 0, 'deleted' => 0];

    echo 'Modo: ' . ($change['mode'] === 'full' ? 'subida completa' : 'incremental (git diff desde el último deploy)') . "\n";
    if ($manifest['last_commit'] !== null) {
        echo "Último deploy en commit: {$manifest['last_commit']}\n";
    }
    echo 'Commit actual: ' . ($currentCommit ?? '(no disponible)') . "\n";
    echo "Subirían: " . count($change['upload']) . " | Eliminarían del server: " . count($change['delete']) . "\n\n";

    foreach (array_slice($change['upload'], 0, 30) as $relative) {
        echo "  + {$relative}\n";
    }
    if (count($change['upload']) > 30) {
        echo "  ... y " . (count($change['upload']) - 30) . " archivos más\n";
    }
    foreach ($change['delete'] as $relative) {
        echo "  - {$relative} (eliminar remoto)\n";
    }

    $stats['uploaded'] = count($change['upload']);
    $stats['deleted'] = count($change['delete']);

    echo "\n[DRY-RUN] Configuración remota:\n";
    $configFiles = $manifest['config_files'];
    foreach (productionConfigMap() as $localProduction => $remoteLocal) {
        $localPath = YOFI_ROOT . '/' . $localProduction;
        $hash = configFileHash($localPath);
        $unchanged = !$forceFull && $hash !== null && ($configFiles[$remoteLocal]['hash'] ?? null) === $hash;
        if ($unchanged) {
            echo "  ○ {$localProduction} → {$remoteLocal} (sin cambios)\n";
            $stats['skipped']++;
        } else {
            echo "  + {$localProduction} → {$remoteLocal}\n";
            $stats['uploaded']++;
        }
        $stats['config_files']++;
    }

    $remoteBase = trim($config['ftp']['remote_path'], '/');
    echo "\n[DRY-RUN] Destino FTP: /{$remoteBase}/\n";

    return $stats;
}

// ---------------------------------------------------------------------------
// Resumen y log
// ---------------------------------------------------------------------------

function logDeploySummary(string $started, string $finished, array $dump, array $stats, array $options): void
{
    $logDir = YOFI_ROOT . '/deploy';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $dumpInfo = $dump['skipped'] ? 'omitido' : ($dump['ok'] ? basename((string)$dump['path']) : 'error');
    $flags = [];
    if ($options['files_only']) {
        $flags[] = 'files-only';
    }
    if ($options['dry_run']) {
        $flags[] = 'dry-run';
    }
    if ($options['force_db']) {
        $flags[] = 'force-db';
    }
    $flagStr = $flags !== [] ? ' [' . implode(',', $flags) . ']' : '';

    $deleted = $stats['deleted'] ?? 0;
    $line = "[{$finished}] deploy{$flagStr} | dump={$dumpInfo} | uploaded={$stats['uploaded']} | skipped={$stats['skipped']} | deleted={$deleted} | config={$stats['config_files']} | errors={$stats['errors']}\n";
    file_put_contents($logDir . '/deploy.log', $line, FILE_APPEND | LOCK_EX);
}

function printFinalSummary(array $dump, array $stats, array $options): void
{
    echo "\n========================================\n";
    echo "  RESUMEN DEL DEPLOY\n";
    echo "========================================\n";

    if ($options['dry_run']) {
        echo "Modo: dry-run (sin cambios reales)\n";
    }

    if ($dump['skipped']) {
        echo "Base de datos: export omitido (--files-only)\n";
    } elseif ($dump['ok']) {
        echo "Base de datos: dump generado OK\n";
        if (!$options['dry_run'] && $dump['path']) {
            echo "  Archivo: deploy/dumps/" . basename($dump['path']) . "\n";
            echo "  Tamaño:  " . number_format($dump['size'] / 1024, 1) . " KB\n";
        }
    } else {
        echo "Base de datos: ERROR en export\n";
    }

    echo "Archivos subidos: {$stats['uploaded']}\n";
    echo "Sin cambios:      {$stats['skipped']}\n";
    if (($stats['deleted'] ?? 0) > 0) {
        echo "Borrados remoto:  {$stats['deleted']}\n";
    }
    echo "Configs prod.:    {$stats['config_files']}\n";
    if ($stats['errors'] > 0) {
        echo "Errores FTP:      {$stats['errors']}\n";
    }

    if (!$options['dry_run'] && !$dump['skipped'] && $dump['ok']) {
        echo "\n--- PASO MANUAL PENDIENTE (base de datos) ---\n";
        echo "El dump NO se importa automáticamente (mismo patrón que Casa de Insecticidas).\n";
        echo "Importalo en DonWeb:\n";
        echo "  1. Panel DonWeb → phpMyAdmin\n";
        echo "  2. Seleccioná la base de datos de producción\n";
        echo "  3. Pestaña «Importar»\n";
        echo "  4. Subí el archivo .sql.gz (phpMyAdmin lo descomprime) o descomprimí antes a .sql\n";
        echo "  5. Ejecutá la importación\n";
        echo "\n⚠  En deploys futuros con datos reales en prod, este paso SOBREESCRIBE todo.\n";
        echo "   Usá --files-only para actualizar solo código, o --force-db tras evaluar el riesgo.\n";
    }

    if (!$options['dry_run'] && $stats['errors'] === 0) {
        echo "\n✓ Deploy de archivos completado.\n";
    } elseif (!$options['dry_run']) {
        echo "\n✗ Deploy completado con errores. Revisá deploy/deploy.log\n";
    }

    echo "========================================\n";
}
