<?php

class CrawlController
{
    protected static function getBasePath(): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        if ($script === '') return '';
        // This controller runs from /crawl/products/index.php (two levels deep).
        // Derive project base path robustly across hosting under a subfolder, e.g. /bookstore_customer.
        $dir = rtrim(dirname(dirname(dirname($script))), '/');
        if ($dir === '' || $dir === '\\' || $dir === '/') return '';
        return $dir; // e.g. '/bookstore_customer'
    }
    protected static function respondJson($payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected static function buildAbsoluteUrl(string $path): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = self::getBasePath();
        // Ensure leading slash for path
        if ($path !== '' && $path[0] !== '/') $path = '/' . $path;
        // Ensure base starts with slash (or empty)
        if ($base !== '' && $base[0] !== '/') $base = '/' . $base;
        // Avoid double slashes when base is empty or '/'
        $joined = rtrim($base, '/') . $path;
        return $scheme . '://' . $host . $joined;
    }

    public static function products(): void
    {
        // Basic CORS for crawlers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        try {
            // Initialize ProductModel PDO if needed
            if (class_exists('ProductModel')) {
                if (method_exists('ProductModel', 'init')) {
                    ProductModel::init();
                }
            }

            $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
            $perPage = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 0; // 0 means all
            $limit = isset($_GET['limit']) ? max(0, (int)$_GET['limit']) : 0; // optional hard cap

            $all = ProductModel::getAllProducts();

            // Optional pagination
            if ($perPage > 0) {
                $offset = ($page - 1) * $perPage;
                $items = array_slice($all, $offset, $perPage);
            } else {
                $items = $all;
            }
            if ($limit > 0) {
                $items = array_slice($items, 0, $limit);
            }

            $result = array_map(function ($p) {
                $id = (int)($p['sanpham_id'] ?? 0);
                $name = $p['name'] ?? '';
                $desc = $p['mo_ta'] ?? '';
                $price = isset($p['gia']) ? (float)$p['gia'] : null;
                $img = $p['hinhanh'] ?? 'defaultProduct.png';
                $type = $p['type'] ?? (function_exists('is_book_category') && isset($p['danhmucSP_id']) && is_book_category((int)$p['danhmucSP_id'])) ? 'book' : 'vpp';

                $relativeProductUrl = 'index.php?page=productview&id=' . $id;
                $absoluteProductUrl = self::buildAbsoluteUrl('/' . $relativeProductUrl);
                $imageUrl = self::buildAbsoluteUrl('/assets/images/products/' . $img);

                return [
                    'id' => $id,
                    'type' => $type,
                    'name' => $name,
                    'description' => $desc,
                    'price' => $price,
                    'stock' => isset($p['soluongton']) ? (int)$p['soluongton'] : null,
                    'sold' => isset($p['soluongban']) ? (int)$p['soluongban'] : null,
                    'category' => $p['category_name'] ?? null,
                    'author' => $p['author_name'] ?? null,
                    'publisher' => $p['publisher_name'] ?? null,
                    'provider' => $p['provider_name'] ?? null,
                    'image_url' => $imageUrl,
                    'url' => $absoluteProductUrl,
                ];
            }, $items);

            // If format=html or Accept header prefers text/html, render a simple HTML page for scrapers
            $format = strtolower($_GET['format'] ?? '');
            $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
            $wantsHtml = ($format === 'html') || (strpos($accept, 'text/html') !== false);

            if ($wantsHtml) {
                header('Content-Type: text/html; charset=utf-8');
                echo "<!DOCTYPE html><html lang=\"vi\"><head><meta charset=\"utf-8\"><title>Products Crawl</title></head><body>";
                echo "<h1>Danh sách sản phẩm</h1>";
                echo "<p>" . htmlspecialchars((string)count($result)) . " mục (tổng " . htmlspecialchars((string)count($all)) . ")</p>";
                foreach ($result as $it) {
                    echo "<article itemscope itemtype=\"https://schema.org/Product\" style=\"margin-bottom:16px;\">";
                    // Name
                    echo "<h2 itemprop=\"name\">" . htmlspecialchars((string)($it['name'] ?? '')) . "</h2>";
                    // Image
                    if (!empty($it['image_url'])) {
                        echo "<img itemprop=\"image\" src=\"" . htmlspecialchars((string)$it['image_url']) . "\" alt=\"" . htmlspecialchars((string)($it['name'] ?? '')) . "\" width=\"160\">";
                    }
                    // Description
                    if (!empty($it['description'])) {
                        echo "<p itemprop=\"description\">" . nl2br(htmlspecialchars((string)$it['description'])) . "</p>";
                    }
                    // Category, author, publisher
                    if (!empty($it['category'])) {
                        echo "<p>Danh mục: <span>" . htmlspecialchars((string)$it['category']) . "</span></p>";
                    }
                    if (!empty($it['author'])) {
                        echo "<p>Tác giả: <span>" . htmlspecialchars((string)$it['author']) . "</span></p>";
                    }
                    if (!empty($it['publisher'])) {
                        echo "<p>NXB: <span>" . htmlspecialchars((string)$it['publisher']) . "</span></p>";
                    }
                    // Offers
                    if (isset($it['price'])) {
                        echo "<div itemprop=\"offers\" itemscope itemtype=\"https://schema.org/Offer\">";
                        echo "<meta itemprop=\"priceCurrency\" content=\"VND\">";
                        echo "<span>Giá: <span itemprop=\"price\">" . htmlspecialchars((string)$it['price']) . "</span> ₫</span>";
                        echo "</div>";
                    }
                    // URL
                    if (!empty($it['url'])) {
                        echo "<p><a itemprop=\"url\" href=\"" . htmlspecialchars((string)$it['url']) . "\">Xem chi tiết</a></p>";
                    }
                    echo "</article>";
                }
                echo "</body></html>";
                exit;
            }

            // Plain text fallback: one product per line
            $wantsText = ($format === 'text') || (strpos($accept, 'text/plain') !== false);
            if ($wantsText) {
                header('Content-Type: text/plain; charset=utf-8');
                foreach ($result as $it) {
                    $line = ($it['name'] ?? '') . ' | ' . (string)($it['price'] ?? '') . ' | ' . ($it['category'] ?? '') . ' | ' . ($it['url'] ?? '');
                    echo $line . "\n";
                }
                exit;
            }

            $payload = [
                'success' => true,
                'count' => count($result),
                'total' => count($all),
                'page' => $perPage > 0 ? $page : null,
                'per_page' => $perPage > 0 ? $perPage : null,
                'items' => $result,
            ];

            self::respondJson($payload);
        } catch (Throwable $e) {
            self::respondJson(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
