<?php

// Use preparer-provided account data when available
$customer = $account_customer ?? $customer;
$order_count = is_array($account_orders) ? count($account_orders) : 0;
$success = $success ?? '';
$error   = $error ?? '';
// Check for open_tab flash (set after redirects) and clear it
$open_tab = $_SESSION['open_tab'] ?? '';
if (!empty($_SESSION['open_tab'])) unset($_SESSION['open_tab']);

// Ensure display name
$customer['ho_ten'] = trim(($customer['ho'] ?? '') . ' ' . ($customer['tendem'] ?? '') . ' ' . ($customer['ten'] ?? ''));
?>

<div class="container mt-5 pb-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div id="account-info" class="card border-0 shadow-sm rounded-3">
                <div class="card-body text-center p-4">
                    <!-- Avatar -->
                    <div class="avatar bg-primary text-white rounded-circle mb-3 d-flex 
                        align-items-center justify-content-center mx-auto"
                        style="width: 90px; height: 90px; font-size: 2rem; font-weight: bold;">
                        <?= strtoupper(substr($customer['ho'] ?? '', 0, 1) . substr($customer['ten'] ?? '', 0, 1)) ?>
                    </div>

                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($customer['ho_ten']) ?></h5>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($customer['email'] ?? '') ?></p>
                </div>

                <div class="list-group list-group-flush rounded-bottom">
                    <a href="#" class="list-group-item list-group-item-action active" data-tab="info">
                        <i class="bi bi-person-circle me-2"></i> Thông tin
                    </a>
                    <a href="index.php?page=orders" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-basket me-2"></i>Đơn hàng</span>
                        <span class="badge bg-primary rounded-pill"><?= intval($order_count) ?></span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" data-tab="change-password">
                        <i class="bi bi-key-fill me-2"></i> Đổi mật khẩu
                    </a>
                    <form method="post" class="mb-0">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <button type="submit" name="logout_exc" class="list-group-item list-group-item-action text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Nội dung chính -->
        <div class="col-lg-9">
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div id="account-information" class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white">
                    <h5 class="m-2 fw-bold">Thông tin cá nhân</h5>
                </div>

                <div class="card-body p-4">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">Họ</label>
                                <input name="ho" class="form-control" value="<?= htmlspecialchars($customer['ho'] ?? '') ?>" placeholder="Họ" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tên đệm</label>
                                <input name="tendem" class="form-control" value="<?= htmlspecialchars($customer['tendem'] ?? '') ?>" placeholder="Tên đệm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tên</label>
                                <input name="ten" class="form-control" value="<?= htmlspecialchars($customer['ten'] ?? '') ?>" placeholder="Tên" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SĐT</label>
                                <input name="sdt" class="form-control" value="<?= htmlspecialchars($customer['sdt'] ?? '') ?>" placeholder="Số điện thoại">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ngày sinh</label>
                                <input type="date" name="ngaysinh" class="form-control" value="<?= htmlspecialchars(!empty($customer['ngaysinh']) ? date('Y-m-d', strtotime($customer['ngaysinh'])) : '') ?>">
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-5 py-2 mt-3 fw-bold">
                                    Cập nhật
                                </button>
                            </div>

                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- Địa chỉ giao hàng -->
                    <div class="mb-3" id="addresses-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Địa chỉ giao hàng</h6>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                <i class="bi bi-plus-circle me-1"></i> Thêm địa chỉ
                            </button>
                        </div>
                    <?php 
                    $addresses = $customer_addresses ?? [];
                    if (empty($addresses)): 
                    ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-geo-alt" style="font-size: 3rem;"></i>
                            <p class="mt-3">Chưa có địa chỉ giao hàng nào</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($addresses as $addr): ?>
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                                                    Địa chỉ <?= htmlspecialchars($addr['dcgh_id']) ?>
                                                </h6>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                                        onclick="editAddress(<?= $addr['dcgh_id'] ?>, '<?= htmlspecialchars($addr['diachi'], ENT_QUOTES) ?>')">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="deleteAddress(<?= $addr['dcgh_id'] ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="text-muted mb-0 small"><?= htmlspecialchars($addr['diachi']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Đổi mật khẩu (nhúng trong trang account) -->
            <div id="account-change-password" class="card border-0 shadow-sm rounded-3" style="display:none;">
                <div class="card-header bg-white">
                    <h5 class="m-2 fw-bold">Đổi mật khẩu</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="index.php?page=change_password">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" name="new_password2" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-5 py-2 mt-3 fw-bold">Cập nhật mật khẩu</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.list-group a[data-tab]');
        const panels = {
            'info': document.getElementById('account-information'),
            'change-password': document.getElementById('account-change-password')
        };

        function activate(name) {
            tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === name));
            Object.keys(panels).forEach(k => {
                if (!panels[k]) return;
                panels[k].style.display = (k === name) ? 'block' : 'none';
            });
            const el = panels[name]; if (el) el.scrollIntoView({behavior:'smooth', block:'start'});
        }

        tabs.forEach(t => t.addEventListener('click', function (e) {
            e.preventDefault();
            activate(this.dataset.tab);
        }));

        // If server requested opening a tab, activate it
        const openTab = <?= json_encode($open_tab) ?>;
        if (openTab && panels[openTab]) activate(openTab);
    });

    // Hàm xóa địa chỉ
    function deleteAddress(id) {
        if (confirm('Bạn có chắc muốn xóa địa chỉ này?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <input type="hidden" name="delete_address" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Hàm sửa địa chỉ
    function editAddress(id, address) {
        document.getElementById('edit_dcgh_id').value = id;
        document.getElementById('edit_diachi').value = address;
        new bootstrap.Modal(document.getElementById('editAddressModal')).show();
    }

    // Scroll to addresses section if there's a success message
    window.addEventListener('load', function() {
        const alerts = document.querySelectorAll('.alert');
        if (alerts.length > 0) {
            const addressSection = document.getElementById('addresses-section');
            if (addressSection) {
                setTimeout(() => {
                    addressSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        }
    });
    </script>

    <!-- Modal Thêm địa chỉ -->
    <div class="modal fade" id="addAddressModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm địa chỉ giao hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="add_address" value="1">
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ chi tiết</label>
                            <textarea name="diachi" class="form-control" rows="3" 
                                placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Thêm địa chỉ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Sửa địa chỉ -->
    <div class="modal fade" id="editAddressModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa địa chỉ giao hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="edit_address" value="1">
                        <input type="hidden" name="dcgh_id" id="edit_dcgh_id">
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ chi tiết</label>
                            <textarea name="diachi" id="edit_diachi" class="form-control" rows="3" 
                                placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
