<?php
/**
 * ThanhPhan Controller - Quản lý thành phần
 */
class ThanhPhanController extends AdminController
{
    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM THANH_PHAN ORDER BY TenThanhPhan");
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('thanh-phan/index', [
            'title' => 'Quản lý thành phần',
            'danhSach' => $danhSach
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("INSERT INTO THANH_PHAN (TenThanhPhan, MoTa) VALUES (?, ?)");
            $stmt->execute([
                $_POST['TenThanhPhan'] ?? '',
                $_POST['MoTa'] ?? ''
            ]);
            $this->setFlash('success', 'Thêm thành phần thành công!');
            $this->redirect('?controller=thanh-phan');
            return;
        }
        $this->view('thanh-phan/create', ['title' => 'Thêm thành phần']);
    }

    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM THANH_PHAN WHERE MaThanhPhan = ?");
        $stmt->execute([$id]);
        $thanhPhan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$thanhPhan) {
            $this->redirect('?controller=thanh-phan');
            return;
        }

        if ($this->isPost()) {
            $stmt = $this->db->prepare("UPDATE THANH_PHAN SET TenThanhPhan = ?, MoTa = ? WHERE MaThanhPhan = ?");
            $stmt->execute([
                $_POST['TenThanhPhan'] ?? '',
                $_POST['MoTa'] ?? '',
                $id
            ]);
            $this->setFlash('success', 'Cập nhật thành công!');
            $this->redirect('?controller=thanh-phan');
            return;
        }

        $this->view('thanh-phan/edit', [
            'title' => 'Sửa thành phần',
            'thanhPhan' => $thanhPhan
        ]);
    }

    public function delete($id)
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("DELETE FROM THANH_PHAN WHERE MaThanhPhan = ?");
            $stmt->execute([$id]);
            $this->setFlash('success', 'Xóa thành công!');
        }
        $this->redirect('?controller=thanh-phan');
    }
}
