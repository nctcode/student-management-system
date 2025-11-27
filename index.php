<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";            
$password = "";                 
$dbname = "qlhs"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8"); 

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Lỗi kết nối Database: " . $conn->connect_error);
}

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$publicControllers = ['auth'];

if (!in_array($controller, $publicControllers) && !isset($_SESSION['user'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

// Đường dẫn file Controller
$controllerName = ucfirst($controller) . "Controller";
$controllerFile = "controllers/" . $controllerName . ".php";

// Kiểm tra file tồn tại
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Kiểm tra class tồn tại
    if (class_exists($controllerName)) {
        
        $controllerInstance = new $controllerName($conn);
        
        // Kiểm tra action (hàm) tồn tại
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            // Xử lý lỗi Action không tồn tại
            echo "<h3 style='color:red; text-align:center;'>Lỗi: Action '$action' không tồn tại trong $controllerName!</h3>";
        }
    } else {
        echo "<h3 style='color:red; text-align:center;'>Lỗi: Class '$controllerName' không tìm thấy!</h3>";
    }
} else {

    $fallbackFile = 'controllers/HomeController.php';
    if (file_exists($fallbackFile)) {
        require_once $fallbackFile;
        if (class_exists('HomeController')) {
            $home = new HomeController($conn);
            $home->index();
        } else {
            echo "Lỗi: Không tìm thấy class HomeController.";
        }
    } else {
        echo "<h3 style='color:red; text-align:center;'>Lỗi 404: Trang bạn tìm không tồn tại (Controller: $controller).</h3>";
    }
}
?>