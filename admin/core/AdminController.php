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
            return '/assets/' . $folder . '/' . $fileName;
        }
        return null;
    }
}
