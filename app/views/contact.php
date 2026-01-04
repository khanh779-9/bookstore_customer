<div class="container mt-5 pb-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold mb-3">Liên hệ với chúng tôi</h1>
        <p class="lead text-muted">Chúng tôi luôn sẵn lòng lắng nghe! Đừng ngần ngại liên hệ nếu bạn có bất kỳ câu hỏi hay góp ý nào.</p>
    </div>


    <div class="row g-4">
        <!-- Left: Store Info -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-5">
                    <h2 class="h4 fw-bold mb-4">
                        <i class="bi bi-shop me-2"></i>Thông tin cửa hàng
                    </h2>

                    <div class="vstack gap-4">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(13,110,253,0.1); border-radius: 8px;">
                                    <i class="bi bi-geo-alt-fill fs-5 text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold mb-1">Địa chỉ</div>
                                <div class="text-muted">180 Cao Lỗ, Quận 8, TP.HCM</div>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(13,110,253,0.1); border-radius: 8px;">
                                    <i class="bi bi-telephone-fill fs-5 text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold mb-1">Điện thoại</div>
                                <a class="text-decoration-none text-primary" href="tel:+84239482958">(+84) 0239 482 958</a>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(13,110,253,0.1); border-radius: 8px;">
                                    <i class="bi bi-envelope-fill fs-5 text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold mb-1">Email</div>
                                <a class="text-decoration-none text-primary" href="mailto:qkhanh12.duration060@passinbox.com">qkhanh12.duration060@passinbox.com</a>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(13,110,253,0.1); border-radius: 8px;">
                                    <i class="bi bi-clock-fill fs-5 text-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold mb-1">Giờ làm việc</div>
                                <div class="text-muted">Thứ Hai - Chủ Nhật: 8:00 - 22:00</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h3 class="h5 fw-bold mb-3">Tìm chúng tôi trên bản đồ</h3>
                    <div class="ratio ratio-16x9 mb-4 rounded-3 overflow-hidden shadow">
                        <!-- Google Maps Embed -->
                        <iframe
                            src="https://www.google.com/maps?q=Trường+Đại+Học+Công+Nghệ+Sài+Gòn+180+Cao+Lỗ+Quận+8+TP.HCM&output=embed"
                            title="Bản đồ cửa hàng"
                            style="border:0;" allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right: Contact Form -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h2 class="h4 fw-bold mb-4" id="contact-form">
                        <i class="bi bi-chat-left-text me-2"></i>Gửi tin nhắn cho chúng tôi
                    </h2>

                    <form class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="fullName" class="form-label required">Họ và tên</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="fullName" 
                                    name="fullName" 
                                    placeholder="Nhập họ và tên của bạn" 
                                    required />
                                <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label required">Email</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    placeholder="your.email@example.com" 
                                    required />
                                <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Số điện thoại (tùy chọn)</label>
                                <input 
                                    type="tel" 
                                    class="form-control" 
                                    id="phone" 
                                    name="phone" 
                                    placeholder="0123 456 789" />
                            </div>

                            <div class="col-12">
                                <label for="subject" class="form-label required">Tiêu đề</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="subject" 
                                    name="subject" 
                                    placeholder="Ví dụ: Hỏi về đơn hàng..." 
                                    required />
                                <div class="invalid-feedback">Vui lòng nhập tiêu đề.</div>
                            </div>

                            <div class="col-12">
                                <label for="message" class="form-label required">Nội dung tin nhắn</label>
                                <textarea 
                                    class="form-control" 
                                    id="message" 
                                    name="message" 
                                    rows="5" 
                                    placeholder="Xin chào, tôi muốn hỏi..." 
                                    required></textarea>
                                <div class="invalid-feedback">Vui lòng nhập nội dung tin nhắn.</div>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        value="" 
                                        id="consent" 
                                        required />
                                    <label class="form-check-label" for="consent">
                                        Tôi đồng ý để nhà sách liên hệ lại về nội dung tôi đã gửi.
                                    </label>
                                    <div class="invalid-feedback">Vui lòng xác nhận đồng ý.</div>
                                </div>
                            </div>

                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                                    <i class="bi bi-send me-2"></i>Gửi ngay
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Alert (ẩn, hiện khi submit thành công) -->
                    <div class="alert alert-success d-none mt-4" role="alert" id="successAlert">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Thành công!</strong> Tin nhắn đã được gửi. Chúng tôi sẽ phản hồi trong thời gian sớm nhất.
                    </div>
                </div>
            </div>
        </div>
    </div>
