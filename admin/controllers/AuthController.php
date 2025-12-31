<?php
/**
 * Auth Controller - Đăng nhập Admin
 */
class AuthController extends AdminController
{
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
            } elseif ($matKhau !== 'admin123') {
                // Mật khẩu mặc định: admin123
                $error = 'Mật khẩu không đúng!';
            } else {
                // Lưu session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['MaNguoiDung'];
                $_SESSION['admin_name'] = $admin['HoTen'] ?? $admin['SoDienThoai'];
                $_SESSION['admin_phone'] = $admin['SoDienThoai'];

                // Lưu cookie ghi nhớ (7 ngày)
                setcookie('AdminLoggedIn', 'true', time() + (7 * 24 * 60 * 60), '/', '', false, true);

                $this->setFlash('success', 'Đăng nhập Admin thành công!');
                $this->redirect('');
                return;
            }
        }

        $this->viewWithoutLayout('auth/login', ['error' => $error]);
    }

    public function logout()
    {
        // Xóa session
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_phone']);

        // Xóa cookie
        setcookie('AdminLoggedIn', '', time() - 3600, '/');

        $this->redirect('?controller=auth&action=login');
    }
}
