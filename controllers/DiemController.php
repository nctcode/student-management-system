<?php
require_once 'models/DiemModel.php';
require_once 'models/GiaoVienModel.php'; 

class DiemController {
    private $diemModel;
    private $giaoVienModel;

    public function __construct() {
        $this->diemModel = new DiemModel();
        $this->giaoVienModel = new GiaoVienModel(); 
    }

    private function checkAuth() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['vaiTro'] !== 'GIAOVIEN') {
            $_SESSION['error'] = "Bạn không có quyền truy cập!";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    // Hiển thị trang Nhập điểm (Trang duy nhất)
    public function index() {
        $this->checkAuth();
        
        $giaoVienInfo = $this->giaoVienModel->getGiaoVienByMaNguoiDung($_SESSION['user']['maNguoiDung']);

        // Kiểm tra an toàn
        if (!$giaoVienInfo || !isset($giaoVienInfo['maGiaoVien'])) {
            $_SESSION['error'] = "Tài khoản của bạn không được liên kết với một hồ sơ giáo viên.";
            header('Location: index.php?controller=home&action=index'); 
            exit;
        }

        $maGiaoVien = $giaoVienInfo['maGiaoVien'];
        
        $danhSachPhanCong = $this->diemModel->getLopVaMonHocGiaoVien($maGiaoVien);
        
        $title = "Nhập điểm";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php'; 
        require_once 'views/diem/nhapdiem.php'; 
        require_once 'views/layouts/footer.php';
    }

    // Xử lý AJAX để lấy bảng điểm
    public function ajaxGetBangDiem() {
        $this->checkAuth();
        
        $maLop = $_GET['maLop'] ?? 0;
        $maMonHoc = $_GET['maMonHoc'] ?? 0;
        $hocKy = $_GET['hocKy'] ?? 0;
        $namHoc = $_GET['namHoc'] ?? '';

        if (!$maLop || !$maMonHoc || !$hocKy || empty($namHoc)) {
            echo json_encode(['error' => 'Vui lòng chọn đầy đủ thông tin.']);
            exit;
        }

        $danhSachHocSinh = $this->diemModel->getDanhSachLopVaDiemHienTai($maLop, $maMonHoc, $hocKy, $namHoc);
        
        // Trả về dữ liệu dạng JSON
        header('Content-Type: application/json');
        echo json_encode($danhSachHocSinh);
        exit;
    }

    // Xử lý lưu điểm
    public function luu() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maLop = $_POST['maLop'];
            $maMonHoc = $_POST['maMonHoc'];
            $hocKy = $_POST['hocKy'];
            $namHoc = $_POST['namHoc'];
            $danhSachDiem = $_POST['diem']; 
            
            $maGiaoVien = $this->giaoVienModel->getGiaoVienByMaNguoiDung($_SESSION['user']['maNguoiDung'])['maGiaoVien'];

            foreach ($danhSachDiem as $maHS => $cacLoaiDiem) {
                foreach ($cacLoaiDiem as $loaiDiem => $diemSo) {
                    $diemSo = str_replace(',', '.', $diemSo); 
                    
                    $danhSachDiem[$maHS][$loaiDiem] = $diemSo;

                    if ($diemSo !== '' && $diemSo !== null) {
                        // Kiểm tra ký tự không hợp lệ hoặc ngoài phạm vi
                        if (!is_numeric($diemSo) || $diemSo < 0 || $diemSo > 10) {
                            $_SESSION['error'] = "Lỗi điểm không hợp lệ (mã HS: $maHS, loại: $loaiDiem). Vui lòng chỉ nhập số từ 0 đến 10.";
                            header("Location: index.php?controller=diem&action=index"); 
                            exit;
                        }
                    }
                }
            }

            if ($this->diemModel->luuBangDiem($maMonHoc, $maGiaoVien, $hocKy, $namHoc, $danhSachDiem)) {
                $_SESSION['success'] = "Lưu điểm thành công!";
            } else {
                // Lỗi CSDL
                $_SESSION['error'] = "Lỗi không thể lưu điểm. Đã có lỗi xảy ra.";
            }

            // Chuyển hướng về trang index VÀ mang theo các tham số
            $redirectUrl = sprintf(
                "Location: index.php?controller=diem&action=index&maLop=%s&maMonHoc=%s&hocKy=%s&namHoc=%s&autoload=true",
                urlencode($maLop),
                urlencode($maMonHoc),
                urlencode($hocKy),
                urlencode($namHoc)
            );
            header($redirectUrl);
            exit;
        }
    }
}
?>