<?php
// For backward compatibility, call the page preparer if variables are not set.
// For backward compatibility, call the page preparer if variables are not set.
if (!isset($products) || !isset($catalogs)) {
    $vars = PageController::prepareProductsPage();
    extract($vars);
} else {
    // Fallback: perform minimal inline preparation
    $search = trim($_GET['search'] ?? '');
    $categoryId = (int)($_GET['danhmucSP_id'] ?? 0);
    $minPrice = (int)($_GET['min'] ?? 0);
    $maxPrice = (int)($_GET['max'] ?? 0);
    $providerId = (int)($_GET['provider_id'] ?? 0);
    $publisherId = (int)($_GET['publisher_id'] ?? 0);
    $sortBy = $_GET['sort_by'] ?? '';
    $products = ProductModel::filterProducts($search, $categoryId, $minPrice, $maxPrice, $providerId, $publisherId, $sortBy);
    $catalogs = CategoriesModel::getAllCategories();
    $providers = ProviderModel::getAllProviders();
    $publishers = PublisherModel::getAllPublishers();
    $allPrices = array_map(fn($p) => floatval($p['gia'] ?? 0), ProductModel::getAllProducts());
    $globalMin = $allPrices ? (int)min($allPrices) : 0;
    $globalMax = $allPrices ? (int)max($allPrices) : 200000;
    if ($globalMin < 0) $globalMin = 0;
    if ($globalMax <= 0) $globalMax = 200000;
    if ($minPrice <= 0) $minPrice = $globalMin;
    if ($maxPrice <= 0) $maxPrice = $globalMax;
}
?>

