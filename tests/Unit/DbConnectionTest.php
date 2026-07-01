<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DbConnectionTest extends TestCase
{
    public function testDbRoConnectsToLocalDatabase(): void
    {
        $pdo = db_ro();
        $stmt = $pdo->query('SELECT DATABASE() AS db');
        $row = $stmt->fetch();

        $this->assertSame('yofi', $row['db']);
    }
}
