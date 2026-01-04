<?php
// Simple checkout placeholder view — implement order creation here later
// If preparer provided data use it, otherwise fallback to existing model calls
$items = $cart_items ?? [];
$total = $cart_total ?? 0;
if (empty($items) && !empty($customer)) {
    $items = CartModel::getCartItems($customer['id']);
    foreach ($items as $it) $total += (($it['gia'] ?? 0) * ($it['soluong'] ?? 0));
}
if (empty($customer)) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để thanh toán.';
    header('Location: index.php?page=login');
    exit;
}
?>
<div class="container mt-5">
    <h2 class="fw-bold mb-4">Thanh toán</h2>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">Giỏ hàng trống.</div>
        <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
    <?php else: ?>
        <div class="card p-4 mb-3">
            <h5>Đơn hàng của bạn</h5>
            <ul class="list-unstyled">
                <?php foreach ($items as $it): ?>
                    <li class="d-flex justify-content-between py-1">
                        <div><?= htmlspecialchars($it['name']) ?> x <?= $it['soluong'] ?></div>
                        <div><?= number_format($it['gia'] * $it['soluong'],0) ?>₫</div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex justify-content-between fw-bold fs-4 mt-3">Tổng cộng: <div><?= number_format($total,0) ?>₫</div></div>
        </div>

        <form method="post" action="index.php?page=checkout_confirm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            <button type="submit" class="btn btn-success">Xác nhận và đặt hàng</button>
        </form>

    <?php endif; ?>
</div>
