<?php
class LoaiSachModel
{
    protected static ?PDO $pdo = null;

    public static function init(?PDO $pdo = null)
    {
        if ($pdo instanceof PDO) {
            self::$pdo = $pdo;
            return;
        }
        if (class_exists('Database')) {
            try {
                self::$pdo = Database::getInstance();
                return;
            } catch (Exception $e) {
            }
        }
 
    }

    protected static function getPdo(): PDO
    {
        if (self::$pdo instanceof PDO) return self::$pdo;
        if (class_exists('Database')) {
            try {
                self::$pdo = Database::getInstance();
                return self::$pdo;
            } catch (Exception $e) {
            }
        }
        throw new RuntimeException('PDO not initialized for LoaiSachModel');
    }

    public static function getAll(): array
    {
        $stmt = self::getPdo()->query("SELECT * FROM loaisach ORDER BY tenLoaiSach");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>