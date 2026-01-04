<?php

// Use preparer when available; fallback to legacy logic
$subpage = $_GET['subpage'] ?? 'list';
$publisher_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$vars = EmployeePageController::prepareEmployeePublishers($subpage, $publisher_id);
if (is_array($vars)) extract($vars);

$page_title = 'Quản lý Nhà Xuất Bản';
ob_start();


?>
<div class="top-bar">
    <h2 class="mb-0">Quản lý Nhà Xuất Bản</h2>
    <button class="btn btn-primary" id="btnAddPublisher" data-bs-toggle="modal" data-bs-target="#publisherModal">Thêm nhà xuất bản</button>
</div>


<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Danh sách nhà xuất bản</h4>
        <div class="d-flex gap-2">
            <input type="text" class="form-control" placeholder="Tìm kiếm..." style="width: 300px;" id="searchInput">

        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên nhà xuất bản</th>
                    <th>Địa chỉ</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publishers as $publisher): ?>
                    <tr>
                        <td><?= $publisher['nhaxuatban_id'] ?></td>
                        <td><?= htmlspecialchars($publisher['ten'] ?? '') ?></td>
                        <td><?= htmlspecialchars($publisher['diachi'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($publisher['sdt'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($publisher['email'] ?? 'N/A') ?></td>
                        <td class="swipe-actions">
                            <!-- <div class="btn-group" role="group">
                           
                        </div> -->
                            <button class="btn btn-sm btn-outline-primary btn-edit-pub"
                                data-id="<?= $publisher['nhaxuatban_id'] ?>"
                                data-ten="<?= htmlspecialchars($publisher['ten'] ?? '', ENT_QUOTES) ?>"
                                data-diachi="<?= htmlspecialchars($publisher['diachi'] ?? '', ENT_QUOTES) ?>"
                                data-sdt="<?= htmlspecialchars($publisher['sdt'] ?? '', ENT_QUOTES) ?>"
                                data-email="<?= htmlspecialchars($publisher['email'] ?? '', ENT_QUOTES) ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="index.php?page=employee_publishers&action=delete" onsubmit="return confirm('Xác nhận xóa nhà xuất bản này?');" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <input type="hidden" name="id" value="<?= $publisher['nhaxuatban_id'] ?>">
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
    <?= render_pagination_controls($pagination, 'employee_publishers') ?>
</div>

<!-- Modal: Add / Edit Publisher -->
<div class="modal fade" id="publisherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="publisherForm" method="POST" action="index.php?page=employee_publishers&action=save">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm nhà xuất bản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <input type="hidden" name="id" id="pub_id" value="">
                    <div class="mb-3">
                        <label class="form-label">Tên</label>
                        <input name="tenNhaXuatBan" id="pub_ten" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input name="diachi" id="pub_diachi" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SĐT</label>
                        <input name="sdt" id="pub_sdt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input name="email" id="pub_email" class="form-control" type="email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="pubSubmit">Lưu</button>
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

        document.querySelectorAll('.btn-edit-pub').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const ten = this.dataset.ten;
                const diachi = this.dataset.diachi;
                const sdt = this.dataset.sdt;
                const email = this.dataset.email;
                document.getElementById('pub_id').value = id;
                document.getElementById('pub_ten').value = ten;
                document.getElementById('pub_diachi').value = diachi;
                document.getElementById('pub_sdt').value = sdt;
                document.getElementById('pub_email').value = email;
                document.getElementById('publisherForm').action = 'index.php?page=employee_publishers&action=update';
                document.querySelector('#publisherModal .modal-title').textContent = 'Sửa nhà xuất bản';
                var modal = new bootstrap.Modal(document.getElementById('publisherModal'));
                modal.show();
            });
        });

        document.getElementById('btnAddPublisher')?.addEventListener('click', function() {
            document.getElementById('pub_id').value = '';
            document.getElementById('pub_ten').value = '';
            document.getElementById('pub_diachi').value = '';
            document.getElementById('pub_sdt').value = '';
            document.getElementById('pub_email').value = '';
            document.getElementById('publisherForm').action = 'index.php?page=employee_publishers&action=save';
            document.querySelector('#publisherModal .modal-title').textContent = 'Thêm nhà xuất bản';
        });
    });
</script>

<?php
$content = ob_get_clean();
include 'app/views/employee/employeeLayout.php';
?>