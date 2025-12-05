<?php
// Kiểm tra dữ liệu
if (!isset($deThi) || empty($deThi)) {
    echo '<div class="alert alert-danger">Không tìm thấy dữ liệu đề thi!</div>';
    require_once 'views/layouts/footer.php';
    exit;
}

// Lấy thông tin file hiện tại
$currentFile = $deThi['noiDung'] ?? '';
$currentFilePath = !empty($currentFile) ? 'uploads/dethi/' . $currentFile : '';
$fileExists = !empty($currentFile) && file_exists($currentFilePath);

// Xác định tên môn học - SỬA TỪ 'monHoc' THÀNH 'tenMonHoc'
$monHoc = isset($deThi['tenMonHoc']) ? $deThi['tenMonHoc'] : ($deThi['monHoc'] ?? 'N/A');

// Xác định tên giáo viên
$tenGiaoVien = isset($deThi['tenGiaoVien']) ? $deThi['tenGiaoVien'] : ($deThi['hoTen'] ?? 'N/A');

// Xác định khối
$maKhoi = $deThi['maKhoi'] ?? 0;
$khoiMap = [5 => '10', 6 => '11', 7 => '12', 1 => '6', 2 => '7', 3 => '8', 4 => '9'];
$tenKhoi = 'Khối ' . ($khoiMap[$maKhoi] ?? $maKhoi);
?>

