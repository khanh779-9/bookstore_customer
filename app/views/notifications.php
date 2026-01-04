<?php

$customer = $_SESSION['khachhang_account'] ?? null;
$csrf = htmlspecialchars($csrf_token ?? ($_SESSION['csrf_token'] ?? ''));
$tab = $tab ?? ($_GET['tab'] ?? 'all');
$limit = $limit ?? (isset($_GET['limit']) ? (int)$_GET['limit'] : 15);
$counts = $counts ?? ['all' => 0, 'orders' => 0, 'promotions' => 0, 'system' => 0];
$notifications = $notifications ?? [];


// function time_ago_vi($d){
//     if(!$d) return '';

//     $timestamp_now = time();
//     $timestamp_now_mili=round(microtime(true) * 1000);

//     $timestamp_cal=strtotime($d) * 1000;

//     echo "Timestamp calc: " . $timestamp_cal . "\n";
//     echo "Timestamp now mili: " . $timestamp_now_mili . "\n";

//     $t= $timestamp_now_mili - $timestamp_cal;

//     if($t<60000){
//         return 'Vừa xong';
//     }
//     else if($t<3600000){
//         $p=round($t/60000);
//         return $p.' phút trước';
//     }
//     else if($t<86400000){
//         $p=round($t/3600000);
//         return $p.' giờ trước';
//     }
//     else if($t<2592000000){
//         $p=round($t/86400000);
//         return $p.' ngày trước';
//     }
//     else if($t<31104000000){
//         $p=round($t/2592000000);
//         return $p.' tháng trước';
//     }

//     return date('Y-m-d H:i:s', $t);
// }




function notification_icon($loai)
{
    if ($loai === 'khuyen_mai') return 'bi-gift';
    else if ($loai === 'don_hang') return 'bi-truck';
    else if ($loai === 'khach_hang') return 'bi-person-circle';
    else if ($loai === 'he_thong') return 'bi-bell';
    return 'bi-bell'; // default
}
?>

<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Thông báo của tôi</h4>
        <?php if (!empty($customer)): ?>
            <form method="post" action="index.php?page=notifications_mark_all" class="mb-0">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <button type="submit" class="btn btn-sm btn-outline-primary">Đánh dấu đã đọc tất cả</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (empty($customer)): ?>
        <div class="alert alert-warning">Vui lòng <a href="index.php?page=login" class="alert-link">đăng nhập</a> để xem thông báo.</div>
    <?php else: ?>

        <div class="d-flex flex-wrap gap-2 mb-3">
            <?php
            $tabs = [
                'all' => ['label' => 'Tất cả', 'count' => $counts['all'] ?? 0],
                'orders' => ['label' => 'Đơn hàng', 'count' => $counts['orders'] ?? 0],
                'promotions' => ['label' => 'Khuyến mãi', 'count' => $counts['promotions'] ?? 0],
                'system' => ['label' => 'Hệ thống', 'count' => $counts['system'] ?? 0]
            ];
            ?>
            <?php foreach ($tabs as $key => $meta): ?>
                <a href="index.php?page=notifications&tab=<?= $key ?>&limit=<?= (int)$limit ?>"
                    class="btn btn-sm <?= $tab === $key ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <?= htmlspecialchars($meta['label']) ?>
                    <?php if (($meta['count'] ?? 0) > 0): ?><span class="badge bg-light text-dark ms-1"><?= (int)$meta['count'] ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2 mb-0">Không có thông báo.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($notifications as $note):
                    $status = $note['trang_thai'] ?? 'chua_doc';
                    $isUnread = ($status === 'chua_doc');
                    $isArchived = ($status === 'luu_tru');
                    $loai = $note['loai'] ;
                    $icon = notification_icon($loai);
                ?>
                    <div class="list-group-item py-3 mt-3 border rounded-3 shadow-sm">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center p-2" style="width:40px;height:40px;">
                                <i class="bi <?= $icon ?> text-primary"></style></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fw-semibold"><?= htmlspecialchars($note['tieu_de'] ?? 'Thông báo') ?></div>
                                    <?php if ($isArchived): ?>
                                        <span class="badge bg-secondary">Lưu trữ</span>
                                    <?php elseif ($isUnread): ?>
                                        <span class="badge bg-warning text-dark">Chưa đọc</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Đã đọc</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted mt-1"><?= nl2br(htmlspecialchars($note['noi_dung'] ?? '')) ?></div>
                            
                                  <div class="small text-muted mt-1"><strong><?= ($note['ngay_tao'] ?? '') ?></strong></div>
                                <div class="d-flex gap-2 mt-2">
                                    <form method="post" action="index.php?page=notifications_mark" class="mb-0">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                        <input type="hidden" name="mark_notification_id" value="<?= (int)($note['thongbao_id'] ?? 0) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <?= $isUnread ? 'Đánh dấu đã đọc' : 'Đánh dấu chưa đọc' ?>
                                        </button>
                                    </form>
                                    <?php if (!$isArchived): ?>
                                        <form method="post" action="index.php?page=notifications_archive" class="mb-0">
                                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                            <input type="hidden" name="archive_notification_id" value="<?= (int)($note['thongbao_id'] ?? 0) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Lưu trữ</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-3">
                <a class="btn btn-sm btn-outline-secondary" href="index.php?page=notifications&tab=<?= htmlspecialchars($tab) ?>&limit=<?= (int)($limit + 10) ?>">Xem thêm thông báo cũ</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>