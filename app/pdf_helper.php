<?php

// Robust mPDF autoload resolution (project uses bundled mpdf/ directory)
// Try common locations relative to this helper file.

use Mpdf\Mpdf;

(function() {
    $candidates = [
        __DIR__ . '/Mpdf/vendor/autoload.php',
        __DIR__ . '/Mpdf/vendor/autoload.php'
    ];
    foreach ($candidates as $path) {
        if (is_file($path)) { require_once $path; return; }
    }
})();

if (!class_exists('Mpdf\Mpdf')) {
    throw new RuntimeException('mPDF autoload not found. Ensure mpdf/vendor is available.');
}

class Pdf_Helper
{
    private static function ensureTempDir(): string
    {
        $candidates = [];
        // Prefer system temp
        $sys = rtrim((string)sys_get_temp_dir(), "\\/ ");
        if ($sys) $candidates[] = $sys . DIRECTORY_SEPARATOR . 'bookstore_mpdf';
        // Project-level tmp under app/tmp/mpdf
        $candidates[] = __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'mpdf';
        // One more fallback alongside mpdf package
        $candidates[] = __DIR__ . DIRECTORY_SEPARATOR . 'mpdf_tmp';

        foreach ($candidates as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            if (is_dir($dir) && is_writable($dir)) {
                return $dir;
            }
        }

        // Last resort: use sys temp even if not writable check succeeded earlier
        return $sys ?: __DIR__;
    }
    public static function html_to_pdf_download($htmlContent, $filename = 'document.pdf')
    {
        $safeName = preg_replace('/[^A-Za-z0-9_\-\.]+/', '_', $filename ?: 'document.pdf');
        if (strtolower(substr($safeName, -4)) !== '.pdf') {
            $safeName .= '.pdf';
        }

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_top' => 12,
            'margin_bottom' => 12,
            'margin_left' => 10,
            'margin_right' => 10,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans',
            'tempDir' => self::ensureTempDir()
        ]);
        $mpdf->WriteHTML($htmlContent);
        $mpdf->Output($safeName, \Mpdf\Output\Destination::DOWNLOAD);
        exit;
    }

    public static function report_to_pdf_download(array $data, string $filename = 'report.pdf')
    {
        $fmtMoney = function($n) { return number_format((float)$n, 0, ',', '.') . 'đ'; };        
        $safeName = preg_replace('/[^A-Za-z0-9_\-\.]+/', '_', $filename ?: 'report.pdf');
        if (strtolower(substr($safeName, -4)) !== '.pdf') $safeName .= '.pdf';

        $start = htmlspecialchars($data['startDate'] ?? '', ENT_QUOTES, 'UTF-8');
        $end = htmlspecialchars($data['endDate'] ?? '', ENT_QUOTES, 'UTF-8');
        $totalRevenue = (float)($data['totalRevenue'] ?? 0);
        $totalOrders = (int)($data['totalOrders'] ?? 0);
        $productsSold = (int)($data['productsSold'] ?? 0);
        $profit = (float)($data['profit'] ?? 0);
        $bestSellers = array_values($data['bestSellers'] ?? []);
        $revenueByDate = array_values($data['revenueByDate'] ?? []);
        $inventory = array_values($data['inventory'] ?? []);

        // Styles for a compact bill/invoice look
        $styles = ' 
            <style>
                body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
                .bill { width: 100%; }
                .header { text-align:center; margin-bottom: 10px; }
                .title { font-size: 18px; font-weight: 700; }
                .meta { font-size: 12px; color:#555; }
                .summary { width:100%; border-collapse: collapse; margin: 10px 0 14px; }
                .summary td { border: 1px solid #ccc; padding: 6px 8px; }
                .section { font-weight:700; margin: 12px 0 6px; }
                table.tbl { width:100%; border-collapse: collapse; }
                table.tbl th, table.tbl td { border: 1px solid #ccc; padding: 6px 8px; }
                table.tbl th { background:#f5f5f5; text-align:left; }
                .right { text-align:right; }
                .muted { color:#666; }
                .badge { display:inline-block; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
                .bg-success { background:#d1e7dd; color:#0f5132; }
                .bg-warning { background:#fff3cd; color:#664d03; }
                .bg-danger  { background:#f8d7da; color:#842029; }
                .footer { margin-top: 14px; font-size: 11px; text-align:center; color:#666; }
            </style>
        ';

        // Summary block
        $summaryHtml = '
            <table class="summary">
                <tr>
                    <td><b>Từ ngày</b></td><td>' . $start . '</td>
                    <td><b>Đến ngày</b></td><td>' . $end . '</td>
                </tr>
                <tr>
                    <td><b>Tổng doanh thu</b></td><td>' . $fmtMoney($totalRevenue) . '</td>
                    <td><b>Tổng đơn hàng</b></td><td>' . number_format($totalOrders) . '</td>
                </tr>
                <tr>
                    <td><b>Sản phẩm đã bán</b></td><td>' . number_format($productsSold) . '</td>
                    <td><b>Lợi nhuận ước tính</b></td><td>' . $fmtMoney($profit) . '</td>
                </tr>
            </table>
        ';

        // Best sellers table
        $bestRows = '';
        foreach ($bestSellers as $p) {
            $name = htmlspecialchars($p['ten_sanpham'] ?? $p['name'] ?? $p['tenVPP'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
            $sold = (int)($p['da_ban'] ?? $p['sold'] ?? $p['sold_quantity'] ?? 0);
            $revenue = (float)($p['tong_tien'] ?? $p['revenue'] ?? 0);
            $bestRows .= '<tr>'
                . '<td>' . $name . '</td>'
                . '<td class="right">' . number_format($sold) . '</td>'
                . '<td class="right">' . $fmtMoney($revenue) . '</td>'
                . '</tr>';
        }
        if ($bestRows === '') {
            $bestRows = '<tr><td colspan="3" class="muted">Không có dữ liệu</td></tr>';
        }
        $bestSellersHtml = '
            <div class="section">Sản phẩm bán chạy</div>
            <table class="tbl">
                <thead>
                    <tr><th>Tên sản phẩm</th><th class="right">Đã bán</th><th class="right">Doanh thu</th></tr>
                </thead>
                <tbody>' . $bestRows . '</tbody>
            </table>
        ';

        // Revenue by date table
        $revRows = '';
        foreach ($revenueByDate as $r) {
            $date = htmlspecialchars($r['date'] ?? $r['label'] ?? '', ENT_QUOTES, 'UTF-8');
            $orders = (int)($r['order_count'] ?? $r['orders'] ?? 0);
            $total = (float)($r['total_revenue'] ?? $r['total'] ?? 0);
            $revRows .= '<tr>'
                . '<td>' . $date . '</td>'
                . '<td class="right">' . number_format($orders) . '</td>'
                . '<td class="right">' . $fmtMoney($total) . '</td>'
                . '</tr>';
        }
        if ($revRows === '') {
            $revRows = '<tr><td colspan="3" class="muted">Không có dữ liệu</td></tr>';
        }
        $revenueHtml = '
            <div class="section">Doanh thu theo ngày</div>
            <table class="tbl">
                <thead>
                    <tr><th>Ngày</th><th class="right">Đơn hàng</th><th class="right">Doanh thu</th></tr>
                </thead>
                <tbody>' . $revRows . '</tbody>
            </table>
        ';

        // Inventory table with badges
        $invRows = '';
        foreach ($inventory as $row) {
            $name = htmlspecialchars($row['name'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
            $sku = htmlspecialchars($row['sku'] ?? '', ENT_QUOTES, 'UTF-8');
            $stock = (int)($row['stock'] ?? $row['soluongton'] ?? 0);
            $status = $stock <= 0 ? ['Hết hàng','bg-danger'] : ($stock <= 10 ? ['Còn ít','bg-warning'] : ['Còn hàng','bg-success']);
            $invRows .= '<tr>'
                . '<td>' . $name . '</td>'
                . '<td>' . $sku . '</td>'
                . '<td class="right">' . number_format($stock) . '</td>'
                . '<td><span class="badge ' . $status[1] . '">' . $status[0] . '</span></td>'
                . '</tr>';
        }
        if ($invRows === '') {
            $invRows = '<tr><td colspan="4" class="muted">Không có dữ liệu kho</td></tr>';
        }
        $inventoryHtml = '
            <div class="section">Tồn kho</div>
            <table class="tbl">
                <thead>
                    <tr><th>Tên sản phẩm</th><th>SKU</th><th class="right">Số lượng còn lại</th><th>Trạng thái</th></tr>
                </thead>
                <tbody>' . $invRows . '</tbody>
            </table>
        ';

        $html = $styles . '
            <div class="bill">
                <div class="header">
                    <div class="title">BÁO CÁO & THỐNG KÊ</div>
                    <div class="meta">Được tạo vào: ' . date('d/m/Y H:i') . '</div>
                </div>
                ' . $summaryHtml . '
                ' . $bestSellersHtml . '
                ' . $revenueHtml . '
                ' . $inventoryHtml . '
                <div class="footer">Cảm ơn bạn đã sử dụng hệ thống báo cáo.</div>
            </div>';

        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 8,
            'margin_right' => 8,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans',
            'tempDir' => self::ensureTempDir()
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output($safeName, \Mpdf\Output\Destination::DOWNLOAD);
        exit;
    }
}

// Backwards compatibility with the name used by EmployeePageController
if (!class_exists('PDF_Help')) {
    class PDF_Help extends Pdf_Helper {}
}