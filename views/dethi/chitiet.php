<?php
// Kiểm tra dữ liệu
if (!isset($deThi) || empty($deThi)) {
    echo '<div class="alert alert-danger">Không tìm thấy dữ liệu đề thi!</div>';
    require_once 'views/layouts/footer.php';
    exit;
}

// Hàm hiển thị trạng thái
function hienThiTrangThai($trangThai) {
    $statusClass = [
        'CHO_DUYET' => 'badge bg-warning',
        'DA_DUYET' => 'badge bg-success',
        'TU_CHOI' => 'badge bg-danger',
        'Chờ nộp' => 'badge bg-info',
        'Đã nộp' => 'badge bg-primary'
    ];
    
    $statusText = [
        'CHO_DUYET' => 'Chờ duyệt',
        'DA_DUYET' => 'Đã duyệt',
        'TU_CHOI' => 'Từ chối',
        'Chờ nộp' => 'Chờ nộp',
        'Đã nộp' => 'Đã nộp'
    ];
    
    $class = $statusClass[$trangThai] ?? 'badge bg-secondary';
    $text = $statusText[$trangThai] ?? $trangThai;
    
    return '<span class="' . $class . '">' . $text . '</span>';
}

// Hàm hiển thị icon file
function getFileIcon($fileName) {
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $icons = [
        'pdf' => 'fa-file-pdf text-danger',
        'doc' => 'fa-file-word text-primary',
        'docx' => 'fa-file-word text-primary',
        'jpg' => 'fa-file-image text-success',
        'jpeg' => 'fa-file-image text-success',
        'png' => 'fa-file-image text-success',
        'zip' => 'fa-file-archive text-warning',
        'rar' => 'fa-file-archive text-warning'
    ];
    
    return isset($icons[$ext]) ? $icons[$ext] : 'fa-file text-secondary';
}

$fileIcon = getFileIcon($deThi['noiDung'] ?? '');
?>

