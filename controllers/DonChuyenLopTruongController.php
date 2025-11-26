<?php
require_once __DIR__ . '/../models/DonChuyenLopTruongModel.php';

class DonChuyenLopTruongController {
    protected $model;

    public function __construct() {
        $this->model = new DonChuyenLopTruongModel();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $currentAction = $_GET['action'] ?? '';
        
        error_log("DEBUG Constructor - User Role: $userRole, Action: $currentAction");
        
        // 🆕 CHỈ CHO PHÉP BGH VÀ PHUHUYNH
        if (!isset($_SESSION['user']) || !in_array($userRole, ['BGH', 'PHUHUYNH'])) {
            error_log("DEBUG Access denied - Invalid role: $userRole");
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này.";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        
        // 🆕 DANH SÁCH ACTION CHO PHÉP THEO ROLE
        // Trong constructor - sửa phần allowedActions
        $allowedActions = [
            'PHUHUYNH' => ['danhsachdoncuatoi', 'create', 'store', 'chitietdoncuatoi', 'ajaxGetLop'],
            'BGH' => ['index', 'danhsach', 'approve', 'reject', 'ajax_chitiet', 'ajaxGetLop']
        ];
        
        // KIỂM TRA ACTION CHO PHÉP
        if (!in_array($currentAction, $allowedActions[$userRole])) {
            error_log("DEBUG Access denied - $userRole cannot access: $currentAction");
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng: $currentAction";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        
        // 🆕 KIỂM TRA BGH CÓ MÃ TRƯỜNG KHÔNG
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
        
        // 🆕 KIỂM TRA PHUHUYNH CÓ MÃ PHỤ HUYNH KHÔNG
        if ($userRole === 'PHUHUYNH') {
            $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? null;
            if ($maNguoiDung && !isset($_SESSION['user']['maPhuHuynh'])) {
                // Tự động lấy maPhuHuynh nếu chưa có
                $maPhuHuynh = $this->model->getMaPhuHuynhByMaNguoiDung($maNguoiDung);
                if ($maPhuHuynh) {
                    $_SESSION['user']['maPhuHuynh'] = $maPhuHuynh;
                } else {
                    error_log("DEBUG: PHUHUYNH missing maPhuHuynh");
                    $_SESSION['error'] = "Không tìm thấy thông tin phụ huynh. Vui lòng liên hệ quản trị viên.";
                    header('Location: index.php?controller=home&action=index');
                    exit;
                }
            }
        }
    }
    public function index() {
    // Tự động chuyển hướng theo role
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        
        if ($userRole === 'PHUHUYNH') {
            header('Location: index.php?controller=donchuyenloptruong&action=danhsachdoncuatoi');
        } else if ($userRole === 'BGH') {
            header('Location: index.php?controller=donchuyenloptruong&action=danhsach');
        } else {
            header('Location: index.php?controller=home&action=index');
        }
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
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        
        if (!in_array($userRole, $allowedRoles)) {
            error_log("DEBUG checkPermission failed - User Role: $userRole, Allowed: " . implode(',', $allowedRoles));
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này.";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        
        return true;
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
    public function danhsachdoncuatoi() {
        $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? null;
        
        if (!$maNguoiDung) {
            $_SESSION['error'] = "Không tìm thấy thông tin người dùng.";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        
        // 🆕 TỰ ĐỘNG LẤY maPhuHuynh NẾU CHƯA CÓ TRONG SESSION
        if (!isset($_SESSION['user']['maPhuHuynh'])) {
            $maPhuHuynh = $this->model->getMaPhuHuynhByMaNguoiDung($maNguoiDung);
            if ($maPhuHuynh) {
                $_SESSION['user']['maPhuHuynh'] = $maPhuHuynh;
            } else {
                $_SESSION['error'] = "Không tìm thấy thông tin phụ huynh. Vui lòng liên hệ quản trị viên.";
                header('Location: index.php?controller=home&action=index');
                exit;
            }
        }
        
        $maPhuHuynh = $_SESSION['user']['maPhuHuynh'];
        
        $requests = $this->model->getByParentId($maPhuHuynh);
        $hocSinhList = $this->model->getStudentsByParent($maPhuHuynh);
        
        $title = "Đơn chuyển lớp/trường của tôi";
        $showSidebar = true;
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/phuhuynh.php';
        require_once 'views/donchuyenloptruong/danhsachdoncuatoi.php';
        require_once 'views/layouts/footer.php';
    }

    public function create() {
        $maPhuHuynh = $_SESSION['user']['maPhuHuynh'] ?? null;
        
        if (!$maPhuHuynh) {
            $_SESSION['error'] = "Không tìm thấy thông tin phụ huynh.";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        
        $hocSinhList = $this->model->getStudentsByParent($maPhuHuynh);
        $truongList = $this->model->getAllSchools();
        
        $title = "Tạo đơn chuyển lớp/trường";
        $showSidebar = true;
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/phuhuynh.php';
        require_once 'views/donchuyenloptruong/taodon.php';
        require_once 'views/layouts/footer.php';
    }


    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=donchuyenloptruong&action=create');
            exit;
        }
        
        $this->checkPermission(['PHUHUYNH']);
        
        $maPhuHuynh = $_SESSION['user']['maPhuHuynh'] ?? null;
        if (!$maPhuHuynh) {
            $_SESSION['error'] = "Không tìm thấy thông tin phụ huynh.";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        
        // Lấy dữ liệu từ form
        $maHocSinh = intval($_POST['maHocSinh'] ?? 0);
        $loaiDon = $_POST['loaiDon'] ?? '';
        $lyDoChuyen = trim($_POST['lyDoChuyen'] ?? '');
        
        // Kiểm tra dữ liệu bắt buộc
        if ($maHocSinh <= 0 || empty($loaiDon) || empty($lyDoChuyen)) {
            $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc.";
            header('Location: index.php?controller=donchuyenloptruong&action=create');
            exit;
        }
        
        // Kiểm tra học sinh thuộc về phụ huynh này
        $hocSinhList = $this->model->getStudentsByParent($maPhuHuynh);
        $isValidStudent = false;
        foreach ($hocSinhList as $hs) {
            if ($hs['maHocSinh'] == $maHocSinh) {
                $isValidStudent = true;
                $currentStudent = $hs;
                break;
            }
        }
        
        if (!$isValidStudent) {
            $_SESSION['error'] = "Học sinh không hợp lệ.";
            header('Location: index.php?controller=donchuyenloptruong&action=create');
            exit;
        }
        
        // Xử lý dữ liệu theo loại đơn
        $maTruongDen = null;
        $maLopDen = null;
        
        if ($loaiDon === 'chuyen_truong') {
            $maTruongDen = intval($_POST['maTruongDen'] ?? 0);
            if ($maTruongDen <= 0) {
                $_SESSION['error'] = "Vui lòng chọn trường chuyển đến.";
                header('Location: index.php?controller=donchuyenloptruong&action=create');
                exit;
            }
        } else if ($loaiDon === 'chuyen_lop') {
            $maLopDen = intval($_POST['maLopDen'] ?? 0);
            if ($maLopDen <= 0) {
                $_SESSION['error'] = "Vui lòng chọn lớp chuyển đến.";
                header('Location: index.php?controller=donchuyenloptruong&action=create');
                exit;
            }
        }
        
        // Tạo đơn
        if ($this->model->createDon($maHocSinh, $loaiDon, $lyDoChuyen, $maTruongDen, $maLopDen)) {
            $_SESSION['success'] = "Tạo đơn chuyển " . ($loaiDon === 'chuyen_lop' ? 'lớp' : 'trường') . " thành công!";
            header('Location: index.php?controller=donchuyenloptruong&action=danhsachdoncuatoi');
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi tạo đơn. Vui lòng thử lại.";
            header('Location: index.php?controller=donchuyenloptruong&action=create');
        }
        exit;
    }

    public function chitietdoncuatoi() {
        $this->checkPermission(['PHUHUYNH']);
        
        $maDon = intval($_GET['id'] ?? 0);
        $maPhuHuynh = $_SESSION['user']['maPhuHuynh'] ?? null;
        
        if ($maDon <= 0 || !$maPhuHuynh) {
            $_SESSION['error'] = "Thông tin không hợp lệ.";
            header('Location: index.php?controller=donchuyenloptruong&action=danhsachdoncuatoi');
            exit;
        }
        
        // Kiểm tra đơn thuộc về phụ huynh này
        $don = $this->model->getByIdAndParent($maDon, $maPhuHuynh);
        if (!$don) {
            $_SESSION['error'] = "Không tìm thấy đơn hoặc bạn không có quyền xem đơn này.";
            header('Location: index.php?controller=donchuyenloptruong&action=danhsachdoncuatoi');
            exit;
        }
        
        $title = "Chi tiết đơn chuyển lớp/trường";
        $showSidebar = true;
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/phuhuynh.php';
        require_once 'views/donchuyenloptruong/chitietdon.php';
        require_once 'views/layouts/footer.php';
    }
    public function ajaxGetLop() {
        // ĐẢM BẢO CHỈ TRẢ VỀ JSON
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            // KIỂM TRA PHƯƠNG THỨC REQUEST
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                throw new Exception('Phương thức không hợp lệ');
            }
            
            $maHocSinh = intval($_GET['maHocSinh'] ?? 0);
            
            if ($maHocSinh <= 0) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Mã học sinh không hợp lệ'
                ]);
                exit;
            }
            
            // Lấy thông tin học sinh
            $studentInfo = $this->model->getStudentInfo($maHocSinh);
            
            if (!$studentInfo) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Không tìm thấy thông tin học sinh'
                ]);
                exit;
            }
            
            $maTruong = $studentInfo['maTruong'] ?? null;
            
            // Lấy danh sách lớp
            $lopList = $this->model->getLopByTruong($maTruong);
            
            echo json_encode([
                'success' => true, 
                'lopList' => $lopList
            ]);
            
        } catch (Exception $e) {
            // TRẢ VỀ LỖI DẠNG JSON
            echo json_encode([
                'success' => false, 
                'message' => 'Lỗi server: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}