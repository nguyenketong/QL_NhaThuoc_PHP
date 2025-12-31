<?php
/**
 * Danh sách đơn hàng
 */
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/user/profile">Tài khoản</a></li>
            <li class="breadcrumb-item active">Đơn hàng</li>
        </ol>
    </nav>

    <h3 class="mb-4"><i class="fas fa-box"></i> Đơn hàng của tôi</h3>

    <?php if (empty($danhSach)): ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-5x text-muted mb-3"></i>
            <h4>Chưa có đơn hàng</h4>
            <p class="text-muted">Bạn chưa có đơn hàng nào</p>
            <a href="<?= BASE_URL ?>/thuoc/danhSach" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Mua sắm ngay
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhSach as $dh): ?>
                        <tr>
                            <td><strong>#<?= $dh['MaDonHang'] ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($dh['NgayDat'])) ?></td>
                            <td class="text-danger fw-bold"><?= number_format($dh['TongTien'], 0, ',', '.') ?>đ</td>
                            <td>
                                <?php
                                $badgeClass = match($dh['TrangThai']) {
                                    'Chờ xác nhận' => 'bg-warning text-dark',
                                    'Đã xác nhận' => 'bg-info',
                                    'Đang giao' => 'bg-primary',
                                    'Hoàn thành' => 'bg-success',
                                    'Đã hủy' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= $dh['TrangThai'] ?></span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/donHang/chiTiet/<?= $dh['MaDonHang'] ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> Chi tiết
                                </a>
                                <?php if ($dh['TrangThai'] === 'Chờ xác nhận'): ?>
                                    <a href="<?= BASE_URL ?>/donHang/huy/<?= $dh['MaDonHang'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                        <i class="fas fa-times"></i> Hủy
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
