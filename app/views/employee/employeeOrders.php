<?php
requireEmployeeLogin();

// Use preparer when available; fallback to legacy logic
$subpage = $_GET['subpage'] ?? 'list';
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$vars = EmployeePageController::prepareEmployeeOrders($subpage, $order_id);
$canCreateAndApprove = EmployeeHelpers::canCreateAndApprove();
if (is_array($vars)) extract($vars);

$page_title = 'Quản lý Đơn hàng';
ob_start();
?>

<div class="top-bar">
    <h2 class="mb-0">Quản lý Đơn hàng</h2>
    <?php if ($canCreateAndApprove): ?>
        <a href="?page=employee_orders&subpage=create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo đơn hàng mới
        </a>
    <?php endif; ?>
</div>

<?php if ($subpage === 'create'): ?>
    <?php
    // Prepare data for form selects
    $customers = [];
    if (class_exists('CustomerModel')) {
        try {
            $customers = CustomerModel::getAllCustomers();
        } catch (Throwable $__) {
            $customers = [];
        }
    }
    $products = [];
    if (class_exists('ProductModel')) {
        try {
            $products = ProductModel::getAllProducts();
        } catch (Throwable $__) {
            $products = [];
        }
    }
    ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Tạo đơn hàng mới</strong>
            <a href="?page=employee_orders" class="btn btn-sm btn-secondary">Hủy</a>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?page=employee_orders&action=create" id="createOrderForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <div id="formErrors" class="mb-2"></div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Khách hàng</label>
                        <select id="customerSelect" name="customer_id" class="form-select">
                            <option value="0">-- Khách hàng mới --</option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?= $c['khachhang_id'] ?>"><?= htmlspecialchars(($c['ho'] ?? '') . ' ' . ($c['ten'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-8" id="newCustomerFields" style="display:none;">
                        <label class="form-label">Thông tin khách hàng mới</label>
                        <div class="row g-2">
                            <div class="col-md-4"><input name="new_name" class="form-control" placeholder="Họ và tên"></div>
                            <div class="col-md-4"><input name="new_email" type="email" class="form-control" placeholder="Email"></div>
                            <div class="col-md-4"><input name="new_phone" class="form-control" placeholder="SĐT"></div>
                            <div class="col-12 mt-2"><input name="new_address" class="form-control" placeholder="Địa chỉ giao hàng"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Sản phẩm</label>
                        <div class="table-responsive">
                            <table class="table table-sm" id="orderItemsTable">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th style="width:110px">Số lượng</th>
                                        <th style="width:140px">Đơn giá</th>
                                        <th style="width:140px">Thành tiền</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row d-none" id="templateItemRow">
                                        <td>
                                            <select name="product_id[]" class="form-select product-select">
                                                <option value="">-- Chọn sản phẩm --</option>
                                                <?php foreach ($products as $p): ?>
                                                    <?php $pname = $p['name'] ?? $p['tenSach'] ?? $p['tenVPP'] ?? 'N/A';
                                                    $pprice = floatval($p['gia'] ?? 0);
                                                    $pstock = intval($p['soluongton'] ?? 0); ?>
                                                    <option value="<?= $p['sanpham_id'] ?>" data-price="<?= $pprice ?>" data-stock="<?= $pstock ?>"><?= htmlspecialchars($pname) ?> — <?= number_format($pprice, 0, ',', '.') ?>đ — Kho: <?= $pstock ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input name="quantity[]" type="number" min="1" value="1" class="form-control qty-input"></td>
                                        <td>
                                            <input name="price[]" type="number" step="0.01" min="0" value="0" class="form-control form-control-sm price-input text-end">
                                        </td>
                                        <td class="line-total text-end">0</td>
                                        <td class="text-muted small"><span class="stock-label" data-stock="0">Kho: 0</span></td>
                                        <td class="swipe-actions"><button type="button" class="btn btn-sm btn-danger remove-item">×</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">Thêm sản phẩm</button>
                            </div>
                            <div class="text-end">
                                <div>Tổng cộng: <strong id="orderTotal">0</strong>₫</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Phương thức thanh toán</label>
                        <select name="payment_method" class="form-select">
                            <option value="tien_mat">Tiền mặt</option>
                            <option value="chuyen_khoan">Chuyển khoản</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Địa chỉ giao hàng (*)</label>
                        <input type="text" name="shipping_address" class="form-control" placeholder="Địa chỉ giao hàng">
                    </div>

                    <div class="col-12 text-end mt-3">
                        <button type="submit" class="btn btn-success">Tạo đơn hàng</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const tmpl = document.getElementById('templateItemRow');
            const tbody = document.querySelector('#orderItemsTable tbody');
            const addBtn = document.getElementById('addItemBtn');
            const totalEl = document.getElementById('orderTotal');
            const customerSelect = document.getElementById('customerSelect');
            const newCustomerFields = document.getElementById('newCustomerFields');
            const form = document.getElementById('createOrderForm');
            const formErrors = document.getElementById('formErrors');

            function formatMoney(n) {
                return new Intl.NumberFormat('vi-VN').format(n);
            }

            function recomputeTotals() {
                let total = 0;
                tbody.querySelectorAll('tr').forEach(r => {
                    if (r.classList.contains('d-none')) return;
                    const priceInput = r.querySelector('.price-input');
                    const qty = Number(r.querySelector('.qty-input').value || 0);
                    const price = Number(priceInput.value || priceInput.dataset.price || 0);
                    const line = qty * price;
                    r.querySelector('.line-total').textContent = formatMoney(line);
                    total += line;
                });
                totalEl.textContent = formatMoney(total);
            }

            function wireRow(row) {
                const prod = row.querySelector('.product-select');
                const qty = row.querySelector('.qty-input');
                const priceCell = row.querySelector('.unit-price');
                prod.addEventListener('change', function() {
                    const opt = prod.selectedOptions[0];
                    const price = Number(opt.dataset.price || 0);
                    const stock = Number(opt.dataset.stock || 0);
                    const priceInput = row.querySelector('.price-input');
                    const stockEl = row.querySelector('.stock-label');
                    priceInput.dataset.price = price;
                    priceInput.value = price;
                    stockEl.textContent = 'Kho: ' + stock;
                    stockEl.dataset.stock = stock;
                    recomputeTotals();
                });
                const priceInput = row.querySelector('.price-input');
                priceInput.addEventListener('input', recomputeTotals);
                qty.addEventListener('input', recomputeTotals);
                row.querySelector('.remove-item').addEventListener('click', function() {
                    row.remove();
                    recomputeTotals();
                });
            }

            addBtn.addEventListener('click', function() {
                const clone = tmpl.cloneNode(true);
                clone.id = '';
                clone.classList.remove('d-none');
                tbody.appendChild(clone);
                wireRow(clone);
            });

            // Khi chọn khách hàng: tải địa chỉ lưu sẵn (AJAX)
            const addressesContainer = document.createElement('div');
            addressesContainer.className = 'mt-2';
            const addrSelect = document.createElement('select');
            addrSelect.name = 'dcgh_id';
            addrSelect.className = 'form-select form-select-sm';
            addressesContainer.appendChild(addrSelect);
            newCustomerFields.parentNode.insertBefore(addressesContainer, newCustomerFields.nextSibling);

            customerSelect.addEventListener('change', function() {
                newCustomerFields.style.display = (Number(this.value) === 0) ? 'block' : 'none';
                addrSelect.innerHTML = '';
                const cid = Number(this.value || 0);
                if (cid > 0) {
                    fetch('index.php?page=employee_orders&action=get_addresses&customer_id=' + cid, {
                            credentials: 'same-origin'
                        })
                        .then(r => r.json()).then(data => {
                            if (data.ok && Array.isArray(data.addresses) && data.addresses.length) {
                                addrSelect.innerHTML = '<option value="">-- Chọn địa chỉ giao hàng --</option>' + data.addresses.map(a => '<option value="' + a.dcgh_id + '">' + (a.diachi || '') + '</option>').join('');
                                addressesContainer.style.display = 'block';
                            } else {
                                addressesContainer.style.display = 'none';
                            }
                        }).catch(() => {
                            addressesContainer.style.display = 'none';
                        });
                } else {
                    addressesContainer.style.display = 'none';
                }
            });

            // start with one row
            addBtn.click();

            customerSelect.addEventListener('change', function() {
                newCustomerFields.style.display = (Number(this.value) === 0) ? 'block' : 'none';
            });

            // Client-side validation and AJAX submit
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                formErrors.innerHTML = '';
                const errors = [];
                let hasItem = false;
                const payload = new FormData(form);
                tbody.querySelectorAll('tr').forEach(r => {
                    if (r.classList.contains('d-none')) return;
                    const prod = r.querySelector('.product-select');
                    const qty = Number(r.querySelector('.qty-input').value || 0);
                    const price = Number(r.querySelector('.price-input').value || 0);
                    const stock = Number(r.querySelector('.stock-label').dataset.stock || 0);
                    if (!prod.value) {
                        errors.push('Vui lòng chọn sản phẩm cho tất cả dòng.');
                    }
                    if (qty <= 0) {
                        errors.push('Số lượng phải lớn hơn 0.');
                    }
                    if (price < 0) {
                        errors.push('Đơn giá phải >= 0.');
                    }
                    if (qty > stock) {
                        errors.push('Số lượng yêu cầu cho "' + (prod.selectedOptions[0].textContent.split(' — ')[0]) + '" vượt quá tồn kho (' + stock + ').');
                    }
                    if (prod.value && qty > 0) hasItem = true;
                });
                if (!hasItem) errors.push('Vui lòng thêm ít nhất một sản phẩm.');
                if (errors.length) {
                    formErrors.innerHTML = '<div class="alert alert-danger"><ul><li>' + errors.join('</li><li>') + '</li></ul></div>';
                    window.scrollTo({
                        top: formErrors.getBoundingClientRect().top + window.scrollY - 80,
                        behavior: 'smooth'
                    });
                    return false;
                }

                // Submit via AJAX to create_ajax endpoint
                fetch('index.php?page=employee_orders&action=create_ajax', {
                    method: 'POST',
                    body: payload,
                    credentials: 'same-origin'
                }).then(r => r.json()).then(data => {
                    if (data.ok) {
                        window.location.href = 'index.php?page=employee_orders&subpage=view&id=' + data.hoadon_id;
                    } else {
                        formErrors.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Lỗi tạo đơn') + '</div>';
                        window.scrollTo({
                            top: formErrors.getBoundingClientRect().top + window.scrollY - 80,
                            behavior: 'smooth'
                        });
                    }
                }).catch(err => {
                    formErrors.innerHTML = '<div class="alert alert-danger">Lỗi mạng hoặc máy chủ</div>';
                });
                return false;
            });

        })();
    </script>

