<?php
$user = $_SESSION['user'];
$roleName = 'Tổ trưởng chuyên môn';
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

                <!-- Thống kê đề thi -->
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-warning text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['pending_exams'] ?? 0; ?></h4>
                                        <p>Đề thi chờ duyệt</p>
                                    </div>
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['approved_exams'] ?? 0; ?></h4>
                                        <p>Đề thi đã duyệt</p>
                                    </div>
                                    <i class="fas fa-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $stats['rejected_exams'] ?? 0; ?></h4>
                                        <p>Đề thi bị từ chối</p>
                                    </div>
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chức năng -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Chức năng Tổ trưởng</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="index.php?controller=duyetdethi&action=duyet" class="list-group-item list-group-item-action">
                                        <i class="fas fa-check-circle me-2"></i>Duyệt đề thi
                                    </a>
                                    <a href="index.php?controller=duyetdethi&action=lichSuDuyetDeThi" class="list-group-item list-group-item-action">
                                        <i class="fas fa-history me-2"></i>Lịch sử duyệt đề
                                    </a>
                                    <a href="index.php?controller=phancongdethi&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-user-edit"></i> Phân công giáo viên ra đề
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông báo nhanh -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Thông báo nhanh</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item">Chưa có thông báo mới</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
