<?php

/**
 * Employee Settings View
 * 
 * Hiển thị cài đặt hệ thống
 */

requireEmployeeLogin();

// Use controller preparer
$vars = EmployeePageController::prepareEmployeeSettings();
if (is_array($vars)) extract($vars);

$page_title = 'Cài đặt hệ thống';
ob_start();
?>

<div class="top-bar">
    <h2 class="mb-0"><i class="bi bi-gear me-2"></i>Cài đặt hệ thống</h2>
</div>


<!-- System Settings Overview -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">

        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1 small"><i class="bi bi-people"></i> Tổng nhân viên</p>
                <h3 class="mb-0 fw-bold"><?= count($allEmployees) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1 small"><i class="bi bi-gear"></i> Phiên bản</p>
                <h3 class="mb-0 fw-bold">1.0.0</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1 small"><i class="bi bi-database"></i> Database</p>
                <h3 class="mb-0 fw-bold">Active</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1 small"><i class="bi bi-clock"></i> Thời gian</p>
                <h3 class="mb-0 fw-bold"><?= date('H:i:s') ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Employee Management -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-people-fill"></i> Quản lý nhân viên</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mã NV</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Chức vụ</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allEmployees as $emp): ?>
                        <tr>
                            <td><strong><?= $emp['nhanvien_id'] ?></strong></td>
                            <td><?= htmlspecialchars(trim(($emp['ho'] ?? '') . ' ' . ($emp['ten'] ?? ''))) ?></td>
                            <td><?= htmlspecialchars($emp['email'] ?? '') ?></td>
                            <td>
                                <span class="badge bg-<?= $emp['role'] === 'admin' ? 'danger' : ($emp['role'] === 'quanly' ? 'warning' : 'secondary') ?>">
                                    <?= ucfirst($emp['role']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $emp['trangthai'] === 'dang_lam' ? 'success' : 'secondary' ?>">
                                    <?= $emp['trangthai'] === 'dang_lam' ? 'Đang làm' : 'Đã nghỉ' ?>
                                </span>
                            </td>
                            <td class="swipe-actions">
                                <a href="?page=employee_employees&subpage=edit&id=<?= $emp['nhanvien_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <a href="?page=employee_employees&subpage=create" class="btn btn-primary">
                <i class="bi bi-plus me-2"></i>Thêm nhân viên
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/employeeLayout.php';
