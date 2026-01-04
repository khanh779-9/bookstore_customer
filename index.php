<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (file_exists(__DIR__ . '/app/core/Autoloader.php')) {
    require_once __DIR__ . '/app/core/Autoloader.php';
}

$service_status = [];

$pdo = Database::getInstance();

$web_product_title = 'BookZone';

$customer = $_SESSION['khachhang_account'] ?? [];

$essentialControllers = [
    // Load auth helper early with correct case to avoid failures on case-sensitive hosts
    'app/controllers/AuthController.php',
    'app/helpers.php'
];

$models = [
    'app/models/categories.php',
    'app/models/authors.php',
    'app/models/product.php',
    'app/models/reviews.php',
    'app/models/notification.php',
    'app/models/employee_helpers.php',
    'app/models/publisher.php',
    'app/models/provider.php',
    'app/models/customer.php',
    'app/models/cart.php',
    'app/models/wishlist.php',
    'app/models/employee.php',
    'app/models/addresses.php',
    'app/models/units.php',
    'app/models/orders.php',
    'app/models/loaisach.php',
    'app/models/promotions.php'
];

$optionalControllers = [
    'app/controllers/EmployeeAuthController.php',
    'app/controllers/EmployeePageController.php',
    'app/controllers/PageController.php',
    'app/controllers/CustomerActionController.php',
    'app/controllers/EmployeeActionController.php'
];

foreach (array_merge($essentialControllers, $models, $optionalControllers) as $file) {
    if (file_exists($file)) {
        include_once $file;
    }
}

if (file_exists(__DIR__ . '/app/legacy_wrappers.php')) {
    include_once __DIR__ . '/app/legacy_wrappers.php';
}


if (!isset($pdo) || !$pdo) {
    include 'app/views/services_status/statusPage.php';
    exit;
}


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$headerVars = PageController::prepareHeader();
if (is_array($headerVars)) extract($headerVars);

