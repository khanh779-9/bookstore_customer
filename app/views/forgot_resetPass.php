<div class="container mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h4 class="text-center mb-5 fw-bold text-primary">Đăng Nhập</h4>

                    <?php if ($service_status['success'] ?? false): ?>
                        <div class="alert alert-success small py-2"><?= htmlspecialchars($service_status['success']) ?></div>
                    <?php endif; ?>

                    <?php if ($service_status['error'] ?? false): ?>
                        <div class="alert alert-danger small py-2"><?= htmlspecialchars($service_status['error']) ?></div>
                    <?php endif; ?>

                    <?php
                        $pr = $_SESSION['password_reset'] ?? null;
                        $now = time();
                    ?>

                    <?php if (!$pr || empty($pr['email']) || ($pr['expires'] ?? 0) < $now): ?>
                        <form method="POST" action="index.php?page=forgot_request" name="forgot_form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

                            <div class="mb-3">
                                <label class="form-label small fw-medium">Nhập email của bạn</label>
                                <input id="forgot_email" type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                    placeholder="nhập email để nhận mã xác nhận" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-medium">Gửi mã xác nhận</button>
                        </form>

                    <?php elseif (empty($pr['verified'])): ?>
                        <div class="mb-3 small text-muted">Mã đã được gửi tới <strong><?= htmlspecialchars($pr['email']) ?></strong>. Mã có hiệu lực đến <?= date('H:i:s d/m/Y', $pr['expires']) ?>.</div>
                        <form method="POST" action="index.php?page=forgot_verify">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Nhập mã xác nhận</label>
                                <input type="text" name="code" class="form-control" placeholder="6 chữ số" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-medium">Xác nhận mã</button>
                        </form>
                        <?php
                            $sent = $pr['sent_at'] ?? 0;
                            $wait = 20; // seconds
                            $remaining = max(0, $wait - (time() - $sent));
                        ?>

                        <div class="mt-3 text-center small">
                            <form id="resend_form" method="POST" action="index.php?page=forgot_request" style="display:inline-block">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <input type="hidden" name="email" value="<?= htmlspecialchars($pr['email'] ?? '') ?>">
                                <button id="resend_btn" type="submit" class="btn btn-link p-0" <?php if ($remaining>0) echo 'disabled'; ?> >Gửi lại mã</button>
                            </form>
                            <div id="resend_info" class="small text-muted mt-1"><?php if ($remaining>0) echo 'Vui lòng chờ ' . $remaining . ' giây để gửi lại.'; ?></div>
                        </div>

                        <script>
                            (function(){
                                var remaining = <?= (int)$remaining ?>;
                                var btn = document.getElementById('resend_btn');
                                var info = document.getElementById('resend_info');
                                if (!btn || !info) return;
                                if (remaining > 0) {
                                    btn.setAttribute('disabled', 'disabled');
                                    var interval = setInterval(function(){
                                        remaining--;
                                        if (remaining <= 0) {
                                            clearInterval(interval);
                                            btn.removeAttribute('disabled');
                                            info.textContent = '';
                                        } else {
                                            info.textContent = 'Vui lòng chờ ' + remaining + ' giây để gửi lại.';
                                        }
                                    }, 1000);
                                }
                            })();
                        </script>

                    <?php else: ?>
                        <div class="mb-3 small text-muted">Đặt mật khẩu mới cho <strong><?= htmlspecialchars($pr['email']) ?></strong></div>
                        <form method="POST" action="index.php?page=forgot_reset">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Mật khẩu mới</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Xác nhận mật khẩu</label>
                                <input type="password" name="password2" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-medium">Đặt mật khẩu</button>
                        </form>
                    <?php endif; ?>

                 
                    <script>
                        // toggle login password
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