<?php
declare(strict_types=1);

/**
 * Thin PDO wrapper. Returns null when the database is unreachable so the
 * site can still render from the fallback content in includes/content.php.
 */
final class Database
{
    private static ?PDO $pdo = null;
    private static bool $failed = false;

    public static function get(): ?PDO
    {
        if (self::$pdo !== null || self::$failed) {
            return self::$pdo;
        }
        try {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (Throwable $e) {
            self::$failed = true;
            error_log('[margha] DB connection failed: ' . $e->getMessage());
        }
        return self::$pdo;
    }

    /** Run a SELECT and return all rows, or null on any failure. */
    public static function fetchAll(string $sql, array $params = []): ?array
    {
        $pdo = self::get();
        if (!$pdo) {
            return null;
        }
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            error_log('[margha] Query failed: ' . $e->getMessage());
            return null;
        }
    }
}
