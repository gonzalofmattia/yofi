<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/php/auth.php';

final class LoginOtpTest extends TestCase
{
    private static int $testUserId;
    private static string $testEmail;

    public static function setUpBeforeClass(): void
    {
        self::$testEmail = 'test-otp-' . bin2hex(random_bytes(4)) . '@example.com';
        $pdo = db_rw();
        $pdo->prepare('
            INSERT INTO tbl_usuarios (email, nombre, apellido, is_guest, activo)
            VALUES (?, ?, ?, 1, 1)
        ')->execute([self::$testEmail, 'Test', 'Otp']);
        self::$testUserId = (int) $pdo->lastInsertId();
    }

    public static function tearDownAfterClass(): void
    {
        db_rw()->prepare('DELETE FROM tbl_usuarios WHERE id_usuario = ?')->execute([self::$testUserId]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        db_rw()->prepare('DELETE FROM tbl_login_otp WHERE usuario_id = ?')->execute([self::$testUserId]);
    }

    private function insertOtp(string $code, int $attempts = 0, int $expiresInSeconds = 600, bool $consumed = false): void
    {
        $pdo = db_rw();
        $pdo->prepare('
            INSERT INTO tbl_login_otp (usuario_id, code_hash, expires_at, attempts, consumed_at)
            VALUES (?, ?, ?, ?, ?)
        ')->execute([
            self::$testUserId,
            password_hash($code, PASSWORD_DEFAULT),
            date('Y-m-d H:i:s', time() + $expiresInSeconds),
            $attempts,
            $consumed ? date('Y-m-d H:i:s') : null,
        ]);
    }

    public function testRequestLoginCodeReturnsGenericMessageForUnknownEmail(): void
    {
        $result = requestLoginCode('no-existe-' . bin2hex(random_bytes(4)) . '@example.com');

        $this->assertTrue($result['success']);
    }

    public function testVerifyLoginCodeSucceedsWithCorrectCode(): void
    {
        $this->insertOtp('654321');

        $result = verifyLoginCode(self::$testEmail, '654321');

        $this->assertTrue($result['success']);
        $this->assertSame(self::$testUserId, $result['user']['id']);
    }

    public function testVerifyLoginCodeFailsWithWrongCode(): void
    {
        $this->insertOtp('111111');

        $result = verifyLoginCode(self::$testEmail, '999999');

        $this->assertFalse($result['success']);
    }

    public function testVerifyLoginCodeFailsAfterTooManyAttempts(): void
    {
        $this->insertOtp('222222', attempts: 5);

        $result = verifyLoginCode(self::$testEmail, '222222');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Demasiados intentos', $result['message']);
    }

    public function testVerifyLoginCodeFailsWhenExpired(): void
    {
        $this->insertOtp('333333', expiresInSeconds: -10);

        $result = verifyLoginCode(self::$testEmail, '333333');

        $this->assertFalse($result['success']);
    }

    public function testVerifyLoginCodeCannotBeReused(): void
    {
        $this->insertOtp('444444');

        $first = verifyLoginCode(self::$testEmail, '444444');
        $second = verifyLoginCode(self::$testEmail, '444444');

        $this->assertTrue($first['success']);
        $this->assertFalse($second['success']);
    }
}
