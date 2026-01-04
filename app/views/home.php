<!-- Banner Carousel -->
<div class="bg-light py-5 mb-4">
  <div class="container">
    <div id="bannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">

      <!-- Indicators -->
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="3"></button>
      </div>

      <!-- Sliders -->
      <div class="carousel-inner rounded-3 overflow-hidden banner-height shadow">

        <!-- 1 -->
        <div class="carousel-item active">
          <img src="assets/banners/1600w-iUbywlem9dU.jpg" class="d-block w-100 banner-img" alt="Banner 1">
          <div class="carousel-caption h-100 d-flex flex-column justify-content-center align-items-start banner-caption" style="background: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.6) 40%, rgba(0,0,0,0.2) 70%, transparent 100%); left: 0; right: 0; top: 0; bottom: 0; max-width: 50%; padding-left: 8%;">
            <h2 class="fw-bold mb-3 text-start banner-title">Khuyến mãi sách học tập</h2>
            <p class="mb-4 text-start banner-text">Giảm đến 50% cho học sinh - sinh viên</p>
            <a href="index.php?page=products" class="btn btn-warning fw-semibold px-4 banner-btn">Mua ngay</a>
          </div>
        </div>

        <!-- 2 -->
        <div class="carousel-item">
          <img src="assets/banners/VPBANK-T10-Web1920x450.webp" class="d-block w-100 banner-img" alt="Banner 2">
          <div class="carousel-caption h-100 d-flex flex-column justify-content-center align-items-start banner-caption" style="background: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.6) 40%, rgba(0,0,0,0.2) 70%, transparent 100%); left: 0; right: 0; top: 0; bottom: 0; max-width: 50%; padding-left: 8%;">
            <h2 class="fw-bold mb-3 text-start banner-title">Văn phòng phẩm siêu tiết kiệm</h2>
            <p class="mb-4 text-start banner-text">Mua càng nhiều - Giá càng rẻ!</p>
            <a href="index.php?page=products" class="btn btn-warning fw-semibold px-4 banner-btn">Khám phá</a>
          </div>
        </div>

        <!-- 3 -->
        <div class="carousel-item">
          <img src="assets/banners/ROHTO_Main-Banner-Web.webp" class="d-block w-100 banner-img" alt="Banner 3">
          <div class="carousel-caption h-100 d-flex flex-column justify-content-center align-items-start banner-caption" style="background: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.6) 40%, rgba(0,0,0,0.2) 70%, transparent 100%); left: 0; right: 0; top: 0; bottom: 0; max-width: 50%; padding-left: 8%;">
            <h2 class="fw-bold mb-3 text-start banner-title">Flash Sale cuối tuần</h2>
            <p class="mb-4 text-start banner-text">Giảm sốc toàn bộ sách nổi bật</p>
            <a href="index.php?page=products" class="btn btn-warning fw-semibold px-4 banner-btn">Xem ngay</a>
          </div>
        </div>

        <!-- 4 -->
        <div class="carousel-item">
          <img src="assets/banners/banner-fb-post-1800_1200-px_b670871b6d974df8bca2fbfa4dc558f6_1024x1024.png" class="d-block w-100 banner-img" alt="Banner 4">
          <div class="carousel-caption h-100 d-flex flex-column justify-content-center align-items-start banner-caption" style="background: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.6) 40%, rgba(0,0,0,0.2) 70%, transparent 100%); left: 0; right: 0; top: 0; bottom: 0; max-width: 50%; padding-left: 8%;">
            <h2 class="fw-bold mb-3 text-start banner-title">Sách Kim Đồng</h2>
            <p class="mb-4 text-start banner-text">Ưu đãi lớn giảm 15% đến 70%</p>
            <a href="index.php?page=products" class="btn btn-warning fw-semibold px-4 banner-btn">Mua sắm</a>
          </div>
        </div>

      </div>

      <!-- Controls -->
      <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev" style="width: 6%;">
        <span class="carousel-control-prev-icon" style="filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.8)); width: 2.5rem; height: 2.5rem;"></span>
        <span class="visually-hidden">Previous</span>
      </button>

      <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next" style="width: 6%;">
        <span class="carousel-control-next-icon" style="filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.8)); width: 2.5rem; height: 2.5rem;"></span>
        <span class="visually-hidden">Next</span>
      </button>

    </div>
  </div>
</div>


<!-- Nút điều hướng nhanh -->
<div class="container text-center my-4">
  <div class="btn-group flex-wrap">
    <a href="index.php?page=products&danhmucSP_id=1" class="btn btn-outline-primary">Sách</a>
    <a href="#promotions" class="btn btn-outline-primary">Khuyến mãi</a>
    <a href="index.php?page=products&danhmucSP_id=2" class="btn btn-outline-primary">Văn phòng phẩm</a>
    <a href="#best-sellers" class="btn btn-outline-primary" >Bán Chạy</a>
  </div>
</div>

