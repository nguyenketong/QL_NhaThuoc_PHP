<?php
/**
 * DonHangController - Quản lý đơn hàng
 */
class DonHangController extends Controller
{
    // GET: donHang (default route)
    public function index()
    {
        $this->danhSach();
    }

    // GET: donHang/danhSach
    public function danhSach()
    {
        $this->requireLogin();

        $donHangModel = $this->model('DonHangModel');
        $danhSach = $donHangModel->getByNguoiDung($this->getUserId());

        $data = [
            'title' => 'Đơn hàng của tôi - ' . STORE_NAME,
            'danhSach' => $danhSach
        ];

        $this->view('don-hang/danh-sach', $data);
    }

    // GET: donHang/chiTiet/{id}
    public function chiTiet($id = null)
    {
        $this->requireLogin();

        if (!$id) {
            $this->redirect('donHang/danhSach');
        }

        $donHangModel = $this->model('DonHangModel');
        $donHang = $donHangModel->getById($id);

        if (!$donHang || $donHang['MaNguoiDung'] != $this->getUserId()) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng!');
            $this->redirect('donHang/danhSach');
        }

        $chiTiet = $donHangModel->getChiTiet($id);

        $data = [
            'title' => 'Chi tiết đơn hàng #' . $id . ' - ' . STORE_NAME,
            'donHang' => $donHang,
            'chiTiet' => $chiTiet
        ];

        $this->view('don-hang/chi-tiet', $data);
    }

    // GET: donHang/theoDoi/{id}
    public function theoDoi($id = null)
    {
        $this->requireLogin();

        if (!$id) {
            $this->redirect('donHang/danhSach');
        }

        $donHangModel = $this->model('DonHangModel');
        $donHang = $donHangModel->getById($id);

        if (!$donHang || $donHang['MaNguoiDung'] != $this->getUserId()) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng!');
            $this->redirect('donHang/danhSach');
        }

        $data = [
            'title' => 'Theo dõi đơn hàng #' . $id,
            'donHang' => $donHang
        ];

        $this->view('don-hang/theo-doi', $data);
    }

    // GET: donHang/huy/{id}
    public function huy($id = null)
    {
        $this->requireLogin();

        if (!$id) {
            $this->redirect('donHang/danhSach');
        }

        $donHangModel = $this->model('DonHangModel');
        $donHang = $donHangModel->getById($id);

        if (!$donHang || $donHang['MaNguoiDung'] != $this->getUserId()) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng!');
            $this->redirect('donHang/danhSach');
        }

        // Chỉ hủy được đơn hàng đang chờ xử lý
        if ($donHang['TrangThai'] !== 'Cho xu ly') {
            $this->setFlash('error', 'Không thể hủy đơn hàng này!');
            $this->redirect('donHang/chiTiet/' . $id);
        }

        // Không được hủy nếu đã thanh toán
        if (!empty($donHang['DaThanhToan'])) {
            $this->setFlash('error', 'Đơn hàng đã thanh toán không thể hủy!');
            $this->redirect('donHang/chiTiet/' . $id);
        }

        try {
            // Cập nhật trạng thái (không cần hoàn tồn kho vì chưa trừ)
            $donHangModel->updateTrangThai($id, 'Da huy');
            $this->setFlash('success', 'Đã hủy đơn hàng thành công!');

        } catch (Exception $e) {
            $this->setFlash('error', 'Không thể hủy đơn hàng!');
        }

        $this->redirect('donHang/danhSach');
    }

    // GET: donHang/thanhToanQR/{id}
    public function thanhToanQR($id = null)
    {
        $this->requireLogin();

        if (!$id) {
            $this->redirect('donHang/danhSach');
        }

        $donHangModel = $this->model('DonHangModel');
        $donHang = $donHangModel->getById($id);

        if (!$donHang || $donHang['MaNguoiDung'] != $this->getUserId()) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng!');
            $this->redirect('donHang/danhSach');
        }

        $data = [
            'title' => 'Thanh toán QR - Đơn hàng #' . $id,
            'donHang' => $donHang
        ];

        $this->view('don-hang/thanh-toan-qr', $data);
    }
}
