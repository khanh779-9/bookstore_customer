<?php
requireEmployeeLogin();
requireEmployeeOrManager(); // Chỉ admin và quản lý mới được truy cập

// Use preparer when available; fallback to legacy logic
$subpage = $_GET['subpage'] ?? 'list';
$employee_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$vars = EmployeePageController::prepareEmployeeEmployees($subpage, $employee_id);
if (is_array($vars)) extract($vars);

$employeeRole = $_SESSION['employee_account']['role'] ?? 'nhanvien';
$employees = EmployeeModel::getAllEmployees();
$page_title = 'Quản lý Nhân viên';
ob_start();
?>

<div class="top-bar">
    <h2 class="mb-0">Quản lý Nhân viên</h2>
    <?php if ($employeeRole === 'admin'): ?>
        <div class="d-flex gap-2">
            <a href="?page=employee_employees&subpage=add" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm nhân viên mới
            </a>
        </div>
    <?php endif; ?>
</div>

<?php if ($subpage === 'add' || $subpage === 'edit'): ?>
    <!-- Form thêm/sửa nhân viên -->
    <?php
    $employee_id = $_GET['id'] ?? null;
    $employee_data = null;
    if ($employee_id && $subpage === 'edit') {
        $employee_data = EmployeeModel::getEmployeeById($employee_id);
        // Chỉ admin mới được sửa, hoặc nhân viên tự sửa thông tin của mình
        if ($employeeRole !== 'admin' && $employee_data['nhanvien_id'] != $_SESSION['employee_account']['id']) {
            $_SESSION['error'] = 'Bạn không có quyền sửa thông tin nhân viên này.';
            redirect('index.php?page=employee_employees');
        }
    }
    ?>
    <div class="table-card">
        <h4><?= $subpage === 'add' ? 'Thêm' : 'Sửa' ?> Nhân viên</h4>
        <form method="POST" action="index.php?page=employee_employees&action=<?= $subpage === 'add' ? 'save' : 'update' ?><?= $employee_id ? '&id=' . $employee_id : '' ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Họ <span class="text-danger">*</span></label>
                    <input type="text" name="ho" class="form-control" value="<?= htmlspecialchars($employee_data['ho'] ?? '') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tên đệm</label>
                    <input type="text" name="tendem" class="form-control" value="<?= htmlspecialchars($employee_data['tendem'] ?? '') ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tên <span class="text-danger">*</span></label>
                    <input type="text" name="ten" class="form-control" value="<?= htmlspecialchars($employee_data['ten'] ?? '') ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($employee_data['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($employee_data['sdt'] ?? '') ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Giới tính</label>
                    <select name="gioitinh" class="form-select">
                        <option value="">Chọn giới tính</option>
                        <option value="Nam" <?= ($employee_data['gioitinh'] ?? '') === 'Nam' ? 'selected' : '' ?>>Nam</option>
                        <option value="Nu" <?= ($employee_data['gioitinh'] ?? '') === 'Nu' ? 'selected' : '' ?>>Nữ</option>
                        <option value="Khac" <?= ($employee_data['gioitinh'] ?? '') === 'Khac' ? 'selected' : '' ?>>Khác</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" name="ngaysinh" class="form-control" value="<?= $employee_data['ngaysinh'] ?? '' ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ngày vào làm</label>
                    <input type="date" name="ngayvaolam" class="form-control" value="<?= $employee_data['ngayvaolam'] ?? date('Y-m-d') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Địa chỉ</label>
                <input type="text" name="diachi" class="form-control" value="<?= htmlspecialchars($employee_data['diachi'] ?? '') ?>">
            </div>

            <?php if ($employeeRole === 'admin'): ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="nhanvien" <?= ($employee_data['role'] ?? 'nhanvien') === 'nhanvien' ? 'selected' : '' ?>>Nhân viên</option>
                            <option value="quanly" <?= ($employee_data['role'] ?? '') === 'quanly' ? 'selected' : '' ?>>Quản lý</option>
                            <option value="admin" <?= ($employee_data['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="trangthai" class="form-select" required>
                            <option value="dang_lam" <?= ($employee_data['trangthai'] ?? 'dang_lam') === 'dang_lam' ? 'selected' : '' ?>>Đang làm việc</option>
                            <option value="tam_nghi" <?= ($employee_data['trangthai'] ?? '') === 'tam_nghi' ? 'selected' : '' ?>>Tạm nghỉ</option>
                            <option value="nghi_viec" <?= ($employee_data['trangthai'] ?? '') === 'nghi_viec' ? 'selected' : '' ?>>Nghỉ việc</option>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($subpage === 'edit'): ?>
                <div class="mb-3">
                    <label class="form-label">Đổi mật khẩu (để trống nếu không đổi)</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới">
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu (mặc định: 123456)</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu (mặc định: 123456)">
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Ghi chú</label>
                <textarea name="ghichu" class="form-control" rows="3"><?= htmlspecialchars($employee_data['ghichu'] ?? '') ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Lưu
                </button>
                <a href="?page=employee_employees" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
<?php else: ?>

    <!-- Danh sách nhân viên -->
    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Danh sách nhân viên</h4>
            <input type="text" class="form-control" placeholder="Tìm kiếm..." style="width: 300px;" id="searchInput">
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày vào làm</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td><?= $emp['nhanvien_id'] ?></td>
                            <td>
                                <?= htmlspecialchars(trim(($emp['ho'] ?? '') . ' ' . ($emp['tendem'] ?? '') . ' ' . ($emp['ten'] ?? ''))) ?>
                            </td>
                            <td><?= htmlspecialchars($emp['email'] ?? '') ?></td>
                            <td><?= htmlspecialchars($emp['sdt'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge bg-<?= $emp['role'] === 'admin' ? 'danger' : ($emp['role'] === 'quanly' ? 'warning' : 'info') ?>">
                                    <?= $emp['role'] === 'admin' ? 'Quản trị viên' : ($emp['role'] === 'quanly' ? 'Quản lý' : 'Nhân viên') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $emp['trangthai'] === 'dang_lam' ? 'success' : ($emp['trangthai'] === 'tam_nghi' ? 'warning' : 'secondary') ?>">
                                    <?= $emp['trangthai'] === 'dang_lam' ? 'Đang làm' : ($emp['trangthai'] === 'tam_nghi' ? 'Tạm nghỉ' : 'Nghỉ việc') ?>
                                </span>
                            </td>
                            <td><?= $emp['ngayvaolam'] ? date('d/m/Y', strtotime($emp['ngayvaolam'])) : 'N/A' ?></td>
                            <td class="swipe-actions">
                                <?php if ($employeeRole === 'admin' || $emp['nhanvien_id'] == $_SESSION['employee_account']['id']): ?>
                                    <a href="?page=employee_employees&subpage=edit&id=<?= $emp['nhanvien_id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($employeeRole === 'admin' && $emp['nhanvien_id'] != $_SESSION['employee_account']['id']): ?>
                                    <form method="POST" action="index.php?page=employee_employees&action=delete" style="display:inline-block; margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                        <input type="hidden" name="id" value="<?= $emp['nhanvien_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/employeeLayout.php';
?>