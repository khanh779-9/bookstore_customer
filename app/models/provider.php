<?php
class ProviderModel
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

        throw new RuntimeException('PDO not initialized for ProviderModel');
    }

    public static function getAllProviders()
    {
        $pdo = self::getPdo();
        $stmt = $pdo->query("SELECT * FROM nhacungcap ORDER BY nhacungcap_id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getProvidersPage(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';
        if (!empty($search)) {
            $where = "WHERE (ten LIKE :q OR diachi LIKE :q OR email LIKE :q OR sdt LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }

        $pdo = self::getPdo();
        $countSql = "SELECT COUNT(*) FROM nhacungcap " . $where;
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT * FROM nhacungcap " . $where . " ORDER BY nhacungcap_id ASC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['items' => $items, 'total' => $total];
    }

    public static function createProvider($provider_array)
    {
        $stmt = self::getPdo()->prepare("INSERT INTO nhacungcap (ten, diachi, sdt, email) VALUES (:ten, :diachi, :sdt, :email)");
        $result = $stmt->execute([
            ':ten' => $provider_array['ten'],
            ':diachi' => $provider_array['diachi'],
            ':sdt' => $provider_array['sdt'],
            ':email' => $provider_array['email']
        ]);

        if ($result) cache_forget('all_providers');
        return $result ? self::getPdo()->lastInsertId() : false;
    }

    public static function getProviderById($id)
    {
        $allProviders = self::getAllProviders();
        foreach ($allProviders as $provider) {
            if ($provider['nhacungcap_id'] == $id) {
                return $provider;
            }
        }
        return null;
    }

    public static function updateProvider($id, $provider)
    {
        $stmt = self::getPdo()->prepare("UPDATE nhacungcap SET ten = :ten, diachi = :diachi, sdt = :sdt, email = :email WHERE nhacungcap_id = :id");
        $result = $stmt->execute([
            ':ten' => $provider['ten'] ?? $provider['tenNCC'] ?? '',
            ':diachi' => $provider['diachi'] ?? $provider['diaChi'] ?? '',
            ':sdt' => $provider['sdt'] ?? $provider['soDienThoai'] ?? '',
            ':email' => $provider['email'] ?? '',
            ':id' => $id
        ]);

        if ($result) cache_forget('all_providers');
        return $result;
    }

    public static function deleteProvider($id)
    {
        $stmt = self::getPdo()->prepare("DELETE FROM nhacungcap WHERE nhacungcap_id = :id");
        $result = $stmt->execute([':id' => $id]);
        if ($result) cache_forget('all_providers');
        return $result;
    }
}
