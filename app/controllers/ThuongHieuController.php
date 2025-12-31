<?php
/**
 * ThuongHieuController - Quản lý thương hiệu
 */
class ThuongHieuController extends Controller
{
    // GET: thuongHieu/danhSach
    public function danhSach()
    {
        $stmt = $this->db->query("SELECT * FROM THUONG_HIEU ORDER BY TenThuongHieu");
        
        $data = [
            'title' => 'Thương hiệu đối tác - ' . STORE_NAME,
            'danhSach' => $stmt->fetchAll()
        ];

        $this->view('thuong-hieu/danh-sach', $data);
    }

    // GET: thuongHieu/chiTiet/{id}
    public function chiTiet($id = null, $page = 1, $sapXep = null)
    {
        if (!$id) {
            $this->redirect('thuongHieu/danhSach');
        }

        $page = (int)($_GET['page'] ?? 1);
        $sapXep = $_GET['sapXep'] ?? null;

        // Lấy thông tin thương hiệu
        $stmt = $this->db->prepare("SELECT * FROM THUONG_HIEU WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        $thuongHieu = $stmt->fetch();

        if (!$thuongHieu) {
            $this->setFlash('error', 'Không tìm thấy thương hiệu!');
            $this->redirect('thuongHieu/danhSach');
        }

        // Lấy sản phẩm của thương hiệu
        $sql = "SELECT t.*, nt.TenNhomThuoc 
                FROM THUOC t
                LEFT JOIN NHOM_THUOC nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                WHERE t.MaThuongHieu = ?";
        
        // Sắp xếp
        $sql .= match($sapXep) {
            'gia-tang' => " ORDER BY t.GiaBan ASC",
            'gia-giam' => " ORDER BY t.GiaBan DESC",
            'ten-az' => " ORDER BY t.TenThuoc ASC",
            'ten-za' => " ORDER BY t.TenThuoc DESC",
            'moi-nhat' => " ORDER BY t.NgayTao DESC",
            default => " ORDER BY t.MaThuoc DESC"
        };

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $allThuocs = $stmt->fetchAll();

        // Phân trang
        $pageSize = 12;
        $totalItems = count($allThuocs);
        $totalPages = ceil($totalItems / $pageSize);
        $thuocs = array_slice($allThuocs, ($page - 1) * $pageSize, $pageSize);

        $data = [
            'title' => $thuongHieu['TenThuongHieu'] . ' - ' . STORE_NAME,
            'thuongHieu' => $thuongHieu,
            'thuocs' => $thuocs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'sapXep' => $sapXep
        ];

        $this->view('thuong-hieu/chi-tiet', $data);
    }
}
