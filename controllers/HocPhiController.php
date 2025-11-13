<?php
require_once 'models/HocPhiModel.php';

class HocPhiController {
    private $hocPhiModel;
    
    public function __construct() {
        $this->hocPhiModel = new HocPhiModel();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }
    
    public function index() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        
        if ($userRole !== 'PHUHUYNH' && $userRole !== 'HOCSINH') {
            $_SESSION['error'] = "Ban không có quyền truy cập chức năng này!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        
        $title = "Dashboard Học phí";
        $showSidebar = true;
        
        require_once 'views/layouts/header.php';
        if ($_SESSION['user']['vaiTro'] === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } else {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        }
        require_once 'views/hocphi/dashboard.php';
        require_once 'views/layouts/footer.php';
    }

    public function lichsu() {
        $title = "Lịch sử Thanh toán";
        $showSidebar = true;
        
        // Lấy lịch sử thanh toán
        $lichSuThanhToan = $this->hocPhiModel->getLichSuThanhToan();
        
        require_once 'views/layouts/header.php';
        if ($_SESSION['user']['vaiTro'] === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } else {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        }
        require_once 'views/hocphi/lichsuthanhtoan.php';
        require_once 'views/layouts/footer.php';
    }
    
    public function donghocphi() {
        $title = "Đóng Học phí";
        $showSidebar = true;
        
        // Lấy danh sách học phí cần đóng
        $hocPhiCanDong = $this->hocPhiModel->getHocPhiCanDong();
        
        require_once 'views/layouts/header.php';
        if ($_SESSION['user']['vaiTro'] === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } else {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        }
        require_once 'views/hocphi/donghocphi.php';
        require_once 'views/layouts/footer.php';
    }
    
    public function thanhtoan() {
        header('Content-Type: application/json');
        
        if ($_POST) {
            $maHocPhi = $_POST['maHocPhi'];
            $phuongThuc = $_POST['phuongThuc'];
            
            // Xử lý thanh toán
            $result = $this->hocPhiModel->xuLyThanhToan($maHocPhi, $phuongThuc);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'maGiaoDich' => $result,
                    'message' => 'Thanh toán thành công'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Thanh toán thất bại. Vui lòng thử lại.'
                ]);
            }
        }
    }

    public function thanhcong() {
        $title = "Thanh toán thành công";
        $showSidebar = true;
        
        $maGiaoDich = $_GET['maGiaoDich'] ?? '';
        
        // Lấy thông tin biên lai
        $bienLai = $this->hocPhiModel->getThongTinBienLai($maGiaoDich);
        
        require_once 'views/layouts/header.php';
        if ($_SESSION['user']['vaiTro'] === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } else {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        }
        require_once 'views/hocphi/thanhtoanthanhcong.php';
        require_once 'views/layouts/footer.php';
    }

    public function inphieu() {
        $title = "In phiếu thu";
        $showSidebar = true; // Ẩn sidebar khi in
        
        $maHocPhi = $_GET['maHocPhi'] ?? '';
        
        // Lấy thông tin phiếu thu
        $phieuThu = $this->hocPhiModel->taoPhieuThu($maHocPhi);
        
        require_once 'views/layouts/header.php';
        if ($_SESSION['user']['vaiTro'] === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } else {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        }
        require_once 'views/hocphi/inphieu.php';
        require_once 'views/layouts/footer.php';
    }

    public function bienlai() {
        $title = "Biên lai thanh toán";
        $showSidebar = true; // Ẩn sidebar khi in
        
        $maGiaoDich = $_GET['maGiaoDich'] ?? '';
        
        // Lấy thông tin biên lai
        $bienLai = $this->hocPhiModel->getThongTinBienLai($maGiaoDich);
        
        require_once 'views/layouts/header.php';
        if ($_SESSION['user']['vaiTro'] === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } else {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        }
        require_once 'views/hocphi/bienlai.php';
        require_once 'views/layouts/footer.php';
    }
}
?>