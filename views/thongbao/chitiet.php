<?php
// views/thongbao/chitiet.php
$title = $data['title'] ?? 'Chi Tiết Thông Báo';
$thongBao = $data['thongBao'] ?? null;
$userRole = $data['userRole'] ?? '';

if (!$thongBao) {
    echo '<div class="alert alert-danger">Thông báo không tồn tại</div>';
    return;
}
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">📢 Chi tiết thông báo</h2>
            <p class="text-muted mb-0">Xem đầy đủ thông tin thông báo</p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?controller=thongbao&action=danhsach" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
            <?php if (in_array($userRole, ['QTV', 'BGH'])): ?>
            <a href="index.php?controller=thongbao&action=xoa&maThongBao=<?php echo $thongBao['maThongBao']; ?>" 
               class="btn btn-outline-danger" 
               onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này?')">
                <i class="fas fa-trash me-2"></i>Xóa
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Notification Detail Card -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white py-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h3 class="card-title text-primary mb-2"><?php echo htmlspecialchars($thongBao['tieuDe']); ?></h3>
                            
                            <!-- Badges -->
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <?php
                                // Badge ưu tiên
                                $priorityClass = '';
                                switch ($thongBao['uuTien']) {
                                    case 'KHAN_CAP':
                                        $priorityClass = 'bg-danger';
                                        $priorityText = 'Khẩn cấp';
                                        break;
                                    case 'CAO':
                                        $priorityClass = 'bg-warning';
                                        $priorityText = 'Cao';
                                        break;
                                    case 'TRUNG_BINH':
                                        $priorityClass = 'bg-info';
                                        $priorityText = 'Trung bình';
                                        break;
                                    case 'THAP':
                                        $priorityClass = 'bg-secondary';
                                        $priorityText = 'Thấp';
                                        break;
                                    default:
                                        $priorityClass = 'bg-secondary';
                                        $priorityText = 'Trung bình';
                                }
                                ?>
                                <span class="badge <?php echo $priorityClass; ?>">
                                    <i class="fas fa-exclamation-circle me-1"></i><?php echo $priorityText; ?>
                                </span>
                                
                                <!-- Badge loại thông báo -->
                                <?php
                                $typeClass = '';
                                switch ($thongBao['loaiThongBao']) {
                                    case 'CHUNG':
                                        $typeClass = 'bg-primary';
                                        $typeText = 'Thông báo chung';
                                        break;
                                    case 'LOP':
                                        $typeClass = 'bg-success';
                                        $typeText = 'Thông báo lớp';
                                        break;
                                    case 'MON_HOC':
                                        $typeClass = 'bg-info';
                                        $typeText = 'Thông báo môn học';
                                        break;
                                    case 'KHOA_HOC':
                                        $typeClass = 'bg-warning';
                                        $typeText = 'Thông báo khóa học';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $typeClass; ?>">
                                    <i class="fas fa-tag me-1"></i><?php echo $typeText; ?>
                                </span>
                                
                                <!-- Badge người nhận -->
                                <?php
                                $receiverClass = '';
                                switch ($thongBao['nguoiNhan']) {
                                    case 'TAT_CA':
                                        $receiverClass = 'bg-dark';
                                        $receiverText = 'Tất cả mọi người';
                                        break;
                                    case 'HOC_SINH':
                                        $receiverClass = 'bg-success';
                                        $receiverText = 'Học sinh';
                                        break;
                                    case 'PHU_HUYNH':
                                        $receiverClass = 'bg-primary';
                                        $receiverText = 'Phụ huynh';
                                        break;
                                    case 'GIAO_VIEN':
                                        $receiverClass = 'bg-info';
                                        $receiverText = 'Giáo viên';
                                        break;
                                    case 'QTV':
                                        $receiverClass = 'bg-info';
                                        $receiverText = 'Quản trị viên';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $receiverClass; ?>">
                                    <i class="fas fa-users me-1"></i><?php echo $receiverText; ?>
                                </span>
                                
                                <!-- Badge trạng thái -->
                                <span class="badge <?php echo $thongBao['trangThai'] === 'Đã xem' ? 'bg-success' : 'bg-warning'; ?>">
                                    <i class="fas <?php echo $thongBao['trangThai'] === 'Đã xem' ? 'fa-check' : 'fa-clock'; ?> me-1"></i>
                                    <?php echo $thongBao['trangThai']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($thongBao['fileDinhKem'])): ?>
                        <div class="text-end">
                            <a href="uploads/thongbao/<?php echo $thongBao['fileDinhKem']; ?>" 
                               class="btn btn-outline-primary btn-sm" 
                               target="_blank"
                               download>
                                <i class="fas fa-download me-1"></i>Tải file
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Meta information -->
                    <div class="row mt-3 text-muted">
                        <div class="col-md-6">
                            <small>
                                <i class="fas fa-user me-1"></i>
                                Người gửi: <strong><?php echo htmlspecialchars($thongBao['tenNguoiGui'] ?? 'Hệ thống'); ?></strong>
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small>
                                <i class="fas fa-clock me-1"></i>
                                Thời gian: <strong><?php echo date('H:i d/m/Y', strtotime($thongBao['ngayGui'])); ?></strong>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Nội dung thông báo -->
                    <div class="mb-4">
                        <h5 class="text-dark mb-3">Nội dung thông báo</h5>
                        <div class="notification-content p-3 border rounded bg-light">
                            <div style="white-space: pre-wrap; line-height: 1.6;">
                                <?php echo htmlspecialchars($thongBao['noiDung']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin bổ sung -->
                    <div class="row">
                        <?php if (!empty($thongBao['thoiGianKetThuc'])): ?>
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-clock me-2 text-warning"></i>Thời gian hiển thị
                                    </h6>
                                    <p class="mb-0">
                                        Kết thúc: <strong><?php echo date('H:i d/m/Y', strtotime($thongBao['thoiGianKetThuc'])); ?></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($thongBao['fileDinhKem'])): ?>
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-paperclip me-2 text-primary"></i>File đính kèm
                                    </h6>
                                    <p class="mb-0">
                                        <i class="fas fa-file me-1"></i>
                                        <?php echo htmlspecialchars($thongBao['fileDinhKem']); ?>
                                    </p>
                                    <div class="mt-2">
                                        <a href="uploads/thongbao/<?php echo $thongBao['fileDinhKem']; ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           target="_blank"
                                           download>
                                            <i class="fas fa-download me-1"></i>Tải xuống
                                        </a>
                                        <a href="uploads/thongbao/<?php echo $thongBao['fileDinhKem']; ?>" 
                                           class="btn btn-sm btn-outline-secondary"
                                           target="_blank">
                                            <i class="fas fa-eye me-1"></i>Xem trước
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-content {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
}

.card {
    border-radius: 12px;
}

.badge {
    font-size: 0.8em;
    padding: 6px 10px;
}
</style>