<div class="container mt-5 pb-5">
    <div class="row">
        <!-- Filter sidebar (hidden on small screens; offcanvas used there) -->
        <aside class="col-md-3 mb-4 d-none d-md-block">
            <div class="card p-3 shadow-sm">
                <h5 class="mb-3">Bộ lọc</h5>
                <form method="GET" action="index.php">
                    <input type="hidden" name="page" value="products">

                    <div class="mb-3">
                        <label class="form-label">Danh mục</label>
                        <select name="danhmucSP_id" class="form-select">
                            <option value="0">Tất cả</option>
                            <?php foreach ($catalogs as $cate): ?>
                                <option value="<?= (int)$cate['danhmucSP_id'] ?>"
                                    <?= $categoryId == (int)$cate['danhmucSP_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cate['tenDanhMuc'] ?? $cate['ten_danhmuc'] ?? '') ?>
                                </option>

                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nhà cung cấp</label>
                        <select name="provider_id" class="form-select">
                            <option value="0">Tất cả</option>
                            <?php foreach ($providers as $prov): ?>
                                <option value="<?= (int)$prov['nhacungcap_id'] ?>" <?= $providerId === (int)$prov['nhacungcap_id'] ? 'selected' : '' ?>><?= htmlspecialchars($prov['ten']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nhà xuất bản</label>
                        <select name="publisher_id" class="form-select">
                            <option value="0">Tất cả</option>
                            <?php foreach ($publishers as $pub): ?>
                                <option value="<?= (int)$pub['nhaxuatban_id'] ?>" <?= $publisherId === (int)$pub['nhaxuatban_id'] ? 'selected' : '' ?>><?= htmlspecialchars($pub['ten']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giá (₫)</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="number" name="min" class="form-control" value="<?= (int)$minPrice ?>" placeholder="Từ">
                            <span class="mx-1">—</span>
                            <input type="number" name="max" class="form-control" value="<?= (int)$maxPrice ?>" placeholder="Đến">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sắp xếp theo</label>
                        <select name="sort_by" class="form-select">
                            <option value="">Mặc định</option>
                            <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Giá: thấp → cao</option>
                            <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Giá: cao → thấp</option>
                            <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="best_selling" <?= $sortBy === 'best_selling' ? 'selected' : '' ?>>Bán chạy</option>
                            <option value="name_asc" <?= $sortBy === 'name_asc' ? 'selected' : '' ?>>Tên A → Z</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary">Lọc</button>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Mobile: filter toggle -->
        <div class="d-flex d-md-none align-items-center mb-3">
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas">
                <i class="bi bi-funnel-fill me-1"></i>Bộ lọc
            </button>
        </div>

        <!-- Products grid -->
        <section class="col-md-9">
            <?php if (empty($products)): ?>
                <div class="alert alert-warning">Không tìm thấy sản phẩm nào.</div>
            <?php else: ?>
                <div class="row g-2 pb-5">
                    <?php foreach ($products as $item):
                        $id = $item['sanpham_id'] ?? $item['id'] ?? 0;
                        $isBook = is_book_category((int)($item['danhmucSP_id'] ?? 0));
                        $imgDefault = 'assets/images/products/defaultProduct.png';
                        $img = 'assets/images/products/' . ($item['hinhanh'] ?? '');
                        if (empty($item['hinhanh'])) {
                            $img = $imgDefault;
                        }
                        $title = $item['name'] ?? $item['tenSach'] ?? 'Sản phẩm';
                        $sold = $item['soluongban'] ?? $item['soluong_ban'] ?? 0;
                        $reviews = ReviewsModel::getAllReviewsByProductId($id);
                        $ratingCount = count($reviews);
                        $avgRating = $ratingCount ? round(array_sum(array_column($reviews, 'rating')) / $ratingCount, 1) : null;
                    ?>
                        <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100 shadow-sm border-0 product-card">
                                <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="text-decoration-none text-dark">
                                    <div class="card-img-wrapper" style="position: relative;">
                                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= htmlspecialchars($imgDefault) ?>';">
                                        <?php 
                                            $promotion = get_product_promotion_price($id, $item['gia'] ?? 0);
                                            if ($promotion && isset($promotion['discounted_price']) && $promotion['discounted_price']): 
                                        ?>
                                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">-<?= (int)($promotion['promotion']['tilegiamgia'] ?? 0) ?>%</span>
                                        <?php endif; ?>
                                    </div>
                                </a>

                                <div class="card-body d-flex flex-column pt-2">
                                    <h6 class="card-title mb-1 text-truncate" title="<?= htmlspecialchars($title) ?>">
                                        <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="text-dark text-decoration-none">
                                            <?= htmlspecialchars(mb_strimwidth($title, 0, 60, '...')) ?>
                                        </a>
                                    </h6>

                                    <div class="d-flex align-items-center mb-2" style="gap:.5rem;font-size:0.9rem;">
                                        <div class="d-flex align-items-center">
                                            <?php if ($avgRating): ?>
                                                <span class="text-warning me-1">
                                                    <?php for ($i = 0; $i < floor($avgRating); $i++): ?>
                                                        <i class="bi bi-star-fill"></i>
                                                    <?php endfor; ?>
                                                </span>
                                                <small class="text-muted"><?= $avgRating ?> (<?= $ratingCount ?>)</small>
                                            <?php else: ?>
                                                <small class="text-muted">Chưa có đánh giá</small>
                                            <?php endif; ?>
                                        </div>

                                        <div class="ms-auto small text-muted">Đã bán: <strong class="text-dark ms-1"><?= htmlspecialchars($sold) ?></strong></div>

                                    </div>

                                    <?php if (isset($item['gia'])): ?>
                                        <div class="mb-2">
                                            <?php 
                                                $promotion = get_product_promotion_price($id, $item['gia'] ?? 0);
                                                if ($promotion && isset($promotion['discounted_price']) && $promotion['discounted_price']): 
                                            ?>
                                                <span class="text-danger fw-bold fs-6"><?= number_format($promotion['discounted_price'], 0, ',', '.') ?>₫</span>
                                                <small class="text-muted text-decoration-line-through ms-1"><?= number_format($item['gia'], 0, ',', '.') ?>₫</small>
                                            <?php else: ?>
                                                <span class="text-danger fw-bold fs-6"><?= number_format($item['gia'], 0, ',', '.') ?>₫</span>
                                                <?php if (isset($item['gia_cu']) && $item['gia_cu'] > $item['gia']): ?>
                                                    <small class="text-muted text-decoration-line-through ms-1"><?= number_format($item['gia_cu'], 0, ',', '.') ?>₫</small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-auto d-grid gap-2">
                                        <div class="d-flex gap-1">
                                            <form method="post" action="index.php?page=cart_add" class="flex-grow-1">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($id) ?>">
                                                <button type="submit" class="d-flex justify-content-center btn btn-sm btn-primary w-100">
                                                    <i class="bi bi-cart-plus"></i>
                                                    <div class="ms-2"> Thêm vào giỏ</div>
                                                </button>
                                            </form>

                                            <?php
                                            $isFavorite = false;
                                            if (!empty($customer)) {
                                                $isFavorite = WishlistModel::isProductFavorite($customer['id'], $id);
                                            }
                                            ?>
                                            <?php if (!empty($customer)): ?>
                                                <form method="post" action="index.php?page=wishlist_toggle">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($id) ?>">
                                                    <button type="submit" class="btn btn-sm <?= $isFavorite ? 'btn-danger' : 'btn-outline-danger' ?>" title="<?= $isFavorite ? 'Bỏ yêu thích' : 'Yêu thích' ?>">
                                                        <i class="bi bi-heart<?= $isFavorite ? '-fill' : '' ?>"></i>
                                                    </button>
                                                </form>


                                            <?php else: ?>
                                                <a href="index.php?page=login" class="btn btn-sm btn-outline-secondary" title="Yêu thích"><i class="bi bi-heart"></i></a>
                                            <?php endif; ?>
                                        </div>

                                        <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="d-flex justify-content-center btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-eye"></i>
                                            <div class="ms-2">Xem chi tiết</div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<!-- Offcanvas for mobile filters -->
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="filterOffcanvasLabel">Bộ lọc</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card p-0 border-0">
            <div class="card-body">
                <form method="GET" action="index.php">
                    <input type="hidden" name="page" value="products">
                    <!-- Copy of filter form fields -->
                    <div class="mb-3">
                        <label class="form-label">Danh mục</label>
                        <select name="danhmucSP_id" class="form-select">
                            <option value="0">Tất cả</option>
                            <?php foreach ($catalogs as $cate): ?>
                                <option value="<?= (int)$cate['danhmucSP_id'] ?>" <?= $categoryId == (int)$cate['danhmucSP_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cate['tenDanhMuc'] ?? $cate['ten_danhmuc'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nhà cung cấp</label>
                        <select name="provider_id" class="form-select">
                            <option value="0">Tất cả</option>
                            <?php foreach ($providers as $prov): ?>
                                <option value="<?= (int)$prov['nhacungcap_id'] ?>" <?= $providerId === (int)$prov['nhacungcap_id'] ? 'selected' : '' ?>><?= htmlspecialchars($prov['ten']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nhà xuất bản</label>
                        <select name="publisher_id" class="form-select">
                            <option value="0">Tất cả</option>
                            <?php foreach ($publishers as $pub): ?>
                                <option value="<?= (int)$pub['nhaxuatban_id'] ?>" <?= $publisherId === (int)$pub['nhaxuatban_id'] ? 'selected' : '' ?>><?= htmlspecialchars($pub['ten']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giá (₫)</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="number" name="min" class="form-control" value="<?= (int)$minPrice ?>" placeholder="Từ">
                            <span class="mx-1">—</span>
                            <input type="number" name="max" class="form-control" value="<?= (int)$maxPrice ?>" placeholder="Đến">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sắp xếp theo</label>
                        <select name="sort_by" class="form-select">
                            <option value="">Mặc định</option>
                            <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Giá: thấp → cao</option>
                            <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Giá: cao → thấp</option>
                            <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="best_selling" <?= $sortBy === 'best_selling' ? 'selected' : '' ?>>Bán chạy</option>
                            <option value="name_asc" <?= $sortBy === 'name_asc' ? 'selected' : '' ?>>Tên A → Z</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary">Lọc</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


</div>