<!-- Danh mục sản phẩm -->
<div class="container mb-5" id="promotions">
  <h4 class="mb-3 fw-bold text-dark">Sản phẩm khuyến mãi</h4>
  <div class="row g-4">

    <?php
    $itemsToShow =  ProductModel::getProductsFromAllPromotion(8);
    foreach ($itemsToShow as $item):

      $id = $item['sanpham_id'] ?? $item['id'] ?? 0;
      $imgDefault = 'assets/images/products/defaultProduct.png';
      $img = 'assets/images/products/' . ($item['hinhanh'] ?? '');
      if (empty($item['hinhanh'])) $img = $imgDefault;
      $title = $item['name'] ?? $item['tenSach'] ?? 'Sản phẩm';
      $sold = $item['soluongban'] ?? $item['soluong_ban'] ?? 0;
      $price = $item['gia'] ?? 0;
      
      // Lấy khuyến mãi
      $promo_data = get_product_promotion_price($id, $price);
      $promotion = $promo_data['promotion'];
      $discountedPrice = $promo_data['discounted_price'];
    ?>

      <div class="col-md-3">
        <div class="card h-100 shadow-sm border-0 product-card">
          <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="text-decoration-none text-dark">
            <div class="card-img-wrapper" style="position: relative;">
              <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= htmlspecialchars($imgDefault) ?>';">
              <?php if ($promotion): ?>
                <span class="badge bg-danger position-absolute top-0 end-0 m-2">-<?= (int)$promotion['tilegiamgia'] ?>%</span>
              <?php endif; ?>
            </div>
          </a>

          <div class="card-body d-flex flex-column pt-2">
            <h6 class="card-title mb-1 text-truncate" title="<?= htmlspecialchars($title) ?>">
              <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="text-dark text-decoration-none">
                <?= htmlspecialchars(mb_strimwidth($title, 0, 60, '...')) ?>
              </a>
            </h6>

            <div class="mb-2">
              <?php if ($discountedPrice): ?>
                <small class="text-muted text-decoration-line-through"><?= number_format($price, 0, ',', '.') ?>₫</small>
                <br>
                <span class="text-danger fw-bold fs-6"><?= number_format($discountedPrice, 0, ',', '.') ?>₫</span>
              <?php else: ?>
                <span class="text-danger fw-bold fs-6"><?= isset($item['gia']) ? number_format($item['gia'], 0, ',', '.') . '₫' : '' ?></span>
              <?php endif; ?>
            </div>

            <div class="d-flex align-items-center mb-2 small text-muted">
              <div>Đã bán: <strong class="text-dark ms-1"><?= htmlspecialchars($sold) ?></strong></div>
            </div>

            <div class="mt-auto d-grid gap-2">
              <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> Xem chi tiết</a>
            </div>
          </div>
        </div>
      </div>

    <?php endforeach; ?>
  </div>
</div>

<!-- Sản phẩm bán chạy -->
<div class="container mb-5 pb-5" id="best-sellers">
  <h4 class="mb-3 fw-bold text-dark">Bán chạy nhất</h4>
  <div class="row g-4">
    <?php
    $banChayItem= ProductModel::getBestSellingProducts(8);
    foreach ($banChayItem as $item):

      $id = $item['sanpham_id'] ?? $item['id'] ?? 0;
      $imgDefault = 'assets/images/products/defaultProduct.png';
      $img = 'assets/images/products/' . ($item['hinhanh'] ?? '');
      if (empty($item['hinhanh'])) $img = $imgDefault;
      $title = $item['name'] ?? $item['tenSach'] ?? 'Sản phẩm';
      $sold = $item['soluongban'] ?? $item['soluong_ban'] ?? 0;
      $price = $item['gia'] ?? 0;
      
      // Lấy khuyến mãi
      $promo_data = get_product_promotion_price($id, $price);
      $promotion = $promo_data['promotion'];
      $discountedPrice = $promo_data['discounted_price'];
    ?>
      <div class="col-md-3">
        <div class="card h-100 shadow-sm border-0 product-card">
          <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="text-decoration-none text-dark">
            <div class="card-img-wrapper" style="position: relative;">
              <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= htmlspecialchars($imgDefault) ?>';">
              <?php if ($promotion): ?>
                <span class="badge bg-danger position-absolute top-0 end-0 m-2">-<?= (int)$promotion['tilegiamgia'] ?>%</span>
              <?php endif; ?>
            </div>
          </a>

          <div class="card-body d-flex flex-column pt-2">
            <h6 class="card-title mb-1 text-truncate" title="<?= htmlspecialchars($title) ?>">
              <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="text-dark text-decoration-none">
                <?= htmlspecialchars(mb_strimwidth($title, 0, 60, '...')) ?>
              </a>
            </h6>

            <div class="mb-2">
              <?php if ($discountedPrice): ?>
                <small class="text-muted text-decoration-line-through"><?= number_format($price, 0, ',', '.') ?>₫</small>
                <br>
                <span class="text-danger fw-bold fs-6"><?= number_format($discountedPrice, 0, ',', '.') ?>₫</span>
              <?php else: ?>
                <span class="text-danger fw-bold fs-6"><?= isset($item['gia']) ? number_format($item['gia'], 0, ',', '.') . '₫' : '' ?></span>
              <?php endif; ?>
            </div>

            <div class="d-flex align-items-center mb-2 small text-muted">
              <div>Đã bán: <strong class="text-dark ms-1"><?= htmlspecialchars($sold) ?></strong></div>
            </div>

            <div class="mt-auto d-grid gap-2">
              <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> Xem chi tiết</a>
            </div>
          </div>
        </div>
      </div>

    <?php endforeach; ?>
  </div>
</div>