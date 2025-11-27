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
        
        // 2. Kiểm tra vai trò (Chỉ HOCSINH)
        if ($_SESSION['user']['vaiTro'] != 'HOCSINH') {
            $_SESSION['error'] = "Chỉ học sinh mới được đăng ký ban học!";
            header('Location: index.php?controller=home&action=student');
            exit;
        }
        
        // 3. Kiểm tra khối (Chỉ khối 11)
        // Sử dụng ?? 0 để tránh lỗi nếu session không có key 'khoi'
        $khoi = $_SESSION['user']['khoi'] ?? 0;
        if ($khoi != 11) {
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
        
        // Kiểm tra xem học sinh này đã đăng ký chưa
        $daDangKy = $this->model->kiemTraDaDangKy($maHocSinh);
        $thongTinDangKy = null;
        
        // Nếu đã đăng ký, lấy thông tin chi tiết để hiển thị
        if ($daDangKy) {
            $thongTinDangKy = $this->model->getThongTinDangKy($maHocSinh);
        }
        
        // Các biến giao diện
        $title = "Đăng ký Ban học";
        $showSidebar = true; // Biến này để header hiển thị sidebar
        
        require_once 'views/dangkybanhoc/index.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maBan = $_POST['ma_ban'] ?? '';
            $maHocSinh = $_SESSION['user']['maHocSinh'];
            
            // --- VALIDATION SERVER-SIDE ---
            
            // 1. Validate dữ liệu đầu vào
            if (empty($maBan)) {
                $_SESSION['error'] = "Vui lòng chọn một ban học!";
                header('Location: index.php?controller=dangkybanhoc&action=index');
                exit;
            }

            // 2. Kiểm tra học sinh đã đăng ký chưa (Ngăn chặn đăng ký trùng lặp)
            if ($this->model->kiemTraDaDangKy($maHocSinh)) {
                $_SESSION['error'] = "Bạn đã đăng ký ban học rồi và không thể thay đổi.";
                header('Location: index.php?controller=dangkybanhoc&action=index');
                exit;
            }

            // --- THỰC HIỆN ĐĂNG KÝ ---
            // Logic trừ chỉ tiêu đã được xử lý Atomic trong Model
            $result = $this->model->dangKyBanHoc($maHocSinh, $maBan);
            
            if ($result === true) {
                $_SESSION['success'] = "Đăng ký ban học thành công!";
                // QUAN TRỌNG: Chuyển hướng sang trang success
                header('Location: index.php?controller=dangkybanhoc&action=success'); 
                exit;
            } else {
                // Trường hợp thất bại (Lỗi DB hoặc Hết chỉ tiêu)
                $_SESSION['error'] = "Đăng ký thất bại! Có thể ban học đã hết chỉ tiêu hoặc có lỗi hệ thống.";
                header('Location: index.php?controller=dangkybanhoc&action=index');
                exit;
            }
        } else {
            // Nếu không phải POST request
            header('Location: index.php?controller=dangkybanhoc&action=index');
            exit;
        }
    }

    // Hàm hiển thị trang thành công
    public function success() {
        // Kiểm tra nếu không có session success thì đá về trang chủ (tránh truy cập trực tiếp)
        if (!isset($_SESSION['success'])) {
             header('Location: index.php?controller=dangkybanhoc&action=index');
             exit;
        }

        $title = "Đăng ký thành công";
        $showSidebar = true;
        
        require_once 'views/dangkybanhoc/success.php';
    }
}
?>