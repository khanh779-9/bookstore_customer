<?php

class EmployeePageController
{
    public static function prepareEmployeeDashboard(string $period = 'month'): array
    {
        $period = in_array($period, ['week', 'month', 'year'], true) ? $period : 'month';

        $stats = EmployeeHelpers::getDashboardStats($period);
        $prevStats = EmployeeHelpers::getPreviousPeriodStats($period);
        $topProducts = EmployeeHelpers::getTopSellingProducts(5);
        $recentOrders = EmployeeHelpers::getRecentOrders(10);
        $orderStats = EmployeeHelpers::getOrderStatistics();
        $customerStats = EmployeeHelpers::getCustomerStatistics();
        $lowStockAlerts = EmployeeHelpers::getLowStockProducts(10);
        $slowMovingProducts = EmployeeHelpers::getSlowMovingProducts(10, 30);
        $revenueSummary = EmployeeHelpers::getRevenueSummary($period);

        if (true) {
            $startDate = match ($period) {
                'week' => date('Y-m-d', strtotime('-7 days')),
                'year' => date('Y-01-01'),
                default => date('Y-m-01')
            };
            $endDate = date('Y-m-d');
            $revenueByDate = EmployeeHelpers::getRevenueByDateRange($startDate, $endDate) ?? [];
        }

        $totalRevenue = $stats['revenue'] ?? 0;
        $totalOrders = $stats['orders'] ?? 0;
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $prevRevenue = $prevStats['revenue'] ?? 0;
        $prevOrders = $prevStats['orders'] ?? 0;
        $revenueChange = isset($stats['revenue'], $prevStats['revenue']) && $prevStats['revenue'] > 0 ?
            round((($stats['revenue'] - $prevStats['revenue']) / $prevStats['revenue']) * 100, 1) : 0;
        $ordersChange = isset($stats['orders'], $prevStats['orders']) && $prevStats['orders'] > 0 ?
            round((($stats['orders'] - $prevStats['orders']) / $prevStats['orders']) * 100, 1) : 0;

        return [
            'period' => $period,
            'stats' => $stats,
            'prevStats' => $prevStats,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'orderStats' => $orderStats,
            'customerStats' => $customerStats,
            'lowStockAlerts' => $lowStockAlerts,
            'slowMovingProducts' => $slowMovingProducts,
            'revenueSummary' => $revenueSummary,
            'revenueByDate' => $revenueByDate,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'avgOrderValue' => $avgOrderValue,
            'prevRevenue' => $prevRevenue,
            'prevOrders' => $prevOrders,
            'revenueChange' => $revenueChange,
            'ordersChange' => $ordersChange
        ];
    }

    public static function prepareEmployeeProducts(string $subpage = 'list', ?int $id = null): array
    {
        // Use models for data access and server-side pagination
        $subpage = $subpage ?: 'list';
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

        $products = [];
        $pagination = null;
        if ($subpage === 'list') {
            $perPage = 10;
            $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            if ($currentPage < 1) $currentPage = 1;
            $res = ProductModel::getProductsPage($currentPage, $perPage, $searchTerm);
            $products = $res['items'] ?? [];
            $total = (int)($res['total'] ?? 0);
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $pagination = [
                'current' => $currentPage,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'offset' => ($currentPage - 1) * $perPage
            ];
        } else {
            // non-list views still load all products for edit/view fallback
            $products = ProductModel::getAllProducts();
        }

        $product = null;
        $productDetails = null;
        if ($subpage === 'edit' && $id) {
            $product = ProductModel::getProductById($id);
            if ($product) {
                $pid = (int)($product['danhmucSP_id'] ?? 0);
                if (function_exists('is_book_category') && is_book_category($pid)) {
                    $productDetails = ProductModel::getProductByIdAndType('Sach', $id);
                } elseif (function_exists('is_stationery_category') && is_stationery_category($pid)) {
                    $productDetails = ProductModel::getProductByIdAndType('VPP', $id);
                } else {
                    $productDetails = null;
                }
                if ($productDetails) $product = array_merge($product, $productDetails);
            }
        }

        $catalogs = CategoriesModel::getAllCategories();
        $publishers = PublisherModel::getAllPublishers();
        $providers = ProviderModel::getAllProviders();

        // Retrieve authors and book types via models
        $authors = [];
        $loaisach = [];
        try {
            if (class_exists('AuthorsModel')) $authors = AuthorsModel::getAllAuthors();
        } catch (Throwable $e) { $authors = []; }
        try {
            if (class_exists('LoaiSachModel')) $loaisach = LoaiSachModel::getAll();
        } catch (Throwable $e) { $loaisach = []; }

        return [
            'subpage' => $subpage,
            'products' => $products,
            'product' => $product,
            'productDetails' => $productDetails,
            'catalogs' => $catalogs,
            'publishers' => $publishers,
            'providers' => $providers,
            'authors' => $authors,
            'loaisach' => $loaisach,
            'pagination' => $pagination
        ];
    }

