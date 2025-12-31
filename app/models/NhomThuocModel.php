<?php
class NhomThuocModel extends Model
{
    protected $table = 'NHOM_THUOC';

    public function getAll()
    {
        return $this->db->query("SELECT * FROM NHOM_THUOC ORDER BY TenNhomThuoc")->fetchAll();
    }

    public function getDanhMucCha()
    {
        return $this->db->query("SELECT * FROM NHOM_THUOC WHERE MaDanhMucCha IS NULL ORDER BY TenNhomThuoc")->fetchAll();
    }

    public function getDanhMucCon($maCha)
    {
        $stmt = $this->db->prepare("SELECT * FROM NHOM_THUOC WHERE MaDanhMucCha = ? ORDER BY TenNhomThuoc");
        $stmt->execute([$maCha]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        return $this->find($id, 'MaNhomThuoc');
    }
}
