<?php
requireEmployeeLogin();

// Use preparer when available; fallback to legacy logic
$subpage = $_GET['subpage'] ?? 'list';
$vars = EmployeePageController::prepareEmployeeCustomers($subpage, isset($_GET['id']) ? (int)$_GET['id'] : null);
if (is_array($vars)) extract($vars);

$page_title = 'Quản lý Khách hàng';
ob_start();
?>

<div class="top-bar">
    <h2 class="mb-0">Quản lý Khách hàng</h2>
</div>

<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Danh sách khách hàng</h4>
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
                    <th>Địa chỉ</th>
                    <th>Ngày tham gia</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?= $customer['khachhang_id'] ?></td>
                    <td>
                        <?= htmlspecialchars(trim(($customer['ho'] ?? '') . ' ' . ($customer['tendem'] ?? '') . ' ' . ($customer['ten'] ?? ''))) ?>
                    </td>
                    <td><?= htmlspecialchars($customer['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($customer['sdt'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($customer['diachi'] ?? 'N/A') ?></td>
                    <td><?= $customer['ngaythamgia'] ? date('d/m/Y', strtotime($customer['ngaythamgia'])) : 'N/A' ?></td>
                    <td class="swipe-actions">
                        <a href="?page=employee_customers&subpage=view&id=<?= $customer['khachhang_id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
        <?= render_pagination_controls($pagination, 'employee_customers') ?>
</div>

<?php
$content = ob_get_clean();
include 'app/views/employee/employeeLayout.php';
?>

