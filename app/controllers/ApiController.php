<?php

class ApiController
{
    // Enforce employee roles for protected API endpoints and return JSON 401 instead of redirecting
    protected static function ensureEmployeeRole(array $allowedRoles): void
    {
        $acc = $_SESSION['employee_account'] ?? null;
        $role = $acc['role'] ?? null;
        $loggedIn = !empty($acc['logged_in']);

        if (!$loggedIn || !in_array($role, $allowedRoles, true)) {
            self::respondJson(['success' => false, 'error' => 'Unauthorized'], 401);
        }
    }
    public static function products()
    {
        self::sendCorsHeaders();
        self::ensureApiAllowed();

        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
        $q = trim($_GET['q'] ?? '');
        $filters = [
            'category' => $_GET['category'] ?? ($_GET['category_id'] ?? 0),
            'provider' => $_GET['provider_id'] ?? 0,
            'publisher' => $_GET['publisher_id'] ?? 0,
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? ''
        ];
        $sortBy = $_GET['sort'] ?? '';

        try {
            $res = ProductModel::getProductsPage($page, $perPage, $q, $filters, $sortBy);
            $total = (int)($res['total'] ?? 0);
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            self::respondJson([
                'success' => true,
                'page' => $res['page'] ?? $page,
                'per_page' => $res['per_page'] ?? $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'items' => $res['items'] ?? []
            ]);
        } catch (Exception $e) {
            self::respondJson(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    protected static function sendCorsHeaders()
    {
        // Allow origins from config if set
        $cfg = [];
        $cfgPath = __DIR__ . '/../config.php';
        if (file_exists($cfgPath)) {
            $c = include $cfgPath;
            if (is_array($c)) $cfg = $c;
        }
        $origin = $cfg['api_allowed_origins'] ?? '*';
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-API-Key');
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    protected static function ensureApiAllowed()
    {
        // API key check (optional): read from app/config.php if present
        $configPath = __DIR__ . '/../config.php';
        $config = [];
        if (file_exists($configPath)) {
            $cfg = include $configPath;
            if (is_array($cfg)) $config = $cfg;
        }

        $requiredKey = $config['api_key'] ?? null;
        $provided = null;
        // Prefer header
        foreach (getallheaders() as $hn => $hv) {
            if (strtolower($hn) === 'x-api-key') {
                $provided = $hv;
                break;
            }
        }
        if ($provided === null && isset($_GET['api_key'])) $provided = $_GET['api_key'];

        if ($requiredKey && (!is_string($provided) || $provided !== $requiredKey)) {
            self::respondJson(['success' => false, 'error' => 'Invalid API key'], 401);
        }

        // rate limit per ID (api_key if provided, otherwise IP)
        $identifier = $provided ?: ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $limitCfg = $config['api_rate_limit'] ?? ['requests' => 60, 'window' => 60];
        $maxReq = (int)($limitCfg['requests'] ?? 60);
        $window = (int)($limitCfg['window'] ?? 60);
        $rl = self::rateLimitAllow($identifier, $maxReq, $window);
        if (empty($rl['allowed'])) {
            // send reset header
            header('X-RateLimit-Limit: ' . $maxReq);
            header('X-RateLimit-Remaining: 0');
            header('X-RateLimit-Reset: ' . ($rl['reset'] ?? time() + $window));
            self::respondJson(['success' => false, 'error' => 'Rate limit exceeded'], 429);
        }
        // send remaining headers for successful requests
        header('X-RateLimit-Limit: ' . $maxReq);
        header('X-RateLimit-Remaining: ' . max(0, ($maxReq - ($rl['count'] ?? 0))));
        header('X-RateLimit-Reset: ' . ($rl['reset'] ?? time() + $window));
    }

    protected static function rateLimitAllow(string $id, int $maxRequests = 60, int $windowSeconds = 60): array
    {
        $file = sys_get_temp_dir() . '/bookstore_api_rate_limit.json';
        $data = [];
        if (file_exists($file)) {
            $raw = @file_get_contents($file);
            $data = $raw ? json_decode($raw, true) : [];
            if (!is_array($data)) $data = [];
        }

        $now = time();
        if (!isset($data[$id]) || !is_array($data[$id])) {
            $data[$id] = ['count' => 1, 'reset' => $now + $windowSeconds];
        } else {
            if ($data[$id]['reset'] <= $now) {
                $data[$id] = ['count' => 1, 'reset' => $now + $windowSeconds];
            } else {
                $data[$id]['count'] = ($data[$id]['count'] ?? 0) + 1;
            }
        }

        $allowed = ($data[$id]['count'] ?? 0) <= $maxRequests;

        // Persist (best-effort)
        @file_put_contents($file, json_encode($data));

        return [
            'allowed' => $allowed,
            'count' => (int)($data[$id]['count'] ?? 0),
            'reset' => (int)($data[$id]['reset'] ?? ($now + $windowSeconds))
        ];
    }

    protected static function respondJson($payload, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function providers()
    {
        self::sendCorsHeaders();
        self::ensureApiAllowed();

        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
        $q = trim($_GET['q'] ?? '');
        try {
            $all = ProviderModel::getAllProviders();
            if ($q !== '') {
                $all = array_values(array_filter($all, fn($it) => stripos($it['ten'] ?? '', $q) !== false));
            }
            $total = count($all);
            $items = array_slice($all, ($page - 1) * $perPage, $perPage);
            self::respondJson(['success' => true, 'page' => $page, 'per_page' => $perPage, 'total' => $total, 'items' => $items]);
        } catch (Exception $e) {
            self::respondJson(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public static function publishers()
    {
        self::sendCorsHeaders();
        self::ensureApiAllowed();

        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
        $q = trim($_GET['q'] ?? '');
        try {
            $all = PublisherModel::getAllPublishers();
            if ($q !== '') {
                $all = array_values(array_filter($all, fn($it) => stripos($it['ten'] ?? '', $q) !== false));
            }
            $total = count($all);
            $items = array_slice($all, ($page - 1) * $perPage, $perPage);
            self::respondJson(['success' => true, 'page' => $page, 'per_page' => $perPage, 'total' => $total, 'items' => $items]);
        } catch (Exception $e) {
            self::respondJson(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public static function customers()
    {
        self::sendCorsHeaders();
        self::ensureApiAllowed();

        // Only admin/manager can view customers via API
        self::ensureEmployeeRole(['admin', 'quanly']);

        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
        $q = trim($_GET['q'] ?? '');
        try {
            $all = CustomerModel::getAllCustomers();
            if ($q !== '') {
                $all = array_values(array_filter($all, fn($it) => stripos(($it['email'] ?? '') . ' ' . ($it['ho'] ?? '') . ' ' . ($it['ten'] ?? ''), $q) !== false));
            }
            $total = count($all);
            $items = array_slice($all, ($page - 1) * $perPage, $perPage);
            // hide password hashes
            foreach ($items as &$i) {
                unset($i['password']);
            }
            self::respondJson(['success' => true, 'page' => $page, 'per_page' => $perPage, 'total' => $total, 'items' => $items]);
        } catch (Exception $e) {
            self::respondJson(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public static function employees()
    {
        self::sendCorsHeaders();
        self::ensureApiAllowed();

        // Only admin can view employees via API
        self::ensureEmployeeRole(['admin']);

        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
        $q = trim($_GET['q'] ?? '');
        try {
            $all = EmployeeModel::getAllEmployees();
            if ($q !== '') {
                $all = array_values(array_filter($all, fn($it) => stripos(($it['email'] ?? '') . ' ' . ($it['ho'] ?? '') . ' ' . ($it['ten'] ?? ''), $q) !== false));
            }
            $total = count($all);
            $items = array_slice($all, ($page - 1) * $perPage, $perPage);
            foreach ($items as &$i) {
                unset($i['password']);
            }
            self::respondJson(['success' => true, 'page' => $page, 'per_page' => $perPage, 'total' => $total, 'items' => $items]);
        } catch (Exception $e) {
            self::respondJson(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public static function searchProducts()
    {
        self::sendCorsHeaders();
        self::ensureApiAllowed();

        $searchTerm = trim($_GET['q'] ?? '');

        if ($searchTerm === '') {
            self::respondJson(['success' => false, 'products' => [], 'error' => 'Missing search keyword'], 400);
        }

        try {
            $products = PromotionModel::searchProducts($searchTerm);
            self::respondJson(['success' => true, 'products' => $products]);
        } catch (Exception $e) {
            self::respondJson(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
