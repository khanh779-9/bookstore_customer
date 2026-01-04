<?php
class WishlistModel
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

        throw new RuntimeException('PDO not initialized for WishlistModel');
    }

    public static function isProductFavorite($khachhang_id, $sanpham_id)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM sanphamyeuthich WHERE khachhang_id = :khachhang_id AND sanpham_id = :sanpham_id");
        $stmt->execute([':khachhang_id' => $khachhang_id, ':sanpham_id' => $sanpham_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['count'] ?? 0) > 0;
    }

    public static function addToWishlist($khachhang_id, $sanpham_id)
    {
        if (self::isProductFavorite($khachhang_id, $sanpham_id)) return true;
        try {
            $pdo = self::getPdo();
            $stmt = $pdo->prepare("INSERT INTO sanphamyeuthich (khachhang_id, sanpham_id, ngaythem) VALUES (:khachhang_id, :sanpham_id, NOW())");
            return $stmt->execute([':khachhang_id' => $khachhang_id, ':sanpham_id' => $sanpham_id]);
        } catch (PDOException $e) {
            error_log("Lỗi thêm yêu thích: " . $e->getMessage());
            return false;
        }
    }

    public static function removeFromWishlist($khachhang_id, $sanpham_id)
    {
        try {
            $pdo = self::getPdo();
            $stmt = $pdo->prepare("DELETE FROM sanphamyeuthich WHERE khachhang_id = :khachhang_id AND sanpham_id = :sanpham_id");
            return $stmt->execute([':khachhang_id' => $khachhang_id, ':sanpham_id' => $sanpham_id]);
        } catch (PDOException $e) {
            error_log("Lỗi xóa yêu thích: " . $e->getMessage());
            return false;
        }
    }

    public static function toggleWishlist($khachhang_id, $sanpham_id)
    {
        if (self::isProductFavorite($khachhang_id, $sanpham_id)) {
            return self::removeFromWishlist($khachhang_id, $sanpham_id);
        } else {
            return self::addToWishlist($khachhang_id, $sanpham_id);
        }
    }

    public static function getWishlistProducts($khachhang_id)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT sp.*, COALESCE(s.tenSach, v.tenVPP) as name, spyt.ngaythem as ngaythem_yeuthich FROM sanphamyeuthich spyt JOIN sanpham sp ON spyt.sanpham_id = sp.sanpham_id LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id WHERE spyt.khachhang_id = :khachhang_id ORDER BY spyt.ngaythem DESC");
        $stmt->execute([':khachhang_id' => $khachhang_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getWishlistByCustomer($khachhang_id)
    {
        return self::getWishlistProducts($khachhang_id);
    }
}
?>