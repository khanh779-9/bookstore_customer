<?php
class Database
{
    protected static $pdo;

    public static function getInstance()
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../config.php';
            $db = $config['db'];
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['dbname'], $db['charset']);
            try {
                self::$pdo = new PDO($dsn, $db['user'], $db['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
