<?php
$user = $_SESSION['user'];
$roleName = 'Học sinh';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>Chào mừng, <?php echo $user['hoTen']; ?>!</h1>
                        <p class="lead">Vai trò: <?php echo $roleName; ?></p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Đăng nhập lúc: <?php echo date('H:i d/m/Y'); ?></small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>8.5</h4>
                                        <p>Điểm TB</p>
                                    </div>
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>95%</h4>
                                        <p>Chuyên cần</p>
                                    </div>
                                    <i class="fas fa-calendar-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>3</h4>
                                        <p>Bài tập mới</p>
                                    </div>
                                    <i class="fas fa-tasks fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>2</h4>
                                        <p>Thông báo</p>
                                    </div>
                                    <i class="fas fa-bell fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- THÊM CARD ĐĂNG KÝ BAN HỌC VÀO ĐÂY -->
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-gradient-purple text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>🎓</h4>
                                        <p>Đăng ký ban học</p>
                                    </div>
                                    <i class="fas fa-graduation-cap fa-2x"></i>
                                </div>
                                <a href="index.php?controller=banhoc&action=dangkyban" class="btn btn-light btn-sm mt-2 w-100">
                                    Đăng ký ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Lịch học hôm nay</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Tiết 1-2: Toán
                                        <span class="badge bg-primary">P.101</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Tiết 3-4: Văn
                                        <span class="badge bg-success">P.102</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Tiết 5-6: Anh Văn
                                        <span class="badge bg-warning">P.103</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Chức năng học sinh</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="index.php?controller=thoikhoabieu&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-calendar-alt me-2"></i>Xem thời khóa biểu
                                    </a>
                                    <a href="index.php?controller=tinnhan&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-comments me-2"></i>Tin nhắn
                                    </a>
                                    <a href="index.php?controller=hocphi&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-money-bill-wave me-2"></i>Học phí
                                    </a>
                                   
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-clipboard-list me-2"></i>Xem điểm số
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-book me-2"></i>Bài tập
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông báo mới nhất -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Thông báo mới nhất</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Lịch thi học kỳ I</h6>
                                            <small>3 ngày trước</small>
                                        </div>
                                        <p class="mb-1">Thông báo lịch thi học kỳ I năm học 2024-2025</p>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Hoạt động ngoại khóa</h6>
                                            <small>1 tuần trước</small>
                                        </div>
                                        <p class="mb-1">Đăng ký tham gia câu lạc bộ thể thao</p>
                                    </a>
                                    
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
.bg-gradient-purple {
    background: linear-gradient(135deg, #6f42c1, #e83e8c);
}
</style>