<?php

requireEmployeeLogin();

$subpage = $_GET['subpage'] ?? 'list';
$provider_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$vars = EmployeePageController::prepareEmployeeProviders($subpage, $provider_id);
if (is_array($vars)) extract($vars);

$page_title = 'Quản lý Nhà Cung Cấp';
ob_start();

?>
<div class="top-bar">
    <h2 class="mb-0">Quản lý Nhà Cung Cấp</h2>
    <button class="btn btn-primary" id="btnAddProvider" data-bs-toggle="modal" data-bs-target="#providerModal">Thêm nhà cung cấp</button>
</div>
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Danh sách nhà cung cấp</h4>
        <div class="d-flex gap-2">
            <input type="text" class="form-control" placeholder="Tìm kiếm..." style="width: 300px;" id="searchInput">

        </div>
    </div>


    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Địa chỉ</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($providers as $prov): ?>
                    <tr>
                        <td><?= $prov['nhacungcap_id'] ?></td>
                        <td><?= htmlspecialchars($prov['ten'] ?? '') ?></td>
                        <td><?= htmlspecialchars($prov['diachi'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($prov['sdt'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($prov['email'] ?? 'N/A') ?></td>
                        <td class="swipe-actions">
                            <!-- <div class="btn-group" role="group">
                              
                            </div> -->

                            <button class="btn btn-sm btn-outline-primary btn-edit-prov" data-id="<?= $prov['nhacungcap_id'] ?>"
                                data-ten="<?= htmlspecialchars($prov['ten'] ?? '', ENT_QUOTES) ?>"
                                data-diachi="<?= htmlspecialchars($prov['diachi'] ?? '', ENT_QUOTES) ?>"
                                data-sdt="<?= htmlspecialchars($prov['sdt'] ?? '', ENT_QUOTES) ?>"
                                data-email="<?= htmlspecialchars($prov['email'] ?? '', ENT_QUOTES) ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="index.php?page=employee_providers&action=delete"
                                onsubmit="return confirm('Xác nhận xóa nhà cung cấp này?');" style="display:inline-block; margin:0;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <input type="hidden" name="id" value="<?= $prov['nhacungcap_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= render_pagination_controls($pagination, 'employee_providers') ?>
</div>

<!-- Modal: Add / Edit Provider -->
<div class="modal fade" id="providerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="providerForm" method="POST" action="index.php?page=employee_providers&action=save">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm nhà cung cấp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <input type="hidden" name="id" id="prov_id" value="">
                    <div class="mb-3">
                        <label class="form-label">Tên</label>
                        <input name="ten" id="prov_ten" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input name="diachi" id="prov_diachi" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SĐT</label>
                        <input name="sdt" id="prov_sdt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input name="email" id="prov_email" class="form-control" type="email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="provSubmit">Lưu</button>
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

        document.querySelectorAll('.btn-edit-prov').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const ten = this.dataset.ten;
                const diachi = this.dataset.diachi;
                const sdt = this.dataset.sdt;
                const email = this.dataset.email;
                document.getElementById('prov_id').value = id;
                document.getElementById('prov_ten').value = ten;
                document.getElementById('prov_diachi').value = diachi;
                document.getElementById('prov_sdt').value = sdt;
                document.getElementById('prov_email').value = email;
                document.getElementById('providerForm').action = 'index.php?page=employee_providers&action=update';
                document.querySelector('#providerModal .modal-title').textContent = 'Sửa nhà cung cấp';
                var modal = new bootstrap.Modal(document.getElementById('providerModal'));
                modal.show();
            });
        });

        document.getElementById('btnAddProvider')?.addEventListener('click', function() {
            document.getElementById('prov_id').value = '';
            document.getElementById('prov_ten').value = '';
            document.getElementById('prov_diachi').value = '';
            document.getElementById('prov_sdt').value = '';
            document.getElementById('prov_email').value = '';
            document.getElementById('providerForm').action = 'index.php?page=employee_providers&action=save';
            document.querySelector('#providerModal .modal-title').textContent = 'Thêm nhà cung cấp';
        });
    });
</script>

<?php
$content = ob_get_clean();
include 'app/views/employee/employeeLayout.php';
?>