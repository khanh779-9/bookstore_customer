<?php
class CustomerModel
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
        
        throw new RuntimeException('PDO not initialized for CustomerModel');
    }

    public static function getAllCustomers(): array
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM khachhang");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getCustomersPage(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';
        if (!empty($search)) {
            $where = "WHERE (ho LIKE :q OR tendem LIKE :q OR ten LIKE :q OR email LIKE :q OR sdt LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }

        $pdo = self::getPdo();
        $countSql = "SELECT COUNT(*) FROM khachhang " . $where;
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT * FROM khachhang " . $where . " ORDER BY ngaythamgia DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['items' => $items, 'total' => $total];
    }

    public static function getCustomerById($id)
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM khachhang WHERE khachhang_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function isTrueLogin($email)
    {
        return self::findCustomerByEmail($email);
    }

    public static function findCustomerByEmail(string $email)
    {
        $sql="SELECT * FROM khachhang WHERE email = :email LIMIT 1";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createCustomer(array $data)
    {
        $sql = "INSERT INTO khachhang (password, ho, tendem, ten, ngaysinh, diachi, sdt, email, gioitinh) VALUES (:password, :ho, :tendem, :ten, :ngaysinh, :diachi, :sdt, :email, :gioitinh)";

        $stmt = self::getPdo()->prepare($sql);
        $ok = $stmt->execute([
            ':password' => $data['password'] ?? null,
            ':ho' => $data['ho'] ?? '',
            ':tendem' => $data['tendem'] ?? null,
            ':ten' => $data['ten'] ?? '',
            ':ngaysinh' => $data['ngaysinh'] ?? null,
            ':diachi' => $data['diachi'] ?? null,
            ':sdt' => $data['sdt'] ?? null,
            ':email' => $data['email'] ?? null,
            ':gioitinh' => $data['gioitinh'] ?? null,
        ]);

        if (!$ok) return false;
        return self::getPdo()->lastInsertId();
    }

    public static function updatePassword(int $id, string $hash): bool
    {
        $stmt = self::getPdo()->prepare("UPDATE khachhang SET password = :pw WHERE khachhang_id = :id");
        return (bool)$stmt->execute([':pw' => $hash, ':id' => $id]);
    }

    public static function updateCustomer(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        if (array_key_exists('ho', $data)) { $fields[] = 'ho = :ho'; $params[':ho'] = $data['ho']; }
        if (array_key_exists('tendem', $data)) { $fields[] = 'tendem = :tendem'; $params[':tendem'] = $data['tendem']; }
        if (array_key_exists('ten', $data)) { $fields[] = 'ten = :ten'; $params[':ten'] = $data['ten']; }
        if (array_key_exists('ngaysinh', $data)) { $fields[] = 'ngaysinh = :ngaysinh'; $params[':ngaysinh'] = $data['ngaysinh'] ?: null; }
        if (array_key_exists('diachi', $data)) { $fields[] = 'diachi = :diachi'; $params[':diachi'] = $data['diachi']; }
        if (array_key_exists('sdt', $data)) { $fields[] = 'sdt = :sdt'; $params[':sdt'] = $data['sdt']; }
        if (array_key_exists('gioitinh', $data)) { $fields[] = 'gioitinh = :gioitinh'; $params[':gioitinh'] = $data['gioitinh']; }

        if (empty($fields)) return true;

        $sql = 'UPDATE khachhang SET ' . implode(', ', $fields) . ' WHERE khachhang_id = :id';
        $stmt = self::getPdo()->prepare($sql);
        return (bool)$stmt->execute($params);
    }
}
?>