    public static function prepareEmployeeOrders(string $subpage = 'list', ?int $id = null): array
    {
        $subpage = $subpage ?: 'list';
        $orders = [];
        $pagination = null;
        if ($subpage === 'list') {
            $perPage = 10;
            $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            if ($currentPage < 1) $currentPage = 1;
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;
            $res = OrdersModel::getOrdersPage($currentPage, $perPage, $search);
            $orders = $res['items'] ?? [];
            $total = (int)($res['total'] ?? 0);
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $pagination = ['current' => $currentPage, 'per_page' => $perPage, 'total' => $total, 'total_pages' => $totalPages, 'offset' => ($currentPage - 1) * $perPage];
        } else {
            $orders = OrdersModel::getAllOrders() ?? [];
        }
        $order = null;
        $items = [];
        if ($subpage === 'view' && $id) {
            $order = OrdersModel::getOrderById($id) ?? null;
            $items = OrdersModel::getOrderItemsByOrderId($id) ?? [];
        }
        return ['subpage' => $subpage, 'orders' => $orders, 'order' => $order, 'items' => $items, 'pagination' => $pagination];
    }

    public static function prepareEmployeeCustomers(string $subpage = 'list', ?int $id = null): array
    {
        $customers = [];
        $pagination = null;
        if ($subpage === 'list') {
            $perPage = 10;
            $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            if ($currentPage < 1) $currentPage = 1;
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;
            $res = CustomerModel::getCustomersPage($currentPage, $perPage, $search);
            $customers = $res['items'] ?? [];
            $total = (int)($res['total'] ?? 0);
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $pagination = ['current' => $currentPage, 'per_page' => $perPage, 'total' => $total, 'total_pages' => $totalPages, 'offset' => ($currentPage - 1) * $perPage];
        } else {
            $customers = CustomerModel::getAllCustomers() ?? [];
        }
        return ['customers' => $customers, 'subpage' => $subpage, 'pagination' => $pagination];
    }

    public static function prepareEmployeeEmployees(string $subpage = 'list', ?int $id = null): array
    {
        $employees = (class_exists('EmployeeModel') && method_exists('EmployeeModel', 'getAllEmployees')) ? EmployeeModel::getAllEmployees() : [];
        $employee_data = null;
            if ($subpage === 'edit' && $id) {
            $employee_data = (class_exists('EmployeeModel') && method_exists('EmployeeModel', 'getEmployeeById')) ? EmployeeModel::getEmployeeById($id) : null;
        }
        return ['employees' => $employees, 'subpage' => $subpage, 'employee_data' => $employee_data];
    }

    public static function prepareEmployeeProfile(?int $employee_id): array
    {
        // Safely load employee data when an id is provided and model exists
        $employee_data = null;
        if (!empty($employee_id) && class_exists('EmployeeModel') && method_exists('EmployeeModel', 'getEmployeeById')) {
            try {
                $employee_data = EmployeeModel::getEmployeeById($employee_id);
            } catch (Throwable $e) {
                $employee_data = null;
            }
        }

        $processedCount = 0;
        try {
            if (!empty($employee_id) && class_exists('EmployeeModel') && method_exists('EmployeeModel', 'getEmployeeOrderCount')) {
                $processedCount = EmployeeModel::getEmployeeOrderCount($employee_id);
            }
        } catch (Throwable $e) {
            $processedCount = 0;
        }

        return ['employee_data' => $employee_data, 'processedCount' => $processedCount];
    }

    public static function prepareEmployeeReports(?string $startDate = null, ?string $endDate = null): array
    {
        // Accept range shortcuts via GET 'range' for convenience
        $range = $_GET['range'] ?? null;
        if ($range === 'today') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } elseif ($range === '7days') {
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $endDate = date('Y-m-d');
        } elseif ($range === 'month') {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-d');
        } elseif ($range === 'year') {
            $startDate = date('Y-01-01');
            $endDate = date('Y-m-d');
        }

        $startDate = $startDate ?: date('Y-m-01');
        $endDate = $endDate ?: date('Y-m-d');

