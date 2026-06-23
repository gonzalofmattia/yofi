<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function getUserAddresses(int $userId): array
{
    $pdo = db_ro();
    $stmt = $pdo->prepare('
        SELECT id_direccion, calle, numero, depto, ciudad, provincia, cp, predeterminada, fecha_creacion
        FROM tbl_usuarios_direcciones
        WHERE usuario_id = ?
        ORDER BY predeterminada DESC, fecha_creacion DESC
    ');
    $stmt->execute([$userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function getUserAddress(int $userId, int $addressId): ?array
{
    $pdo = db_ro();
    $stmt = $pdo->prepare('
        SELECT id_direccion, calle, numero, depto, ciudad, provincia, cp, predeterminada
        FROM tbl_usuarios_direcciones
        WHERE id_direccion = ? AND usuario_id = ?
        LIMIT 1
    ');
    $stmt->execute([$addressId, $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

function saveUserAddress(int $userId, array $data, ?int $addressId = null): array
{
    $calle = trim((string) ($data['calle'] ?? ''));
    $numero = trim((string) ($data['numero'] ?? ''));
    $depto = trim((string) ($data['depto'] ?? ''));
    $ciudad = trim((string) ($data['ciudad'] ?? ''));
    $provincia = trim((string) ($data['provincia'] ?? ''));
    $cp = trim((string) ($data['cp'] ?? ''));
    $predeterminada = !empty($data['predeterminada']) ? 1 : 0;

    if ($calle === '' || $ciudad === '' || $provincia === '' || $cp === '') {
        return ['success' => false, 'message' => 'Completá calle, ciudad, provincia y código postal'];
    }

    try {
        $pdo = db_rw();

        if ($addressId !== null) {
            $existing = getUserAddress($userId, $addressId);
            if (!$existing) {
                return ['success' => false, 'message' => 'Dirección no encontrada'];
            }
        }

        if ($predeterminada) {
            $pdo->prepare('UPDATE tbl_usuarios_direcciones SET predeterminada = 0 WHERE usuario_id = ?')
                ->execute([$userId]);
        }

        if ($addressId !== null) {
            $stmt = $pdo->prepare('
                UPDATE tbl_usuarios_direcciones
                SET calle = ?, numero = ?, depto = ?, ciudad = ?, provincia = ?, cp = ?, predeterminada = ?
                WHERE id_direccion = ? AND usuario_id = ?
            ');
            $stmt->execute([
                $calle, $numero, $depto !== '' ? $depto : null, $ciudad, $provincia, $cp,
                $predeterminada, $addressId, $userId,
            ]);

            return ['success' => true, 'message' => 'Dirección actualizada', 'id_direccion' => $addressId];
        }

        $countStmt = $pdo->prepare('SELECT COUNT(*) FROM tbl_usuarios_direcciones WHERE usuario_id = ?');
        $countStmt->execute([$userId]);
        $count = (int) $countStmt->fetchColumn();
        if ($count === 0) {
            $predeterminada = 1;
        }

        $stmt = $pdo->prepare('
            INSERT INTO tbl_usuarios_direcciones
                (usuario_id, calle, numero, depto, ciudad, provincia, cp, predeterminada)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $userId, $calle, $numero, $depto !== '' ? $depto : null, $ciudad, $provincia, $cp, $predeterminada,
        ]);

        return ['success' => true, 'message' => 'Dirección guardada', 'id_direccion' => (int) $pdo->lastInsertId()];
    } catch (Throwable $e) {
        error_log('saveUserAddress: ' . $e->getMessage());

        return ['success' => false, 'message' => 'No se pudo guardar la dirección'];
    }
}

function deleteUserAddress(int $userId, int $addressId): array
{
    try {
        $pdo = db_rw();
        $existing = getUserAddress($userId, $addressId);
        if (!$existing) {
            return ['success' => false, 'message' => 'Dirección no encontrada'];
        }

        $stmt = $pdo->prepare('DELETE FROM tbl_usuarios_direcciones WHERE id_direccion = ? AND usuario_id = ?');
        $stmt->execute([$addressId, $userId]);

        if ((int) ($existing['predeterminada'] ?? 0) === 1) {
            $next = $pdo->prepare('
                SELECT id_direccion FROM tbl_usuarios_direcciones
                WHERE usuario_id = ?
                ORDER BY fecha_creacion ASC
                LIMIT 1
            ');
            $next->execute([$userId]);
            $nextId = $next->fetchColumn();
            if ($nextId) {
                $pdo->prepare('UPDATE tbl_usuarios_direcciones SET predeterminada = 1 WHERE id_direccion = ? AND usuario_id = ?')
                    ->execute([(int) $nextId, $userId]);
            }
        }

        return ['success' => true, 'message' => 'Dirección eliminada'];
    } catch (Throwable $e) {
        error_log('deleteUserAddress: ' . $e->getMessage());

        return ['success' => false, 'message' => 'No se pudo eliminar la dirección'];
    }
}

function setDefaultUserAddress(int $userId, int $addressId): array
{
    $existing = getUserAddress($userId, $addressId);
    if (!$existing) {
        return ['success' => false, 'message' => 'Dirección no encontrada'];
    }

    try {
        $pdo = db_rw();
        $pdo->prepare('UPDATE tbl_usuarios_direcciones SET predeterminada = 0 WHERE usuario_id = ?')
            ->execute([$userId]);
        $pdo->prepare('UPDATE tbl_usuarios_direcciones SET predeterminada = 1 WHERE id_direccion = ? AND usuario_id = ?')
            ->execute([$addressId, $userId]);

        return ['success' => true, 'message' => 'Dirección predeterminada actualizada'];
    } catch (Throwable $e) {
        error_log('setDefaultUserAddress: ' . $e->getMessage());

        return ['success' => false, 'message' => 'No se pudo actualizar'];
    }
}
