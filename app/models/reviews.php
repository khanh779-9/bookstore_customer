<?php
class ReviewsModel
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

        throw new RuntimeException('PDO not initialized for ReviewsModel');
    }

    public static function getAllReviewsByProductId(int $productId): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM danhgia WHERE sanpham_id = :productId");
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function customerHasReviewed(int $customerId, int $productId): bool
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM danhgia WHERE khachhang_id = :kh AND sanpham_id = :sp");
        $stmt->execute([':kh' => $customerId, ':sp' => $productId]);
        $count = (int)$stmt->fetchColumn();
        return $count > 0;
    }

    public static function createReview(int $customerId, int $productId, int $rating, string $comment = ''): bool
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("INSERT INTO danhgia (khachhang_id, sanpham_id, rating, binhluan, ngaytao) VALUES (:kh, :sp, :rating, :binhluan, NOW())");
        return $stmt->execute([
            ':kh' => $customerId,
            ':sp' => $productId,
            ':rating' => $rating,
            ':binhluan' => $comment
        ]);
    }
}
?>