        // Data gathering using helper models if available
        $bestSellers = [];
        try {
            $bestSellers = EmployeeHelpers::getTopSellingProducts(10, $startDate, $endDate);
        } catch (Throwable $e) {
            try {
                $bestSellers = EmployeeHelpers::getTopSellingProducts(10);
            } catch (Throwable $__) {
                $bestSellers = [];
            }
        }

        $allOrders = [];
        try {
            $allOrders = OrdersModel::getAllOrdersByDateRange($startDate, $endDate);
        } catch (Throwable $e) {
            try {
                $allOrders = OrdersModel::getAllOrders();
            } catch (Throwable $__) {
                $allOrders = [];
            }
        }

        $orderStats = [];
        try {
            $orderStats = OrdersModel::getOrderStatistics($startDate, $endDate);
        } catch (Throwable $e) {
            try {
                $orderStats = EmployeeHelpers::getOrderStatistics();
            } catch (Throwable $__) {
                $orderStats = [];
            }
        }

        $revenueByDate = [];
        try {
            $revenueByDate = EmployeeHelpers::getRevenueByDateRange($startDate, $endDate);
        } catch (Throwable $e) {
            $revenueByDate = [];
        }

        $customerStats = [];
        try {
            $customerStats = EmployeeHelpers::getCustomerStatistics($startDate, $endDate);
        } catch (Throwable $e) {
            try {
                $customerStats = EmployeeHelpers::getCustomerStatistics();
            } catch (Throwable $__) {
                $customerStats = [];
            }
        }

        // Inventory data: prefer helper, otherwise derive from ProductModel
        $inventory = [];
        try {
            $inventory = EmployeeHelpers::getInventoryStatus();
        } catch (Throwable $e) {
            $inventory = [];
        }
        // Fallback to fresh data from ProductModel if helper returns empty/stale
        if (empty($inventory) && class_exists('ProductModel')) {
            $ps = ProductModel::getAllProducts();
            $inventory = array_map(fn($p) => [
                'name' => $p['name'] ?? ($p['tenSach'] ?? $p['tenVPP'] ?? 'N/A'),
                'sku' => $p['sanpham_id'] ?? '',
                'stock' => intval($p['soluongton'] ?? 0)
            ], $ps);
        }

        // Normalize inventory to consistent keys even when coming from helpers
        if (!empty($inventory)) {
            $inventory = array_map(function($row) {
                $name = $row['name'] ?? ($row['ten_sanpham'] ?? ($row['tenSach'] ?? ($row['tenVPP'] ?? null)));
                $sku = $row['sku'] ?? ($row['sanpham_id'] ?? ($row['id'] ?? null));
                $stock = intval($row['stock'] ?? ($row['soluongton'] ?? 0));
                return [
                    'name' => $name ?? 'N/A',
                    'sku' => $sku ?? '',
                    'stock' => $stock
                ];
            }, $inventory);
        }

        // Low-stock threshold (hard-coded)
        $lowStockThreshold = 20;
        $inventory = array_values(array_filter($inventory, function($row) use ($lowStockThreshold) {
            $stock = intval($row['stock'] ?? ($row['soluongton'] ?? 0));
            return $stock <= $lowStockThreshold;
        }));

        // Normalize orderStats to expected keys
        $os = $orderStats;
        $totalRevenue = floatval($os['total_revenue'] ?? $os['revenue'] ?? array_sum(array_map(fn($r) => floatval($r['total_revenue'] ?? $r['total'] ?? 0), $revenueByDate)));
        $totalOrders = intval($os['total_orders'] ?? $os['orders'] ?? count($allOrders));
        $productsSold = intval($os['products_sold'] ?? array_sum(array_map(fn($p) => intval($p['da_ban'] ?? $p['sold'] ?? 0), $bestSellers)));
        $profit = floatval($os['profit'] ?? round($totalRevenue * 0.25));

        // If export requested, provide CSV/XLSX or printable PDF
        $export = $_GET['export'] ?? null;
        $which = $_GET['which'] ?? 'bestSellers';
        $categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

