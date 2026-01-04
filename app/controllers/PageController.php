<?php

class PageController
{
    public static function prepareProductsPage(): array
    {
        $search = trim($_GET['search'] ?? '');
        $categoryId = (int)($_GET['danhmucSP_id'] ?? 0);
        $minPrice = (int)($_GET['min'] ?? 0);
        $maxPrice = (int)($_GET['max'] ?? 0);
        $providerId = (int)($_GET['provider_id'] ?? 0);
        $publisherId = (int)($_GET['publisher_id'] ?? 0);
        $sortBy = $_GET['sort_by'] ?? '';

        $products =  ProductModel::filterProducts($search, $categoryId, $minPrice, $maxPrice, $providerId, $publisherId, $sortBy);

        $catalogs = CategoriesModel::getAllCategories();
        $providers = ProviderModel::getAllProviders();
        $publishers = PublisherModel::getAllPublishers();

        $all_products = ProductModel::getAllProducts();
        $allPrices = $all_products ? array_map(fn($p) => floatval($p['gia'] ?? 0), $all_products) : [];
        $globalMin = $allPrices ? (int)min($allPrices) : 0;
        $globalMax = $allPrices ? (int)max($allPrices) : 200000;
        if ($globalMin < 0) $globalMin = 0;
        if ($globalMax <= 0) $globalMax = 200000;

        if ($minPrice <= 0) $minPrice = $globalMin;
        if ($maxPrice <= 0) $maxPrice = $globalMax;
        if ($minPrice > $maxPrice) { $tmp = $minPrice; $minPrice = $maxPrice; $maxPrice = $tmp; }

        return [
            'products' => $products,
            'catalogs' => $catalogs,
            'providers' => $providers,
            'publishers' => $publishers,
            'globalMin' => $globalMin,
            'globalMax' => $globalMax,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sortBy' => $sortBy,
            'search' => $search,
            'categoryId' => $categoryId,
            'providerId' => $providerId,
            'publisherId' => $publisherId
        ];
    }

    public static function prepareNotifications(): array
    {
        $customer = $_SESSION['khachhang_account'] ?? null;
        if (!$customer) {
            return [
                'require_login' => true,
                'notifications' => [],
                'tab' => 'all',
                'limit' => 0,
                'counts' => ['all' => 0, 'orders' => 0, 'promotions' => 0, 'system' => 0]
            ];
        }

        $tab = $_GET['tab'] ?? 'all';
        $limit = isset($_GET['limit']) ? max(5, (int)$_GET['limit']) : 15;
        try {
            $list = NotificationModel::getCustomerNotifications((int)$customer['id'], $limit);
        } catch (Throwable $e) {
            $list = [];
        }

        $categorize = function(array $note): string {
            $title = mb_strtolower($note['tieu_de'] ?? '');
            $body  = mb_strtolower($note['noi_dung'] ?? '');
            $txt   = $title . ' ' . $body;
            if (preg_match('/\bđơn hàng|giao hàng|đã giao|đang giao|xác nhận|hóa đơn\b/u', $txt)) return 'orders';
            if (preg_match('/\bkhuyến mãi|flash sale|giảm giá|mã giảm|voucher|ưu đãi\b/u', $txt)) return 'promotions';
            return 'system';
        };

        $counts = ['all' => count($list), 'orders' => 0, 'promotions' => 0, 'system' => 0];
        foreach ($list as &$n) {
            $n['category'] = $categorize($n);
            $counts[$n['category']]++;
        }
        unset($n);

        if (in_array($tab, ['orders','promotions','system'], true)) {
            $filtered = array_values(array_filter($list, fn($n) => ($n['category'] ?? '') === $tab));
        } else {
            $filtered = $list;
        }

        return [
            'notifications' => $filtered,
            'counts' => $counts,
            'tab' => in_array($tab, ['orders','promotions','system','all'], true) ? $tab : 'all',
            'limit' => $limit
        ];
    }
    public static function prepareProductViewPage(int $id): array
    {
        $product = ProductModel::getProductById($id);
        $reviews = ReviewsModel::getAllReviewsByProductId($id) ?? [];

        return [
            'product' => $product,
            'reviews' => $reviews
        ];
    }

    public static function prepareHeader(): array
    {
        $search = $_GET['search'] ?? '';
        $customer = $_SESSION['khachhang_account'] ?? null;

        $catalogs = CategoriesModel::getAllCategories();
        if ($customer) {
            $cart_count = count(CartModel::getCartItems($customer['id']));
        } else {
            $cart_count = isset($_SESSION['guest_cart']) && is_array($_SESSION['guest_cart']) ? count($_SESSION['guest_cart']) : -1;
        }

        $notifications = [];
        $notification_count = 0;
        if (!empty($customer)) {
            $notification_count = NotificationModel::countUnreadNotifications($customer['id']);
            $notifications = NotificationModel::getCustomerNotifications($customer['id'], 8);
        }

        if (empty($_SESSION['csrf_token'])) generate_csrf_token();
        $csrf_token = $_SESSION['csrf_token'];

        return [
            'search' => $search,
            'catalogs' => $catalogs,
            'cart_count' => $cart_count,
            'notifications' => $notifications,
            'notification_count' => $notification_count,
            'csrf_token' => $csrf_token
        ];
    }

    public static function prepareHome(): array
    {
        $featured = ProductModel::getFeaturedProducts(8);
        $bestSellers = EmployeeHelpers::getTopSellingProducts(8);
        $newArrivals = ProductModel::getNewArrivals(8);

        return [
            'featured' => $featured,
            'bestSellers' => $bestSellers,
            'newArrivals' => $newArrivals
        ];
    }

