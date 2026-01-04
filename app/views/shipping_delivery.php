<style>
    .shipping-hero {
        background: radial-gradient(circle at 30% 50%, #dbeafe, #a7f3d0 40%, #ffffff 75%);
    }
    .shipping-pill {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    /* Timeline (Shipping) - responsive & aligned */
    .timeline-ship {
        position: relative;
        --icon-size: 38px;
        --icon-left: 0.95rem;
        --icon-top: 0.25rem;
    }
    .timeline-ship::before {
        content: "";
        position: absolute;
        left: calc(var(--icon-left) + (var(--icon-size) / 2));
        top: calc(var(--icon-top) + (var(--icon-size) / 2));
        bottom: calc((var(--icon-size) / 2) - var(--icon-top));
        width: 2px;
        background: #e5e7eb;
    }
    .timeline-ship .step {
        position: relative;
        padding-left: 0;
    }
    .timeline-ship .step::before { /* per-step separators no longer needed */
        display: none;
    }
    .timeline-ship .step .d-flex {
        position: relative;
    }
    .timeline-ship .step .icon-circle {
        position: absolute;
        left: var(--icon-left);
        top: var(--icon-top);
        width: var(--icon-size);
        height: var(--icon-size);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        font-weight: 700;
        font-size: 15px;
        box-shadow: 0 8px 18px rgba(16, 185, 129, 0.2);
    }
    .timeline-ship .step .d-flex > div:last-child { /* content block */
        margin-left: 3.5rem; /* space for the absolute icon */
    }
    @media (max-width: 576px) {
        .timeline-ship { --icon-size: 32px; --icon-left: 0.7rem; --icon-top: 0.2rem; }
        .timeline-ship .step .icon-circle { font-size: 14px; }
        .timeline-ship .step .d-flex > div:last-child { margin-left: 3rem; }
    }
</style>

<div class="container mt-5 pb-5">
    <!-- Hero -->
    <div class="shipping-hero rounded-4 p-4 p-lg-5 mb-4 position-relative overflow-hidden shadow-sm">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill shipping-pill mb-3">
                    <span class="badge bg-success rounded-pill">BookZone Logistics</span>
                    <span class="text-muted small">Giao nhanh, đảm bảo an toàn</span>
                </div>
                <h1 class="display-5 fw-bold mb-3">Vận chuyển & Giao hàng</h1>
                <p class="lead text-muted mb-4">Hợp tác với các đối tác vận chuyển uy tín, đảm bảo sản phẩm đến tay bạn nhanh chóng và an toàn.</p>
                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-lightning-charge text-success"></i>
                        <div>
                            <div class="fw-bold">1-2 ngày</div>
                            <small class="text-muted">Giao nhanh</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-globe text-primary"></i>
                        <div>
                            <div class="fw-bold">Toàn quốc</div>
                            <small class="text-muted">Các tỉnh thành</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white rounded-3 shadow-sm">
                        <i class="bi bi-box-seam text-info"></i>
                        <div>
                            <div class="fw-bold">Miễn phí</div>
                            <small class="text-muted">Đơn >500k</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="bg-white rounded-4 shadow-sm p-4 h-100 border text-start">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-success rounded-circle" style="width: 46px; height: 46px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="bi bi-truck"></i>
                        </span>
                        <div>
                            <div class="fw-bold">Nhiều lựa chọn</div>
                            <small class="text-muted">Giao nhanh hoặc tiêu chuẩn</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-info rounded-circle" style="width: 46px; height: 46px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="bi bi-eye"></i>
                        </span>
                        <div>
                            <div class="fw-bold">Theo dõi realtime</div>
                            <small class="text-muted">Mã vận đơn cập nhật liên tục</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-primary rounded-circle" style="width: 46px; height: 46px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <div>
                            <div class="fw-bold">Bảo hiểm hàng</div>
                            <small class="text-muted">Bảo vệ sản phẩm</small>
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
                                <i class="bi bi-truck me-2 text-success"></i>Dịch vụ vận chuyển
                            </h2>
                            <p class="text-muted mb-0">Chọn hình thức giao hàng phù hợp với nhu cầu của bạn.</p>
                        </div>
                        <span class="badge bg-light text-success border">4 lựa chọn</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-success"><i class="bi bi-lightning-charge"></i></span>
                                    <div>
                                        <div class="fw-bold">Giao nhanh</div>
                                        <p class="text-muted small mb-0">1-2 ngày làm việc</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-success"><i class="bi bi-calendar"></i></span>
                                    <div>
                                        <div class="fw-bold">Giao tiêu chuẩn</div>
                                        <p class="text-muted small mb-0">3-5 ngày làm việc</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-success"><i class="bi bi-globe"></i></span>
                                    <div>
                                        <div class="fw-bold">Toàn quốc</div>
                                        <p class="text-muted small mb-0">Tất cả tỉnh thành</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light h-100">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="text-success"><i class="bi bi-shop"></i></span>
                                    <div>
                                        <div class="fw-bold">Lấy tại cửa hàng</div>
                                        <p class="text-muted small mb-0">Miễn phí vận chuyển</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h4 fw-bold mb-3">
                        <i class="bi bi-cash-coin me-2 text-success"></i>Chi phí vận chuyển
                    </h2>
                    <p class="text-muted mb-4">Giá cả rõ ràng, minh bạch, không phí ẩn.</p>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Khoảng cách</th>
                                    <th>Tiêu chuẩn</th>
                                    <th>Nhanh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Nội thành TPHCM</strong></td>
                                    <td>30.000đ</td>
                                    <td>50.000đ</td>
                                </tr>
                                <tr>
                                    <td><strong>Lân cận TPHCM</strong></td>
                                    <td>45.000đ</td>
                                    <td>70.000đ</td>
                                </tr>
                                <tr>
                                    <td><strong>Các tỉnh khác</strong></td>
                                    <td>60.000đ+</td>
                                    <td>90.000đ+</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Miễn phí (Đơn >500k)</strong></td>
                                    <td colspan="2">Tất cả khoảng cách</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> -->

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h4 fw-bold mb-3">
                        <i class="bi bi-diagram-2 me-2 text-info"></i>Quy trình giao hàng
                    </h2>
                    <p class="text-muted mb-4">4 bước rõ ràng từ xác nhận đơn đến nhận hàng.</p>
                    <div class="timeline-ship">
                        <div class="step mb-4 pb-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">1</div>
                                <div>
                                    <div class="fw-bold">Xác nhận đơn</div>
                                    <p class="text-muted small mb-1">Chúng tôi kiểm tra và xác nhận đơn hàng của bạn.</p>
                                    <span class="badge bg-light text-success border">Trong giờ hành chính</span>
                                </div>
                            </div>
                        </div>
                        <div class="step mb-4 pb-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">2</div>
                                <div>
                                    <div class="fw-bold">Chuẩn bị hàng</div>
                                    <p class="text-muted small mb-1">Đóng gói sản phẩm cẩn thận, kiểm tra chất lượng.</p>
                                    <span class="badge bg-light text-success border">1-2 ngày</span>
                                </div>
                            </div>
                        </div>
                        <div class="step mb-4 pb-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">3</div>
                                <div>
                                    <div class="fw-bold">Bàn giao vận chuyển</div>
                                    <p class="text-muted small mb-1">Gửi hàng tới đối tác vận chuyển, cấp mã vận đơn.</p>
                                    <span class="badge bg-light text-success border">Cập nhật ngay</span>
                                </div>
                            </div>
                        </div>
                        <div class="step">
                            <div class="d-flex align-items-start gap-3">
                                <div class="icon-circle">4</div>
                                <div>
                                    <div class="fw-bold">Giao tới bạn</div>
                                    <p class="text-muted small mb-1">Nhận hàng tại địa chỉ của bạn theo thời gian chọn.</p>
                                    <span class="badge bg-light text-success border">1-5 ngày</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h4 fw-bold mb-3">
                        <i class="bi bi-question-circle me-2 text-info"></i>Câu hỏi thường gặp
                    </h2>
                    <div class="accordion" id="shippingFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <i class="bi bi-question-circle text-info me-2"></i>Tôi có thể theo dõi đơn hàng không?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#shippingFAQ">
                                <div class="accordion-body">
                                    Có, sau khi đặt hàng bạn sẽ nhận được mã vận đơn. Bạn có thể theo dõi hàng trên website của công ty vận chuyển hoặc tại tài khoản của bạn trên BookZone.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <i class="bi bi-question-circle text-info me-2"></i>Hàng bị hỏng hoặc thất lạc thì sao?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                <div class="accordion-body">
                                    Vui lòng liên hệ với chúng tôi ngay lập tức với ảnh chứng minh. Chúng tôi sẽ hỗ trợ tối đa để giải quyết vấn đề.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    <i class="bi bi-question-circle text-info me-2"></i>Có thể thay đổi địa chỉ giao hàng không?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#shippingFAQ">
                                <div class="accordion-body">
                                    Bạn có thể thay đổi địa chỉ miễn là hàng chưa được giao cho công ty vận chuyển. Hãy liên hệ nhanh chóng với chúng tôi.
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
                        <i class="bi bi-building me-2 text-success"></i>Đối tác vận chuyển
                    </h5>
                    <p class="text-muted small mb-3">Hợp tác với các công ty hàng đầu Việt Nam:</p>
                    <ul class="list-unstyled">
                        <li class="d-flex gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span class="small">Giao hàng nhanh</span>
                        </li>
                        <li class="d-flex gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span class="small">Viettel Post</span>
                        </li>
                        <li class="d-flex gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span class="small">DHL Express</span>
                        </li>
                        <li class="d-flex gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span class="small">& các đối tác khác</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">
                        <i class="bi bi-star me-2 text-warning"></i>Ưu điểm giao hàng
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex gap-3 mb-3">
                            <span class="text-warning"><i class="bi bi-box2"></i></span>
                            <div class="small text-muted">Đóng gói chuyên nghiệp, cẩn thận</div>
                        </li>
                        <li class="d-flex gap-3 mb-3">
                            <span class="text-warning"><i class="bi bi-shield-check"></i></span>
                            <div class="small text-muted">Bảo hiểm hàng hóa tự động</div>
                        </li>
                        <li class="d-flex gap-3 mb-3">
                            <span class="text-warning"><i class="bi bi-eye"></i></span>
                            <div class="small text-muted">Theo dõi realtime mã vận đơn</div>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="text-warning"><i class="bi bi-headset"></i></span>
                            <div class="small text-muted">Hỗ trợ 24/7 nếu có sự cố</div>
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
                                <div class="small text-muted">Giao nhanh</div>
                                <div class="fw-bold">1-2 ngày</div>
                            </div>
                            <i class="bi bi-lightning-charge text-success fs-5"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <div class="small text-muted">Giao tiêu chuẩn</div>
                                <div class="fw-bold">3-5 ngày</div>
                            </div>
                            <i class="bi bi-calendar-check text-success fs-5"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                            <div>
                                <div class="small text-muted">Miễn phí</div>
                                <div class="fw-bold">Đơn >500k</div>
                            </div>
                            <i class="bi bi-truck text-success fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
