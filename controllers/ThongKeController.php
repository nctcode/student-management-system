<?php
require_once 'models/GiaoVienModel.php';
require_once 'models/ThongKeModel.php';
require_once 'models/Database.php';

class ThongKeController {
    private $giaoVienModel;
    private $thongKeModel;

    public function __construct() {
        $this->giaoVienModel = new GiaoVienModel();
        $this->thongKeModel = new ThongKeModel();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->checkPermission(['QTV', 'BGH']);
    }
    
    private function checkPermission($allowedRoles) {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['vaiTro'], $allowedRoles)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
    }

    /**
     * Basic Flow 1-2: Hiển thị trang Thống kê Báo cáo tổng quan (Trang chọn lọc)
     */
    public function index() {
        $title = "Thống Kê Báo Cáo";
        
        try {
            // Lấy maTruong từ session (giả sử user đã đăng nhập có trường)
            $maTruong = $_SESSION['user']['maTruong'] ?? null;
            
            // Chỉ số tổng quan (Cho Dashboard mini trên trang lọc) - THÊM maTruong
            $tongSoLop = $this->giaoVienModel->getTotalClasses($maTruong);
            $lopCoGVCN = $this->giaoVienModel->getClassesWithGVCN($maTruong);
            $tongSoGV = $this->giaoVienModel->getTotalTeachers($maTruong);
            
            // Dữ liệu cho bộ lọc - THÊM maTruong
            $danhSachKhoi = $this->giaoVienModel->getAllKhoi($maTruong);
            $danhSachLop = $this->giaoVienModel->getAllClasses($maTruong); 
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi hệ thống khi tải dữ liệu thống kê tổng quan!";
            $tongSoLop = $lopCoGVCN = $tongSoGV = 0;
            $danhSachKhoi = $danhSachLop = [];
        }

        $showSidebar = true;
        $roleName = strtolower($_SESSION['user']['vaiTro']);
        $sidebarPath = 'views/layouts/sidebar/' . ($roleName === 'bgh' ? 'bangiamhieu' : $roleName) . '.php';

        require_once 'views/layouts/header.php';
        require_once $sidebarPath;
        require_once 'views/thongke/index.php'; // Quay lại view index.php cho trang lọc
        require_once 'views/layouts/footer.php';
        exit();
    }

    /**
     * Basic Flow 3-6: Xử lý hiển thị báo cáo chi tiết theo loại (Theo Khối/Lớp)
     */
    public function chiTietBaoCao() {
        $loaiBaoCao = $_GET['loaiBaoCao'] ?? null;
        $maKhoi = $_GET['maKhoi'] ?? null;
        $maLop = $_GET['maLop'] ?? null;
        $hocKy = intval($_GET['hocKy'] ?? 1);   
        
        if (!$loaiBaoCao) {
            $_SESSION['error'] = "Vui lòng chọn loại báo cáo cần xem.";
            header('Location: index.php?controller=ThongKe&action=index');
            exit;
        }
        
        $title = "Báo Cáo Chi Tiết";
        $baoCaoData = [];
        $baoCaoTitle = "";

        try {
            // Lấy maTruong từ session
            $maTruong = $_SESSION['user']['maTruong'] ?? null;
            
            switch ($loaiBaoCao) {
                case 'phanCong':
                    // Sử dụng hàm chi tiết theo Khối/Lớp - THÊM maTruong
                    $baoCaoData = $this->thongKeModel->getThongKePhanCong($maKhoi, $maLop, $maTruong);
                    $baoCaoTitle = "Thống kê Phân công Giáo viên Bộ môn và Chủ nhiệm";
                    $viewFile = 'views/thongke/chi_tiet_bao_cao_phan_cong.php';
                    break;
                case 'hocLuc':
                    // Sử dụng hàm chi tiết theo Khối/Lớp - THÊM maTruong
                    $baoCaoData = $this->thongKeModel->getThongKeHocLuc($maKhoi, $maLop, $hocKy, $maTruong);
                    $baoCaoTitle = "Thống kê Xếp loại Học lực Học kỳ $hocKy";
                    $viewFile = 'views/thongke/chi_tiet_bao_cao_hoc_luc.php';
                    break;
                case 'chuyenCan':
                    // Sử dụng hàm chi tiết theo Khối/Lớp - THÊM maTruong
                    $baoCaoData = $this->thongKeModel->getThongKeChuyenCan($maKhoi, $maLop, $hocKy, $maTruong);
                    $baoCaoTitle = "Thống kê Chuyên cần Học kỳ $hocKy";
                    $viewFile = 'views/thongke/chi_tiet_bao_cao_chuyen_can.php';
                    break;
                default:
                    $_SESSION['error'] = "Loại báo cáo không hợp lệ.";
                    header('Location: index.php?controller=ThongKe&action=index');
                    exit;
            }
            
            if (empty($baoCaoData)) {
                // Alternative Flow 6.2: Không tìm thấy dữ liệu
                $_SESSION['warning'] = "Không tìm thấy dữ liệu báo cáo phù hợp với bộ lọc.";
            }

        } catch (Exception $e) {
            // Exception Flow 7.1: Lỗi hệ thống khi tải báo cáo
            error_log("Lỗi tạo báo cáo: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi hệ thống khi tạo báo cáo chi tiết!";
            header('Location: index.php?controller=ThongKe&action=index');
            exit;
        }

        $showSidebar = true;
        $roleName = strtolower($_SESSION['user']['vaiTro']);
        $sidebarPath = 'views/layouts/sidebar/' . ($roleName === 'bgh' ? 'bangiamhieu' : $roleName) . '.php';

        require_once 'views/layouts/header.php';
        require_once $sidebarPath;
        require_once $viewFile;
        require_once 'views/layouts/footer.php';
        exit();
    }
}
?>