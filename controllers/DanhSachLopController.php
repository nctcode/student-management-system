<?php
require_once 'models/DanhSachLopModel.php';
require_once 'models/GiaoVienModel.php';
require_once 'models/Database.php';

class DanhSachLopController {
    private $model;
    private $giaoVienModel;

    public function __construct() {
        $this->model = new DanhSachLopModel();
        $this->giaoVienModel = new GiaoVienModel();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->checkPermission(['GIAOVIEN']);
    }

    private function checkPermission($allowedRoles) {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['vaiTro'], $allowedRoles)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
    }

    /**
     * Basic Flow 1-2: Hiển thị danh sách lớp
     */
    public function index() {
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $maGiaoVien = $this->giaoVienModel->getMaGiaoVien($maNguoiDung);

        if (!$maGiaoVien) {
            $_SESSION['error'] = "Không tìm thấy thông tin giáo viên!";
            header('Location: index.php?controller=home&action=teacher');
            exit;
        }

        try {
            $danhSachLop = $this->model->getLopCuaGiaoVien($maGiaoVien);
        } catch (Exception $e) {
            // Exception Flow 2.1: Lỗi hệ thống
            $_SESSION['error'] = "Lỗi hệ thống khi tải danh sách lớp!";
            $danhSachLop = [];
        }

        $title = "Danh Sách Lớp";
        $showSidebar = true;
        $roleName = strtolower($_SESSION['user']['vaiTro']);
        $sidebarPath = 'views/layouts/sidebar/' . $roleName . '.php';

        // Truyền model vào view để có thể gọi getSoHocSinh từ view
        $model = $this->model; 
        
        require_once 'views/layouts/header.php';
        require_once $sidebarPath;
        require_once 'views/danhsachlop/index.php';
        require_once 'views/layouts/footer.php';
    }

    /**
     * Basic Flow 3-4: Hiển thị danh sách học sinh trong lớp
     */
    public function danhSachHocSinh() {
        $maLop = intval($_GET['maLop'] ?? 0);
        
        if ($maLop <= 0) {
            $_SESSION['error'] = "Lớp không hợp lệ!";
            header('Location: index.php?controller=danhsachlop&action=index');
            exit;
        }

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $maGiaoVien = $this->giaoVienModel->getMaGiaoVien($maNguoiDung);

        // Kiểm tra quyền truy cập
        if (!$this->model->checkGiaoVienCoQuyenXemLop($maGiaoVien, $maLop)) {
            $_SESSION['error'] = "Bạn không có quyền xem lớp này!";
            header('Location: index.php?controller=danhsachlop&action=index');
            exit;
        }

        try {
            // Lấy thông tin lớp để hiển thị
            $lop = $this->model->getThongTinLop($maLop);
            
            // Kiểm tra xem giáo viên có phải là GVCN không
            $isGVCN = $this->model->checkGiaoVienLaGVCN($maGiaoVien, $maLop);
            
            // Lấy danh sách học sinh - nếu là GVBM thì chỉ lấy thông tin cơ bản
            if ($isGVCN) {
                $hocSinh = $this->model->getHocSinhByLop($maLop);
            } else {
                $hocSinh = $this->model->getHocSinhBasicByLop($maLop);
            }
            
            // Alternative Flow 4.1: Lớp chưa có học sinh
            if (empty($hocSinh)) {
                $_SESSION['info'] = "Danh sách lớp trống";
            }
        } catch (Exception $e) {
            // Exception Flow 4.2: Lỗi hệ thống
            $_SESSION['error'] = "Lỗi hệ thống khi tải danh sách học sinh!";
            $hocSinh = [];
        }

        $title = "Danh Sách Học Sinh - " . ($lop['tenLop'] ?? '');
        $showSidebar = true;
        $roleName = strtolower($_SESSION['user']['vaiTro']);
        $sidebarPath = 'views/layouts/sidebar/' . $roleName . '.php';

        require_once 'views/layouts/header.php';
        require_once $sidebarPath;
        require_once 'views/danhsachlop/danh_sach_hoc_sinh.php';
        require_once 'views/layouts/footer.php';
    }

    /**
     * Basic Flow 5-6: Hiển thị thông tin chi tiết học sinh
     */
    public function chiTietHocSinh() {
        $maHocSinh = intval($_GET['maHocSinh'] ?? 0);
        
        if ($maHocSinh <= 0) {
            $_SESSION['error'] = "Học sinh không hợp lệ!";
            header('Location: index.php?controller=danhsachlop&action=index');
            exit;
        }

        try {
            $maNguoiDung = $_SESSION['user']['maNguoiDung'];
            $maGiaoVien = $this->giaoVienModel->getMaGiaoVien($maNguoiDung);
            
            $hocSinh = $this->model->getChiTietHocSinh($maHocSinh);
            
            // DEBUG: Ghi log kết quả truy vấn
            error_log("DEBUG: Ket qua truy van hocSinh (maHS: $maHocSinh): " . print_r($hocSinh, true));
            
            if (!$hocSinh) {
                // Alternative Flow 6.1: Thông tin học sinh không đầy đủ
                $_SESSION['error'] = "Thông tin học sinh chưa đầy đủ, vui lòng cập nhật";
                header('Location: index.php?controller=danhsachlop&action=index');
                exit;
            }

            // Lấy maLop từ $hocSinh, kiểm tra nếu l.maLop là null (do LEFT JOIN) thì không cần kiểm tra quyền
            $maLopHienTai = $hocSinh['maLop'] ?? 0;
            
            // KHỞI TẠO MẶC ĐỊNH
            $isGVCN = false;
            
            if ($maLopHienTai > 0) {
                // Kiểm tra quyền truy cập
                if (!$this->model->checkGiaoVienCoQuyenXemLop($maGiaoVien, $maLopHienTai)) {
                    $_SESSION['error'] = "Bạn không có quyền xem thông tin học sinh này!";
                    header('Location: index.php?controller=danhsachlop&action=index');
                    exit;
                }

                // Kiểm tra xem giáo viên có phải là GVCN không
                $isGVCN = $this->model->checkGiaoVienLaGVCN($maGiaoVien, $maLopHienTai);
            }
            
            // Lấy thêm điểm số và chuyên cần - GVBM chỉ được xem điểm môn mình dạy
            if ($isGVCN) {
                $diemSo = $this->model->getDiemHocSinh($maHocSinh);
            } else {
                // GVBM chỉ xem được điểm môn mình dạy
                $diemSo = $this->model->getDiemHocSinhByMonGiaoVien($maHocSinh, $maGiaoVien);
            }
            
            $chuyenCan = $isGVCN ? $this->model->getChuyenCanHocSinh($maHocSinh) : [];

        } catch (Exception $e) {
            error_log("Lỗi hệ thống khi tải thông tin chi tiết học sinh: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi hệ thống khi tải thông tin học sinh!";
            header('Location: index.php?controller=danhsachlop&action=index');
            exit;
        }

        $title = "Thông Tin Học Sinh - " . $hocSinh['hoTen'];
        $showSidebar = true;
        $roleName = strtolower($_SESSION['user']['vaiTro']);
        $sidebarPath = 'views/layouts/sidebar/' . $roleName . '.php';

        require_once 'views/layouts/header.php';
        require_once $sidebarPath;
        require_once 'views/danhsachlop/chi_tiet_hoc_sinh.php';
        require_once 'views/layouts/footer.php';
    }
}
?>