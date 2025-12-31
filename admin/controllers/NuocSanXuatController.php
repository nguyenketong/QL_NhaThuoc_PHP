<?php
/**
 * NuocSanXuat Controller - Quản lý nước sản xuất
 */
class NuocSanXuatController extends AdminController
{
    public function index()
    {
        $stmt = $this->db->query("
            SELECT nsx.*, (SELECT COUNT(*) FROM THUOC WHERE MaNuocSX = nsx.MaNuocSX) as SoLuongThuoc
            FROM NUOC_SAN_XUAT nsx
            ORDER BY nsx.TenNuocSX
        ");
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('nuoc-san-xuat/index', [
            'title' => 'Quản lý nước sản xuất',
            'danhSach' => $danhSach
        ]);
    }

    public function create()
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("INSERT INTO NUOC_SAN_XUAT (TenNuocSX) VALUES (?)");
            $stmt->execute([$_POST['TenNuocSX'] ?? '']);
            $this->setFlash('success', 'Thêm nước sản xuất thành công!');
            $this->redirect('?controller=nuoc-san-xuat');
            return;
        }
        $this->view('nuoc-san-xuat/create', ['title' => 'Thêm nước sản xuất']);
    }

    public function edit($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM NUOC_SAN_XUAT WHERE MaNuocSX = ?");
        $stmt->execute([$id]);
        $nuocSX = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nuocSX) {
            $this->redirect('?controller=nuoc-san-xuat');
            return;
        }

        if ($this->isPost()) {
            $stmt = $this->db->prepare("UPDATE NUOC_SAN_XUAT SET TenNuocSX = ? WHERE MaNuocSX = ?");
            $stmt->execute([$_POST['TenNuocSX'] ?? '', $id]);
            $this->setFlash('success', 'Cập nhật thành công!');
            $this->redirect('?controller=nuoc-san-xuat');
            return;
        }

        $this->view('nuoc-san-xuat/edit', [
            'title' => 'Sửa nước sản xuất',
            'nuocSX' => $nuocSX
        ]);
    }

    public function delete($id)
    {
        if ($this->isPost()) {
            $stmt = $this->db->prepare("DELETE FROM NUOC_SAN_XUAT WHERE MaNuocSX = ?");
            $stmt->execute([$id]);
            $this->setFlash('success', 'Xóa thành công!');
        }
        $this->redirect('?controller=nuoc-san-xuat');
    }
}
