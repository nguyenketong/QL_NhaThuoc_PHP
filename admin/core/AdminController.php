<?php
/**
 * Base Admin Controller
 */
class AdminController
{
    protected $db;
    protected $data = [];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->data['title'] = 'Admin Panel';
        $this->data['adminName'] = $_SESSION['admin_name'] ?? 'Admin';
        $this->data['adminPhone'] = $_SESSION['admin_phone'] ?? '';
        
        // Đếm đơn hàng chờ xử lý
        $this->data['soDonChoXuLy'] = $this->countPendingOrders();
        
        // Kiểm tra yêu cầu đổi mật khẩu (trừ trang đổi mật khẩu và logout)
        $this->checkPasswordChangeRequired();
    }

    protected function checkPasswordChangeRequired()
    {
        // Bỏ qua nếu đang ở trang auth
        $controller = $_GET['controller'] ?? '';
        $action = $_GET['action'] ?? '';
        
        if ($controller === 'auth') {
            return; // Không kiểm tra khi đang ở trang auth
        }
        
        // Nếu cần đổi mật khẩu thì redirect
        if (isset($_SESSION['require_password_change']) && $_SESSION['require_password_change'] === true) {
            header('Location: ' . BASE_URL . '/admin/?controller=auth&action=changePassword');
            exit;
        }
    }

    protected function countPendingOrders()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM DON_HANG WHERE TrangThai = 'Cho xu ly'");
        return $stmt->fetchColumn();
    }

    protected function view($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        extract($data);
        
        ob_start();
        $viewFile = ADMIN_ROOT . '/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
        $content = ob_get_clean();
        
        require ADMIN_ROOT . '/views/layouts/admin-layout.php';
    }

    protected function viewWithoutLayout($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        extract($data);
        require ADMIN_ROOT . '/views/' . $view . '.php';
    }

    protected function redirect($url)
    {
        header('Location: ' . BASE_URL . '/admin/' . $url);
        exit;
    }

    protected function redirectFull($url)
    {
        header('Location: ' . $url);
        exit;
    }

    protected function json($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Upload hình ảnh từ file
     */
    protected function uploadImage($file, $folder = 'images')
    {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return null;
        }

        $uploadDir = ROOT . '/assets/' . $folder . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Trả về đường dẫn đầy đủ với BASE_URL
            return BASE_URL . '/assets/' . $folder . '/' . $fileName;
        }
        return null;
    }

    /**
     * Xử lý hình ảnh - hỗ trợ cả upload file và paste URL
     * @param array|null $file - File upload từ $_FILES
     * @param string|null $imageUrl - URL hình ảnh (paste link)
     * @param string|null $currentImage - Hình ảnh hiện tại (khi edit)
     * @param string $folder - Thư mục lưu
     * @return string|null - Đường dẫn hình ảnh
     */
    protected function processImage($file, $imageUrl = null, $currentImage = null, $folder = 'images')
    {
        // Ưu tiên 1: Upload file mới
        if (isset($file['tmp_name']) && !empty($file['tmp_name'])) {
            return $this->uploadImage($file, $folder);
        }
        
        // Ưu tiên 2: Paste URL mới
        if (!empty($imageUrl) && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }
        
        // Ưu tiên 3: Giữ hình cũ
        return $currentImage;
    }
}
