<?php
/**
 * Liên hệ
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Liên hệ</li>
        </ol>
    </nav>

    <h1 class="text-center text-primary mb-4">Liên hệ với chúng tôi</h1>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin liên hệ</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6><i class="fas fa-map-marker-alt text-danger"></i> Địa chỉ</h6>
                        <p class="text-muted"><?= STORE_ADDRESS ?></p>
                    </div>
                    <div class="mb-4">
                        <h6><i class="fas fa-phone text-success"></i> Hotline</h6>
                        <p><a href="tel:<?= STORE_PHONE ?>" class="text-decoration-none h4"><?= STORE_PHONE ?></a></p>
                        <small class="text-muted">Miễn phí - Hoạt động 24/7</small>
                    </div>
                    <div class="mb-4">
                        <h6><i class="fas fa-envelope text-primary"></i> Email</h6>
                        <p><a href="mailto:<?= STORE_EMAIL ?>" class="text-decoration-none"><?= STORE_EMAIL ?></a></p>
                    </div>
                    <div>
                        <h6><i class="fas fa-clock text-warning"></i> Giờ làm việc</h6>
                        <p class="text-muted">Thứ 2 - Chủ nhật: 7:00 - 22:00</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-paper-plane"></i> Gửi tin nhắn</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input type="text" class="form-control" placeholder="Nhập họ tên">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="Nhập email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nội dung</label>
                            <textarea class="form-control" rows="4" placeholder="Nhập nội dung tin nhắn"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="card">
        <div class="card-body p-0">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3929.0!2d106.3!3d9.9!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOcKwNTQnMDAuMCJOIDEwNsKwMTgnMDAuMCJF!5e0!3m2!1svi!2s!4v1234567890" 
                    width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</div>
