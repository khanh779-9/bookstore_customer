<?php

/**
 * Employee Reports View
 * 
 * Hiển thị báo cáo và thống kê
 */

requireEmployeeLogin();

// Use controller preparer
$vars = EmployeePageController::prepareEmployeeReports($_GET['start_date'] ?? null, $_GET['end_date'] ?? null);
if (is_array($vars)) extract($vars);
$page_title = $page_title ?? 'Báo cáo & Thống kê';
ob_start();
?>

<div class="top-bar d-flex justify-content-between align-items-center">
    <h2 class="mb-0"><i class="bi bi-graph-up me-2"></i> Báo cáo & Thống kê</h2>
    <div>
        <div class="btn-group me-2" role="group" aria-label="Quick ranges">
            <?php
            $currentRange = $_GET['range'] ?? '';
            $baseUrl = 'index.php?page=employee_reports';
            ?>
            <a href="<?= $baseUrl ?>&range=today" class="btn btn-sm <?= $currentRange === 'today' ? 'btn-primary' : 'btn-outline-primary' ?>">Hôm nay</a>
            <a href="<?= $baseUrl ?>&range=7days" class="btn btn-sm <?= $currentRange === '7days' ? 'btn-primary' : 'btn-outline-primary' ?>">7 ngày</a>
            <a href="<?= $baseUrl ?>&range=month" class="btn btn-sm <?= $currentRange === 'month' ? 'btn-primary' : 'btn-outline-primary' ?>">Tháng này</a>
            <a href="<?= $baseUrl ?>&range=year" class="btn btn-sm <?= $currentRange === 'year' ? 'btn-primary' : 'btn-outline-primary' ?>">Năm nay</a>
            <button class="btn btn-sm btn-outline-secondary" id="customRangeBtn">Tùy chỉnh</button>
        </div>
        <div class="d-inline-block">
            <?php $currentCategory = (int)($_GET['category'] ?? 0);
            $lowStockThreshold = 20; // hard-coded threshold
            $exportBase = $baseUrl . '&range=' . urlencode($currentRange)
                . '&start_date=' . urlencode($_GET['start_date'] ?? '')
                . '&end_date=' . urlencode($_GET['end_date'] ?? '') ?>
            <!-- <select id="categoryFilter" class="form-select form-select-sm d-inline-block me-2" style="width:220px;">
                <option value="0" <?= $currentCategory === 0 ? 'selected' : '' ?>>-- Tất cả danh mục --</option>
                <?php if (function_exists('CategoriesModel::getAllCategories') || class_exists('CategoriesModel')): ?>
                    <?php foreach (CategoriesModel::getAllCategories() as $c): ?>
                        <option value="<?= $c['danhmucSP_id'] ?>" <?= $currentCategory === (int)$c['danhmucSP_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['tenDanhMuc']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select> -->
            <a id="exportCsvBtn" href="<?= $exportBase ?>&export=excel&which=bestSellers&category=<?= $currentCategory ?>" class="btn btn-sm btn-outline-success me-1">Export Excel</a>
            <a id="exportPdfBtn" href="<?= $exportBase ?>&export=pdf&which=bestSellers&category=<?= $currentCategory ?>" class="btn btn-sm btn-outline-primary">Export PDF</a>
        </div>
    </div>
</div>

<!-- Custom range panel -->
<div id="customRangePanel" class="card my-3" style="display: <?= isset($_GET['start_date']) || isset($_GET['end_date']) ? 'block' : 'none' ?>;">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="employee_reports">
            <div class="col-md-4">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i>Lọc dữ liệu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary cards -->
<div class="row g-3 mb-4">
    <?php
    $totalRevenue = $orderStats['total_revenue'] ?? ($orderStats['revenue'] ?? 0);
    $totalOrders = $orderStats['total_orders'] ?? count($allOrders ?? []);
    $totalProfit = $orderStats['profit'] ?? round(($totalRevenue) * 0.25);
    $productsSold = $orderStats['products_sold'] ?? array_sum(array_map(fn($p) => intval($p['da_ban'] ?? 0), $bestSellers ?? []));
    ?>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small">Tổng doanh thu</div>
                <h4 class="fw-bold text-success mt-2"><?= number_format($totalRevenue, 0, ',', '.') ?>đ</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small">Tổng số đơn hàng</div>
                <h4 class="fw-bold text-primary mt-2"><?= number_format($totalOrders) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small">Lợi nhuận</div>
                <h4 class="fw-bold text-info mt-2"><?= number_format($totalProfit, 0, ',', '.') ?>đ</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-muted small">Số sản phẩm đã bán</div>
                <h4 class="fw-bold text-warning mt-2"><?= number_format($productsSold) ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Xu hướng doanh thu</strong>
                <small class="text-muted">Biểu đồ doanh thu theo ngày</small>
            </div>
            <div class="card-body">
                <canvas id="revenueTrendChart" height="420px"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Sản phẩm bán chạy</strong>
                <small class="text-muted">Top 10</small>
            </div>
            <div class="card-body p-4">
                <canvas id="bestSellersChart" height="403px"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Inventory status table -->
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <strong>Trạng thái kho</strong>
    </div>
    <div class="card-body">
        <?php
        $inventory = [];

        if (empty($inventory) && class_exists('ProductModel')) {
            $prods = ProductModel::getAllProducts();
            $inventory = array_map(function ($p) {
                $name = $p['name'];
                return [
                    'name' => $name,
                    'sku' => $p['sanpham_id'] ?? '',
                    'soluongton' => intval($p['soluongton'])
                ];
            }, $prods);
        }
        // Chỉ hiển thị các sản phẩm có tồn kho <= ngưỡng
        $inventory = array_values(array_filter($inventory, function($row) use ($lowStockThreshold) {
            $stock = isset($row['soluongton']) ? intval($row['soluongton']) : 0;
            return $stock <= $lowStockThreshold;
        }));
        // var_dump($inventory);
        ?>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>SKU</th>
                        <th class="text-end">Số lượng còn lại</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inventory)): ?>
                        <?php foreach ($inventory as $row):

                            $stock = isset($row['soluongton']) ? intval($row['soluongton']) : 0;
                            $status = $stock <= 0 ? 'Hết hàng' : ($stock <= 10 ? 'Còn ít' : 'Còn hàng');
                            $badge = $stock <= 0 ? 'danger' : ($stock <= 10 ? 'warning' : 'success');
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['sku'] ?? '') ?></td>
                                <td class="text-end"><?= number_format($stock) ?></td>
                                <td><span class="badge bg-<?= $badge ?>"><?= $status ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Không có dữ liệu kho</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include Chart.js and render charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data passed from PHP
    const revenueByDate = <?= json_encode(array_values($revenueByDate ?? [])) ?>;
    const bestSellers = <?= json_encode(array_values($bestSellers ?? [])) ?>;

    // Revenue trend chart
    (function() {
        const labels = revenueByDate.map(r => r.date ? (new Date(r.date)).toLocaleDateString() : r.label);
        const data = revenueByDate.map(r => Number(r.total_revenue ?? r.total ?? 0));
        const ctx = document.getElementById('revenueTrendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Doanh thu',
                    data,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25,135,84,0.15)',
                    fill: true,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    })();

    // Best sellers donut chart
    (function() {
        const labels = bestSellers.map(p => (p.ten_sanpham ?? p.name ?? p.tenVPP ?? 'N/A').slice(0, 40));
        const data = bestSellers.map(p => Number(p.da_ban ?? p.sold ?? p.sold_quantity ?? 0));
        const colors = [
            '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0'
        ];
        const ctx = document.getElementById('bestSellersChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors.slice(0, labels.length)
                }]
            },
            options: {
                responsive: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 14
                        }
                    }
                }
            }
        });
    })();

    // Custom range toggle
    document.getElementById('customRangeBtn').addEventListener('click', function() {
        const p = document.getElementById('customRangePanel');
        p.style.display = (p.style.display === 'none' || p.style.display === '') ? 'block' : 'none';
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/employeeLayout.php';
