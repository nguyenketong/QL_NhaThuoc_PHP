<?php
/**
 * Dashboard Controller - Thống kê tổng quan
 */
class DashboardController extends AdminController
{
    public function index()
    {
        // 1. Tổng số thuốc
        $stmt = $this->db->query("SELECT COUNT(*) FROM THUOC");
        $tongThuoc = $stmt->fetchColumn();

        // 2. Tổng số đơn hàng
        $stmt = $this->db->query("SELECT COUNT(*) FROM DON_HANG");
        $tongDonHang = $stmt->fetchColumn();

        // 3. Tổng số khách hàng
        $stmt = $this->db->query("SELECT COUNT(*) FROM NGUOI_DUNG WHERE VaiTro = 'User' OR VaiTro IS NULL");
        $tongKhachHang = $stmt->fetchColumn();

        // 4. Đơn hàng theo trạng thái
        $stmt = $this->db->query("
            SELECT 
                SUM(CASE WHEN TrangThai = 'Cho xu ly' THEN 1 ELSE 0 END) as ChoXuLy,
                SUM(CASE WHEN TrangThai = 'Dang giao' THEN 1 ELSE 0 END) as DangGiao,
                SUM(CASE WHEN TrangThai = 'Hoan thanh' THEN 1 ELSE 0 END) as HoanThanh,
                SUM(CASE WHEN TrangThai = 'Da huy' THEN 1 ELSE 0 END) as DaHuy
            FROM DON_HANG
        ");
        $trangThaiDH = $stmt->fetch(PDO::FETCH_ASSOC);

        // 5. Doanh thu tháng này
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(TongTien), 0) as DoanhThu
            FROM DON_HANG 
            WHERE TrangThai = 'Hoan thanh' 
            AND MONTH(NgayDatHang) = MONTH(CURRENT_DATE())
            AND YEAR(NgayDatHang) = YEAR(CURRENT_DATE())
        ");
        $doanhThuThang = $stmt->fetchColumn();

        // 6. Đơn hàng gần đây (Top 5)
        $stmt = $this->db->query("
            SELECT dh.MaDonHang, dh.NgayDatHang, dh.TrangThai, dh.TongTien, nd.HoTen
            FROM DON_HANG dh
            LEFT JOIN NGUOI_DUNG nd ON dh.MaNguoiDung = nd.MaNguoiDung
            ORDER BY dh.NgayDatHang DESC
            LIMIT 5
        ");
        $donHangGanDay = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 7. Top thuốc bán chạy (Top 5)
        $stmt = $this->db->query("
            SELECT t.TenThuoc, COALESCE(SUM(ct.SoLuong), 0) as TongBan
            FROM THUOC t
            LEFT JOIN CHI_TIET_DON_HANG ct ON t.MaThuoc = ct.MaThuoc
            LEFT JOIN DON_HANG dh ON ct.MaDonHang = dh.MaDonHang AND dh.TrangThai = 'Hoan thanh'
            GROUP BY t.MaThuoc, t.TenThuoc
            ORDER BY TongBan DESC
            LIMIT 5
        ");
        $topThuocBanChay = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 8. Doanh thu 7 ngày gần nhất
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $ngay = date('Y-m-d', strtotime("-$i days"));
            $chartLabels[] = date('d/m', strtotime($ngay));
            
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(TongTien), 0) 
                FROM DON_HANG 
                WHERE DATE(NgayDatHang) = ? AND TrangThai != 'Da huy'
            ");
            $stmt->execute([$ngay]);
            $chartData[] = (float)$stmt->fetchColumn();
        }

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'tongThuoc' => $tongThuoc,
            'tongDonHang' => $tongDonHang,
            'tongKhachHang' => $tongKhachHang,
            'choXuLy' => $trangThaiDH['ChoXuLy'] ?? 0,
            'dangGiao' => $trangThaiDH['DangGiao'] ?? 0,
            'hoanThanh' => $trangThaiDH['HoanThanh'] ?? 0,
            'daHuy' => $trangThaiDH['DaHuy'] ?? 0,
            'doanhThuThang' => $doanhThuThang,
            'donHangGanDay' => $donHangGanDay,
            'topThuocBanChay' => $topThuocBanChay,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData
        ]);
    }
}
