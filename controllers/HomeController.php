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
                            <!-- Học Phí -->
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-money-bill-wave fa-3x text-primary mb-3"></i>
                                        <h5>Học phí</h5>
                                        <p class="text-muted">Quản lý và đóng học phí</p>
                                        <a href="index.php?controller=hocphi&action=index" class="btn btn-primary btn-sm">Truy cập</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Tin Nhắn -->
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-comments fa-3x text-success mb-3"></i>
                                        <h5>Tin nhắn</h5>
                                        <p class="text-muted">Gửi và nhận tin nhắn</p>
                                        <a href="index.php?controller=tinnhan&action=index" class="btn btn-success btn-sm">Truy cập</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Thời Khóa Biểu -->
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-alt fa-3x text-warning mb-3"></i>
                                        <h5>Thời khóa biểu</h5>
                                        <p class="text-muted">Xem lịch học</p>
                                        <a href="index.php?controller=thoikhoabieu&action=index" class="btn btn-warning btn-sm">Truy cập</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Đơn Chuyển Lớp -->
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-exchange-alt fa-3x text-info mb-3"></i>
                                        <h5>Đơn chuyển lớp</h5>
                                        <p class="text-muted">Quản lý đơn chuyển lớp/trường</p>
                                        <a href="index.php?controller=donchuyenloptruong&action=index" class="btn btn-info btn-sm">Truy cập</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Phân Công Đề Thi -->
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-tasks fa-3x text-secondary mb-3"></i>
                                        <h5>Phân công đề thi</h5>
                                        <p class="text-muted">Phân công coi thi, chấm thi</p>
                                        <a href="index.php?controller=phancondethi&action=index" class="btn btn-secondary btn-sm">Truy cập</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Tuyển Sinh -->
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-graduate fa-3x text-dark mb-3"></i>
                                        <h5>Tuyển sinh</h5>
                                        <p class="text-muted">Quản lý hồ sơ tuyển sinh</p>
                                        <a href="index.php?controller=tuyensinh&action=index" class="btn btn-dark btn-sm">Truy cập</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Duyệt Đề Thi -->
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-check-circle fa-3x text-danger mb-3"></i>
                                        <h5>Duyệt đề thi</h5>
                                        <p class="text-muted">Duyệt và phê duyệt đề thi</p>
                                        <a href="index.php?controller=duyetdethi&action=index" class="btn btn-danger btn-sm">Truy cập</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Quản lý Người Dùng -->
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-3x text-purple mb-3"></i>
                                        <h5>Quản lý người dùng</h5>
                                        <p class="text-muted">Quản lý học sinh, giáo viên</p>
                                        <a href="index.php?controller=quanlynguoidung&action=index" class="btn btn-purple btn-sm">Truy cập</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thống kê nhanh -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h4>Thống kê hệ thống</h4>
                                <div class="row">
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number">1,250</div>
                                            <div class="stat-label">Học sinh</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number">85</div>
                                            <div class="stat-label">Giáo viên</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number">45</div>
                                            <div class="stat-label">Lớp học</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number">12</div>
                                            <div class="stat-label">Môn học</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number">5</div>
                                            <div class="stat-label">Đơn chờ duyệt</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 mb-3">
                                        <div class="stat-card">
                                            <div class="stat-number">8</div>
                                            <div class="stat-label">Tin nhắn mới</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
        .btn-purple {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }
        .btn-purple:hover {
            background-color: #5a2d91;
            border-color: #5a2d91;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #007bff;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 14px;
            color: #6c757d;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        </style>
        
        <?php
        require_once 'views/layouts/footer.php';
    }
}
?>