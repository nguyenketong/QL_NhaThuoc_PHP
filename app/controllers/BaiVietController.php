<?php
/**
 * BaiVietController - Quản lý bài viết
 */
class BaiVietController extends Controller
{
    private $pageSize = 10;

    // GET: baiViet/gocSucKhoe (alias cho danhSach)
    public function gocSucKhoe()
    {
        $this->danhSach();
    }

    // GET: baiViet/danhSach
    public function danhSach()
    {
        $page = (int)($_GET['page'] ?? 1);

        // Đếm tổng số bài viết
        $stmt = $this->db->query("SELECT COUNT(*) FROM BAI_VIET WHERE IsActive = 1");
        $totalItems = $stmt->fetchColumn();
        $totalPages = ceil($totalItems / $this->pageSize);

        // Lấy bài viết theo trang
        $offset = ($page - 1) * $this->pageSize;
        $stmt = $this->db->prepare("SELECT * FROM BAI_VIET WHERE IsActive = 1 ORDER BY NgayDang DESC LIMIT ? OFFSET ?");
        $stmt->execute([$this->pageSize, $offset]);
        $baiViets = $stmt->fetchAll();

        $data = [
            'title' => 'Góc sức khỏe - ' . STORE_NAME,
            'baiViets' => $baiViets,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems
        ];

        $this->view('bai-viet/danh-sach', $data);
    }

    // GET: baiViet/chiTiet/{id}
    public function chiTiet($id = null)
    {
        if (!$id) {
            $this->redirect('baiViet/danhSach');
        }

        $stmt = $this->db->prepare("SELECT * FROM BAI_VIET WHERE MaBaiViet = ? AND IsActive = 1");
        $stmt->execute([$id]);
        $baiViet = $stmt->fetch();

        if (!$baiViet) {
            $this->setFlash('error', 'Không tìm thấy bài viết!');
            $this->redirect('baiViet/danhSach');
        }

        // Tăng lượt xem
        $stmt = $this->db->prepare("UPDATE BAI_VIET SET LuotXem = COALESCE(LuotXem, 0) + 1 WHERE MaBaiViet = ?");
        $stmt->execute([$id]);

        // Bài viết liên quan
        $stmt = $this->db->prepare("SELECT * FROM BAI_VIET WHERE MaBaiViet != ? AND IsActive = 1 ORDER BY NgayDang DESC LIMIT 4");
        $stmt->execute([$id]);
        $baiVietLienQuan = $stmt->fetchAll();

        $data = [
            'title' => $baiViet['TieuDe'] . ' - ' . STORE_NAME,
            'baiViet' => $baiViet,
            'baiVietLienQuan' => $baiVietLienQuan
        ];

        $this->view('bai-viet/chi-tiet', $data);
    }
}
