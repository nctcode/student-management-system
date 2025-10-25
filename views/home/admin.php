<?php
$user = $_SESSION['user'];
$roleName = 'Quản trị viên';
// $stats đã được truyền từ controller
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
                                        <h4><?php echo number_format($stats['total_students']); ?></h4>
                                        <p>Học sinh</p>
                                    </div>
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo number_format($stats['total_teachers']); ?></h4>
                                        <p>Giáo viên</p>
                                    </div>
                                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo number_format($stats['total_classes']); ?></h4>
                                        <p>Lớp học</p>
                                    </div>
                                    <i class="fas fa-school fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo number_format($stats['pending_requests']); ?></h4>
                                        <p>Đơn chờ duyệt</p>
                                    </div>
                                    <i class="fas fa-clipboard-list fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ... phần còn lại giữ nguyên ... -->
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Quản lý hệ thống</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="index.php?controller=quanlynguoidung&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-users me-2"></i>Quản lý người dùng
                                    </a>
                                    <a href="index.php?controller=tuyensinh&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-user-graduate me-2"></i>Quản lý tuyển sinh
                                    </a>
                                    <a href="index.php?controller=hocphi&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-money-bill-wave me-2"></i>Quản lý học phí
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Thống kê nhanh</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="adminChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>