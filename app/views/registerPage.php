<?php
// Error message is now transferred from $_SESSION to $service_status in index.php
?>
<div class="container mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <h4 class="mb-3">Đăng ký tài khoản</h4>
                <?php if ($service_status['error'] ?? false): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($service_status['error']) ?></div>
                <?php endif; ?>
                <?php if ($service_status['success'] ?? false): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($service_status['success']) ?></div>
                <?php endif; ?>
                <form method="post" action="index.php?page=register">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input class="form-control" name="ho" placeholder="Họ" required>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" name="tendem" placeholder="Tên đệm">
                        </div>
                        <div class="col-md-4">
                            <input class="form-control" name="ten" placeholder="Tên" required>
                        </div>
                    </div>
                    <div class="mt-2">
                        <input class="form-control" name="email" type="email" placeholder="Email" required>
                    </div>
                    <div class="mt-2 position-relative">
                        <input id="reg_password" class="form-control" name="password" type="password" placeholder="Mật khẩu" required>
                        <i id="reg_toggle" class="bi bi-eye-fill position-absolute" style="right:12px;top:10px;cursor:pointer"></i>
                    </div>
                    <div class="mt-2 position-relative">
                        <input id="reg_password2" class="form-control" name="password2" type="password" placeholder="Nhập lại mật khẩu" required>
                        <i id="reg_toggle2" class="bi bi-eye-fill position-absolute" style="right:12px;top:10px;cursor:pointer"></i>
                    </div>
                    <div class="mt-2">
                        <input class="form-control" name="sdt" placeholder="Số điện thoại">
                    </div>
                    <div class="mt-2">
                        <input class="form-control" name="diachi" placeholder="Địa chỉ">
                    </div>
                    <div class="mt-3 d-flex justify-content-between">
                        <a href="index.php?page=login" class="btn btn-outline-secondary">Đã có tài khoản</a>
                        <button class="btn btn-primary">Đăng ký</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // toggle show/hide password
    function toggle(id, iconId) {
        const ip = document.getElementById(id);
        const ic = document.getElementById(iconId);
        if (!ip || !ic) return;
        ic.addEventListener('click', () => {
            if (ip.type === 'password') {
                ip.type = 'text';
                ic.className = 'bi bi-eye-slash-fill position-absolute';
            } else {
                ip.type = 'password';
                ic.className = 'bi bi-eye-fill position-absolute';
            }
        });
    }
    toggle('reg_password', 'reg_toggle');
    toggle('reg_password2', 'reg_toggle2');
</script>