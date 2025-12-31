<?php
/**
 * NhomThuocController - Quản lý nhóm thuốc
 */
class NhomThuocController extends Controller
{
    // GET: nhomThuoc/danhSach
    public function danhSach()
    {
        // Lấy nhóm cha (MaDanhMucCha = NULL)
        $stmt = $this->db->query("SELECT * FROM NHOM_THUOC WHERE MaDanhMucCha IS NULL ORDER BY TenNhomThuoc");
        $nhomChas = $stmt->fetchAll();

        // Đếm số sản phẩm cho mỗi nhóm
        foreach ($nhomChas as &$nhom) {
            // Lấy danh mục con
            $stmtCon = $this->db->prepare("SELECT MaNhomThuoc FROM NHOM_THUOC WHERE MaDanhMucCha = ?");
            $stmtCon->execute([$nhom['MaNhomThuoc']]);
            $danhMucCon = $stmtCon->fetchAll(PDO::FETCH_COLUMN);

            // Đếm sản phẩm trực tiếp
            $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM THUOC WHERE MaNhomThuoc = ?");
            $stmtCount->execute([$nhom['MaNhomThuoc']]);
            $soLuong = $stmtCount->fetchColumn();

            // Đếm sản phẩm từ danh mục con
            if (!empty($danhMucCon)) {
                $placeholders = implode(',', array_fill(0, count($danhMucCon), '?'));
                $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM THUOC WHERE MaNhomThuoc IN ($placeholders)");
                $stmtCount->execute($danhMucCon);
                $soLuong += $stmtCount->fetchColumn();
            }

            $nhom['SoLuongSanPham'] = $soLuong;
            $nhom['DanhMucCon'] = $danhMucCon;
        }

        $data = [
            'title' => 'Danh mục thuốc - ' . STORE_NAME,
            'nhomThuocs' => $nhomChas
        ];

        $this->view('nhom-thuoc/danh-sach', $data);
    }

    // GET: nhomThuoc/chiTiet/{id}
    public function chiTiet($id = null)
    {
        if (!$id) {
            $this->redirect('nhomThuoc/danhSach');
        }

        $page = (int)($_GET['page'] ?? 1);
        $sapXep = $_GET['sapXep'] ?? null;

        // Lấy thông tin nhóm thuốc
        $stmt = $this->db->prepare("SELECT * FROM NHOM_THUOC WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        $nhomThuoc = $stmt->fetch();

        if (!$nhomThuoc) {
            $this->setFlash('error', 'Không tìm thấy nhóm thuốc!');
            $this->redirect('nhomThuoc/danhSach');
        }

        // Lấy danh mục con
        $stmt = $this->db->prepare("SELECT * FROM NHOM_THUOC WHERE MaDanhMucCha = ?");
        $stmt->execute([$id]);
        $danhMucCon = $stmt->fetchAll();

        // Lấy tất cả ID nhóm (bao gồm nhóm hiện tại và con)
        $nhomIds = [$id];
        foreach ($danhMucCon as $dm) {
            $nhomIds[] = $dm['MaNhomThuoc'];
        }

        // Lấy sản phẩm
        $placeholders = implode(',', array_fill(0, count($nhomIds), '?'));
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu 
                FROM THUOC t
                LEFT JOIN NHOM_THUOC nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN THUONG_HIEU th ON t.MaThuongHieu = th.MaThuongHieu
                WHERE t.MaNhomThuoc IN ($placeholders)";
        
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
        $stmt->execute($nhomIds);
        $allThuocs = $stmt->fetchAll();

        // Phân trang
        $pageSize = 12;
        $totalItems = count($allThuocs);
        $totalPages = ceil($totalItems / $pageSize);
        $thuocs = array_slice($allThuocs, ($page - 1) * $pageSize, $pageSize);

        // Lấy danh mục cha nếu có
        $danhMucCha = null;
        if (!empty($nhomThuoc['MaDanhMucCha'])) {
            $stmt = $this->db->prepare("SELECT * FROM NHOM_THUOC WHERE MaNhomThuoc = ?");
            $stmt->execute([$nhomThuoc['MaDanhMucCha']]);
            $danhMucCha = $stmt->fetch();
        }

        $data = [
            'title' => $nhomThuoc['TenNhomThuoc'] . ' - ' . STORE_NAME,
            'nhomThuoc' => $nhomThuoc,
            'danhMucCha' => $danhMucCha,
            'danhMucCon' => $danhMucCon,
            'thuocs' => $thuocs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'sapXep' => $sapXep
        ];

        $this->view('nhom-thuoc/chi-tiet', $data);
    }
}
