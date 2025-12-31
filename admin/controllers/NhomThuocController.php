<?php
/**
 * NhomThuoc Controller - Quản lý nhóm thuốc
 */
class NhomThuocController extends AdminController
{
    public function index()
    {
        $stmt = $this->db->query("
            SELECT nt.*, ntc.TenNhomThuoc as TenDanhMucCha,
                   (SELECT COUNT(*) FROM THUOC WHERE MaNhomThuoc = nt.MaNhomThuoc) as SoLuongThuoc
            FROM NHOM_THUOC nt
            LEFT JOIN NHOM_THUOC ntc ON nt.MaDanhMucCha = ntc.MaNhomThuoc
            ORDER BY nt.MaDanhMucCha IS NULL DESC, nt.TenNhomThuoc
        ");
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('nhom-thuoc/index', [
            'title' => 'Quản lý nhóm thuốc',
            'danhSach' => $danhSach
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("INSERT INTO NHOM_THUOC (TenNhomThuoc, MoTa, MaDanhMucCha) VALUES (?, ?, ?)");
            $stmt->execute([
                $_POST['TenNhomThuoc'] ?? '',
                $_POST['MoTa'] ?? '',
                $_POST['MaDanhMucCha'] ?: null
            ]);
            $this->setFlash('success', 'Thêm nhóm thuốc thành công!');
            $this->redirect('?controller=nhom-thuoc');
            return;
        }

        // Load danh mục cha
        $stmt = $this->db->query("SELECT * FROM NHOM_THUOC WHERE MaDanhMucCha IS NULL ORDER BY TenNhomThuoc");
        $danhMucChaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('nhom-thuoc/create', [
            'title' => 'Thêm nhóm thuốc',
            'danhMucChaList' => $danhMucChaList
        ]);
    }

    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM NHOM_THUOC WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        $nhomThuoc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nhomThuoc) {
            $this->redirect('?controller=nhom-thuoc');
            return;
        }

        if ($this->isPost()) {
            $stmt = $this->db->prepare("UPDATE NHOM_THUOC SET TenNhomThuoc = ?, MoTa = ?, MaDanhMucCha = ? WHERE MaNhomThuoc = ?");
            $stmt->execute([
                $_POST['TenNhomThuoc'] ?? '',
                $_POST['MoTa'] ?? '',
                $_POST['MaDanhMucCha'] ?: null,
                $id
            ]);
            $this->setFlash('success', 'Cập nhật nhóm thuốc thành công!');
            $this->redirect('?controller=nhom-thuoc');
            return;
        }

        // Load danh mục cha (không bao gồm chính nó)
        $stmt = $this->db->prepare("SELECT * FROM NHOM_THUOC WHERE MaDanhMucCha IS NULL AND MaNhomThuoc != ? ORDER BY TenNhomThuoc");
        $stmt->execute([$id]);
        $danhMucChaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('nhom-thuoc/edit', [
            'title' => 'Sửa nhóm thuốc',
            'nhomThuoc' => $nhomThuoc,
            'danhMucChaList' => $danhMucChaList
        ]);
    }

    public function delete($id)
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("DELETE FROM NHOM_THUOC WHERE MaNhomThuoc = ?");
            $stmt->execute([$id]);
            $this->setFlash('success', 'Xóa nhóm thuốc thành công!');
        }
        $this->redirect('?controller=nhom-thuoc');
    }
}
