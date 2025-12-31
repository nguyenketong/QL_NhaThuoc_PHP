<?php
/**
 * ThuongHieu Controller - Quản lý thương hiệu
 */
class ThuongHieuController extends AdminController
{
    public function index()
    {
        $stmt = $this->db->query("
            SELECT th.*, (SELECT COUNT(*) FROM THUOC WHERE MaThuongHieu = th.MaThuongHieu) as SoLuongThuoc
            FROM THUONG_HIEU th
            ORDER BY th.TenThuongHieu
        ");
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('thuong-hieu/index', [
            'title' => 'Quản lý thương hiệu',
            'danhSach' => $danhSach
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $hinhAnh = null;
            if (isset($_FILES['LogoFile']) && $_FILES['LogoFile']['size'] > 0) {
                $hinhAnh = $this->uploadImage($_FILES['LogoFile'], 'images/brands');
            }

            $stmt = $this->db->prepare("INSERT INTO THUONG_HIEU (TenThuongHieu, QuocGia, DiaChi, HinhAnh) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['TenThuongHieu'] ?? '',
                $_POST['QuocGia'] ?? '',
                $_POST['DiaChi'] ?? '',
                $hinhAnh
            ]);
            $this->setFlash('success', 'Thêm thương hiệu thành công!');
            $this->redirect('?controller=thuong-hieu');
            return;
        }

        $this->view('thuong-hieu/create', ['title' => 'Thêm thương hiệu']);
    }

    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM THUONG_HIEU WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        $thuongHieu = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$thuongHieu) {
            $this->redirect('?controller=thuong-hieu');
            return;
        }

        if ($this->isPost()) {
            $hinhAnh = $thuongHieu['HinhAnh'];
            if (isset($_FILES['LogoFile']) && $_FILES['LogoFile']['size'] > 0) {
                $hinhAnh = $this->uploadImage($_FILES['LogoFile'], 'images/brands');
            }

            $stmt = $this->db->prepare("UPDATE THUONG_HIEU SET TenThuongHieu = ?, QuocGia = ?, DiaChi = ?, HinhAnh = ? WHERE MaThuongHieu = ?");
            $stmt->execute([
                $_POST['TenThuongHieu'] ?? '',
                $_POST['QuocGia'] ?? '',
                $_POST['DiaChi'] ?? '',
                $hinhAnh,
                $id
            ]);
            $this->setFlash('success', 'Cập nhật thương hiệu thành công!');
            $this->redirect('?controller=thuong-hieu');
            return;
        }

        $this->view('thuong-hieu/edit', [
            'title' => 'Sửa thương hiệu',
            'thuongHieu' => $thuongHieu
        ]);
    }

    public function delete($id)
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("DELETE FROM THUONG_HIEU WHERE MaThuongHieu = ?");
            $stmt->execute([$id]);
            $this->setFlash('success', 'Xóa thương hiệu thành công!');
        }
        $this->redirect('?controller=thuong-hieu');
    }
}
