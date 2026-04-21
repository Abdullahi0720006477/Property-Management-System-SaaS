<?php
require_once __DIR__ . '/env.php';

/**
 * Database Configuration - Singleton PDO Connection
 */
class Database
{
    private static ?PDO $instance = null;

    private static function getHost(): string { return $_ENV['DB_HOST'] ?? 'localhost'; }
    private static function getDbName(): string { return $_ENV['DB_NAME'] ?? 'property_management_db'; }
    private static function getUsername(): string { return $_ENV['DB_USER'] ?? 'root'; }
    private static function getPassword(): string { return $_ENV['DB_PASS'] ?? ''; }
    private const CHARSET = 'utf8mb4';

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::getHost(),
                self::getDbName(),
                self::CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$instance = new PDO($dsn, self::getUsername(), self::getPassword(), $options);
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                die('Database connection failed. Please check your configuration.');
            }
        }

        return self::$instance;
    }
}
