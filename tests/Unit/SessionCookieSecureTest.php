<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/php/auth.php';

final class SessionCookieSecureTest extends TestCase
{
    private array $serverBackup = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = $_SERVER;
        unset($_SERVER['HTTPS'], $_SERVER['HTTP_X_FORWARDED_PROTO'], $_SERVER['HTTP_FRONT_END_HTTPS'], $_SERVER['HTTP_HOST']);
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        parent::tearDown();
    }

    public function testNotSecureOverPlainHttpEvenOnProductionDomain(): void
    {
        // Regresión: yofi.com.ar no tenía certificado SSL. El código viejo
        // asumía HTTPS por el nombre de dominio y marcaba la cookie como
        // Secure igual, así que el navegador nunca la guardaba (rompía CSRF
        // en todos los endpoints protegidos).
        $_SERVER['HTTP_HOST'] = 'yofi.com.ar';

        $this->assertFalse(isSessionCookieSecure());
    }

    public function testSecureWhenHttpsServerVarIsOn(): void
    {
        $_SERVER['HTTPS'] = 'on';

        $this->assertTrue(isSessionCookieSecure());
    }

    public function testSecureWhenForwardedProtoIsHttps(): void
    {
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

        $this->assertTrue(isSessionCookieSecure());
    }

    public function testNotSecureOnLocalhost(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';

        $this->assertFalse(isSessionCookieSecure());
    }
}
