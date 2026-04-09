<?php
// db.php - database configuration and connection utility
// This file returns a PDO instance for use by models/controllers.

class Database {
    // Using untyped properties for compatibility with PHP < 7.4
    private static $host = 'localhost';
    private static $db   = 'event_sync_db';
    private static $user = 'root';
    private static $pass = '';
    private static $charset = 'utf8mb4';

    /**
     * Returns a singleton PDO connection.
     *
     * Usage: $pdo = Database::getConnection();
     */
    public static function getConnection(): PDO {
        static $pdo = null;
        if ($pdo === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset;
            try {
                $pdo = new PDO($dsn, self::$user, self::$pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                // In production you might log the error instead of displaying it
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        return $pdo;
    }
}
