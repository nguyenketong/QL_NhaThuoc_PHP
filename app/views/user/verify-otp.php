<?php
/**
 * Xác nhận OTP
 */
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-shield-alt"></i> Xác nhận OTP</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">
                        Mã OTP đã được gửi đến số <strong><?= htmlspecialchars($soDienThoai) ?></strong>
                    </p>

                    <?php if (!empty($devOtp)): ?>
                        <div class="alert alert-warning text-center">
                            <small><strong>Chế độ DEV:</strong> Mã OTP là <code><?= $devOtp ?></code></small>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= BASE_URL ?>/user/confirmOtp" method="POST">
                        <input type="hidden" name="soDienThoai" value="<?= htmlspecialchars($soDienThoai) ?>">
                        
                        <div class="mb-4">
                            <label class="form-label">Mã OTP</label>
                            <input type="text" name="otp" class="form-control form-control-lg text-center" 
                                   placeholder="000000" required maxlength="6" pattern="[0-9]{6}"
                                   style="letter-spacing: 10px; font-size: 24px;">
                            <small class="text-muted">Nhập mã 6 số đã gửi đến điện thoại</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-check"></i> Xác nhận
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-2">Không nhận được mã?</p>
                        <a href="<?= BASE_URL ?>/user/phoneLogin" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-redo"></i> Gửi lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
