<?php
require_once __DIR__ . '/../models/DonChuyenLopTruongModel.php';

class DonChuyenLopTruongController {
    protected $model;

    public function __construct() {
        $this->model = new DonChuyenLopTruongModel();
        
        // ĐẢM BẢO SESSION ĐƯỢC KHỞI TẠO
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // DEBUG: Kiểm tra session trong constructor
        error_log("DEBUG DonChuyenLopTruongController - Session: " . print_r($_SESSION, true));
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        
        if (!in_array($userRole, ['QTV', 'BGH', 'GIAOVIEN'])) {
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        // KIỂM TRA KỸ HƠN: Nếu là BGH mà không có maTruong
        if ($userRole === 'BGH') {
            if (!isset($_SESSION['user']['maTruong']) || empty($_SESSION['user']['maTruong'])) {
                error_log("DEBUG: BGH user missing maTruong in session");
                $_SESSION['error'] = "Không tìm thấy mã trường trong phiên đăng nhập. Vui lòng đăng nhập lại.";
                header('Location: index.php?controller=auth&action=login');
                exit;
            } else {
                error_log("DEBUG: BGH user maTruong = " . $_SESSION['user']['maTruong']);
            }
        }

        // Đảm bảo BGH không bị lỗi truy cập trường khác
        if ($userRole === 'BGH' && isset($_GET['school']) && is_numeric($_GET['school'])) {
            $maTruongTam = intval($_GET['school']);
            if ($maTruongTam !== ($_SESSION['user']['maTruong'] ?? 0)) {
                // BGH chỉ được xem trường của mình, chuyển hướng nếu cố tình xem trường khác
                header('Location: index.php?controller=home&action=principal&error=unauthorized_school');
                exit;
            }
        }
    }

    public function index() {
        header('Location: index.php?controller=donchuyenloptruong&action=danhsach');
        exit;
    }

    public function danhsach() {
        $this->checkPermission(['QTV', 'BGH', 'GIAOVIEN']);

        // DEBUG: Kiểm tra session trong danhsach
        error_log("DEBUG danhsach - Session user: " . print_r($_SESSION['user'] ?? 'NO SESSION', true));

        $maTruong = $this->getMaTruongFilter();
        $loaiDon = $_GET['loaiDon'] ?? 'tat_ca';
        $search = $_GET['search'] ?? '';

        // Giữ lại tham số school trong URL (cần thiết cho Model và View)
        $selectedSchool = $this->getCurrentSchoolId();
        
        $requests = $this->model->getAll($search, $maTruong, $loaiDon);
        $schools = $this->model->getAllSchools();
        
        $currentSchoolId = $this->getCurrentSchoolId();
        $currentSchoolName = $this->getSchoolName($schools, $currentSchoolId);

        $title = "Danh sách Đơn chuyển lớp/trường";
        $showSidebar = true;
        
        // KHẮC PHỤC LỖI: LOGIC TÌM TỆP SIDEBAR CHÍNH XÁC (từ bgh.php -> bangiamhieu.php)
        $roleName = strtolower($_SESSION['user']['vaiTro']);
        $sidebarPath = 'views/layouts/sidebar/';
        
        if ($roleName === 'bgh') {
             $sidebarPath .= 'bangiamhieu.php'; 
        } else {
             $sidebarPath .= $roleName . '.php';
        }

        require_once 'views/layouts/header.php';
        require_once $sidebarPath; 
        require_once 'views/donchuyenloptruong/danhsachdon.php'; 
        require_once 'views/layouts/footer.php';
    }

    // Lọc Mã trường dựa trên vai trò
    private function getMaTruongFilter() {
        $role = $_SESSION['user']['vaiTro'] ?? '';
        $maTruongUser = $_SESSION['user']['maTruong'] ?? null;
        
        error_log("DEBUG getMaTruongFilter - Role: $role, maTruongUser: " . ($maTruongUser ?? 'NULL'));
        
        if ($role === 'BGH') {
            return $maTruongUser;
        } elseif ($role === 'QTV') {
            return isset($_GET['school']) && is_numeric($_GET['school']) ? intval($_GET['school']) : null;
        }
        
        return null;
    }
    
    // Lấy ID trường hiện tại để highlight trên bộ lọc
    private function getCurrentSchoolId() {
        $role = $_SESSION['user']['vaiTro'] ?? '';
        
        error_log("DEBUG getCurrentSchoolId - Role: $role");
        
        if ($role === 'BGH') {
            $maTruong = $_SESSION['user']['maTruong'] ?? null;
            error_log("DEBUG getCurrentSchoolId - BGH maTruong: " . ($maTruong ?? 'NULL'));
            return $maTruong;
        }
        
        if ($role === 'QTV' && isset($_GET['school']) && is_numeric($_GET['school'])) {
            return intval($_GET['school']);
        }
        
        return null;
    }

    private function getSchoolName($schools, $id) {
        if ($id === null) return "Tất cả các trường";
        foreach ($schools as $school) {
            if ($school['maTruong'] == $id) {
                return $school['tenTruong'];
            }
        }
        return "Tất cả các trường";
    }

    private function checkPermission($allowedRoles) {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['vaiTro'], $allowedRoles)) {
            header('Location: index.php?controller=home&action=index');
            exit;
        }
    }
    
    public function approve() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=donchuyenloptruong&action=danhsach'); 
            exit;
        }

        $maDon = intval($_POST['maDon'] ?? 0);
        $side  = $_POST['side'] ?? ''; 
        $maTruong = $this->getCurrentSchoolId(); // Lấy mã trường đang duyệt

        if ($maDon <= 0 || !$side || !$maTruong) {
             $_SESSION['error'] = "Lỗi: Dữ liệu không hợp lệ hoặc không xác định được trường.";
            header('Location: ?controller=donchuyenloptruong&action=danhsach'); 
            exit;
        }

        if ($this->model->approve($maDon, $side)) {
            $_SESSION['success'] = "Duyệt đơn #$maDon thành công.";
        } else {
            $_SESSION['error'] = "Lỗi khi duyệt đơn #$maDon.";
        }
        
        $qs = $maTruong ? '&school=' . $maTruong : '';
        header("Location: ?controller=donchuyenloptruong&action=danhsach$qs");
        exit;
    }

    public function reject() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=donchuyenloptruong&action=danhsach'); 
            exit;
        }

        $maDon = intval($_POST['maDon'] ?? 0);
        $side = $_POST['side'] ?? ''; 
        $reason = trim($_POST['reason'] ?? '');
        $maTruong = $this->getCurrentSchoolId(); // Lấy mã trường đang duyệt

        if ($maDon <= 0 || !$side || $reason === '' || !$maTruong) {
             $_SESSION['error'] = "Lỗi: Dữ liệu không hợp lệ, lý do từ chối là bắt buộc, hoặc không xác định được trường.";
            header("Location: ?controller=donchuyenloptruong&action=danhsach"); 
            exit;
        }

        if ($this->model->reject($maDon, $side, $reason)) {
            $_SESSION['success'] = "Từ chối đơn #$maDon thành công.";
        } else {
             $_SESSION['error'] = "Lỗi khi từ chối đơn #$maDon.";
        }
        
        $qs = $maTruong ? '&school=' . $maTruong : '';
        header("Location: ?controller=donchuyenloptruong&action=danhsach$qs");
        exit;
    }

    public function ajax_chitiet() {
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        
        header('Content-Type: application/json');

        try {
            $id = intval($_GET['id'] ?? 0);
            
            if ($id <= 0) { 
                echo json_encode(['error' => 'ID không hợp lệ']); 
                exit; 
            }
            
            $don = $this->model->getById($id);
            
            if (!$don) { 
                echo json_encode(['error' => 'Không tìm thấy đơn']); 
                exit; 
            }
            
            $don['loaiDon'] = $don['loaiDon'] ?? 'chuyen_truong';
            
            echo json_encode($don);
            exit; 
            
        } catch (\PDOException $e) {
            echo json_encode(['error' => 'Lỗi CSDL (PDO): ' . $e->getMessage()]);
            exit;
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Lỗi máy chủ: ' . $e->getMessage()]);
            exit;
        }
    }
}