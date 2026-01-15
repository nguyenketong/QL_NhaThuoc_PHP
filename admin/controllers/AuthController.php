<?php
/**
 * Auth Controller - Đăng nhập Admin
 */
class AuthController extends AdminController
{
    // Mật khẩu mặc định
    const DEFAULT_PASSWORD = 'admin123';

    public function login()
    {
        // Nếu đã đăng nhập thì redirect về Dashboard
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            $this->redirect('');
            return;
        }

        $error = null;

        if ($this->isPost()) {
            $soDienThoai = $_POST['soDienThoai'] ?? '';
            $matKhau = $_POST['matKhau'] ?? '';

            // Tìm người dùng có quyền Admin
            $stmt = $this->db->prepare("SELECT * FROM NGUOI_DUNG WHERE SoDienThoai = ? AND VaiTro = 'Admin'");
            $stmt->execute([$soDienThoai]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin) {
                $error = 'Tài khoản không có quyền quản trị!';
            } else {
                $matKhauDB = $admin['MatKhau'] ?? null;
                $isValidPassword = false;
                $isDefaultPassword = false;

                // Kiểm tra mật khẩu
                if (empty($matKhauDB) || $matKhauDB === self::DEFAULT_PASSWORD) {
                    // Mật khẩu chưa được hash hoặc là mặc định
                    if ($matKhau === self::DEFAULT_PASSWORD) {
                        $isValidPassword = true;
                        $isDefaultPassword = true;
                    }
                } elseif (password_verify($matKhau, $matKhauDB)) {
                    // Mật khẩu đã được hash
                    $isValidPassword = true;
                    $isDefaultPassword = false;
                } elseif ($matKhau === $matKhauDB) {
                    // Mật khẩu plain text (tương thích ngược)
                    $isValidPassword = true;
                    $isDefaultPassword = ($matKhau === self::DEFAULT_PASSWORD);
                }

                if (!$isValidPassword) {
                    $error = 'Mật khẩu không đúng!';
                } else {
                    // Lưu session
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['MaNguoiDung'];
                    $_SESSION['admin_name'] = $admin['HoTen'] ?? $admin['SoDienThoai'];
                    $_SESSION['admin_phone'] = $admin['SoDienThoai'];
                    $_SESSION['require_password_change'] = $isDefaultPassword;

                    // Lưu cookie ghi nhớ (7 ngày)
                    setcookie('AdminLoggedIn', 'true', time() + (7 * 24 * 60 * 60), '/', '', false, true);

                    if ($isDefaultPassword) {
                        $this->setFlash('warning', 'Bạn đang sử dụng mật khẩu mặc định. Vui lòng đổi mật khẩu để bảo mật tài khoản!');
                        $this->redirect('?controller=auth&action=changePassword');
                    } else {
                        $this->setFlash('success', 'Đăng nhập Admin thành công!');
                        $this->redirect('');
                    }
                    return;
                }
            }
        }

        $this->viewWithoutLayout('auth/login', ['error' => $error]);
    }

    public function changePassword()
    {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $this->redirect('?controller=auth&action=login');
            return;
        }

        $error = null;
        $success = null;
        $requireChange = $_SESSION['require_password_change'] ?? false;

        if ($this->isPost()) {
            $matKhauCu = $_POST['matKhauCu'] ?? '';
            $matKhauMoi = $_POST['matKhauMoi'] ?? '';
            $xacNhanMatKhau = $_POST['xacNhanMatKhau'] ?? '';

            // Validate
            if (empty($matKhauCu) || empty($matKhauMoi) || empty($xacNhanMatKhau)) {
                $error = 'Vui lòng nhập đầy đủ thông tin!';
            } elseif (strlen($matKhauMoi) < 6) {
                $error = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
            } elseif ($matKhauMoi !== $xacNhanMatKhau) {
                $error = 'Xác nhận mật khẩu không khớp!';
            } elseif ($matKhauMoi === self::DEFAULT_PASSWORD) {
                $error = 'Mật khẩu mới không được trùng với mật khẩu mặc định!';
            } else {
                // Lấy thông tin admin hiện tại
                $adminId = $_SESSION['admin_id'];
                $stmt = $this->db->prepare("SELECT MatKhau FROM NGUOI_DUNG WHERE MaNguoiDung = ?");
                $stmt->execute([$adminId]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                $matKhauDB = $admin['MatKhau'] ?? null;

                // Kiểm tra mật khẩu cũ
                $isValidOldPassword = false;
                if (empty($matKhauDB) || $matKhauDB === self::DEFAULT_PASSWORD) {
                    $isValidOldPassword = ($matKhauCu === self::DEFAULT_PASSWORD);
                } elseif (password_verify($matKhauCu, $matKhauDB)) {
                    $isValidOldPassword = true;
                } elseif ($matKhauCu === $matKhauDB) {
                    $isValidOldPassword = true;
                }

                if (!$isValidOldPassword) {
                    $error = 'Mật khẩu cũ không đúng!';
                } else {
                    // Hash mật khẩu mới và cập nhật
                    $hashedPassword = password_hash($matKhauMoi, PASSWORD_DEFAULT);
                    $stmt = $this->db->prepare("UPDATE NGUOI_DUNG SET MatKhau = ? WHERE MaNguoiDung = ?");
                    $stmt->execute([$hashedPassword, $adminId]);

                    // Xóa flag yêu cầu đổi mật khẩu
                    $_SESSION['require_password_change'] = false;

                    $this->setFlash('success', 'Đổi mật khẩu thành công!');
                    $this->redirect('');
                    return;
                }
            }
        }

        $this->viewWithoutLayout('auth/change-password', [
            'error' => $error,
            'success' => $success,
            'requireChange' => $requireChange
        ]);
    }

    public function logout()
    {
        // Xóa session
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_phone']);
        unset($_SESSION['require_password_change']);

        // Xóa cookie
        setcookie('AdminLoggedIn', '', time() - 3600, '/');

        $this->redirect('?controller=auth&action=login');
    }
}
