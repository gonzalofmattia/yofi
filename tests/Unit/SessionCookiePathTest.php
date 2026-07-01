<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/php/auth.php';

final class SessionCookiePathTest extends TestCase
{
    private array $serverBackup = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        parent::tearDown();
    }

    public function testCookiePathIsConsistentRegardlessOfWhichScriptIsRunning(): void
    {
        // Regresión: antes se derivaba del SCRIPT_NAME de cada request, así
        // que la home (/index.php) calculaba un path distinto al de los
        // endpoints bajo /public/api/ o /admin/api/ — el navegador terminaba
        // guardando dos cookies de sesión distintas (una por path), cada una
        // con su propio CSRF token, y las llamadas AJAX a la API nunca veían
        // la sesión de la página. Ahora debe dar siempre el mismo resultado.
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $pathForPage = getPublicSessionCookiePath();

        $_SERVER['SCRIPT_NAME'] = '/public/api/zipnova/cotizar.php';
        $pathForPublicApi = getPublicSessionCookiePath();

        $_SERVER['SCRIPT_NAME'] = '/admin/api/cambiar-estado-pedido.php';
        $pathForAdminApi = getPublicSessionCookiePath();

        $_SERVER['SCRIPT_NAME'] = '/checkout/process.php';
        $pathForCheckout = getPublicSessionCookiePath();

        $this->assertSame($pathForPage, $pathForPublicApi);
        $this->assertSame($pathForPage, $pathForAdminApi);
        $this->assertSame($pathForPage, $pathForCheckout);
    }
}