<div class="main-content" style="margin-left: 250px; padding: 20px;">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="index.php?controller=deThi&action=index" class="btn btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <h2 class="mb-0">Chi tiết đề thi</h2>
                <small class="text-muted">Mã đề: #<?php echo $deThi['maDeThi']; ?></small>
            </div>
            
            <div class="btn-group">
                <?php if (($deThi['trangThai'] ?? '') == 'CHO_DUYET'): ?>
                    <a href="index.php?controller=deThi&action=edit&id=<?php echo $deThi['maDeThi']; ?>" 
                       class="btn btn-warning">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                <?php endif; ?>
                
                <button class="btn btn-info" onclick="window.print()">
                    <i class="fas fa-print"></i> In
                </button>
            </div>
        </div>

        <!-- Thông báo -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message']['status'] == 'success' ? 'success' : 'danger'; ?> 
                alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']['text']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Card thông tin chính -->
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin đề thi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Tiêu đề:</th>
                                        <td><strong><?php echo htmlspecialchars($deThi['tieuDe'] ?? 'N/A'); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Môn học:</th>
                                        <td><?php echo htmlspecialchars($deThi['monHoc'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Giáo viên:</th>
                                        <td><?php echo htmlspecialchars($deThi['hoTen'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Khối:</th>
                                        <td>
                                            <?php
                                            $maKhoi = $deThi['maKhoi'] ?? 0;
                                            $khoiMap = [5 => '10', 6 => '11', 7 => '12', 1 => '6', 2 => '7', 3 => '8', 4 => '9'];
                                            echo 'Khối ' . ($khoiMap[$maKhoi] ?? $maKhoi);
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Trạng thái:</th>
                                        <td><?php echo hienThiTrangThai($deThi['trangThai'] ?? ''); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Ngày tạo:</th>
                                        <td>
                                            <?php if (!empty($deThi['ngayTao'])): ?>
                                                <?php echo date('d/m/Y H:i', strtotime($deThi['ngayTao'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Ngày nộp:</th>
                                        <td>
                                            <?php if (!empty($deThi['ngayNop'])): ?>
                                                <span class="text-success">
                                                    <i class="far fa-calendar-check"></i>
                                                    <?php echo date('d/m/Y H:i', strtotime($deThi['ngayNop'])); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-warning">Chưa nộp</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Ngày duyệt:</th>
                                        <td>
                                            <?php if (!empty($deThi['ngayDuyet'])): ?>
                                                <?php echo date('d/m/Y H:i', strtotime($deThi['ngayDuyet'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa duyệt</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Ghi chú từ tổ trưởng -->
                        <?php if (!empty($deThi['ghiChu'])): ?>
                        <div class="alert alert-light border mt-3">
                            <h6><i class="fas fa-sticky-note"></i> Ghi chú từ tổ trưởng:</h6>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($deThi['ghiChu'])); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Card File đính kèm -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-paperclip"></i> File đề thi</h5>
                    </div>
                    <div class="card-body text-center">
                        <?php if (!empty($deThi['noiDung'])): 
                            $filePath = 'uploads/dethi/' . $deThi['noiDung'];
                            $fileExists = file_exists($filePath);
                            $fileSize = $fileExists ? filesize($filePath) : 0;
                            $fileSizeFormatted = $fileSize > 0 ? round($fileSize / 1024, 2) . ' KB' : 'N/A';
                        ?>
                            <div class="mb-3">
                                <i class="fas <?php echo $fileIcon; ?> fa-5x mb-3"></i>
                                <h5 class="mb-2"><?php echo htmlspecialchars($deThi['noiDung']); ?></h5>
                                <p class="text-muted mb-0">
                                    <i class="far fa-file"></i> 
                                    <?php echo strtoupper(pathinfo($deThi['noiDung'], PATHINFO_EXTENSION)); ?> 
                                    • <?php echo $fileSizeFormatted; ?>
                                </p>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <?php if ($fileExists): ?>
                                    <a href="<?php echo $filePath; ?>" 
                                       class="btn btn-success" 
                                       target="_blank">
                                        <i class="fas fa-eye"></i> Xem trước
                                    </a>
                                    <a href="<?php echo $filePath; ?>" 
                                       class="btn btn-primary" 
                                       download>
                                        <i class="fas fa-download"></i> Tải xuống
                                    </a>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        File không tồn tại trên máy chủ
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-file-excel fa-4x text-muted mb-3"></i>
                                <p class="text-muted">Không có file đính kèm</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Card thông tin bổ sung -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-cogs"></i> Thông tin kỹ thuật</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Mã đề thi:</strong></td>
                                <td>#<?php echo $deThi['maDeThi']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Mã giáo viên:</strong></td>
                                <td><?php echo $deThi['maGiaoVien'] ?? 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Mã môn học:</strong></td>
                                <td><?php echo $deThi['maMonHoc'] ?? 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Mã khối:</strong></td>
                                <td><?php echo $deThi['maKhoi'] ?? 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Mã niên khóa:</strong></td>
                                <td><?php echo $deThi['maNienKhoa'] ?? 'N/A'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch sử trạng thái -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Lịch sử trạng thái</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Tạo đề thi</h6>
                            <small class="text-muted">
                                <?php echo !empty($deThi['ngayTao']) ? date('d/m/Y H:i', strtotime($deThi['ngayTao'])) : 'N/A'; ?>
                            </small>
                            <p class="mb-0">Giáo viên tạo đề thi mới</p>
                        </div>
                    </div>
                    
                    <?php if (!empty($deThi['ngayNop'])): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Nộp đề thi</h6>
                            <small class="text-muted">
                                <?php echo date('d/m/Y H:i', strtotime($deThi['ngayNop'])); ?>
                            </small>
                            <p class="mb-0">Giáo viên nộp đề thi lên hệ thống</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($deThi['ngayDuyet'])): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker 
                            <?php echo ($deThi['trangThai'] == 'DA_DUYET') ? 'bg-success' : 'bg-danger'; ?>">
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">
                                <?php echo ($deThi['trangThai'] == 'DA_DUYET') ? 'Đã duyệt' : 'Từ chối'; ?>
                            </h6>
                            <small class="text-muted">
                                <?php echo date('d/m/Y H:i', strtotime($deThi['ngayDuyet'])); ?>
                            </small>
                            <?php if (!empty($deThi['ghiChu'])): ?>
                                <p class="mb-0"><?php echo htmlspecialchars($deThi['ghiChu']); ?></p>
                            <?php else: ?>
                                <p class="mb-0">
                                    <?php echo ($deThi['trangThai'] == 'DA_DUYET') ? 
                                        'Đề thi đã được phê duyệt' : 
                                        'Đề thi không được phê duyệt'; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Nút hành động -->
        <div class="d-flex justify-content-between">
            <div>
                <a href="index.php?controller=deThi&action=index" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
            <div class="btn-group">
                <?php if (($deThi['trangThai'] ?? '') == 'CHO_DUYET'): ?>
                    <a href="index.php?controller=deThi&action=edit&id=<?php echo $deThi['maDeThi']; ?>" 
                       class="btn btn-warning">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    
                    <!-- Modal xác nhận xóa -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                <?php endif; ?>
                
                <?php if (($deThi['trangThai'] ?? '') == 'TU_CHOI'): ?>
                    <a href="index.php?controller=deThi&action=edit&id=<?php echo $deThi['maDeThi']; ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-redo"></i> Chỉnh sửa và nộp lại
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal xác nhận xóa -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Xác nhận xóa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa đề thi <strong>"<?php echo htmlspecialchars($deThi['tieuDe']); ?>"</strong> không?</p>
                        <p class="text-danger"><small><i class="fas fa-exclamation-triangle"></i> Hành động này không thể hoàn tác!</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <form action="index.php?controller=deThi&action=delete" method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $deThi['maDeThi']; ?>">
                            <input type="hidden" name="_token" value="<?php 
                                if (session_status() === PHP_SESSION_NONE) session_start();
                                echo $_SESSION['csrf_token'] ?? ''; 
                            ?>">
                            <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS tùy chỉnh -->
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-left: 2px solid #dee2e6;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .timeline-marker {
        position: absolute;
        left: -9px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 3px var(--bs-primary);
    }
    
    .timeline-content {
        margin-left: 20px;
    }
    
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: none;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
        font-weight: 600;
    }
    
    .btn-group .btn {
        border-radius: 8px;
        padding: 8px 16px;
    }
    
    @media print {
        .btn, .modal, .d-print-none {
            display: none !important;
        }
        
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
    }
</style>

<script>
// Xử lý khi nhấn nút xóa
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('shown.bs.modal', function() {
            console.log('Delete modal shown');
        });
    }
    
    // Xử lý nút in
    document.querySelector('[onclick="window.print()"]').addEventListener('click', function() {
        window.print();
    });
});
</script>