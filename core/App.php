<?php
/**
 * Core App - Router
 */
class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // Controller
        if (isset($url[0]) && !empty($url[0])) {
            // Chuyển đổi URL có dấu gạch ngang thành camelCase
            // gio-hang -> gioHang, nhom-thuoc -> nhomThuoc
            $controllerSlug = $this->convertToCamelCase($url[0]);
            $controllerName = ucfirst($controllerSlug) . 'Controller';
            $controllerFile = ROOT . '/app/controllers/' . $controllerName . '.php';
            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
            }
            unset($url[0]);
        }

        require_once ROOT . '/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Method
        if (isset($url[1]) && !empty($url[1])) {
            // Chuyển đổi method có dấu gạch ngang thành camelCase
            $methodName = $this->convertToCamelCase($url[1]);
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
            }
            unset($url[1]);
        }

        // Params
        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Chuyển đổi slug có dấu gạch ngang thành camelCase
     * gio-hang -> gioHang
     * danh-sach -> danhSach
     */
    protected function convertToCamelCase($slug)
    {
        $parts = explode('-', $slug);
        $result = lcfirst($parts[0]);
        for ($i = 1; $i < count($parts); $i++) {
            $result .= ucfirst($parts[$i]);
        }
        return $result;
    }

    protected function parseUrl()
    {
        // Hỗ trợ cả 2 kiểu URL:
        // 1. Clean URL: /gioHang hoặc /gio-hang (qua .htaccess)
        // 2. Query string: index.php?url=gioHang
        
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        
        // Fallback: parse từ REQUEST_URI nếu không có $_GET['url']
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $basePath = '/Ql_NhaThuoc/php/';
        
        if (strpos($uri, $basePath) !== false) {
            $path = substr($uri, strpos($uri, $basePath) + strlen($basePath));
            $path = strtok($path, '?'); // Bỏ query string
            if (!empty($path) && $path !== 'index.php') {
                return explode('/', filter_var(rtrim($path, '/'), FILTER_SANITIZE_URL));
            }
        }
        
        return [];
    }
}
