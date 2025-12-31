<?php
/**
 * BaiVietModel - Model quản lý bài viết
 */
class BaiVietModel extends Model
{
    protected $table = 'BAI_VIET';

    public function getAll($limit = null)
    {
        $sql = "SELECT * FROM BAI_VIET WHERE IsActive = 1 ORDER BY NgayDang DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM BAI_VIET WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getNoiBat($limit = 5)
    {
        $sql = "SELECT * FROM BAI_VIET WHERE IsActive = 1 AND IsNoiBat = 1 ORDER BY NgayDang DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
