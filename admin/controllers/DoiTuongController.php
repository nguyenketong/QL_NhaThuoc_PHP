<?php
/**
 * DoiTuong Controller - Quản lý đối tượng sử dụng
 */
class DoiTuongController extends AdminController
{
    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM DOI_TUONG_SU_DUNG ORDER BY TenDoiTuong");
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('doi-tuong/index', [
            'title' => 'Quản lý đối tượng sử dụng',
            'danhSach' => $danhSach
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("INSERT INTO DOI_TUONG_SU_DUNG (TenDoiTuong, MoTa) VALUES (?, ?)");
            $stmt->execute([
                $_POST['TenDoiTuong'] ?? '',
                $_POST['MoTa'] ?? ''
            ]);
            $this->setFlash('success', 'Thêm đối tượng sử dụng thành công!');
            $this->redirect('?controller=doi-tuong');
            return;
        }
        $this->view('doi-tuong/create', ['title' => 'Thêm đối tượng sử dụng']);
    }

    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM DOI_TUONG_SU_DUNG WHERE MaDoiTuong = ?");
        $stmt->execute([$id]);
        $doiTuong = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doiTuong) {
            $this->redirect('?controller=doi-tuong');
            return;
        }

        if ($this->isPost()) {
            $stmt = $this->db->prepare("UPDATE DOI_TUONG_SU_DUNG SET TenDoiTuong = ?, MoTa = ? WHERE MaDoiTuong = ?");
            $stmt->execute([
                $_POST['TenDoiTuong'] ?? '',
                $_POST['MoTa'] ?? '',
                $id
            ]);
            $this->setFlash('success', 'Cập nhật thành công!');
            $this->redirect('?controller=doi-tuong');
            return;
        }

        $this->view('doi-tuong/edit', [
            'title' => 'Sửa đối tượng sử dụng',
            'doiTuong' => $doiTuong
        ]);
    }

    public function delete($id)
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("DELETE FROM DOI_TUONG_SU_DUNG WHERE MaDoiTuong = ?");
            $stmt->execute([$id]);
            $this->setFlash('success', 'Xóa thành công!');
        }
        $this->redirect('?controller=doi-tuong');
    }
}
