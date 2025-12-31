<?php
class DonHangModel extends Model
{
    protected $table = 'DON_HANG';

    public function getByUser($maNguoiDung)
    {
        $stmt = $this->db->prepare("SELECT * FROM DON_HANG WHERE MaNguoiDung = ? ORDER BY NgayDatHang DESC");
        $stmt->execute([$maNguoiDung]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT dh.*, nd.HoTen, nd.SoDienThoai 
                FROM DON_HANG dh 
                LEFT JOIN NGUOI_DUNG nd ON dh.MaNguoiDung = nd.MaNguoiDung
                WHERE dh.MaDonHang = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getChiTiet($maDonHang)
    {
        $stmt = $this->db->prepare("SELECT ct.*, t.TenThuoc, t.HinhAnh 
                FROM CHI_TIET_DON_HANG ct
                JOIN THUOC t ON ct.MaThuoc = t.MaThuoc
                WHERE ct.MaDonHang = ?");
        $stmt->execute([$maDonHang]);
        return $stmt->fetchAll();
    }

    public function create($data, $chiTiet)
    {
        $this->db->beginTransaction();
        try {
            // Tạo đơn hàng
            $maDonHang = $this->insert($data);
            
            // Thêm chi tiết
            $stmt = $this->db->prepare("INSERT INTO CHI_TIET_DON_HANG (MaDonHang, MaThuoc, SoLuong, DonGia, ThanhTien) VALUES (?, ?, ?, ?, ?)");
            foreach ($chiTiet as $item) {
                $stmt->execute([$maDonHang, $item['maThuoc'], $item['soLuong'], $item['donGia'], $item['thanhTien']]);
                
                // Giảm số lượng tồn
                $this->db->prepare("UPDATE THUOC SET SoLuongTon = SoLuongTon - ? WHERE MaThuoc = ?")
                         ->execute([$item['soLuong'], $item['maThuoc']]);
            }
            
            $this->db->commit();
            return $maDonHang;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateTrangThai($maDonHang, $trangThai)
    {
        $stmt = $this->db->prepare("UPDATE DON_HANG SET TrangThai = ? WHERE MaDonHang = ?");
        return $stmt->execute([$trangThai, $maDonHang]);
    }

    public function huyDon($maDonHang, $maNguoiDung)
    {
        // Kiểm tra đơn hàng thuộc về user và đang ở trạng thái có thể hủy
        $stmt = $this->db->prepare("SELECT * FROM DON_HANG WHERE MaDonHang = ? AND MaNguoiDung = ? AND TrangThai = 'Chờ xác nhận'");
        $stmt->execute([$maDonHang, $maNguoiDung]);
        $don = $stmt->fetch();
        
        if (!$don) return false;
        
        // Hoàn lại số lượng tồn
        $chiTiet = $this->getChiTiet($maDonHang);
        foreach ($chiTiet as $item) {
            $this->db->prepare("UPDATE THUOC SET SoLuongTon = SoLuongTon + ? WHERE MaThuoc = ?")
                     ->execute([$item['SoLuong'], $item['MaThuoc']]);
        }
        
        return $this->updateTrangThai($maDonHang, 'Đã hủy');
    }
}
