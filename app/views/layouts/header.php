<?php
/**
 * Header Component
 */
$cartCount = 0;
if (isset($_COOKIE['GioHang'])) {
    $gioHang = json_decode($_COOKIE['GioHang'], true) ?: [];
    $cartCount = array_sum(array_column($gioHang, 'SoLuong'));
}
$userId = $_SESSION['user_id'] ?? ($_COOKIE['UserId'] ?? null);
$hoTen = $_SESSION['user_name'] ?? '';
$soDienThoai = $_SESSION['user_phone'] ?? '';

// Load nhóm thuốc
$db = Database::getInstance()->getConnection();
$nhomThuocs = $db->query("SELECT * FROM NHOM_THUOC ORDER BY TenNhomThuoc")->fetchAll(PDO::FETCH_ASSOC);
$danhMucCha = array_filter($nhomThuocs, fn($n) => empty($n['MaDanhMucCha']));
if (empty($danhMucCha)) $danhMucCha = $nhomThuocs;

// Load thương hiệu
$thuongHieus = $db->query("SELECT * FROM THUONG_HIEU ORDER BY TenThuongHieu LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Fixed Header Wrapper -->
<div class="header-wrapper" id="headerWrapper">
    <!-- Top Bar -->
    <div class="top-bar" style="background: #1a8ccc; padding: 6px 0;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-white small">
                    <marquee behavior="scroll" direction="left" scrollamount="3">
                        <?= STORE_NAME ?> - Chuyên cung cấp thuốc chính hãng, giá tốt nhất thị trường. Hotline: <?= STORE_PHONE ?>
                    </marquee>
                </div>
                <div class="d-none d-md-block">
                    <select class="form-select form-select-sm bg-transparent text-white border-0" style="width: auto; font-size: 12px;">
                        <option>🌐 Select Language</option>
                        <option>Tiếng Việt</option>
                        <option>English</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header bg-white py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-3 col-6">
                    <a href="<?= BASE_URL ?>" class="d-flex align-items-center text-decoration-none">
                        <div class="logo-icon me-2">
                            <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #1a8ccc, #28a745); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clinic-medical text-white fa-2x"></i>
                            </div>
                        </div>
                        <div class="logo-text">
                            <div style="font-size: 1.5rem; font-weight: bold; color: #1a8ccc; line-height: 1.2;">NHÀ THUỐC TÂY</div>
                            <div style="font-size: 1.8rem; font-weight: bold; color: #d63384; line-height: 1;">THANH HOÀN</div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-5 col-md-5 d-none d-md-block">
                    <form action="<?= BASE_URL ?>/thuoc/timKiem" method="get">
                        <div class="input-group" style="border: 2px solid #1a8ccc; border-radius: 25px; overflow: hidden;">
                            <input type="text" name="tuKhoa" class="form-control border-0 py-2 px-4" placeholder="Nhập từ khóa cần tìm..." style="box-shadow: none;">
                            <button class="btn px-4" type="submit" style="background: #1a8ccc; color: white;"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 col-md-4 col-6 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="hotline-box d-flex align-items-center">
                            <div class="hotline-icon me-2"><i class="fas fa-phone-alt fa-lg" style="color: #28a745;"></i></div>
                            <div class="hotline-text">
                                <small class="text-muted d-block" style="font-size: 11px;">Hotline hỗ trợ</small>
                                <strong style="color: #1a8ccc; font-size: 1.3rem;"><?= STORE_PHONE ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row d-md-none mt-2">
                <div class="col-12">
                    <form action="<?= BASE_URL ?>/thuoc/timKiem" method="get">
                        <div class="input-group input-group-sm">
                            <input type="text" name="tuKhoa" class="form-control" placeholder="Tìm kiếm...">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Primary Navigation -->
    <nav class="primary-nav" style="background: #1a8ccc;">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <ul class="nav-menu-new d-none d-lg-flex list-unstyled mb-0">
                    <li class="nav-item-new">
                        <a href="<?= BASE_URL ?>" class="nav-link-new"><i class="fas fa-home me-1"></i> Trang chủ</a>
                    </li>
                    <li class="nav-item-new">
                        <a href="<?= BASE_URL ?>/home/gioiThieu" class="nav-link-new"><i class="fas fa-info-circle me-1"></i> Giới thiệu</a>
                    </li>
                    <li class="nav-item-new">
                        <a href="<?= BASE_URL ?>/thuoc/khuyenMai" class="nav-link-new"><i class="fas fa-tags me-1"></i> Khuyến mãi</a>
                    </li>
                    <li class="nav-item-new">
                        <a href="<?= BASE_URL ?>/baiViet/danhSach" class="nav-link-new"><i class="fas fa-share-alt me-1"></i> Góc chia sẻ</a>
                    </li>
                    <li class="nav-item-new nav-dropdown">
                        <a href="<?= BASE_URL ?>/thuongHieu/danhSach" class="nav-link-new">
                            <i class="fas fa-building me-1"></i> Thương hiệu <i class="fas fa-chevron-down ms-1 small"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <?php foreach ($thuongHieus as $th): ?>
                                <a href="<?= BASE_URL ?>/thuongHieu/chiTiet/<?= $th['MaThuongHieu'] ?>">
                                    <?php if (!empty($th['HinhAnh'])): ?>
                                        <img src="<?= BASE_URL . $th['HinhAnh'] ?>" alt="" class="brand-icon">
                                    <?php else: ?>
                                        <i class="fas fa-building brand-icon-placeholder"></i>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($th['TenThuongHieu']) ?>
                                </a>
                            <?php endforeach; ?>
                            <a href="<?= BASE_URL ?>/thuongHieu/danhSach" class="view-all-brands">
                                Xem tất cả <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item-new">
                        <a href="<?= BASE_URL ?>/home/lienHe" class="nav-link-new"><i class="fas fa-phone-alt me-1"></i> Liên hệ</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <?php if ($userId): ?>
                        <!-- Thông báo -->
                        <div class="dropdown">
                            <a href="#" class="text-white position-relative" data-bs-toggle="dropdown" id="notificationDropdown">
                                <i class="fas fa-bell fa-lg"></i>
                                <span class="notification-badge" id="notification-count" style="display:none;">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                    <strong>Thông báo</strong>
                                    <a href="#" onclick="danhDauTatCaDaDoc()" class="small text-primary">Đánh dấu đã đọc</a>
                                </div>
                                <div id="notification-list">
                                    <div class="text-center py-3 text-muted">Đang tải...</div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($hoTen ?: $soDienThoai) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/profile"><i class="fas fa-user me-2"></i>Thông tin cá nhân</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/don-hang"><i class="fas fa-box me-2"></i>Đơn hàng của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/user/logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/user/phoneLogin" class="text-white text-decoration-none"><i class="fas fa-sign-in-alt me-1"></i> Đăng nhập</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/gio-hang" class="cart-btn-new">
                        <i class="fas fa-shopping-cart me-1"></i> GIỎ HÀNG
                        <span class="cart-badge-new" id="cart-count"><?= $cartCount ?></span>
                    </a>
                </div>
                <button class="btn text-white d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Secondary Navigation - Danh mục -->
    <nav class="secondary-nav bg-white border-bottom py-2">
        <div class="container">
            <div class="d-flex align-items-center">
                <!-- Category Button with Mega Menu -->
                <div class="dropdown me-4">
                    <button class="btn category-btn-new dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bars me-2"></i> Danh mục
                    </button>
                    <div class="dropdown-menu mega-menu-new">
                        <div class="row g-0">
                            <?php 
                            $chunks = array_chunk(array_slice($danhMucCha, 0, 12), 4);
                            foreach ($chunks as $chunk): 
                            ?>
                                <div class="col-md-3 border-end">
                                    <div class="p-3">
                                        <?php foreach ($chunk as $nhom): ?>
                                            <div class="mb-3">
                                                <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $nhom['MaNhomThuoc'] ?>" class="fw-bold text-primary text-decoration-none d-block mb-1">
                                                    <?= htmlspecialchars($nhom['TenNhomThuoc']) ?>
                                                </a>
                                                <?php 
                                                $danhMucCon = array_filter($nhomThuocs, fn($n) => $n['MaDanhMucCha'] == $nhom['MaNhomThuoc']);
                                                if (!empty($danhMucCon)): 
                                                ?>
                                                    <ul class="list-unstyled ms-2 small">
                                                        <?php foreach (array_slice($danhMucCon, 0, 4) as $con): ?>
                                                            <li><a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $con['MaNhomThuoc'] ?>" class="text-muted text-decoration-none"><?= htmlspecialchars($con['TenNhomThuoc']) ?></a></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <a href="<?= BASE_URL ?>/nhom-thuoc" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-th-large me-1"></i> Xem tất cả (<?= count($nhomThuocs) ?>)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Links -->
                <div class="category-links d-none d-lg-flex flex-wrap gap-1">
                    <?php foreach (array_slice($danhMucCha, 0, 6) as $nhomCha): ?>
                        <?php $danhMucCon = array_filter($nhomThuocs, fn($n) => $n['MaDanhMucCha'] == $nhomCha['MaNhomThuoc']); ?>
                        <?php if (!empty($danhMucCon)): ?>
                            <div class="cat-dropdown">
                                <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $nhomCha['MaNhomThuoc'] ?>" class="category-link-new">
                                    <?= htmlspecialchars($nhomCha['TenNhomThuoc']) ?> <i class="fas fa-chevron-down ms-1 small"></i>
                                </a>
                                <div class="cat-dropdown-menu">
                                    <?php foreach ($danhMucCon as $con): ?>
                                        <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $con['MaNhomThuoc'] ?>"><?= htmlspecialchars($con['TenNhomThuoc']) ?></a>
                                    <?php endforeach; ?>
                                    <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $nhomCha['MaNhomThuoc'] ?>" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $nhomCha['MaNhomThuoc'] ?>" class="category-link-new">
                                <?= htmlspecialchars($nhomCha['TenNhomThuoc']) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (count($danhMucCha) > 6): ?>
                        <div class="cat-dropdown">
                            <a href="#" class="category-link-new">XEM THÊM <i class="fas fa-chevron-down ms-1 small"></i></a>
                            <div class="cat-dropdown-menu cat-dropdown-menu-right">
                                <?php foreach (array_slice($danhMucCha, 6) as $nhom): ?>
                                    <a href="<?= BASE_URL ?>/nhom-thuoc/chi-tiet/<?= $nhom['MaNhomThuoc'] ?>"><?= htmlspecialchars($nhom['TenNhomThuoc']) ?></a>
                                <?php endforeach; ?>
                                <a href="<?= BASE_URL ?>/nhom-thuoc" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="collapse d-lg-none bg-white border-bottom" id="mobileMenu">
        <div class="container py-3">
            <ul class="list-unstyled mb-0">
                <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>" class="text-dark text-decoration-none"><i class="fas fa-home me-2 text-primary"></i> Trang chủ</a></li>
                <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/thuoc/danhSach" class="text-dark text-decoration-none"><i class="fas fa-capsules me-2 text-primary"></i> Sản phẩm</a></li>
                <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/nhom-thuoc" class="text-dark text-decoration-none"><i class="fas fa-th-list me-2 text-primary"></i> Danh mục</a></li>
                <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/home/gioiThieu" class="text-dark text-decoration-none"><i class="fas fa-info-circle me-2 text-primary"></i> Giới thiệu</a></li>
                <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/home/lienHe" class="text-dark text-decoration-none"><i class="fas fa-phone-alt me-2 text-primary"></i> Liên hệ</a></li>
                <?php if ($userId): ?>
                    <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/user/profile" class="text-dark text-decoration-none"><i class="fas fa-user me-2 text-primary"></i> Tài khoản</a></li>
                    <li class="py-2 border-bottom"><a href="<?= BASE_URL ?>/don-hang" class="text-dark text-decoration-none"><i class="fas fa-box me-2 text-primary"></i> Đơn hàng</a></li>
                    <li class="py-2"><a href="<?= BASE_URL ?>/user/logout" class="text-danger text-decoration-none"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                <?php else: ?>
                    <li class="py-2"><a href="<?= BASE_URL ?>/user/phoneLogin" class="text-primary text-decoration-none"><i class="fas fa-sign-in-alt me-2"></i> Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<div class="header-spacer"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const headerWrapper = document.getElementById('headerWrapper');
    const mainHeader = document.querySelector('.main-header');
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop > 100) {
            headerWrapper.classList.add('header-scrolled');
            if (mainHeader) mainHeader.classList.add('main-header-hidden');
        } else {
            headerWrapper.classList.remove('header-scrolled');
            if (mainHeader) mainHeader.classList.remove('main-header-hidden');
        }
    });

    <?php if ($userId): ?>
    // Load thông báo
    loadThongBao();
    setInterval(loadThongBao, 30000);
    <?php endif; ?>
});

