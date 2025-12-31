<?php
/**
 * ThongBaoController - Quản lý thông báo
 */
class ThongBaoController extends Controller
{
    // GET: thongBao/laySoLuongChuaDoc (AJAX)
    public function laySoLuongChuaDoc()
    {
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            $this->json(['soLuong' => 0]);
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM THONG_BAO WHERE MaNguoiDung = ? AND DaDoc = 0");
        $stmt->execute([$maNguoiDung]);
        $soLuong = $stmt->fetchColumn();

        $this->json(['soLuong' => $soLuong]);
    }

    // GET: thongBao/layDanhSach (AJAX)
    public function layDanhSach()
    {
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            $this->json(['thongBaos' => []]);
        }

        $stmt = $this->db->prepare("SELECT MaThongBao, TieuDe, NoiDung, LoaiThongBao, DaDoc, DuongDan, 
                                    DATE_FORMAT(NgayTao, '%d/%m/%Y %H:%i') as NgayTao 
                                    FROM THONG_BAO 
                                    WHERE MaNguoiDung = ? 
                                    ORDER BY NgayTao DESC LIMIT 10");
        $stmt->execute([$maNguoiDung]);
        $thongBaos = $stmt->fetchAll();

        $this->json(['thongBaos' => $thongBaos]);
    }

    // POST: thongBao/danhDauDaDoc (AJAX)
    public function danhDauDaDoc()
    {
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            $this->json(['success' => false]);
        }

        $id = (int)($_POST['id'] ?? 0);
        
        $stmt = $this->db->prepare("UPDATE THONG_BAO SET DaDoc = 1 WHERE MaThongBao = ? AND MaNguoiDung = ?");
        $stmt->execute([$id, $maNguoiDung]);

        $this->json(['success' => true]);
    }

    // POST: thongBao/danhDauTatCaDaDoc (AJAX)
    public function danhDauTatCaDaDoc()
    {
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            $this->json(['success' => false]);
        }

        $stmt = $this->db->prepare("UPDATE THONG_BAO SET DaDoc = 1 WHERE MaNguoiDung = ? AND DaDoc = 0");
        $stmt->execute([$maNguoiDung]);

        $this->json(['success' => true]);
    }
}
