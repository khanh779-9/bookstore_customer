<?php

class PromotionModel
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
        throw new RuntimeException('PDO not initialized for PromotionModel');
    }

    public static function getAllPromotions($startDate=null, $endDate=null): array
    {
        $pdo = self::getPdo();
        $queryCommand = "SELECT * FROM khuyenmai";
        $params = [];
        if($startDate!=null)
        {
            $queryCommand .= " WHERE ngaybatdau >= :startDate";
            $params[':startDate'] = $startDate;
        }
        if($endDate!=null)
        {
            if($startDate!=null)
            {
                $queryCommand .= " AND ngayketthuc <= :endDate";
            }
            else
            {
                $queryCommand .= " WHERE ngayketthuc <= :endDate";
            }
            $params[':endDate'] = $endDate;
        }
        $queryCommand .= " ORDER BY ngaybatdau DESC";
        $stmt = $pdo->prepare($queryCommand);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPromotionDetails($promotionId): array
    {
        $pdo = self::getPdo();
        $queryCommand = "SELECT * FROM chitietkhuyenmai WHERE khuyenmai_id = :promotionId";
        $stmt = $pdo->prepare($queryCommand);
        $stmt->execute([':promotionId' => $promotionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPromotionById($promotionId): ?array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM khuyenmai WHERE khuyenmai_id = :id");
        $stmt->execute([':id' => $promotionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public static function getPromotionWithDetails($promotionId): array
    {
        $promotion = self::getPromotionById($promotionId);
        if (!$promotion) return [];
        
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("
            SELECT ctkm.*, sp.sanpham_id, 
                   COALESCE(s.tenSach, v.tenVPP) as tenSanPham,
                   sp.gia as gia_goc
            FROM chitietkhuyenmai ctkm
            JOIN sanpham sp ON ctkm.sanpham_id = sp.sanpham_id
            LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id
            LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id
            WHERE ctkm.khuyenmai_id = :promotionId
        ");
        $stmt->execute([':promotionId' => $promotionId]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $promotion['details'] = $details;
        return $promotion;
    }

    public static function createPromotion(array $data): int
    {
        $pdo = self::getPdo();
        
        $stmt = $pdo->prepare("
            INSERT INTO khuyenmai (ten, ngaybatdau, ngayketthuc)
            VALUES (:ten, :ngaybatdau, :ngayketthuc)
        ");
        
        $stmt->execute([
            ':ten' => $data['ten'],
            ':ngaybatdau' => $data['ngaybatdau'],
            ':ngayketthuc' => $data['ngayketthuc']
        ]);
        
        return (int)$pdo->lastInsertId();
    }

    public static function updatePromotion($promotionId, array $data): bool
    {
        $pdo = self::getPdo();
        
        $stmt = $pdo->prepare("
            UPDATE khuyenmai 
            SET ten = :ten, 
                ngaybatdau = :ngaybatdau, 
                ngayketthuc = :ngayketthuc
            WHERE khuyenmai_id = :id
        ");
        
        return $stmt->execute([
            ':id' => $promotionId,
            ':ten' => $data['ten'],
            ':ngaybatdau' => $data['ngaybatdau'],
            ':ngayketthuc' => $data['ngayketthuc']
        ]);
    }

    public static function deletePromotion($promotionId): bool
    {
        $pdo = self::getPdo();
        
        // First delete all promotion details
        $stmt = $pdo->prepare("DELETE FROM chitietkhuyenmai WHERE khuyenmai_id = :id");
        $stmt->execute([':id' => $promotionId]);
        
        // Then delete the promotion
        $stmt = $pdo->prepare("DELETE FROM khuyenmai WHERE khuyenmai_id = :id");
        return $stmt->execute([':id' => $promotionId]);
    }

    public static function addPromotionDetail(array $data): int
    {
        $pdo = self::getPdo();
        
        $stmt = $pdo->prepare("
            INSERT INTO chitietkhuyenmai (khuyenmai_id, sanpham_id, soluong, tilegiamgia)
            VALUES (:khuyenmai_id, :sanpham_id, :soluong, :tilegiamgia)
        ");
        
        $stmt->execute([
            ':khuyenmai_id' => $data['khuyenmai_id'],
            ':sanpham_id' => $data['sanpham_id'],
            ':soluong' => $data['soluong'],
            ':tilegiamgia' => $data['tilegiamgia']
        ]);
        
        return (int)$pdo->lastInsertId();
    }

    public static function updatePromotionDetail($ctkm_id, array $data): bool
    {
        $pdo = self::getPdo();
        
        $stmt = $pdo->prepare("
            UPDATE chitietkhuyenmai 
            SET soluong = :soluong, 
                tilegiamgia = :tilegiamgia
            WHERE ctkm_id = :id
        ");
        
        return $stmt->execute([
            ':id' => $ctkm_id,
            ':soluong' => $data['soluong'],
            ':tilegiamgia' => $data['tilegiamgia']
        ]);
    }

    public static function deletePromotionDetail($ctkm_id): bool
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("DELETE FROM chitietkhuyenmai WHERE ctkm_id = :id");
        return $stmt->execute([':id' => $ctkm_id]);
    }

    public static function getActivePromotionForProduct(int $productId): ?array
    {
        $pdo = self::getPdo();
        
        // Lấy khuyến mãi đang hoạt động cho sản phẩm (ngày hiện tại nằm trong khoảng ngaybatdau - ngayketthuc)
        $stmt = $pdo->prepare("
            SELECT ctkm.*, km.ten, km.ngaybatdau, km.ngayketthuc
            FROM chitietkhuyenmai ctkm
            JOIN khuyenmai km ON ctkm.khuyenmai_id = km.khuyenmai_id
            WHERE ctkm.sanpham_id = :product_id
            AND CURDATE() BETWEEN km.ngaybatdau AND km.ngayketthuc
            LIMIT 1
        ");
        
        $stmt->execute([
            ':product_id' => $productId
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function searchProducts(string $searchTerm): array
    {
        $pdo = self::getPdo();
        
        $stmt = $pdo->prepare("
            SELECT sp.sanpham_id, 
                   COALESCE(s.tenSach, v.tenVPP) as tenSanPham,
                   sp.gia,
                   sp.soluongton
            FROM sanpham sp
            LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id
            LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id
            WHERE COALESCE(s.tenSach, v.tenVPP) LIKE :search
               OR sp.sanpham_id = :exact_id
            LIMIT 20
        ");
        
        $search = '%' . $searchTerm . '%';
        $exact_id = is_numeric($searchTerm) ? (int)$searchTerm : 0;
        
        $stmt->execute([
            ':search' => $search,
            ':exact_id' => $exact_id
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>