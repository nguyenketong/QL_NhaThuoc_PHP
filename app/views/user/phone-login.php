<?php
/**
 * Đăng nhập bằng số điện thoại
 */
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-sign-in-alt"></i> Đăng nhập</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">Nhập số điện thoại để nhận mã OTP</p>
                    
                    <form action="<?= BASE_URL ?>/user/sendOtp" method="POST">
                        <div class="mb-4">
                            <label class="form-label">Số điện thoại</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" name="soDienThoai" class="form-control form-control-lg" 
                                       placeholder="0912345678" required pattern="[0-9]{10,11}">
                            </div>
                            <small class="text-muted">Nhập số điện thoại 10-11 số</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-paper-plane"></i> Gửi mã OTP
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted mb-2">Hoặc liên hệ hotline</p>
                        <a href="tel:<?= STORE_PHONE ?>" class="btn btn-outline-primary">
                            <i class="fas fa-phone"></i> <?= STORE_PHONE ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
