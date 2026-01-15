<?php
/**
 * NhomThuoc Controller - Quản lý nhóm thuốc
 */
class NhomThuocController extends AdminController
{
    public function index()
    {
        // Lấy tất cả nhóm thuốc
        $stmt = $this->db->query("
            SELECT nt.*, ntc.TenNhomThuoc as TenDanhMucCha,
                   (SELECT COUNT(*) FROM THUOC WHERE MaNhomThuoc = nt.MaNhomThuoc) as SoLuongThuoc
            FROM NHOM_THUOC nt
            LEFT JOIN NHOM_THUOC ntc ON nt.MaDanhMucCha = ntc.MaNhomThuoc
            ORDER BY nt.TenNhomThuoc
        ");
        $allItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sắp xếp: danh mục cha trước, sau đó là các danh mục con của nó
        $danhSach = [];
        $danhMucCha = array_filter($allItems, fn($item) => empty($item['MaDanhMucCha']));
        
        foreach ($danhMucCha as $cha) {
            $cha['isParent'] = true;
            $danhSach[] = $cha;
            // Tìm các danh mục con
            foreach ($allItems as $con) {
                if ($con['MaDanhMucCha'] == $cha['MaNhomThuoc']) {
                    $con['isParent'] = false;
                    $danhSach[] = $con;
                }
            }
        }

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

    public function delete($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? 0;
        
        if ($this->isPost() && $id) {
            // Kiểm tra có thuốc nào thuộc nhóm này không
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM THUOC WHERE MaNhomThuoc = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $this->setFlash('error', "Không thể xóa! Nhóm thuốc này có $count sản phẩm.");
            } else {
                // Kiểm tra có danh mục con không
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM NHOM_THUOC WHERE MaDanhMucCha = ?");
                $stmt->execute([$id]);
                $countCon = $stmt->fetchColumn();
                
                if ($countCon > 0) {
                    $this->setFlash('error', "Không thể xóa! Nhóm thuốc này có $countCon danh mục con.");
                } else {
                    $stmt = $this->db->prepare("DELETE FROM NHOM_THUOC WHERE MaNhomThuoc = ?");
                    $stmt->execute([$id]);
                    $this->setFlash('success', 'Xóa nhóm thuốc thành công!');
                }
            }
        }
        $this->redirect('?controller=nhom-thuoc');
    }
}
