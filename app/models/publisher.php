<?php
class PublisherModel
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
        throw new RuntimeException('PDO not initialized for PublisherModel');
    }

    public static function getAllPublishers()
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM nhaxuatban ORDER BY nhaxuatban_id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPublishersPage(int $page = 1, int $perPage = 10, ?string $search = null): array
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
        $countSql = "SELECT COUNT(*) FROM nhaxuatban " . $where;
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT * FROM nhaxuatban " . $where . " ORDER BY nhaxuatban_id ASC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['items' => $items, 'total' => $total];
    }

    public static function getPublisherById($nhaxuatban_id)
    {
        $allPublishers = self::getAllPublishers();
        foreach ($allPublishers as $publisher) {
            if ($publisher['nhaxuatban_id'] == $nhaxuatban_id) {
                return $publisher;
            }
        }
        return null;
    }

    public static function clearPublishersCache()
    {
        cache_forget('all_publishers');
    }



    public static function createPublisher($data_array)
    {
        $ten = $data_array['ten'] ?? '';
        $diachi = $data_array['diachi'] ?? '';
        $sdt = $data_array['sdt'] ?? '';
        $email = $data_array['email'] ?? '';

        $pdo = self::getPdo();
        $stmt = $pdo->prepare("INSERT INTO nhaxuatban (ten, diachi, sdt, email) VALUES (:ten, :diachi, :sdt, :email)");
        $stmt->execute([
            ':ten' => $ten,
            ':diachi' => $diachi,
            ':sdt' => $sdt,
            ':email' => $email
        ]);
        self::clearPublishersCache();
        return $pdo->lastInsertId();
    }

    public static function updatePublisher($nhaxuatban_id, $data_array)
    {
        $ten = $data_array['ten'] ?? '';
        $diachi = $data_array['diachi'] ?? '';
        $sdt = $data_array['sdt'] ?? '';
        $email = $data_array['email'] ?? '';

        $pdo = self::getPdo();
        $stmt = $pdo->prepare("UPDATE nhaxuatban SET ten = :ten, diachi = :diachi, sdt = :sdt, email = :email WHERE nhaxuatban_id = :nhaxuatban_id");
        $stmt->execute([
            ':ten' => $ten,
            ':diachi' => $diachi,
            ':sdt' => $sdt,
            ':email' => $email,
            ':nhaxuatban_id' => $nhaxuatban_id
        ]);
        self::clearPublishersCache();
        return $stmt->rowCount();
    }

    public static function deletePublisher($nhaxuatban_id)
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("DELETE FROM nhaxuatban WHERE nhaxuatban_id = :nhaxuatban_id");
        $stmt->execute([':nhaxuatban_id' => $nhaxuatban_id]);
        self::clearPublishersCache();
        return $stmt->rowCount();
    }
}
