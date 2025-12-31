<?php
/**
 * ThuongHieuModel - Model quản lý thương hiệu
 */
class ThuongHieuModel extends Model
{
    protected $table = 'THUONG_HIEU';

    public function getAll()
    {
        return $this->db->query("SELECT * FROM THUONG_HIEU ORDER BY TenThuongHieu")->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM THUONG_HIEU WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
