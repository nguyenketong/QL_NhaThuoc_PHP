<?php
/**
 * Footer Component
 */
?>
<footer class="main-footer bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <a href="<?= BASE_URL ?>" class="logo mb-3 d-inline-block text-decoration-none">
                        <i class="fas fa-clinic-medical text-primary fa-2x"></i>
                        <span class="logo-text ms-2 fw-bold text-white">NhàThuốc</span>
                    </a>
                    <p class="text-muted">Hệ thống nhà thuốc uy tín hàng đầu Việt Nam. Cam kết cung cấp thuốc chính hãng, giá tốt nhất.</p>
                    <div class="social-links">
                        <a href="https://www.facebook.com" target="_blank" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-white me-3"><i class="fas fa-comment-dots"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white mb-3">Về chúng tôi</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= BASE_URL ?>/home/gioiThieu" class="text-muted">Giới thiệu</a></li>
                    <li><a href="#" class="text-muted">Hệ thống cửa hàng</a></li>
                    <li><a href="#" class="text-muted">Giấy phép kinh doanh</a></li>
                    <li><a href="#" class="text-muted">Quy chế hoạt động</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white mb-3">Danh mục</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= BASE_URL ?>/thuoc/theoNhom/1" class="text-muted">Thuốc giảm đau</a></li>
                    <li><a href="<?= BASE_URL ?>/thuoc/theoNhom/5" class="text-muted">Vitamin & TPCN</a></li>
                    <li><a href="<?= BASE_URL ?>/thuoc/theoNhom/4" class="text-muted">Thuốc cảm cúm</a></li>
                    <li><a href="<?= BASE_URL ?>/nhomThuoc/danhSach" class="text-muted">Xem tất cả</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white mb-3">Hỗ trợ</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#" class="text-muted">Hướng dẫn mua hàng</a></li>
                    <li><a href="#" class="text-muted">Chính sách đổi trả</a></li>
                    <li><a href="#" class="text-muted">Chính sách bảo mật</a></li>
                    <li><a href="<?= BASE_URL ?>/home/lienHe" class="text-muted">Liên hệ</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white mb-3">Tổng đài</h5>
                <ul class="list-unstyled footer-links">
                    <li>
                        <i class="fas fa-phone-alt text-primary"></i>
                        <a href="tel:<?= STORE_PHONE ?>" class="text-white"><strong><?= STORE_PHONE ?></strong></a>
                        <br><small class="text-muted">Miễn phí 24/7</small>
                    </li>
                    <li class="mt-2">
                        <i class="fas fa-envelope text-primary"></i>
                        <a href="mailto:<?= STORE_EMAIL ?>" class="text-muted"><?= STORE_EMAIL ?></a>
                    </li>
                </ul>
            </div>
        </div>
        <hr class="my-4 border-secondary" />
        <div class="footer-bottom text-center">
            <p class="mb-0 text-muted">&copy; 2025 <?= STORE_NAME ?>. All rights reserved.</p>
        </div>
    </div>
</footer>
