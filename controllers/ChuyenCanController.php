<?php
require_once 'models/ChuyenCanModel.php';
require_once 'models/GiaoVienModel.php'; 

class ChuyenCanController {
    private $chuyenCanModel;
    private $giaoVienModel;
    private $maGiaoVien;

    public function __construct() {
        $this->chuyenCanModel = new ChuyenCanModel();
        $this->giaoVienModel = new GiaoVienModel(); 
    }

    // Kiểm tra quyền (GV) và lấy maGiaoVien
    private function checkAuthAndGetMaGV() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['vaiTro'] !== 'GIAOVIEN') {
            $_SESSION['error'] = "Bạn không có quyền truy cập!";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        $giaoVienInfo = $this->giaoVienModel->getGiaoVienByMaNguoiDung($_SESSION['user']['maNguoiDung']);
        if (!$giaoVienInfo) {
            $_SESSION['error'] = "Tài khoản của bạn không được liên kết với một hồ sơ giáo viên.";
            header('Location: index.php?controller=home&action=index'); 
            exit;
        }
        $this->maGiaoVien = $giaoVienInfo['maGiaoVien'];
    }

    // Hiển thị trang chọn Lớp, Môn
    public function index() {
        $this->checkAuthAndGetMaGV();
        
        // Lấy danh sách các tiết học (Buổi học) mà GV này dạy
        $danhSachTietHoc = $this->chuyenCanModel->getTietHocGiaoVien($this->maGiaoVien);
        
        $title = "Ghi nhận chuyên cần";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php'; 
        require_once 'views/chuyencan/diemdanh.php'; 
        require_once 'views/layouts/footer.php';
    }

    // HÀM MỚI: Xử lý AJAX để lấy bảng điểm danh
    public function ajaxGetBangDiemDanh() {
        $this->checkAuthAndGetMaGV();
        
        $maLop = $_GET['maLop'] ?? 0;
        $maTietHoc = $_GET['maTietHoc'] ?? 0;
        $ngayDiemDanh = $_GET['ngayDiemDanh'] ?? date('Y-m-d'); 

        if (!$maLop || !$maTietHoc) {
            echo json_encode(['error' => 'Vui lòng chọn lớp và tiết học hợp lệ.']);
            exit;
        }

        // Lấy danh sách HS và trạng thái chuyên cần (nếu đã có)
        $danhSachHocSinh = $this->chuyenCanModel->getDanhSachLopDeDiemDanh($maLop, $maTietHoc, $ngayDiemDanh);
        
        // Lấy thông tin tiết học để hiển thị
        $thongTinTietHoc = $this->chuyenCanModel->getThongTinTietHoc($maTietHoc);
        
        // Gộp kết quả
        $result = [
            'danhSachHocSinh' => $danhSachHocSinh,
            'thongTinTietHoc' => $thongTinTietHoc
        ];

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    // Xử lý lưu điểm danh
    public function luu() {
        $this->checkAuthAndGetMaGV();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maLop = $_POST['maLop'];
            $maTietHoc = $_POST['maTietHoc'];
            $ngayDiemDanh = $_POST['ngayDiemDanh'];
            $danhSachTrangThai = $_POST['trangthai']; 
            $danhSachGhiChu = $_POST['ghichu']; 

            if ($this->chuyenCanModel->luuChuyenCan($maTietHoc, $ngayDiemDanh, $danhSachTrangThai, $danhSachGhiChu)) {
                $_SESSION['success'] = "Lưu chuyên cần thành công!";
            } else {
                $_SESSION['error'] = "Không thể lưu chuyên cần. Đã có lỗi xảy ra.";
            }

            $redirectUrl = "index.php?controller=chuyencan&action=index&maLop=$maLop&maTietHoc=$maTietHoc&ngayDiemDanh=$ngayDiemDanh&autoload=true";
            header("Location: " . $redirectUrl);
            exit;
        }
    }
}
?>