<?php
class CategoriesModel
{
    protected static ?PDO $pdo = null;

    public static function init(?PDO $pdo = null)
    {
        if ($pdo instanceof PDO) {
            self::$pdo = $pdo;
            return;
        }
        if (class_exists('Database')) {
            try { self::$pdo = Database::getInstance(); return; } catch (Exception $e) {}
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
      
        throw new RuntimeException('PDO not initialized for CategoriesModel');
    }

    public static function getAllCategories()
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM danhmucsanpham ORDER BY danhmucSP_id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getCategoriesPage(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';
        if (!empty($search)) {
            $where = "WHERE (tenDanhMuc LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }

        $pdo = self::getPdo();
        $countSql = "SELECT COUNT(*) FROM danhmucsanpham " . $where;
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT * FROM danhmucsanpham " . $where . " ORDER BY danhmucSP_id ASC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['items' => $items, 'total' => $total];
    }

    public static function getCategory($danhmucSP_id)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM danhmucsanpham WHERE danhmucSP_id = :code LIMIT 1");
        $stmt->execute([':code' => $danhmucSP_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getCategoryById($danhmucSP_id)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM danhmucsanpham WHERE danhmucSP_id = :id LIMIT 1");
        $stmt->execute([':id' => $danhmucSP_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function clearCategoriesCache()
    {
        cache_forget('all_categories');
    }

    public static function createCategory(array $data)
    {
        $tenDanhMuc = $data['tenDanhMuc'] ?? ($data['ten'] ?? '');
        $mo_ta = $data['mo_ta'] ?? null;
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("INSERT INTO danhmucsanpham (tenDanhMuc, mo_ta) VALUES (:tenDanhMuc, :mo_ta)");
        $stmt->execute([':tenDanhMuc' => $tenDanhMuc, ':mo_ta' => $mo_ta]);
        self::clearCategoriesCache();
        return $pdo->lastInsertId();
    }

    public static function updateCategory($danhmucSP_id, array $data)
    {
        $tenDanhMuc = $data['tenDanhMuc'] ?? ($data['ten'] ?? '');
        $mo_ta = $data['mo_ta'] ?? null;
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("UPDATE danhmucsanpham SET tenDanhMuc = :tenDanhMuc, mo_ta = :mo_ta WHERE danhmucSP_id = :id");
        $stmt->execute([':tenDanhMuc' => $tenDanhMuc, ':mo_ta' => $mo_ta, ':id' => $danhmucSP_id]);
        self::clearCategoriesCache();
        return $stmt->rowCount();
    }

    public static function deleteCategory($danhmucSP_id)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("DELETE FROM danhmucsanpham WHERE danhmucSP_id = :id");
        $stmt->execute([':id' => $danhmucSP_id]);
        self::clearCategoriesCache();
        return $stmt->rowCount();
    }
}
?>