<?php
requireEmployeeLogin();

// Use preparer when available; fallback to legacy logic
$subpage = $_GET['subpage'] ?? 'list';
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$vars = EmployeePageController::prepareEmployeeProducts($subpage, $product_id);
if (is_array($vars)) extract($vars);

$page_title = 'Quản lý Sản phẩm';
ob_start();

$loaisachs = LoaiSachModel::getAll() ?? [];


?>

<div class="top-bar">
    <h2 class="mb-0">Quản lý Sản phẩm</h2>
    <div class="d-flex gap-2">
        <a href="?page=employee_products&subpage=add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Thêm sản phẩm mới
        </a>
    </div>
</div>

<?php if ($subpage === 'add' || $subpage === 'edit'): ?>
    <!-- Form thêm/sửa sản phẩm -->
    <?php
    $product_id = $_GET['id'] ?? null;
    if ($subpage === 'add') {
        $product = null; // Reset for add mode
    }
    // Xác định hiển thị form nào dựa vào danhmucSP_id
    // Theo yêu cầu: khi `danhmucSP_id` === 1 thì là Sách
    $isSach = function_exists('is_book_category') ? is_book_category($product['danhmucSP_id'] ?? 0) : ((int)($product['danhmucSP_id'] ?? 0) === 1);
    $isOther = !$isSach; // other categories (not book)
    ?>
    <div class="table-card">
        <h4><?= $subpage === 'add' ? 'Thêm' : 'Sửa' ?> Sản phẩm</h4>

        <form method="POST" enctype="multipart/form-data" action="index.php?page=employee_products&action=<?= $subpage === 'add' ? 'save' : 'update' ?><?= $product_id ? '&id=' . $product_id : '' ?>&subpage=<?= $subpage ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div class="mb-3">
                <label class="form-label">Danh mục sản phẩm <span class="text-danger">*</span></label>
                <select id="danhmucSP_id" name="danhmucSP_id" class="form-select" required data-book-id="1" data-stationery-id="2">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($catalogs as $catalog): ?>
                        <option value="<?= $catalog['danhmucSP_id'] ?>" <?= ($product['danhmucSP_id'] ?? '') == $catalog['danhmucSP_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($catalog['tenDanhMuc']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <!-- Tên sản phẩm / VPP -->
                    <div class="mb-3">
                        <label class="form-label">Tên chung <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                        <div class="form-text">Bạn có thể nhập tên chung vào đây; hoặc dùng các trường chuyên biệt bên dưới.</div>
                    </div>

                    <!-- Sách: trường chuyên biệt -->
                    <div id="sachFields" style="display: <?= $isSach ? 'block' : 'none' ?>;">
                        <div class="mb-3">
                            <label class="form-label">Tiêu đề sách</label>
                            <input id="tenSachInput" type="text" name="tenSach" class="form-control" value="<?= htmlspecialchars($product['tenSach'] ?? '') ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nhà xuất bản</label>
                                <select name="nhaxuatban_id" class="form-select">
                                    <option value="">-- Không chọn --</option>
                                    <?php if (function_exists('PublisherModel::getAllPublishers') || class_exists('PublisherModel')): ?>
                                        <?php foreach (PublisherModel::getAllPublishers() as $pub): ?>
                                            <option value="<?= $pub['nhaxuatban_id'] ?>" <?= (isset($product['nhaxuatban_id']) && $product['nhaxuatban_id'] == $pub['nhaxuatban_id']) ? 'selected' : '' ?>><?= htmlspecialchars($pub['ten'] ?? $pub['name'] ?? '') ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tác giả</label>
                                <select name="tacgia_id" class="form-select">
                                    <option value="">-- Không chọn --</option>
                                    <?php if (class_exists('TacGiaModel') || class_exists('AuthorsModel') || class_exists('AuthorModel') || class_exists('Authors')): ?>
                                        <?php
                                        // try a few common model names
                                        $authors = [];
                                        if (class_exists('AuthorsModel')) $authors = AuthorsModel::getAllAuthors();
                                        foreach ($authors as $a): ?>
                                            <option value="<?= $a['tacgia_id'] ?>" <?= (isset($product['tacgia_id']) && $product['tacgia_id'] == $a['tacgia_id']) ? 'selected' : '' ?>><?= htmlspecialchars(trim(($a['ho'] ?? '') . ' ' . ($a['tendem'] ?? '') . ' ' . ($a['ten'] ?? ''))) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Loại sách</label>
                            <select name="loaisach_code" class="form-select">
                                <option value="">-- Không chọn --</option>
                                <?php if (!empty($loaisachs) && is_array($loaisachs)): ?>
                                    <?php foreach ($loaisachs as $ls): ?>
                                        <option value="<?= htmlspecialchars($ls['loaisach_code']) ?>" <?= (isset($product['loaisach_code']) && $product['loaisach_code'] == $ls['loaisach_code']) ? 'selected' : '' ?>><?= htmlspecialchars($ls['tenLoaiSach']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Năm xuất bản</label>
                            <input type="number" name="namXB" class="form-control" value="<?= htmlspecialchars($product['namXB'] ?? '') ?>" min="1900" max="2100">
                        </div>
                    </div>

                    <!-- VPP: trường chuyên biệt -->
                    <div id="vppFields" style="display: <?= $isOther ? 'block' : 'none' ?>;">
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm (khác)</label>
                            <input id="tenVPPInput" type="text" name="tenVPP" class="form-control" value="<?= htmlspecialchars($product['tenVPP'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="6"><?= htmlspecialchars($product['mo_ta'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Hình ảnh -->
                    <div class="mb-3">
                        <label class="form-label">Hình ảnh</label>
                        <div class="input-group mb-2">
                            <input type="file" name="hinhanh_file" id="hinhanhFileInput" class="form-control" accept="image/*">
                            <button type="button" class="btn btn-outline-secondary" id="previewImageBtn">Xem</button>
                        </div>
                        <div id="imagePreview" class="mt-2">
                            <?php if (!empty($product['hinhanh'])): ?>
                                <img src="./assets/images/products/<?= htmlspecialchars($product['hinhanh']) ?>" style="max-width:150px; max-height:150px; object-fit:cover; border:1px solid #ddd; padding:4px;">
                            <?php endif; ?>
                        </div>
                        <div class="form-text">Tải lên file ảnh; tên file sẽ được đặt tự động theo định dạng <mã sp>-<danh mục>.<đuôi>.</div>
                    </div>

                    <!-- Giá, số lượng, nhà cung cấp -->

                    <div class="mb-3">
                        <label class="form-label">Giá (₫)</label>
                        <input type="number" name="gia" class="form-control" value="<?= $product['gia'] ?? '' ?>" min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số lượng tồn</label>
                        <input type="number" name="soluongton" class="form-control" value="<?= $product['soluongton'] ?? 0 ?>" min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nhà cung cấp</label>
                        <select name="nhacungcap_id" class="form-select">
                            <?php foreach (ProviderModel::getAllProviders() as $provider): ?>
                                <option value="<?= $provider['nhacungcap_id'] ?>" <?= ($product['nhacungcap_id'] ?? '') == $provider['nhacungcap_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($provider['ten']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="?page=employee_products" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary"><?= $subpage === 'add' ? 'Thêm sản phẩm' : 'Cập nhật sản phẩm' ?></button>
            </div>
        </form>
    </div>
<?php else: ?>

   

    <!-- Danh sách sản phẩm -->
    <div class="table-card">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Danh sách sản phẩm</h4>

            <form method="GET" action="index.php" class="d-flex" style="width:300px;">
                <input type="hidden" name="page" value="employee_products">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <!-- <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button> -->
                </div>
            </form>


            <!-- <input type="text" class="form-control" placeholder="Tìm kiếm..." style="width: 300px;" id="searchInput"> -->
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Đã bán</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $prod): ?>
                        <tr>
                            <td><?= $prod['sanpham_id'] ?></td>
                            <td class="swipe-actions">
                                <div class="d-flex align-items-center">
                                    <?php if ($prod['hinhanh']): ?>
                                        <img src="./assets/images/products/<?= htmlspecialchars($prod['hinhanh']) ?>"
                                            alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px;"
                                            onerror="this.src='assets/images/products/defaultProduct.png'">

                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($prod['name'] ?? 'N/A') ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($prod['provider_name'] ?? '') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?= product_category_badge_class($prod['danhmucSP_id'] ?? 0) ?>">
                                    <?= htmlspecialchars(get_category_name($prod['danhmucSP_id'] ?? 0) ?: (CategoriesModel::getCategory($prod['danhmucSP_id'])[0]['tenDanhMuc'] ?? 'N/A')) ?>
                                </span>
                            </td>
                            <td><?= number_format($prod['gia'], 0, ',', '.') ?>₫</td>
                            <td><?= number_format($prod['soluongton'] ?? 0, 0, ',', '.') ?></td>
                            <td><?= number_format($prod['soluongban'] ?? 0, 0, ',', '.') ?></td>
                            <td>
                                <a href="?page=employee_products&subpage=edit&id=<?= $prod['sanpham_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="index.php?page=employee_products&action=delete" style="display:inline-block; margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                    <input type="hidden" name="id" value="<?= $prod['sanpham_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($pagination) && intval($pagination['total_pages'] ?? 0) > 1): ?>
            <?php
            $qp = $_GET;
            $qp['page'] = 'employee_products';
            $currentP = intval($pagination['current']);
            $tp = intval($pagination['total_pages']);
            $start = max(1, $currentP - 2);
            $end = min($tp, $currentP + 2);
            ?>
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination">
                    <li class="page-item <?= $currentP <= 1 ? 'disabled' : '' ?>">
                        <?php $qp['p'] = max(1, $currentP - 1); ?>
                        <a class="page-link" href="index.php?<?= htmlspecialchars(http_build_query($qp)) ?>">&laquo; Trước</a>
                    </li>
                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <?php $qp['p'] = $i; ?>
                        <li class="page-item <?= $i === $currentP ? 'active' : '' ?>"><a class="page-link" href="index.php?<?= htmlspecialchars(http_build_query($qp)) ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                    <li class="page-item <?= $currentP >= $tp ? 'disabled' : '' ?>">
                        <?php $qp['p'] = min($tp, $currentP + 1); ?>
                        <a class="page-link" href="index.php?<?= htmlspecialchars(http_build_query($qp)) ?>">Tiếp &raquo;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
    // Toggle fields based on danh mục sản phẩm

        document.getElementById('danhmucSP_id')?.addEventListener('change', function() {
            const danhmucSP_id = parseInt(this.value || '0', 10);
            // Numerical mapping: 1 = Sách, 2 = VPP (stationery)
            const bookId = parseInt(this.dataset.bookId || '1', 10);
            // const bookId= parseInt( <?= is_book_category($product['danhmucSP_id'] ?? 0) ?>, 10);
            const stationeryId = parseInt(this.dataset.stationeryId || '2', 10);
            const isSach = danhmucSP_id === bookId;
            const isOther = !isSach; // other categories

            document.getElementById('sachFields').style.display = isSach ? 'block' : 'none';
            document.getElementById('vppFields').style.display = isOther ? 'block' : 'none';

        // Set required attribute based on visibility
        const tenSachInput = document.getElementById('tenSachInput');
        const tenVPPInput = document.getElementById('tenVPPInput');

        if (tenSachInput) {
            tenSachInput.required = isSach;
            if (!isSach) tenSachInput.value = '';
        }
        if (tenVPPInput) {
            tenVPPInput.required = isOther;
            if (!isOther) tenVPPInput.value = '';
        }
    });

    // Trigger on page load to set initial state and add image preview
    document.addEventListener('DOMContentLoaded', function() {
        const danhmucSelect = document.getElementById('danhmucSP_id');
        // Always dispatch change to ensure correct visibility even when empty (add mode)
        if (danhmucSelect) danhmucSelect.dispatchEvent(new Event('change'));

        const hinhanhFileInput = document.getElementById('hinhanhFileInput');
        const previewBtn = document.getElementById('previewImageBtn');
        const imagePreviewEl = document.getElementById('imagePreview');

        function showPreviewFromFile(file) {
            imagePreviewEl.innerHTML = '';
            if (!file) return;
            const url = URL.createObjectURL(file);
            const img = document.createElement('img');
            img.src = url;
            img.alt = file.name || 'preview';
            img.style.maxWidth = '150px';
            img.style.maxHeight = '150px';
            img.style.objectFit = 'cover';
            img.style.border = '1px solid #ddd';
            img.style.padding = '4px';
            const a = document.createElement('a');
            a.href = url;
            a.target = '_blank';
            a.appendChild(img);
            imagePreviewEl.appendChild(a);
        }

        if (previewBtn) {
            previewBtn.addEventListener('click', function() {
                if (hinhanhFileInput && hinhanhFileInput.files && hinhanhFileInput.files[0]) {
                    showPreviewFromFile(hinhanhFileInput.files[0]);
                }
            });

            if (hinhanhFileInput) {
                hinhanhFileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) showPreviewFromFile(this.files[0]);
                });
            }
        }

        // Set required attributes consistent with visible fields on load
        const tenSachInput = document.getElementById('tenSachInput');
        const tenVPPInput = document.getElementById('tenVPPInput');
        const dmVal = danhmucSelect ? parseInt(danhmucSelect.value || '0', 10) : 0;
        const bookId = danhmucSelect ? parseInt(danhmucSelect.dataset.bookId || '1', 10) : 1;
        const isSach = dmVal === bookId;
        const isOther = !isSach;
        if (tenSachInput) tenSachInput.required = isSach;
        if (tenVPPInput) tenVPPInput.required = isOther;
    });
</script>

<?php
$content = ob_get_clean();
include 'app/views/employee/employeeLayout.php';
?>