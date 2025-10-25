<?php
$user = $_SESSION['user'];
$roleName = 'Phụ huynh';
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
                                        <h4>8.2</h4>
                                        <p>Điểm TB con</p>
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
                                        <h4>92%</h4>
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
                                        <h4>1.5M</h4>
                                        <p>Học phí tháng</p>
                                    </div>
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>3</h4>
                                        <p>Thông báo mới</p>
                                    </div>
                                    <i class="fas fa-bell fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Thông tin học tập của con</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-chart-bar me-2"></i>Xem điểm số chi tiết
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-calendar-alt me-2"></i>Xem chuyên cần
                                    </a>
                                    <a href="index.php?controller=hocphi&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-money-bill-wave me-2"></i>Học phí & thanh toán
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-clipboard-list me-2"></i>Kết quả học tập
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-book me-2"></i>Bài tập về nhà
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Liên hệ & Hỗ trợ</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="index.php?controller=tinnhan&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-comments me-2"></i>Nhắn tin cho giáo viên
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-phone me-2"></i>Liên hệ nhà trường
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-calendar me-2"></i>Lịch họp phụ huynh
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-file-alt me-2"></i>Đơn từ & Biểu mẫu
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông báo từ nhà trường -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Thông báo từ nhà trường</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Họp phụ huynh đầu năm</h6>
                                            <small>2 ngày trước</small>
                                        </div>
                                        <p class="mb-1">Thông báo lịch họp phụ huynh học kỳ I năm học 2024-2025</p>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Đóng học phí tháng 10</h6>
                                            <small>5 ngày trước</small>
                                        </div>
                                        <p class="mb-1">Hạn đóng học phí tháng 10 đến ngày 25/10/2024</p>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Hoạt động ngoại khóa</h6>
                                            <small>1 tuần trước</small>
                                        </div>
                                        <p class="mb-1">Đăng ký cho học sinh tham gia câu lạc bộ ngoại khóa</p>
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