<?php
// View expects: $product (array), $reviews (array)
// Compute derived values if not provided
$product = $product ?? [];
$reviews = $reviews ?? [];
$totalReviews = count($reviews);
$averageRating = $totalReviews ? round(array_sum(array_column($reviews, 'rating')) / $totalReviews, 1) : 0;
$starsCount = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
foreach ($reviews as $r) {
    $starsCount[intval($r['rating'])]++;
}
$otherBooks = isset($product['danhmucSP_id']) ? ProductModel::getProductsByCategory($product['danhmucSP_id']) : [];
// Wishlist state
$currentCustomerId = $_SESSION['khachhang_account']['id'] ?? null;
$isFavorite = false;
if (!empty($product['sanpham_id']) && $currentCustomerId) {
    try {
        $isFavorite = WishlistModel::isProductFavorite((int)$currentCustomerId, (int)$product['sanpham_id']);
    } catch (Throwable $e) {
        $isFavorite = false;
    }
}
?>

<div class="container mt-5 pb-5">

    <div class="row g-4 justify-content-center">

        <!-- Image & buy buttons -->
        <div class="col-lg-6">
            <div class="p-4 pb-2 bg-white border shadow-sm rounded-2 text-center">
                <img src="assets/images/products/<?= htmlspecialchars($product['hinhanh'] ?? 'defaultProduct.png') ?>"
                    class="rounded-3 img-fluid" width="280"
                    alt="<?= htmlspecialchars($product['name'] ?? '') ?>" onerror="this.src='assets/images/products/defaultProduct.png'">

                <div class="d-flex flex-wrap gap-2 mt-4 justify-content-center">
                    <form method="post" action="index.php?page=cart_add">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['sanpham_id'] ?? 0) ?>">
                        <button type="submit" class="btn btn-outline-primary px-4 py-2 fs-6"><i class="bi bi-cart-plus me-1"></i> Thêm</button>
                    </form>

                    <form method="post" action="index.php?page=checkout">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['sanpham_id'] ?? 0) ?>">
                        <button type="submit" class="btn btn-danger px-4 py-2 fs-6"><i class="bi bi-lightning-charge-fill me-1"></i> Mua</button>
                    </form>

                    <div class="d-flex align-items-center">
                        <?php if ($currentCustomerId): ?>
                            <form method="post" action="index.php?page=wishlist_toggle">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['sanpham_id'] ?? 0) ?>">

                                <button type="submit" class="btn <?= $isFavorite ? 'btn-danger' : 'btn-outline-danger' ?> btn-sm d-inline-flex align-items-center justify-content-center"
                                    title="<?= $isFavorite ? 'Bỏ yêu thích' : 'Thêm yêu thích' ?>" style="width:40px; height:40px; padding: 0;">
                                    <i class="bi <?= $isFavorite ? 'bi-heart-fill' : 'bi-heart' ?> fs-6"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="index.php?page=login" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center p-0" style="width:40px; height:40px; padding: 0;" title="Đăng nhập để yêu thích">
                                <i class="bi bi-heart fs-6"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-start mt-4">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-gift me-2"></i> Chính sách ưu đãi</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Miễn phí giao hàng trên 300.000₫</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Đổi trả trong 7 ngày</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Nhiều phương thức thanh toán</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Ưu đãi cho học sinh - sinh viên</li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- Product info -->
        <div class="col-lg-6">
            <div class="p-4 bg-white rounded-2 shadow-sm mb-4">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <h2 class="fw-bold mb-0"><?= htmlspecialchars($product['name'] ?? '') ?></h2>

                </div>
                <?php
                $promotion = get_product_promotion_price($product['sanpham_id'] ?? 0, $product['gia'] ?? 0);
                if ($promotion && isset($promotion['discounted_price']) && $promotion['discounted_price']):
                ?>
                    <div class="my-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="badge bg-danger px-3 py-2 fs-6">-<?= (int)($promotion['promotion']['tilegiamgia'] ?? 0) ?>% GIẢM</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-4">
                            <div class="fs-2 fw-bold text-danger"><?= number_format($promotion['discounted_price']) ?>₫</div>
                            <div class="text-decoration-line-through text-muted fs-5"><?= number_format($product['gia']) ?>₫</div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="my-4">
                        <div class="fs-2 fw-bold text-danger"><?= isset($product['gia']) ? number_format($product['gia']) . '₫' : '' ?></div>
                    </div>
                <?php endif; ?>

                <dl class="row g-2">
                    <dt class="col-4 text-muted">Mã sản phẩm:</dt>
                    <dd class="col-8"><?= htmlspecialchars($product['sanpham_id'] ?? '') ?></dd>
                    <dt class="col-4 text-muted">Nhà cung cấp:</dt>
                    <dd class="col-8"><?= htmlspecialchars($product['provider_name'] ?? '') ?></dd>

                    <?php if (is_book_category((int)($product['danhmucSP_id'] ?? 0))): ?>
                        <dt class="col-4 text-muted">Tác giả:</dt>
                        <dd class="col-8"><?= htmlspecialchars($product['author_name'] ?? '') ?></dd>
                        <dt class="col-4 text-muted">NXB:</dt>
                        <dd class="col-8"><?= htmlspecialchars($product['publisher_name'] ?? '') ?></dd>
                        <dt class="col-4 text-muted">Loại sách:</dt>
                        <dd class="col-8"><?= htmlspecialchars($product['category_name'] ?? '') ?></dd>
                        <dt class="col-4 text-muted">Năm XB:</dt>
                        <dd class="col-8"><?= htmlspecialchars($product['namXB'] ?? '') ?></dd>
                    <?php endif; ?>

                    <dt class="col-4 text-muted">Đã bán:</dt>
                    <dd class="col-8"><span class="badge bg-success px-3 py-2"><?= htmlspecialchars($product['soluongban'] ?? 0) ?></span></dd>
                </dl>
            </div>


        </div>

    </div>

    <div class="p-4 bg-white rounded-2 shadow-sm mt-4">
        <h5 class="fw-semibold mb-3">Mô tả sản phẩm</h5>
        <p class="text-secondary lh-lg"><?= nl2br(htmlspecialchars($product['mo_ta'] ?? '')) ?></p>
    </div>

    <!-- Reviews -->
    <div class="mt-5">
        <div class="p-4 bg-white border rounded-2 shadow-sm mb-4">
            <h4 class="mb-3">Đánh giá sản phẩm</h4>
            <div class="d-flex align-items-center mb-4">
                <span class="fs-2 fw-bold me-3"><?= $averageRating ?>/5</span>
                <div>
                    <div class="text-warning rating-stars">
                        <?php
                        $filled = (int)floor($averageRating);
                        $hasHalf = ($averageRating - $filled) >= 0.5;
                        for ($i = 1; $i <= 5; $i++):
                            if ($i <= $filled) {
                                echo '<i class="bi bi-star-fill"></i>';
                            } elseif ($hasHalf && $i === $filled + 1) {
                                echo '<i class="bi bi-star-half"></i>';
                                $hasHalf = false;
                            } else {
                                echo '<i class="bi bi-star"></i>';
                            }
                        endfor;
                        ?>
                    </div>
                    <small class="text-muted"><?= $totalReviews ?> đánh giá</small>
                </div>
            </div>
            <?php foreach ($starsCount as $star => $count): $percent = $totalReviews ? $count / $totalReviews * 100 : 0; ?>
                <div class="d-flex align-items-center mb-1">
                    <span class="me-2"><?= $star ?> ⭐</span>
                    <div class="progress flex-grow-1">
                        <div class="progress-bar bg-warning" style="width: <?= $percent ?>%"></div>
                    </div>
                    <span class="ms-2"><?= $count ?></span>
                </div>
            <?php endforeach; ?>

            <?php
            $canReview = false;
            if ($currentCustomerId) {
                $hasPurchased = OrdersModel::customerHasPurchasedProduct($currentCustomerId, $product['sanpham_id'] ?? 0);
                $hasReviewed = ReviewsModel::customerHasReviewed($currentCustomerId, $product['sanpham_id'] ?? 0);
                $canReview = $hasPurchased && !$hasReviewed;
            }
            ?>

            <?php if ($canReview): ?>
                <div class="p-4 bg-white border rounded-2 shadow-sm mt-4">
                    <h5 class="mb-3">Viết đánh giá của bạn</h5>
                    <form method="post" action="index.php?page=submit_review">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['sanpham_id'] ?? 0) ?>">

                        <div class="mb-3">
                            <label class="form-label">Đánh giá (1-5)</label>
                            <select name="rating" class="form-select" required>
                                <option value="">Chọn</option>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <option value="<?= $i ?>"><?= $i ?> ⭐</option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bình luận</label>
                            <textarea name="comment" class="form-control" rows="4" placeholder="Viết cảm nhận của bạn..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </form>
                </div>
            <?php elseif ($currentCustomerId): ?>
                <div class="p-3 mt-3">
                    <?php if (!OrdersModel::customerHasPurchasedProduct($currentCustomerId, $product['sanpham_id'] ?? 0)): ?>
                        <div class="alert alert-info">Bạn chỉ có thể đánh giá sản phẩm sau khi mua.</div>
                    <?php else: ?>
                        <div class="alert alert-secondary">Bạn đã đánh giá sản phẩm này. Cảm ơn phản hồi của bạn.</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php foreach ($reviews as $review): $kh = CustomerModel::getCustomerById($review['khachhang_id']);
                $hoten = ($kh['ho'] ?? '') . ' ' . ($kh['tendem'] ?? '') . ' ' . ($kh['ten'] ?? ''); ?>
                <div class="card mb-3 mt-4 mx-3 shadow-sm border-0 rounded-2 bg-light">
                    <div class="card-body d-flex">
                        <img src="assets/images/avatar-default.png" class="rounded-circle border me-3" style="width:50px; height:50px; object-fit:cover;">
                        <div class="flex-grow-1 mb-0">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0 fw-semibold"><?= htmlspecialchars($hoten) ?></h6><span class="text-warning fw-bold"><?= htmlspecialchars($review['rating'] ?? 0) ?>/5 ⭐</span>
                            </div>
                            <p class=" mb-0 lh-lg"><?= nl2br(htmlspecialchars($review['binhluan'] ?? '')) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <?php if (!empty($otherBooks)): ?>
            <div class="mt-5 pb-5">
                <h4 class="mb-3">Sản phẩm khác bạn có thể thích</h4>
                <div class="row g-4">
                    <?php foreach ($otherBooks as $item):
                        $id = $item['sanpham_id'] ?? 0;
                        $imgDefault = 'assets/images/products/defaultProduct.png';
                        $img = !empty($item['hinhanh']) ? 'assets/images/products/' . $item['hinhanh'] : $imgDefault;
                        $title = $item['name'] ?? $item['tenSach'] ?? 'Sản phẩm';
                        $sold = $item['soluongban'] ?? 0;
                    ?>
                        <div class="col-6 col-md-3">
                            <div class="card h-100 shadow-sm border-0 product-card">
                                <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="text-decoration-none text-dark">
                                    <div class="card-img-wrapper position-relative">
                                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= htmlspecialchars($imgDefault) ?>';" />
                                        <?php
                                        $promo = get_product_promotion_price($id, $item['gia'] ?? 0);
                                        if ($promo && isset($promo['discounted_price']) && $promo['discounted_price']):
                                        ?>
                                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">-<?= (int)($promo['promotion']['tilegiamgia'] ?? 0) ?>%</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body d-flex flex-column mt-3 pt-2">
                                        <h6 class="card-title mb-1 text-truncate" title="<?= htmlspecialchars($title) ?>"><a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="text-dark text-decoration-none"><?= htmlspecialchars(mb_strimwidth($title, 0, 60, '...')) ?></a></h6>
                                        <div class="mb-2">
                                            <?php
                                            if ($promo && isset($promo['discounted_price']) && $promo['discounted_price']):
                                            ?>
                                                <span class="text-danger fw-bold fs-6"><?= number_format($promo['discounted_price'], 0, ',', '.') ?>₫</span>
                                                <small class="text-muted text-decoration-line-through ms-1"><?= number_format($item['gia'], 0, ',', '.') ?>₫</small>
                                            <?php else: ?>
                                                <span class="text-danger fw-bold fs-6"><?= isset($item['gia']) ? number_format($item['gia'], 0, ',', '.') . '₫' : '' ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mt-auto d-grid gap-2"><a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> Xem chi tiết</a></div>
                                    </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>