<?php elseif ($subpage === 'view' && isset($_GET['id'])): ?>
    <?php
    $order_id = (int)$_GET['id'];
    $order = OrdersModel::getOrderById($order_id);
    $items = OrdersModel::getOrderItemsByOrderId($order_id);
    $customerOfOrder = CustomerModel::getCustomerById($order['khachhang_id'] ?? 0);
    ?>
    <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Chi tiết đơn hàng #<?= $order_id ?></h4>
            <a href="?page=employee_orders" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Thông tin khách hàng</h5>
                <p><strong>Tên:</strong> <?= htmlspecialchars($customerOfOrder['ten'] ?? 'N/A') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($customerOfOrder['email'] ?? 'N/A') ?></p>
                <p><strong>SĐT:</strong> <?= htmlspecialchars($customerOfOrder['sdt'] ?? 'N/A') ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($customerOfOrder['diachi_giaohang'] ?? $customerOfOrder['diachi_khachhang'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6">
                <h5>Thông tin đơn hàng</h5>
                <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['ngaytao'])) ?></p>
                <p><strong>Tổng tiền:</strong> <span class="text-primary fw-bold"><?= number_format($order['tongtien'], 0, ',', '.') ?>₫</span></p>
                <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($order['phuongthuc_thanhtoan'] ?? 'tien_mat') ?></p>
                <p><strong>Trạng thái:</strong>
                    <?php
                    $statusClass = [
                        'cho_xac_nhan' => 'warning',
                        'da_xac_nhan' => 'info',
                        'dang_giao_hang' => 'primary',
                        'da_giao_hang' => 'success',
                        'da_huy' => 'danger'
                    ];
                    $statusText = [
                        'cho_xac_nhan' => 'Chờ xác nhận',
                        'da_xac_nhan' => 'Đã xác nhận',
                        'dang_giao_hang' => 'Đang giao',
                        'da_giao_hang' => 'Hoàn thành',
                        'da_huy' => 'Đã hủy'
                    ];
                    $status = $order['trangthai'] ?? 'cho_xac_nhan';
                    ?>
                    <span class="badge bg-<?= $statusClass[$status] ?? 'secondary' ?>">
                        <?= $statusText[$status] ?? $status ?>
                    </span>
                </p>
            </div>
        </div>

        <h5 class="mb-3">Sản phẩm trong đơn hàng</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã SP</th>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    foreach ($items as $item): ?>
                        <tr>
                            <td>#<?= $item['sanpham_id'] ?></td>
                            <td><?= htmlspecialchars($item['ten_sanpham']) ?></td>
                            <td><?= $item['soluong'] ?></td>
                            <td><?= number_format($item['dongia'], 0, ',', '.') ?>₫</td>
                            <td><?= number_format($item['thanhtien'], 0, ',', '.') ?>₫</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (($order['trangthai'] !== 'da_giao_hang' && $order['trangthai'] !== 'da_huy') && $canCreateAndApprove): ?>
            <div class="mt-4">
                <h5>Cập nhật trạng thái</h5>
                <form method="POST" action="index.php?page=employee_orders&action=update_status&id=<?= $order_id ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <div class="d-flex gap-2">
                        <select name="trangthai" class="form-select" style="width: auto;">
                            <option value="cho_xac_nhan" <?= $order['trangthai'] === 'cho_xac_nhan' ? 'selected' : '' ?>>Chờ xác nhận</option>
                            <option value="da_xac_nhan" <?= $order['trangthai'] === 'da_xac_nhan' ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="dang_giao_hang" <?= $order['trangthai'] === 'dang_giao_hang' ? 'selected' : '' ?>>Đang giao hàng</option>
                            <option value="da_giao_hang" <?= $order['trangthai'] === 'da_giao_hang' ? 'selected' : '' ?>>Hoàn thành</option>
                            <option value="da_huy" <?= $order['trangthai'] === 'da_huy' ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>

  
    <div class="table-card">
        <h4 class="mb-3">Danh sách đơn hàng</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>MÃ ĐƠN</th>
                        <th>KHÁCH HÀNG</th>
                        <th>NGÀY ĐẶT</th>
                        <th>Đặt lúc</th>
                        <th>TỔNG TIỀN</th>
                        <th>TRẠNG THÁI</th>
                        <th>THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['hoadon_id'] ?></td>
                            <td><?= htmlspecialchars(OrdersModel::getCustomerByOrderId($order['hoadon_id'])['ho_ten'] ?? 'N/A') ?></td>
                            <td><?= date('d/m/Y', strtotime($order['ngaytao'])) ?></td>
                            <td><?= date('H:i', strtotime($order['ngaytao'])) ?></td>
                            <td><?= number_format($order['tongtien'], 0, ',', '.') ?>₫</td>
                            <td>
                                <?php
                                $statusClass = [
                                    'cho_xac_nhan' => 'warning',
                                    'da_xac_nhan' => 'info',
                                    'dang_giao_hang' => 'primary',
                                    'da_giao_hang' => 'success',
                                    'da_huy' => 'danger'
                                ];
                                $statusText = [
                                    'cho_xac_nhan' => 'Chờ xác nhận',
                                    'da_xac_nhan' => 'Đã xác nhận',
                                    'dang_giao_hang' => 'Đang giao',
                                    'da_giao_hang' => 'Hoàn thành',
                                    'da_huy' => 'Đã hủy'
                                ];
                                $status = $order['trangthai'] ?? 'cho_xac_nhan';
                                ?>
                                <span class="badge bg-<?= $statusClass[$status] ?? 'secondary' ?>">
                                    <?= $statusText[$status] ?? $status ?>
                                </span>
                            </td>
                            <td class="swipe-actions">
                                <a href="?page=employee_orders&subpage=view&id=<?= $order['hoadon_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                    </table>
                </div>
                <?= render_pagination_controls($pagination, 'employee_orders') ?>
            </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'app/views/employee/employeeLayout.php';
?>