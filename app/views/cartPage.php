<div class="container mt-5 pb-5">

    <!-- Tiêu đề -->
    <h2 class="fw-bold mb-4">Giỏ hàng</h2>

    <?php
    // Allow guests to view a temporary cart built from session
    if (empty($customer)) {
        echo '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Bạn chưa đăng nhập. Giỏ hàng sẽ được lưu tạm trong phiên này.</div>';
    }

    $items = $cart_items ?? (empty($customer) ? [] : CartModel::getCartItems($customer['id']));
    $has_insufficient = false;
    if (empty($items)): ?>

        <div class="alert alert-info">
            <i class="bi bi-cart-x me-2"></i> Giỏ hàng của bạn đang trống.
        </div>

    <?php else: ?>

        <?php
        $total = $cart_total ?? 0;
        if (empty($total)) {
            foreach ($items as $i) {
                $total += ($i['gia'] ?? 0) * ($i['soluong'] ?? 0);
            }
        }
        ?>

        <!-- Layout 2 cột -->
        <div class="row pb-5">
            <!-- Cột trái – Danh sách sản phẩm -->
            <div class="col-lg-8">

                <!-- DANH SÁCH SẢN PHẨM DẠNG BẢNG -->
                <div class="table-responsive mb-4">
                    <table class="table table-striped table-hover table-bordered align-middle shadow-sm cart-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Thành tiền</th>
                                <th class="text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($items as $item):
                                $itemTotal = ($item['gia'] ?? 0) * ($item['soluong'] ?? 0);
                                $stock = 0;
                                try {
                                    $p = ProductModel::getProductById((int)$item['sanpham_id']);
                                    $stock = (int)($p['soluongton'] ?? $p['soluong'] ?? 0);
                                } catch (Throwable $e) {
                                    $stock = 0;
                                }
                                $insufficient = ($stock < (int)($item['soluong'] ?? 0));
                                if ($insufficient) $has_insufficient = true;

                            ?>
                                <tr class="cart-row <?= $insufficient ? 'table-warning' : '' ?>">
                                    <!-- Sản phẩm -->
                                    <td data-label="Sản phẩm">
                                        <div class="d-flex gap-3 align-items-center">
                                            <a href="index.php?page=productview&id=<?= $item['sanpham_id'] ?>">
                                                <img src="assets/images/products/<?= htmlspecialchars($item['hinhanh'] ?? 'defaultProduct.png') ?>"
                                                    class="cart-thumb"
                                                    onerror="this.src='assets/images/products/defaultProduct.png'">
                                            </a>

                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($item['name']) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($item['type']) ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Đơn giá -->
                                    <td data-label="Đơn giá" class="text-center fw-semibold text-primary">
                                        <div><?= number_format($item['gia'], 0) ?>₫</div>
                                        <input type="hidden" name="gia[]" value="<?= htmlspecialchars($item['gia']) ?>">
                                    </td>

                                    <!-- Số lượng -->
                                    <td data-label="Số lượng" class="text-center">
                                        <form action="index.php?page=cart_update" method="POST" class="d-inline-flex">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['sanpham_id']) ?>">

                                            <?php if ($insufficient): ?>
                                                <!-- <div class="text-danger small">Sản phẩm này không còn tồn tại hoặc số lượng ko đủ.</div> -->
                                                <div class="input-group input-group-sm quantity-box">
                                                    <input type="number" name="quantity" value="<?= $item['soluong'] ?>" min="1" max="<?= $stock ?>" step="1" class="form-control text-center qty-input" disabled>
                                                </div>
                                            <?php else: ?>
                                                <div class="input-group input-group-sm quantity-box">
                                                    <button class="btn btn-outline-secondary btn-sm" name="decrease" type="submit">-</button>
                                                    <input type="number" name="quantity" value="<?= $item['soluong'] ?>" min="1" max="99" step="1" class="form-control text-center qty-input">
                                                    <button class="btn btn-outline-secondary btn-sm" name="increase" type="submit">+</button>
                                                </div>
                                            <?php endif; ?>
                                        </form>
                                    </td>

                                    <!-- Thành tiền -->
                                    <td data-label="Thành tiền" class="text-end fw-bold text-primary fs-5">
                                        <?= number_format($itemTotal, 0) ?>₫
                                    </td>

                                    <!-- Xóa -->
                                    <td data-label="Xóa" class="text-center swipe-actions">
                                        <form action="index.php?page=cart_remove" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['sanpham_id']) ?>">
                                            <button class="btn btn-link text-danger p-0 delete-btn" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');" data-bs-toggle="tooltip" data-bs-placement="top">
                                                <i class="bi bi-trash-fill fs-5"></i>
                                            </button>
                                        </form>
                                        <?php if ($insufficient): ?>
                                            <div class="mt-2 small text-warning">Sản phẩm này không còn tồn tại hoặc số lượng ko đủ.</div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Nút điều hướng -->
                <div class="d-flex justify-content-between mb-4 p-0">
                    <a href="index.php" class="btn btn-light border fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Tiếp tục mua sắm
                    </a>
                </div>


            </div>


            <!-- Cột phải – Tóm tắt đơn hàng -->
            <div class="col-lg-4">

                <div class="card shadow-sm border-0 position-sticky" style="top:90px">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Tóm tắt đơn hàng</h5>

                        <div class="border-top pt-3 d-flex justify-content-between"></div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span class="fw-bold"><?= number_format($total, 0) ?>₫</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển</span>
                            <span class="fw-bold"><?= number_format(0, 0) ?>₫</span>
                        </div>

                        <div class="border-top pt-3 d-flex justify-content-between mt-4">
                            <span class="fw-bold fs-5">Tổng cộng</span>
                            <span class="text-primary fw-bold fs-4">
                                <?= number_format($total, 0) ?>₫
                            </span>
                        </div>

                        <?php if ($has_insufficient): ?>
                            <button class="btn btn-secondary w-100 fw-bold mt-4 py-2 fs-5" disabled>Thanh toán (có sản phẩm không đủ)</button>
                            <div class="mt-2 small text-warning">Sản phẩm này không còn tồn tại hoặc số lượng ko đủ.</div>
                        <?php else: ?>
                            <form action="index.php?page=checkout" method="POST" class="w-100">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <button type="submit" class="btn btn-primary w-100 fw-bold mt-4 py-2 fs-5">Thanh toán ngay</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

        <!-- Thanh toán nhanh (mobile) -->
        <div class="checkout-bar d-md-none bg-white shadow-lg p-3 
                position-fixed w-100 bottom-0 left-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-bold fs-5 text-primary">
                    <?= number_format($total, 0) ?>₫
                </div>
                <form action="index.php?page=checkout" method="POST" class="m-0">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <button type="submit" class="btn btn-primary fw-bold px-4">Thanh toán</button>
                </form>
            </div>
        </div>

        <script>
            var tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipList.map(el => new bootstrap.Tooltip(el));
        </script>

    <?php endif; ?>

</div>