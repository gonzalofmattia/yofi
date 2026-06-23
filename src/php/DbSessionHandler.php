<?php

declare(strict_types=1);

/**
 * Session handler — sesiones PHP en MySQL (tbl_sessions).
 */
class DbSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
    private PDO $pdo;
    private int $maxLifetime;

    public function __construct(PDO $pdo, int $maxLifetime = 14400)
    {
        $this->pdo = $pdo;
        $this->maxLifetime = $maxLifetime;
    }

    public static function register(): void
    {
        require_once __DIR__ . '/db.php';
        $pdo = db_ro();
        $lifetime = (int) ini_get('session.gc_maxlifetime');
        if ($lifetime < 600) {
            $lifetime = 14400;
        }
        session_set_save_handler(new self($pdo, $lifetime), true);
    }

    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    #[\ReturnTypeWillChange]
    public function read(string $id): string|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT `data` FROM `tbl_sessions` WHERE `id` = :id AND `last_access` >= :min'
        );
        $stmt->execute([
            ':id' => $id,
            ':min' => time() - $this->maxLifetime,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (string) $row['data'] : '';
    }

    public function write(string $id, string $data): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO `tbl_sessions` (`id`, `data`, `last_access`)
             VALUES (:id, :data, :time)
             ON DUPLICATE KEY UPDATE `data` = VALUES(`data`), `last_access` = VALUES(`last_access`)'
        );

        return $stmt->execute([
            ':id' => $id,
            ':data' => $data,
            ':time' => time(),
        ]);
    }

    public function destroy(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM `tbl_sessions` WHERE `id` = :id');

        return $stmt->execute([':id' => $id]);
    }

    #[\ReturnTypeWillChange]
    public function gc(int $maxLifetime): int|false
    {
        $stmt = $this->pdo->prepare('DELETE FROM `tbl_sessions` WHERE `last_access` < :min');
        $stmt->execute([':min' => time() - $maxLifetime]);

        return $stmt->rowCount();
    }

    public function validateId(string $id): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM `tbl_sessions` WHERE `id` = :id AND `last_access` >= :min LIMIT 1'
        );
        $stmt->execute([
            ':id' => $id,
            ':min' => time() - $this->maxLifetime,
        ]);

        return (bool) $stmt->fetch();
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `tbl_sessions` SET `last_access` = :time WHERE `id` = :id'
        );

        return $stmt->execute([
            ':id' => $id,
            ':time' => time(),
        ]);
    }
}
