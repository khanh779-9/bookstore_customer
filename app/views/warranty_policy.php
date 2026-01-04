<style>
    .warranty-hero {
        background: radial-gradient(circle at 80% 30%, #fef3c7, #fecaca 35%, #ffffff 70%);
    }
    .warranty-pill {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    /* Timeline (Warranty Policy) - responsive & aligned */
    .timeline-warranty { position: relative; --icon-size: 38px; --icon-left: 0.95rem; --icon-top: 0.25rem; }
    .timeline-warranty::before {
        content: "";
        position: absolute;
        left: calc(var(--icon-left) + (var(--icon-size) / 2));
        top: calc(var(--icon-top) + (var(--icon-size) / 2));
        bottom: calc((var(--icon-size) / 2) - var(--icon-top));
        width: 2px;
        background: #e5e7eb;
    }
    .timeline-warranty .step { position: relative; padding-left: 0; }
    .timeline-warranty .step::before { display: none; }
    .timeline-warranty .step .d-flex { position: relative; }
    .timeline-warranty .step .icon-circle {
        position: absolute;
        left: var(--icon-left);
        top: var(--icon-top);
        width: var(--icon-size);
        height: var(--icon-size);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
        font-weight: 700;
        font-size: 15px;
        box-shadow: 0 8px 18px rgba(245, 158, 11, 0.2);
    }
    .timeline-warranty .step .d-flex > div:last-child { margin-left: 3.5rem; }
    @media (max-width: 576px) {
        .timeline-warranty { --icon-size: 32px; --icon-left: 0.7rem; --icon-top: 0.2rem; }
        .timeline-warranty .step .icon-circle { font-size: 14px; }
        .timeline-warranty .step .d-flex > div:last-child { margin-left: 3rem; }
    }
</style>

<div class="container mt-5 pb-5">
    <!-- Hero -->
    <div class="warranty-hero rounded-4 p-4 p-lg-5 mb-4 position-relative overflow-hidden shadow-sm">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill warranty-pill mb-3">
                    <span class="badge bg-warning rounded-pill">BookZone Guarantee</span>
                    <span class="text-muted small">Sửa chữa hoặc thay thế miễn phí</span>
                </div>
                <h1 class="display-5 fw-bold mb-3">Chính sách bảo hành</h1>
                <p class="lead text-muted mb-4">Tất cả sản phẩm BookZone đều được bảo hành chuyên nghiệp với thời gian phù hợp loại sản phẩm.</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-calendar-check text-warning"></i>
                        <div>
                            <div class="fw-bold">Đa dạng</div>
                            <small class="text-muted">7-12 tháng tùy loại</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-tools text-primary"></i>
                        <div>
                            <div class="fw-bold">Chuyên nghiệp</div>
                            <small class="text-muted">Sửa chữa tại trung tâm</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-lightning-charge text-danger"></i>
                        <div>
                            <div class="fw-bold">Nhanh chóng</div>
                            <small class="text-muted">Xử lý 5-7 ngày</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="bg-white rounded-4 shadow-sm p-4 h-100 border text-start">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-warning rounded-circle" style="width: 46px; height: 46px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <div>
                            <div class="fw-bold">Miễn phí sửa chữa</div>
                            <small class="text-muted">Cho lỗi do nhà sản xuất</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-info rounded-circle" style="width: 46px; height: 46px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="bi bi-chat-dots"></i>
                        </span>
                        <div>
                            <div class="fw-bold">Hỗ trợ đầy đủ</div>
                            <small class="text-muted">Hotline & email hỗ trợ</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-primary rounded-circle" style="width: 46px; height: 46px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="bi bi-box-seam"></i>
                        </span>
                        <div>
                            <div class="fw-bold">Trả về miễn phí</div>
                            <small class="text-muted">Nếu lỗi do NSX</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                        <div>
                            <h2 class="h4 fw-bold mb-1">
                                <i class="bi bi-shield-check me-2 text-warning"></i>Thời hạn bảo hành
                            </h2>
                            <p class="text-muted mb-0">Tùy theo loại sản phẩm, chúng tôi cung cấp bảo hành từ 7 ngày đến 12 tháng.</p>
                        </div>
                        <span class="badge bg-light text-warning border">Miễn phí 100%</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-warning"><i class="bi bi-book"></i></span>
                                    <div>
                                        <div class="fw-bold">Sách & Văn phòng</div>
                                        <p class="text-muted small mb-0">7 ngày từ ngày nhận</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-warning"><i class="bi bi-cpu"></i></span>
                                    <div>
                                        <div class="fw-bold">Sản phẩm điện tử</div>
                                        <p class="text-muted small mb-0">12 tháng từ ngày mua</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-warning"><i class="bi bi-gift"></i></span>
                                    <div>
                                        <div class="fw-bold">Bộ quà tặng</div>
                                        <p class="text-muted small mb-0">14 ngày từ ngày nhận</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-warning"><i class="bi bi-box"></i></span>
                                    <div>
                                        <div class="fw-bold">Sản phẩm khác</div>
                                        <p class="text-muted small mb-0">Theo quy định NSX</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h4 fw-bold mb-3">
                        <i class="bi bi-tools me-2 text-info"></i>Điều kiện bảo hành
                    </h2>
                    <p class="text-muted mb-4">Sản phẩm được bảo hành miễn phí khi đáp ứng các điều kiện sau.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-info"><i class="bi bi-check-circle-fill"></i></span>
                                    <div>
                                        <div class="fw-bold">Lỗi kỹ thuật</div>
                                        <p class="text-muted small mb-0">Lỗi không do người dùng gây ra, chỉ do sản xuất.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-info"><i class="bi bi-check-circle-fill"></i></span>
                                    <div>
                                        <div class="fw-bold">Trong hạn bảo hành</div>
                                        <p class="text-muted small mb-0">Còn trong thời gian bảo hành quy định.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-info"><i class="bi bi-check-circle-fill"></i></span>
                                    <div>
                                        <div class="fw-bold">Có hóa đơn</div>
                                        <p class="text-muted small mb-0">Kèm hóa đơn gốc hoặc giấy tờ chứng minh mua.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-info"><i class="bi bi-check-circle-fill"></i></span>
                                    <div>
                                        <div class="fw-bold">Chưa sửa chữa</div>
                                        <p class="text-muted small mb-0">Chưa bị tháo rời hoặc sửa chữa trái phép.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h4 fw-bold mb-3">
                        <i class="bi bi-diagram-2 me-2 text-warning"></i>Quy trình bảo hành
                    </h2>
                    <p class="text-muted mb-4">4 bước nhanh gọn, có cập nhật trạng thái qua email.</p>
                    <div class="timeline-warranty">
                        <div class="step mb-4 pb-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">1</div>
                                <div>
                                    <div class="fw-bold">Báo lỗi</div>
                                    <p class="text-muted small mb-1">Liên hệ hotline hoặc trang Liên hệ, cung cấp ảnh chứng minh lỗi.</p>
                                    <span class="badge bg-light text-warning border">Phản hồi trong 24h</span>
                                </div>
                            </div>
                        </div>
                        <div class="step mb-4 pb-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">2</div>
                                <div>
                                    <div class="fw-bold">Kiểm tra</div>
                                    <p class="text-muted small mb-1">Chúng tôi kiểm tra sản phẩm để xác nhận lỗi kỹ thuật.</p>
                                    <span class="badge bg-light text-warning border">Xử lý nhanh</span>
                                </div>
                            </div>
                        </div>
                        <div class="step mb-4 pb-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">3</div>
                                <div>
                                    <div class="fw-bold">Sửa chữa/Thay thế</div>
                                    <p class="text-muted small mb-1">Sửa chữa hoặc thay thế sản phẩm miễn phí.</p>
                                    <span class="badge bg-light text-warning border">5-7 ngày làm việc</span>
                                </div>
                            </div>
                        </div>
                        <div class="step">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">4</div>
                                <div>
                                    <div class="fw-bold">Giao hàng</div>
                                    <p class="text-muted small mb-1">Gửi sản phẩm về cho bạn miễn phí vận chuyển.</p>
                                    <span class="badge bg-light text-warning border">Cập nhật liên tục</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h4 fw-bold mb-3">
                        <i class="bi bi-exclamation-triangle me-2 text-danger"></i>Không áp dụng bảo hành</h2>
                    <p class="text-muted mb-3">Các trường hợp ngoại lệ được loại trừ khỏi bảo hành miễn phí.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-danger"><i class="bi bi-x-circle-fill"></i></span>
                                    <div class="small text-muted">
                                        Hư hỏng do nước, ẩm ướt, va chạm hoặc rơi.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-danger"><i class="bi bi-x-circle-fill"></i></span>
                                    <div class="small text-muted">
                                        Sản phẩm đã quá hạn bảo hành hoặc hết bảo hành.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-danger"><i class="bi bi-x-circle-fill"></i></span>
                                    <div class="small text-muted">
                                        Sử dụng không đúng cách theo hướng dẫn.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-danger"><i class="bi bi-x-circle-fill"></i></span>
                                    <div class="small text-muted">
                                        Số seri/bảo hành bị tẩy xóa hoặc sửa chữa bên thứ ba.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">
                        <i class="bi bi-headset me-2 text-warning"></i>Cần hỗ trợ bảo hành?
                    </h5>
                    <p class="text-muted small mb-3">Đội ngũ CSKH sẵn sàng hỗ trợ. Phản hồi trong 24 giờ.</p>
                    <div class="d-grid gap-2">
                        <a href="index.php?page=contact" class="btn btn-warning">
                            <i class="bi bi-envelope me-2"></i>Gửi yêu cầu bảo hành
                        </a>
                        <a href="tel:0239482958" class="btn btn-outline-warning">
                            <i class="bi bi-telephone me-2"></i>Hotline 0239 482 958
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">
                        <i class="bi bi-clipboard-check me-2 text-success"></i>Mẹo xử lý nhanh
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex gap-3 mb-3">
                            <span class="text-success"><i class="bi bi-camera"></i></span>
                            <div class="small text-muted">Chụp rõ ràng lỗi sản phẩm từ nhiều góc độ.</div>
                        </li>
                        <li class="d-flex gap-3 mb-3">
                            <span class="text-success"><i class="bi bi-receipt"></i></span>
                            <div class="small text-muted">Chuẩn bị hóa đơn hoặc giấy tờ chứng minh mua.</div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="text-success"><i class="bi bi-box"></i></span>
                            <div class="small text-muted">Giữ nguyên bao bì và phụ kiện ban đầu.</div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">
                        <i class="bi bi-info-circle me-2 text-info"></i>Thông tin nhanh
                    </h5>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <div class="small text-muted">Ngắn nhất</div>
                                <div class="fw-bold">7 ngày</div>
                            </div>
                            <i class="bi bi-calendar2-check text-warning fs-5"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <div class="small text-muted">Lâu nhất</div>
                                <div class="fw-bold">12 tháng</div>
                            </div>
                            <i class="bi bi-calendar2-event text-warning fs-5"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <div class="small text-muted">Xử lý</div>
                                <div class="fw-bold">5-7 ngày</div>
                            </div>
                            <i class="bi bi-lightning-charge text-warning fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
