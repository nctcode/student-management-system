<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar/totruong.php';

// Kiểm tra xem có dữ liệu đề thi không
if (!isset($deThi) || empty($deThi)) {
    echo '<div class="alert alert-danger">Không tìm thấy dữ liệu!</div>';
    require_once __DIR__ . '/../layouts/footer.php';
    exit;
}
?>

<div class="main-content" style="margin-left: 250px; padding: 20px;">
    <div class="container-fluid">
        <!-- Header với nút quay lại -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="index.php?controller=phancongrade" class="btn btn-outline-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <h2 class="mb-0">Chi tiết Phân công Ra đề</h2>
                <small class="text-muted">Mã đề: #<?php echo htmlspecialchars($deThi['maDeThi']); ?></small>
            </div>
            
            <!-- Nút hành động -->
            <div class="btn-group">
                <?php if (($deThi['trangThai'] ?? '') == 'Chờ nộp'): ?>
                    <a href="index.php?controller=phancongrade&action=edit&id=<?php echo $deThi['maDeThi']; ?>" 
                       class="btn btn-warning">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                <?php endif; ?>
                
                <!-- Nút in/export -->
                <button type="button" class="btn btn-info" onclick="window.print()">
                    <i class="fas fa-print"></i> In
                </button>
            </div>
        </div>

        <!-- Thông báo -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Card thông tin chính -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin chung</h5>
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
                                <th>Khối:</th>
                                <td>
                                    <?php 
                                    $tenKhoi = $deThi['tenKhoi'] ?? 'N/A';
                                    // Hiển thị khối dạng 10, 11, 12 thay vì 5, 6, 7
                                    $khoiMap = [5 => '10', 6 => '11', 7 => '12', 1 => '6', 2 => '7', 3 => '8', 4 => '9'];
                                    $maKhoi = $deThi['maKhoi'] ?? 0;
                                    echo $khoiMap[$maKhoi] ?? $tenKhoi;
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Môn học:</th>
                                <td><?php echo htmlspecialchars($deThi['tenMonHoc'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Số lượng đề:</th>
                                <td><?php echo htmlspecialchars($deThi['soLuongDe'] ?? '1'); ?> đề</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Trạng thái:</th>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'Chờ nộp' => 'badge bg-warning',
                                        'Đã nộp' => 'badge bg-info',
                                        'CHO_DUYET' => 'badge bg-primary',
                                        'DA_DUYET' => 'badge bg-success',
                                        'TU_CHOI' => 'badge bg-danger',
                                        'HUY' => 'badge bg-secondary'
                                    ];
                                    $statusText = [
                                        'Chờ nộp' => 'Chờ nộp',
                                        'Đã nộp' => 'Đã nộp',
                                        'CHO_DUYET' => 'Chờ duyệt',
                                        'DA_DUYET' => 'Đã duyệt',
                                        'TU_CHOI' => 'Từ chối',
                                        'HUY' => 'Đã hủy'
                                    ];
                                    $status = $deThi['trangThai'] ?? 'Chờ nộp';
                                    $class = $statusClass[$status] ?? 'badge bg-secondary';
                                    $text = $statusText[$status] ?? $status;
                                    ?>
                                    <span class="<?php echo $class; ?>"><?php echo $text; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Hạn nộp:</th>
                                <td>
                                    <?php if (!empty($deThi['hanNopDe'])): ?>
                                        <span class="text-primary">
                                            <i class="far fa-calendar-alt"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($deThi['hanNopDe'])); ?>
                                        </span>
                                        <?php
                                        // Tính số ngày còn lại
                                        $now = new DateTime();
                                        $deadline = new DateTime($deThi['hanNopDe']);
                                        $interval = $now->diff($deadline);
                                        
                                        if ($deadline < $now) {
                                            echo ' <span class="badge bg-danger">Quá hạn</span>';
                                        } elseif ($interval->days <= 2) {
                                            echo ' <span class="badge bg-warning">Sắp hạn</span>';
                                        }
                                        ?>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa đặt hạn</span>
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
            </div>
        </div>

        <!-- Row cho các card khác -->
        <div class="row">
            <!-- Card Giáo viên được phân công -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chalkboard-teacher"></i> Giáo viên được phân công</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Lấy danh sách giáo viên từ chuỗi dsTenGiaoVien
                        $dsGiaoVien = !empty($deThi['dsTenGiaoVien']) ? explode(', ', $deThi['dsTenGiaoVien']) : [];
                        $dsMaGiaoVien = !empty($deThi['dsMaGiaoVien']) ? explode(',', $deThi['dsMaGiaoVien']) : [];
                        
                        if (!empty($dsGiaoVien) && $dsGiaoVien[0] !== ''):
                        ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($dsGiaoVien as $index => $tenGV): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-user-circle text-primary me-2"></i>
                                            <?php echo htmlspecialchars($tenGV); ?>
                                            <?php if (isset($dsMaGiaoVien[$index])): ?>
                                                <small class="text-muted ms-2">(Mã: <?php echo $dsMaGiaoVien[$index]; ?>)</small>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="bg-light border rounded p-3 mb-3" style="min-height: 60px;">
                                <i class="fas fa-exclamation-triangle"></i> Chưa phân công giáo viên
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Card Ghi chú & Thông tin bổ sung -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-sticky-note"></i> Ghi chú & Thông tin</h5>
                    </div>
                    <div class="card-body">
                        <h6>Ghi chú phân công:</h6>
                        <div class="bg-light border rounded p-3 mb-3" style="min-height: 60px;">
                            <?php if (!empty($deThi['ghiChu'])): ?>
                                <?php echo nl2br(htmlspecialchars($deThi['ghiChu'])); ?>
                            <?php else: ?>
                                <span class="text-muted">Không có ghi chú</span>
                            <?php endif; ?>
                        </div>

                        <h6>Nội dung yêu cầu:</h6>
                        <div class="bg-light border rounded p-3" style="min-height: 100px;">
                            <?php if (!empty($deThi['noiDung'])): ?>
                                <?php echo nl2br(htmlspecialchars($deThi['noiDung'])); ?>
                            <?php else: ?>
                                <span class="text-muted">Không có nội dung chi tiết</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card File đính kèm -->
        <?php if (!empty($deThi['fileDinhKem'])): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-paperclip"></i> File đính kèm</h5>
                </div>
                <div class="card-body">
                    <?php
                    $filePath = $deThi['fileDinhKem'];
                    $fileName = basename($filePath);
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $fileIcon = 'fa-file';
                    
                    // Icon theo loại file
                    $iconMap = [
                        'pdf' => 'fa-file-pdf',
                        'doc' => 'fa-file-word',
                        'docx' => 'fa-file-word',
                        'xls' => 'fa-file-excel',
                        'xlsx' => 'fa-file-excel',
                        'ppt' => 'fa-file-powerpoint',
                        'pptx' => 'fa-file-powerpoint',
                        'jpg' => 'fa-file-image',
                        'jpeg' => 'fa-file-image',
                        'png' => 'fa-file-image',
                        'zip' => 'fa-file-archive',
                        'rar' => 'fa-file-archive'
                    ];
                    
                    if (isset($iconMap[$fileExt])) {
                        $fileIcon = $iconMap[$fileExt];
                    }
                    ?>
                    <div class="d-flex align-items-center p-3 border rounded bg-light">
                        <div class="me-3">
                            <i class="fas <?php echo $fileIcon; ?> fa-3x text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?php echo htmlspecialchars($fileName); ?></h6>
                            <small class="text-muted"><?php echo strtoupper($fileExt); ?> file</small>
                        </div>
                        <div>
                            <a href="<?php echo htmlspecialchars($filePath); ?>" 
                               class="btn btn-success" 
                               target="_blank" 
                               download>
                                <i class="fas fa-download"></i> Tải xuống
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Nút hành động ở cuối -->
        <div class="mt-4 d-flex justify-content-between">
            <div>
                <a href="index.php?controller=phancongrade" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
            <div class="btn-group">
                <?php if (($deThi['trangThai'] ?? '') == 'Chờ nộp'): ?>
                    <a href="index.php?controller=phancongrade&action=edit&id=<?php echo $deThi['maDeThi']; ?>" 
                       class="btn btn-warning">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    
                    <!-- Nút xóa với modal xác nhận -->
                    <button type="button" 
                            class="btn btn-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                    
                    <!-- Modal xác nhận xóa -->
                    <div class="modal fade" id="deleteModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Xác nhận xóa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Bạn có chắc chắn muốn xóa phân công "<strong><?php echo htmlspecialchars($deThi['tieuDe']); ?></strong>" không?
                                    <br><small class="text-danger">Hành động này không thể hoàn tác!</small>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <form action="index.php?controller=phancongrade&action=delete" method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $deThi['maDeThi']; ?>">
                                        <input type="hidden" name="_token" value="<?php 
                                            if (session_status() === PHP_SESSION_NONE) {
                                                session_start();
                                            }
                                            echo $_SESSION['csrf_token'] ?? ''; 
                                        ?>">
                                        <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Nút duyệt nếu là tổ trưởng -->
            </div>
        </div>
    </div>
</div>

<!-- CSS tùy chỉnh -->
<style>
    /* TẠM THỜI: Ngăn chặn mọi animation/transition */
    .alert-light {
        opacity: 1 !important;
        display: block !important;
        visibility: visible !important;
        animation: none !important;
        transition: none !important;
    }

    /* Hoặc thử class khác */
    .no-fade {
        opacity: 1 !important;
        display: block !important;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    th {
        font-weight: 600;
        color: #495057;
    }
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:first-child {
        border-top: none;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>