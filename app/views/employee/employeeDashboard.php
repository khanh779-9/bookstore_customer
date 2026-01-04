<?php

/**
 * Employee Dashboard View - Clean & Professional UI
 */

requireEmployeeLogin();

if (!EmployeeModel::canAccessDashboard()) {
    echo '<div class="alert alert-danger"><i class="fa-solid fa-ban"></i> Truy cập bị từ chối</div>';
    exit;
}

$period = $_GET['period'] ?? 'month';
$vars = EmployeePageController::prepareEmployeeDashboard($period);
if (is_array($vars)) extract($vars);

$page_title = 'Dashboard Quản lý';

$recentOrders = EmployeeHelpers::getRecentOrders();

if (isset($_GET['action']) && $_GET['action'] === 'fetch_recent_orders') {

    // Chỉ cho request từ JS bên trong
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
        http_response_code(403);
        exit('Forbidden');
    }

    // Kiểm tra token
    if (!isset($_SERVER['HTTP_X_DASHBOARD_TOKEN']) || $_SERVER['HTTP_X_DASHBOARD_TOKEN'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        exit('Forbidden');
    }

    //Trả về các đơn hàng mới gần đây nhưng render về html để hiện thị dưới bảng kia
    echo PageController::fetchRecentOrders();
    exit;
}


ob_start();
?>

