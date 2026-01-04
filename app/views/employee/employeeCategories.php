<?php

$subpage = $_GET['subpage'] ?? 'list';
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$vars = EmployeePageController::prepareEmployeeCategories($subpage, $category_id);
if (is_array($vars)) extract($vars);

$page_title = 'Quản lý Danh Mục Sản Phẩm';
ob_start();

?>
<div class="top-bar">
    <h2 class="mb-0">Quản lý Danh Mục Sản Phẩm</h2>
    <button class="btn btn-primary" id="btnAddCategory" data-bs-toggle="modal" data-bs-target="#categoryModal">Thêm danh mục</button>
</div>

<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Danh sách danh mục</h4>
        <div class="d-flex gap-2">
            <input type="text" class="form-control" placeholder="Tìm kiếm..." style="width: 300px;" id="searchInput">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($categories ?? []) as $cat): ?>
                    <tr>
                        <td><?= (int)($cat['danhmucSP_id'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($cat['tenDanhMuc'] ?? '') ?></td>
                        <td><?= htmlspecialchars($cat['mo_ta'] ?? '') ?></td>
                        <td class="swipe-actions">
                            <button class="btn btn-sm btn-outline-primary btn-edit-cat"
                                data-id="<?= (int)($cat['danhmucSP_id'] ?? 0) ?>"
                                data-ten="<?= htmlspecialchars($cat['tenDanhMuc'] ?? '', ENT_QUOTES) ?>"
                                data-mota="<?= htmlspecialchars($cat['mo_ta'] ?? '', ENT_QUOTES) ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="index.php?page=employee_categories&action=delete" onsubmit="return confirm('Xác nhận xóa danh mục này?');" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <input type="hidden" name="id" value="<?= (int)($cat['danhmucSP_id'] ?? 0) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= render_pagination_controls($pagination ?? null, 'employee_categories') ?>
</div>

<!-- Modal: Add / Edit Category -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="categoryForm" method="POST" action="index.php?page=employee_categories&action=save">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <input type="hidden" name="id" id="cat_id" value="">
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục</label>
                        <input name="tenDanhMuc" id="cat_ten" class="form-control" required>
                    </div>
                     <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="mo_ta" id="cat_mo_ta" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="catSubmit">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const search = document.getElementById('searchInput');
        search?.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('table tbody tr').forEach(tr => {
                tr.style.display = (q === '' || tr.textContent.toLowerCase().includes(q)) ? '' : 'none';
            });
        });

        document.querySelectorAll('.btn-edit-cat').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const ten = this.dataset.ten;
                const mota = this.dataset.mota || '';
                document.getElementById('cat_id').value = id;
                document.getElementById('cat_ten').value = ten;
                document.getElementById('cat_mo_ta').value = mota;
                document.getElementById('categoryForm').action = 'index.php?page=employee_categories&action=update';
                document.querySelector('#categoryModal .modal-title').textContent = 'Sửa danh mục';
                var modal = new bootstrap.Modal(document.getElementById('categoryModal'));
                modal.show();
            });
        });

        document.getElementById('btnAddCategory')?.addEventListener('click', function() {
            document.getElementById('cat_id').value = '';
            document.getElementById('cat_ten').value = '';
            document.getElementById('cat_mo_ta').value = '';
            document.getElementById('categoryForm').action = 'index.php?page=employee_categories&action=save';
            document.querySelector('#categoryModal .modal-title').textContent = 'Thêm danh mục';
        });
    });
</script>

<?php
$content = ob_get_clean();
include 'app/views/employee/employeeLayout.php';
?>
