<?php
class NotificationModel
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

        throw new RuntimeException('PDO not initialized for Notification model');
    }

    public static function getCustomerNotifications(int $khachhang_id, int $limit = 10): array
    {
        $pdo = self::getPdo();
        $limit = (int)$limit;
        $stmt = $pdo->prepare("SELECT * FROM thongbao WHERE khachhang_id = :khachhang_id ORDER BY ngay_tao DESC LIMIT $limit");
        $stmt->bindValue(':khachhang_id', $khachhang_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countUnreadNotifications(int $khachhang_id): int
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM thongbao WHERE khachhang_id = :khachhang_id AND trang_thai = 'chua_doc'");
        $stmt->execute([':khachhang_id' => $khachhang_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }

    public static function createNotification(int $khachhang_id, string $tieu_de, string $noi_dung): bool
    {
        $pdo = self::getPdo();
        try {
            $stmt = $pdo->prepare("INSERT INTO thongbao (khachhang_id, tieu_de, noi_dung, ngay_tao, loai) VALUES (:khachhang_id, :tieu_de, :noi_dung, NOW(), :loai)");
            return $stmt->execute([':khachhang_id' => $khachhang_id, ':tieu_de' => $tieu_de, ':noi_dung' => $noi_dung, ':loai' => 'don_hang']);
        } catch (PDOException $e) {
            error_log("Lỗi tạo thông báo: " . $e->getMessage());
            return false;
        }
    }

    public static function isNotificationReaded(int $thongbao_id, int $khachhang_id): bool
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT trang_thai FROM thongbao WHERE thongbao_id = :id AND khachhang_id = :khachhang_id");
        $stmt->execute([':id' => $thongbao_id, ':khachhang_id' => $khachhang_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && ($result['trang_thai'] === 'da_doc');
    }

    public static function markNotificationRead(int $thongbao_id, int $khachhang_id, bool $isRead = true): bool
    {
        $pdo = self::getPdo();
        $status = $isRead ? 'da_doc' : 'chua_doc';
        $stmt = $pdo->prepare("UPDATE thongbao SET trang_thai = :status WHERE thongbao_id = :id AND khachhang_id = :khachhang_id");
        return $stmt->execute([':status' => $status, ':id' => $thongbao_id, ':khachhang_id' => $khachhang_id]);
    }

    public static function archiveNotification(int $thongbao_id, int $khachhang_id): bool
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("UPDATE thongbao SET trang_thai = 'luu_tru' WHERE thongbao_id = :id AND khachhang_id = :khachhang_id");
        return $stmt->execute([':id' => $thongbao_id, ':khachhang_id' => $khachhang_id]);
    }

    public static function markAllRead(int $khachhang_id): bool
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("UPDATE thongbao SET trang_thai = 'da_doc' WHERE khachhang_id = :khachhang_id AND trang_thai <> 'luu_tru'");
        return $stmt->execute([':khachhang_id' => $khachhang_id]);
    }

    public static function deleteNotification(int $thongbao_id): bool
    {
        $pdo = self::getPdo();
        try {
            $stmt = $pdo->prepare("DELETE FROM thongbao WHERE thongbao_id = :id");
            return $stmt->execute([':id' => $thongbao_id]);
        } catch (PDOException $e) {
            error_log("Lỗi xóa thông báo: " . $e->getMessage());
            return false;
        }
    }
}
