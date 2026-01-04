<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- quan trọng để responsive -->
    <title><?= htmlspecialchars($web_product_title) . " - " . htmlspecialchars($service_status['title']) ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../../../../../public/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container my-5">
        <?php
            $type = 'info';
            if (!empty($service_status['error'])) $type = 'danger';
            elseif (!empty($service_status['warning'])) $type = 'warning';
            elseif (!empty($service_status['success'])) $type = 'success';

            $iconSrc = '';
            $iconAlt = '';
            if (!empty($service_status['error'])){ $iconSrc = 'assets/status_icons/icons8_error_solid.png'; $iconAlt = 'Lỗi'; }
            elseif (!empty($service_status['warning'])){ $iconSrc = 'assets/status_icons/icons8_warning.png'; $iconAlt = 'Cảnh báo'; }
            elseif (!empty($service_status['success'])){ $iconSrc = 'assets/status_icons/icons8_success.png'; $iconAlt = 'Thành công'; }
        ?>

        <div class="card service-status-card border-0">
            <div class="row g-0 align-items-center">
                <div class="col-12 col-sm-4 service-status-icon text-center p-3">
                    <?php if (!empty($iconSrc)): ?>
                        <img src="<?= $iconSrc ?>" alt="<?= htmlspecialchars($iconAlt) ?>" class="img-fluid">
                    <?php else: ?>
                        <i class="fa-solid fa-info-circle fa-3x text-<?= $type ?>"></i>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-sm-8 service-status-body bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="service-status-title mb-1"><?= htmlspecialchars($service_status['title']) ?></h5>
                            <p class="text-muted mb-0 small"><?= htmlspecialchars($service_status['detail']) ?></p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-center mt-3">
                        <div class="me-2"><strong>Mã lỗi:</strong> <span class="service-status-code"><?= htmlspecialchars($service_status['code']) ?></span></div>
                        <?php if (!empty($service_status['time'])): ?>
                            <div class="text-muted small">• <?= htmlspecialchars($service_status['time']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4">
                        <a href="index.php" class="btn btn-outline-<?= $type ?> btn-sm me-2"><i class="fa-solid fa-house"></i> Về trang chủ</a>
                        <a href="contact.php" class="btn btn-<?= $type ?> btn-sm"><i class="fa-solid fa-headset"></i> Liên hệ hỗ trợ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>