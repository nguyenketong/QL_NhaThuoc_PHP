<?php
/**
 * ADMIN PANEL - Entry Point
 * Nhà Thuốc Thanh Hoàn
 */

session_start();

// Định nghĩa ROOT cho admin
define('ADMIN_ROOT', __DIR__);
define('ROOT', dirname(__DIR__));

// Load config
require_once ROOT . '/config/config.php';
require_once ROOT . '/config/database.php';

// Load core admin
require_once ADMIN_ROOT . '/core/AdminController.php';

// Routing đơn giản
$controller = $_GET['controller'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Kiểm tra đăng nhập (trừ trang login)
if ($controller !== 'auth') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ' . BASE_URL . '/admin/?controller=auth&action=login');
        exit;
    }
}

// Map controller
$controllerMap = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'thuoc' => 'ThuocController',
    'don-hang' => 'DonHangController',
    'nhom-thuoc' => 'NhomThuocController',
    'thuong-hieu' => 'ThuongHieuController',
    'nguoi-dung' => 'NguoiDungController',
    'bai-viet' => 'BaiVietController',
    'nuoc-san-xuat' => 'NuocSanXuatController',
    'thanh-phan' => 'ThanhPhanController',
    'tac-dung-phu' => 'TacDungPhuController',
    'doi-tuong' => 'DoiTuongController'
];

$controllerClass = $controllerMap[$controller] ?? 'DashboardController';
$controllerFile = ADMIN_ROOT . '/controllers/' . $controllerClass . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $ctrl = new $controllerClass();
    
    if (method_exists($ctrl, $action)) {
        $ctrl->$action($id);
    } else {
        $ctrl->index();
    }
} else {
    echo "Controller không tồn tại: " . $controllerClass;
}
