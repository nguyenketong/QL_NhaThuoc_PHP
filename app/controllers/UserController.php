<?php
/**
 * UserController - Quản lý người dùng
 */
class UserController extends Controller
{
    // GET: user/phoneLogin
    public function phoneLogin()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('');
        }
        $this->view('user/phone-login', ['title' => 'Đăng nhập - ' . STORE_NAME]);
    }

    // POST: user/sendOtp
    public function sendOtp()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/phoneLogin');
        }

        $soDienThoai = $_POST['soDienThoai'] ?? '';
        $soDienThoai = preg_replace('/[^0-9]/', '', $soDienThoai);

        if (strlen($soDienThoai) < 10) {
            $this->setFlash('error', 'Số điện thoại không hợp lệ!');
            $this->redirect('user/phoneLogin');
        }

        // Tạo OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_phone'] = $soDienThoai;
        $_SESSION['otp_time'] = time();

        // Gửi OTP qua eSMS
        if (OTP_MODE === 'real') {
            $result = $this->sendEsmsOtp($soDienThoai, $otp);
            if (!$result['success']) {
                $this->setFlash('error', $result['message']);
                $this->redirect('user/phoneLogin');
            }
        }

        $this->redirect('user/verifyOtp');
    }

    // GET: user/verifyOtp
    public function verifyOtp()
    {
        $soDienThoai = $_SESSION['otp_phone'] ?? '';
        if (empty($soDienThoai)) {
            $this->redirect('user/phoneLogin');
        }

        $data = [
            'title' => 'Xác nhận OTP - ' . STORE_NAME,
            'soDienThoai' => $soDienThoai,
            'devOtp' => OTP_MODE === 'dev' ? ($_SESSION['otp'] ?? '') : null
        ];
        $this->view('user/verify-otp', $data);
    }

    // POST: user/confirmOtp
    public function confirmOtp()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/phoneLogin');
        }

        $otpInput = $_POST['otp'] ?? '';
        $soDienThoai = $_SESSION['otp_phone'] ?? '';
        $otpSaved = $_SESSION['otp'] ?? '';
        $otpTime = $_SESSION['otp_time'] ?? 0;

        // Kiểm tra hết hạn (5 phút)
        if (time() - $otpTime > 300) {
            $this->setFlash('error', 'Mã OTP đã hết hạn!');
            $this->redirect('user/phoneLogin');
        }

        // Kiểm tra OTP
        if ($otpInput != $otpSaved) {
            $this->setFlash('error', 'Mã OTP không đúng!');
            $this->redirect('user/verifyOtp');
        }

        // Tìm hoặc tạo người dùng
        $nguoiDungModel = $this->model('NguoiDungModel');
        $nguoiDung = $nguoiDungModel->findByPhone($soDienThoai);

        if (!$nguoiDung) {
            // Tạo mới
            $maNguoiDung = $nguoiDungModel->create([
                'SoDienThoai' => $soDienThoai,
                'HoTen' => 'Khách hàng ' . substr($soDienThoai, -4),
                'VaiTro' => 'User'
            ]);
            $nguoiDung = $nguoiDungModel->getById($maNguoiDung);
        }

        // Lưu đăng nhập
        $_SESSION['user_id'] = $nguoiDung['MaNguoiDung'];
        setcookie('UserId', $nguoiDung['MaNguoiDung'], time() + 30 * 24 * 3600, '/');

        // Xóa session OTP
        unset($_SESSION['otp'], $_SESSION['otp_phone'], $_SESSION['otp_time']);

        $this->setFlash('success', 'Đăng nhập thành công!');
        
        // Redirect về trang trước đó
        $redirectUrl = $_SESSION['redirect_after_login'] ?? '';
        unset($_SESSION['redirect_after_login']);
        
        $this->redirect($redirectUrl ?: '');
    }

    // GET: user/profile
    public function profile()
    {
        $this->requireLogin();

        $nguoiDungModel = $this->model('NguoiDungModel');
        $nguoiDung = $nguoiDungModel->getById($this->getUserId());

        if (!$nguoiDung) {
            $this->redirect('user/logout');
        }

        // Thống kê
        $tongDonHang = $this->db->prepare("SELECT COUNT(*) FROM DON_HANG WHERE MaNguoiDung = ?");
        $tongDonHang->execute([$this->getUserId()]);
        
        $tongChiTieu = $this->db->prepare("SELECT COALESCE(SUM(TongTien), 0) FROM DON_HANG WHERE MaNguoiDung = ? AND TrangThai = 'Hoàn thành'");
        $tongChiTieu->execute([$this->getUserId()]);

        $data = [
            'title' => 'Thông tin tài khoản - ' . STORE_NAME,
            'nguoiDung' => $nguoiDung,
            'tongDonHang' => $tongDonHang->fetchColumn(),
            'tongChiTieu' => $tongChiTieu->fetchColumn()
        ];

        $this->view('user/profile', $data);
    }

    // POST: user/updateProfile
    public function updateProfile()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/profile');
        }

        $hoTen = $_POST['hoTen'] ?? '';
        $diaChi = $_POST['diaChi'] ?? '';

        $nguoiDungModel = $this->model('NguoiDungModel');
        $nguoiDungModel->update($this->getUserId(), [
            'HoTen' => $hoTen,
            'DiaChi' => $diaChi
        ]);

        $this->setFlash('success', 'Cập nhật thông tin thành công!');
        $this->redirect('user/profile');
    }

    // GET: user/diaChi
    public function diaChi()
    {
        $this->requireLogin();

        $nguoiDungModel = $this->model('NguoiDungModel');
        $nguoiDung = $nguoiDungModel->getById($this->getUserId());

        $data = [
            'title' => 'Quản lý địa chỉ - ' . STORE_NAME,
            'nguoiDung' => $nguoiDung,
            'activeMenu' => 'diachi'
        ];

        $this->view('user/dia-chi', $data);
    }

    // GET: user/logout
    public function logout()
    {
        unset($_SESSION['user_id']);
        setcookie('UserId', '', time() - 3600, '/');
        
        $this->setFlash('success', 'Đăng xuất thành công!');
        $this->redirect('');
    }

    // Gửi OTP qua eSMS
    private function sendEsmsOtp($phone, $otp)
    {
        $content = "Ma OTP cua ban la: $otp. Ma co hieu luc trong 5 phut.";
        
        $data = [
            'ApiKey' => ESMS_API_KEY,
            'SecretKey' => ESMS_SECRET_KEY,
            'Phone' => $phone,
            'Content' => $content,
            'Brandname' => ESMS_BRAND_NAME,
            'SmsType' => 2
        ];

        $ch = curl_init(ESMS_BASE_URL . '/SendMultipleMessage_V4_post_json/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        
        if (isset($result['CodeResult']) && $result['CodeResult'] == '100') {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => $result['ErrorMessage'] ?? 'Không thể gửi OTP'];
    }
}
