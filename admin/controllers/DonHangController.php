<?php
/**
 * DonHang Controller - Quản lý đơn hàng
 */
class DonHangController extends AdminController
{
    public function index()
    {
        $trangThai = $_GET['trangThai'] ?? '';
        
        $sql = "SELECT dh.*, nd.HoTen, nd.SoDienThoai,
                (SELECT COUNT(*) FROM CHI_TIET_DON_HANG WHERE MaDonHang = dh.MaDonHang) as SoSanPham
                FROM DON_HANG dh
                LEFT JOIN NGUOI_DUNG nd ON dh.MaNguoiDung = nd.MaNguoiDung";
        
        if ($trangThai) {
            $sql .= " WHERE dh.TrangThai = :trangThai";
        }
        $sql .= " ORDER BY dh.NgayDatHang DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($trangThai) {
            $stmt->execute(['trangThai' => $trangThai]);
        } else {
            $stmt->execute();
        }
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('don-hang/index', [
            'title' => 'Quản lý đơn hàng',
            'danhSach' => $danhSach,
            'trangThaiFilter' => $trangThai
        ]);
    }

    public function details($id)
    {
        // Lấy thông tin đơn hàng
        $stmt = $this->db->prepare("
            SELECT dh.*, nd.HoTen, nd.SoDienThoai, nd.DiaChi as DiaChiND
            FROM DON_HANG dh
            LEFT JOIN NGUOI_DUNG nd ON dh.MaNguoiDung = nd.MaNguoiDung
            WHERE dh.MaDonHang = ?
        ");
        $stmt->execute([$id]);
        $donHang = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$donHang) {
            $this->redirect('?controller=don-hang');
            return;
        }

        // Lấy chi tiết đơn hàng
        $stmt = $this->db->prepare("
            SELECT ct.*, t.TenThuoc, t.HinhAnh
            FROM CHI_TIET_DON_HANG ct
            LEFT JOIN THUOC t ON ct.MaThuoc = t.MaThuoc
            WHERE ct.MaDonHang = ?
        ");
        $stmt->execute([$id]);
        $chiTiet = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('don-hang/details', [
            'title' => 'Chi tiết đơn hàng #' . $id,
            'donHang' => $donHang,
            'chiTiet' => $chiTiet
        ]);
    }

    public function capNhatTrangThai()
    {
        if (!$this->isPost()) {
            $this->redirect('?controller=don-hang');
            return;
        }

        $id = $_POST['id'] ?? 0;
        $trangThaiMoi = $_POST['trangThai'] ?? '';

        // Lấy đơn hàng hiện tại
        $stmt = $this->db->prepare("SELECT * FROM DON_HANG WHERE MaDonHang = ?");
        $stmt->execute([$id]);
        $donHang = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$donHang) {
            $this->redirect('?controller=don-hang');
            return;
        }

        $trangThaiHienTai = $donHang['TrangThai'];

        // Quy tắc 1: Đã hủy không thể chuyển
        if ($trangThaiHienTai == 'Da huy') {
            $this->setFlash('error', 'Đơn hàng đã hủy không thể thay đổi trạng thái!');
            $this->redirect("?controller=don-hang&action=details&id=$id");
            return;
        }

        // Quy tắc 2: Hoàn thành không thể chuyển
        if ($trangThaiHienTai == 'Hoan thanh') {
            $this->setFlash('error', 'Đơn hàng đã hoàn thành không thể thay đổi trạng thái!');
            $this->redirect("?controller=don-hang&action=details&id=$id");
            return;
        }

        // Quy tắc 3: Đang giao không được hủy
        if ($trangThaiHienTai == 'Dang giao' && $trangThaiMoi == 'Da huy') {
            $this->setFlash('error', 'Đơn hàng đang giao không thể hủy!');
            $this->redirect("?controller=don-hang&action=details&id=$id");
            return;
        }

        // Quy tắc 4: Đã thanh toán không thể hủy
        if ($donHang['DaThanhToan'] && $trangThaiMoi == 'Da huy') {
            $this->setFlash('error', 'Đơn hàng đã thanh toán không thể hủy!');
            $this->redirect("?controller=don-hang&action=details&id=$id");
            return;
        }

        // Quy tắc 5: Chuyển khoản chưa thanh toán không cho giao/hoàn thành
        if ($donHang['PhuongThucThanhToan'] == 'Chuyển khoản' && !$donHang['DaThanhToan']) {
            if ($trangThaiMoi == 'Dang giao' || $trangThaiMoi == 'Hoan thanh') {
                $this->setFlash('error', 'Vui lòng xác nhận đã nhận tiền chuyển khoản trước!');
                $this->redirect("?controller=don-hang&action=details&id=$id");
                return;
            }
        }

        // Trừ tồn kho khi chuyển từ "Chờ xử lý" sang "Đang giao" hoặc "Hoàn thành"
        if ($trangThaiHienTai == 'Cho xu ly' && ($trangThaiMoi == 'Dang giao' || $trangThaiMoi == 'Hoan thanh')) {
            $stmt = $this->db->prepare("SELECT * FROM CHI_TIET_DON_HANG WHERE MaDonHang = ?");
            $stmt->execute([$id]);
            $chiTiet = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($chiTiet as $ct) {
                $this->db->prepare("
                    UPDATE THUOC SET 
                        SoLuongTon = COALESCE(SoLuongTon, 0) - ?,
                        SoLuongDaBan = COALESCE(SoLuongDaBan, 0) + ?
                    WHERE MaThuoc = ?
                ")->execute([$ct['SoLuong'], $ct['SoLuong'], $ct['MaThuoc']]);
            }
        }

        // Cập nhật trạng thái
        $stmt = $this->db->prepare("UPDATE DON_HANG SET TrangThai = ? WHERE MaDonHang = ?");
        $stmt->execute([$trangThaiMoi, $id]);

        // Tạo thông báo cho user
        $tieuDe = '';
        $noiDung = '';
        switch ($trangThaiMoi) {
            case 'Dang giao':
                $tieuDe = "Đơn hàng #$id đang giao";
                $noiDung = "Đơn hàng của bạn đã được giao cho đơn vị vận chuyển. Vui lòng chú ý điện thoại!";
                break;
            case 'Hoan thanh':
                $tieuDe = "Đơn hàng #$id hoàn thành";
                $noiDung = "Đơn hàng đã giao thành công. Cảm ơn bạn đã mua hàng!";
                break;
            case 'Da huy':
                $tieuDe = "Đơn hàng #$id đã hủy";
                $noiDung = "Đơn hàng của bạn đã bị hủy. Liên hệ hotline nếu cần hỗ trợ.";
                break;
            default:
                $tieuDe = "Đơn hàng #$id cập nhật";
                $noiDung = "Trạng thái đơn hàng đã được cập nhật thành: $trangThaiMoi";
        }

        $stmt = $this->db->prepare("
            INSERT INTO THONG_BAO (MaNguoiDung, MaDonHang, TieuDe, NoiDung, LoaiThongBao, DuongDan, NgayTao)
            VALUES (?, ?, ?, ?, 'DonHang', ?, NOW())
        ");
        $stmt->execute([
            $donHang['MaNguoiDung'],
            $id,
            $tieuDe,
            $noiDung,
            "/don-hang/chi-tiet/$id"
        ]);

        $this->setFlash('success', 'Cập nhật trạng thái đơn hàng thành công!');
        $this->redirect("?controller=don-hang&action=details&id=$id");
    }

    public function xacNhanThanhToan()
    {
        if (!$this->isPost()) {
            $this->redirect('?controller=don-hang');
            return;
        }

        $id = $_POST['id'] ?? 0;

        // Lấy đơn hàng
        $stmt = $this->db->prepare("SELECT * FROM DON_HANG WHERE MaDonHang = ?");
        $stmt->execute([$id]);
        $donHang = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($donHang && $donHang['PhuongThucThanhToan'] == 'Chuyển khoản') {
            // Cập nhật đã thanh toán
            $stmt = $this->db->prepare("UPDATE DON_HANG SET DaThanhToan = 1 WHERE MaDonHang = ?");
            $stmt->execute([$id]);

            // Tạo thông báo
            $stmt = $this->db->prepare("
                INSERT INTO THONG_BAO (MaNguoiDung, MaDonHang, TieuDe, NoiDung, LoaiThongBao, DuongDan, NgayTao)
                VALUES (?, ?, ?, ?, 'DonHang', ?, NOW())
            ");
            $stmt->execute([
                $donHang['MaNguoiDung'],
                $id,
                "Đơn hàng #$id đã xác nhận thanh toán",
                "Chúng tôi đã nhận được tiền chuyển khoản của bạn. Đơn hàng sẽ sớm được xử lý!",
                "/don-hang/chi-tiet/$id"
            ]);

            $this->setFlash('success', 'Đã xác nhận thanh toán thành công!');
        }

        $this->redirect("?controller=don-hang&action=details&id=$id");
    }
}
