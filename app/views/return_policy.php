<style>
    .return-hero {
        background: radial-gradient(circle at 20% 20%, #eef2ff, #e0f2fe 45%, #ffffff 80%);
    }
    .return-pill {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    /* Timeline (Return Policy) - responsive & aligned */
    .timeline-clean { position: relative; --icon-size: 38px; --icon-left: 0.95rem; --icon-top: 0.25rem; }
    .timeline-clean::before {
        content: "";
        position: absolute;
        left: calc(var(--icon-left) + (var(--icon-size) / 2));
        top: calc(var(--icon-top) + (var(--icon-size) / 2));
        bottom: calc((var(--icon-size) / 2) - var(--icon-top));
        width: 2px;
        background: #e5e7eb;
    }
    .timeline-clean .step { position: relative; padding-left: 0; }
    .timeline-clean .step::before { display: none; }
    .timeline-clean .step .d-flex { position: relative; }
    .timeline-clean .step .icon-circle {
        position: absolute;
        left: var(--icon-left);
        top: var(--icon-top);
        width: var(--icon-size);
        height: var(--icon-size);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #2563eb);
        color: #fff;
        font-weight: 700;
        font-size: 15px;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.2);
    }
    .timeline-clean .step .d-flex > div:last-child { margin-left: 3.5rem; }
    @media (max-width: 576px) {
        .timeline-clean { --icon-size: 32px; --icon-left: 0.7rem; --icon-top: 0.2rem; }
        .timeline-clean .step .icon-circle { font-size: 14px; }
        .timeline-clean .step .d-flex > div:last-child { margin-left: 3rem; }
    }
</style>

<div class="container mt-5 pb-5">
    <!-- Hero -->
    <div class="return-hero rounded-4 p-4 p-lg-5 mb-4 position-relative overflow-hidden shadow-sm">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill return-pill mb-3">
                    <span class="badge bg-primary rounded-pill">BookZone Care</span>
                    <span class="text-muted small">Đổi trả nhanh - xử lý minh bạch</span>
                </div>
                <h1 class="display-5 fw-bold mb-3">Chính sách đổi trả</h1>
                <p class="lead text-muted mb-4">Tự tin mua sắm với quy trình đổi trả rõ ràng, linh hoạt và ưu tiên quyền lợi khách hàng.</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-stopwatch text-primary"></i>
                        <div>
                            <div class="fw-bold">30 ngày</div>
                            <small class="text-muted">Cửa sổ đổi trả</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-truck text-success"></i>
                        <div>
                            <div class="fw-bold">Miễn phí</div>
                            <small class="text-muted">Phí vận chuyển hàng lỗi</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-lightning-charge text-warning"></i>
                        <div>
                            <div class="fw-bold">5-7 ngày</div>
                            <small class="text-muted">Thời gian xử lý</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="bg-white rounded-4 shadow-sm p-4 h-100 border text-start">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-success rounded-circle" style="width: 46px; height: 46px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <div>
                            <div class="fw-bold">An tâm 1 đổi 1</div>
                            <small class="text-muted">Áp dụng với sản phẩm lỗi do nhà sản xuất</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-info rounded-circle" style="width: 46px; height: 46px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="bi bi-chat-dots"></i>
                        </span>
                        <div>
                            <div class="fw-bold">Hỗ trợ đa kênh</div>
                            <small class="text-muted">Hotline, email, chat trong giờ hành chính</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-primary rounded-circle" style="width: 46px; height: 46px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="bi bi-clock-history"></i>
                        </span>
                        <div>
                            <div class="fw-bold">Theo dõi tiến độ</div>
                            <small class="text-muted">Cập nhật trạng thái đổi trả qua email</small>
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
                                <i class="bi bi-arrow-left-right me-2 text-primary"></i>Điều kiện đổi trả
                            </h2>
                            <p class="text-muted mb-0">Đặt lợi ích khách hàng làm trọng tâm với tiêu chí rõ ràng, minh bạch.</p>
                        </div>
                        <span class="badge bg-light text-primary border">Áp dụng trong 30 ngày</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-primary"><i class="bi bi-check-circle-fill"></i></span>
                                    <div>
                                        <div class="fw-bold">Thời gian</div>
                                        <p class="text-muted small mb-0">Đổi trả trong vòng 30 ngày kể từ ngày nhận hàng.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-primary"><i class="bi bi-bag-check-fill"></i></span>
                                    <div>
                                        <div class="fw-bold">Tình trạng</div>
                                        <p class="text-muted small mb-0">Sản phẩm còn nguyên vẹn, chưa sử dụng hoặc lỗi do nhà sản xuất.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-primary"><i class="bi bi-receipt"></i></span>
                                    <div>
                                        <div class="fw-bold">Hóa đơn & bao bì</div>
                                        <p class="text-muted small mb-0">Kèm hóa đơn gốc, tem mác và phụ kiện (nếu có).</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-primary"><i class="bi bi-truck"></i></span>
                                    <div>
                                        <div class="fw-bold">Chi phí vận chuyển</div>
                                        <p class="text-muted small mb-0">Miễn phí với sản phẩm lỗi kỹ thuật, hỗ trợ tối đa cho bạn.</p>
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
                        <i class="bi bi-bag-check me-2 text-success"></i>Quy trình đổi trả
                    </h2>
                    <p class="text-muted mb-4">4 bước gọn nhẹ, có cập nhật trạng thái qua email để bạn luôn chủ động.</p>
                    <div class="timeline-clean">
                        <div class="step mb-4 pb-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">1</div>
                                <div>
                                    <div class="fw-bold">Báo yêu cầu</div>
                                    <p class="text-muted small mb-1">Liên hệ hotline 0239 482 958 hoặc trang Liên hệ, cung cấp mã đơn và lý do đổi trả.</p>
                                    <span class="badge bg-light text-primary border">Phản hồi trong 24h</span>
                                </div>
                            </div>
                        </div>
                        <div class="step mb-4 pb-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">2</div>
                                <div>
                                    <div class="fw-bold">Chuẩn bị sản phẩm</div>
                                    <p class="text-muted small mb-1">Giữ nguyên phụ kiện, tem mác. Đóng gói sản phẩm cùng hóa đơn/phiếu mua.</p>
                                    <span class="badge bg-light text-primary border">Hỗ trợ lấy hàng tận nơi</span>
                                </div>
                            </div>
                        </div>
                        <div class="step mb-4 pb-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">3</div>
                                <div>
                                    <div class="fw-bold">Gửi về BookZone</div>
                                    <p class="text-muted small mb-1">Gửi đến địa chỉ trung tâm đổi trả hoặc điểm gửi được hướng dẫn.</p>
                                    <span class="badge bg-light text-primary border">Miễn phí nếu lỗi NSX</span>
                                </div>
                            </div>
                        </div>
                        <div class="step">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">4</div>
                                <div>
                                    <div class="fw-bold">Nhận kết quả</div>
                                    <p class="text-muted small mb-1">Xử lý trong 5-7 ngày làm việc: hoàn tiền, đổi mới hoặc sửa chữa theo yêu cầu.</p>
                                    <span class="badge bg-light text-primary border">Cập nhật liên tục qua email</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h4 fw-bold mb-3">
                        <i class="bi bi-exclamation-triangle me-2 text-warning"></i>Không áp dụng đổi trả</h2>
                    <p class="text-muted mb-3">Một số trường hợp ngoại lệ nhằm đảm bảo công bằng cho tất cả khách hàng.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-warning"><i class="bi bi-x-circle-fill"></i></span>
                                    <div class="small text-muted">
                                        Sản phẩm đã qua sử dụng, hư hỏng do tác động hoặc thay đổi thiết kế ban đầu.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-warning"><i class="bi bi-x-circle-fill"></i></span>
                                    <div class="small text-muted">
                                        Sản phẩm quá 30 ngày kể từ ngày nhận hoặc thiếu hóa đơn, tem mác, bao bì gốc.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-warning"><i class="bi bi-x-circle-fill"></i></span>
                                    <div class="small text-muted">
                                        Hàng đặt riêng theo yêu cầu đặc biệt hoặc sản phẩm thuộc danh mục không hỗ trợ.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-warning"><i class="bi bi-x-circle-fill"></i></span>
                                    <div class="small text-muted">
                                        Sản phẩm bị can thiệp, sửa chữa bởi bên thứ ba không được BookZone ủy quyền.
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
                        <i class="bi bi-headset me-2 text-primary"></i>Cần hỗ trợ ngay?
                    </h5>
                    <p class="text-muted small mb-3">Đội ngũ CSKH sẵn sàng hỗ trợ trong giờ hành chính. Chúng tôi sẽ phản hồi trong 24h.</p>
                    <div class="d-grid gap-2">
                        <a href="index.php?page=contact" class="btn btn-primary">
                            <i class="bi bi-envelope me-2"></i>Gửi yêu cầu đổi trả
                        </a>
                        <a href="tel:0239482958" class="btn btn-outline-primary">
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
                            <div class="small text-muted">Chụp rõ lỗi sản phẩm và đính kèm khi gửi yêu cầu.</div>
                        </li>
                        <li class="d-flex gap-3 mb-3">
                            <span class="text-success"><i class="bi bi-box"></i></span>
                            <div class="small text-muted">Giữ đầy đủ phụ kiện, tem mác để rút ngắn thời gian kiểm tra.</div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="text-success"><i class="bi bi-mailbox"></i></span>
                            <div class="small text-muted">Ưu tiên gửi tại điểm vận chuyển được hướng dẫn để miễn phí.</div>
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
                                <div class="small text-muted">Thời gian đổi trả</div>
                                <div class="fw-bold">30 ngày</div>
                            </div>
                            <i class="bi bi-calendar2-check text-primary fs-5"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <div class="small text-muted">Phí vận chuyển</div>
                                <div class="fw-bold">Miễn phí hàng lỗi</div>
                            </div>
                            <i class="bi bi-truck text-success fs-5"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <div class="small text-muted">Thời gian xử lý</div>
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
