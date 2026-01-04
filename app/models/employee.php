<?php
class EmployeeModel
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

        throw new RuntimeException('PDO not initialized for EmployeeModel');
    }

    public static function findEmployeeByEmail(string $email)
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM nhanvien WHERE email = :email AND trangthai = 'dang_lam' LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findEmployeeByCode($ma_nhan_vien)
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM nhanvien WHERE nhanvien_id = :ma AND trangthai = 'dang_lam' LIMIT 1");
        $stmt->execute([':ma' => $ma_nhan_vien]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getEmployeeById(int $id)
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM nhanvien WHERE nhanvien_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllEmployees()
    {
        $stmt = self::getPdo()->query("SELECT * FROM nhanvien ORDER BY nhanvien_id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRecentOrders($limit = 10)
    {
        $stmt = self::getPdo()->prepare("SELECT hd.hoadon_id, hd.ngaytao, hd.tongtien, hd.trangthai, CONCAT(kh.ho, ' ', COALESCE(kh.tendem, ''), ' ', kh.ten) as ten_khachhang FROM hoadon hd LEFT JOIN khachhang kh ON hd.khachhang_id = kh.khachhang_id ORDER BY hd.ngaytao DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllOrders()
    {
        $stmt = self::getPdo()->query("SELECT hd.*, CONCAT(kh.ho, ' ', COALESCE(kh.tendem, ''), ' ', kh.ten) as ten_khachhang, CONCAT(nv.ho, ' ', COALESCE(nv.tendem, ''), ' ', nv.ten) as ten_nhanvien FROM hoadon hd LEFT JOIN khachhang kh ON hd.khachhang_id = kh.khachhang_id LEFT JOIN nhanvien nv ON hd.nhanvien_id = nv.nhanvien_id ORDER BY hd.ngaytao DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOrderDetails($hoadon_id)
    {
        $stmt = self::getPdo()->prepare("SELECT hd.*, CONCAT(kh.ho, ' ', COALESCE(kh.tendem, ''), ' ', kh.ten) as ten_khachhang, kh.email as email_khachhang, kh.sdt as sdt_khachhang, kh.diachi as diachi_khachhang, CONCAT(nv.ho, ' ', COALESCE(nv.tendem, ''), ' ', nv.ten) as ten_nhanvien, dcgh.diachi as diachi_giaohang FROM hoadon hd LEFT JOIN khachhang kh ON hd.khachhang_id = kh.khachhang_id LEFT JOIN nhanvien nv ON hd.nhanvien_id = nv.nhanvien_id LEFT JOIN diachi_giaohang dcgh ON hd.dcgh_id = dcgh.dcgh_id WHERE hd.hoadon_id = :id");
        $stmt->execute([':id' => $hoadon_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getOrderItems($hoadon_id)
    {
        $stmt = self::getPdo()->prepare("SELECT cthd.*, COALESCE(s.tenSach, v.tenVPP) as ten_sanpham, sp.hinhanh FROM chitiethoadon cthd JOIN sanpham sp ON cthd.sanpham_id = sp.sanpham_id LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id WHERE cthd.hoadon_id = :id");
        $stmt->execute([':id' => $hoadon_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateOrderStatus($hoadon_id, $trangthai)
    {
        $pdo = self::getPdo();
        try {
            $pdo->beginTransaction();

            // Update order status
            $stmt = $pdo->prepare("UPDATE hoadon SET trangthai = :trangthai WHERE hoadon_id = :id");
            $result = $stmt->execute([':trangthai' => $trangthai, ':id' => $hoadon_id]);

            // If order is delivered (da_giao_hang), update product quantities
            if ($result && $trangthai === 'da_giao_hang') {
                // Get all order items
                $stmtItems = $pdo->prepare(
                    "SELECT sanpham_id, soluong FROM chitiethoadon WHERE hoadon_id = :hoadon_id"
                );
                $stmtItems->execute([':hoadon_id' => $hoadon_id]);
                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                // Update product quantities: increase soluongban, soluongton already decreased at order creation
                $stmtUpdateProduct = $pdo->prepare(
                    "UPDATE sanpham SET soluongban = soluongban + :qty WHERE sanpham_id = :sanpham_id"
                );

                foreach ($items as $item) {
                    $stmtUpdateProduct->execute([
                        ':qty' => (int)$item['soluong'],
                        ':sanpham_id' => (int)$item['sanpham_id']
                    ]);
                }
            }

            $pdo->commit();
            return $result;
        } catch (Throwable $e) {
            try {
                $pdo->rollBack();
            } catch (Throwable $__) {
            }
            error_log('updateOrderStatus failed: ' . $e->getMessage());
            return false;
        }
    }

    public static function createEmployee(array $data)
    {
        try {
            $stmt = self::getPdo()->prepare("INSERT INTO nhanvien (password, ho, tendem, ten, gioitinh, ngaysinh, diachi, sdt, email, ngayvaolam, trangthai, role, ghichu) VALUES (:password, :ho, :tendem, :ten, :gioitinh, :ngaysinh, :diachi, :sdt, :email, :ngayvaolam, :trangthai, :role, :ghichu)");
            $password = $data['password'] ?? '123456';
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $ok = $stmt->execute([
                ':password' => $hashedPassword,
                ':ho' => $data['ho'] ?? '',
                ':tendem' => $data['tendem'] ?? null,
                ':ten' => $data['ten'] ?? '',
                ':gioitinh' => $data['gioitinh'] ?? null,
                ':ngaysinh' => $data['ngaysinh'] ?? null,
                ':diachi' => $data['diachi'] ?? null,
                ':sdt' => $data['sdt'] ?? null,
                ':email' => $data['email'] ?? null,
                ':ngayvaolam' => $data['ngayvaolam'] ?? date('Y-m-d'),
                ':trangthai' => $data['trangthai'] ?? 'dang_lam',
                ':role' => $data['role'] ?? 'nhanvien',
                ':ghichu' => $data['ghichu'] ?? null
            ]);
            return $ok ? self::getPdo()->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("Lỗi tạo nhân viên: " . $e->getMessage());
            return false;
        }
    }

    public static function updateEmployee($nhanvien_id, array $data)
    {
        try {
            $updateFields = [];
            $params = [':id' => $nhanvien_id];
            $allowedFields = ['ho', 'tendem', 'ten', 'gioitinh', 'ngaysinh', 'diachi', 'sdt', 'email', 'ngayvaolam', 'trangthai', 'role', 'ghichu'];
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
            if (!empty($data['password'])) {
                $updateFields[] = "password = :password";
                $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            if (empty($updateFields)) return true;
            $sql = "UPDATE nhanvien SET " . implode(", ", $updateFields) . " WHERE nhanvien_id = :id";
            $stmt = self::getPdo()->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật nhân viên: " . $e->getMessage());
            return false;
        }
    }

    public static function updatePassword(int $nhanvien_id, string $hash): bool
    {
        try {
            $stmt = self::getPdo()->prepare("UPDATE nhanvien SET password = :password WHERE nhanvien_id = :id");
            return (bool)$stmt->execute([':password' => $hash, ':id' => $nhanvien_id]);
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật mật khẩu nhân viên: " . $e->getMessage());
            return false;
        }
    }

    public static function deleteEmployee($nhanvien_id)
    {
        try {
            $stmt = self::getPdo()->prepare("UPDATE nhanvien SET trangthai = 'nghi_viec' WHERE nhanvien_id = :id");
            return $stmt->execute([':id' => $nhanvien_id]);
        } catch (PDOException $e) {
            error_log("Lỗi xóa nhân viên: " . $e->getMessage());
            return false;
        }
    }

    public static function getEmployeesByRole($role = null)
    {
        if ($role) {
            $stmt = self::getPdo()->prepare("SELECT * FROM nhanvien WHERE role = :role ORDER BY nhanvien_id DESC");
            $stmt->execute([':role' => $role]);
        } else {
            $stmt = self::getPdo()->query("SELECT * FROM nhanvien ORDER BY nhanvien_id DESC");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function canAccessDashboard()
    {
        $employeeRole = $_SESSION['employee_account']['role'] ?? 'nhanvien';
        return in_array($employeeRole, ['admin', 'quanly']);
    }

    public static function canAccessReports()
    {
        $employeeRole = $_SESSION['employee_account']['role'] ?? 'nhanvien';
        return in_array($employeeRole, ['admin', 'quanly']);
    }

    public static function canAccessSettings()
    {
        $employeeRole = $_SESSION['employee_account']['role'] ?? 'nhanvien';
        return $employeeRole === 'admin';
    }

    public static function getPreviousPeriodStats($period = 'month')
    {
        switch ($period) {
            case 'week':
                $startDate = date('Y-m-d', strtotime('-14 days'));
                $endDate = date('Y-m-d', strtotime('-8 days'));
                break;
            case 'month':
                $startDate = date('Y-m-01', strtotime('last month'));
                $endDate = date('Y-m-t', strtotime('last month'));
                break;
            case 'year':
                $startDate = date('Y-01-01', strtotime('last year'));
                $endDate = date('Y-12-31', strtotime('last year'));
                break;
            default:
                $startDate = date('Y-m-01', strtotime('last month'));
                $endDate = date('Y-m-t', strtotime('last month'));
        }

        $stats = [];
        $stmt = self::getPdo()->prepare("SELECT COALESCE(SUM(tongtien), 0) as total FROM hoadon WHERE ngaytao >= :start AND ngaytao <= :end AND trangthai != 'da_huy'");
        $stmt->execute([':start' => $startDate, ':end' => $endDate]);
        $stats['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stmt = self::getPdo()->prepare("SELECT COUNT(*) as total FROM hoadon WHERE ngaytao >= :start AND ngaytao <= :end");
        $stmt->execute([':start' => $startDate, ':end' => $endDate]);
        $stats['orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stmt = self::getPdo()->prepare("SELECT COUNT(*) as total FROM khachhang WHERE ngaythamgia >= :start AND ngaythamgia <= :end");
        $stmt->execute([':start' => $startDate, ':end' => $endDate]);
        $stats['customers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        return $stats;
    }

    public static function getLowStockProducts($threshold = 10)
    {
        $stmt = self::$pdo->prepare("SELECT sp.sanpham_id, COALESCE(s.tenSach, v.tenVPP) as ten_sanpham, sp.soluongton, sp.gia FROM sanpham sp LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id WHERE sp.soluongton <= :threshold ORDER BY sp.soluongton ASC LIMIT 10");
        $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOrderStatistics()
    {
        $stats = [];
        $stmt = self::$pdo->query("SELECT COUNT(*) as total FROM hoadon WHERE trangthai = 'cho_xac_nhan'");
        $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $stmt = self::$pdo->query("SELECT COUNT(*) as total FROM hoadon WHERE trangthai = 'da_xac_nhan'");
        $stats['confirmed'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $stmt = self::$pdo->query("SELECT COUNT(*) as total FROM hoadon WHERE trangthai = 'dang_giao_hang'");
        $stats['shipped'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $stmt = self::$pdo->query("SELECT COUNT(*) as total FROM hoadon WHERE trangthai = 'da_giao_hang'");
        $stats['delivered'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $stmt = self::$pdo->query("SELECT COALESCE(SUM(tongtien), 0) as total FROM hoadon WHERE trangthai != 'da_huy'");
        $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        return $stats;
    }

    public static function getRevenueByDateRange($startDate, $endDate)
    {
        $stmt = self::$pdo->prepare("SELECT DATE(ngaytao) as date, COUNT(*) as order_count, COALESCE(SUM(tongtien), 0) as total_revenue FROM hoadon WHERE ngaytao >= :start AND ngaytao <= :end AND trangthai != 'da_huy' GROUP BY DATE(ngaytao) ORDER BY DATE(ngaytao) ASC");
        $stmt->execute([':start' => $startDate . ' 00:00:00', ':end' => $endDate . ' 23:59:59']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRevenueSummary($period = 'month')
    {
        switch ($period) {
            case 'today':
                $dateCondition = "DATE(ngaytao) = CURDATE()";
                break;
            case 'week':
                $dateCondition = "ngaytao >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'month':
            default:
                $dateCondition = "MONTH(ngaytao) = MONTH(CURDATE()) AND YEAR(ngaytao) = YEAR(CURDATE())";
                break;
        }
        $stmt = self::$pdo->prepare("SELECT COUNT(*) as order_count, COALESCE(SUM(tongtien), 0) as total_revenue, AVG(tongtien) as avg_order_value FROM hoadon WHERE $dateCondition AND trangthai != 'da_huy'");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getCustomerStatistics()
    {
        $stats = [];
        $stmt = self::$pdo->query("SELECT COUNT(*) as total FROM khachhang");
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $stmt = self::$pdo->query("SELECT COUNT(*) as total FROM khachhang WHERE DATE(ngaythamgia) = CURDATE()");
        $stats['new_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $stmt = self::$pdo->query("SELECT COUNT(*) as total FROM khachhang WHERE ngaythamgia >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $stats['new_week'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $stmt = self::$pdo->query("SELECT COUNT(*) as total FROM khachhang WHERE MONTH(ngaythamgia) = MONTH(CURDATE()) AND YEAR(ngaythamgia) = YEAR(CURDATE())");
        $stats['new_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        return $stats;
    }

    public static function getEmployeeOrderCount(int $employee_id): int
    {
        $stmt = self::getPdo()->prepare("SELECT COUNT(*) as total FROM hoadon WHERE nhanvien_id = :eid");
        $stmt->execute([':eid' => $employee_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }
}

?>