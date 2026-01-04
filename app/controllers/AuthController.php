<?php

class AuthController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Database::getInstance();
    }

    public function login()
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
            return $this->redirect('index.php?page=login');
        }

        $user = CustomerModel::findCustomerByEmail($email);

        if (!$user) {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng.';
            return $this->redirect('index.php?page=login');
        }

        $stored = $user['password'] ?? '';
        $ok = false;

        if ($stored !== '') {
            $info = password_get_info($stored);
            if (!empty($info['algo'])) {
                if (password_verify($password, $stored)) {
                    $ok = true;
                    if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
                        try {
                            $newHash = password_hash($password, PASSWORD_DEFAULT);
                            CustomerModel::updatePassword((int)$user['khachhang_id'], $newHash);
                        } catch (Exception $e) {
                        }
                    }
                }
            } else {
                if ($stored === $password) {
                    $ok = true;
                    try {
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        CustomerModel::updatePassword((int)$user['khachhang_id'], $newHash);
                    } catch (Exception $e) {
                    }
                }
            }
        }

        if (!$ok) {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng.';
            return $this->redirect('index.php?page=login');
        }

        session_regenerate_id(true);

        if (function_exists('generate_csrf_token')) generate_csrf_token(true);

        $_SESSION['khachhang_account'] = [
            'id'        => $user['khachhang_id'],
            'ho'        => $user['ho'] ?? '',
            'tendem'    => $user['tendem'] ?? '',
            'ten'       => $user['ten'] ?? '',
            'ho_ten'    => trim(($user['ho'] ?? '') . ' ' . ($user['tendem'] ?? '') . ' ' . ($user['ten'] ?? '')),
            'email'     => $user['email'] ?? '',
            'ngaysinh'  => $user['ngaysinh'] ?? null,
            'diachi'    => $user['diachi'] ?? null,
            'sdt'       => $user['sdt'] ?? null,
            'gioitinh'  => $user['gioitinh'] ?? null,
            'ngaythamgia' => $user['ngaythamgia'] ?? null,
            'role'      => 'customer',
            'logged_in' => true,
            'login_time' => time()
        ];

        global $customer;
        $customer = $_SESSION['khachhang_account'];
        $_SESSION['success'] = 'Chào mừng ' . htmlspecialchars($_SESSION['khachhang_account']['ho_ten']);

        include_once __DIR__ . "/../models/notification.php";
        include_once __DIR__ . "/helpers.php";

        $agent = getLoginAgent();

        NotificationModel::createNotification(
            $customer['id'],
            'Đăng nhập tài khoản',
            "Phát hiện lần đăng nhập mới nhất từ \n" .
                "Thiết bị: {$agent['Device']}\n" .
                "Trình duyệt: {$agent['Browser']}\n" .
                "Hệ điều hành: {$agent['OperatingSystem']}"
        );
        if (!empty($_SESSION['guest_cart']) && is_array($_SESSION['guest_cart'])) {
            foreach ($_SESSION['guest_cart'] as $pid => $qty) {
                $pid = (int)$pid;
                $qty = (int)$qty;
                if ($pid <= 0 || $qty <= 0) continue;
                $product = ProductModel::getProductById($pid);
                $price = (float)($product['gia'] ?? 0);
                try {
                    CartModel::addCartItem((int)$customer['id'], $pid, $qty, $price);
                } catch (Exception $e) {
                }
            }
            unset($_SESSION['guest_cart']);
        }

        return $this->redirect('index.php');
    }
    
    public function loginWithGoogle()
    {
        require_once __DIR__ . '/../login_with_google_helper.php';

        $code = $_GET['code'] ?? null;

        if (!$code) {
            $_SESSION['error'] = 'Đăng nhập Google thất bại. Vui lòng thử lại.';
            return $this->redirect('index.php?page=login');
        }

        $accessToken = getGoogleAccessToken($code);

        if (!$accessToken) {
            $_SESSION['error'] = 'Không thể xác thực với Google. Vui lòng thử lại.';
            return $this->redirect('index.php?page=login');
        }

        $googleUser = getGoogleUserInfo($accessToken);

        if (!$googleUser || empty($googleUser['email'])) {
            $_SESSION['error'] = 'Không thể lấy thông tin từ Google. Vui lòng thử lại.';
            return $this->redirect('index.php?page=login');
        }

        $email = $googleUser['email'];
        $user = CustomerModel::findCustomerByEmail($email);

        if (!$user) {
            $givenName = $googleUser['given_name'] ?? '';
            $familyName = $googleUser['family_name'] ?? '';
            $name = $googleUser['name'] ?? '';

            $nameParts = explode(' ', trim($name));
            $ho = $nameParts[0] ?? '';
            $ten = array_pop($nameParts) ?? '';
            $tendem = implode(' ', array_slice($nameParts, 1));

            $randomPassword = bin2hex(random_bytes(16));
            $hash = password_hash($randomPassword, PASSWORD_DEFAULT);

            $userId = CustomerModel::createCustomer([
                'password' => $hash,
                'ho' => $ho ?: $familyName,
                'tendem' => $tendem,
                'ten' => $ten ?: $givenName,
                'email' => $email,
                'ngaysinh' => null,
                'diachi' => null,
                'sdt' => null,
                'gioitinh' => null
            ]);

            if (!$userId) {
                $_SESSION['error'] = 'Không thể tạo tài khoản. Vui lòng thử lại.';
                return $this->redirect('index.php?page=login');
            }

            $user = CustomerModel::findCustomerByEmail($email);
        }

        session_regenerate_id(true);

        if (function_exists('generate_csrf_token')) {
            generate_csrf_token(true);
        }

        $_SESSION['khachhang_account'] = [
            'id'        => $user['khachhang_id'],
            'ho'        => $user['ho'] ?? '',
            'tendem'    => $user['tendem'] ?? '',
            'ten'       => $user['ten'] ?? '',
            'ho_ten'    => trim(($user['ho'] ?? '') . ' ' . ($user['tendem'] ?? '') . ' ' . ($user['ten'] ?? '')),
            'email'     => $user['email'] ?? '',
            'ngaysinh'  => $user['ngaysinh'] ?? null,
            'diachi'    => $user['diachi'] ?? null,
            'sdt'       => $user['sdt'] ?? null,
            'gioitinh'  => $user['gioitinh'] ?? null,
            'ngaythamgia' => $user['ngaythamgia'] ?? null,
            'role'      => 'customer',
            'logged_in' => true,
            'login_time' => time(),
            'google_login' => true
        ];

        global $customer;
        $customer = $_SESSION['khachhang_account'];
        $_SESSION['success'] = 'Chào mừng ' . htmlspecialchars($_SESSION['khachhang_account']['ho_ten']);
        include_once __DIR__ . "/../models/notification.php";
        include_once __DIR__ . "/../helpers.php";

        $agent = getLoginAgent();

        NotificationModel::createNotification(
            $customer['id'],
            'Đăng nhập qua Google',
            "Phát hiện lần đăng nhập mới qua Google từ \n" .
                "Thiết bị: {$agent['Device']}\n" .
                "Trình duyệt: {$agent['Browser']}\n" .
                "Hệ điều hành: {$agent['OperatingSystem']}"
        );
        if (!empty($_SESSION['guest_cart']) && is_array($_SESSION['guest_cart'])) {
            foreach ($_SESSION['guest_cart'] as $pid => $qty) {
                $pid = (int)$pid;
                $qty = (int)$qty;
                if ($pid <= 0 || $qty <= 0) continue;
                $product = ProductModel::getProductById($pid);
                $price = (float)($product['gia'] ?? 0);
                try {
                    CartModel::addCartItem((int)$customer['id'], $pid, $qty, $price);
                } catch (Exception $e) {
                    // Silent fail
                }
            }
            unset($_SESSION['guest_cart']);
        }

        return $this->redirect('index.php');
    }
    public function register()
    {
        $ho = trim($_POST['ho'] ?? '');
        $tendem = trim($_POST['tendem'] ?? '');
        $ten = trim($_POST['ten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $sdt = trim($_POST['sdt'] ?? '');
        $ngaysinh = $_POST['ngaysinh'] ?? null;
        $diachi = trim($_POST['diachi'] ?? '');
        $gioitinh = $_POST['gioitinh'] ?? null;

        if ($ho === '' || $ten === '' || $email === '' || $password === '') {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ các trường bắt buộc.';
            return $this->redirect('index.php?page=register');
        }

        if ($password !== $password2) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp.';
            return $this->redirect('index.php?page=register');
        }

        if (CustomerModel::findCustomerByEmail($email)) {
            $_SESSION['error'] = 'Email đã được sử dụng.';
            return $this->redirect('index.php?page=register');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $id = CustomerModel::createCustomer([
            'password' => $hash,
            'ho' => $ho,
            'tendem' => $tendem,
            'ten' => $ten,
            'ngaysinh' => $ngaysinh,
            'diachi' => $diachi,
            'sdt' => $sdt,
            'email' => $email,
            'gioitinh' => $gioitinh
        ]);

        if (!$id) {
            $_SESSION['error'] = 'Đăng ký thất bại, vui lòng thử lại.';
            return $this->redirect('index.php?page=register');
        }

        $_SESSION['success'] = 'Đăng ký thành công. Vui lòng đăng nhập.';
        return $this->redirect('index.php?page=login');
    }

    public function logout(): void
    {
        $_SESSION['success'] = 'Bạn đã đăng xuất thành công.';

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }

        session_destroy();
        $this->redirect('index.php?page=login');
    }

    public function requireLogin(): void
    {
        $acc = $_SESSION['khachhang_account'] ?? null;

        if (!$acc || empty($acc['logged_in'])) {
            $this->redirect('index.php?page=login');
        }
    }

    public function redirect(string $to)
    {
        if (!headers_sent()) {
            header("Location: $to");
            exit;
        }

        echo "<script>window.location.href='$to';</script>";
        exit;
    }
}

?>