<!-- Top Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Dashboard Quản lý</h2>
        <span class="text-muted small">Tổng quan hoạt động kinh doanh</span>
    </div>
    <div class="d-flex gap-2">
        <?php
        $tabs = [
            'week'  => '7 ngày',
            'month' => 'Tháng',
            'year'  => 'Năm'
        ];
        foreach ($tabs as $key => $label):
        ?>
            <a href="?page=employee_dashboard&period=<?= $key ?>"
                class="btn btn-sm <?= $period === $key ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>

        <a href="?page=employee_orders&subpage=create" class="btn btn-primary btn-sm ms-2">
            <i class="fas fa-plus me-2"></i>Tạo đơn hàng
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        [
            'label' => 'Doanh thu',
            'value' => number_format($stats['revenue'] ?? 0, 0, ',', '.') . 'đ',
            'change' => $revenueChange,
            'icon' => 'fa-wallet',
            'color' => 'primary'
        ],
        [
            'label' => 'Đơn hàng',
            'value' => number_format($stats['orders'] ?? 0),
            'change' => $ordersChange,
            'icon' => 'fa-shopping-cart',
            'color' => 'success'
        ],
        [
            'label' => 'Khách hàng',
            'value' => number_format($stats['customers'] ?? 0),
            'sub' => ($customerStats['new_month'] ?? 0) . ' tháng',
            'icon' => 'fa-users',
            'color' => 'info'
        ],
        [
            'label' => 'Chờ xác nhận',
            'value' => $orderStats['pending'] ?? 0,
            'sub' => 'Cần xử lý',
            'icon' => 'fa-hourglass-half',
            'color' => 'warning'
        ],
    ];

    foreach ($cards as $c):
    ?>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted small mb-1"><?= $c['label'] ?></p>
                            <h5 class="fw-bold mb-1"><?= $c['value'] ?></h5>

                            <?php if (isset($c['change'])): ?>
                                <small class="<?= $c['change'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <i class="fas fa-arrow-<?= $c['change'] >= 0 ? 'up' : 'down' ?>"></i>
                                    <?= abs($c['change']) ?>%
                                </small>
                            <?php else: ?>
                                <small class="text-<?= $c['color'] ?>">
                                    <i class="fas fa-info-circle"></i> <?= $c['sub'] ?>
                                </small>
                            <?php endif; ?>
                        </div>

                        <div class="rounded-circle bg-<?= $c['color'] ?> bg-opacity-10 d-flex align-items-center justify-content-center"
                            style="width: 45px; height: 45px;">
                            <i class="fas <?= $c['icon'] ?> text-<?= $c['color'] ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Chart + Top Products Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fas fa-chart-line text-primary me-1"></i>Doanh thu theo thời gian</h6>
                <span class="text-muted small"><?= ucfirst($period) ?></span>
            </div>
            <div class="card-body">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h6 class="fw-bold mb-0">Top Sản phẩm bán chạy</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-end">Bán</th>
                                <th class="text-end">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topProducts)): ?>
                                <?php foreach ($topProducts as $i => $p): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary me-2"><?= $i + 1 ?></span>
                                            <?= htmlspecialchars($p['ten_sanpham']) ?>
                                        </td>
                                        <td class="text-end fw-bold"><?= number_format($p['da_ban']) ?></td>
                                        <td class="text-end text-success fw-bold">
                                            <?= number_format($p['tong_tien'], 0, ',', '.') ?>đ
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">Chưa có dữ liệu</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bảng thống kê sản phẩm bán ế -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="fas fa-chart-line text-warning me-2"></i>Sản phẩm bán ế</h6>
        <span class="badge bg-warning text-dark">30 ngày qua</span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-center">Tồn kho</th>
                        <th class="text-end">Giá</th>
                        <th class="text-center">Đã bán</th>
                        <th class="text-end">Doanh thu</th>
                        <th class="text-center">Bán lần cuối</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($slowMovingProducts)): ?>
                        <?php foreach ($slowMovingProducts as $sp): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-box text-muted me-2"></i>
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($sp['ten_sanpham']) ?></div>
                                            <small class="text-muted">ID: <?= $sp['sanpham_id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $sp['soluongton'] > 50 ? 'warning' : 'secondary' ?>">
                                        <?= number_format($sp['soluongton']) ?>
                                    </span>
                                </td>
                                <td class="text-end"><?= number_format($sp['gia'], 0, ',', '.') ?>đ</td>
                                <td class="text-center">
                                    <?php if ($sp['da_ban'] == 0): ?>
                                        <span class="badge bg-danger">Chưa bán</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><?= number_format($sp['da_ban']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($sp['doanh_thu'] == 0): ?>
                                        <span class="text-muted">0đ</span>
                                    <?php else: ?>
                                        <?= number_format($sp['doanh_thu'], 0, ',', '.') ?>đ
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($sp['ngay_ban_cuoi']): ?>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($sp['ngay_ban_cuoi'])) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Chưa bán</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Không có sản phẩm bán ế
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($slowMovingProducts)): ?>
    <div class="card-footer bg-light border-0">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Hiển thị sản phẩm có lượt bán ≤ 5 trong 30 ngày qua
            </small>
            <a href="?page=employee_products" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-th-list me-1"></i>Xem tất cả sản phẩm
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Recent Orders -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h6 class="fw-bold mb-0">Đơn hàng gần đây</h6>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th class="text-end">Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="recent-order-body">
                <!-- <?php if (!empty($recentOrders)): foreach ($recentOrders as $o): ?>
                        <tr>

                            <?php
                                $ord_customer = OrdersModel::getCustomerByOrderId($o['hoadon_id']);
                            ?>

                            <td class="fw-bold">#<?= $o['hoadon_id'] ?></td>
                            <td><?= htmlspecialchars(substr($ord_customer['ho_ten'], 0, 20)) ?></td>
                            <td class="text-end fw-bold"><?= number_format($o['tongtien'], 0, ',', '.') ?>đ</td>
                            <td>
                                <?php
                                $class = match ($o['trangthai']) {
                                    'cho_xac_nhan' => 'warning',
                                    'da_xac_nhan' => 'info',
                                    'dang_giao_hang' => 'secondary',
                                    'da_giao_hang' => 'success',
                                    'da_huy' => 'danger',
                                    default => 'dark'
                                };
                                ?>
                                <span class="badge bg-<?= $class ?>"><?= $o['trangthai'] ?></span>
                            </td>
                            <td><?= date('d/m H:i', strtotime($o['ngaytao'])) ?></td>
                            <td>
                                <a href="?page=employee_orders&subpage=view&id=<?= $o['hoadon_id'] ?>"
                                    class="btn btn-sm btn-outline-primary p-1 px-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach;
                        else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-3 text-muted">Chưa có đơn hàng</td>
                    </tr>
                <?php endif; ?> -->
            </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Chart Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function loadRecentOrders() {
        fetch('?page=employee_dashboard&action=fetch_recent_orders', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Dashboard-Token': '<?= $csrf_token ?>'
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('recent-order-body').innerHTML = html;
            })
            .catch(err => console.error('Lỗi tải đơn hàng gần đây:', err));
    }

    setInterval(loadRecentOrders, 60000);
    loadRecentOrders();

    document.addEventListener('DOMContentLoaded', () => {
        const labels = <?= json_encode(array_column($revenueByDate, 'date')) ?>;
        const revenue = <?= json_encode(array_column($revenueByDate, 'total_revenue')) ?>;
        const orders = <?= json_encode(array_column($revenueByDate, 'order_count')) ?>;

        if (!labels.length) {
            document.getElementById('revenueChart').outerHTML =
                '<p class="text-muted text-center py-4">Chưa có dữ liệu doanh thu</p>';
            return;
        }

        new Chart(revenueChart, {
            type: 'line',
            data: {
                labels: labels.map(d => new Date(d).toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit'
                })),
                datasets: [{
                        label: 'Doanh thu',
                        data: revenue,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.08)',
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Số đơn',
                        data: orders,
                        borderColor: '#dc3545',
                        fill: false,
                        tension: 0.35,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left'
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    #revenueChart {
        height: 700px;
    }
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/employeeLayout.php';
?>