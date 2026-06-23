<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

/**
 * @return array{exists:bool,is_guest:bool,has_password:bool,user_id?:int}|null
 */
function checkEmailAccountStatus(string $email): ?array
{
    $email = trim($email);
    if ($email === '') {
        return null;
    }

    try {
        $pdo = db_ro();
        $stmt = $pdo->prepare('
            SELECT id_usuario, is_guest, password_hash, activo
            FROM tbl_usuarios
            WHERE email = ?
            LIMIT 1
        ');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return ['exists' => false, 'is_guest' => false, 'has_password' => false];
        }

        return [
            'exists' => true,
            'is_guest' => (int) ($row['is_guest'] ?? 0) === 1,
            'has_password' => !empty($row['password_hash']),
            'user_id' => (int) $row['id_usuario'],
            'activo' => (int) ($row['activo'] ?? 1) === 1,
        ];
    } catch (Throwable $e) {
        error_log('checkEmailAccountStatus: ' . $e->getMessage());

        return null;
    }
}

function registerUser(string $email, string $password, string $nombre, string $apellido, string $telefono = ''): array
{
    $email = trim($email);
    $nombre = trim($nombre);
    $apellido = trim($apellido);
    $telefono = trim($telefono);

    if ($nombre === '' || $apellido === '') {
        return ['success' => false, 'message' => 'Nombre y apellido son obligatorios'];
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Email inválido'];
    }
    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres'];
    }

    $status = checkEmailAccountStatus($email);
    if ($status === null) {
        return ['success' => false, 'message' => 'Error al verificar el email'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $pdo = db_rw();

        if ($status['exists']) {
            if ($status['is_guest'] && !$status['has_password']) {
                $userId = (int) $status['user_id'];
                $stmt = $pdo->prepare('
                    UPDATE tbl_usuarios
                    SET password_hash = ?, nombre = ?, apellido = ?, telefono = ?,
                        is_guest = 0, activo = 1, email_verificado = 1, fecha_actualizacion = NOW()
                    WHERE id_usuario = ?
                ');
                $stmt->execute([
                    $hash,
                    $nombre,
                    $apellido,
                    $telefono !== '' ? $telefono : null,
                    $userId,
                ]);

                $userRow = getUserData($userId);
                if ($userRow) {
                    establishUserSession([
                        'id_usuario' => $userId,
                        'email' => $email,
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'email_verificado' => 1,
                    ], false);
                }

                return [
                    'success' => true,
                    'message' => '¡Cuenta completada! Ya podés usar tu email y contraseña.',
                    'user_id' => $userId,
                    'converted_guest' => true,
                ];
            }

            return [
                'success' => false,
                'message' => 'Este email ya está registrado.',
                'code' => 'email_exists',
            ];
        }

        $stmt = $pdo->prepare('
            INSERT INTO tbl_usuarios (email, password_hash, nombre, apellido, telefono, is_guest, activo, email_verificado)
            VALUES (?, ?, ?, ?, ?, 0, 1, 1)
        ');
        $stmt->execute([
            $email,
            $hash,
            $nombre,
            $apellido,
            $telefono !== '' ? $telefono : null,
        ]);
        $userId = (int) $pdo->lastInsertId();

        establishUserSession([
            'id_usuario' => $userId,
            'email' => $email,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email_verificado' => 1,
        ], false);

        return [
            'success' => true,
            'message' => 'Cuenta creada correctamente.',
            'user_id' => $userId,
            'converted_guest' => false,
        ];
    } catch (Throwable $e) {
        error_log('registerUser: ' . $e->getMessage());

        return ['success' => false, 'message' => 'No se pudo crear la cuenta'];
    }
}

function findOrCreateGuestUser(string $email, string $nombre, string $apellido, string $telefono = ''): ?int
{
    $status = checkEmailAccountStatus($email);
    if ($status && $status['exists']) {
        return (int) $status['user_id'];
    }

    try {
        $pdo = db_rw();
        $stmt = $pdo->prepare('
            INSERT INTO tbl_usuarios (email, nombre, apellido, telefono, is_guest, activo, email_verificado)
            VALUES (?, ?, ?, ?, 1, 1, 0)
        ');
        $stmt->execute([
            trim($email),
            trim($nombre),
            trim($apellido),
            trim($telefono) !== '' ? trim($telefono) : null,
        ]);

        return (int) $pdo->lastInsertId();
    } catch (Throwable $e) {
        error_log('findOrCreateGuestUser: ' . $e->getMessage());

        return null;
    }
}
