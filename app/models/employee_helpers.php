<?php
class EmployeeHelpers
{
    protected static ?PDO $pdo = null;

    public static function init(?PDO $pdo = null)
    {
        if ($pdo instanceof PDO) { self::$pdo = $pdo; return; }
        if (class_exists('Database')) { try { self::$pdo = Database::getInstance(); return; } catch (Exception $e) {} }
        
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
        
        throw new RuntimeException('PDO not initialized for EmployeeHelpers');
    }

    public static function getOrderCountForEmployee(int $employee_id): int
    {
        $stmt = self::getPdo()->prepare("SELECT COUNT(*) as count FROM hoadon WHERE nhanvien_id = :id");
        $stmt->execute([':id' => $employee_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['count'] ?? 0);
    }


    public static function getDashboardStats($period = 'month')
    {
        $hoadonDateCondition = '';
        switch ($period) {
            case 'today':
                $hoadonDateCondition = "DATE(hd.ngaytao) = CURDATE()";
                break;
            case 'week':
                $hoadonDateCondition = "hd.ngaytao >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last_month':
                $hoadonDateCondition = "MONTH(hd.ngaytao) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(hd.ngaytao) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
                break;
            case 'month':
            default:
                $hoadonDateCondition = "MONTH(hd.ngaytao) = MONTH(CURDATE()) AND YEAR(hd.ngaytao) = YEAR(CURDATE())";
                break;
        }

        $khachhangDateCondition = '';
        switch ($period) {
            case 'today':
                $khachhangDateCondition = "DATE(kh.ngaythamgia) = CURDATE()";
                break;
            case 'week':
                $khachhangDateCondition = "kh.ngaythamgia >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last_month':
                $khachhangDateCondition = "MONTH(kh.ngaythamgia) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(kh.ngaythamgia) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
                break;
            case 'month':
            default:
                $khachhangDateCondition = "MONTH(kh.ngaythamgia) = MONTH(CURDATE()) AND YEAR(kh.ngaythamgia) = YEAR(CURDATE())";
                break;
        }

        $stats = [];

        $stmt = self::getPdo()->prepare("SELECT COALESCE(SUM(tongtien), 0) as total FROM hoadon hd WHERE $hoadonDateCondition AND hd.trangthai != 'da_huy'");
        $stmt->execute();
        $stats['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM hoadon hd WHERE $hoadonDateCondition");
        $stmt->execute();
        $stats['orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(cthd.soluong), 0) as total FROM chitiethoadon cthd JOIN hoadon hd ON cthd.hoadon_id = hd.hoadon_id WHERE $hoadonDateCondition AND hd.trangthai != 'da_huy'");
        $stmt->execute();
        $stats['products_sold'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM khachhang kh WHERE $khachhangDateCondition");
        $stmt->execute();
        $stats['customers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        return $stats;
    }

   
    public static function getTopSellingProducts($limit = 12, $startDate = null, $endDate = null, $categoryId = null)
    {
        $pdo = self::getPdo();
        $query = "SELECT sp.sanpham_id, COALESCE(s.tenSach, v.tenVPP) as ten_sanpham, SUM(cthd.soluong) as da_ban, SUM(cthd.thanhtien) as tong_tien 
              FROM chitiethoadon cthd 
              JOIN sanpham sp ON cthd.sanpham_id = sp.sanpham_id 
              LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id 
              LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id 
              JOIN hoadon hd ON cthd.hoadon_id = hd.hoadon_id
              LEFT JOIN danhmucsanpham dm ON sp.danhmucSP_id = dm.danhmucSP_id 
              WHERE hd.trangthai != 'da_huy'";
        $params = [];
        if ($startDate !== null) {
            $query .= " AND hd.ngaytao >= :startDate";
            $params[':startDate'] = $startDate . ' 00:00:00';
        }
        if ($endDate !== null) {
            $query .= " AND hd.ngaytao <= :endDate";
            $params[':endDate'] = $endDate . ' 23:59:59';
        }
        if ($categoryId !== null) {
            $query .= " AND dm.danhmucSP_id = :categoryId";
            $params[':categoryId'] = $categoryId;
        }
       
        $query .= " GROUP BY sp.sanpham_id, s.tenSach, v.tenVPP ORDER BY da_ban DESC LIMIT :limit";
        $stmt = $pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function canAccessReports()
    {
        global $_SESSION;
        $employeeRole = $_SESSION['employee_account']['role'] ?? 'nhanvien';
        return in_array($employeeRole, ['admin', 'quanly']);
    }

    public static function canCreateAndApprove()
    {
        global $_SESSION;
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
        $pdo = self::getPdo();
 
        $startDate = '';
        $endDate = '';

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
 
        $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(tongtien), 0) as total 
        FROM hoadon
        WHERE ngaytao >= :start AND ngaytao <= :end AND trangthai != 'da_huy'
    ");
        $stmt->execute([':start' => $startDate, ':end' => $endDate]);
        $stats['revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
 
        $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM hoadon
        WHERE ngaytao >= :start AND ngaytao <= :end
    ");
        $stmt->execute([':start' => $startDate, ':end' => $endDate]);
        $stats['orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
 
        $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM khachhang
        WHERE ngaythamgia >= :start AND ngaythamgia <= :end
    ");
        $stmt->execute([':start' => $startDate, ':end' => $endDate]);
        $stats['customers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        return $stats;
    }
 
    public static function getLowStockProducts($threshold = 10)
    {
        $pdo = self::getPdo();

        $stmt = $pdo->prepare("\n        SELECT \n            sp.sanpham_id,\n            COALESCE(s.tenSach, v.tenVPP) as ten_sanpham,\n            sp.soluongton,\n            sp.gia\n        FROM sanpham sp\n        LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id\n        LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id\n        WHERE sp.soluongton <= :threshold\n        ORDER BY sp.soluongton ASC\n        LIMIT 10\n    ");
        $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

 
    public static function getRevenueByDateRange($startDate, $endDate)
    {
        $pdo = self::getPdo();

        $stmt = $pdo->prepare("\n        SELECT \n            DATE(ngaytao) as date,\n            COUNT(*) as order_count,\n            COALESCE(SUM(tongtien), 0) as total_revenue\n        FROM hoadon\n        WHERE ngaytao >= :start AND ngaytao <= :end AND trangthai != 'da_huy'\n        GROUP BY DATE(ngaytao)\n        ORDER BY DATE(ngaytao) ASC\n    ");
        $stmt->execute([
            ':start' => $startDate . ' 00:00:00',
            ':end' => $endDate . ' 23:59:59'
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 
    public static function getRevenueSummary($period = 'month')
    {
        $pdo = self::getPdo();

        $dateCondition = '';
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

        $stmt = $pdo->prepare("\n        SELECT \n            COUNT(*) as order_count,\n            COALESCE(SUM(tongtien), 0) as total_revenue,\n            AVG(tongtien) as avg_order_value\n        FROM hoadon\n        WHERE $dateCondition AND trangthai != 'da_huy'\n    ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public static function getCustomerStatistics($startDate = null, $endDate = null)
    {
        $pdo = self::getPdo();

        $stats = [];

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM khachhang");
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;


        $stmt = $pdo->query("SELECT COUNT(*) as total 
                         FROM khachhang 
                         WHERE DATE(ngaythamgia) = CURDATE()");
        $stats['new_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;


        $stmt = $pdo->query("SELECT COUNT(*) as total 
                         FROM khachhang 
                         WHERE ngaythamgia >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $stats['new_week'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;


        $stmt = $pdo->query("SELECT COUNT(*) as total 
                         FROM khachhang 
                         WHERE MONTH(ngaythamgia) = MONTH(CURDATE()) 
                           AND YEAR(ngaythamgia) = YEAR(CURDATE())");
        $stats['new_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $query = "SELECT COUNT(*) as total FROM khachhang WHERE 1";
        $params = [];

        if (!empty($startDate)) {
            $query .= " AND DATE(ngaythamgia) >= :start";
            $params[':start'] = $startDate;
        }

        if (!empty($endDate)) {
            $query .= " AND DATE(ngaythamgia) <= :end";
            $params[':end'] = $endDate;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $stats['in_range'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;


        return $stats;
    }




    public static function getOrderStatistics(): array
    {
        $pdo = self::getPdo();
        // Chờ đơn
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM hoadon WHERE trangthai = 'cho_xac_nhan'");
        $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Đơn đã xác nhận
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM hoadon WHERE trangthai = 'da_xac_nhan'");
        $stats['confirmed'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Đơn đang giao hàng
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM hoadon WHERE trangthai = 'dang_giao_hang'");
        $stats['shipped'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Đơn đã giao hàng
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM hoadon WHERE trangthai = 'da_giao_hang'");
        $stats['delivered'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Tổng doanh thu
        $stmt = $pdo->query("SELECT COALESCE(SUM(tongtien), 0) as total FROM hoadon WHERE trangthai != 'da_huy'");
        $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        return $stats;
    }

    public static function getInventoryStatus(): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->query("SELECT COUNT(*) as total_products, SUM(soluongton) as total_stock, SUM(gia * soluongton) as total_inventory_value FROM sanpham");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getRecentOrders($limit = 10): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT hd.hoadon_id, kh.ho + kh.tendem + kh.ten as ho_ten, hd.ngaytao, hd.tongtien, hd.trangthai FROM hoadon hd JOIN khachhang kh ON hd.khachhang_id = kh.khachhang_id ORDER BY hd.ngaytao DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách sản phẩm bán ế
     * @param int $limit Số lượng sản phẩm cần lấy
     * @param int $days Số ngày để tính toán (mặc định 30 ngày)
     * @return array Danh sách sản phẩm bán ế
     */
    public static function getSlowMovingProducts($limit = 10, $days = 30): array
    {
        $pdo = self::getPdo();
        
        // Lấy sản phẩm có số lượng bán thấp nhất hoặc không bán được trong X ngày gần đây
        $stmt = $pdo->prepare("
            SELECT 
                sp.sanpham_id,
                COALESCE(s.tenSach, v.tenVPP) as ten_sanpham,
                sp.soluongton,
                sp.gia,
                COALESCE(SUM(cthd.soluong), 0) as da_ban,
                COALESCE(SUM(cthd.thanhtien), 0) as doanh_thu,
                MAX(hd.ngaytao) as ngay_ban_cuoi
            FROM sanpham sp
            LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id
            LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id
            LEFT JOIN chitiethoadon cthd ON sp.sanpham_id = cthd.sanpham_id
            LEFT JOIN hoadon hd ON cthd.hoadon_id = hd.hoadon_id 
                AND hd.ngaytao >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                AND hd.trangthai != 'da_huy'
            WHERE sp.soluongton > 0
            GROUP BY sp.sanpham_id, s.tenSach, v.tenVPP, sp.soluongton, sp.gia
            HAVING da_ban <= 5
            ORDER BY da_ban ASC, sp.soluongton DESC
            LIMIT :limit
        ");
        
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>