<?php
// Prepare variables for the page
$subpage = $_GET['subpage'] ?? 'list';
$promotion_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$vars = EmployeePageController::prepareEmployeePromotions($subpage, $promotion_id);
if (is_array($vars)) extract($vars);

$employee = $_SESSION['employee_account'] ?? null;
$employeeRole = $employee['role'] ?? 'nhanvien';
$canEdit = in_array($employeeRole, ['admin', 'quanly']);

// Redirect if non-manager tries to access edit/add pages
if (!$canEdit && in_array($subpage, ['add', 'edit'])) {
    header('Location: index.php?page=employee_promotions');
    exit;
}

$page_title = 'Quản lý Khuyến Mãi';
ob_start();
?>

<div class="top-bar">
    <h2 class="mb-0">Quản lý Khuyến Mãi</h2>
    <?php if ($canEdit): ?>
        <button class="btn btn-primary" onclick="showAddPromotionModal()">
            <i class="fas fa-plus"></i> Thêm khuyến mãi
        </button>
    <?php endif; ?>
</div>

<?php if ($subpage === 'list'): ?>
    <!-- List View -->
    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Danh sách khuyến mãi</h4>
            <div class="d-flex gap-2">
                <input type="text" class="form-control" placeholder="Tìm kiếm..." style="width: 300px;" id="searchInput">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên khuyến mãi</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($promotions)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Chưa có khuyến mãi nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($promotions as $promo):
                            $now = date('Y-m-d');
                            $status = 'Chưa bắt đầu';
                            $statusClass = 'secondary';
                            if ($now >= $promo['ngaybatdau'] && $now <= $promo['ngayketthuc']) {
                                $status = 'Đang diễn ra';
                                $statusClass = 'success';
                            } elseif ($now > $promo['ngayketthuc']) {
                                $status = 'Đã kết thúc';
                                $statusClass = 'danger';
                            }
                        ?>
                            <tr>
                                <td><?= $promo['khuyenmai_id'] ?></td>
                                <td><?= htmlspecialchars($promo['ten'] ?? '') ?></td>
                                <td><?= date('d/m/Y', strtotime($promo['ngaybatdau'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($promo['ngayketthuc'])) ?></td>
                                <td><span class="badge bg-<?= $statusClass ?>"><?= $status ?></span></td>
                                <td class="swipe-actions">
                                    <a href="?page=employee_promotions&subpage=view&id=<?= $promo['khuyenmai_id'] ?>"
                                        class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($canEdit): ?>
                                        <a href="?page=employee_promotions&subpage=edit&id=<?= $promo['khuyenmai_id'] ?>"
                                            class="btn btn-sm btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="index.php?page=employee_promotions&action=delete"
                                            onsubmit="return confirm('Xác nhận xóa khuyến mãi này?');" style="display:inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                            <input type="hidden" name="id" value="<?= $promo['khuyenmai_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($subpage === 'view' && $promotion): ?>
    <!-- View Details -->
    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Chi tiết khuyến mãi</h4>
            <div class="d-flex gap-2">
                <a href="?page=employee_promotions" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <?php if ($canEdit): ?>
                    <a href="?page=employee_promotions&subpage=edit&id=<?= $promotion['khuyenmai_id'] ?>"
                        class="btn btn-primary">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Thông tin chung</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">ID:</th>
                        <td><?= $promotion['khuyenmai_id'] ?></td>
                    </tr>
                    <tr>
                        <th>Tên khuyến mãi:</th>
                        <td><?= htmlspecialchars($promotion['ten']) ?></td>
                    </tr>
                    <tr>
                        <th>Ngày bắt đầu:</th>
                        <td><?= date('d/m/Y', strtotime($promotion['ngaybatdau'])) ?></td>
                    </tr>
                    <tr>
                        <th>Ngày kết thúc:</th>
                        <td><?= date('d/m/Y', strtotime($promotion['ngayketthuc'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <h5 class="mb-3">Sản phẩm khuyến mãi</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID Sản phẩm</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá gốc</th>
                        <th>Số lượng KM</th>
                        <th>Tỉ lệ giảm giá</th>
                        <th>Giá sau KM</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($promotion['details'])): ?>
                        <tr>
                            <td colspan="6" class="text-center">Chưa có sản phẩm nào trong khuyến mãi này</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($promotion['details'] as $detail):
                            $discountedPrice = $detail['gia_goc'] * (1 - $detail['tilegiamgia'] / 100);
                        ?>
                            <tr>
                                <td><?= $detail['sanpham_id'] ?></td>
                                <td><?= htmlspecialchars($detail['tenSanPham']) ?></td>
                                <td><?= number_format($detail['gia_goc'], 0, ',', '.') ?>đ</td>
                                <td><?= number_format($detail['soluong'], 0, ',', '.') ?></td>
                                <td><?= $detail['tilegiamgia'] ?>%</td>
                                <td><?= number_format($discountedPrice, 0, ',', '.') ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($subpage === 'edit' && $promotion && $canEdit): ?>
    <!-- Edit Form -->
    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Chỉnh sửa khuyến mãi</h4>
            <a href="?page=employee_promotions" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <form method="POST" action="index.php?page=employee_promotions&action=update">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            <input type="hidden" name="promotion_id" value="<?= $promotion['khuyenmai_id'] ?>">

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Tên khuyến mãi <span class="text-danger">*</span></label>
                    <input type="text" name="ten" class="form-control"
                        value="<?= htmlspecialchars($promotion['ten']) ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                    <input type="date" name="ngaybatdau" class="form-control"
                        value="<?= $promotion['ngaybatdau'] ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                    <input type="date" name="ngayketthuc" class="form-control"
                        value="<?= $promotion['ngayketthuc'] ?>" required>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật thông tin
                </button>
            </div>
        </form>

        <hr class="my-4">

        <!-- Manage Products in Promotion -->
        <h5 class="mb-3">Quản lý sản phẩm khuyến mãi</h5>

        <button class="btn btn-success mb-3" onclick="showAddProductModal()">
            <i class="fas fa-plus"></i> Thêm sản phẩm
        </button>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID Sản phẩm</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá gốc</th>
                        <th>Số lượng KM</th>
                        <th>Tỉ lệ giảm giá (%)</th>
                        <th>Giá sau KM</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($promotion['details'])): ?>
                        <tr>
                            <td colspan="7" class="text-center">Chưa có sản phẩm nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($promotion['details'] as $detail):
                            $discountedPrice = $detail['gia_goc'] * (1 - $detail['tilegiamgia'] / 100);
                        ?>
                            <tr>
                                <td><?= $detail['sanpham_id'] ?></td>
                                <td><?= htmlspecialchars($detail['tenSanPham']) ?></td>
                                <td><?= number_format($detail['gia_goc'], 0, ',', '.') ?>đ</td>
                                <td><?= number_format($detail['soluong'], 0, ',', '.') ?></td>
                                <td><?= $detail['tilegiamgia'] ?>%</td>
                                <td><?= number_format($discountedPrice, 0, ',', '.') ?>đ</td>
                                <td>
                                    <form method="POST" action="index.php?page=employee_promotions&action=detail_delete"
                                        onsubmit="return confirm('Xác nhận xóa sản phẩm khỏi khuyến mãi?');" style="display:inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                        <input type="hidden" name="ctkm_id" value="<?= $detail['ctkm_id'] ?>">
                                        <input type="hidden" name="khuyenmai_id" value="<?= $promotion['khuyenmai_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($subpage === 'add' && $canEdit): ?>
    <!-- Add New Promotion Form -->
    <div class="table-card">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="mb-1">Thêm khuyến mãi mới</h4>
                <small class="text-muted">Nhập thông tin khuyến mãi và chọn sản phẩm tham gia</small>
            </div>
            <a href="?page=employee_promotions" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Quay lại</span>
            </a>
        </div>

        <form method="POST" action="index.php?page=employee_promotions&action=save" id="addPromotionForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div class="bg-light border border-light rounded-2 p-4 mb-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Tên khuyến mãi <span class="text-danger">*</span></label>
                        <input type="text" name="ten" class="form-control form-control" placeholder="Ví dụ: Khuyến mãi mùa hè 2024" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" name="ngaybatdau" class="form-control" required>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label fw-semibold">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date" name="ngayketthuc" class="form-control" required>
                    </div>
                </div>
            </div>

            <h5 class="mb-3 fw-semibold"><i class="fas fa-cube text-primary me-2"></i>Sản phẩm khuyến mãi</h5>

            <div class="row g-3">
                <!-- Left: Product Table -->
                <div class="col-12 col-xl-7">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                                <h6 class="mb-0"><i class="fas fa-list text-primary me-2"></i>Danh sách sản phẩm</h6>
                                <input type="text" class="form-control form-control-sm" style="max-width: 180px;"
                                    id="productTableSearch" placeholder="Tìm kiếm...">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                                <table class="table table-sm table-hover mb-0" id="productsTable">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th width="45" class="text-center"></th>
                                            <th width="65" class="d-none d-lg-table-cell">ID</th>
                                            <th width="fit-content">Tên sản phẩm</th>
                                            <th width="85" class="d-none d-md-table-cell text-end">Giá</th>
                                            <th width="70" class="d-none d-lg-table-cell text-end">Kho</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Get all products for display
                                        $allProducts = ProductModel::getAllProducts();
                                        if (!empty($allProducts)):
                                            foreach ($allProducts as $prod):
                                                $imagePath = 'assets/images/products/' . $prod['hinhanh'];
                                        ?>
                                                <tr class="align-middle">
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input product-checkbox cursor-pointer"
                                                            data-id="<?= $prod['sanpham_id'] ?>"
                                                            data-name="<?= htmlspecialchars($prod['name']) ?>"
                                                            data-price="<?= $prod['gia'] ?>"
                                                            data-image="<?= htmlspecialchars($prod['hinhanh']) ?>">
                                                    </td>
                                                    <td class="d-none d-lg-table-cell small text-muted"><?= $prod['sanpham_id'] ?></td>
                                                    <td class="d-flex align-items-center gap-2">
                                                        <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($prod['name']) ?>"
                                                            class="rounded-2" style="width: 55px; height: 55px; object-fit: cover; border: 1px solid #dee2e6;"
                                                            onerror="this.src='assets/images/products/defaultProduct_2.png'">

                                                        <div class="fw-500 d-none d-md-block"><?= htmlspecialchars($prod['name']) ?></div>

                                                        <div class="d-block d-md-none ms-2">
                                                            <div class="fw-500"><?= htmlspecialchars($prod['name']) ?></div>

                                                            <small class="text-muted ">
                                                                Giá: <?= number_format($prod['gia'], 0, ',', '.') ?>đ <br> Kho: <?= $prod['soluongton'] ?>
                                                            </small><br>
                                                            <small class="text-muted">
                                                                ID: <?= $prod['sanpham_id'] ?>
                                                            </small><br>
                                                        </div>

                                                    </td>
                                                    <td class="d-none d-md-table-cell text-end"><?= number_format($prod['gia'], 0, ',', '.') ?>đ</td>
                                                    <td class="d-none d-lg-table-cell text-end"><?= $prod['soluongton'] ?></td>
                                                </tr>
                                            <?php
                                            endforeach;
                                        else:
                                            ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">Không có sản phẩm nào</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Selected Products with Details -->
                <div class="col-12 col-xl-5">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);">
                            <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Sản phẩm đã chọn (<span id="selectedCount">0</span>)</h6>
                        </div>
                        <div class="card-body p-3">
                            <div id="selectedProductsList" style="max-height: 450px; overflow-y: auto;">
                                <div class="text-muted text-center py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <p>Chưa chọn sản phẩm nào</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column-reverse flex-sm-row justify-content-between gap-2 mt-4 pt-3 border-top">
                <a href="?page=employee_promotions" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i><span class="d-none d-sm-inline">Hủy bỏ</span>
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i><span class="d-none d-sm-inline">Tạo khuyến mãi</span>
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Modal: Add Product to Promotion (for edit page) -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="index.php?page=employee_promotions&action=detail_add">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm sản phẩm vào khuyến mãi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <input type="hidden" name="khuyenmai_id" value="<?= $promotion['khuyenmai_id'] ?? '' ?>">

                    <div class="mb-3">
                        <label class="form-label">Tìm sản phẩm</label>
                        <input type="text" class="form-control" id="productSearchInput" placeholder="Tìm theo tên hoặc ID...">
                        <div id="productSearchResults" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ID Sản phẩm <span class="text-danger">*</span></label>
                        <input type="number" name="sanpham_id" id="selectedProductId" class="form-control" required readonly>
                        <div id="selectedProductName" class="text-muted mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số lượng khuyến mãi <span class="text-danger">*</span></label>
                        <input type="number" name="soluong" class="form-control" required min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tỉ lệ giảm giá (%) <span class="text-danger">*</span></label>
                        <input type="number" name="tilegiamgia" class="form-control" required min="0" max="100" step="0.01">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add New Promotion (for list page) -->
<div class="modal fade" id="addPromotionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="GET">
                <input type="hidden" name="page" value="employee_promotions">
                <input type="hidden" name="subpage" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm khuyến mãi mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn sẽ được chuyển đến trang thêm khuyến mãi mới.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tiếp tục</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const q = this.value.toLowerCase();
                document.querySelectorAll('table tbody tr').forEach(tr => {
                    if (tr.querySelector('td[colspan]')) return; // Skip empty rows
                    tr.style.display = (q === '' || tr.textContent.toLowerCase().includes(q)) ? '' : 'none';
                });
            });
        }

        // Product search in modal
        const productSearchInput = document.getElementById('productSearchInput');
        if (productSearchInput) {
            let searchTimeout;
            productSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();

                if (searchTerm.length < 1) {
                    document.getElementById('productSearchResults').innerHTML = '';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch('index.php?page=api&action=search_products&q=' + encodeURIComponent(searchTerm))
                        .then(response => response.json())
                        .then(data => {
                            const resultsDiv = document.getElementById('productSearchResults');
                            resultsDiv.innerHTML = '';

                            if (data.products && data.products.length > 0) {
                                data.products.forEach(product => {
                                    const item = document.createElement('a');
                                    item.href = '#';
                                    item.className = 'list-group-item list-group-item-action';
                                    item.innerHTML = `<strong>${product.sanpham_id}</strong> - ${product.tenSanPham} 
                                                  <span class="text-muted">(${Number(product.gia).toLocaleString('vi-VN')}đ)</span>`;
                                    item.onclick = (e) => {
                                        e.preventDefault();
                                        document.getElementById('selectedProductId').value = product.sanpham_id;
                                        document.getElementById('selectedProductName').textContent = product.tenSanPham;
                                        resultsDiv.innerHTML = '';
                                        productSearchInput.value = '';
                                    };
                                    resultsDiv.appendChild(item);
                                });
                            } else {
                                resultsDiv.innerHTML = '<div class="list-group-item">Không tìm thấy sản phẩm</div>';
                            }
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                        });
                }, 300);
            });
        }
    });

    function showAddPromotionModal() {
        window.location.href = '?page=employee_promotions&subpage=add';
    }

    function showAddProductModal() {
        const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
        modal.show();
    }

    // Product selection handler for add promotion page
    document.addEventListener('DOMContentLoaded', function() {
        const selectedProducts = new Map(); // Store selected products with their details

        // Search in product table
        const productTableSearch = document.getElementById('productTableSearch');
        if (productTableSearch) {
            productTableSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#productsTable tbody tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Handle product checkbox changes
        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const productId = this.dataset.id;
                const productName = this.dataset.name;
                const productPrice = this.dataset.price;

                if (this.checked) {
                    // Add product to selected list
                    selectedProducts.set(productId, {
                        id: productId,
                        name: productName,
                        price: productPrice,
                        quantity: 0,
                        discount: 0
                    });
                } else {
                    // Remove product from selected list
                    selectedProducts.delete(productId);
                }

                updateSelectedProductsList();
            });
        });

        function updateSelectedProductsList() {
            const container = document.getElementById('selectedProductsList');
            const countSpan = document.getElementById('selectedCount');

            countSpan.textContent = selectedProducts.size;

            if (selectedProducts.size === 0) {
                container.innerHTML = '<p class="text-muted text-center py-4">Chưa chọn sản phẩm nào</p>';
                return;
            }

            let html = '';
            selectedProducts.forEach((product, id) => {
                html += `
                <div class="border rounded p-2 mb-2 bg-light" id="selected-${id}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong class="text-primary">#${id}</strong> - ${product.name}
                            <div class="text-muted small">Giá: ${Number(product.price).toLocaleString('vi-VN')}đ</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSelectedProduct('${id}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small mb-1">Số lượng KM</label>
                            <input type="number" class="form-control form-control-sm" 
                                   name="soluong[]" min="0" value="${product.quantity}"
                                   onchange="updateProductQuantity('${id}', this.value)" required>
                            <input type="hidden" name="sanpham_id[]" value="${id}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small mb-1">Giảm giá (%)</label>
                            <input type="number" class="form-control form-control-sm" 
                                   name="tilegiamgia[]" min="0" max="100" step="0.01" value="${product.discount}"
                                   onchange="updateProductDiscount('${id}', this.value)" required>
                        </div>
                    </div>
                    <div class="mt-2 small">
                        <strong>Giá sau KM:</strong> 
                        <span class="text-success" id="price-${id}">
                            ${calculateDiscountedPrice(product.price, product.discount)}
                        </span>
                    </div>
                </div>
            `;
            });

            container.innerHTML = html;
        }

        function calculateDiscountedPrice(price, discount) {
            const discounted = price * (1 - discount / 100);
            return Number(discounted).toLocaleString('vi-VN') + 'đ';
        }

        // Make functions global so they can be called from inline handlers
        window.removeSelectedProduct = function(productId) {
            selectedProducts.delete(productId);
            // Uncheck the checkbox
            const checkbox = document.querySelector(`.product-checkbox[data-id="${productId}"]`);
            if (checkbox) checkbox.checked = false;
            updateSelectedProductsList();
        };

        window.updateProductQuantity = function(productId, quantity) {
            if (selectedProducts.has(productId)) {
                selectedProducts.get(productId).quantity = quantity;
            }
        };

        window.updateProductDiscount = function(productId, discount) {
            if (selectedProducts.has(productId)) {
                selectedProducts.get(productId).discount = discount;
                // Update displayed price
                const priceElement = document.getElementById(`price-${productId}`);
                if (priceElement) {
                    const product = selectedProducts.get(productId);
                    priceElement.textContent = calculateDiscountedPrice(product.price, discount);
                }
            }
        };
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/employeeLayout.php';