        if (in_array($export, ['csv', 'excel', 'xlsx'], true)) {
            $rows = [];
            $rowsCsv = [];

            $borderThin = 'border="thin"';
            $cell = fn($text, $withBorder = true, $isBold = false) =>
                ($withBorder ? '<style ' . $borderThin . '>' : '<style>')
                . ($isBold ? '<b>' . $text . '</b>' : $text)
                . '</style>';

            // Tổng quan
            $rows[] = [$cell('Báo cáo', true, true), $cell('Từ'), $cell($startDate), $cell('Đến'), $cell($endDate)];
            $rows[] = [$cell('Tổng doanh thu', true, true), $cell($totalRevenue)];
            $rows[] = [$cell('Tổng đơn hàng', true, true), $cell($totalOrders)];
            $rows[] = [$cell('Sản phẩm đã bán', true, true), $cell($productsSold)];
            $rows[] = [$cell('Lợi nhuận ước tính', true, true), $cell($profit)];

            $rowsCsv[] = ['Báo cáo', 'Từ', $startDate, 'Đến', $endDate];
            $rowsCsv[] = ['Tổng doanh thu', $totalRevenue];
            $rowsCsv[] = ['Tổng đơn hàng', $totalOrders];
            $rowsCsv[] = ['Sản phẩm đã bán', $productsSold];
            $rowsCsv[] = ['Lợi nhuận ước tính', $profit];

            // Sản phẩm bán chạy
            $rows[] = [];
            $rows[] = [$cell('Sản phẩm bán chạy', true, true)];
            $rows[] = [$cell('Tên sản phẩm', true, true), $cell('Đã bán', true, true), $cell('Doanh thu', true, true)];
            $rowsCsv[] = [];
            $rowsCsv[] = ['Sản phẩm bán chạy'];
            $rowsCsv[] = ['Tên sản phẩm', 'Đã bán', 'Doanh thu'];
            foreach ($bestSellers as $p) {
                $rowData = [
                    $p['ten_sanpham'] ?? $p['name'] ?? $p['tenVPP'] ?? '',
                    $p['da_ban'] ?? $p['sold'] ?? 0,
                    $p['tong_tien'] ?? $p['revenue'] ?? 0
                ];
                $rows[] = array_map(fn($v) => $cell($v, true, false), $rowData);
                $rowsCsv[] = $rowData;
            }

            // Doanh thu theo ngày
            $rows[] = [];
            $rows[] = [$cell('Doanh thu theo ngày', true, true)];
            $rows[] = [$cell('Ngày', true, true), $cell('Đơn hàng', true, true), $cell('Doanh thu', true, true)];
            $rowsCsv[] = [];
            $rowsCsv[] = ['Doanh thu theo ngày'];
            $rowsCsv[] = ['Ngày', 'Đơn hàng', 'Doanh thu'];
            foreach ($revenueByDate as $r) {
                $rowData = [
                    $r['date'] ?? $r['label'] ?? '',
                    $r['order_count'] ?? $r['orders'] ?? 0,
                    $r['total_revenue'] ?? $r['total'] ?? 0
                ];
                $rows[] = array_map(fn($v) => $cell($v, true, false), $rowData);
                $rowsCsv[] = $rowData;
            }

            // Tồn kho
            $rows[] = [];
            $rows[] = [$cell('Tồn kho', true, true)];
            $rows[] = [$cell('Tên sản phẩm', true, true), $cell('SKU', true, true), $cell('Số lượng còn lại', true, true), $cell('Trạng thái', true, true)];
            $rowsCsv[] = [];
            $rowsCsv[] = ['Tồn kho'];
            $rowsCsv[] = ['Tên sản phẩm', 'SKU', 'Số lượng còn lại', 'Trạng thái'];
            foreach ($inventory as $row) {
                $stock = intval($row['stock'] ?? 0);
                $status = $stock <= 0 ? 'Hết hàng' : ($stock <= 5 ? 'Còn ít' : 'Còn hàng');
                $rowData = [$row['name'] ?? '', $row['sku'] ?? '', $stock, $status];
                $rows[] = array_map(fn($v) => $cell($v, true, false), $rowData);
                $rowsCsv[] = $rowData;
            }

            if ($export === 'csv') {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="report_' . $which . '_' . date('Ymd') . '.csv"');
                $out = fopen('php://output', 'w');
                foreach ($rowsCsv as $row) {
                    fputcsv($out, $row);
                }
                fclose($out);
                exit;
            }

            require_once __DIR__ . '/../xlsx_helper.php';
            Xlsx_Helper::array_to_xlsx_download($rows, 'report_' . $which . '_' . date('Ymd') . '.xlsx');
        }

