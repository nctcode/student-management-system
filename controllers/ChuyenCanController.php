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

    // Hiển thị trang chọn Lớp, Buổi học
    public function index() {
        $this->checkAuthAndGetMaGV();
        
        $danhSachBuoiHoc = $this->chuyenCanModel->getBuoiHocGiaoVien($this->maGiaoVien);
        
        $title = "Ghi nhận chuyên cần";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php'; 
        require_once 'views/chuyencan/diemdanh.php'; 
        require_once 'views/layouts/footer.php';
    }

    // Xử lý AJAX để lấy bảng điểm danh
    public function ajaxGetBangDiemDanh() {
        $this->checkAuthAndGetMaGV();
        
        $maLop = $_GET['maLop'] ?? 0;
        $maBuoiHoc = $_GET['maBuoiHoc'] ?? 0;

        if (!$this->chuyenCanModel->kiemTraQuyenBuoiHoc($maBuoiHoc, $this->maGiaoVien)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Bạn không có quyền truy cập buổi học này.']);
            exit;
        }

        if (!$maLop || !$maBuoiHoc) {
            echo json_encode(['error' => 'Vui lòng chọn lớp và buổi học hợp lệ.']);
            exit;
        }

        $danhSachHocSinh = $this->chuyenCanModel->getDanhSachLopDeDiemDanh($maLop, $maBuoiHoc);
        $thongTinBuoiHoc = $this->chuyenCanModel->getThongTinBuoiHoc($maBuoiHoc);
        
        $result = [
            'danhSachHocSinh' => $danhSachHocSinh,
            'thongTinBuoiHoc' => $thongTinBuoiHoc
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
            $maBuoiHoc = $_POST['maBuoiHoc'];
            $ngayDiemDanh = $_POST['ngayDiemDanh'];
            $danhSachTrangThai = $_POST['trangthai'] ?? []; 
            $danhSachGhiChu = $_POST['ghichu'] ?? []; 

            if (!$this->chuyenCanModel->kiemTraQuyenBuoiHoc($maBuoiHoc, $this->maGiaoVien)) {
                $_SESSION['error'] = "Bạn không có quyền lưu chuyên cần cho buổi học này.";
                header('Location: index.php?controller=chuyencan&action=index');
                exit;
            }

            if ($this->chuyenCanModel->luuChuyenCan($maBuoiHoc, $danhSachTrangThai, $danhSachGhiChu)) {
                $_SESSION['success'] = "Lưu chuyên cần thành công!";
            } else {
                $_SESSION['error'] = "Không thể lưu chuyên cần. Đã có lỗi xảy ra.";
            }
            $redirectUrl = "index.php?controller=chuyencan&action=index&maLop=$maLop&maBuoiHoc=$maBuoiHoc&ngayDiemDanh=$ngayDiemDanh&autoload=true";
            header("Location: " . $redirectUrl);
            exit;
        }
    }

    // Chức năng tạo buổi học từ TKB (cho admin)
    public function taoBuoiHoc() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['vaiTro'] !== 'QTV') {
            $_SESSION['error'] = "Bạn không có quyền thực hiện chức năng này!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maNienKhoa = $_POST['maNienKhoa'];
            $ngayBatDau = $_POST['ngayBatDau'];
            $ngayKetThuc = $_POST['ngayKetThuc'];

            if ($this->chuyenCanModel->taoBuoiHocTuTKB($maNienKhoa, $ngayBatDau, $ngayKetThuc)) {
                $_SESSION['success'] = "Tạo buổi học từ TKB thành công!";
            } else {
                $_SESSION['error'] = "Không thể tạo buổi học. Đã có lỗi xảy ra.";
            }

            header("Location: index.php?controller=admin&action=thoikhoabieu");
            exit;
        }
    }
}
?>