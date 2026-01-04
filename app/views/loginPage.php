<div class="container mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h4 class="text-center mb-5 fw-bold text-primary">Đăng Nhập</h4>

                    <?php
                    $google_auth_url = '#';
                    try {
                        require_once __DIR__ . '/../login_with_google_helper.php';
                        $google_auth_url = getGoogleAuthUrl();
                    } catch (Exception $e) {
                        $google_auth_url = '#';
                    }

                    ?>

                    <?php if ($service_status['success'] ?? false): ?>
                        <div class="alert alert-success small py-2"><?= htmlspecialchars($service_status['success']) ?></div>
                    <?php endif; ?>

                    <?php if ($service_status['error'] ?? false): ?>
                        <div class="alert alert-danger small py-2"><?= htmlspecialchars($service_status['error']) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=login" name="login_form">
                        <input type="hidden" name="login_form" value="1">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Email</label>
                            <input type="email" name="email" class="form-control"
                                value=""
                                placeholder="Nhập email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-medium">Mật khẩu</label>
                            <div class="position-relative">
                                <input id="login_password" type="password" name="password" class="form-control"
                                    placeholder="Nhập mật khẩu" required>
                                  <i id="login_toggle" class="bi bi-eye-fill position-absolute" style="right:16px;top:50%;transform: translateY(-50%);cursor:pointer;font-size:1.1rem;"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-medium">
                            Đăng Nhập
                        </button>

                        <div class="text-center my-3">
                            <a href="<?= htmlspecialchars($google_auth_url) ?>" class="btn btn-outline-danger w-100 fw-medium">
                                <i class="bi bi-google me-2"></i> Đăng nhập với Google
                            </a>
                        </div>
                    </form>

                    <div class="text-center mt-3 small text-muted">
                        Chưa có tài khoản?
                        <a href="index.php?page=register" class="text-decoration-none fw-bold text-primary">Đăng ký</a>
                    </div>

                    <div class="text-center mt-2 small">
                        <a href="index.php?page=forgot_resetPass" class="text-decoration-none">Quên mật khẩu?</a>
                    </div>

                    <div class="text-center mt-3 small text-muted">
                         Đăng nhập với tài khoản nội bộ?
                         <a href="index.php?page=employee_login" class="text-decoration-none fw-bold text-danger">Đăng nhập</a>
                    </div>
                    <script>
                        (function(){
                            const ip = document.getElementById('login_password');
                            const ic = document.getElementById('login_toggle');
                            if (!ip || !ic) return;
                            ic.addEventListener('click', ()=>{
                                if (ip.type === 'password') { ip.type = 'text'; ic.className='bi bi-eye-slash-fill position-absolute'; }
                                else { ip.type = 'password'; ic.className='bi bi-eye-fill position-absolute'; }
                            });
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>