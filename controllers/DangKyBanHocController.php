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
        
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Kiểm tra vai trò và khối
        if ($_SESSION['user']['vaiTro'] != 'HOCSINH') {
            $_SESSION['error'] = "Chỉ học sinh mới được đăng ký ban học!";
            header('Location: index.php?controller=home&action=student');
            exit;
        }
        
        if (($_SESSION['user']['khoi'] ?? null) != 11) {
            $_SESSION['error'] = "Chỉ học sinh khối 11 mới được đăng ký ban học cho lớp 12!";
            header('Location: index.php?controller=home&action=student');
            exit;
        }
        
        // Khởi tạo Database và Model
        $db = new Database();
        $conn = $db->getConnection();
        $this->model = new DangKyBanHocModel($conn);
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
        // BIẾN QUAN TRỌNG: Đặt biến này để file header.php hiển thị sidebar
        $showSidebar = true; 
        require_once 'views/dangkybanhoc/index.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maBan = $_POST['ma_ban'] ?? '';
            $maHocSinh = $_SESSION['user']['maHocSinh'];
            
            // --- VALIDATION SERVER-SIDE ---
            
            // 1. Kiểm tra học sinh đã đăng ký chưa (Ngăn chặn đăng ký trùng lặp)
            if ($this->model->kiemTraDaDangKy($maHocSinh)) {
                $_SESSION['error'] = "Bạn đã đăng ký ban học rồi và không thể thay đổi.";
                header('Location: index.php?controller=dangkybanhoc&action=index');
                exit;
            }

            // 2. Việc kiểm tra chỉ tiêu đã được xử lý bằng logic Transaction/Atomic Update 
            //    trong Model (dangKyBanHoc). Không cần kiểm tra lặp lại ở đây.
            
            // --- THỰC HIỆN ĐĂNG KÝ ---
            
            $result = $this->model->dangKyBanHoc($maHocSinh, $maBan);
            
            if ($result === true) {
                $_SESSION['success'] = "Đăng ký ban học thành công!";
                header('Location: index.php?controller=dangkybanhoc&action=index'); 
                exit;
            } else {
                // Trường hợp thất bại bao gồm: lỗi DB, hoặc ban học hết chỉ tiêu ngay lúc đăng ký (race condition)
                $_SESSION['error'] = "Có lỗi xảy ra trong quá trình xử lý, vui lòng thử lại!";
                header('Location: index.php?controller=dangkybanhoc&action=index');
                exit;
            }
        } else {
            header('Location: index.php?controller=dangkybanhoc&action=index');
            exit;
        }
    }
}
?>