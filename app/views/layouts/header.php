<?php
$search = $search ?? ($_GET['search'] ?? '');
$cart_count = $cart_count ?? ($customer ? count(CartModel::getCartItems($customer['id'])) : -1);
$notifications = $customer ? NotificationModel::getCustomerNotifications($customer['id']) : [];
$notification_count = $notification_count ?? 0;
$catalogs = $catalogs ?? CategoriesModel::getAllCategories();
$csrf_token = $csrf_token ?? ($_SESSION['csrf_token'] ?? '');
?>

<!-- Header -->
<nav class="navbar navbar-expand-lg shadow-sm sticky-top">
  <div class="container px-4">
    <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href=".">
      <img src="./assets/images/bookstoreLogo.png" alt="logo" class="me-2 img-fluid" style="height: 50px; object-fit:contain;">
      <span class="ms-1">BookZone</span>
    </a>
    <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">


        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="categoryMenu" data-bs-toggle="dropdown">
            <i class="fa-solid fa-list"></i> Danh mục
          </a>
          <ul class="dropdown-menu shadow-sm">

            <?php foreach ($catalogs as $cat): ?>
              <li>
                <a class="dropdown-item" href="index.php?page=products&danhmucSP_id=<?= $cat['danhmucSP_id'] ?>">
                  <?= htmlspecialchars($cat['tenDanhMuc']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </li>


        <li class="nav-item">
          <a class="nav-link" href="index.php?page=products">Tất cả sản phẩm</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=contact">Liên hệ</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=about">Về</a>
        </li>
      </ul>

      <form class="me-3 flex-grow-1 flex-lg-grow-0" action="index.php">
        <input type="hidden" name="page" value="products">
        <div class="input-group">
          <input type="search" class="form-control" name="search" value="<?= $search ?>" placeholder="Tìm kiếm sản phẩm...">
          <button class="btn btn-outline-primary" type="submit">
            <i class="fa fa-search"></i>
          </button>
        </div>
      </form>


      <ul class="navbar-nav mb-2 mb-lg-0">

        <li class="nav-item dropdown me-3">
          <a class="nav-link position-relative" id="notifyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-regular fa-bell"></i>
            <?php if ($notification_count > 0): ?>
              <span class="badge bg-danger position-absolute top-1 start-100 translate-middle rounded-pill"><?= $notification_count ?></span>
            <?php endif; ?>
          </a>
          <div class="dropdown-menu dropdown-menu-end p-0 shadow" aria-labelledby="notifyDropdown" style="width:380px; border-radius:12px; overflow:hidden;">
            <div class="px-3 d-flex align-items-center justify-content-between bg-light border-bottom py-2">
              <div class="d-flex align-items-center">
                <strong>Thông báo mới</strong>
                <?php if ($notification_count > 0): ?><span class="badge bg-danger ms-2"><?= $notification_count ?></span><?php endif; ?>
              </div>
              <!-- <button class="btn btn-sm btn-link text-muted" onclick="bootstrap.Dropdown.getInstance(document.getElementById('notifyDropdown')).hide();">×</button> -->
            </div>
          
            <?php
          
            function _note_icon_dd($loai)
            {
              if ($loai === 'khuyen_mai') return 'bi-gift';
              else if ($loai === 'don_hang') return 'bi-truck';
              else if ($loai === 'khach_hang') return 'bi-person-circle';
              else if ($loai === 'he_thong') return 'bi-bell';
              return 'bi-bell';
            }
            ?>

            <div class="px-3 py-2" style="max-height:360px; overflow-y:auto;">
              <?php if (!empty($notifications)): ?>
                <?php for($i=0; $i < 5; $i++):
                  $n = $notifications[$i] ?? null;
                  if (!$n) break;
                  $status = $n['trang_thai'] ?? 'chua_doc';
                  $isUnread = ($status === 'chua_doc');
                  $loai = $n['loai'] ?? 'he_thong';
                  $icon = _note_icon_dd($loai);
                  $bodyText = $n['noi_dung'] ?? '';
                  $showShipping = preg_match('/đang giao hàng|dang giao hang/i', $bodyText);
                ?>
                  <div class="border rounded-3 p-2 mb-2 bg-white">
                    <div class="d-flex align-items-start gap-3">
                      <div class="rounded-3 bg-light d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                        <i class="bi <?= $icon ?> text-primary"></i>
                      </div>
                      <div class="flex-grow-1" >
                        <div class="d-flex align-items-center justify-content-between">
                          <div class="fw-semibold"><?= htmlspecialchars($n['tieu_de'] ?? 'Thông báo') ?></div>
                          <?php if ($isUnread): ?><span class="badge bg-primary-subtle text-primary border">Mới</span><?php endif; ?>
                        </div>
                        <div class="text-muted mt-1"><?= nl2br(htmlspecialchars($bodyText)) ?></div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                          <?php if ($showShipping): ?><span class="badge bg-success-subtle text-success border">Đang giao hàng</span><?php endif; ?>
                          <span class="small text-muted ms-auto"><strong><?= ($n['ngay_tao'] ?? '') ?></strong></span>
                        </div>
                        <div class="mt-2">
                          <a href="index.php?page=notifications" class="small text-primary text-decoration-none">Xem ngay →</a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endfor; ?>
              <?php else: ?>
                <div class="text-center text-muted py-4">
                  <i class="bi bi-inbox fs-1"></i>
                  <div class="mt-2">Không có thông báo mới</div>
                </div>
              <?php endif; ?>
            </div>

            <div class="p-3 border-top bg-light">
              <div class="d-grid mb-2">
                <a href="index.php?page=notifications" class="btn btn-primary">Xem tất cả thông báo</a>
              </div>
              <div class="text-center">
                <form method="post" action="index.php?page=notifications_mark_all" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                  <button type="submit" class="btn btn-link btn-sm text-muted text-decoration-none">Đánh dấu tất cả đã đọc</button>
                </form>
              </div>
            </div>
          </div>
        </li>

        <li class="nav-item me-3">
          <a class="nav-link position-relative" href="index.php?page=cart">
            <i class="fa-solid fa-cart-shopping fs-5"></i>
            <?php if ($cart_count >= 0) : ?>
              <span class="badge bg-danger position-absolute top-1 start-100 translate-middle rounded-pill">
                <?= $cart_count ?>
              </span>
            <?php endif; ?>
          </a>
        </li>



        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="accountMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-regular fa-user"></i>
            <span class="ms-2 d-none d-lg-inline"><?= !empty($customer) ? htmlspecialchars($customer['ho_ten']) : 'Tài khoản' ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow border-1" aria-labelledby="accountMenu">
            <?php if (!empty($customer)): ?>
              <li><a class="dropdown-item" href="index.php?page=account">Hồ sơ</a></li>
              <li><a class="dropdown-item" href="index.php?page=orders">Đơn hàng</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <form method="post" action="" class="mb-0">
                  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                  <button type="submit" name="logout_exc" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start" style="cursor: pointer;">
                    <i class="fa-solid fa-sign-out-alt me-2"></i>Đăng xuất
                  </button>
                </form>
              </li>
            <?php else: ?>
              <li><a class="dropdown-item" href="index.php?page=login">Đăng nhập</a></li>
              <li><a class="dropdown-item" href="index.php?page=register">Đăng ký</a></li>
            <?php endif; ?>
          </ul>
        </li>


      </ul>
    </div>
  </div>
</nav>

<!-- Offcanvas mobile menu -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="mobileMenuLabel">Menu</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="mb-3">
      <form class="d-flex" action="index.php">
        <input type="hidden" name="page" value="products">
        <div class="input-group">
          <input type="search" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm kiếm sản phẩm...">
          <button class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
        </div>
      </form>
    </div>

    <?php if (!empty($customer)): ?>
      <div class="card mb-3">
        <div class="card-body p-3">
          <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:44px;height:44px;font-size:1.1rem;">
              <i class="fa-regular fa-user"></i>
            </div>
            <div class="ms-3 flex-grow-1">
              <div class="fw-semibold"><?= htmlspecialchars($customer['ho_ten'] ?? '') ?></div>
              <small class="text-muted d-block"><?= htmlspecialchars($customer['email'] ?? '') ?></small>
            </div>
          </div>
          <div class="mt-3 d-flex gap-2">
            <a href="index.php?page=account" class="btn btn-sm btn-outline-secondary flex-fill">Hồ sơ</a>
            <a href="index.php?page=orders" class="btn btn-sm btn-outline-secondary flex-fill">Đơn hàng</a>
          </div>
          <form method="post" action="" class="mt-2">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            <button type="submit" name="logout_exc" class="btn btn-sm btn-outline-danger w-100">Đăng xuất</button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <div class="d-grid gap-2 mb-3">
        <a class="btn btn-primary" href="index.php?page=login">Đăng nhập</a>
        <a class="btn btn-outline-secondary" href="index.php?page=register">Đăng ký</a>
      </div>
    <?php endif; ?>

    <div class="mb-3">
      <div class="fw-semibold mb-2 d-flex align-items-center justify-content-between">
        <span>Thông báo</span>
        <?php if ($notification_count > 0): ?><span class="badge bg-danger"><?= $notification_count ?></span><?php endif; ?>
      </div>
      <div class="list-group mb-2">
        <?php $mobileNotes = array_slice($notifications, 0, 5);
        if (!empty($mobileNotes)): foreach ($mobileNotes as $n): ?>
            <div class="list-group-item small"><?php echo htmlspecialchars(mb_strimwidth($n['tieu_de'] ?? '', 0, 60, '...')); ?><br><small class="text-muted"><?= htmlspecialchars(mb_strimwidth($n['noi_dung'] ?? '', 0, 80, '...')) ?></small></div>
          <?php endforeach;
        else: ?>
          <div class="list-group-item small text-muted">Không có thông báo.</div>
        <?php endif; ?>
      </div>
      <a href="index.php?page=notifications" class="small text-decoration-none">Xem tất cả thông báo</a>
    </div>

    <div class="mb-3">
      <div class="fw-semibold mb-2">Danh mục</div>
      <div class="list-group">
        <?php foreach ($catalogs as $cat): ?>
          <a class="list-group-item list-group-item-action" href="index.php?page=products&danhmucSP_id=<?= $cat['danhmucSP_id'] ?>"><?= htmlspecialchars($cat['tenDanhMuc']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary flex-grow-1" href="index.php?page=cart">
        <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng
        <?php if ($cart_count >= 0): ?><span class="badge bg-danger ms-2"><?= $cart_count ?></span><?php endif; ?>
      </a>
    </div>
  </div>
</div>

<script>
  // function loadNotifications()
  // {
  //   // Gọi AJAX để tải thông báo mới
  //   fetch('index.php?page=fetch_notifications')
  //     .then(response => response.text())
  //     .then(data => {
  //       // Cập nhật nội dung thông báo trong dropdown
  //       const notifyMenu = document.getElementById('notifyMenu');
  //       const dropdownMenu = notifyMenu.nextElementSibling;
  //       dropdownMenu.innerHTML = data;
  //     })
  //     .catch(error => console.error('Error fetching notifications:', error));
  // }
</script>
<?php
// Hiển thị thông báo flash nếu có
if (!empty($service_status['success']) || !empty($service_status['error'])): ?>
  <div class="container mt-3">
    <?php if (!empty($service_status['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($service_status['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <?php if (!empty($service_status['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($service_status['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
  </div>
<?php
  // optional: clear so it doesn't reappear if header included elsewhere
  unset($service_status['success'], $service_status['error']);
endif;
