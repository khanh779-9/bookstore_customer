<?php
class OrdersModel
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

        throw new RuntimeException('PDO not initialized for OrdersModel');
    }

    public static function getAllOrders(): array
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM hoadon ORDER BY ngaytao DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOrdersPage(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';
        if (!empty($search)) {
            // simple match on order id or customer name/email
            $where = "WHERE (hoadon_id = :exact OR kh.ho LIKE :q OR kh.ten LIKE :q OR kh.email LIKE :q)";
            $params[':q'] = '%' . $search . '%';
            $params[':exact'] = is_numeric($search) ? (int)$search : 0;
        }

        $pdo = self::getPdo();
        if (!empty($where)) {
            $countSql = "SELECT COUNT(*) FROM hoadon h JOIN khachhang kh ON h.khachhang_id = kh.khachhang_id " . $where;
            $stmt = $pdo->prepare($countSql);
            $stmt->execute($params);
            $total = (int)$stmt->fetchColumn();

            $sql = "SELECT h.* FROM hoadon h JOIN khachhang kh ON h.khachhang_id = kh.khachhang_id " . $where . " ORDER BY h.ngaytao DESC LIMIT :limit OFFSET :offset";
            $stmt = $pdo->prepare($sql);
            foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        } else {
            $countSql = "SELECT COUNT(*) FROM hoadon";
            $stmt = $pdo->prepare($countSql);
            $stmt->execute();
            $total = (int)$stmt->fetchColumn();

            $sql = "SELECT * FROM hoadon ORDER BY ngaytao DESC LIMIT :limit OFFSET :offset";
            $stmt = $pdo->prepare($sql);
        }

        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['items' => $items, 'total' => $total];
    }

    public static function getOrdersByCustomer(int $customerId): array
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM hoadon WHERE khachhang_id = :customerId ORDER BY ngaytao DESC");
        $stmt->execute([':customerId' => $customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOrderById(int $hoadon_id)
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM hoadon WHERE hoadon_id = :id LIMIT 1");
        $stmt->execute([':id' => $hoadon_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getOrderItemsByOrderId(int $hoadon_id): array
    {
        $stmt = self::getPdo()->prepare(
            "SELECT cthd.*, COALESCE(s.tenSach, v.tenVPP) AS ten_sanpham, sp.hinhanh
             FROM chitiethoadon cthd
             JOIN sanpham sp ON cthd.sanpham_id = sp.sanpham_id
             LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id
             LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id
             WHERE cthd.hoadon_id = :id"
        );
        $stmt->execute([':id' => $hoadon_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = self::getPdo()->prepare("SELECT * FROM hoadon ORDER BY ngaytao DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateOrderStatus(int $hoadon_id, string $order_status, int $nhanvien_id = null): bool
    {
        $pdo = self::getPdo();
        try {
            $pdo->beginTransaction();

            // Update order status and total
            $stmt = $pdo->prepare("
            UPDATE hoadon
            SET trangthai = :status,
            tongtien = (
                SELECT COALESCE(SUM(soluong * dongia), 0)
                FROM chitiethoadon
                WHERE hoadon_id = :id
            ),
            nhanvien_id = :nhanvien_id
            WHERE hoadon_id = :id;
        ");

            $result = $stmt->execute([
                ':status' => $order_status,
                ':nhanvien_id' => $nhanvien_id,
                ':id'     => $hoadon_id
            ]);

            // If order is delivered (da_giao_hang), update product quantities
            if ($result && $order_status === 'da_giao_hang') {
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


    public static function createOrder(int $customerId, array $items, string $paymentMethod = 'tien_mat', $shippingOrAddress = null, bool $sendNotification = true, string $trangthai='cho_thanh_toan')
    {
        $pdo = self::getPdo();
        try {
            $pdo->beginTransaction();
            $total = 0.0;
            foreach ($items as $it) {
                $qty = (int)($it['soluong'] ?? $it['quantity'] ?? 0);
                $price = floatval($it['dongia'] ?? $it['gia'] ?? $it['price'] ?? 0);
                $total += $qty * $price;
            }
            $dcgh_id = null;

            if (is_int($shippingOrAddress) && $shippingOrAddress > 0) {
                $dcgh_id = $shippingOrAddress;
            } elseif (is_string($shippingOrAddress) && $shippingOrAddress !== '' && class_exists('AddressesModel')) {
                try {
                    $dcgh_id = AddressesModel::addAddress($customerId, $shippingOrAddress);
                } catch (Throwable $__) {
                    $dcgh_id = null;
                }
            }

            if ($dcgh_id) {
                $stmt = $pdo->prepare("INSERT INTO hoadon (khachhang_id, ngaytao, tongtien, phuongthuc_thanhtoan, trangthai, dcgh_id) VALUES (:kh, :ngay, :tong, :pm, :trang, :dc)");
                $ok = $stmt->execute([
                    ':kh' => $customerId,
                    ':ngay' => date('Y-m-d H:i:s'),
                    ':tong' => $total,
                    ':pm' => $paymentMethod,
                    ':trang' => $trangthai,
                    ':dc' => $dcgh_id
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO hoadon (khachhang_id, ngaytao, tongtien, phuongthuc_thanhtoan, trangthai) VALUES (:kh, :ngay, :tong, :pm, :trang)");
                $ok = $stmt->execute([
                    ':kh' => $customerId,
                    ':ngay' => date('Y-m-d H:i:s'),
                    ':tong' => $total,
                    ':pm' => $paymentMethod,
                    ':trang' => $trangthai
                ]);
            }

            if (!$ok) {
                $pdo->rollBack();
                return false;
            }
            $hoadon_id = (int)$pdo->lastInsertId();

            $stmtItem = $pdo->prepare("INSERT INTO chitiethoadon (hoadon_id, sanpham_id, soluong, dongia, thanhtien) VALUES (:hid, :sp, :qty, :dg, :tt)");
            $stmtUpdateStock = $pdo->prepare("UPDATE sanpham SET soluongton = GREATEST(0, soluongton - :qty) WHERE sanpham_id = :spid");

            foreach ($items as $it) {
                $sp = (int)($it['product_id'] ?? $it['sanpham_id'] ?? 0);
                $qty = (int)($it['soluong'] ?? $it['quantity'] ?? 0);
                $price = floatval($it['dongia'] ?? $it['gia'] ?? $it['price'] ?? 0);
                $lineTotal = $qty * $price;
                if ($sp <= 0 || $qty <= 0) continue;
                $stmtItem->execute([':hid' => $hoadon_id, ':sp' => $sp, ':qty' => $qty, ':dg' => $price, ':tt' => $lineTotal]);
                $stmtUpdateStock->execute([':qty' => $qty, ':spid' => $sp]);
            }

            $pdo->commit();

            if($sendNotification ==true && class_exists('NotificationModel')) {
                try {
                    include_once __DIR__ . '/notification.php';
                    $tr= $trangthai=='cho_thanh_toan' ?'Vui lòng hoàn tất thanh toán.':'Vui long chờ xác nhận đơn hàng.';
                    NotificationModel::createNotification(
                        $customerId,
                        "Đơn hàng",
                        "Đơn hàng #$hoadon_id của bạn đã được tạo thành công.\n$tr",
                        'don_hang'
                    );
                } catch (Throwable $__) {
                }
            }

            return $hoadon_id;
        } catch (Throwable $e) {
            try {
                $pdo->rollBack();
            } catch (Throwable $__) {
            }
            error_log('createOrder failed: ' . $e->getMessage());
            return false;
        }
    }

    public static function finalizePendingOrder(int $hoadon_id, int $customerId, string $paymentMethod = 'tien_mat', ?int $dcgh_id = null, string $newStatus = 'cho_xac_nhan'): bool
    {
        $pdo = self::getPdo();
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT hoadon_id, khachhang_id, trangthai FROM hoadon WHERE hoadon_id = :id LIMIT 1");
            $stmt->execute([':id' => $hoadon_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order || (int)($order['khachhang_id'] ?? 0) !== $customerId || ($order['trangthai'] ?? '') !== 'cho_thanh_toan') {
                $pdo->rollBack();
                return false;
            }

            $sql = "UPDATE hoadon SET phuongthuc_thanhtoan = :pm, trangthai = :st, dcgh_id = :dc, tongtien = (SELECT COALESCE(SUM(soluong * dongia), 0) FROM chitiethoadon WHERE hoadon_id = :id) WHERE hoadon_id = :id";
            $stmtUpdate = $pdo->prepare($sql);
            $stmtUpdate->execute([
                ':pm' => $paymentMethod,
                ':st' => $newStatus,
                ':dc' => $dcgh_id,
                ':id' => $hoadon_id
            ]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            try {
                $pdo->rollBack();
            } catch (Throwable $__) {
            }
            error_log('finalizePendingOrder failed: ' . $e->getMessage());
            return false;
        }
    }

    public static function customerHasPurchasedProduct(int $customerId, int $productId): bool
    {
        $stmt = self::getPdo()->prepare(
            "SELECT COUNT(*) FROM hoadon h JOIN chitiethoadon c ON h.hoadon_id = c.hoadon_id WHERE h.khachhang_id = :kh AND c.sanpham_id = :sp"
        );
        $stmt->execute([':kh' => $customerId, ':sp' => $productId]);
        $count = (int)$stmt->fetchColumn();
        return $count > 0;
    }

    public static function getAllOrdersByDateRange(int $startDate, int $endDate): array
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM hoadon WHERE ngaytao BETWEEN :start AND :end ORDER BY ngaytao DESC");
        $stmt->execute([':start' => date('Y-m-d H:i:s', $startDate), ':end' => date('Y-m-d H:i:s', $endDate)]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOrderStatistics($startDate, $endDate): array
    {
        $stmt = self::getPdo()->prepare("
            SELECT 
                COUNT(*) AS total_orders, 
                SUM(tongtien) AS total_revenue 
            FROM hoadon 
            WHERE ngaytao BETWEEN :start AND :end
        ");
        $stmt->execute([':start' => date('Y-m-d H:i:s', $startDate), ':end' => date('Y-m-d H:i:s', $endDate)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getOrderDetailsById(int $orderId): array
    {

        $stmt = self::getPdo()->prepare("SELECT * FROM hoadon WHERE hoadon_id = :id LIMIT 1");
        $stmt->execute([':id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            return [];
        }
        $stmt = self::getPdo()->prepare("
            SELECT 
                h.*, 
                kh.ho, kh.tendem, kh.ten, 
                CONCAT(kh.ho, ' ', kh.tendem, ' ', kh.ten) AS ho_ten,
                kh.email, kh.sdt, 
                kh.diachi AS diachi_khachhang,
                dc.diachi AS diachi_giaohang,
                cthd.*
            FROM hoadon h
            JOIN khachhang kh ON h.khachhang_id = kh.khachhang_id
            LEFT JOIN chitiethoadon cthd ON h.hoadon_id = cthd.hoadon_id
            LEFT JOIN diachi_giaohang dc ON h.diachi_id = dc.diachi_id
            WHERE h.hoadon_id = :id
            LIMIT 1
        ");

        $stmt = self::getPdo()->prepare("SELECT cthd.*, COALESCE(s.tenSach, v.tenVPP) as ten_sanpham, sp.hinhanh FROM chitiethoadon cthd JOIN sanpham sp ON cthd.sanpham_id = sp.sanpham_id LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id WHERE cthd.hoadon_id = :id");
        $stmt->execute([':id' => $orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $order['items'] = $items;

        return $order ?: [];
    }

    public static function getCustomerByOrderId(int $hoadon_id): ?array
    {
        $stmt = self::getPdo()->prepare("
        SELECT kh.*, CONCAT(kh.ho, ' ', kh.tendem, ' ', kh.ten) AS ho_ten
        FROM hoadon
        JOIN khachhang kh ON hoadon.khachhang_id = kh.khachhang_id
        WHERE hoadon.hoadon_id = :id
        LIMIT 1
    ");

        $stmt->execute([':id' => $hoadon_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public static function getProductsByOrderId(int $hoadon_id): array
    {
        $qe="
        SELECT sp.*, cthd.soluong, cthd.dongia, cthd.thanhtien,
               COALESCE(s.tenSach, v.tenVPP) AS ten_sanpham
        FROM chitiethoadon cthd
        JOIN sanpham sp ON cthd.sanpham_id = sp.sanpham_id
        LEFT JOIN sach s ON sp.sanpham_id = s.sanpham_id
        LEFT JOIN vanphongpham v ON sp.sanpham_id = v.sanpham_id
        WHERE cthd.hoadon_id = :id";
        $stmt = self::getPdo()->prepare($qe);
        $stmt->execute([':id' => $hoadon_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllOrderIsShipping(): array
    {
        $qe="SELECT * FROM hoadon WHERE trangthai = 'dang_giao_hang' ORDER BY ngaytao DESC";
        $stmt = self::getPdo()->prepare($qe);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
