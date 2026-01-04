<?php
$parentpage = $_GET['page'] ?? '';
// Layout chung cho employee dashboard

$current_page = substr($parentpage, strlen('employee_')) ?: 'dashboard';

$employee = $_SESSION['employee_account'] ?? null;
$employeeRole = $employee['role'] ?? 'nhanvien';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Employee Dashboard' ?> - BookZone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1e293b;
            --sidebar-active: #3b82f6;
            --bg-light: #f8fafc;
        }

        body {
            background-color: var(--bg-light);
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: white;
            padding: 0;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-header i {
            font-size: 1.5rem;
            color: var(--sidebar-active);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu li a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu li a.active {
            background: rgba(59, 130, 246, 0.2);
            color: white;
            border-left-color: var(--sidebar-active);
        }

        .sidebar-menu li a i {
            width: 20px;
            margin-right: 0.75rem;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
        }

        .main-content {
            background: var(--sidebar-bg);
            padding: 2rem 0.2rem;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: var(--sidebar-width);
            }
        }

        .top-bar {
            background: white;
            padding: 1rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-dropdown {
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
        }

        .notification-item:hover {
            background: #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: bold;
            margin: 0.5rem 0;
        }

        .stat-change {
            font-size: 0.875rem;
        }

        .table-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Responsive tweaks for table cards and search inputs */
        .table-card { padding: 1rem; }
        .table-card .form-control { width: 100% !important; max-width: 100% !important; }

        @media (min-width: 768px) {
            .table-card .form-control { max-width: 300px !important; width: 300px !important; }
        }

          /* Keep table headers visible on all devices and allow horizontal scrolling
              Rows remain horizontal (no wrap); long tables can be swiped horizontally. */
          @media (max-width: 575.98px) {
                .table thead { display: table-header-group; }
                .table tbody tr { display: table-row; }
                .table tbody td { padding: .5rem; white-space: nowrap; }
                .table th, .table td { vertical-align: middle; }
          }

        /* Offcanvas width and styles to match sidebar */
        .offcanvas.offcanvas-start {
            width: var(--sidebar-width);
            max-width: 90%;
        }

        .offcanvas .sidebar-header { padding: 1rem; }
        .offcanvas .sidebar-menu li a { padding: 0.75rem 1rem; }

        /* Responsive chart sizing */
        canvas { max-height: 420px; width: 100% !important; }

        @media (min-width: 1200px) {
            canvas { max-height: 700px; }
        }
    </style>
</head>

<body>
    <!-- Sidebar (visible on md and larger) -->
    <div class="sidebar d-none d-md-block">
        <div class="sidebar-header">
            <i class="fas fa-gem"></i>
            <span class="fw-bold">BookZone Employee</span>
        </div>

        <ul class="sidebar-menu">
            <?php if (in_array($employeeRole, ['admin', 'quanly'])): ?>
                <li>
                    <a href="index.php?page=employee_dashboard" class="<?= $current_page === 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-chart-line"></i>
                        <span>Tổng quan</span>
                    </a>
                </li>

                <li>
                    <a href="index.php?page=employee_products" class="<?= $current_page === 'products' ? 'active' : '' ?>">
                        <i class="fas fa-box"></i>
                        <span>Sản phẩm</span>
                    </a>
                </li>
            <?php endif; ?>

            <li>
                <a href="index.php?page=employee_orders" class="<?= $current_page === 'orders' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Đơn hàng</span>
                </a>
            </li>

            <?php
            // Promotions - Admin & Manager only
            if (in_array($employeeRole, ['admin', 'quanly'])):
            ?>
                <li>
                    <a href="index.php?page=employee_promotions" class="<?= $current_page === 'promotions' ? 'active' : '' ?>">
                        <i class="fas fa-tags"></i>
                        <span>Khuyến mãi</span>
                    </a>
                </li>
            <?php endif; ?>

            <li>
                <a href="index.php?page=employee_customers" class="<?= $current_page === 'customers' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Khách hàng</span>
                </a>
            </li>

            <?php
            // Employees - Admin & Manager only
            if (in_array($employeeRole, ['admin', 'quanly'])):
            ?>
                <li>
                    <a href="index.php?page=employee_employees" class="<?= $current_page === 'employees' ? 'active' : '' ?>">
                        <i class="fas fa-user-tie"></i>
                        <span>Nhân viên</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            // Publishers/Providers - Admin & Manager only
            if (in_array($employeeRole, ['admin', 'quanly'])):
            ?>
                <li>
                    <a href="index.php?page=employee_providers" class="<?= $current_page === 'providers' ? 'active' : '' ?>">
                        <i class="fas fa-truck"></i>
                        <span>Nhà cung cấp</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=employee_categories" class="<?= $current_page === 'categories' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i>
                        <span>Danh mục</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?page=employee_publishers" class="<?= $current_page === 'publishers' ? 'active' : '' ?>">
                        <i class="fas fa-book"></i>
                        <span>Nhà xuất bản</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            // Reports - Admin & Manager only
            if (in_array($employeeRole, ['admin', 'quanly'])):
            ?>
                <li>
                    <a href="index.php?page=employee_reports&range=7days" class="<?= $current_page === 'reports' ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span>Báo cáo</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            // if ($employeeRole === 'admin' ): 
            if (in_array($employeeRole, ['admin', 'quanly'])):
            ?>
                <li>
                    <a href="index.php?page=employee_settings" class="<?= $current_page === 'settings' ? 'active' : '' ?>">
                        <i class="fas fa-cog"></i>
                        <span>Cài đặt</span>
                    </a>
                </li>
            <?php endif; ?>



        </ul>

        <div class="sidebar-footer">
            <div class="d-flex align-items-center mb-2">
                <a href="?page=employee_profile" class="text-decoration-none text-white d-flex align-items-center w-100">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="ms-2 flex-grow-1">
                        <div class="fw-bold"><?= htmlspecialchars($employee['ho_ten'] ?? 'Employee') ?></div>
                        <small class="text-muted d-block"><?= htmlspecialchars($employee['email'] ?? '') ?></small>
                        <small class="badge bg-<?= ($employee['role'] ?? 'nhanvien') === 'admin' ? 'danger' : (($employee['role'] ?? 'nhanvien') === 'quanly' ? 'warning' : 'info') ?> mt-1">
                            <?= ($employee['role'] ?? 'nhanvien') === 'admin' ? 'Quản trị viên' : (($employee['role'] ?? 'nhanvien') === 'quanly' ? 'Quản lý' : 'Nhân viên') ?>
                        </small>
                    </div>
                </a>
            </div>
            <form method="POST" action="index.php?page=employee_logout" class="mt-2">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <button type="submit" class="btn btn-sm btn-outline-light w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                </button>
            </form>
        </div>
    </div>

    <!-- Offcanvas sidebar for small screens -->
    <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="employeeOffcanvas" aria-labelledby="employeeOffcanvasLabel">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title" id="employeeOffcanvasLabel">BookZone Employee</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="bg-dark text-white h-100">
                <div class="sidebar-header p-3">
                    <i class="fas fa-gem"></i>
                    <span class="fw-bold ms-2">BookZone Employee</span>
                </div>
                <ul class="sidebar-menu">
                    <?php
                    if (in_array($employeeRole, ['admin', 'quanly'])):
                    ?>
                        <li>
                            <a href="index.php?page=employee_dashboard" class="<?= $current_page === 'dashboard' ? 'active' : '' ?>">
                                <i class="fas fa-chart-line"></i>
                                <span>Tổng quan</span>
                            </a>
                        </li>

                        <li>
                            <a href="index.php?page=employee_products" class="<?= $current_page === 'products' ? 'active' : '' ?>">
                                <i class="fas fa-box"></i>
                                <span>Sản phẩm</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <a href="index.php?page=employee_orders" class="<?= $current_page === 'orders' ? 'active' : '' ?>">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Đơn hàng</span>
                        </a>
                    </li>
                    
                     <?php
                    if (in_array($employeeRole, ['admin', 'quanly'])):
                    ?>
                        <li>
                            <a href="index.php?page=employee_promotions" class="<?= $current_page === 'promotions' ? 'active' : '' ?>">
                                <i class="fas fa-tags"></i>
                                <span>Khuyến mãi</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <a href="index.php?page=employee_customers" class="<?= $current_page === 'customers' ? 'active' : '' ?>">
                            <i class="fas fa-users"></i>
                            <span>Khách hàng</span>
                        </a>
                    </li>

                    <?php
                    if (in_array($employeeRole, ['admin', 'quanly'])):
                    ?>
                        <li>
                            <a href="index.php?page=employee_employees" class="<?= $current_page === 'employees' ? 'active' : '' ?>">
                                <i class="fas fa-user-tie"></i>
                                <span>Nhân viên</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    if (in_array($employeeRole, ['admin', 'quanly'])):
                    ?>
                        <li>
                            <a href="index.php?page=employee_providers" class="<?= $current_page === 'providers' ? 'active' : '' ?>">
                                <i class="fas fa-truck"></i>
                                <span>Nhà cung cấp</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=employee_categories" class="<?= $current_page === 'categories' ? 'active' : '' ?>">
                                <i class="fas fa-list"></i>
                                <span>Danh mục</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?page=employee_publishers" class="<?= $current_page === 'publishers' ? 'active' : '' ?>">
                                <i class="fas fa-book"></i>
                                <span>Nhà xuất bản</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    if (in_array($employeeRole, ['admin', 'quanly'])):
                    ?>
                        <li>
                            <a href="index.php?page=employee_reports&range=7days" class="<?= $current_page === 'reports' ? 'active' : '' ?>">
                                <i class="fas fa-chart-bar"></i>
                                <span>Báo cáo</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    if (in_array($employeeRole, ['admin', 'quanly'])):
                    ?>
                        <li>
                            <a href="index.php?page=employee_settings" class="<?= $current_page === 'settings' ? 'active' : '' ?>">
                                <i class="fas fa-cog"></i>
                                <span>Cài đặt</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="sidebar-footer p-3">
                    <div class="d-flex align-items-center mb-2">
                        <a href="?page=employee_profile" class="text-decoration-none text-white d-flex align-items-center w-100">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="ms-2 flex-grow-1">
                                <div class="fw-bold"><?= htmlspecialchars($employee['ho_ten'] ?? 'Employee') ?></div>
                                <small class="text-muted d-block"><?= htmlspecialchars($employee['email'] ?? '') ?></small>
                                <small class="badge bg-<?= ($employee['role'] ?? 'nhanvien') === 'admin' ? 'danger' : (($employee['role'] ?? 'nhanvien') === 'quanly' ? 'warning' : 'info') ?> mt-1">
                                    <?= ($employee['role'] ?? 'nhanvien') === 'admin' ? 'Quản trị viên' : (($employee['role'] ?? 'nhanvien') === 'quanly' ? 'Quản lý' : 'Nhân viên') ?>
                                </small>
                            </div>
                        </a>
                    </div>
                    <form method="POST" action="index.php?page=employee_logout" class="mt-2">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <button type="submit" class="btn btn-sm btn-outline-light w-100">
                            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tran nội dung chính -->
    <div class="main-content bg-light" style="min-height: 100vh;">
        <!-- Mobile header: toggle sidebar -->
        <div class="d-flex d-md-none align-items-center mb-3">
            <button class="btn btn-primary me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#employeeOffcanvas" aria-controls="employeeOffcanvas">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0"><?= htmlspecialchars($page_title ?? 'Dashboard') ?></h5>
        </div>

        <div class="container-fluid">
            <?php
          
            if (!empty($service_status['success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo htmlspecialchars($service_status['success']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                echo '</div>';
            }
            if (!empty($service_status['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo htmlspecialchars($service_status['error']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                echo '</div>';
            }
            ?>
        </div>

        <div class="container-fluid">
            <?= $content ?? '' ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable swipe-to-reveal actions on employee tables for small screens
        (function() {
            function enableSwipeTables() {
                if (window.innerWidth > 575.98) return;
                document.querySelectorAll('.main-content .table').forEach(function(table) {
                    if (table.classList.contains('swipe-enabled')) return;
                    // collect headers
                    var headers = Array.from(table.querySelectorAll('thead th')).map(function(h){ return h.textContent.trim(); });
                    // add data-labels from headers
                    table.querySelectorAll('tbody tr').forEach(function(tr){
                        Array.from(tr.children).forEach(function(td, idx){
                            if (!td.hasAttribute('data-label')) td.setAttribute('data-label', headers[idx] || '');
                        });
                    });

                    // mark table as swipe-enabled
                    table.classList.add('swipe-enabled');

                    // locate action cell index (heuristic: last column)
                    var actionIndex = Math.max(0, headers.length - 1);

                    table.querySelectorAll('tbody tr').forEach(function(tr){
                        // add data-labels (already done above) and ensure the last cell uses swipe-actions
                        var tds = tr.querySelectorAll('td');
                        if (tds.length > 0) {
                            var last = tds[tds.length - 1];
                            last.classList.add('swipe-actions');
                        }
                        // remove touch-driven translate behavior to avoid overlapping layout on many devices
                        // If we want to re-enable swipe interactions in future, implement non-destructive highlighting instead.
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', enableSwipeTables);
            window.addEventListener('resize', function(){ setTimeout(enableSwipeTables, 120); });
        })();
    </script>
</body>

</html>