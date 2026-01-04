<?php
/**
 * API endpoint để xuất toàn bộ dữ liệu sản phẩm dưới dạng JSON
 * Dùng cho training chatbot Mira AI
 */

// Thiết lập header cho JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Load các file cần thiết
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/product.php';
require_once __DIR__ . '/../../app/models/categories.php';
require_once __DIR__ . '/../../app/models/loaisach.php';
require_once __DIR__ . '/../../app/models/authors.php';
require_once __DIR__ . '/../../app/models/publisher.php';
require_once __DIR__ . '/../../app/models/provider.php';
require_once __DIR__ . '/../../app/models/units.php';
require_once __DIR__ . '/../../app/models/promotions.php';
require_once __DIR__ . '/../../app/models/reviews.php';
require_once __DIR__ . '/../../app/models/customer.php';
require_once __DIR__ . '/../../app/models/promotions.php';

try {
    // Khởi tạo database
    $pdo = Database::getInstance();
    
    // Khởi tạo các models
    ProductModel::init($pdo);
    CategoriesModel::init($pdo);
    LoaiSachModel::init($pdo);
    AuthorsModel::init($pdo);
    PublisherModel::init($pdo);
    ProviderModel::init($pdo);
    UnitsModel::init($pdo);
    PromotionModel::init($pdo);
    ReviewsModel::init($pdo);

    // Lấy tất cả sản phẩm
    $products = ProductModel::getAllProducts();
    
    // Lấy các dữ liệu phụ trợ
    $categories = CategoriesModel::getAllCategories();
    $loaisach = LoaiSachModel::getAll();
    $authors = AuthorsModel::getAllAuthors();
    $publishers = PublisherModel::getAllPublishers();
    $providers = ProviderModel::getAllProviders();
    $units = UnitsModel::getAllUnits();
    $promotions = PromotionModel::getAllPromotions();
    
    // Tạo lookup arrays để dễ truy xuất
    $categoryLookup = [];
    foreach ($categories as $cat) {
        $categoryLookup[$cat['danhmucSP_id']] = $cat;
    }
    
    $loaisachLookup = [];
    foreach ($loaisach as $ls) {
        $loaisachLookup[$ls['loaisach_code']] = $ls;
    }
    
    $authorLookup = [];
    foreach ($authors as $author) {
        $authorLookup[$author['tacgia_id']] = $author;
    }
    
    $publisherLookup = [];
    foreach ($publishers as $pub) {
        $publisherLookup[$pub['nhaxuatban_id']] = $pub;
    }
    
    $providerLookup = [];
    foreach ($providers as $prov) {
        $providerLookup[$prov['nhacungcap_id']] = $prov;
    }
    
    $unitLookup = [];
    foreach ($units as $unit) {
        $unitLookup[$unit['donvitinh_id']] = $unit;
    }
    
    // Lấy khuyến mãi đang hoạt động cho từng sản phẩm
    $promotionsByProduct = [];
    foreach ($products as $product) {
        $activePromotions = PromotionModel::getActivePromotionForProduct($product['sanpham_id']);
        if (!empty($activePromotions) && is_array($activePromotions)) {
            // Nếu là một promotion đơn lẻ, chuyển thành array
            if (isset($activePromotions['khuyenmai_id'])) {
                $promotionsByProduct[$product['sanpham_id']] = [$activePromotions];
            } else {
                $promotionsByProduct[$product['sanpham_id']] = $activePromotions;
            }
        }
    }
    
    // Lấy đánh giá cho từng sản phẩm
    $reviewsByProduct = [];
    foreach ($products as $product) {
        $reviews = ReviewsModel::getAllReviewsByProductId($product['sanpham_id']);
        if (!empty($reviews)) {
            $reviewsByProduct[$product['sanpham_id']] = $reviews;
        }
    }
    
    // Làm giàu dữ liệu sản phẩm
    $enrichedProducts = [];
    foreach ($products as $product) {
        $enriched = [
            'id' => (int)$product['sanpham_id'],
            'name' => $product['name'] ?? '',
            'type' => $product['type'] ?? '', // 'book' hoặc 'vpp'
            'description' => $product['mo_ta'] ?? '',
            'price' => (float)$product['gia'],
            'stock_quantity' => (int)$product['soluongton'],
            'sold_quantity' => (int)$product['soluongban'],
            'image' => $product['hinhanh'] ?? '',
            'image_url' => !empty($product['hinhanh']) ? '/assets/images/products/' . $product['hinhanh'] : '',
            
            // Danh mục sản phẩm
            'category' => [
                'id' => (int)$product['danhmucSP_id'],
                'name' => isset($categoryLookup[$product['danhmucSP_id']]) ? ($categoryLookup[$product['danhmucSP_id']]['tenDanhMuc'] ?? '') : '',
                'description' => isset($categoryLookup[$product['danhmucSP_id']]) ? ($categoryLookup[$product['danhmucSP_id']]['mo_ta'] ?? '') : ''
            ],
            
            // Đơn vị tính
            'unit' => [
                'id' => (int)$product['donvitinh_id'],
                'name' => isset($unitLookup[$product['donvitinh_id']]) ? ($unitLookup[$product['donvitinh_id']]['ten'] ?? '') : ''
            ],
            
            // Nhà cung cấp
            'provider' => [
                'id' => (int)$product['nhacungcap_id'],
                'name' => $product['provider_name'] ?? '',
                'address' => isset($providerLookup[$product['nhacungcap_id']]) ? ($providerLookup[$product['nhacungcap_id']]['diachi'] ?? '') : '',
                'phone' => isset($providerLookup[$product['nhacungcap_id']]) ? ($providerLookup[$product['nhacungcap_id']]['sdt'] ?? '') : '',
                'email' => isset($providerLookup[$product['nhacungcap_id']]) ? ($providerLookup[$product['nhacungcap_id']]['email'] ?? '') : ''
            ]
        ];
        
        // Thông tin chi tiết cho sách
        if ($product['type'] === 'book' && isset($product['sach_id'])) {
            $enriched['book_details'] = [
                'book_id' => (int)$product['item_id'],
                'category_name' => $product['category_name'] ?? '',
                'year_published' => $product['namXB'] ?? '',
                
                // Tác giả
                'author' => null,
                
                // Nhà xuất bản
                'publisher' => null
            ];
            
            // Thêm thông tin tác giả nếu có
            if (!empty($product['author_name'])) {
                $authorId = null;
                // Tìm author_id từ sach table
                $stmtAuthor = $pdo->prepare("SELECT tacgia_id FROM sach WHERE sach_id = :sach_id");
                $stmtAuthor->execute([':sach_id' => $product['item_id']]);
                $authorData = $stmtAuthor->fetch(PDO::FETCH_ASSOC);
                if ($authorData && isset($authorLookup[$authorData['tacgia_id']])) {
                    $author = $authorLookup[$authorData['tacgia_id']];
                    $enriched['book_details']['author'] = [
                        'id' => (int)$author['tacgia_id'],
                        'full_name' => trim(($author['ho'] ?? '') . ' ' . ($author['tendem'] ?? '') . ' ' . ($author['ten'] ?? '')),
                        'first_name' => $author['ho'] ?? '',
                        'middle_name' => $author['tendem'] ?? '',
                        'last_name' => $author['ten'] ?? '',
                        'address' => $author['diachi'] ?? '',
                        'phone' => $author['sdt'] ?? '',
                        'email' => $author['email'] ?? ''
                    ];
                }
            }
            
            // Thêm thông tin nhà xuất bản nếu có
            if (!empty($product['publisher_name'])) {
                $publisherId = null;
                // Tìm nhaxuatban_id từ sach table
                $stmtPublisher = $pdo->prepare("SELECT nhaxuatban_id FROM sach WHERE sach_id = :sach_id");
                $stmtPublisher->execute([':sach_id' => $product['item_id']]);
                $publisherData = $stmtPublisher->fetch(PDO::FETCH_ASSOC);
                if ($publisherData && isset($publisherLookup[$publisherData['nhaxuatban_id']])) {
                    $publisher = $publisherLookup[$publisherData['nhaxuatban_id']];
                    $enriched['book_details']['publisher'] = [
                        'id' => (int)$publisher['nhaxuatban_id'],
                        'name' => $publisher['ten'] ?? '',
                        'address' => $publisher['diachi'] ?? '',
                        'phone' => $publisher['sdt'] ?? '',
                        'email' => $publisher['email'] ?? ''
                    ];
                }
            }
        }
        
        // Khuyến mãi đang áp dụng
        $enriched['promotions'] = [];
        if (isset($promotionsByProduct[$product['sanpham_id']]) && is_array($promotionsByProduct[$product['sanpham_id']])) {
            foreach ($promotionsByProduct[$product['sanpham_id']] as $promo) {
                // Kiểm tra $promo có phải là array không
                if (is_array($promo) && isset($promo['khuyenmai_id'])) {
                    $enriched['promotions'][] = [
                        'id' => (int)$promo['khuyenmai_id'],
                        'name' => $promo['ten'] ?? '',
                        'discount_rate' => (float)($promo['tilegiamgia'] ?? 0),
                        'start_date' => $promo['ngaybatdau'] ?? '',
                        'end_date' => $promo['ngayketthuc'] ?? '',
                        'discounted_price' => (float)$product['gia'] * (1 - (float)($promo['tilegiamgia'] ?? 0) / 100)
                    ];
                }
            }
        }
        
        // Đánh giá sản phẩm
        $enriched['reviews'] = [
            'total_reviews' => 0,
            'average_rating' => 0,
            'reviews_list' => []
        ];
        
        if (isset($reviewsByProduct[$product['sanpham_id']])) {
            $reviews = $reviewsByProduct[$product['sanpham_id']];
            $enriched['reviews']['total_reviews'] = count($reviews);
            
            $totalRating = 0;
            foreach ($reviews as $review) {
                $totalRating += (int)$review['rating'];
                $enriched['reviews']['reviews_list'][] = [
                    'id' => (int)$review['danhgia_id'],
                    'customer_id' => (int)$review['khachhang_id'],
                    'customer_name' => $review['customer_name'] ?? 'Khách hàng',
                    'rating' => (int)$review['rating'],
                    'comment' => $review['binhluan'] ?? '',
                    'created_at' => $review['ngaytao'] ?? ''
                ];
            }
            
            if ($enriched['reviews']['total_reviews'] > 0) {
                $enriched['reviews']['average_rating'] = round($totalRating / $enriched['reviews']['total_reviews'], 1);
            }
        }
        
        // Trạng thái tồn kho
        $enriched['stock_status'] = 'out_of_stock';
        if ($enriched['stock_quantity'] > 0) {
            if ($enriched['stock_quantity'] < 10) {
                $enriched['stock_status'] = 'low_stock';
            } else {
                $enriched['stock_status'] = 'in_stock';
            }
        }
        
        // Tính giá sau khuyến mãi (nếu có)
        $enriched['final_price'] = $enriched['price'];
        if (!empty($enriched['promotions'])) {
            $maxDiscount = 0;
            foreach ($enriched['promotions'] as $promo) {
                if ($promo['discount_rate'] > $maxDiscount) {
                    $maxDiscount = $promo['discount_rate'];
                }
            }
            $enriched['final_price'] = $enriched['price'] * (1 - $maxDiscount / 100);
        }
        
        $enrichedProducts[] = $enriched;
    }
    
    // Tạo metadata
    $metadata = [
        'total_products' => count($enrichedProducts),
        'total_books' => count(array_filter($enrichedProducts, fn($p) => $p['type'] === 'book')),
        'total_stationery' => count(array_filter($enrichedProducts, fn($p) => $p['type'] === 'vpp')),
        'generated_at' => date('Y-m-d H:i:s'),
        'categories' => array_map(function($cat) {
            return [
                'id' => (int)$cat['danhmucSP_id'],
                'name' => $cat['tenDanhMuc'],
                'description' => $cat['mo_ta'] ?? ''
            ];
        }, $categories),
        'book_categories' => array_map(function($ls) {
            return [
                'code' => $ls['loaisach_code'],
                'name' => $ls['tenLoaiSach']
            ];
        }, $loaisach)
    ];
    
    // Xuất JSON
    $response = [
        'success' => true,
        'metadata' => $metadata,
        'products' => $enrichedProducts
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi lấy dữ liệu sản phẩm',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
