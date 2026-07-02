<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../admin/include/auth_redirect.php';

final class AdminAuthRedirectTest extends TestCase
{
    public function testAdminBasePathEndsWithAdminSlash(): void
    {
        $this->assertStringEndsWith('/admin/', admin_base_path());
    }

    public function testSafeRedirectTargetAcceptsPathInsideAdmin(): void
    {
        $target = admin_base_path() . 'pedidos/detalle.php?id=20';

        $this->assertSame($target, admin_safe_redirect_target($target));
    }

    /**
     * @dataProvider unsafeTargetProvider
     */
    public function testSafeRedirectTargetRejectsUnsafeValues(?string $raw): void
    {
        $this->assertSame(admin_base_path() . 'dashboard.php', admin_safe_redirect_target($raw));
    }

    /**
     * @return array<string, array{0: string|null}>
     */
    public static function unsafeTargetProvider(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'external absolute url' => ['https://evil.com/phish'],
            'protocol-relative' => ['//evil.com/phish'],
            'scheme embedded' => ['/admin/../https://evil.com'],
            'outside admin path' => ['/checkout/process.php'],
            'relative without leading slash' => ['dashboard.php'],
        ];
    }
}
