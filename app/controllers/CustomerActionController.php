<?php

class CustomerController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Database::getInstance();
    }

    public function handleCustomerLogin()
    {
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=login');
        login();
    }

    public function handleCustomerRegister()
    {
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=register');
        register();
    }

    public function handleForgotRequest()
    {
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=forgot_resetPass');

        $email = trim($_POST['email'] ?? '');
        if ($email === '' || !validate_email($email)) {
            $_SESSION['error'] = 'Vui lòng nhập email hợp lệ.';
            redirect('index.php?page=forgot_resetPass');
        }

        $user = CustomerModel::findCustomerByEmail($email);
        if (!$user) {
            $_SESSION['error'] = 'Email không tồn tại trong hệ thống.';
            redirect('index.php?page=forgot_resetPass');
        }

        // Generate a 6-digit numeric code and store in session for later verification
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $_SESSION['password_reset'] = [
            'email' => $email,
            'code' => $code,
            'expires' => time() + 15 * 60, // 15 minutes
            'sent_at' => time()
        ];

        // Try to load mail helper if available, then send email with the code
        if (!class_exists('MailHelper')) {
            $mh = __DIR__ . '/../mail_helper.php';
            if (file_exists($mh)) require_once $mh;
        }

        if (class_exists('MailHelper')) {
            // send HTML email containing the code (mail helper uses PHPMailer)
            MailHelper::sendPasswordResetEmail($email, "Mã xác nhận của bạn là: {$code}. Mã này có hiệu lực trong 15 phút.");
        } else {
            app_log('MailHelper not available; password reset code generated but not emailed.', 'WARNING');
        }

        $_SESSION['success'] = 'Mã xác nhận đã được đã dược gửi đến bạn. Vui lòng kiểm tra email.';
        redirect('index.php?page=forgot_resetPass');
    }

    public function handleForgotVerify()
    {
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=forgot_resetPass');

        $code = trim($_POST['code'] ?? '');
        $pr = $_SESSION['password_reset'] ?? null;
        if (!$pr || empty($pr['code']) || ($pr['expires'] ?? 0) < time()) {
            $_SESSION['error'] = 'Yêu cầu đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.';
            unset($_SESSION['password_reset']);
            redirect('index.php?page=forgot_resetPass');
        }

        if ($code !== $pr['code']) {
            $_SESSION['error'] = 'Mã xác nhận không đúng.';
            redirect('index.php?page=forgot_resetPass');
        }

        // mark verified
        $_SESSION['password_reset']['verified'] = true;
        $_SESSION['success'] = 'Mã xác nhận hợp lệ. Vui lòng đặt mật khẩu mới.';
        redirect('index.php?page=forgot_resetPass');
    }

    public function handleForgotReset()
    {
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=forgot_resetPass');

        $pr = $_SESSION['password_reset'] ?? null;
        if (!$pr || empty($pr['verified']) || ($pr['expires'] ?? 0) < time()) {
            $_SESSION['error'] = 'Yêu cầu đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.';
            unset($_SESSION['password_reset']);
            redirect('index.php?page=forgot_resetPass');
        }

        $pw = $_POST['password'] ?? '';
        $pw2 = $_POST['password2'] ?? '';
        if ($pw === '' || $pw !== $pw2) {
            $_SESSION['error'] = 'Mật khẩu trống hoặc không khớp.';
            redirect('index.php?page=forgot_resetPass');
        }

        $user = CustomerModel::findCustomerByEmail($pr['email']);
        if (!$user) {
            $_SESSION['error'] = 'Người dùng không tồn tại.';
            unset($_SESSION['password_reset']);
            redirect('index.php?page=forgot_resetPass');
        }

        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $ok = CustomerModel::updatePassword((int)$user['khachhang_id'], $hash);
        if (!$ok) {
            $_SESSION['error'] = 'Không thể cập nhật mật khẩu. Vui lòng thử lại.';
            redirect('index.php?page=forgot_resetPass');
        }

        unset($_SESSION['password_reset']);
        $_SESSION['success'] = 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.';
        redirect('index.php?page=login');
    }

    public function handleCustomerLogout()
    {
        logout();
    }

    public function handleProfileUpdate()
    {
        requireLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=account');

        $customer_id = $_SESSION['khachhang_account']['id'] ?? null;
        if (!$customer_id) {
            $_SESSION['error'] = 'Vui lòng đăng nhập.';
            redirect('index.php?page=login');
        }

        $data = [
            'ho' => trim($_POST['ho'] ?? ''),
            'tendem' => trim($_POST['tendem'] ?? ''),
            'ten' => trim($_POST['ten'] ?? ''),
            'ngaysinh' => trim($_POST['ngaysinh'] ?? '') ?: null,
            'sdt' => trim($_POST['sdt'] ?? '') ?: null,
            'diachi' => trim($_POST['diachi'] ?? ''),
        ];

        $ok = CustomerModel::updateCustomer((int)$customer_id, $data);
        if ($ok) {
            $user = CustomerModel::getCustomerById($customer_id);
            if ($user) {
                $_SESSION['khachhang_account'] = [
                    'id' => $user['khachhang_id'],
                    'ho' => $user['ho'] ?? '',
                    'tendem' => $user['tendem'] ?? '',
                    'ten' => $user['ten'] ?? '',
                    'ho_ten' => trim(($user['ho'] ?? '') . ' ' . ($user['tendem'] ?? '') . ' ' . ($user['ten'] ?? '')),
                    'email' => $user['email'] ?? '',
                    'ngaysinh' => $user['ngaysinh'] ?? null,
                    'diachi' => $user['diachi'] ?? null,
                    'sdt' => $user['sdt'] ?? null,
                    'gioitinh' => $user['gioitinh'] ?? null,
                    'ngaythamgia' => $user['ngaythamgia'] ?? null,
                    'logged_in' => true,
                    'login_time' => $_SESSION['khachhang_account']['login_time'] ?? time()
                ];
            }
            $_SESSION['success'] = 'Cập nhật thông tin thành công.';
        } else {
            $_SESSION['error'] = 'Không thể cập nhật thông tin. Vui lòng thử lại.';
        }

        redirect('index.php?page=account');
    }

    public function handleCartAdd()
    {
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=products');

        $product_id = (int)($_POST['product_id'] ?? 0);
        if ($product_id <= 0) {
            $_SESSION['error'] = 'Sản phẩm không hợp lệ.';
            redirect('index.php?page=products');
        }

        $product = ProductModel::getProductById($product_id);
        if (empty($product)) {
            $_SESSION['error'] = 'Sản phẩm không tồn tại.';
            redirect('index.php?page=products');
        }

        $customer_id = $_SESSION['khachhang_account']['id'] ?? null;
        if ($customer_id) {
            CartModel::addCartItem($customer_id, $product_id, 1, $product['gia'] ?? 0);
        } else {
            if (empty($_SESSION['guest_cart']) || !is_array($_SESSION['guest_cart'])) $_SESSION['guest_cart'] = [];
            $cur = (int)($_SESSION['guest_cart'][$product_id] ?? 0);
            $_SESSION['guest_cart'][$product_id] = $cur + 1;
        }

        $_SESSION['success'] = 'Đã thêm vào giỏ hàng.';
        redirect('index.php?page=cart');
    }

    public function handleCartUpdate()
    {
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=cart');

        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (isset($_POST['decrease'])) {
            $quantity = max(1, $quantity - 1);
        }
        if (isset($_POST['increase'])) {
            $quantity = $quantity + 1;
        }

        $customer_id = $_SESSION['khachhang_account']['id'] ?? null;
        if ($customer_id) {
            CartModel::updateCartItem($customer_id, $product_id, $quantity);
        } else {
            if (empty($_SESSION['guest_cart']) || !is_array($_SESSION['guest_cart'])) $_SESSION['guest_cart'] = [];
            if ($quantity <= 0) {
                unset($_SESSION['guest_cart'][$product_id]);
            } else {
                $_SESSION['guest_cart'][$product_id] = $quantity;
            }
        }

        redirect('index.php?page=cart');
    }

    public function handleCartRemove()
    {
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=cart');

        $product_id = (int)($_POST['product_id'] ?? 0);
        $customer_id = $_SESSION['khachhang_account']['id'] ?? null;

        if ($customer_id) {
            CartModel::updateCartItem($customer_id, $product_id, 0);
        } else {
            if (!empty($_SESSION['guest_cart']) && isset($_SESSION['guest_cart'][$product_id])) {
                unset($_SESSION['guest_cart'][$product_id]);
            }
        }

        $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
        redirect('index.php?page=cart');
    }

    public function handleCheckout()
    {
        requireLogin();
        redirect('index.php?page=checkout');
    }

    public function handleCheckoutConfirm()
    {
        requireLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=checkout');

        $customer_id = $_SESSION['khachhang_account']['id'] ?? null;
        if (!$customer_id) {
            $_SESSION['error'] = 'Vui lòng đăng nhập.';
            redirect('index.php?page=login');
        }

        $cartRow = CartModel::getCart($customer_id);
        if (empty($cartRow['giohang_id'])) {
            $_SESSION['error'] = 'Giỏ hàng trống.';
            redirect('index.php?page=cart');
        }

        $cart_items = CartModel::getCartItems($customer_id);
        if (empty($cart_items)) {
            $_SESSION['error'] = 'Giỏ hàng trống.';
            redirect('index.php?page=cart');
        }

        try {
            // Pre-check stock availability via ProductModel
            foreach ($cart_items as $it) {
                $product = ProductModel::getProductById((int)$it['sanpham_id']);
                $stock = (int)($product['soluongton'] ?? 0);
                if ($stock < (int)$it['soluong']) {
                    $_SESSION['error'] = 'Sản phẩm "' . ($it['name'] ?? '') . '" không đủ số lượng trong kho.';
                    redirect('index.php?page=cart');
                    return;
                }
            }

            $phuongthuc = $_POST['phuongthuc'] ?? 'tien_mat';
            $dcgh_id = !empty($_POST['dcgh_id']) ? (int)$_POST['dcgh_id'] : null;

            $items = [];
            foreach ($cart_items as $it) {
                $items[] = [
                    'product_id' => (int)$it['sanpham_id'],
                    'quantity' => (int)$it['soluong'],
                    'price' => (float)($it['gia'] ?? $it['dongia'] ?? 0)
                ];
            }

            $hoadon_id = OrdersModel::createOrder($customer_id, $items, $phuongthuc, $dcgh_id);
            if (!$hoadon_id) {
                $_SESSION['error'] = 'Có lỗi khi xử lý đơn hàng. Vui lòng thử lại.';
                redirect('index.php?page=checkout');
                return;
            }

            // Xoá giỏ hàng sau khi đặt hàng thành công
            CartModel::clearCart($cartRow['giohang_id']);

            $_SESSION['success'] = 'Đặt hàng thành công! Mã đơn hàng: #' . $hoadon_id;

            redirect('index.php?page=orders');
        } catch (Exception $e) {
            app_log('Checkout error: ' . $e->getMessage(), 'ERROR');
            $_SESSION['error'] = 'Có lỗi khi xử lý đơn hàng. Vui lòng thử lại.';
            redirect('index.php?page=checkout');
        }
    }

    public function handleWishlistToggle()
    {
        requireLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', $_SERVER['HTTP_REFERER'] ?? 'index.php?page=products');
        $customer_id = $_SESSION['khachhang_account']['id'] ?? 0;
        if ($customer_id == 0) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để sử dụng tính năng yêu thích.';
            redirect('index.php?page=login');
        }

        $product_id = (int)($_POST['product_id'] ?? 0);
        if ($product_id <= 0) {
            $_SESSION['error'] = 'Sản phẩm không hợp lệ.';
            redirect($_SERVER['HTTP_REFERER'] ?? 'index.php?page=products');
        }

        $isFavorite =  WishlistModel::isProductFavorite($customer_id, $product_id);
        $result = WishlistModel::toggleWishlist($customer_id, $product_id);

        if ($result) {
            $_SESSION['success'] = $isFavorite
                ? 'Đã xóa khỏi danh sách yêu thích'
                : 'Đã thêm vào danh sách yêu thích';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra. Vui lòng thử lại.';
        }

        $redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php?page=products';
        redirect($redirect);
    }

    public function handleNotificationMark()
    {
        requireLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', $_SERVER['HTTP_REFERER'] ?? 'index.php');

        $customer_id = $_SESSION['khachhang_account']['id'] ?? null;
        if (!$customer_id) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để thực hiện hành động.';
            redirect('index.php?page=login');
        }

        $note_id = (int)($_POST['mark_notification_id'] ?? 0);
        if ($note_id <= 0) {
            redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
        }

        $isRead =  NotificationModel::isNotificationReaded($note_id, $customer_id);
        NotificationModel::markNotificationRead($note_id, $customer_id, !$isRead);

        redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
    }

    public function handleNotificationArchive()
    {
        requireLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', $_SERVER['HTTP_REFERER'] ?? 'index.php');

        $customer_id = $_SESSION['khachhang_account']['id'] ?? null;
        if (!$customer_id) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để thực hiện hành động.';
            redirect('index.php?page=login');
        }

        $note_id = (int)($_POST['archive_notification_id'] ?? 0);
        if ($note_id <= 0) {
            redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
        }

        NotificationModel::archiveNotification($note_id, $customer_id);

        redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
    }

    public function handleNotificationMarkAll()
    {
        requireLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=notifications');

        $customer_id = $_SESSION['khachhang_account']['id'] ?? null;
        if (!$customer_id) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để thực hiện hành động.';
            redirect('index.php?page=login');
        }

        NotificationModel::markAllRead((int)$customer_id);
        $_SESSION['success'] = 'Đã đánh dấu tất cả là đã đọc.';
        redirect('index.php?page=notifications');
    }

    public function handleSubmitReview()
    {
        requireLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', $_SERVER['HTTP_REFERER'] ?? 'index.php');

        $customer_id = $_SESSION['khachhang_account']['id'] ?? null;
        if (!$customer_id) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đánh giá sản phẩm.';
            redirect('index.php?page=login');
        }

        $product_id = (int)($_POST['product_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($product_id <= 0 || $rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Dữ liệu đánh giá không hợp lệ.';
            redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
        }

        if (!OrdersModel::customerHasPurchasedProduct($customer_id, $product_id)) {
            $_SESSION['error'] = 'Chỉ khách hàng đã mua sản phẩm mới có thể đánh giá.';
            redirect('index.php?page=productview&id=' . $product_id);
        }

        if (ReviewsModel::customerHasReviewed($customer_id, $product_id)) {
            $_SESSION['error'] = 'Bạn đã đánh giá sản phẩm này rồi.';
            redirect('index.php?page=productview&id=' . $product_id);
        }

        $ok = ReviewsModel::createReview($customer_id, $product_id, $rating, $comment);
        if ($ok) {
            $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá sản phẩm.';
        } else {
            $_SESSION['error'] = 'Có lỗi khi lưu đánh giá. Vui lòng thử lại.';
        }

        redirect('index.php?page=productview&id=' . $product_id);
    }

    public function handleChangePassword()
    {
        requireLogin();
        require_csrf_or_redirect($_POST['csrf_token'] ?? '', 'index.php?page=account');

        $customer_id = $_SESSION['khachhang_account']['id'] ?? null;
        if (!$customer_id) {
            $_SESSION['error'] = 'Vui lòng đăng nhập.';
            redirect('index.php?page=login');
        }

        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $new2 = $_POST['new_password2'] ?? '';

        if ($new === '' || $new !== $new2) {
            $_SESSION['error'] = 'Mật khẩu mới rỗng hoặc không khớp.';
            $_SESSION['open_tab'] = 'change-password';
            redirect('index.php?page=account');
        }

        $user = CustomerModel::getCustomerById($customer_id);
        if (!$user) {
            $_SESSION['error'] = 'Người dùng không tồn tại.';
            $_SESSION['open_tab'] = 'change-password';
            redirect('index.php?page=account');
        }

        $stored = $user['password'] ?? '';
        $ok = false;
        if ($stored !== '') {
            $info = password_get_info($stored);
            if (!empty($info['algo'])) {
                if (password_verify($current, $stored)) {
                    $ok = true;
                }
            } else {
                // legacy plaintext stored — allow migration and re-hash immediately
                if ($stored === $current) {
                    $ok = true;
                    try {
                        $newHash = password_hash($current, PASSWORD_DEFAULT);
                        CustomerModel::updatePassword((int)$customer_id, $newHash);
                    } catch (Exception $e) {
                    }
                }
            }
        }

        if (!$ok) {
            $_SESSION['error'] = 'Mật khẩu hiện tại không đúng.';
            $_SESSION['open_tab'] = 'change-password';
            redirect('index.php?page=account');
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $updated = CustomerModel::updatePassword((int)$customer_id, $hash);
        if ($updated) {
            // instruct account page to open change-password tab after redirect
            $_SESSION['open_tab'] = 'change-password';
            redirect('index.php?page=account');
        }

        // still open change-password tab so user sees the error
        $_SESSION['open_tab'] = 'change-password';
        redirect('index.php?page=account');
    }

    public function handleAddAddress()
    {
        $customer = $_SESSION['khachhang_account'] ?? null;
        if (!$customer) {
            redirect('index.php?page=login');
        }

        $diachi = trim($_POST['diachi'] ?? '');
        if (empty($diachi)) {
            $_SESSION['error'] = 'Vui lòng nhập địa chỉ.';
            redirect('index.php?page=account');
        }

        $result = AddressesModel::addAddress($customer['id'], $diachi);
        if ($result) {
            $_SESSION['success'] = 'Thêm địa chỉ thành công.';
        } else {
            $_SESSION['error'] = 'Không thể thêm địa chỉ.';
        }
        redirect('index.php?page=account');
    }

    public function handleEditAddress()
    {
        $customer = $_SESSION['khachhang_account'] ?? null;
        if (!$customer) {
            redirect('index.php?page=login');
        }

        $dcgh_id = intval($_POST['dcgh_id'] ?? 0);
        $diachi = trim($_POST['diachi'] ?? '');

        if ($dcgh_id <= 0 || empty($diachi)) {
            $_SESSION['error'] = 'Thông tin không hợp lệ.';
            redirect('index.php?page=account');
        }

        // Kiểm tra địa chỉ thuộc về khách hàng
        $address = AddressesModel::getAddressById($dcgh_id);
        if (!$address || $address['khachhang_id'] != $customer['id']) {
            $_SESSION['error'] = 'Không tìm thấy địa chỉ.';
            redirect('index.php?page=account');
        }

        $result = AddressesModel::updateAddress($dcgh_id, $diachi);
        if ($result) {
            $_SESSION['success'] = 'Cập nhật địa chỉ thành công.';
        } else {
            $_SESSION['error'] = 'Không thể cập nhật địa chỉ.';
        }
        redirect('index.php?page=account');
    }

    public function handleDeleteAddress()
    {
        $customer = $_SESSION['khachhang_account'] ?? null;
        if (!$customer) {
            redirect('index.php?page=login');
        }

        $dcgh_id = intval($_POST['delete_address'] ?? 0);
        if ($dcgh_id <= 0) {
            $_SESSION['error'] = 'ID địa chỉ không hợp lệ.';
            redirect('index.php?page=account');
        }

        // Kiểm tra địa chỉ thuộc về khách hàng
        $address = AddressesModel::getAddressById($dcgh_id);
        if (!$address || $address['khachhang_id'] != $customer['id']) {
            $_SESSION['error'] = 'Không tìm thấy địa chỉ.';
            redirect('index.php?page=account');
        }

        $result = AddressesModel::deleteAddress($dcgh_id);
        if ($result) {
            $_SESSION['success'] = 'Xóa địa chỉ thành công.';
        } else {
            $_SESSION['error'] = 'Không thể xóa địa chỉ.';
        }
        redirect('index.php?page=account');
    }

    public function routeCustomerActions($page)
    {
        switch ($page) {
            case 'login':
                $this->handleCustomerLogin();
                break;
            case 'register':
                $this->handleCustomerRegister();
                break;
            case 'cart_add':
                $this->handleCartAdd();
                break;
            case 'cart_update':
                $this->handleCartUpdate();
                break;
            case 'cart_remove':
                $this->handleCartRemove();
                break;
            case 'checkout':
                $this->handleCheckout();
                break;
            case 'checkout_confirm':
                $this->handleCheckoutConfirm();
                break;
            case 'wishlist_toggle':
                $this->handleWishlistToggle();
                break;
            case 'submit_review':
                $this->handleSubmitReview();
                break;
            case 'change_password':
                $this->handleChangePassword();
                break;
            case 'forgot_request':
                $this->handleForgotRequest();
                break;
            case 'forgot_verify':
                $this->handleForgotVerify();
                break;
            case 'forgot_reset':
                $this->handleForgotReset();
                break;
            case 'notifications_mark':
                $this->handleNotificationMark();
                break;
            case 'notifications_archive':
                $this->handleNotificationArchive();
                break;
            case 'notifications_mark_all':
                $this->handleNotificationMarkAll();
                break;
            default:
                if (isset($_POST['logout_exc'])) {
                    $this->handleCustomerLogout();
                    break;
                }
                if (isset($_POST['update_profile'])) {
                    $this->handleProfileUpdate();
                    break;
                }
                if (isset($_POST['add_address'])) {
                    $this->handleAddAddress();
                    break;
                }
                if (isset($_POST['edit_address'])) {
                    $this->handleEditAddress();
                    break;
                }
                if (isset($_POST['delete_address'])) {
                    $this->handleDeleteAddress();
                    break;
                }
                break;
        }
    }
}

// Legacy wrapper moved to `app/legacy_wrappers.php`
