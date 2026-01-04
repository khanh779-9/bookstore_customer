<?php
requireEmployeeLogin();

// Use preparer when available; fallback to legacy logic
$employee_id = $_SESSION['employee_account']['id'] ?? null;
$vars = EmployeePageController::prepareEmployeeProfile($employee_id);
if (is_array($vars)) extract($vars);
// Ensure defaults so view won't error when preparer returns null/partial data
if (!isset($subpage)) $subpage = 'view';
if (!isset($csrf_token)) $csrf_token = '';
if (!isset($employee_data) || !is_array($employee_data)) $employee_data = [];
if (empty($employee_id)) $employee_id = $employee_data['nhanvien_id'] ?? ($_SESSION['employee_account']['id'] ?? null);
?>

<div class="top-bar">
    <h2 class="mb-0">Thông tin cá nhân</h2>
    <a href="index.php?page=employee_employees&subpage=edit&id=<?= htmlspecialchars($employee_id) ?>" class="btn btn-primary">
        <i class="fas fa-edit me-2"></i>Sửa thông tin
    </a>
</div>

<?php if ($subpage === 'edit'): ?>

<?php else: ?>
   
    <!-- Xem thông tin -->
    <div class="row">
        <div class="col-md-8">
            <div class="table-card">
                <h4 class="mb-4">Thông tin cá nhân</h4>

                <dl class="row">
                    <dt class="col-sm-4">Mã nhân viên:</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($employee_data['nhanvien_id'] ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Họ tên:</dt>
                    <dd class="col-sm-8">
                        <?= htmlspecialchars(trim(($employee_data['ho'] ?? '') . ' ' . ($employee_data['tendem'] ?? '') . ' ' . ($employee_data['ten'] ?? ''))) ?>
                    </dd>

                    <dt class="col-sm-4">Email:</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($employee_data['email'] ?? '') ?></dd>

                    <dt class="col-sm-4">Số điện thoại:</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($employee_data['sdt'] ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Giới tính:</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($employee_data['gioitinh'] ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Ngày sinh:</dt>
                    <dd class="col-sm-8"><?= !empty($employee_data['ngaysinh']) ? date('d/m/Y', strtotime($employee_data['ngaysinh'])) : 'N/A' ?></dd>

                    <dt class="col-sm-4">Địa chỉ:</dt>
                    <dd class="col-sm-8"><?= htmlspecialchars($employee_data['diachi'] ?? 'N/A') ?></dd>

                    <dt class="col-sm-4">Ngày vào làm:</dt>
                    <dd class="col-sm-8"><?= !empty($employee_data['ngayvaolam']) ? date('d/m/Y', strtotime($employee_data['ngayvaolam'])) : 'N/A' ?></dd>

                    <dt class="col-sm-4">Vai trò:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-<?= ($employee_data['role'] ?? '') === 'admin' ? 'danger' : (($employee_data['role'] ?? '') === 'quanly' ? 'warning' : 'info') ?>">
                            <?= ($employee_data['role'] ?? '') === 'admin' ? 'Quản trị viên' : (($employee_data['role'] ?? '') === 'quanly' ? 'Quản lý' : 'Nhân viên') ?>
                        </span>
                    </dd>

                    <dt class="col-sm-4">Trạng thái:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-<?= ($employee_data['trangthai'] ?? '') === 'dang_lam' ? 'success' : (($employee_data['trangthai'] ?? '') === 'tam_nghi' ? 'warning' : 'secondary') ?>">
                            <?= ($employee_data['trangthai'] ?? '') === 'dang_lam' ? 'Đang làm việc' : (($employee_data['trangthai'] ?? '') === 'tam_nghi' ? 'Tạm nghỉ' : 'Nghỉ việc') ?>
                        </span>
                    </dd>

                    <?php if (!empty($employee_data['ghichu'])): ?>
                        <dt class="col-sm-4">Ghi chú:</dt>
                        <dd class="col-sm-8"><?= nl2br(htmlspecialchars($employee_data['ghichu'])) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <div class="col-md-4">
            <div class="table-card">
                <h5 class="mb-3">Thống kê</h5>
                <div class="mb-3">
                    <div class="text-muted small">Đơn hàng đã xử lý</div>
                    <div class="fs-4 fw-bold text-primary">
                        <?= number_format(EmployeeModel::getEmployeeOrderCount($employee_id), 0, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/employeeLayout.php';
?>