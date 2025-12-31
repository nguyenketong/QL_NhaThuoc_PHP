<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-edit"></i> Sửa bài viết
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/admin/?controller=bai-viet&action=edit&id=<?= $baiViet['MaBaiViet'] ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="TieuDe" class="form-control" required value="<?= htmlspecialchars($baiViet['TieuDe'] ?? '') ?>" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả ngắn</label>
                        <textarea name="MoTaNgan" class="form-control" rows="2"><?= htmlspecialchars($baiViet['MoTaNgan'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung</label>
                        <textarea name="NoiDung" class="form-control" rows="10"><?= htmlspecialchars($baiViet['NoiDung'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hình ảnh</label>
                        <?php if ($baiViet['HinhAnh']): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL . $baiViet['HinhAnh'] ?>" alt="" style="max-width: 200px; border-radius: 8px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="hinhAnhFile" class="form-control" accept="image/*" />
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="IsNoiBat" value="1" class="form-check-input" id="chkNoiBat" <?= ($baiViet['IsNoiBat'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="chkNoiBat">Bài viết nổi bật</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                    <a href="<?= BASE_URL ?>/admin/?controller=bai-viet" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
