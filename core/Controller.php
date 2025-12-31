<?php
/**
 * Base Controller - MVC Framework
 */
class Controller
{
    protected $db;
    protected $data = [];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->data['title'] = STORE_NAME;
    }

    protected function model($model)
    {
        $modelFile = ROOT . '/app/models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model($this->db);
        }
        return null;
    }

    protected function view($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        extract($data);
        
        ob_start();
        $viewFile = ROOT . '/app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
        $content = ob_get_clean();
        
        require ROOT . '/app/views/layouts/main.php';
    }

    protected function redirect($url)
    {
        header('Location: ' . BASE_URL . '/' . $url);
        exit;
    }

    protected function json($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function getUserId()
    {
        return $_SESSION['user_id'] ?? ($_COOKIE['UserId'] ?? null);
    }

    protected function isLoggedIn()
    {
        return $this->getUserId() !== null;
    }

    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('user/phoneLogin');
        }
    }

    protected function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }
}
