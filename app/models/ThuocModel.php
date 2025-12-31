<?php
class ThuocModel extends Model
{
    protected $table = 'THUOC';

    public function getAll($filters = [])
    {
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu, nsx.TenNuocSX 
                FROM THUOC t
                LEFT JOIN NHOM_THUOC nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN THUONG_HIEU th ON t.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN NUOC_SAN_XUAT nsx ON t.MaNuocSX = nsx.MaNuocSX
                WHERE 1=1";
        $params = [];

        if (!empty($filters['maNhom'])) {
            $sql .= " AND t.MaNhomThuoc = ?";
            $params[] = $filters['maNhom'];
        }
        if (!empty($filters['maThuongHieu'])) {
            $sql .= " AND t.MaThuongHieu = ?";
            $params[] = $filters['maThuongHieu'];
        }
        if (!empty($filters['tuKhoa'])) {
            $sql .= " AND (t.TenThuoc LIKE ? OR t.MoTa LIKE ?)";
            $params[] = "%{$filters['tuKhoa']}%";
            $params[] = "%{$filters['tuKhoa']}%";
        }
        if (isset($filters['isActive'])) {
            $sql .= " AND t.IsActive = ?";
            $params[] = $filters['isActive'];
        }

        $sql .= " ORDER BY t.MaThuoc DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu, nsx.TenNuocSX 
                FROM THUOC t
                LEFT JOIN NHOM_THUOC nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN THUONG_HIEU th ON t.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN NUOC_SAN_XUAT nsx ON t.MaNuocSX = nsx.MaNuocSX
                WHERE t.MaThuoc = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getSanPhamMoi($limit = 10)
    {
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu 
                FROM THUOC t
                LEFT JOIN NHOM_THUOC nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN THUONG_HIEU th ON t.MaThuongHieu = th.MaThuongHieu
                WHERE t.IsNew = 1 AND t.IsActive = 1
                ORDER BY t.NgayTao DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getSanPhamKhuyenMai($limit = 10)
    {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu 
                FROM THUOC t
                LEFT JOIN NHOM_THUOC nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN THUONG_HIEU th ON t.MaThuongHieu = th.MaThuongHieu
                WHERE t.IsActive = 1 AND t.PhanTramGiam > 0
                AND (t.NgayBatDauKM IS NULL OR t.NgayBatDauKM <= ?)
                AND (t.NgayKetThucKM IS NULL OR t.NgayKetThucKM >= ?)
                ORDER BY t.PhanTramGiam DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$now, $now, $limit]);
        return $stmt->fetchAll();
    }

    public function getSanPhamBanChay($limit = 10)
    {
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu 
                FROM THUOC t
                LEFT JOIN NHOM_THUOC nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN THUONG_HIEU th ON t.MaThuongHieu = th.MaThuongHieu
                WHERE t.IsActive = 1 AND t.SoLuongDaBan > 0
                ORDER BY t.SoLuongDaBan DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getThanhPhan($maThuoc)
    {
        $sql = "SELECT ct.*, tp.TenThanhPhan, tp.MoTa 
                FROM CT_THANH_PHAN ct
                JOIN THANH_PHAN tp ON ct.MaThanhPhan = tp.MaThanhPhan
                WHERE ct.MaThuoc = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maThuoc]);
        return $stmt->fetchAll();
    }

    public function getTacDungPhu($maThuoc)
    {
        $sql = "SELECT ct.*, tdp.TenTacDungPhu, tdp.MoTa 
                FROM CT_TAC_DUNG_PHU ct
                JOIN TAC_DUNG_PHU tdp ON ct.MaTacDungPhu = tdp.MaTacDungPhu
                WHERE ct.MaThuoc = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maThuoc]);
        return $stmt->fetchAll();
    }

    public function getDoiTuong($maThuoc)
    {
        $sql = "SELECT ct.*, dt.TenDoiTuong, dt.MoTa 
                FROM CT_DOI_TUONG ct
                JOIN DOI_TUONG_SU_DUNG dt ON ct.MaDoiTuong = dt.MaDoiTuong
                WHERE ct.MaThuoc = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maThuoc]);
        return $stmt->fetchAll();
    }
}
