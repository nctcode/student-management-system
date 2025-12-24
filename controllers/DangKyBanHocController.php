<?php
// Tải Model và Database
require_once 'models/Database.php';
require_once 'models/DangKyBanHocModel.php';

class DangKyBanHocController {
    private $model;
    
    public function __construct() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // 1. Kiểm tra đăng nhập
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
    
    // 2. Kiểm tra vai trò
    if ($_SESSION['user']['vaiTro'] != 'HOCSINH') {
        $_SESSION['error'] = "Chỉ học sinh mới được đăng ký ban học!";
        header('Location: index.php?controller=home&action=student');
        exit;
    }
    
    // 3. Kiểm tra thông tin khối
    $userKhoi = $_SESSION['user']['khoi'] ?? null;
    
    if ($userKhoi === null) {
        // Nếu không có thông tin khối, thử lấy lại từ database
        error_log("WARNING: No khoi in session for student: " . ($_SESSION['user']['maHocSinh'] ?? 'unknown'));
        
        // Có thể thêm code để lấy lại thông tin khối từ DB ở đây
        // Tạm thời bỏ qua nếu không có
        $_SESSION['user']['khoi'] = 11; // Giả sử là khối 11
        $userKhoi = 11;
    }
    
    // 4. Kiểm tra khối 11 (chuyển đổi linh hoạt)
    $userKhoiStr = (string) $userKhoi;
    $userKhoiStr = trim($userKhoiStr);
    
    if ($userKhoiStr !== "11") {
        $_SESSION['error'] = "Chỉ học sinh khối 11 mới được đăng ký ban học cho lớp 12! Khối hiện tại: " . htmlspecialchars($userKhoiStr);
        header('Location: index.php?controller=home&action=student');
        exit;
    }
    
    // 5. Khởi tạo Database và Model
    $db = new Database();
    $conn = $db->getConnection();
    $this->model = new DangKyBanHocModel($conn);
    
    // 6. Kiểm tra thời hạn đăng ký (tạm bỏ nếu muốn test)
    // if (!$this->model->kiemTraThoiHanDangKy()) {
    //     $_SESSION['error'] = "Đã hết thời hạn đăng ký ban học!";
    //     header('Location: index.php?controller=home&action=student');
    //     exit;
    // }
}
    
    public function index() {
        $maHocSinh = $_SESSION['user']['maHocSinh'];
        
        // Lấy danh sách ban học còn chỉ tiêu
        $danhSachBan = $this->model->getDanhSachBanConChiTieu();
        $daDangKy = $this->model->kiemTraDaDangKy($maHocSinh);
        $thongTinDangKy = null;
        
        if ($daDangKy) {
            $thongTinDangKy = $this->model->getThongTinDangKy($maHocSinh);
        }
        
        $title = "Đăng ký Ban học";
        $showSidebar = true; 
        require_once 'views/dangkybanhoc/index.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maBan = $_POST['ma_ban'] ?? '';
            $maHocSinh = $_SESSION['user']['maHocSinh'];
            
            if (empty($maBan)) {
                $_SESSION['error'] = "Vui lòng chọn ban học!";
                header('Location: index.php?controller=dangkybanhoc&action=index');
                exit;
            }
            
            // Thực hiện đăng ký hoặc cập nhật
            $result = $this->model->dangKyBanHoc($maHocSinh, $maBan);
            
            if ($result === "created") {
                $_SESSION['success'] = "Đăng ký ban học thành công!";
            } elseif ($result === "updated") {
                $_SESSION['success'] = "Cập nhật ban học thành công!";
            } elseif ($result === "same") {
                $_SESSION['info'] = "Bạn đã đăng ký ban học này rồi!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra trong quá trình xử lý, vui lòng thử lại!";
            }
            
            header('Location: index.php?controller=dangkybanhoc&action=index'); 
            exit;
        } else {
            header('Location: index.php?controller=dangkybanhoc&action=index');
            exit;
        }
    }
}
?>