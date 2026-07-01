<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/php/users.php';

final class CheckEmailAccountStatusTest extends TestCase
{
    private static int $registeredUserId;
    private static string $registeredEmail;
    private static int $guestUserId;
    private static string $guestEmail;

    public static function setUpBeforeClass(): void
    {
        $pdo = db_rw();
        self::$registeredEmail = 'test-registered-' . bin2hex(random_bytes(4)) . '@example.com';
        $pdo->prepare('INSERT INTO tbl_usuarios (email, password_hash, nombre, apellido, is_guest, activo) VALUES (?, ?, ?, ?, 0, 1)')
            ->execute([self::$registeredEmail, password_hash('secret', PASSWORD_DEFAULT), 'Reg', 'User']);
        self::$registeredUserId = (int) $pdo->lastInsertId();

        self::$guestEmail = 'test-guest-' . bin2hex(random_bytes(4)) . '@example.com';
        $pdo->prepare('INSERT INTO tbl_usuarios (email, password_hash, nombre, apellido, is_guest, activo) VALUES (?, NULL, ?, ?, 1, 1)')
            ->execute([self::$guestEmail, 'Guest', 'User']);
        self::$guestUserId = (int) $pdo->lastInsertId();
    }

    public static function tearDownAfterClass(): void
    {
        $pdo = db_rw();
        $pdo->prepare('DELETE FROM tbl_usuarios WHERE id_usuario IN (?, ?)')
            ->execute([self::$registeredUserId, self::$guestUserId]);
    }

    public function testUnknownEmailReturnsExistsFalse(): void
    {
        $status = checkEmailAccountStatus('no-existe-' . bin2hex(random_bytes(4)) . '@example.com');

        $this->assertSame(['exists' => false, 'is_guest' => false, 'has_password' => false], $status);
    }

    public function testRegisteredEmailHasPassword(): void
    {
        $status = checkEmailAccountStatus(self::$registeredEmail);

        $this->assertTrue($status['exists']);
        $this->assertFalse($status['is_guest']);
        $this->assertTrue($status['has_password']);
    }

    public function testGuestEmailHasNoPassword(): void
    {
        $status = checkEmailAccountStatus(self::$guestEmail);

        $this->assertTrue($status['exists']);
        $this->assertTrue($status['is_guest']);
        $this->assertFalse($status['has_password']);
    }
}
