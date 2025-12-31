<?php
/**
 * NguoiDung Controller - Quản lý người dùng
 */
class NguoiDungController extends AdminController
{
    public function index()
    {
        $stmt = $this->db->query("
            SELECT nd.*,
                   (SELECT COUNT(*) FROM DON_HANG WHERE MaNguoiDung = nd.MaNguoiDung) as SoDonHang,
                   (SELECT COALESCE(SUM(TongTien), 0) FROM DON_HANG WHERE MaNguoiDung = nd.MaNguoiDung AND TrangThai = 'Hoan thanh') as TongChiTieu
            FROM NGUOI_DUNG nd
            ORDER BY nd.NgayTao DESC
        ");
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('nguoi-dung/index', [
            'title' => 'Quản lý khách hàng',
            'danhSach' => $danhSach
        ]);
    }

    public function details($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM NGUOI_DUNG WHERE MaNguoiDung = ?");
        $stmt->execute([$id]);
        $nguoiDung = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nguoiDung) {
            $this->redirect('?controller=nguoi-dung');
            return;
        }

        // Lấy danh sách đơn hàng
        $stmt = $this->db->prepare("SELECT * FROM DON_HANG WHERE MaNguoiDung = ? ORDER BY NgayDatHang DESC");
        $stmt->execute([$id]);
        $donHangs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('nguoi-dung/details', [
            'title' => 'Chi tiết khách hàng',
            'nguoiDung' => $nguoiDung,
            'donHangs' => $donHangs
        ]);
    }

    public function capNhatVaiTro()
    {
        if ($this->isPost()) {
            $id = $_POST['id'] ?? 0;
            $vaiTro = $_POST['vaiTro'] ?? 'User';

            $stmt = $this->db->prepare("UPDATE NGUOI_DUNG SET VaiTro = ? WHERE MaNguoiDung = ?");
            $stmt->execute([$vaiTro, $id]);
            $this->setFlash('success', 'Cập nhật vai trò thành công!');
            $this->redirect("?controller=nguoi-dung&action=details&id=$id");
            return;
        }
        $this->redirect('?controller=nguoi-dung');
    }

    public function delete($id)
    {
        if ($this->isPost()) {
            // Kiểm tra không cho xóa Admin
            $stmt = $this->db->prepare("SELECT VaiTro FROM NGUOI_DUNG WHERE MaNguoiDung = ?");
            $stmt->execute([$id]);
            $nguoiDung = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($nguoiDung && $nguoiDung['VaiTro'] == 'Admin') {
                $this->setFlash('error', 'Không thể xóa tài khoản Admin!');
            } else {
                $stmt = $this->db->prepare("DELETE FROM NGUOI_DUNG WHERE MaNguoiDung = ?");
                $stmt->execute([$id]);
                $this->setFlash('success', 'Xóa người dùng thành công!');
            }
        }
        $this->redirect('?controller=nguoi-dung');
    }
}
