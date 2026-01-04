<?php
class CartModel
{
    protected static ?PDO $pdo = null;

    public static function init(?PDO $pdo = null)
    {
        if ($pdo instanceof PDO) { self::$pdo = $pdo; return; }
        if (class_exists('Database')) { try { self::$pdo = Database::getInstance(); return; } catch (Exception $e) {} } 
    }

    protected static function getPdo()
    {
        if (self::$pdo instanceof PDO) return self::$pdo;
        if (class_exists('Database')) {
            try {
                self::$pdo = Database::getInstance();
                return self::$pdo;
            } catch (Exception $e) {
             
            }
        }
      
        throw new RuntimeException('PDO not initialized for CartModel');
    }

    public static function getCart(int $khachhang_id): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM giohang WHERE khachhang_id = :kh LIMIT 1");
        $stmt->execute([':kh' => $khachhang_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            $stmt = $pdo->prepare("INSERT INTO giohang (khachhang_id, ngaytao, soluong) VALUES (:kh, NOW(), 0)");
            $stmt->execute([':kh' => $khachhang_id]);
            $id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM giohang WHERE giohang_id = :id");
            $stmt->execute([':id' => $id]);
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $cart ?: [];
    }

    public static function getCartItems(int $khachhang_id): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT ct.ctgh_id, ct.giohang_id, ct.sanpham_id, ct.soluong, ct.dongia as gia, (ct.soluong * ct.dongia) AS thanhtien, 
        CASE WHEN s.sanpham_id IS NOT NULL THEN s.tenSach WHEN v.vpp_id IS NOT NULL 
        THEN v.tenVPP ELSE sp.mo_ta END AS name, 
        -- sp.danhmucSP_id AS type, 
        d.tenDanhMuc AS type,
        sp.hinhanh 
        FROM chitietgiohang ct INNER JOIN giohang g ON g.giohang_id = ct.giohang_id 
        INNER JOIN sanpham sp ON sp.sanpham_id = ct.sanpham_id 
        LEFT JOIN sach s ON s.sanpham_id = ct.sanpham_id 
        LEFT JOIN vanphongpham v ON v.sanpham_id = ct.sanpham_id 
        LEFT JOIN danhmucsanpham d ON d.danhmucSP_id = sp.danhmucSP_id
        WHERE g.khachhang_id = :kh";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':kh' => $khachhang_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function refreshCartCount(int $giohang_id): void
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(soluong),0) as total_qty FROM chitietgiohang WHERE giohang_id = :gid");
        $stmt->execute([':gid' => $giohang_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = $row['total_qty'] ?? 0;
        $stmt = $pdo->prepare("UPDATE giohang SET soluong = :total WHERE giohang_id = :gid");
        $stmt->execute([':total' => $total, ':gid' => $giohang_id]);
    }

    public static function addCartItem(int $customer_id, int $product_id, int $quantity, float $price): void
    {
        $pdo = self::getPdo();
        $cart = self::getCart($customer_id);
        $gid = $cart['giohang_id'];
        $stmt = $pdo->prepare("SELECT * FROM chitietgiohang WHERE giohang_id = :gid AND sanpham_id = :pid LIMIT 1");
        $stmt->execute([':gid' => $gid, ':pid' => $product_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($item) {
            $currentQty = (int)($item['soluong'] ?? 0);
            $dongia = (float)($item['dongia'] ?? 0);
            $newQty = $currentQty + $quantity;
            $newTotal = $newQty * $dongia;
            $stmt = $pdo->prepare("UPDATE chitietgiohang SET soluong = :newQty, thanhtien = :newTotal WHERE ctgh_id = :id");
            $stmt->execute([':newQty' => $newQty, ':newTotal' => $newTotal, ':id' => $item['ctgh_id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO chitietgiohang (giohang_id, sanpham_id, soluong, dongia, thanhtien) VALUES (:gid, :pid, :qty, :price, :total)");
            $stmt->execute([':gid' => $gid, ':pid' => $product_id, ':qty' => $quantity, ':price' => $price, ':total' => $price * $quantity]);
        }
        self::refreshCartCount($gid);
    }

    // Thêm hoặc cập nhật các sản phẩn phẩm vào giỏ hàng của khách hàng từ mảng tạm khi khách hàng chưa đăng nhập
    public static function addCartItems(int $customer_id, array $items): void
    {
        foreach ($items as $item) {
            $product_id = (int)($item['product_id'] ?? 0);
            $quantity = (int)($item['quantity'] ?? 0);
            $price = (float)($item['price'] ?? 0);
            if ($product_id > 0 && $quantity > 0) {
                self::addCartItem($customer_id, $product_id, $quantity, $price);
            }
        }
    }

    public static function updateCartItem(int $customer_id, int $product_id, int $quantity): void
    {
        $pdo = self::getPdo();
        $cart = self::getCart($customer_id);
        $gid = $cart['giohang_id'];
        if ($quantity <= 0) {
            $stmt = $pdo->prepare("DELETE FROM chitietgiohang WHERE giohang_id = :gid AND sanpham_id = :pid");
            $stmt->execute([':gid' => $gid, ':pid' => $product_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE chitietgiohang SET soluong = :qty, thanhtien = :qty_total * dongia WHERE giohang_id = :gid AND sanpham_id = :pid");
            $stmt->execute([':qty' => $quantity, ':qty_total' => $quantity, ':gid' => $gid, ':pid' => $product_id]);
        }
        self::refreshCartCount($gid);
    }

    public static function cartTotal(int $customer_id): float
    {
        $pdo = self::getPdo();
        $cart = self::getCart($customer_id);
        $gid = $cart['giohang_id'];
        $stmt = $pdo->prepare("SELECT SUM(thanhtien) as total FROM chitietgiohang WHERE giohang_id = :gid");
        $stmt->execute([':gid' => $gid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($row['total'] ?? 0);
    }

    public static function clearCart(int $giohang_id): void
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("DELETE FROM chitietgiohang WHERE giohang_id = :gid");
        $stmt->execute([':gid' => $giohang_id]);
        self::refreshCartCount($giohang_id);
    }

}
?>