<?php
/**
 * Trang chủ - Home/Index
 */
$baiVietNoiBat = $baiViets[0] ?? null;
$baiVietKhac = array_slice($baiViets ?? [], 1);
?>

<!-- Hero Slider -->
<section class="hero-slider-full">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-slide-full" style="background-image: url('https://images.unsplash.com/photo-1631549916768-4119b2e5f926?w=1920&q=80'); background-size: cover; background-position: center;">
                    <div class="container position-relative">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-lg-5">
                                <div class="hero-content-box bg-white bg-opacity-90 p-4 rounded">
                                    <span class="badge bg-primary mb-3 px-3 py-2">🏥 Nhà Thuốc Uy Tín</span>
                                    <h1 class="display-5 fw-bold mb-3 text-primary">Chăm sóc sức khỏe toàn diện</h1>
                                    <p class="lead mb-4 text-muted">Thuốc chính hãng - Giá tốt nhất - Giao hàng nhanh chóng</p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <a href="<?= BASE_URL ?>/thuoc/danhSach" class="btn btn-primary btn-lg px-4">
                                            <i class="fas fa-shopping-bag me-2"></i> Mua ngay
                                        </a>
                                        <a href="tel:<?= STORE_PHONE ?>" class="btn btn-outline-primary btn-lg px-4">
                                            <i class="fas fa-phone me-2"></i> Tư vấn
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide-full" style="background-image: url('https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=1920&q=80'); background-size: cover; background-position: center;">
                    <div class="container position-relative">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-lg-5">
                                <div class="hero-content-box bg-white bg-opacity-90 p-4 rounded">
                                    <span class="badge bg-success mb-3 px-3 py-2">🚚 Free Ship</span>
                                    <h1 class="display-5 fw-bold mb-3 text-success">Miễn phí giao hàng</h1>
                                    <p class="lead mb-4 text-muted">Giao hàng toàn quốc trong 24h</p>
                                    <a href="<?= BASE_URL ?>/thuoc/danhSach" class="btn btn-success btn-lg px-4">
                                        <i class="fas fa-truck me-2"></i> Khám phá ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide-full" style="background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=1920&q=80'); background-size: cover; background-position: center;">
                    <div class="container position-relative">
                        <div class="row align-items-center min-vh-50">
                            <div class="col-lg-5">
                                <div class="hero-content-box bg-white bg-opacity-90 p-4 rounded">
                                    <span class="badge bg-warning text-dark mb-3 px-3 py-2">👨‍⚕️ Hỗ trợ 24/7</span>
                                    <h1 class="display-5 fw-bold mb-3 text-warning">Tư vấn dược sĩ miễn phí</h1>
                                    <p class="lead mb-4 text-muted">Hotline: <?= STORE_PHONE ?></p>
                                    <a href="tel:<?= STORE_PHONE ?>" class="btn btn-warning btn-lg px-4">
                                        <i class="fas fa-phone me-2"></i> Gọi ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- SẢN PHẨM BÁN CHẠY -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title-main text-primary fw-bold">SẢN PHẨM BÁN CHẠY</h2>
            <p class="text-muted">Một số dòng sản phẩm bán chạy của công ty chúng tôi</p>
        </div>
        
        <div class="row g-3">
            <?php if (!empty($sanPhamBanChay)): ?>
                <?php foreach (array_slice($sanPhamBanChay, 0, 10) as $thuoc): ?>
                    <?php include ROOT . '/app/views/components/product-card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Chưa có sản phẩm</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- SẢN PHẨM KHUYẾN MÃI -->
<section class="py-5">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title-main text-primary fw-bold">SẢN PHẨM KHUYẾN MÃI</h2>
            <p class="text-muted">Săn deal hot - Giá siêu hời</p>
        </div>
        
        <div class="row g-3">
            <?php if (!empty($sanPhamKhuyenMai)): ?>
                <?php foreach (array_slice($sanPhamKhuyenMai, 0, 10) as $thuoc): ?>
                    <?php include ROOT . '/app/views/components/product-card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Chưa có sản phẩm khuyến mãi</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/thuoc/khuyenMai" class="btn btn-outline-danger btn-lg px-5">
                <i class="fas fa-tags me-2"></i> Xem tất cả khuyến mãi
            </a>
        </div>
    </div>
</section>

<!-- SẢN PHẨM MỚI -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title-main text-primary fw-bold">SẢN PHẨM MỚI</h2>
            <p class="text-muted">Một số dòng sản phẩm mới nhất</p>
        </div>
        
        <div class="row g-3">
            <?php if (!empty($sanPhamMoi)): ?>
                <?php foreach (array_slice($sanPhamMoi, 0, 10) as $thuoc): ?>
                    <?php include ROOT . '/app/views/components/product-card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <p class="text-muted">Chưa có sản phẩm mới</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- HÃY CHỌN NHÀ THUỐC -->
<section class="py-5" style="background: linear-gradient(135deg, #1a8ccc 0%, #0d6efd 100%);">
    <div class="container">
        <div class="text-center text-white mb-5">
            <h2 class="fw-bold">HÃY CHỌN <?= strtoupper(STORE_NAME) ?></h2>
            <p>Cam kết mang đến dịch vụ tốt nhất cho khách hàng</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center text-white">
                    <div class="mb-3"><i class="fas fa-headset fa-3x"></i></div>
                    <h5>TƯ VẤN MIỄN PHÍ</h5>
                    <p class="small opacity-75">Tư vấn tận tâm 24/7</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center text-white">
                    <div class="mb-3"><i class="fas fa-truck fa-3x"></i></div>
                    <h5>VẬN CHUYỂN NHANH</h5>
                    <p class="small opacity-75">Giao hàng tận nơi</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center text-white">
                    <div class="mb-3"><i class="fas fa-heart fa-3x"></i></div>
                    <h5>PHỤC VỤ TẬN TÂM</h5>
                    <p class="small opacity-75">Khách hàng là trên hết</p>
                </div>
            </div>
        </div>
    </div>
</section>
