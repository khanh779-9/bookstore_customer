<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Nhân viên - BookZone</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #eef2f6;
            /* margin: 0; */
            /* padding: 0; */
        }

        .login-container {
            min-height: 100vh;
        }

        .login-header {
            background: #1e293b;
        }

        .btn-primary {
            background: #1e293b;
            border-color: #1e293b;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: #334155;
            border-color: #334155;
        }
    </style>
</head>

<body>

    <div class="login-container d-flex justify-content-center">
        <div class="card border-0 shadow w-100 rounded-4 overflow-hidden mt-5" style="max-width: 420px; max-height: fit-content;">

            <div class="login-header text-white text-center py-4 px-3">
                <h4 class="mb-2 d-flex justify-content-center align-items-center gap-2 fw-semibold">
                    <i class="fas fa-user-tie"></i> Đăng nhập Nhân viên
                </h4>
                <p class="mb-0 small opacity-75">Hệ thống quản lý nội bộ</p>
            </div>

            <div class="card-body p-4">

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start mb-3">
                        <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <?= htmlspecialchars($_SESSION['error'] ?? '') ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <?= htmlspecialchars($_SESSION['success'] ?? '') ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <form method="POST" action="index.php?page=employee_login">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="ma_nhan_vien">Mã nhân viên</label>
                        <input type="number" name="ma_nhan_vien" id="ma_nhan_vien"
                            class="form-control" placeholder="Nhập mã nhân viên"
                            required autofocus min="1">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="password">Mật khẩu</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="password"
                                class="form-control pe-5" placeholder="Nhập mật khẩu"
                                required>
                            <span class="position-absolute end-0 top-50 translate-middle-y me-3" id="togglePassword" style="cursor: pointer;">
                                <i class="fas fa-eye-slash text-secondary"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3 fw-semibold">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                    </button>

                </form>

                <div class="text-center">
                    <a href="index.php" class="text-secondary text-decoration-none small">
                        <i class="fas fa-arrow-left me-1"></i>Về trang chủ
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        toggleBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>