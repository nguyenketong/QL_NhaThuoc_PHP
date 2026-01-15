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
        
        // Tạo Google Login URL
        $googleLoginUrl = '';
        if (!empty(GOOGLE_CLIENT_ID)) {
            $params = [
                'client_id' => GOOGLE_CLIENT_ID,
                'redirect_uri' => GOOGLE_REDIRECT_URI,
                'response_type' => 'code',
                'scope' => 'email profile',
                'access_type' => 'online',
                'prompt' => 'select_account'
            ];
            $googleLoginUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        }
        
        $this->view('user/phone-login', [
            'title' => 'Đăng nhập - ' . STORE_NAME,
            'googleLoginUrl' => $googleLoginUrl
        ]);
    }

    // GET: user/googleCallback - Xử lý callback từ Google
    public function googleCallback()
    {
        $code = $_GET['code'] ?? '';
        
        if (empty($code)) {
            $this->setFlash('error', 'Đăng nhập Google thất bại!');
            $this->redirect('user/phoneLogin');
        }

        // Đổi code lấy access token
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $tokenData = [
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $tokenResponse = curl_exec($ch);
        curl_close($ch);

        $tokenResult = json_decode($tokenResponse, true);
        
        if (empty($tokenResult['access_token'])) {
            $this->setFlash('error', 'Không thể lấy token từ Google!');
            $this->redirect('user/phoneLogin');
        }

        // Lấy thông tin user từ Google
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $tokenResult['access_token'];
        $ch = curl_init($userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userResponse = curl_exec($ch);
        curl_close($ch);

        $googleUser = json_decode($userResponse, true);
        
        if (empty($googleUser['email'])) {
            $this->setFlash('error', 'Không thể lấy thông tin từ Google!');
            $this->redirect('user/phoneLogin');
        }

        // Tìm hoặc tạo người dùng
        $nguoiDungModel = $this->model('NguoiDungModel');
        $nguoiDung = $nguoiDungModel->findByEmail($googleUser['email']);

        if (!$nguoiDung) {
            // Tạo mới
            $maNguoiDung = $nguoiDungModel->create([
                'Email' => $googleUser['email'],
                'HoTen' => $googleUser['name'] ?? 'Google User',
                'Avatar' => $googleUser['picture'] ?? '',
                'GoogleId' => $googleUser['id'] ?? '',
                'LoaiDangNhap' => 'Google',
                'VaiTro' => 'User',
                'NgayTao' => date('Y-m-d H:i:s')
            ]);
            $nguoiDung = $nguoiDungModel->getById($maNguoiDung);
        } else {
            // Cập nhật thông tin
            $nguoiDungModel->update($nguoiDung['MaNguoiDung'], [
                'HoTen' => $googleUser['name'] ?? $nguoiDung['HoTen'],
                'Avatar' => $googleUser['picture'] ?? $nguoiDung['Avatar'],
                'GoogleId' => $googleUser['id'] ?? ''
            ], 'MaNguoiDung');
        }

        // Lưu đăng nhập
        $_SESSION['user_id'] = $nguoiDung['MaNguoiDung'];
        setcookie('UserId', $nguoiDung['MaNguoiDung'], time() + 30 * 24 * 3600, '/');

        $this->setFlash('success', 'Đăng nhập Google thành công!');
        
        // Redirect về trang trước đó
        $redirectUrl = $_SESSION['redirect_after_login'] ?? '';
        unset($_SESSION['redirect_after_login']);
        
        $this->redirect($redirectUrl ?: '');
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
        
        // Lưu vào session
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_phone'] = $soDienThoai;
        $_SESSION['otp_time'] = time();

        // Lưu vào database
        $nguoiDungModel = $this->model('NguoiDungModel');
        $nguoiDung = $nguoiDungModel->findByPhone($soDienThoai);
        
        if (!$nguoiDung) {
            // Tạo người dùng mới
            $maNguoiDung = $nguoiDungModel->create([
                'SoDienThoai' => $soDienThoai,
                'HoTen' => 'Khách hàng ' . substr($soDienThoai, -4),
                'VaiTro' => 'User',
                'OTP' => $otp,
                'OTP_Expire' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ]);
        } else {
            // Cập nhật OTP cho người dùng đã tồn tại
            $nguoiDungModel->saveOtp($nguoiDung['MaNguoiDung'], $otp);
        }

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
        $soDienThoai = $_POST['soDienThoai'] ?? null;

        $nguoiDungModel = $this->model('NguoiDungModel');
        
        $updateData = [
            'HoTen' => $hoTen,
            'DiaChi' => $diaChi
        ];
        
        // Chỉ cập nhật SĐT nếu user đăng nhập bằng Google và chưa có SĐT
        if ($soDienThoai) {
            $soDienThoai = preg_replace('/[^0-9]/', '', $soDienThoai);
            if (strlen($soDienThoai) >= 10) {
                $updateData['SoDienThoai'] = $soDienThoai;
            }
        }
        
        $nguoiDungModel->update($this->getUserId(), $updateData, 'MaNguoiDung');

        // Cập nhật session
        $_SESSION['user_name'] = $hoTen;

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
        
        // SmsType = 8: Tin nhắn đầu số ngẫu nhiên (không cần Brandname)
        // SmsType = 2: Tin nhắn Brandname (cần đăng ký trước)
        $data = [
            'ApiKey' => ESMS_API_KEY,
            'SecretKey' => ESMS_SECRET_KEY,
            'Phone' => $phone,
            'Content' => $content,
            'SmsType' => 8
        ];

        $ch = curl_init(ESMS_BASE_URL . '/SendMultipleMessage_V4_post_json/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'message' => 'Lỗi kết nối: ' . $error];
        }

        $result = json_decode($response, true);
        
        if (isset($result['CodeResult']) && $result['CodeResult'] == '100') {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => $result['ErrorMessage'] ?? 'Không thể gửi OTP'];
    }
}
