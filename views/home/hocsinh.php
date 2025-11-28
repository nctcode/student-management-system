<?php
$user = $_SESSION['user'];
$roleName = 'Học sinh';

// FIX LỖI: KIỂM TRA VÀ LẤY THÔNG TIN KHỐI
$khoi = isset($user['khoi']) ? $user['khoi'] : null;
$tenLop = isset($user['tenLop']) ? $user['tenLop'] : 'Chưa xác định';
$maHocSinh = isset($user['maHocSinh']) ? $user['maHocSinh'] : null;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>Chào mừng, <?php echo htmlspecialchars($user['hoTen']); ?>!</h1>
                        <p class="lead">Vai trò: <?php echo $roleName; ?></p>
                        <!-- HIỂN THỊ THÔNG TIN KHỐI NẾU CÓ -->
                        <?php if ($khoi && $tenLop): ?>
                        <p class="text-muted">Khối: <?php echo htmlspecialchars($khoi); ?> - Lớp: <?php echo htmlspecialchars($tenLop); ?></p>
                        <?php endif; ?>
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
                                    <a href="index.php?controller=tracuuhoso&action=traCuuHoSo" class="list-group-item list-group-item-action">
                                        <i class="fas fa-book me-2"></i>Tra cứu hồ sơ
                                    </a>
                                    <!-- CHỈ HIỆN ĐĂNG KÝ BAN HỌC CHO HỌC SINH KHỐI 11 -->
                                    <?php if ($khoi == 11): ?>
                                    <a href="index.php?controller=dangkybanhoc&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-graduation-cap me-2"></i>Đăng ký ban học lớp 12
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- THÔNG BÁO ĐĂNG KÝ BAN HỌC (CHỈ HIỆN CHO KHỐI 11) -->
                <?php if ($khoi == 11): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5><i class="fas fa-exclamation-circle me-2"></i>Thông báo quan trọng: Đăng ký Ban học Lớp 12</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-2">Bạn đang học <strong>khối 11</strong> và cần đăng ký ban học cho năm lớp 12.</p>
                                        <p class="mb-3 text-muted">Thời hạn đăng ký: Từ <?php echo date('d/m/Y'); ?> đến <?php echo date('d/m/Y', strtotime('+2 weeks')); ?></p>
                                        
                                        <!-- Kiểm tra nếu đã đăng ký rồi -->
                                        <?php 
                                        // Tạm thời để trống, sẽ implement sau
                                        $daDangKy = false;
                                        if ($daDangKy): 
                                            $thongTinDangKy = []; // Tạm thời
                                        ?>
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                <strong>Bạn đã đăng ký:</strong> <?php echo $thongTinDangKy['tenBan']; ?>
                                                <br><small>Ngày đăng ký: <?php echo date('d/m/Y', strtotime($thongTinDangKy['ngayDangKy'])); ?></small>
                                            </div>
                                        <?php else: ?>
                                            <a href="index.php?controller=dangkybanhoc&action=index" class="btn btn-primary">
                                                <i class="fas fa-pencil-alt me-2"></i>Đăng ký ngay
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="bg-light rounded p-3">
                                            <i class="fas fa-clock fa-3x text-warning mb-2"></i>
                                            <p class="mb-0"><small>Còn <strong id="countdown">14</strong> ngày</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

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