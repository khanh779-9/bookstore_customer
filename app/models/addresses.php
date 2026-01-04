<?php
class AddressesModel
{
    protected static ?PDO $pdo = null;

    public static function init(?PDO $pdo = null)
    {
        if ($pdo instanceof PDO) { self::$pdo = $pdo; return; }
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
        throw new RuntimeException('PDO not initialized for AddressesModel');
    }

    public static function getAddressesByCustomer(int $khachhang_id): array
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM diachi_giaohang WHERE khachhang_id = :kh ORDER BY dcgh_id DESC");
        $stmt->execute([':kh' => $khachhang_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAddressById(int $dcgh_id)
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM diachi_giaohang WHERE dcgh_id = :id LIMIT 1");
        $stmt->execute([':id' => $dcgh_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function addAddress(int $khachhang_id, string $diachi)
    {
        $stmt = self::getPdo()->prepare("INSERT INTO diachi_giaohang (khachhang_id, diachi) VALUES (:kh, :diachi)");
        $ok = $stmt->execute([':kh' => $khachhang_id, ':diachi' => $diachi]);
        return $ok ? self::getPdo()->lastInsertId() : false;
    }

    public static function updateAddress(int $dcgh_id, string $diachi)
    {
        $stmt = self::getPdo()->prepare("UPDATE diachi_giaohang SET diachi = :diachi WHERE dcgh_id = :id");
        return $stmt->execute([':diachi' => $diachi, ':id' => $dcgh_id]);
    }

    public static function deleteAddress(int $dcgh_id)
    {
        $stmt = self::getPdo()->prepare("DELETE FROM diachi_giaohang WHERE dcgh_id = :id");
        return $stmt->execute([':id' => $dcgh_id]);
    }
}
 
?>
