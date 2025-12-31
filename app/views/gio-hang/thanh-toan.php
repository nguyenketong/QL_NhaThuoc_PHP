<?php
/**
 * Thanh toán
 */
$tongTien = array_sum(array_map(function($item) {
    return $item['GiaBan'] * $item['SoLuong'];
}, $gioHang));
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/gioHang">Giỏ hàng</a></li>
            <li class="breadcrumb-item active">Thanh toán</li>
        </ol>
    </nav>

    <h3 class="mb-4"><i class="fas fa-credit-card"></i> Thanh toán</h3>

    <form action="<?= BASE_URL ?>/gioHang/datHang" method="POST">
        <div class="row">
            <div class="col-lg-8">
                <!-- Thông tin giao hàng -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-truck"></i> Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Họ tên người nhận</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($nguoiDung['HoTen'] ?? '') ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($nguoiDung['SoDienThoai'] ?? '') ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình thức nhận hàng</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="hinhThucNhanHang" value="Giao hàng" id="giaoHang" checked onchange="toggleDiaChi()">
                                <label class="form-check-label" for="giaoHang">Giao hàng tận nơi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="hinhThucNhanHang" value="Nhận tại nhà thuốc" id="nhanTaiNhaThuoc" onchange="toggleDiaChi()">
                                <label class="form-check-label" for="nhanTaiNhaThuoc">Nhận tại nhà thuốc</label>
                            </div>
                        </div>
                        <div class="mb-3" id="diaChiWrapper">
                            <label class="form-label">Địa chỉ giao hàng</label>
                            <textarea name="diaChiGiaoHang" class="form-control" rows="3" placeholder="Nhập địa chỉ giao hàng..."><?= htmlspecialchars($nguoiDung['DiaChi'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Phương thức thanh toán -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-wallet"></i> Phương thức thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="phuongThucThanhToan" value="Tiền mặt" id="tienMat" checked>
                            <label class="form-check-label" for="tienMat">
                                <i class="fas fa-money-bill-wave text-success"></i> Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="phuongThucThanhToan" value="Chuyển khoản" id="chuyenKhoan">
                            <label class="form-check-label" for="chuyenKhoan">
                                <i class="fas fa-university text-primary"></i> Chuyển khoản ngân hàng
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Danh sách sản phẩm -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Sản phẩm (<?= count($gioHang) ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($gioHang as $item): ?>
                            <div class="d-flex align-items-center p-3 border-bottom">
                                <img src="<?= !empty($item['HinhAnh']) ? $item['HinhAnh'] : BASE_URL . '/assets/images/no-image.svg' ?>" 
                                     alt="" style="width: 60px; height: 60px; object-fit: cover;" class="rounded me-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= htmlspecialchars($item['TenThuoc']) ?></h6>
                                    <small class="text-muted">x<?= $item['SoLuong'] ?></small>
                                </div>
                                <div class="text-danger fw-bold">
                                    <?= number_format($item['GiaBan'] * $item['SoLuong'], 0, ',', '.') ?>đ
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Tổng tiền -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Tổng đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span><?= number_format($tongTien, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span class="text-success">Miễn phí</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Tổng cộng:</strong>
                            <strong class="text-danger h4"><?= number_format($tongTien, 0, ',', '.') ?>đ</strong>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-check"></i> Đặt hàng
                        </button>
                        <a href="<?= BASE_URL ?>/gioHang" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function toggleDiaChi() {
    const wrapper = document.getElementById('diaChiWrapper');
    const nhanTaiNhaThuoc = document.getElementById('nhanTaiNhaThuoc').checked;
    wrapper.style.display = nhanTaiNhaThuoc ? 'none' : 'block';
}
</script>