if (!empty($_SESSION['error'])) {
    $service_status['error'] = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (!empty($_SESSION['success'])) {
    $service_status['success'] = $_SESSION['success'];
    unset($_SESSION['success']);
}

function isEmployeePage($page)
{
    return strpos($page, 'employee_') === 0;
}


function prepareProductData($postData)
{
    $danhmucSP_id = (int)($postData['danhmucSP_id'] ?? 1);
    // Use helper for category -> is_sach
    $is_sach = function_exists('is_book_category') ? is_book_category($danhmucSP_id) : ((int)$danhmucSP_id === 1);

    $data = [
        'danhmucSP_id' => $danhmucSP_id,
        'hinhanh' => $postData['hinhanh'] ?? null,
        'mo_ta' => $postData['mo_ta'] ?? null,
        'soluongton' => (int)($postData['soluongton'] ?? 0),
        'donvitinh_id' => 1,
        'soluongban' => 0,
        'gia' => (float)($postData['gia'] ?? 0),
        'nhacungcap_id' => (int)($postData['nhacungcap_id'] ?? 1)
    ];

    if ($is_sach) {
        $data['tenSach'] = $postData['tenSach'] ?? ($postData['name'] ?? '');
        $data['nhaxuatban_id'] = !empty($postData['nhaxuatban_id']) ? (int)$postData['nhaxuatban_id'] : null;
        $data['tacgia_id'] = !empty($postData['tacgia_id']) ? (int)$postData['tacgia_id'] : null;
        $data['loaisach_code'] = $postData['loaisach_code'] ?? null;
        $data['namXB'] = !empty($postData['namXB']) ? (int)$postData['namXB'] : null;
    } else {
        $data['tenVPP'] = $postData['tenVPP'] ?? ($postData['name'] ?? '');
    }

    return ['data' => $data, 'is_sach' => $is_sach];
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $page = $_GET['page'] ?? '';
    $action = $_GET['action'] ?? '';

    if (isEmployeePage($page)) {
        routeEmployeeActions($page, $action);
    } else {
        routeCustomerActions($page);
    }
}

$page = $_GET['page'] ?? 'home';
$isEmployee = isEmployeePage($page);

// Handle Google OAuth callback
if ($page === 'login' && isset($_GET['action']) && $_GET['action'] === 'google_callback' && isset($_GET['code'])) {
    $authCtl = new AuthController();
    $authCtl->loginWithGoogle();
    exit;
}

// Simple JSON API endpoints (clean, paginated product list)
if ($page === 'api_products') {
    if (!class_exists('ProductModel')) {
        // ensure models were included earlier; attempt to include fallback
        @include_once __DIR__ . '/app/models/product.php';
    }
    require_once __DIR__ . '/app/controllers/ApiController.php';
    ApiController::products();
    exit;
}
if ($page === 'api_providers') {
    @include_once __DIR__ . '/app/models/provider.php';
    require_once __DIR__ . '/app/controllers/ApiController.php';
    ApiController::providers();
    exit;
}
if ($page === 'api_publishers') {
    @include_once __DIR__ . '/app/models/publisher.php';
    require_once __DIR__ . '/app/controllers/ApiController.php';
    ApiController::publishers();
    exit;
}
if ($page === 'api_customers') {
    @include_once __DIR__ . '/app/models/customer.php';
    require_once __DIR__ . '/app/controllers/ApiController.php';
    ApiController::customers();
    exit;
}
if ($page === 'api_employees') {
    @include_once __DIR__ . '/app/models/employee.php';
    require_once __DIR__ . '/app/controllers/ApiController.php';
    ApiController::employees();
    exit;
}

// API endpoint for searching products (used in promotions page)
if ($page === 'api' && isset($_GET['action']) && $_GET['action'] === 'search_products') {
    @include_once __DIR__ . '/app/models/promotions.php';
    require_once __DIR__ . '/app/controllers/ApiController.php';
    ApiController::searchProducts();
    exit;
}

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($web_product_title . (!$isEmployee ? ' - Nhà sách BookZone' : ' - Quản lý BookZone')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="./public/css/style.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">
    <?php if (!$isEmployee): ?>
        <?php include 'app/views/layouts/header.php'; ?>
    <?php endif; ?>

    <main class="flex-grow-1 bg-light">
        <?php
        if ($isEmployee) {
            $employeePages = [
                'employee_login' => 'app/views/employee/employeeLogin.php',
                'employee_dashboard' => 'app/views/employee/employeeDashboard.php',
                'employee_products' => 'app/views/employee/employeeProducts.php',
                'employee_orders' => 'app/views/employee/employeeOrders.php',
                'employee_customers' => 'app/views/employee/employeeCustomers.php',
                'employee_employees' => 'app/views/employee/employeeEmployees.php',
                'employee_profile' => 'app/views/employee/employeeProfile.php',
                'employee_reports' => 'app/views/employee/employeeReports.php',
                'employee_settings' => 'app/views/employee/employeeSettings.php',
                'employee_publishers' => 'app/views/employee/employeePublishers.php',
                'employee_providers' => 'app/views/employee/employeeProviders.php',
                'employee_categories' => 'app/views/employee/employeeCategories.php',
                'employee_promotions' => 'app/views/employee/employeePromotions.php'
            ];

            $employeePageRoles = [
                'employee_dashboard' => ['admin', 'quanly'],
                'employee_products' => ['admin', 'quanly'],
                'employee_orders' => ['admin', 'quanly', 'nhanvien'],
                'employee_customers' => ['admin', 'quanly', 'nhanvien'],
                'employee_employees' => ['admin'],
                'employee_profile' => ['admin', 'quanly', 'nhanvien'],
                'employee_reports' => ['admin', 'quanly'],
                'employee_settings' => ['admin'],
                'employee_publishers' => ['admin', 'quanly'],
                'employee_providers' => ['admin', 'quanly'],
                'employee_categories' => ['admin', 'quanly'],
                'employee_promotions' => ['admin', 'quanly']
            ];

            if (isset($employeePages[$page])) {

                if ($page !== 'employee_login' && $page !== 'employee_logout') {
                    $requiredRoles = $employeePageRoles[$page] ?? ['admin', 'quanly', 'nhanvien'];
                    // Nếu không đủ quyền, đưa về trang đơn hàng (tránh vòng lặp dashboard)
                    EmployeeAuthController::enforceRole($requiredRoles, 'index.php?page=employee_orders');
                }

                $employeePreparers = [
                    'employee_dashboard' => 'prepareEmployeeDashboard',
                    'employee_products' => 'prepareEmployeeProducts',
                    'employee_orders' => 'prepareEmployeeOrders',
                    'employee_customers' => 'prepareEmployeeCustomers',
                    'employee_employees' => 'prepareEmployeeEmployees',
                    'employee_profile' => 'prepareEmployeeProfile',
                    'employee_reports' => 'prepareEmployeeReports',
                    'employee_settings' => 'prepareEmployeeSettings',
                    'employee_publishers' => 'prepareEmployeePublishers',
                    'employee_providers' => 'prepareEmployeeProviders',
                    'employee_categories' => 'prepareEmployeeCategories',
                    'employee_promotions' => 'prepareEmployeePromotions'
                ];

                if (isset($employeePreparers[$page])) {
                    $prep = $employeePreparers[$page];

                    $vars = (function ($page, $prep) {
                        switch ($page) {
                            case 'employee_dashboard':
                                return $prep($_GET['period'] ?? 'month');
                            case 'employee_products':
                            case 'employee_orders':
                            case 'employee_customers':
                            case 'employee_employees':
                            case 'employee_publishers':
                            case 'employee_providers':
                            case 'employee_categories':
                            case 'employee_promotions':
                                return $prep($_GET['subpage'] ?? 'list', isset($_GET['id']) ? (int)$_GET['id'] : null);
                            case 'employee_profile':
                                $eid = $_SESSION['employee_account']['id'] ?? null;
                                return $prep($eid);
                            case 'employee_reports':
                                return $prep($_GET['start_date'] ?? null, $_GET['end_date'] ?? null);
                            case 'employee_settings':
                                return $prep();
                            default:
                                return [];
                        }
                    })($page, $prep);

                    if (is_array($vars)) extract($vars);
                }

                include $employeePages[$page];
            } else {
                redirect('index.php?page=employee_login');
            }
        } else {
            // Các trang dành cho khách hàng
            $customerPages = [
                'cart' => 'app/views/cartPage.php',
                'login' => 'app/views/loginPage.php',
                'register' => 'app/views/registerPage.php',
                'forgot_resetPass' => 'app/views/forgot_resetPass.php',
                'bookview' => 'app/views/bookview.php',
                'checkout' => 'app/views/checkout.php',
                'productview' => 'app/views/productview.php',
                'products' => 'app/views/products.php',
                'contact' => 'app/views/contact.php',
                'orders' => 'app/views/orders.php',
                'about' => 'app/views/about.php',
                'account' => 'app/views/customerPage.php',
                'notifications' => 'app/views/notifications.php',
                'return_policy' => 'app/views/return_policy.php',
                'warranty_policy' => 'app/views/warranty_policy.php',
                'shipping_delivery' => 'app/views/shipping_delivery.php',
                'privacy_policy' => 'app/views/privacy_policy.php'
            ];

            if (isset($customerPages[$page])) {
                // Gọi các hàm chuẩn bị dữ liệu cho trang khi có sẵn (giữ cho view gọn nhẹ)
                $id = (int)($_GET['id'] ?? 0);
                $customerPreparers = [
                    'products' => fn() => PageController::prepareProductsPage(),
                    'productview' => fn() => ($id > 0) ? PageController::prepareProductViewPage($id) : [],
                    'cart' => fn() => PageController::prepareCart(),
                    'checkout' => fn() => PageController::prepareCheckout(),
                    'account' => fn() => PageController::prepareAccount(),
                    'notifications' => fn() => PageController::prepareNotifications(),
                    'home' => fn() => PageController::prepareHome()
                ];

                if (isset($customerPreparers[$page])) {
                    $vars = $customerPreparers[$page]();
                    if (is_array($vars)) extract($vars);
                }

                include $customerPages[$page];
            } else {
                // Mặc định về trang chủ và chuẩn bị dữ liệu cho nó
                $vars = PageController::prepareHome();
                if (is_array($vars)) extract($vars);
                include 'app/views/home.php';
            }
        }
        ?>
    </main>

    <?php if (!$isEmployee): ?>
        <?php include 'app/views/layouts/footer.php'; ?>
    <?php endif; ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</html>