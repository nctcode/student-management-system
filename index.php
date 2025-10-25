<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple router
$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Cho phép auth controller không cần đăng nhập
$publicControllers = ['auth'];

if (!in_array($controller, $publicControllers) && !isset($_SESSION['user'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Include controller
$controllerFile = "controllers/" . ucfirst($controller) . "Controller.php";
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    $controllerClass = ucfirst($controller) . "Controller";
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            die("Action không tồn tại: $action");
        }
    } else {
        die("Controller không tồn tại: $controllerClass");
    }
} else {
    // Fallback - hiển thị trang chủ
    require_once 'controllers/HomeController.php';
    $home = new HomeController();
    $home->index();
}
?>