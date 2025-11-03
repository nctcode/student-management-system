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
            // Xử lý các action cần tham số
            switch ($controller) {
                case 'donchuyenloptruong':
                    switch ($action) {
                        case 'detail':
                        case 'pheduyetdon':
                        case 'cancel':
                            $maDon = $_GET['maDon'] ?? '';
                            $controllerInstance->$action($maDon);
                            break;
                        default:
                            $controllerInstance->$action();
                    }
                    break;
                
                case 'home':
                    switch ($action) {
                        case 'admin':
                        case 'teacher':
                        case 'student':
                        case 'parent':
                        case 'principal':
                            $controllerInstance->$action();
                            break;
                        default:
                            $controllerInstance->$action();
                    }
                    break;

                case 'tuyensinh':
                    switch ($action) {
                        case 'dangkyhoso':
                            $controllerInstance->dangkyhoso();
                            break;
                        case 'danhsachhoso':
                            $controllerInstance->danhsachhoso();
                            break;
                        case 'hosocuatoi':
                            $controllerInstance->hosocuatoi();
                            break;
                        case 'xemhoso':
                            $maHoSo = $_GET['maHoSo'] ?? '';
                            $controllerInstance->xemhoso($maHoSo);
                            break;
                        case 'xemhoso_theohocsinh':
                            $maHocSinh = $_GET['maHocSinh'] ?? '';
                            $controllerInstance->xemhoso_theohocsinh($maHocSinh);
                            break;
                        case 'xulyhoso':
                            $maHoSo = $_GET['maHoSo'] ?? '';
                            $controllerInstance->xulyhoso($maHoSo);
                            break;
                        case 'chitiethoso':
                            $maHoSo = $_GET['maHoSo'] ?? '';
                            $controllerInstance->chitiethoso($maHoSo);
                            break;
                        case 'nhapdiem':
                            $maHoSo = $_GET['maHoSo'] ?? '';
                            $controllerInstance->nhapdiem($maHoSo);
                            break;
                        default:
                            $userRole = $_SESSION['user']['vaiTro'] ?? '';
                            if (in_array($userRole, ['QTV', 'BGH'])) {
                                $controllerInstance->danhsachhoso();
                            } else {
                                $controllerInstance->hosocuatoi();
                            }
                    }
                    break;

                case 'thoikhoabieu':
                    switch ($action) {
                        case 'quanlytkb':
                            $controllerInstance->quanlytkb();
                            break;
                        case 'taotkb':
                            $controllerInstance->taotkb();
                            break;
                        case 'xemluoi':
                            $controllerInstance->xemluoi();
                            break;
                        case 'xoatkb':
                            $controllerInstance->xoatkb();
                            break;
                        case 'getGiaoVienByMon':
                            $controllerInstance->getGiaoVienByMon();
                            break;
                        case 'luutiet':
                            $controllerInstance->luutiet();
                            break;
                        default:
                            $controllerInstance->xemluoi();
                    }
                    break;
                
                case 'tinnhan':
                    switch ($action) {
                        case 'guitinnhan':
                            $controllerInstance->guitinnhan();
                            break;
                        case 'chitiettinnhan':
                            $maHoiThoai = $_GET['maHoiThoai'] ?? '';
                            $controllerInstance->chitiettinnhan($maHoiThoai);
                            break;
                        case 'getHocSinhByLop':
                            $controllerInstance->getHocSinhByLop();
                            break;
                        case 'getPhuHuynhByLop':
                            $controllerInstance->getPhuHuynhByLop();
                            break;
                        default:
                            $controllerInstance->index();
                    }
                    break;
                
                case 'diem':
                    $controllerInstance->$action();
                    break;

                case 'chuyencan':
                    $controllerInstance->$action();
                    break; 
                
                case 'baitap':
                    switch ($action) {
                        case 'danhsach':
                            $controllerInstance->danhsach();
                            break;
                        case 'luu': 
                            $controllerInstance->luu();
                            break;
                        case 'chitiet':
                            $maBaiTap = $_GET['maBaiTap'] ?? 0;
                            $controllerInstance->chitiet($maBaiTap);
                            break;
                        default:
                            $controllerInstance->index();
                    }
                    break;

                default:
                    $controllerInstance->$action();
            }
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