        if ($export === 'pdf') {
            require_once __DIR__ . '/../pdf_helper.php';
            PDF_Help::report_to_pdf_download([
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalRevenue' => $totalRevenue,
                'totalOrders' => $totalOrders,
                'productsSold' => $productsSold,
                'profit' => $profit,
                'bestSellers' => $bestSellers,
                'revenueByDate' => $revenueByDate,
                'inventory' => $inventory
            ], 'report_' . $which . '_' . date('Ymd') . '.pdf');
        }

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'bestSellers' => $bestSellers,
            'allOrders' => $allOrders,
            'orderStats' => ['total_revenue' => $totalRevenue, 'total_orders' => $totalOrders, 'products_sold' => $productsSold, 'profit' => $profit],
            'revenueByDate' => $revenueByDate,
            'customerStats' => $customerStats,
            'inventory' => $inventory
        ];
    }

    public static function prepareEmployeeSettings(): array
    {
        $allEmployees = (class_exists('EmployeeModel') && method_exists('EmployeeModel', 'getAllEmployees')) ? EmployeeModel::getAllEmployees() : [];
        return ['allEmployees' => $allEmployees];
    }

    public static function prepareEmployeePublishers(string $subpage = 'list', ?int $id = null): array
    {
        $subpage = $subpage ?: 'list';
        $publishers = [];
        $pagination = null;
        if ($subpage === 'list') {
            $perPage = 10;
            $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            if ($currentPage < 1) $currentPage = 1;
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;
            $res = PublisherModel::getPublishersPage($currentPage, $perPage, $search);
            $publishers = $res['items'] ?? [];
            $total = (int)($res['total'] ?? 0);
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $pagination = ['current' => $currentPage, 'per_page' => $perPage, 'total' => $total, 'total_pages' => $totalPages, 'offset' => ($currentPage - 1) * $perPage];
        } else {
            $publishers = PublisherModel::getAllPublishers() ?? [];
        }
        $publisher = null;
        if ($subpage === 'edit' && $id) {
            $publisher = PublisherModel::getPublisherById($id) ?? null;
        }
        return ['subpage' => $subpage, 'publishers' => $publishers, 'publisher' => $publisher, 'pagination' => $pagination];
    }

    public static function prepareEmployeeProviders(string $subpage = 'list', ?int $id = null): array
    {
        $subpage = $subpage ?: 'list';
        $providers = [];
        $pagination = null;
        if ($subpage === 'list') {
            $perPage = 10;
            $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            if ($currentPage < 1) $currentPage = 1;
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;
            $res = ProviderModel::getProvidersPage($currentPage, $perPage, $search);
            $providers = $res['items'] ?? [];
            $total = (int)($res['total'] ?? 0);
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $pagination = ['current' => $currentPage, 'per_page' => $perPage, 'total' => $total, 'total_pages' => $totalPages, 'offset' => ($currentPage - 1) * $perPage];
        } else {
            $providers = ProviderModel::getAllProviders() ?? [];
        }
        $provider = null;
        if ($subpage === 'edit' && $id) {
            $provider = ProviderModel::getProviderById($id) ?? null;
        }
        return ['subpage' => $subpage, 'providers' => $providers, 'provider' => $provider, 'pagination' => $pagination];
    }

    public static function prepareEmployeeCategories(string $subpage = 'list', ?int $id = null): array
    {
        $subpage = $subpage ?: 'list';
        $categories = [];
        $pagination = null;
        if ($subpage === 'list') {
            $perPage = 10;
            $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
            if ($currentPage < 1) $currentPage = 1;
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;
            $res = CategoriesModel::getCategoriesPage($currentPage, $perPage, $search);
            $categories = $res['items'] ?? [];
            $total = (int)($res['total'] ?? 0);
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $pagination = ['current' => $currentPage, 'per_page' => $perPage, 'total' => $total, 'total_pages' => $totalPages, 'offset' => ($currentPage - 1) * $perPage];
        } else {
            $categories = CategoriesModel::getAllCategories() ?? [];
        }
        $category = null;
        if ($subpage === 'edit' && $id) {
            $category = CategoriesModel::getCategoryById($id) ?? null;
        }
        return ['subpage' => $subpage, 'categories' => $categories, 'category' => $category, 'pagination' => $pagination];
    }

    public static function prepareEmployeePromotions(string $subpage = 'list', ?int $id = null): array
    {
        $subpage = $subpage ?: 'list';
        $promotions = [];
        $promotion = null;
        
        if ($subpage === 'list') {
            $promotions = PromotionModel::getAllPromotions();
        } elseif ($subpage === 'view' && $id) {
            $promotion = PromotionModel::getPromotionWithDetails($id);
        } elseif ($subpage === 'edit' && $id) {
            $promotion = PromotionModel::getPromotionWithDetails($id);
        }
        
        return [
            'subpage' => $subpage,
            'promotions' => $promotions,
            'promotion' => $promotion
        ];
    }
}