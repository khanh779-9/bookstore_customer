<?php
if (!isset($customer)) {
    include 'loginPage.php';
    return;
}

$customerId = (int)($customer['id'] ?? 0);

// If an order id is provided, show order detail
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId > 0) {
    $order = OrdersModel::getOrderDetailsById($orderId);
    if (empty($order) || ((int)($order['khachhang_id'] ?? 0) !== $customerId)) {
        echo '<div class="container py-5"><div class="alert alert-danger">Đơn hàng không tồn tại hoặc bạn không có quyền xem.</div></div>';
        return;
    }

?>
    <div class="container mt-5 pb-5">
        <a href="index.php?page=orders" class="btn btn-light border mb-5">Quay lại danh sách đơn</a>
        <h4 class="mt-3 mb-3">Chi tiết đơn hàng #<?= htmlspecialchars($order['hoadon_id']) ?></h4>

        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>Khách hàng:</strong> <?= htmlspecialchars($customer['ho_ten'] ?? '') ?></p>
                <p><strong>Ngày đặt:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($order['ngaytao']))) ?></p>
                <p><strong>Đặt lúc: </strong><?= htmlspecialchars(date('H:i:s', strtotime($order['ngaytao']))) ?></p>
                <p><strong>Địa chỉ giao hàng:</strong> <?= htmlspecialchars($order['diachi_giaohang'] ?? '') ?></p>
            </div>
            <div class="col-md-6 text-end">
                <p><strong>Trạng thái:</strong> <?= htmlspecialchars( translate_order_status($order['trangthai'] ?? '') ) ?></p>
                <p><strong>Phương thức:</strong> <?= htmlspecialchars(translate_payment_method($order['phuongthuc_thanhtoan'] ?? '')) ?></p>
                <p><strong>Tổng tiền:</strong> <span class="fw-bold text-primary"><?= number_format($order['tongtien'] ?? 0, 0, ',', '.') ?>₫</span></p>
            </div>
        </div>

        <h5>Sản phẩm</h5>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th style="width:120px">Số lượng</th>
                        <th style="width:140px">Đơn giá</th>
                        <th style="width:140px">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $it): ?>
                        <tr>
                            <td data-label="Ảnh" style="width:80px"><img src="assets/images/products/<?= htmlspecialchars($it['hinhanh'] ?? '') ?>" alt="" style="max-width:70px"></td>
                            <td data-label="Sản phẩm"><?= htmlspecialchars($it['ten_sanpham'] ?? 'N/A') ?></td>
                            <td data-label="Số lượng"><?= (int)($it['soluong'] ?? 0) ?></td>
                            <td data-label="Đơn giá" class="text-end"><?= number_format((float)($it['dongia'] ?? 0), 0, ',', '.') ?>₫</td>
                            <td data-label="Thành tiền" class="text-end"><?= number_format((float)($it['thanhtien'] ?? (($it['dongia'] ?? 0) * ($it['soluong'] ?? 0))), 0, ',', '.') ?>₫</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
    return;
}

// Otherwise show list of customer's orders
$orders = OrdersModel::getOrdersByCustomer($customerId);
?>
<div class="container py-5">
    <h4 class="mb-3">Đơn hàng của tôi</h4>
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Ngày tạo</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td data-label="Mã đơn hàng">#<?= htmlspecialchars($order['hoadon_id']) ?></td>
                            <td data-label="Ngày tạo"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($order['ngaytao']))) ?></td>
                            <td data-label="Tổng tiền" class="text-end"><?= number_format((float)($order['tongtien'] ?? 0), 0, ',', '.') ?>₫</td>
                            <td data-label="Trạng thái"><?= htmlspecialchars(translate_order_status($order['trangthai'] ?? '')) ?></td>
                            <td data-label="Hành động" class="swipe-actions"><a href="index.php?page=orders&id=<?= (int)$order['hoadon_id'] ?>" class="btn btn-sm btn-outline-primary">Xem chi tiết</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>