<?php
// Minimal printable report view
?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Printable Report</title>
    <style>
        body{font-family: Arial, sans-serif; margin:20px}
        table{width:100%; border-collapse: collapse}
        th,td{border:1px solid #ddd; padding:6px; text-align:left}
        th{background:#f7f7f7}
    </style>
</head>
<body>
    <h2>Report from <?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?></h2>
    <h3>Summary</h3>
    <ul>
        <li>Total revenue: <?= number_format($totalRevenue ?? 0,0,',','.') ?>đ</li>
        <li>Total orders: <?= number_format($totalOrders ?? 0) ?></li>
        <li>Products sold: <?= number_format($productsSold ?? 0) ?></li>
        <li>Profit (est): <?= number_format($profit ?? 0,0,',','.') ?>đ</li>
    </ul>

    <h3>Best Sellers</h3>
    <?php if (!empty($bestSellers)): ?>
        <table>
            <thead><tr><th>#</th><th>Name</th><th>Sold</th><th>Revenue</th></tr></thead>
            <tbody>
            <?php foreach ($bestSellers as $i => $p): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($p['ten_sanpham'] ?? $p['name'] ?? $p['tenVPP'] ?? '') ?></td>
                    <td><?= number_format($p['da_ban'] ?? $p['sold'] ?? 0) ?></td>
                    <td><?= number_format($p['tong_tien'] ?? $p['revenue'] ?? 0,0,',','.') ?>đ</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No data</p>
    <?php endif; ?>

    <h3>Inventory</h3>
    <?php if (!empty($inventory)): ?>
        <table>
            <thead><tr><th>Name</th><th>SKU</th><th>Stock</th></tr></thead>
            <tbody>
            <?php foreach ($inventory as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['sku'] ?? '') ?></td>
                    <td><?= number_format(intval($row['stock'] ?? 0)) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No inventory data</p>
    <?php endif; ?>

</body>
</html>
