<?php
require_once 'models/HocPhiModel.php';

class HocPhiController {
    private $hocPhiModel;
    
    public function __construct() {
        $this->hocPhiModel = new HocPhiModel();
    }
    
    public function index() {
        $title = "Quản lý Học phí";
        $showSidebar = true;
        
        // Lấy danh sách học phí (tạm thời dùng mock data)
        $hocPhiList = $this->hocPhiModel->getAll();
        
        require_once 'views/layouts/header.php';
        require_once 'views/hocphi/danhsachhocphi.php';
        require_once 'views/layouts/footer.php';
    }
    
    public function donghocphi() {
        $title = "Đóng Học phí";
        $showSidebar = true;
        
        require_once 'views/layouts/header.php';
        require_once 'views/hocphi/donghocphi.php';
        require_once 'views/layouts/footer.php';
    }
}
?>