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
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?= $stats['total_students'] ?? 0 ?></h4>
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
                                        <h4><?= $stats['pending_assignments'] ?? 0 ?></h4>
                                        <p>Bài tập chưa chấm</p>
                                    </div>
                                    <i class="fas fa-tasks fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?= $stats['today_lessons'] ?? 0 ?></h4>
                                        <p>Tiết dạy hôm nay</p>
                                    </div>
                                    <i class="fas fa-chalkboard fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?= $stats['homeroom_classes'] ?? 0 ?></h4>
                                        <p>Lớp chủ nhiệm</p>
                                    </div>
                                    <i class="fas fa-school fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Chức năng chính</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="index.php?controller=danhsachlop&action=index" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <i class="fas fa-users me-3 text-primary"></i>
                                        <div>
                                            <strong>Danh Sách Lớp</strong>
                                            <small class="d-block text-muted">Xem thông tin lớp và học sinh</small>
                                        </div>
                                    </a>
                                    <a href="index.php?controller=diem&action=nhapdiem" class="list-group-item list-group-item-action">
                                        <i class="fas fa-edit me-2"></i>
                                        <strong>Nhập điểm</strong>
                                    </a>
                                    <a href="index.php?controller=chuyencan&action=index" class="list-group-item list-group-item-action">
                                        <i class="fas fa-user-check me-2"></i>
                                        <strong>Ghi nhận chuyên cần</strong>
                                    </a>
                                    <a href="index.php?controller=thoikhoabieu&action=index" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <i class="fas fa-calendar-alt me-3 text-success"></i>
                                        <div>
                                            <strong>Thời Khóa Biểu</strong>
                                            <small class="d-block text-muted">Xem lịch dạy và lịch học</small>
                                        </div>
                                    </a>
                                    <a href="index.php?controller=phancongdethi&action=index" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <i class="fas fa-tasks me-3 text-warning"></i>
                                        <div>
                                            <strong>Phân Công Đề Thi</strong>
                                            <small class="d-block text-muted">Quản lý đề thi và kiểm tra</small>
                                        </div>
                                    </a>
                                    <a href="index.php?controller=tinnhan&action=index" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <i class="fas fa-comments me-3 text-info"></i>
                                        <div>
                                            <strong>Tin Nhắn</strong>
                                            <small class="d-block text-muted">Liên hệ với học sinh và phụ huynh</small>
                                        </div>
                                    </a>
                                    <a href="index.php?controller=baitap&action=danhsach" class="list-group-item list-group-item-action">
                                        <i class="fas fa-tasks me-2"></i>
                                        <strong>Bài tập</strong>
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
                                <?php if (!empty($todaySchedule)): ?>
                                    <ul class="list-group">
                                        <?php foreach ($todaySchedule as $schedule): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?= $schedule['tenMonHoc'] ?? 'Môn học' ?></strong>
                                                    <small class="d-block text-muted">Tiết <?= $schedule['tietBatDau'] ?? '' ?></small>
                                                </div>
                                                <span class="badge bg-primary"><?= $schedule['tenLop'] ?? 'Lớp' ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                        <p>Không có tiết dạy nào hôm nay</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Thông báo mới -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Thông báo mới</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($newNotifications)): ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($newNotifications as $notification): ?>
                                            <div class="list-group-item px-0">
                                                <h6 class="mb-1"><?= htmlspecialchars($notification['tieuDe']) ?></h6>
                                                <p class="mb-1 small text-muted"><?= substr($notification['noiDung'], 0, 100) ?>...</p>
                                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($notification['ngayGui'])) ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                        <p>Không có thông báo mới</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>