<div class="main-content" style="margin-left: 250px; padding: 20px;">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="index.php?controller=deThi&action=view&id=<?php echo $deThi['maDeThi']; ?>" 
                   class="btn btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left"></i> Quay lại chi tiết
                </a>
                <h2 class="mb-0">Chỉnh sửa đề thi</h2>
                <small class="text-muted">Mã đề: #<?php echo htmlspecialchars($deThi['maDeThi'] ?? 'N/A'); ?></small>
            </div>
            
            <div>
                <?php if (($deThi['trangThai'] ?? '') == 'TU_CHOI'): ?>
                    <span class="badge bg-danger me-2">
                        <i class="fas fa-times-circle"></i> Đã bị từ chối
                    </span>
                <?php else: ?>
                    <span class="badge bg-warning me-2">
                        <i class="fas fa-clock"></i> Chờ duyệt
                    </span>
                <?php endif; ?>
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

        <!-- Form chỉnh sửa -->
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Chỉnh sửa đề thi</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?controller=deThi&action=update" 
                      enctype="multipart/form-data" id="editForm">
                    
                    <input type="hidden" name="maDeThi" value="<?php echo htmlspecialchars($deThi['maDeThi'] ?? ''); ?>">
                    
                    <!-- Thông tin hiện tại -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="bg-light border rounded p-3 mb-3">
                                <h6><i class="fas fa-info-circle"></i> Thông tin hiện tại:</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Môn học:</strong><br>
                                        <span class="text-primary"><?php echo htmlspecialchars($monHoc); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Khối:</strong><br>
                                        <span class="text-primary"><?php echo htmlspecialchars($tenKhoi); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Ngày tạo:</strong><br>
                                        <span>
                                            <?php echo !empty($deThi['ngayTao']) ? date('d/m/Y', strtotime($deThi['ngayTao'])) : 'N/A'; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Thông tin giáo viên -->
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <strong>Giáo viên:</strong><br>
                                        <span><?php echo htmlspecialchars($tenGiaoVien); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Học kỳ:</strong><br>
                                        <span>
                                            <?php
                                            if (!empty($deThi['hocKy']) && !empty($deThi['namHoc'])) {
                                                $hocKyText = '';
                                                switch($deThi['hocKy']) {
                                                    case 'HK1': $hocKyText = 'Học kỳ 1'; break;
                                                    case 'HK2': $hocKyText = 'Học kỳ 2'; break;
                                                    case 'CA_NAM': $hocKyText = 'Cả năm'; break;
                                                    default: $hocKyText = $deThi['hocKy'];
                                                }
                                                echo htmlspecialchars($hocKyText . ' (' . $deThi['namHoc'] . ')');
                                            } else {
                                                echo '<span class="text-muted">N/A</span>';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Ngày nộp:</strong><br>
                                        <span class="<?= empty($deThi['ngayNop']) ? 'text-warning' : 'text-success' ?>">
                                            <?= !empty($deThi['ngayNop']) ? date('d/m/Y H:i', strtotime($deThi['ngayNop'])) : 'Chưa nộp' ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Lý do từ chối -->
                                <?php if (!empty($deThi['ghiChu']) && ($deThi['trangThai'] ?? '') == 'TU_CHOI'): ?>
                                <div class="mt-3 p-2 alert-danger rounded">
                                    <strong><i class="fas fa-comment"></i> Lý do từ chối:</strong>
                                    <div class="mt-1">
                                        <?php echo nl2br(htmlspecialchars($deThi['ghiChu'])); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tiêu đề -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">
                                <strong>Tiêu đề đề thi</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="tieuDe" class="form-control" 
                                   value="<?php echo htmlspecialchars($deThi['tieuDe'] ?? ''); ?>"
                                   placeholder="Nhập tiêu đề đề thi" required>
                            <small class="text-muted">Tiêu đề phải rõ ràng, thể hiện nội dung đề thi</small>
                        </div>
                    </div>

                    <!-- File đề thi -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">
                                <strong>File đề thi</strong> 
                                <span class="text-muted">(Chỉ upload nếu muốn thay đổi)</span>
                            </label>
                            
                            <!-- Hiển thị file hiện tại -->
                            <?php if (!empty($currentFile)): ?>
                            <div class="bg-light border rounded p-2 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-alt text-primary me-2"></i>
                                        <strong>File hiện tại:</strong> 
                                        <?php if ($fileExists): ?>
                                            <a href="<?php echo htmlspecialchars($currentFilePath); ?>" target="_blank">
                                                <?php echo htmlspecialchars($currentFile); ?>
                                            </a>
                                            <small class="text-muted ms-2">
                                                (<?php echo round(filesize($currentFilePath) / 1024, 2); ?> KB)
                                            </small>
                                        <?php else: ?>
                                            <span class="text-danger">
                                                <?php echo htmlspecialchars($currentFile); ?> 
                                                <i class="fas fa-exclamation-triangle"></i> File không tồn tại
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($fileExists): ?>
                                    <div>
                                        <a href="<?php echo htmlspecialchars($currentFilePath); ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           target="_blank">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="<?php echo htmlspecialchars($currentFilePath); ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           download>
                                            <i class="fas fa-download"></i> Tải
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                Không tìm thấy file đề thi hiện tại. Vui lòng upload file mới.
                            </div>
                            <?php endif; ?>
                            
                            <!-- Upload file mới -->
                            <div class="input-group">
                                <input type="file" name="fileDeThi" class="form-control" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       id="fileInput">
                                <span class="input-group-text">
                                    <i class="fas fa-upload"></i>
                                </span>
                            </div>
                            <div id="fileInfo" class="mt-2" style="display: none;">
                                <div class="alert alert-light border">
                                    <strong>Thông tin file:</strong>
                                    <div id="fileName"></div>
                                    <div id="fileSize"></div>
                                </div>
                            </div>
                            <div class="form-text mt-2">
                                <i class="fas fa-info-circle"></i>
                                Chỉ upload file mới nếu muốn thay thế file hiện tại. 
                                Định dạng cho phép: PDF, Word (DOC/DOCX), JPG, PNG. 
                                Tối đa 10MB.
                            </div>
                        </div>
                    </div>

                    <!-- Ghi chú bổ sung -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">
                                <strong>Ghi chú bổ sung</strong>
                                <span class="text-muted">(Tùy chọn)</span>
                            </label>
                            <textarea name="noiDungBoSung" class="form-control" rows="4" 
                                      placeholder="Nhập ghi chú bổ sung, nội dung yêu cầu, hoặc giải thích về thay đổi (nếu có)..."><?php 
                                // Hiển thị ghi chú cũ nếu có (trừ lý do từ chối)
                                $oldNote = '';
                                if (!empty($deThi['ghiChu']) && ($deThi['trangThai'] ?? '') != 'TU_CHOI') {
                                    echo htmlspecialchars($deThi['ghiChu']);
                                }
                            ?></textarea>
                            <div class="form-text">
                                Ghi chú này sẽ được gửi kèm khi nộp lại đề thi. 
                                <?php if (($deThi['trangThai'] ?? '') == 'TU_CHOI'): ?>
                                    <span class="text-danger">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Hãy giải thích rõ bạn đã chỉnh sửa những gì sau khi bị từ chối.
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Cảnh báo quan trọng -->
                    <?php if (($deThi['trangThai'] ?? '') == 'TU_CHOI'): ?>
                    <div class="alert alert-warning border">
                        <h6><i class="fas fa-exclamation-triangle"></i> Lưu ý quan trọng:</h6>
                        <ul class="mb-0">
                            <li>Đề thi của bạn đã bị từ chối với lý do: 
                                <strong>"<?php echo htmlspecialchars($deThi['ghiChu'] ?? ''); ?>"</strong>
                            </li>
                            <li>Sau khi chỉnh sửa, đề thi sẽ được chuyển về trạng thái <strong>"Chờ duyệt"</strong></li>
                            <li>Hãy đảm bảo bạn đã sửa tất cả các vấn đề được nêu trong lý do từ chối</li>
                            <li>Tổ trưởng sẽ xem xét lại đề thi sau khi bạn nộp</li>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Nút hành động -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div>
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                            <a href="index.php?controller=deThi&action=view&id=<?php echo htmlspecialchars($deThi['maDeThi'] ?? ''); ?>" 
                               class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                        
                        <!-- Nút xóa -->
                        <button type="button" class="btn btn-danger" 
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash"></i> Xóa đề thi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Xác nhận xóa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <h5>Bạn có chắc chắn muốn xóa đề thi này?</h5>
                </div>
                
                <div class="alert alert-light border">
                    <h6>Thông tin đề thi sẽ xóa:</h6>
                    <ul class="mb-0">
                        <li><strong>Tiêu đề:</strong> <?php echo htmlspecialchars($deThi['tieuDe'] ?? ''); ?></li>
                        <li><strong>Mã đề:</strong> #<?php echo htmlspecialchars($deThi['maDeThi'] ?? ''); ?></li>
                        <li><strong>Trạng thái:</strong> 
                            <?php echo (($deThi['trangThai'] ?? '') == 'TU_CHOI') ? 'Đã bị từ chối' : 'Chờ duyệt'; ?>
                        </li>
                        <li><strong>Môn học:</strong> <?php echo htmlspecialchars($monHoc); ?></li>
                        <li><strong>Khối:</strong> <?php echo htmlspecialchars($tenKhoi); ?></li>
                    </ul>
                </div>
                
                <div class="alert alert-danger mt-3">
                    <h6><i class="fas fa-radiation"></i> Cảnh báo:</h6>
                    <ul class="mb-0">
                        <li>Đề thi sẽ bị xóa vĩnh viễn khỏi hệ thống</li>
                        <li>File đính kèm cũng sẽ bị xóa</li>
                        <li>Hành động này <strong>KHÔNG THỂ</strong> hoàn tác</li>
                        <?php if (($deThi['trangThai'] ?? '') == 'TU_CHOI'): ?>
                        <li class="text-danger"><strong>Đề thi này đã bị từ chối, bạn nên chỉnh sửa thay vì xóa</strong></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Hủy
                </button>
                <form action="index.php?controller=deThi&action=delete" method="POST" 
                      style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($deThi['maDeThi'] ?? ''); ?>">
                    <input type="hidden" name="_token" value="<?php 
                        if (session_status() === PHP_SESSION_NONE) session_start();
                        echo $_SESSION['csrf_token'] ?? ''; 
                    ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-check"></i> Xác nhận xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CSS tùy chỉnh -->
<style>
    .main-content {
        background-color: #f8f9fa;
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
    
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    
    .alert {
        border-radius: 8px;
    }
    
    .btn {
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
    }
    
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .modal-header {
        border-radius: 12px 12px 0 0 !important;
    }
</style>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý hiển thị thông tin file khi chọn
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                
                fileName.innerHTML = `<i class="fas fa-file"></i> Tên file: ${file.name}`;
                fileSize.innerHTML = `<i class="fas fa-hdd"></i> Kích thước: ${sizeMB} MB`;
                fileInfo.style.display = 'block';
            } else {
                fileInfo.style.display = 'none';
            }
        });
    }

    // Validate form
    const form = document.getElementById('editForm');
    form.addEventListener('submit', function(e) {
        const fileInput = form.querySelector('input[name="fileDeThi"]');
        const titleInput = form.querySelector('input[name="tieuDe"]');
        
        // Kiểm tra tiêu đề
        if (titleInput.value.trim().length < 5) {
            e.preventDefault();
            alert('Tiêu đề đề thi phải có ít nhất 5 ký tự');
            titleInput.focus();
            return false;
        }
        
        // Kiểm tra file nếu có upload
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedExt = ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png'];
            const fileExt = '.' + file.name.split('.').pop().toLowerCase();
            
            // Kiểm tra kích thước
            if (file.size > maxSize) {
                e.preventDefault();
                alert('File không được vượt quá 10MB');
                return false;
            }
            
            // Kiểm tra định dạng
            if (!allowedExt.includes(fileExt)) {
                e.preventDefault();
                alert('Chỉ chấp nhận file PDF, Word hoặc hình ảnh');
                return false;
            }
        }
        
        // Hiển thị loading
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
        
        return true;
    });
    
    // Xử lý modal xóa
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function() {
            console.log('Delete modal opening...');
        });
    }
    
    // Kiểm tra trước khi đóng trang
    let formChanged = false;
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            formChanged = true;
        });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'Bạn có thay đổi chưa lưu. Bạn có chắc muốn rời đi?';
        }
    });
    
    // Vô hiệu hóa kiểm tra khi submit form
    form.addEventListener('submit', () => {
        formChanged = false;
    });
});
</script>