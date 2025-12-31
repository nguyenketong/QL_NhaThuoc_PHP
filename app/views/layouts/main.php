<?php
/**
 * Layout chính - Nhà Thuốc Thanh Hoàn
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $title ?? STORE_NAME ?></title>
    <meta name="description" content="Nhà thuốc online uy tín - Thuốc chính hãng, giá tốt, giao hàng nhanh toàn quốc">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/custom.css" />
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Header Styles */
        .nav-menu-new { display: flex; gap: 5px; }
        .nav-item-new { position: relative; }
        .nav-link-new { display: block; padding: 12px 15px; color: rgba(255,255,255,0.9); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.3s; }
        .nav-link-new:hover, .nav-link-new.active { color: #fff; background: rgba(255,255,255,0.1); }

        /* Nav Dropdown */
        .nav-dropdown { position: relative; }
        .nav-dropdown-menu { position: absolute; top: 100%; left: 0; min-width: 250px; background: #fff; border-radius: 8px; box-shadow: 0 5px 25px rgba(0,0,0,0.15); padding: 10px 0; opacity: 0; visibility: hidden; transform: translateY(10px); transition: all 0.3s ease; z-index: 1000; }
        .nav-dropdown:hover .nav-dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .nav-dropdown-menu a { display: flex; align-items: center; padding: 10px 15px; color: #333; text-decoration: none; font-size: 13px; transition: all 0.2s; }
        .nav-dropdown-menu a:hover { background: #f0f7ff; color: #1a8ccc; }
        .nav-dropdown-menu .brand-icon { width: 30px; height: 20px; object-fit: contain; margin-right: 10px; }
        .nav-dropdown-menu .brand-icon-placeholder { width: 30px; margin-right: 10px; color: #1a8ccc; text-align: center; }
        .nav-dropdown-menu .view-all-brands { border-top: 1px solid #eee; margin-top: 5px; padding-top: 12px; color: #1a8ccc; font-weight: 600; justify-content: center; }
        .nav-dropdown-menu .view-all-brands:hover { background: #1a8ccc; color: #fff; }
        
        .cart-btn-new { display: inline-flex; align-items: center; padding: 8px 20px; background: #28a745; color: #fff; text-decoration: none; border-radius: 20px; font-size: 13px; font-weight: 600; position: relative; }
        .cart-btn-new:hover { background: #218838; color: #fff; }
        .cart-badge-new { position: absolute; top: -5px; right: -5px; background: #dc3545; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center; }
        
        .category-btn-new { background: #f8f9fa; border: 1px solid #dee2e6; padding: 8px 15px; font-size: 14px; font-weight: 500; color: #333; }
        .category-btn-new:hover { background: #e9ecef; }
        
        .cat-dropdown { position: relative; }
        .category-link-new { color: #333; text-decoration: none; font-size: 13px; font-weight: 600; padding: 8px 10px; transition: all 0.3s; display: inline-block; white-space: nowrap; }
        .category-link-new:hover { color: #1a8ccc; }
        .cat-dropdown-menu { position: absolute; top: 100%; left: 0; min-width: 220px; background: #fff; border-radius: 8px; box-shadow: 0 5px 25px rgba(0,0,0,0.15); padding: 10px 0; opacity: 0; visibility: hidden; transform: translateY(10px); transition: all 0.3s ease; z-index: 1000; }
        .cat-dropdown-menu-right { left: auto; right: 0; }
        .cat-dropdown:hover .cat-dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .cat-dropdown-menu a { display: block; padding: 10px 20px; color: #333; text-decoration: none; font-size: 13px; transition: all 0.2s; }
        .cat-dropdown-menu a:hover { background: #f0f7ff; color: #1a8ccc; padding-left: 25px; }
        .cat-dropdown-menu a.view-all { border-top: 1px solid #eee; margin-top: 5px; padding-top: 12px; color: #1a8ccc; font-weight: 600; }
        .cat-dropdown-menu a.view-all:hover { background: #1a8ccc; color: #fff; }
        
        .mega-menu-new { min-width: 700px; padding: 0; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.15); }
        
        .notification-badge { position: absolute; top: -8px; right: -8px; background: #dc3545; color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center; }
        .notification-dropdown .notification-item { display: block; padding: 12px 15px; border-bottom: 1px solid #eee; text-decoration: none; color: #333; transition: background 0.2s; }
        .notification-dropdown .notification-item:hover { background: #f8f9fa; }
        .notification-dropdown .notification-item.unread { background: #f0f7ff; }
        .notification-dropdown .notification-item .notif-title { font-weight: 600; font-size: 13px; margin-bottom: 3px; }
        .notification-dropdown .notification-item .notif-content { font-size: 12px; color: #666; margin-bottom: 3px; }
        .notification-dropdown .notification-item .notif-time { font-size: 11px; color: #999; }
        
        .header-wrapper { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; transition: all 0.3s ease; }
        .header-scrolled { box-shadow: 0 2px 20px rgba(0,0,0,0.15); }
        .main-header-hidden { display: none !important; height: 0 !important; padding: 0 !important; overflow: hidden; }
        .header-spacer { height: 200px; }
        @media (max-width: 991px) { .header-spacer { height: 160px; } }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include ROOT . '/app/views/layouts/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (isset($_SESSION['flash']['success'])): ?>
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['flash']['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['flash']['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['flash']['error'])): ?>
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['flash']['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            <?php unset($_SESSION['flash']['error']); ?>
        <?php endif; ?>
        
        <?= $content ?>
    </main>

    <!-- Footer -->
    <?php include ROOT . '/app/views/layouts/footer.php'; ?>

    <!-- Back to Top -->
    <button id="backToTop" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Floating Buttons -->
    <div class="floating-buttons-wrapper">
        <div class="float-btn-main" title="Liên hệ">
            <i class="fas fa-share-alt"></i>
        </div>
        <div class="floating-buttons">
            <a href="https://zalo.me/<?= STORE_PHONE ?>" target="_blank" class="float-btn zalo" title="Chat Zalo">
                <i class="fas fa-comment-dots"></i>
                <span class="float-label">Zalo</span>
            </a>
            <a href="<?= BASE_URL ?>/gioHang" class="float-btn cart" title="Giỏ hàng">
                <i class="fas fa-shopping-cart"></i>
                <span class="float-label">Giỏ hàng</span>
            </a>
            <a href="tel:<?= STORE_PHONE ?>" class="float-btn phone" title="Gọi điện">
                <i class="fas fa-phone"></i>
                <span class="float-label">Gọi ngay</span>
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Back to top
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            backToTop.classList.toggle('show', window.scrollY > 300);
        });
        backToTop?.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Auto hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);

        // Cập nhật số lượng giỏ hàng
        function capNhatSoLuongGioHang() {
            fetch('<?= BASE_URL ?>/gioHang/laySoLuong')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.soLuong;
                })
                .catch(err => console.log('Lỗi:', err));
        }

        // Thêm vào giỏ hàng AJAX
        function themVaoGioHang(maThuoc, soLuong = 1) {
            fetch('<?= BASE_URL ?>/gioHang/themAjax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `maThuoc=${maThuoc}&soLuong=${soLuong}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-count').textContent = data.soLuong;
                    hienThiThongBao(data.message, 'success');
                } else {
                    hienThiThongBao(data.message, 'danger');
                }
            })
            .catch(err => hienThiThongBao('Có lỗi xảy ra', 'danger'));
        }

        // Hiển thị thông báo toast
        function hienThiThongBao(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 180px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            setTimeout(() => alertDiv.remove(), 3000);
        }
        
        document.addEventListener('DOMContentLoaded', capNhatSoLuongGioHang);
    </script>
</body>
</html>
