<?php
// controllers/TinNhanController.php
class TinNhanController {
    
    public function index() {
        $title = "Tin Nhắn - QLHS";
        $showSidebar = true;
        
        require_once 'views/layouts/header.php';
        ?>
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="dashboard-card">
                        <h1>📨 Quản lý Tin Nhắn</h1>
                        <p class="lead">Chức năng đang được phát triển</p>
                        
                        <div class="alert alert-info">
                            <strong>Thông báo:</strong> Tính năng tin nhắn sẽ sớm được hoàn thiện!
                        </div>
                        
                        <a href="index.php?controller=home&action=index" class="btn btn-primary">
                            ← Quay lại Trang Chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
        require_once 'views/layouts/footer.php';
    }
}
?>