async function loadThongBao() {
    try {
        const countRes = await fetch('<?= BASE_URL ?>/thong-bao/laySoLuongChuaDoc');
        const countData = await countRes.json();
        const badge = document.getElementById('notification-count');
        if (countData.soLuong > 0) {
            badge.textContent = countData.soLuong > 9 ? '9+' : countData.soLuong;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }

        const listRes = await fetch('<?= BASE_URL ?>/thong-bao/layDanhSach');
        const listData = await listRes.json();
        const listEl = document.getElementById('notification-list');
        
        if (listData.thongBaos && listData.thongBaos.length > 0) {
            listEl.innerHTML = listData.thongBaos.map(tb => `
                <a href="${tb.duongDan || '#'}" class="notification-item ${tb.daDoc ? '' : 'unread'}" onclick="danhDauDaDoc(${tb.maThongBao})">
                    <div class="notif-title">${tb.tieuDe}</div>
                    <div class="notif-content">${tb.noiDung}</div>
                    <div class="notif-time"><i class="far fa-clock me-1"></i>${tb.ngayTao}</div>
                </a>
            `).join('');
        } else {
            listEl.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-bell-slash fa-2x mb-2"></i><br>Không có thông báo</div>';
        }
    } catch(e) { console.error(e); }
}

async function danhDauDaDoc(id) {
    await fetch('<?= BASE_URL ?>/thong-bao/danhDauDaDoc', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'id=' + id });
}

async function danhDauTatCaDaDoc() {
    await fetch('<?= BASE_URL ?>/thong-bao/danhDauTatCaDaDoc', { method: 'POST' });
    loadThongBao();
}
</script>
