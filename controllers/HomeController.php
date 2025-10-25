<?php
class HomeController {
    
    public function index() {
        $title = "Trang Chủ - QLHS";
        $showSidebar = true;
        
        require_once 'views/layouts/header.php';
        ?>
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="dashboard-card">
                        <h1>Chào mừng đến với Hệ thống QLHS</h1>
                        <p class="lead">Hệ thống đang trong quá trình phát triển</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-money-bill-wave fa-3x text-primary mb-3"></i>
                                        <h5>Học phí</h5>
                                        <p class="text-muted">Quản lý và đóng học phí</p>
                                        <a href="index.php?controller=hocphi&action=index" class="btn btn-primary">Truy cập</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-comments fa-3x text-success mb-3"></i>
                                        <h5>Tin nhắn</h5>
                                        <p class="text-muted">Gửi và nhận tin nhắn</p>
                                        <a href="index.php?controller=tinnhan&action=index" class="btn btn-success">Truy cập</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-alt fa-3x text-warning mb-3"></i>
                                        <h5>Thời khóa biểu</h5>
                                        <p class="text-muted">Xem lịch học</p>
                                        <a href="index.php?controller=thoikhoabieu&action=index" class="btn btn-warning">Truy cập</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
        require_once 'views/layouts/footer.php';
    }
}
?>