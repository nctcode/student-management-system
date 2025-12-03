<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/vendor/autoload.php';

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
                        case 'index':
                        case 'store':
                        case 'create':
                        case 'danhsachdoncuatoi':
                        case 'chitietdoncuatoi':
                        case 'danhsach':
                        case 'approve':
                        case 'reject':
                        case 'ajax_chitiet':
                        case 'ajax_getlop':
                            $controllerInstance->$action();
                            break;
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
                        case 'leader':
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
                
                // Trong phần case 'tinnhan' của index.php
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
                        case 'guitinnhangiaovien':
                            $controllerInstance->guitinnhangiaovien();
                            break;
                        case 'getAllGiaoVien': // THÊM DÒNG NÀY
                            $controllerInstance->getAllGiaoVien();
                            break;
                        case 'getGiaoVienByLop': // THÊM DÒNG NÀY
                            $controllerInstance->getGiaoVienByLop();
                            break;
                        default:
                            $controllerInstance->index();
                    }
                    break;
                
                case 'diem':
                    switch ($action) {
                        case 'xemdiem':
                            $controllerInstance->xemdiem();
                            break;
                        case 'nhapdiem':
                            $controllerInstance->nhapdiem();
                            break;
                        case 'taibangdiem':
                            $controllerInstance->taibangdiem();
                            break;
                        default:
                            $controllerInstance->$action();
                    }
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
                        case 'taiTatCaBaiNop':
                            $maBaiTap = $_GET['maBaiTap'] ?? 0;
                            $controllerInstance->taiTatCaBaiNop($maBaiTap);
                            break;
                        case 'danhsach_hs': 
                            $controllerInstance->danhsach_hs();
                            break;
                        case 'chitiet_hs':
                            $maBaiTap = $_GET['maBaiTap'] ?? 0;
                            $controllerInstance->chitiet_hs($maBaiTap);
                            break;
                        case 'nopbai':
                            $controllerInstance->nopbai();
                            break;
                        case 'xoaFileNop':
                            $controllerInstance->xoaFileNop();
                            break;
                        default:
                            $controllerInstance->index();
                    }
                    break;
                
                case 'thongbao':
                    switch ($action) {
                        case 'dangthongbao':
                        case 'xulydangthongbao':
                        case 'danhsach':
                        case 'chitiet':
                        case 'xoa':
                        case 'loadNotifications':
                        case 'markAsRead':
                            $controllerInstance->$action();
                            break;
                        default:
                            $controllerInstance->danhsach();
                    }
                    break;
                
                case 'ketquahoctap':
                    switch ($action) {
                        case 'index':
                        case 'view':
                            $controllerInstance->$action();
                        case 'thongke':
                            $controllerInstance->thongke();
                            break;
                        case 'xuatExcel': 
                            $controllerInstance->xuatExcel();
                            break;
                        default:
                            $controllerInstance->thongke();
                    }
                    break;
                
                case 'dangkybanhoc':
                    switch ($action) {
                        case 'index':
                            $controllerInstance->index();
                            break;
                        case 'store':
                            $controllerInstance->store();
                            break;
                        case 'success':
                            $controllerInstance->success();
                            break;
                        default:
                            $controllerInstance->index();
                    }
                    break;    
                
                case 'quanlytaikhoan':
                    switch ($action) {
                        case 'index':
                        case 'create':
                        case 'store':
                        case 'edit':
                        case 'update':
                        case 'delete':
                            $controllerInstance->$action();
                            break;
                        default:
                            $controllerInstance->index();
                    }
                    break;
                
                case 'phancongrade':
                    switch ($action) {
                        case 'index':
                        case 'view':
                        case 'create':
                        case 'getGiaoVienByMonHoc':
                        case 'store':
                        case 'edit':
                        case 'update':
                        case 'delete':
                            $controllerInstance->$action();
                            break;
                        default:
                            $controllerInstance->index();
                    }
                
                case 'dethi':
                    switch ($action) {
                        case 'index':
                        case 'store':
                        case 'capNhatTrangThai':
                            $controllerInstance->$action();
                        case 'duyet':
                        case 'lichSuDuyetDeThi':
                            $controllerInstance->$action();
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