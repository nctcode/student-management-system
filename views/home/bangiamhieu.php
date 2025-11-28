<?php
$user = $_SESSION['user'];
$roleName = 'Ban giám hiệu';
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
                                        <h4>1,250</h4>
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
                                        <h4>85</h4>
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
                                        <h4>45</h4>
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
                                        <h4>15</h4>
                                        <p>Đơn chờ duyệt</p>
                                    </div>
                                    <i class="fas fa-clipboard-list fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Quản lý nhà trường</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="index.php?controller=tuyensinh&action=danhsachhoso" class="list-group-item list-group-item-action">
                                        <i class="fas fa-user-graduate me-2"></i>Tuyển sinh & Đào tạo
                                    </a>
                                    <!-- Thêm mục mới cho phân công giáo viên -->
                                    <a href="index.php?controller=PhanCongGVBMCN&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-chalkboard-teacher me-2"></i>Phân công giáo viên
                                    </a>
                                    <a href="index.php?controller=PhanCongGVBMCN&action=viewCurrentAssignments" class="list-group-item list-group-item-action">
                                        <i class="fas fa-list-alt me-2"></i>Xem phân công hiện tại
                                    </a>
                                    <!-- Thêm mục mới cho phê duyệt chuyển lớp/trường -->
                                    <a href="index.php?controller=donchuyenloptruong&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-exchange-alt me-2"></i>Phê duyệt chuyển lớp/trường
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Báo cáo & Thống kê</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="index.php?controller=ThongKe&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-chart-bar me-2"></i>Báo cáo học tập
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