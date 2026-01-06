<?php
class ProductModel
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
        throw new RuntimeException('PDO not initialized for ProductModel');
    }

    public static function getAllProducts(): array
    {
        $pdo = self::getPdo();
        $queryCommand = "
            SELECT sp.*, s.sach_id AS item_id, s.tenSach AS name,
                c.tenLoaiSach AS category_name, a.ten AS author_name,
                p.ten AS publisher_name, prov.ten AS provider_name,
                'book' AS type
                FROM sanpham sp
                JOIN sach s ON sp.sanpham_id = s.sanpham_id
                LEFT JOIN loaisach c ON s.loaisach_code = c.loaisach_code
                LEFT JOIN tacgia a ON s.tacgia_id = a.tacgia_id
                LEFT JOIN nhaxuatban p ON s.nhaxuatban_id = p.nhaxuatban_id
                LEFT JOIN nhacungcap prov ON sp.nhacungcap_id = prov.nhacungcap_id

                UNION

                SELECT sp.*, v.vpp_id AS item_id, v.tenVPP AS name,
                    NULL AS category_name, NULL AS author_name,
                    NULL AS publisher_name, prov.ten AS provider_name,
                    'vpp' AS type
                FROM sanpham sp
                JOIN vanphongpham v ON v.sanpham_id = sp.sanpham_id
                LEFT JOIN nhacungcap prov ON sp.nhacungcap_id = prov.nhacungcap_id

                ORDER BY sanpham_id ASC
            ";

        $stmt = $pdo->query($queryCommand);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getProductById($id)
    {
        $pdo = self::getPdo();

        $sqlBook = "
            SELECT sp.*, s.sach_id AS item_id, s.tenSach AS name,
                   c.tenLoaiSach AS category_name, a.ten AS author_name,
                   p.ten AS publisher_name, prov.ten AS provider_name,
                   s.namXB, 'book' AS type
                    FROM sanpham sp
                    JOIN sach s ON sp.sanpham_id = s.sanpham_id
                    LEFT JOIN loaisach c ON s.loaisach_code = c.loaisach_code
                    LEFT JOIN tacgia a ON s.tacgia_id = a.tacgia_id
                    LEFT JOIN nhaxuatban p ON s.nhaxuatban_id = p.nhaxuatban_id
                    LEFT JOIN nhacungcap prov ON sp.nhacungcap_id = prov.nhacungcap_id
                    WHERE sp.sanpham_id = :id
            ";


        $sqlVpp = "
            SELECT sp.*, v.vpp_id AS item_id, v.tenVPP AS name,
                   prov.ten AS provider_name, 'vpp' AS type
            FROM sanpham sp
            JOIN vanphongpham v ON v.sanpham_id = sp.sanpham_id
            LEFT JOIN nhacungcap prov ON sp.nhacungcap_id = prov.nhacungcap_id
            WHERE sp.sanpham_id = :id
        ";

        $stmt = $pdo->prepare($sqlBook);
        $stmt->execute([':id' => $id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($book) {
            return $book;
        }

        $stmt = $pdo->prepare($sqlVpp);
        $stmt->execute([':id' => $id]);
        $vpp = $stmt->fetch(PDO::FETCH_ASSOC);

        return $vpp ?? [];
    }


    public static function getProductByIdAndType(string $type, int $sanpham_id)
    {
        $pdo = self::getPdo();
        if ($type === 'Sach') {
            $stmt = $pdo->prepare("SELECT * FROM sach WHERE sanpham_id = :sanpham_id");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM vanphongpham WHERE sanpham_id = :sanpham_id");
        }
        $stmt->execute([':sanpham_id' => $sanpham_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getProductsByType(int $type): array
    {
        $pdo = self::getPdo();
        // Use helper to decide whether this category is for books
        if (function_exists('is_book_category') && is_book_category($type)) {
            $stmt2 = $pdo->prepare("SELECT sp.*, s.sach_id AS sach_id, s.tenSach AS name, c.tenLoaiSach AS category_name, a.ten AS author_name, p.ten AS publisher_name, prov.ten AS provider_name, s.namXB FROM sanpham sp LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id LEFT JOIN loaisach c ON s.loaisach_code = c.loaisach_code LEFT JOIN tacgia a ON s.tacgia_id = a.tacgia_id LEFT JOIN nhaxuatban p ON s.nhaxuatban_id = p.nhaxuatban_id LEFT JOIN nhacungcap prov ON sp.nhacungcap_id = prov.nhacungcap_id WHERE sp.danhmucSP_id = :type");
            $stmt2->execute([':type' => $type]);
            return $stmt2->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt2 = $pdo->prepare("SELECT sp.*, v.vpp_id AS vpp_id, v.tenVPP AS name, prov.ten AS provider_name FROM vanphongpham v JOIN sanpham sp ON v.sanpham_id = sp.sanpham_id LEFT JOIN nhacungcap prov ON sp.nhacungcap_id = prov.nhacungcap_id WHERE sp.danhmucSP_id = :type");
            $stmt2->execute([':type' => $type]);
            return $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public static function addProduct(string $type, array $data)
    {
        $pdo = self::getPdo();
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO sanpham (danhmucSP_id, hinhanh, mo_ta, soluongton, donvitinh_id, soluongban, gia, nhacungcap_id) VALUES (:danhmucSP_id, :hinhanh, :mo_ta, :soluongton, :donvitinh_id, :soluongban, :gia, :nhacungcap_id)");
            $stmt->execute([
                ':danhmucSP_id' => $data['danhmucSP_id'] ?? ($type === 'Sach' ? 1 : 2),
                ':hinhanh' => $data['hinhanh'] ?? null,
                ':mo_ta' => $data['mo_ta'] ?? null,
                ':soluongton' => $data['soluongton'] ?? 0,
                ':donvitinh_id' => $data['donvitinh_id'] ?? 1,
                ':soluongban' => $data['soluongban'] ?? 0,
                ':gia' => $data['gia'] ?? 0,
                ':nhacungcap_id' => $data['nhacungcap_id'] ?? 1
            ]);
            $sanpham_id = $pdo->lastInsertId();
            if ($type === 'Sach') {
                $stmt2 = $pdo->prepare("INSERT INTO sach (sanpham_id, tenSach, nhaxuatban_id, loaisach_code, namXB, tacgia_id) VALUES (:sanpham_id, :tenSach, :nhaxuatban_id, :loaisach_code, :namXB, :tacgia_id)");
                $stmt2->execute([
                    ':sanpham_id' => $sanpham_id,
                    ':tenSach' => $data['tenSach'] ?? '',
                    ':nhaxuatban_id' => $data['nhaxuatban_id'] ?? null,
                    ':loaisach_code' => $data['loaisach_code'] ?? null,
                    ':namXB' => $data['namXB'] ?? null,
                    ':tacgia_id' => $data['tacgia_id'] ?? null
                ]);
            } else {
                $stmt2 = $pdo->prepare("INSERT INTO vanphongpham (sanpham_id, tenVPP) VALUES (:sanpham_id, :tenVPP)");
                $stmt2->execute([':sanpham_id' => $sanpham_id, ':tenVPP' => $data['tenVPP'] ?? '']);
            }
            $pdo->commit();
            return (int)$sanpham_id;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Lỗi thêm sản phẩm: " . $e->getMessage());
            return false;
        }
    }

    public static function updateProduct(string $type, int $sanpham_id, array $data)
    {
        $pdo = self::getPdo();
        try {
            $pdo->beginTransaction();
            $updateSanPham = [];
            $paramsSanPham = [':sanpham_id' => $sanpham_id];
            $fields = ['danhmucSP_id', 'hinhanh', 'mo_ta', 'soluongton', 'donvitinh_id', 'soluongban', 'gia', 'nhacungcap_id'];
            foreach ($fields as $f) {
                if (isset($data[$f])) {
                    $updateSanPham[] = "$f = :$f";
                    $paramsSanPham[":$f"] = $data[$f];
                }
            }
            if (!empty($updateSanPham)) {
                $sqlSanPham = "UPDATE sanpham SET " . implode(", ", $updateSanPham) . " WHERE sanpham_id = :sanpham_id";
                $stmt = $pdo->prepare($sqlSanPham);
                $stmt->execute($paramsSanPham);
            }

            $existingSach = self::getProductByIdAndType('Sach', $sanpham_id);
            $existingVpp = self::getProductByIdAndType('VPP', $sanpham_id);

            if ($type === 'Sach') {
                if ($existingSach) {
                    $updateSach = [];
                    $paramsSach = [':sanpham_id' => $sanpham_id];
                    $fields2 = ['tenSach', 'nhaxuatban_id', 'loaisach_code', 'namXB', 'tacgia_id'];
                    foreach ($fields2 as $f) {
                        if (isset($data[$f])) {
                            $updateSach[] = "$f = :$f";
                            $paramsSach[":$f"] = $data[$f];
                        }
                    }
                    if (!empty($updateSach)) {
                        $sqlSach = "UPDATE sach SET " . implode(", ", $updateSach) . " WHERE sanpham_id = :sanpham_id";
                        $stmt2 = $pdo->prepare($sqlSach);
                        $stmt2->execute($paramsSach);
                    }
                } else {
                    if ($existingVpp) {
                        $del = $pdo->prepare("DELETE FROM vanphongpham WHERE sanpham_id = :sanpham_id");
                        $del->execute([':sanpham_id' => $sanpham_id]);
                    }

                    $stmtIns = $pdo->prepare("INSERT INTO sach (sanpham_id, tenSach, nhaxuatban_id, loaisach_code, namXB, tacgia_id) VALUES (:sanpham_id, :tenSach, :nhaxuatban_id, :loaisach_code, :namXB, :tacgia_id)");
                    $stmtIns->execute([
                        ':sanpham_id' => $sanpham_id,
                        ':tenSach' => $data['tenSach'] ?? ($data['name'] ?? ''),
                        ':nhaxuatban_id' => $data['nhaxuatban_id'] ?? null,
                        ':loaisach_code' => $data['loaisach_code'] ?? null,
                        ':namXB' => $data['namXB'] ?? null,
                        ':tacgia_id' => $data['tacgia_id'] ?? null
                    ]);
                }
            } else {
                if ($existingVpp) {
                    $updateVPP = [];
                    $paramsVPP = [':sanpham_id' => $sanpham_id];
                    if (isset($data['tenVPP'])) {
                        $updateVPP[] = "tenVPP = :tenVPP";
                        $paramsVPP[':tenVPP'] = $data['tenVPP'];
                    }
                    if (!empty($updateVPP)) {
                        $sqlVPP = "UPDATE vanphongpham SET " . implode(", ", $updateVPP) . " WHERE sanpham_id = :sanpham_id";
                        $stmt2 = $pdo->prepare($sqlVPP);
                        $stmt2->execute($paramsVPP);
                    }
                } else {

                    if ($existingSach) {
                        $del = $pdo->prepare("DELETE FROM sach WHERE sanpham_id = :sanpham_id");
                        $del->execute([':sanpham_id' => $sanpham_id]);
                    }

                    $stmtIns = $pdo->prepare("INSERT INTO vanphongpham (sanpham_id, tenVPP) VALUES (:sanpham_id, :tenVPP)");
                    $stmtIns->execute([
                        ':sanpham_id' => $sanpham_id,
                        ':tenVPP' => $data['tenVPP'] ?? ($data['name'] ?? '')
                    ]);
                }
            }
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Lỗi cập nhật sản phẩm: " . $e->getMessage());
            return false;
        }
    }

    public static function deleteProduct(string $type, int $sanpham_id)
    {
        $pdo = self::getPdo();
        try {
            $pdo->beginTransaction();
            if ($type === 'Sach') {
                $stmt = $pdo->prepare("DELETE FROM sach WHERE sanpham_id = :sanpham_id");
            } else {
                $stmt = $pdo->prepare("DELETE FROM vanphongpham WHERE sanpham_id = :sanpham_id");
            }
            $stmt->execute([':sanpham_id' => $sanpham_id]);
            $stmt2 = $pdo->prepare("DELETE FROM sanpham WHERE sanpham_id = :sanpham_id");
            $stmt2->execute([':sanpham_id' => $sanpham_id]);
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Lỗi xóa sản phẩm: " . $e->getMessage());
            return false;
        }
    }

    public static function filterProducts(string $search = '', int $categoryId = 0, float $minPrice = 0, float $maxPrice = 0, int $providerId = 0, int $publisherId = 0, string $sortBy = ''): array
    {
        $products = self::getAllProducts();
        $items = array_filter($products, function ($item) use ($search, $categoryId, $minPrice, $maxPrice) {
            if ($search !== '' && stripos($item['name'] ?? '', $search) === false) return false;
            if ($categoryId > 0 && ($item['danhmucSP_id'] ?? 0) != $categoryId) return false;
            if ($minPrice > 0 && ($item['gia'] ?? 0) < $minPrice) return false;
            if ($maxPrice > 0 && ($item['gia'] ?? 0) > $maxPrice) return false;
            return true;
        });
        if (!empty($sortBy)) {
            usort($items, function ($a, $b) use ($sortBy) {
                switch ($sortBy) {
                    case 'price_asc':
                        return ($a['gia'] ?? 0) <=> ($b['gia'] ?? 0);
                    case 'price_desc':
                        return ($b['gia'] ?? 0) <=> ($a['gia'] ?? 0);
                    case 'newest':
                        return ($b['sanpham_id'] ?? 0) <=> ($a['sanpham_id'] ?? 0);
                    case 'best_selling':
                        return ($b['soluongban'] ?? 0) <=> ($a['soluongban'] ?? 0);
                    case 'name_asc':
                        return strcmp(strtolower($a['name'] ?? ''), strtolower($b['name'] ?? ''));
                    default:
                        return 0;
                }
            });
        }
        if ($providerId > 0) $items = array_filter($items, fn($it) => intval($it['nhacungcap_id'] ?? 0) === $providerId);
        if ($publisherId > 0) $items = array_filter($items, fn($it) => intval($it['nhaxuatban_id'] ?? 0) === $publisherId);
        return $items;
    }

    public static function getFeaturedProducts(int $limit = 8): array
    {
        $all = self::getAllProducts();
        if (empty($all)) return [];

        $explicitFeatured = array_filter($all, function ($p) {
            if (isset($p['noi_bat']) && intval($p['noi_bat']) === 1) return true;
            if (isset($p['is_featured']) && intval($p['is_featured']) === 1) return true;
            if (isset($p['featured']) && intval($p['featured']) === 1) return true;
            return false;
        });

        if (!empty($explicitFeatured)) {
            usort($explicitFeatured, function ($a, $b) {
                return intval($b['sanpham_id'] ?? 0) <=> intval($a['sanpham_id'] ?? 0);
            });
            return array_slice($explicitFeatured, 0, $limit);
        }

        usort($all, function ($a, $b) {
            return intval($b['soluongban'] ?? 0) <=> intval($a['soluongban'] ?? 0);
        });
        return array_slice($all, 0, $limit);
    }

    public static function getNewArrivals(int $limit = 8): array
    {
        $all = self::getAllProducts();
        usort($all, function ($a, $b) {
            return intval($b['sanpham_id'] ?? 0) <=> intval($a['sanpham_id'] ?? 0);
        });
        return array_slice($all, 0, $limit);
    }

    public static function getFilteredProducts(string $search, int $category, string $price): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT sp.*, COALESCE(s.tenSach, v.tenVPP) AS name, prov.ten AS provider_name FROM sanpham sp LEFT JOIN sach s ON s.sanpham_id = sp.sanpham_id LEFT JOIN vanphongpham v ON v.sanpham_id = sp.sanpham_id LEFT JOIN nhacungcap prov ON prov.nhacungcap_id = sp.nhacungcap_id WHERE 1 = 1";
        $params = [];
        if ($search !== '') {
            $sql .= " AND (s.tenSach LIKE :search OR v.tenVPP LIKE :search) ";
            $params[':search'] = "%$search%";
        }
        if ($category > 0) {
            $sql .= " AND sp.danhmucSP_id = :cat ";
            $params[':cat'] = $category;
        }
        if ($price === 'low') $sql .= " AND sp.gia < 50000 ";
        elseif ($price === 'mid') $sql .= " AND sp.gia BETWEEN 50000 AND 100000 ";
        elseif ($price === 'high') $sql .= " AND sp.gia > 100000 ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getProductsPage(int $page = 1, int $perPage = 10, string $search = '', array $filters = [], string $sortBy = ''): array
    {
        $pdo = self::getPdo();
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $whereParts = ['1 = 1'];
        $params = [];
        if ($search !== '') {
            // search across book name or vpp name (can't use SELECT alias in WHERE)
            $whereParts[] = "(COALESCE(s.tenSach, v.tenVPP) LIKE :search)";
            $params[':search'] = "%$search%";
        }
        if (!empty($filters['category']) && intval($filters['category']) > 0) {
            $whereParts[] = 'sp.danhmucSP_id = :category';
            $params[':category'] = (int)$filters['category'];
        }
        if (!empty($filters['provider']) && intval($filters['provider']) > 0) {
            $whereParts[] = 'sp.nhacungcap_id = :provider';
            $params[':provider'] = (int)$filters['provider'];
        }
        if (!empty($filters['publisher']) && intval($filters['publisher']) > 0) {
            $whereParts[] = 's.nhaxuatban_id = :publisher';
            $params[':publisher'] = (int)$filters['publisher'];
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $whereParts[] = 'sp.gia >= :min_price';
            $params[':min_price'] = (float)$filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $whereParts[] = 'sp.gia <= :max_price';
            $params[':max_price'] = (float)$filters['max_price'];
        }

        $where = implode(' AND ', $whereParts);

        $orderSql = 'sp.sanpham_id ASC';
        switch ($sortBy) {
            case 'price_asc': $orderSql = 'sp.gia ASC'; break;
            case 'price_desc': $orderSql = 'sp.gia DESC'; break;
            case 'newest': $orderSql = 'sp.sanpham_id DESC'; break;
            case 'best_selling': $orderSql = 'sp.soluongban DESC'; break;
            case 'name_asc': $orderSql = 'COALESCE(s.tenSach, v.tenVPP) ASC'; break;
            default: $orderSql = 'sp.sanpham_id ASC';
        }

        $sql = "SELECT sp.*, COALESCE(s.tenSach, v.tenVPP) AS name, prov.ten AS provider_name
                FROM sanpham sp
                LEFT JOIN sach s ON s.sanpham_id = sp.sanpham_id
                LEFT JOIN vanphongpham v ON v.sanpham_id = sp.sanpham_id
                LEFT JOIN nhacungcap prov ON prov.nhacungcap_id = sp.nhacungcap_id
                WHERE $where
                ORDER BY $orderSql
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            if (is_int($v)) $stmt->bindValue($k, $v, PDO::PARAM_INT);
            else $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Count total
        $countSql = "SELECT COUNT(DISTINCT sp.sanpham_id) as cnt
                     FROM sanpham sp
                     LEFT JOIN sach s ON s.sanpham_id = sp.sanpham_id
                     LEFT JOIN vanphongpham v ON v.sanpham_id = sp.sanpham_id
                     WHERE $where";
        $countStmt = $pdo->prepare($countSql);
        foreach ($params as $k => $v) {
            if (is_int($v)) $countStmt->bindValue($k, $v, PDO::PARAM_INT);
            else $countStmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)($countStmt->fetchColumn(0) ?? 0);

        return ['items' => $items, 'total' => $total, 'page' => $page, 'per_page' => $perPage];
    }


    public static function getProductsByCategory(int $categoryId): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT sp.*, COALESCE(s.tenSach, v.tenVPP) AS name FROM sanpham sp LEFT JOIN sach s ON s.sanpham_id = sp.sanpham_id LEFT JOIN vanphongpham v ON v.sanpham_id = sp.sanpham_id WHERE sp.danhmucSP_id = :cat");
        $stmt->execute([':cat' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getBestSellingProducts(int $limit = 20): array
    {
        $all = self::getAllProducts();
        usort($all, function ($a, $b) {
            return intval($b['soluongban'] ?? 0) <=> intval($a['soluongban'] ?? 0);
        });
        return array_slice($all, 0, $limit);
    }

    public static function getProductsFromAllPromotion($limitNumber = 0): array
    {
        // Tạo mảng để lưu trữ tất cả sản phẩm từ các khuyến mãi
        $arr = [];
        foreach (PromotionModel::getAllPromotions() as $promotion) {
            $promotionId = $promotion['khuyenmai_id'] ?? 0;
            if ($promotionId > 0 && time() >= strtotime($promotion['ngaybatdau']) && time() <= strtotime($promotion['ngayketthuc']))
                $arr = array_merge($arr, self::getProductsFromPromotion((int)$promotionId));
        }
        return $limitNumber > 0 ? array_slice($arr, 0, $limitNumber) : $arr;
    }

    public static function getProductsFromPromotion(int $promotionId, int $limit = 8): array
    {
        
        $arr = [];
        foreach (PromotionModel::getPromotionDetails($promotionId) as $detail) {
            $sanphamId = $detail['sanpham_id'] ?? 0;
            if ($sanphamId > 0) {
                $product = self::getProductById((int)$sanphamId);
                if (!empty($product) ) {
                    $arr[] = $product;
                }
            }
        }
        return $limit > 0 ? array_slice($arr, 0, $limit) : $arr;
    }

    public static function checkProductNameExists(string $type, string $name, ?int $excludeId = null): bool
    {
        $pdo = self::getPdo();
        if ($type === 'Sach') {
            $sql = "SELECT COUNT(*) FROM sach s JOIN sanpham sp ON s.sanpham_id = sp.sanpham_id WHERE s.tenSach = :name";
        } else {
            $sql = "SELECT COUNT(*) FROM vanphongpham v JOIN sanpham sp ON v.sanpham_id = sp.sanpham_id WHERE v.tenVPP = :name";
        }
        if ($excludeId !== null) {
            $sql .= " AND sp.sanpham_id != :excludeId";
        }
        $stmt = $pdo->prepare($sql);
        $params = [':name' => $name];
        if ($excludeId !== null) {
            $params[':excludeId'] = $excludeId;
        }
        $stmt->execute($params);
        $count = (int)$stmt->fetchColumn();
        return $count > 0;
    }   
}
