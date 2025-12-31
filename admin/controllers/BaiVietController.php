<?php
/**
 * BaiViet Controller - Quản lý bài viết
 */
class BaiVietController extends AdminController
{
    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM BAIVIET ORDER BY NgayDang DESC");
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('bai-viet/index', [
            'title' => 'Quản lý bài viết',
            'danhSach' => $danhSach
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $hinhAnh = null;
            if (isset($_FILES['hinhAnhFile']) && $_FILES['hinhAnhFile']['size'] > 0) {
                $hinhAnh = $this->uploadImage($_FILES['hinhAnhFile'], 'images/baiviet');
            }

            $stmt = $this->db->prepare("
                INSERT INTO BAI_VIET (TieuDe, MoTaNgan, NoiDung, HinhAnh, NgayDang, LuotXem, IsNoiBat, IsActive)
                VALUES (?, ?, ?, ?, NOW(), 0, ?, 1)
            ");
            $stmt->execute([
                $_POST['TieuDe'] ?? '',
                $_POST['MoTaNgan'] ?? '',
                $_POST['NoiDung'] ?? '',
                $hinhAnh,
                isset($_POST['IsNoiBat']) ? 1 : 0
            ]);
            $this->setFlash('success', 'Thêm bài viết thành công!');
            $this->redirect('?controller=bai-viet');
            return;
        }

        $this->view('bai-viet/create', ['title' => 'Thêm bài viết']);
    }

    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM BAI_VIET WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        $baiViet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$baiViet) {
            $this->redirect('?controller=bai-viet');
            return;
        }

        if ($this->isPost()) {
            $hinhAnh = $baiViet['HinhAnh'];
            if (isset($_FILES['hinhAnhFile']) && $_FILES['hinhAnhFile']['size'] > 0) {
                $hinhAnh = $this->uploadImage($_FILES['hinhAnhFile'], 'images/baiviet');
            }

            $stmt = $this->db->prepare("
                UPDATE BAI_VIET SET TieuDe = ?, MoTaNgan = ?, NoiDung = ?, HinhAnh = ?, IsNoiBat = ?
                WHERE MaBaiViet = ?
            ");
            $stmt->execute([
                $_POST['TieuDe'] ?? '',
                $_POST['MoTaNgan'] ?? '',
                $_POST['NoiDung'] ?? '',
                $hinhAnh,
                isset($_POST['IsNoiBat']) ? 1 : 0,
                $id
            ]);
            $this->setFlash('success', 'Cập nhật bài viết thành công!');
            $this->redirect('?controller=bai-viet');
            return;
        }

        $this->view('bai-viet/edit', [
            'title' => 'Sửa bài viết',
            'baiViet' => $baiViet
        ]);
    }

    public function delete($id)
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("DELETE FROM BAI_VIET WHERE MaBaiViet = ?");
            $stmt->execute([$id]);
            $this->setFlash('success', 'Xóa bài viết thành công!');
        }
        $this->redirect('?controller=bai-viet');
    }

    public function toggleNoiBat($id)
    {
        $stmt = $this->db->prepare("UPDATE BAI_VIET SET IsNoiBat = NOT COALESCE(IsNoiBat, 0) WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        $this->redirect('?controller=bai-viet');
    }

    public function toggleActive($id)
    {
        $stmt = $this->db->prepare("UPDATE BAI_VIET SET IsActive = NOT COALESCE(IsActive, 0) WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        $this->redirect('?controller=bai-viet');
    }
}