    public static function prepareCart(): array
    {
        $customer = $_SESSION['khachhang_account'] ?? null;
        $items = [];
        $total = 0;
        if ($customer) {
            $items = CartModel::getCartItems($customer['id']);
            foreach ($items as $it) $total += (float)($it['thanhtien'] ?? ($it['dongia'] * $it['soluong']));
        }
        else {
            // Build items array from guest session cart
            $guest = $_SESSION['guest_cart'] ?? [];
            if (!empty($guest) && is_array($guest)) {
                foreach ($guest as $pid => $qty) {
                    $p = ProductModel::getProductById((int)$pid);
                    if (empty($p)) continue;
                    $price = (float)($p['gia'] ?? 0);
                    $itm = [
                        'ctgh_id' => null,
                        'giohang_id' => null,
                        'sanpham_id' => (int)$pid,
                        'soluong' => (int)$qty,
                        'gia' => $price,
                        'thanhtien' => $price * (int)$qty,
                        'name' => $p['tenSach'] ?? $p['tenVPP'] ?? ($p['name'] ?? ($p['mo_ta'] ?? '')), 
                        'type' => (function($p){ $id = (int)($p['danhmucSP_id'] ?? 0); if (function_exists('is_book_category') && is_book_category($id)) return 'Sach'; if (function_exists('is_stationery_category') && is_stationery_category($id)) return 'VanPhongPham'; return 'Khac'; })($p),
                        'hinhanh' => $p['hinhanh'] ?? null
                    ];
                    $items[] = $itm;
                    $total += $itm['thanhtien'];
                }
            }
        }
        return ['cart_items' => $items, 'cart_total' => $total];
    }

    public static function prepareCheckout(): array
    {
        $customer = $_SESSION['khachhang_account'] ?? null;
        $addresses = [];
        $cart_items = [];
        $cart_total = 0;
        if ($customer) {
            $addresses = AddressesModel::getAddressesByCustomer($customer['id']);
            if ($customer) {
                $cart_items = CartModel::getCartItems($customer['id']);
                foreach ($cart_items as $it) $cart_total += (float)($it['thanhtien'] ?? ($it['dongia'] * $it['soluong']));
            }
        }
        return ['addresses' => $addresses, 'cart_items' => $cart_items, 'cart_total' => $cart_total];
    }

    public static function prepareAccount(): array
    {
        $customer = $_SESSION['khachhang_account'] ?? null;
        $orders = [];
        $wishlist = [];
        $addresses = [];
        if ($customer) {
            $orders = OrdersModel::getOrdersByCustomer($customer['id']);
            $wishlist = WishlistModel::getWishlistByCustomer($customer['id']);
            $addresses = AddressesModel::getAddressesByCustomer($customer['id']);
        }
        return [
            'account_customer' => $customer, 
            'account_orders' => $orders, 
            'account_wishlist' => $wishlist,
            'customer_addresses' => $addresses
        ];
    }



    public static function fetchRecentOrders()
    {
        try {
            $recentOrders = EmployeeHelpers::getRecentOrders();
        } catch (Throwable $e) {
            $recentOrders = [];
        }
        $strings = "";
        if (!empty($recentOrders)):
            foreach ($recentOrders as $o):
                $orderId = (int)($o['hoadon_id'] ?? 0);
                try {
                    $ord_customer = OrdersModel::getCustomerByOrderId($orderId) ?: [];
                } catch (Throwable $e) {
                    $ord_customer = [];
                }

                $class = match ($o['trangthai']) {
                    'cho_xac_nhan' => 'warning',
                    'da_xac_nhan' => 'info',
                    'dang_giao_hang' => 'secondary',
                    'da_giao_hang' => 'success',
                    'da_huy' => 'danger',
                    default => 'dark'
                };

                $defined_status = '';
                switch ($o['trangthai']) {
                    case 'cho_xac_nhan':
                        $defined_status = 'Chờ xác nhận';
                        break;
                    case 'da_xac_nhan':
                        $defined_status = 'Đã xác nhận';
                        break;
                    case 'dang_giao_hang':
                        $defined_status = 'Đang giao hàng';
                        break;
                    case 'da_giao_hang':
                        $defined_status = 'Đã giao hàng';
                        break;
                    case 'da_huy':
                        $defined_status = 'Đã hủy';
                        break;
                    default:
                        $defined_status = 'Không xác định';
                }
                $customer_name = isset($ord_customer['ho_ten']) && is_string($ord_customer['ho_ten']) ? htmlspecialchars(substr($ord_customer['ho_ten'], 0, 20)) : '-';
                $amount = isset($o['tongtien']) ? (float)$o['tongtien'] : 0;
                $created = !empty($o['ngaytao']) ? date('d/m H:i', strtotime($o['ngaytao'])) : '-';
                $strings .= "
                <tr>
                    <td class='fw-bold'>#{$orderId}</td>
                    <td>" . $customer_name . "</td>
                    <td class='text-end fw-bold'>" . number_format($amount, 0, ',', '.') . "đ</td>
                    <td><span class='badge bg-{$class}'>{$defined_status}</span></td>
                    <td>" . $created . "</td>
                    <td>
                        <a href='?page=employee_orders&subpage=view&id={$orderId}' 
                           class='btn btn-sm btn-outline-primary p-1 px-2'>
                           <i class='fas fa-eye'></i>
                        </a>
                    </td>
                </tr>";
            endforeach;
        else:
            $strings .= "
            <tr>
                <td colspan='6' class='text-center py-3 text-muted'>Chưa có đơn hàng</td>
            </tr>";
        endif;
        return $strings;
    }
}
