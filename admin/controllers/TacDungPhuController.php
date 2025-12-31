<?php
/**
 * TacDungPhu Controller - Quản lý tác dụng phụ
 */
class TacDungPhuController extends AdminController
{
    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM TAC_DUNG_PHU ORDER BY TenTacDungPhu");
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('tac-dung-phu/index', [
            'title' => 'Quản lý tác dụng phụ',
            'danhSach' => $danhSach
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("INSERT INTO TAC_DUNG_PHU (TenTacDungPhu, MoTa) VALUES (?, ?)");
            $stmt->execute([
                $_POST['TenTacDungPhu'] ?? '',
                $_POST['MoTa'] ?? ''
            ]);
            $this->setFlash('success', 'Thêm tác dụng phụ thành công!');
            $this->redirect('?controller=tac-dung-phu');
            return;
        }
        $this->view('tac-dung-phu/create', ['title' => 'Thêm tác dụng phụ']);
    }

    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM TAC_DUNG_PHU WHERE MaTacDungPhu = ?");
        $stmt->execute([$id]);
        $tacDungPhu = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tacDungPhu) {
            $this->redirect('?controller=tac-dung-phu');
            return;
        }

        if ($this->isPost()) {
            $stmt = $this->db->prepare("UPDATE TAC_DUNG_PHU SET TenTacDungPhu = ?, MoTa = ? WHERE MaTacDungPhu = ?");
            $stmt->execute([
                $_POST['TenTacDungPhu'] ?? '',
                $_POST['MoTa'] ?? '',
                $id
            ]);
            $this->setFlash('success', 'Cập nhật thành công!');
            $this->redirect('?controller=tac-dung-phu');
            return;
        }

        $this->view('tac-dung-phu/edit', [
            'title' => 'Sửa tác dụng phụ',
            'tacDungPhu' => $tacDungPhu
        ]);
    }

    public function delete($id)
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("DELETE FROM TAC_DUNG_PHU WHERE MaTacDungPhu = ?");
            $stmt->execute([$id]);
            $this->setFlash('success', 'Xóa thành công!');
        }
        $this->redirect('?controller=tac-dung-phu');
    }
}
