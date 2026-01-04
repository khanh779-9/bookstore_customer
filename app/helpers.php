<?php

function compareTimes($time1, $time2) { 
    $t1 = new DateTime($time1);
    $t2 = new DateTime($time2);
    if ($t1 < $t2) {
        $result = -1; // "Thời gian 1 xảy ra trước thời gian 2"
    } elseif ($t1 > $t2) {
        $result = 1; // "Thời gian 1 xảy ra sau thời gian 2"
    } else {
        $result = 0; // "Hai thời gian bằng nhau"
    }
    $diff = $t1->diff($t2);
    //  [
    //     "ket_qua" => $result,
    //     "khoang_cach" => [
    //         "ngay" => $diff->days,
    //         "gio" => $diff->h,
    //         "phut" => $diff->i,
    //         "giay" => $diff->s
    //     ]
    // ];
    return $result;
}


function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_phone($phone) {
    return preg_match('/^0\d{9}$/', $phone);
}

function validate_required_fields($fields) {
    $missing = [];
    foreach ($fields as $field) {
        if (empty(trim($_POST[$field] ?? ''))) $missing[] = $field;
    }
    return $missing;
}

function sanitize_string($str) { return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8'); }

function format_price($price) { return number_format($price, 0, ',', '.') . 'đ'; }

function truncate_text($text, $length = 100, $suffix = '...') { return mb_strlen($text) <= $length ? $text : mb_substr($text,0,$length) . $suffix; }

// Simple DB helpers
function db_fetch_one($query, $params = []) { $pdo = \Database::getInstance(); $stmt = $pdo->prepare($query); $stmt->execute($params); return $stmt->fetch(PDO::FETCH_ASSOC) ?: []; }
function db_fetch_all($query, $params = []) { $pdo = \Database::getInstance(); $stmt = $pdo->prepare($query); $stmt->execute($params); return $stmt->fetchAll(PDO::FETCH_ASSOC); }
function db_execute($query, $params = []) { $pdo = \Database::getInstance(); $stmt = $pdo->prepare($query); $result = $stmt->execute($params); return $result ? $stmt->rowCount() : false; }

// Simple cache (session)
function cache_get($key, $default = null) { return $_SESSION['cache_'.$key] ?? $default; }
function cache_set($key, $value, $ttl = 3600) { $_SESSION['cache_'.$key] = $value; $_SESSION['cache_'.$key.'_expire'] = time()+$ttl; }
function cache_has($key) { if (!isset($_SESSION['cache_'.$key])) return false; $exp = $_SESSION['cache_'.$key.'_expire'] ?? 0; if ($exp < time()) { unset($_SESSION['cache_'.$key]); unset($_SESSION['cache_'.$key.'_expire']); return false; } return true; }
function cache_forget($key) { unset($_SESSION['cache_'.$key]); unset($_SESSION['cache_'.$key.'_expire']); }
function cache_remember($key,$ttl,$cb){ if(cache_has($key)) return cache_get($key); $v = $cb(); cache_set($key,$v,$ttl); return $v; }

// Status helpers
function get_order_status_badge($s){ $m=['cho_xac_nhan'=>'warning','da_xac_nhan'=>'info','dang_giao_hang'=>'primary','da_giao_hang'=>'success','da_huy'=>'danger']; return $m[$s] ?? 'secondary'; }
function translate_order_status($s){ $t=['cho_xac_nhan'=>'Chờ xác nhận','da_xac_nhan'=>'Đã xác nhận','dang_giao_hang'=>'Đang giao hàng','da_giao_hang'=>'Đã giao hàng','da_huy'=>'Đã hủy']; return $t[$s] ?? $s; }

// get paid method label
function translate_payment_method($pm){ $t=['tien_mat'=>'Tiền mặt','chuyen_khoan'=>'Chuyển khoản','momo'=>'MoMo','zalopay'=>'ZaloPay']; return $t[$pm] ?? $pm; }

// Get product image path with fallback
function get_product_image($imageName, $default = 'assets/images/products/defaultProduct.png') {
    if (empty($imageName)) return $default;
    $path = 'assets/images/products/' . $imageName;
    return file_exists($path) ? $path : $default;
}

// Category helpers
function is_book_category($danhmucSP_id): bool {
    return ((int)$danhmucSP_id === 1);
}

function is_stationery_category($danhmucSP_id): bool {
    return ((int)$danhmucSP_id === 2);
}

function is_other_category($danhmucSP_id): bool {
    $id = (int)$danhmucSP_id;
    return !is_book_category($id) && !is_stationery_category($id);
}

function get_category_name($danhmucSP_id): string {
    if (class_exists('CategoriesModel')) {
        $row = CategoriesModel::getCategory((int)$danhmucSP_id);
        if (!empty($row) && is_array($row)) {
            $first = $row[0] ?? null;
            if (is_array($first)) return $first['tenDanhMuc'] ?? ($first['ten'] ?? '');
            if (is_string($first)) return $first;
        }
    }
    return '';
}

function product_category_badge_class($danhmucSP_id): string {
    if (is_book_category($danhmucSP_id)) return 'primary';
    if (is_stationery_category($danhmucSP_id)) return 'info';
    return 'secondary';
}

// Employee status badge
function get_employee_status_badge($status) { $badges = ['dang_lam'=>'success','nghi_viec'=>'danger','tam_nghi'=>'warning']; return $badges[$status] ?? 'secondary'; }

// CSRF helpers
function generate_csrf_token($force = false) {
    if ($force || empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            // fallback
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token = null) {
    $token = $token ?: ($_POST['csrf_token'] ?? $_POST['_csrf'] ?? null);
    if (empty($token)) {
        if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        elseif (!empty(getallheaders()['X-CSRF-Token'] ?? null)) $token = getallheaders()['X-CSRF-Token'];
    }

    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

function require_csrf_or_redirect($token = null, $redirect = 'index.php') {
    $token = $token ?: ($_POST['csrf_token'] ?? $_POST['_csrf'] ?? null);
    if (!verify_csrf_token($token)) {
        $_SESSION['error'] = 'Yêu cầu không hợp lệ (CSRF). Vui lòng thử lại.';
        redirect($redirect);
        exit;
    }
}

// Lightweight application logger
function app_log($message, $level = 'INFO') {
    $msg = sprintf("[%s] %s: %s", date('Y-m-d H:i:s'), $level, $message);
    error_log($msg);
}


function getLoginAgent() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (strpos($userAgent, 'Chrome') !== false) {
        $browser = "Chrome";
    } elseif (strpos($userAgent, 'Firefox') !== false) {
        $browser = "Firefox";
    } elseif (strpos($userAgent, 'Safari') !== false) {
        $browser = "Safari";
    } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
        $browser = "Internet Explorer";
    } else {
        $browser = "Unknown";
    }

    if (preg_match('/Windows NT 10.0/i', $userAgent)) {
        $os = "Windows 10";
    } elseif (preg_match('/Windows NT 6.1/i', $userAgent)) {
        $os = "Windows 7";
    } elseif (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
        $os = "macOS";
    } elseif (preg_match('/Linux/i', $userAgent)) {
        $os = "Linux";
    } elseif (preg_match('/Android/i', $userAgent)) {
        $os = "Android";
    } elseif (preg_match('/iPhone|iPad/i', $userAgent)) {
        $os = "iOS";
    } else {
        $os = "Unknown";
    }

    if (preg_match('/Mobile/i', $userAgent)) {
        $device = "Mobile";
    } elseif (preg_match('/Tablet/i', $userAgent)) {
        $device = "Tablet";
    } else {
        $device = "Desktop";
    }

    return [
        "Browser" => $browser,
        "OperatingSystem" => $os,
        "Device" => $device
    ];
}


// Hàm helper để tính giá khuyến mãi cho sản phẩm
function get_product_promotion_price($productId, $basePrice) {
    $promotion = null;
    $discountedPrice = null;
    if (class_exists('PromotionModel')) {
        try {
            $promotion = PromotionModel::getActivePromotionForProduct($productId);
            if ($promotion && isset($promotion['tilegiamgia'])) {
                $discountRate = (float)$promotion['tilegiamgia'];
                $discountedPrice = $basePrice * (1 - $discountRate / 100);
            }
        } catch (Exception $e) {
            $promotion = null;
        }
    }
    return ['promotion' => $promotion, 'discounted_price' => $discountedPrice];
}

function render_product_card($product) {
    $id = $product['sanpham_id'] ?? $product['id'] ?? 0;
    $name = $product['name'] ?? $product['tenSach'] ?? $product['tenVPP'] ?? 'Sản phẩm';
    $price = $product['gia'] ?? 0;
    $image = get_product_image($product['hinhanh'] ?? '');
    $sold = $product['soluongban'] ?? 0;
    $reviews = ReviewsModel::getAllReviewsByProductId($id) ?? [];
    $ratingCount = count($reviews);
    $avgRating = $ratingCount ? round(array_sum(array_column($reviews, 'rating')) / $ratingCount, 1) : null;
    
    // Lấy khuyến mãi áp dụng cho sản phẩm
    $promotion = null;
    $discountedPrice = null;
    if (class_exists('PromotionModel')) {
        try {
            $promotion = PromotionModel::getActivePromotionForProduct($id);
            if ($promotion && isset($promotion['tilegiamgia'])) {
                $discountRate = (float)$promotion['tilegiamgia'];
                $discountedPrice = $price * (1 - $discountRate / 100);
            }
        } catch (Exception $e) {
            $promotion = null;
        }
    }
    
    ob_start();
    ?>
    <div class="col-6 col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100 shadow-sm border-0 product-card">
            <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="text-decoration-none text-dark">
                <div class="card-img-wrapper">
                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy" class="card-img-top">
                    <?php if ($promotion): ?>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">-<?= (int)$promotion['tilegiamgia'] ?>%</span>
                    <?php endif; ?>
                </div>
            </a>
            <div class="card-body d-flex flex-column pt-2">
                <h6 class="card-title mb-1 text-truncate" title="<?= htmlspecialchars($name) ?>">
                    <a href="index.php?page=productview&id=<?= htmlspecialchars($id) ?>" class="text-dark text-decoration-none">
                        <?= htmlspecialchars(truncate_text($name, 60)) ?>
                    </a>
                </h6>
                <div class="d-flex align-items-center mb-2" style="gap:.5rem;font-size:0.9rem;">
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
                <div class="mt-auto">
                    <?php if ($discountedPrice): ?>
                        <div class="mb-2">
                            <small class="text-muted text-decoration-line-through"><?= format_price($price) ?></small>
                            <p class="card-text fw-bold text-danger mb-0"><?= format_price($discountedPrice) ?></p>
                        </div>
                    <?php else: ?>
                        <p class="card-text fw-bold text-danger mb-2"><?= format_price($price) ?></p>
                    <?php endif; ?>
                    <small class="text-muted">Đã bán: <?= number_format($sold) ?></small>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function render_product_table_row($product, $index) {
    $id = $product['sanpham_id'] ?? 0;
    $name = $product['name'] ?? $product['tenSach'] ?? $product['tenVPP'] ?? 'N/A';
    $price = $product['gia'] ?? 0;
    $stock = $product['soluongton'] ?? 0;
    $category = $product['category_name'] ?? 'N/A';
    $stockClass = $stock <= 10 ? 'text-danger fw-bold' : ($stock <= 30 ? 'text-warning' : 'text-success');
    ob_start();
    ?>
    <tr>
        <td><?= $index ?></td>
        <td><?= htmlspecialchars($name) ?></td>
        <td><?= htmlspecialchars($category) ?></td>
        <td><?= format_price($price) ?></td>
        <td class="<?= $stockClass ?>"><?= number_format($stock) ?></td>
        <td>
            <a href="?page=employee_products&action=edit&id=<?= $id ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
            <?php $csrf = htmlspecialchars($csrf_token ?? $_SESSION['csrf_token'] ?? ''); ?>
            <form method="POST" action="index.php?page=employee_products&action=delete" style="display:inline-block; margin:0;">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này?')"><i class="bi bi-trash"></i></button>
            </form>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}

function render_form_input($name, $label, $value = '', $type = 'text', $required = false, $attributes = []) {
    $requiredAttr = $required ? 'required' : '';
    $requiredMark = $required ? '<span class="text-danger">*</span>' : '';
    $attrStr = '';
    foreach ($attributes as $key => $val) $attrStr .= sprintf(' %s="%s"', htmlspecialchars($key), htmlspecialchars($val));
    ob_start();
    ?>
    <div class="mb-3">
        <label for="<?= htmlspecialchars($name) ?>" class="form-label"><?= htmlspecialchars($label) ?> <?= $requiredMark ?></label>
        <input type="<?= htmlspecialchars($type) ?>" class="form-control" id="<?= htmlspecialchars($name) ?>" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars($value) ?>" <?= $requiredAttr ?> <?= $attrStr ?> />
    </div>
    <?php
    return ob_get_clean();
}

function render_form_select($name, $label, $options, $selected = '', $required = false, $attributes = []) {
    $requiredAttr = $required ? 'required' : '';
    $requiredMark = $required ? '<span class="text-danger">*</span>' : '';
    $attrStr = '';
    foreach ($attributes as $key => $val) $attrStr .= sprintf(' %s="%s"', htmlspecialchars($key), htmlspecialchars($val));
    ob_start();
    ?>
    <div class="mb-3">
        <label for="<?= htmlspecialchars($name) ?>" class="form-label"><?= htmlspecialchars($label) ?> <?= $requiredMark ?></label>
        <select class="form-select" id="<?= htmlspecialchars($name) ?>" name="<?= htmlspecialchars($name) ?>" <?= $requiredAttr ?> <?= $attrStr ?>>
            <?php foreach ($options as $value => $text): ?>
                <option value="<?= htmlspecialchars($value) ?>" <?= $selected == $value ? 'selected' : '' ?>><?= htmlspecialchars($text) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
    return ob_get_clean();
}

function render_form_textarea($name, $label, $value = '', $required = false, $rows = 3, $attributes = []) {
    $requiredAttr = $required ? 'required' : '';
    $requiredMark = $required ? '<span class="text-danger">*</span>' : '';
    $attrStr = '';
    foreach ($attributes as $key => $val) $attrStr .= sprintf(' %s="%s"', htmlspecialchars($key), htmlspecialchars($val));
    ob_start();
    ?>
    <div class="mb-3">
        <label for="<?= htmlspecialchars($name) ?>" class="form-label"><?= htmlspecialchars($label) ?> <?= $requiredMark ?></label>
        <textarea class="form-control" id="<?= htmlspecialchars($name) ?>" name="<?= htmlspecialchars($name) ?>" rows="<?= $rows ?>" <?= $requiredAttr ?> <?= $attrStr ?>><?= htmlspecialchars($value) ?></textarea>
    </div>
    <?php
    return ob_get_clean();
}

function render_status_badge($status, $type = 'order') {
    $badgeClass = $type === 'order' ? get_order_status_badge($status) : get_employee_status_badge($status);
    $label = $type === 'order' ? translate_order_status($status) : $status;
    return sprintf('<span class="badge bg-%s">%s</span>', htmlspecialchars($badgeClass), htmlspecialchars($label));
}

function render_breadcrumb($items) {
    ob_start();
    ?>
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <?php foreach ($items as $url => $label): ?>
            <?php if ($url === '#' || $url === ''): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($label) ?></li>
            <?php else: ?>
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($label) ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol></nav>
    <?php
    return ob_get_clean();
}

function render_empty_state($title = 'Không có dữ liệu', $message = '', $icon = 'inbox') {
    ob_start();
    ?>
    <div class="text-center py-5">
        <i class="bi bi-<?= htmlspecialchars($icon) ?> display-1 text-muted"></i>
        <h4 class="mt-3"><?= htmlspecialchars($title) ?></h4>
        <?php if ($message): ?><p class="text-muted"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

function render_loading_spinner($text = 'Đang tải...') {
    ob_start();
    ?>
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status"><span class="visually-hidden"><?= htmlspecialchars($text) ?></span></div>
        <p class="mt-2 text-muted"><?= htmlspecialchars($text) ?></p>
    </div>
    <?php
    return ob_get_clean();
}

function render_confirm_modal($id, $title, $message, $confirmText = 'Xác nhận', $cancelText = 'Hủy') {
    ob_start();
    ?>
    <div class="modal fade" id="<?= htmlspecialchars($id) ?>" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><?= htmlspecialchars($title) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><?= htmlspecialchars($message) ?></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= htmlspecialchars($cancelText) ?></button><button type="button" class="btn btn-primary" id="<?= htmlspecialchars($id) ?>-confirm"><?= htmlspecialchars($confirmText) ?></button></div></div></div>
    </div>
    <?php
    return ob_get_clean();
}

function render_data_table($columns, $data, $actions = []) {
    ob_start();
    ?>
    <div class="table-responsive"><table class="table table-hover table-striped"><thead><tr><?php foreach ($columns as $col): ?><th><?= htmlspecialchars($col) ?></th><?php endforeach; if (!empty($actions)) echo '<th>Thao tác</th>'; ?></tr></thead><tbody><?php if (empty($data)): ?><tr><td colspan="<?= count($columns) + (!empty($actions) ? 1 : 0) ?>" class="text-center text-muted">Không có dữ liệu</td></tr><?php else: foreach ($data as $row): ?><tr><?php foreach (array_keys($columns) as $key): ?><td><?= htmlspecialchars($row[$key] ?? '') ?></td><?php endforeach; if (!empty($actions)): ?><td><?php foreach ($actions as $action): ?><?= $action($row) ?><?php endforeach; ?></td><?php endif; ?></tr><?php endforeach; endif; ?></tbody></table></div>
    <?php
    return ob_get_clean();
}

function render_stats_card($title, $value, $icon, $bgColor = 'primary', $subtext = '') {
    ob_start();
    ?>
    <div class="card bg-<?= htmlspecialchars($bgColor) ?> text-white"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6 class="card-title mb-1"><?= htmlspecialchars($title) ?></h6><h3 class="mb-0"><?= htmlspecialchars($value) ?></h3><?php if ($subtext): ?><small><?= htmlspecialchars($subtext) ?></small><?php endif; ?></div><div><i class="bi bi-<?= htmlspecialchars($icon) ?> display-4 opacity-50"></i></div></div></div></div>
    <?php
    return ob_get_clean();
}

// Pagination helper for views
function render_pagination_controls($pagination = null, string $pageName = null, array $extraQuery = []): string {
    // Accept null or non-array $pagination for backward compatibility
    if (empty($pagination) || !is_array($pagination) || intval($pagination['total_pages'] ?? 0) <= 1) return '';
    $qp = $_GET;
    if ($pageName) $qp['page'] = $pageName;
    foreach ($extraQuery as $k => $v) $qp[$k] = $v;
    $currentP = intval($pagination['current'] ?? 1);
    $tp = intval($pagination['total_pages'] ?? 1);
    $start = max(1, $currentP - 2);
    $end = min($tp, $currentP + 2);
    ob_start();
    ?>
    <nav aria-label="Page navigation" class="mt-3">
        <ul class="pagination">
            <li class="page-item <?= $currentP <= 1 ? 'disabled' : '' ?>">
                <?php $qp['p'] = max(1, $currentP - 1); ?>
                <a class="page-link" href="index.php?<?= htmlspecialchars(http_build_query($qp)) ?>">&laquo; Trước</a>
            </li>
            <?php for ($i = $start; $i <= $end; $i++): ?>
                <?php $qp['p'] = $i; ?>
                <li class="page-item <?= $i === $currentP ? 'active' : '' ?>"><a class="page-link" href="index.php?<?= htmlspecialchars(http_build_query($qp)) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?= $currentP >= $tp ? 'disabled' : '' ?>">
                <?php $qp['p'] = min($tp, $currentP + 1); ?>
                <a class="page-link" href="index.php?<?= htmlspecialchars(http_build_query($qp)) ?>">Tiếp &raquo;</a>
            </li>
        </ul>
    </nav>
    <?php
    return ob_get_clean();
}

?>
