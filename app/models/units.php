<?php
class UnitsModel
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
        throw new RuntimeException('PDO not initialized for UnitsModel');
    }

    public static function getAllUnits(): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->query("SELECT * FROM donvitinh ORDER BY donvitinh_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUnitById(int $id)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM donvitinh WHERE donvitinh_id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>