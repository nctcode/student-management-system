<?php
class BanhocController {
    private $banHocModel;
    
    public function __construct() {
        require_once 'models/BanHocModel.php';
        $this->banHocModel = new BanHocModel();
    }
    
    public function dangkyban() {
        // SỬA Ở ĐÂY: dùng maNguoiDung thay vì maHocSinh
        $maHocSinh = $_SESSION['user']['maNguoiDung'] ?? '';
        
        if (!$maHocSinh) {
            $_SESSION['error'] = "Bạn cần đăng nhập với tư cách học sinh để đăng ký ban học";
            header('Location: index.php?controller=home&action=student');
            exit;
        }
        
        // Lấy danh sách các ban
        $danhSachBan = $this->banHocModel->getAllBanHoc();
        
        // Lọc các ban còn chỉ tiêu
        $danhSachBanConChiTieu = array_filter($danhSachBan, function($ban) {
            return $ban['soLuongDaDangKy'] < $ban['chiTieu'];
        });
        
        require_once 'views/banhoc/dangkyban.php';
    }
    
    public function xulydangkyban() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // SỬA Ở ĐÂY: dùng maNguoiDung
            $maHocSinh = $_POST['maHocSinh'] ?? '';
            $maBan = $_POST['maBan'] ?? '';
            
            // Validate dữ liệu
            if (empty($maBan)) {
                $_SESSION['error'] = "Vui lòng chọn ban học";
                header('Location: index.php?controller=banhoc&action=dangkyban');
                exit;
            }
            
            try {
                // Cập nhật số lượng đăng ký
                $result = $this->banHocModel->updateSoLuongDangKy($maBan);
                
                if ($result) {
                    $_SESSION['success'] = "Đăng ký ban học thành công";
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra khi đăng ký ban học. Vui lòng thử lại";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            }
            
            header('Location: index.php?controller=banhoc&action=dangkyban');
            exit;
        }
    }
    
    public function quanlydangky() {
        // Kiểm tra quyền
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if (!in_array($userRole, ['QTV', 'BGH'])) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này";
            header('Location: index.php?controller=home');
            exit;
        }
        
        $danhSachBan = $this->banHocModel->getAllBanHoc();
        require_once 'views/banhoc/quanlydangky.php';
    }
}
?>