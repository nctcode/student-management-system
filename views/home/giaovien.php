<?php
$user = $_SESSION['user'];
$roleName = 'Giáo viên';
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
                    <div class="col-md-4 mb-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>35</h4>
                                        <p>Học sinh</p>
                                    </div>
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>12</h4>
                                        <p>Bài tập chưa chấm</p>
                                    </div>
                                    <i class="fas fa-tasks fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-warning text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>5</h4>
                                        <p>Tiết dạy hôm nay</p>
                                    </div>
                                    <i class="fas fa-chalkboard fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Chức năng giáo viên</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="index.php?controller=thoikhoabieu&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-calendar-alt me-2"></i>Thời khóa biểu
                                    </a>
                                    <a href="index.php?controller=diem&action=nhapdiem" class="list-group-item list-group-item-action">
                                        <i class="fas fa-edit me-2"></i>Nhập điểm
                                    </a>
                                    <a href="index.php?controller=chuyencan&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-user-check me-2"></i>Ghi nhận chuyên cần
                                    </a>
                                    <a href="index.php?controller=tinnhan&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-comments me-2"></i>Tin nhắn
                                    </a>
                                    <a href="index.php?controller=baitap&action=danhsach" class="list-group-item list-group-item-action">
                                        <i class="fas fa-tasks me-2"></i>Bài tập
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Lịch dạy hôm nay</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Tiết 1-2: Toán 10A1
                                        <span class="badge bg-primary">P.101</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Tiết 3-4: Toán 10A2
                                        <span class="badge bg-primary">P.102</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>