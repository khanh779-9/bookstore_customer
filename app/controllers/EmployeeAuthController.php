<?php

class EmployeeAuthController
{
    public static function employeeLogin()
    {
        $ma_nhan_vien = trim($_POST['ma_nhan_vien'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($ma_nhan_vien === '' || $password === '') {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
            return self::redirect('index.php?page=employee_login');
        }

        if (!is_numeric($ma_nhan_vien)) {
            $_SESSION['error'] = 'Mã nhân viên không hợp lệ.';
            return self::redirect('index.php?page=employee_login');
        }

        $employee = EmployeeModel:: findEmployeeByCode($ma_nhan_vien);

        if (!$employee) {
            $_SESSION['error'] = 'Mã nhân viên hoặc mật khẩu không đúng.';
            return self::redirect('index.php?page=employee_login');
        }

        if ($employee['trangthai'] !== 'dang_lam') {
            $_SESSION['error'] = 'Tài khoản của bạn không hoạt động.';
            return self::redirect('index.php?page=employee_login');
        }

        $stored = $employee['password'] ?? '';
        $ok = false;

        if ($stored !== '') {
            $info = password_get_info($stored);
            if (!empty($info['algo'])) {
                if (password_verify($password, $stored)) {
                    $ok = true;
                }
            } else {
                if ($stored === $password) {
                    $ok = true;
                }
            }
        }

        if (!$ok) {
            $_SESSION['error'] = 'Mã nhân viên hoặc pass ko hợp lệ.';
            $service_status['error'] = $_SESSION['error'];
            return self::redirect('index.php?page=employee_login');
        }

        session_regenerate_id(true);

        if (function_exists('generate_csrf_token')) generate_csrf_token(true);

        $role = $employee['role'] ?? 'nhanvien';
        if (!in_array($role, ['admin', 'quanly', 'nhanvien'], true)) {
            $role = 'nhanvien';
        }

        $_SESSION['employee_account'] = [
            'id' => $employee['nhanvien_id'],
            'ho' => $employee['ho'] ?? '',
            'tendem' => $employee['tendem'] ?? '',
            'ten' => $employee['ten'] ?? '',
            'ho_ten' => trim(($employee['ho'] ?? '') . ' ' . ($employee['tendem'] ?? '') . ' ' . ($employee['ten'] ?? '')),
            'email' => $employee['email'] ?? '',
            'role' => $role,
            'trangthai' => $employee['trangthai'] ?? 'dang_lam',
            'logged_in' => true,
            'login_time' => time()
        ];

        try {
            $infoAfter = password_get_info($stored);
            if (empty($infoAfter['algo'])) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                EmployeeModel::updatePassword((int)$employee['nhanvien_id'], $newHash);
            }
        } catch (Exception $e) {
        }

        $_SESSION['success'] = 'Chào mừng ' . htmlspecialchars($_SESSION['employee_account']['ho_ten']);
        $landing = $role === 'nhanvien' ? 'employee_orders' : 'employee_dashboard';
        return self::redirect('index.php?page=' . $landing);
    }

    public static function employeeLogout(): void
    {
        $_SESSION['success'] = 'Bạn đã đăng xuất thành công.';
        
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }

        unset($_SESSION['employee_account']);
        self::redirect('index.php?page=employee_login');
    }

    public static function requireEmployeeLogin(): void
    {
        $acc = $_SESSION['employee_account'] ?? null;

        if (!$acc || empty($acc['logged_in'])) {
            self::redirect('index.php?page=employee_login');
        }
    }

    public static function requireEmployeeOrManager(): void
    {
        self::enforceRole(['admin', 'quanly']);
    }

    public static function requireEmployeeAdmin(): void
    {
        self::enforceRole(['admin']);
    }

    public static function enforceRole(array $allowedRoles, string $fallback = 'index.php?page=employee_orders'): void
    {
        self::requireEmployeeLogin();
        $acc = $_SESSION['employee_account'] ?? null;
        $role = $acc['role'] ?? '';

        if (!in_array($role, $allowedRoles, true)) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
            self::redirect($fallback);
        }
    }

    public static function redirect(string $to)
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        header("Location: $to");
        exit;
    }
}

?>

