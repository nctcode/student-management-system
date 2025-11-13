<?php
// views/thongbao/dangthongbao.php
$title = $data['title'] ?? 'Đăng Thông Báo';
$danhSachLop = $data['danhSachLop'] ?? [];
$danhSachKhoi = $data['danhSachKhoi'] ?? [];
$danhSachMonHoc = $data['danhSachMonHoc'] ?? [];
?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">📢 Đăng thông báo mới</h2>
            <p class="text-muted mb-0">Tạo và gửi thông báo đến các đối tượng trong hệ thống</p>
        </div>
        <a href="index.php?controller=thongbao&action=danhsach" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Notification Form Card -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white py-4 border-bottom-0">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-edit me-2"></i>Thông tin thông báo
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form id="formThongBao" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <!-- Tiêu đề -->
                        <div class="mb-4">
                            <label for="tieuDe" class="form-label fw-semibold">
                                <i class="fas fa-heading me-2 text-primary"></i>Tiêu đề thông báo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="tieuDe" name="tieuDe" 
                                   placeholder="Nhập tiêu đề thông báo..." required>
                            <div class="invalid-feedback">Vui lòng nhập tiêu đề thông báo.</div>
                            <div class="form-text">Tiêu đề nên ngắn gọn và mô tả rõ nội dung thông báo.</div>
                        </div>

                        <!-- Nội dung -->
                        <div class="mb-4">
                            <label for="noiDung" class="form-label fw-semibold">
                                <i class="fas fa-align-left me-2 text-primary"></i>Nội dung thông báo <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="noiDung" name="noiDung" rows="8" 
                                      placeholder="Nhập nội dung chi tiết của thông báo..." required></textarea>
                            <div class="invalid-feedback">Vui lòng nhập nội dung thông báo.</div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="form-text">Nội dung sẽ được hiển thị đầy đủ cho người nhận.</div>
                                <small class="text-muted" id="charCount">0 ký tự</small>
                            </div>
                        </div>

                        <!-- Cấu hình gửi -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="nguoiNhan" class="form-label fw-semibold">
                                    <i class="fas fa-users me-2 text-primary"></i>Đối tượng nhận
                                </label>
                                <select class="form-select" id="nguoiNhan" name="nguoiNhan">
                                    <option value="TAT_CA">🎯 Tất cả mọi người</option>
                                    <option value="HOC_SINH">👨‍🎓 Học sinh</option>
                                    <option value="PHU_HUYNH">👨‍👩‍👧‍👦 Phụ huynh</option>
                                    <option value="GIAO_VIEN">👨‍🏫 Giáo viên</option>
                                    <option value="QTV">👨‍🏫 Quản trị viên</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="uuTien" class="form-label fw-semibold">
                                    <i class="fas fa-exclamation-circle me-2 text-primary"></i>Mức độ ưu tiên
                                </label>
                                <select class="form-select" id="uuTien" name="uuTien">
                                    <option value="THAP">💤 Ưu tiên thấp</option>
                                    <option value="TRUNG_BINH" selected>💡 Ưu tiên trung bình</option>
                                    <option value="CAO">⚠️ Ưu tiên cao</option>
                                    <option value="KHAN_CAP">🚨 Khẩn cấp</option>
                                </select>
                            </div>
                        </div>

                        <!-- Phân loại chi tiết -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-tags me-2 text-primary"></i>Phân loại chi tiết (Tùy chọn)
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="loaiThongBao" class="form-label">Loại thông báo</label>
                                        <select class="form-select" id="loaiThongBao" name="loaiThongBao">
                                            <option value="CHUNG">📢 Thông báo chung</option>
                                            <option value="LOP">🏫 Thông báo lớp học</option>
                                            <option value="MON_HOC">📚 Thông báo môn học</option>
                                            <option value="KHOA_HOC">🎓 Thông báo khóa học</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="maLop" class="form-label">Lớp học</label>
                                        <select class="form-select" id="maLop" name="maLop">
                                            <option value="">-- Chọn lớp --</option>
                                            <?php foreach ($danhSachLop as $lop): ?>
                                                <option value="<?php echo $lop['maLop']; ?>">
                                                    <?php echo htmlspecialchars($lop['tenLop']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="maKhoi" class="form-label">Khối</label>
                                        <select class="form-select" id="maKhoi" name="maKhoi">
                                            <option value="">-- Chọn khối --</option>
                                            <?php foreach ($danhSachKhoi as $khoi): ?>
                                                <option value="<?php echo $khoi['maKhoi']; ?>">
                                                    <?php echo htmlspecialchars($khoi['tenKhoi']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="maMonHoc" class="form-label">Môn học</label>
                                        <select class="form-select" id="maMonHoc" name="maMonHoc">
                                            <option value="">-- Chọn môn học --</option>
                                            <?php foreach ($danhSachMonHoc as $monHoc): ?>
                                                <option value="<?php echo $monHoc['maMonHoc']; ?>">
                                                    <?php echo htmlspecialchars($monHoc['tenMonHoc']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thời gian hiển thị -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-clock me-2 text-primary"></i>Cài đặt thời gian (Tùy chọn)
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="thoiGianKetThuc" class="form-label">Thời gian kết thúc hiển thị</label>
                                        <input type="datetime-local" class="form-control" id="thoiGianKetThuc" name="thoiGianKetThuc">
                                        <div class="form-text">Thông báo sẽ tự động ẩn sau thời gian này.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File đính kèm -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-paperclip me-2 text-primary"></i>File đính kèm
                                </h6>
                                <div class="file-upload-area">
                                    <div class="file-upload-box border-dashed rounded-3 p-4 text-center">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted mb-2">Kéo thả file vào đây hoặc click để chọn</h6>
                                        <p class="text-muted small mb-3">Hỗ trợ: PDF, DOC, DOCX, JPG, PNG (Tối đa 5MB)</p>
                                        <input type="file" class="form-control d-none" id="fileDinhKem" name="fileDinhKem" 
                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileDinhKem').click()">
                                            <i class="fas fa-folder-open me-2"></i>Chọn file
                                        </button>
                                    </div>
                                    <div id="filePreview" class="mt-3 d-none">
                                        <div class="file-preview-card d-flex align-items-center justify-content-between p-3 border rounded">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf fa-2x text-danger me-3 file-icon"></i>
                                                <div>
                                                    <h6 class="mb-1 file-name">Tên file sẽ hiển thị ở đây</h6>
                                                    <small class="text-muted file-size">Kích thước file</small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sendImmediately" checked>
                                <label class="form-check-label" for="sendImmediately">
                                    Gửi ngay lập tức
                                </label>
                            </div>
                            <div class="d-flex gap-3">
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>Làm mới
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane me-2"></i>Đăng thông báo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="card shadow border-0 mt-4">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-eye me-2 text-primary"></i>Xem trước thông báo
                    </h6>
                </div>
                <div class="card-body">
                    <div id="notificationPreview" class="preview-content p-3 border rounded bg-light">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-newspaper fa-3x mb-3"></i>
                            <p>Nội dung xem trước sẽ hiển thị ở đây</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control-lg {
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 1.1rem;
}

.form-control, .form-select {
    border-radius: 10px;
    padding: 10px 14px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
}

.border-dashed {
    border: 2px dashed #dee2e6 !important;
}

.file-upload-box {
    background-color: #fafbfc;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-box:hover {
    background-color: #f0f7ff;
    border-color: #007bff !important;
}

.file-preview-card {
    background: white;
    transition: transform 0.2s ease;
}

.file-preview-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.preview-content {
    min-height: 200px;
    background: white;
}

.card {
    border-radius: 16px;
}

.card-header {
    border-radius: 16px 16px 0 0 !important;
}

.btn {
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
}

.was-validated .form-control:invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6.4.4.4-.4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.was-validated .form-control:valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.bg-light-blue {
    background-color: #f0f7ff !important;
}
</style>

<!-- THÊM JQUERY TRƯỚC KHI SỬ DỤNG -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Sử dụng jQuery.noConflict() để tránh xung đột
(function($) {
    'use strict';

    // Biến để theo dõi trạng thái
    let isSubmitting = false;

    $(document).ready(function() {
        // Character count for content
        $('#noiDung').on('input', function() {
            const charCount = $(this).val().length;
            $('#charCount').text(charCount + ' ký tự');
            updatePreview();
        });

        // File upload handling - SỬA LẠI
        $('#fileDinhKem').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Kiểm tra kích thước file
                if (file.size > 5 * 1024 * 1024) {
                    showAlert('danger', 'File không được vượt quá 5MB');
                    $(this).val('');
                    return;
                }
                
                // Kiểm tra loại file
                const allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                const fileExtension = file.name.split('.').pop().toLowerCase();
                if (!allowedTypes.includes(fileExtension)) {
                    showAlert('danger', 'Chỉ chấp nhận file PDF, DOC, DOCX, JPG, PNG');
                    $(this).val('');
                    return;
                }
                
                const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                $('#filePreview .file-name').text(file.name);
                $('#filePreview .file-size').text(fileSize + ' MB');
                
                // Hiển thị icon phù hợp với loại file
                let fileIcon = 'fa-file';
                let iconColor = 'text-secondary';
                if (fileExtension === 'pdf') {
                    fileIcon = 'fa-file-pdf';
                    iconColor = 'text-danger';
                } else if (['doc', 'docx'].includes(fileExtension)) {
                    fileIcon = 'fa-file-word';
                    iconColor = 'text-primary';
                } else if (['jpg', 'jpeg', 'png'].includes(fileExtension)) {
                    fileIcon = 'fa-file-image';
                    iconColor = 'text-success';
                }
                
                $('#filePreview .file-icon').removeClass().addClass(`fas ${fileIcon} ${iconColor} fa-2x me-3`);
                
                $('#filePreview').removeClass('d-none');
                updatePreview();
            }
        });

        // Real-time preview update - SỬA LẠI: Sử dụng setTimeout để tránh call stack
        function debouncedUpdatePreview() {
            clearTimeout(window.previewTimeout);
            window.previewTimeout = setTimeout(updatePreview, 100);
        }

        $('#tieuDe, #noiDung, #nguoiNhan, #uuTien, #loaiThongBao, #maLop, #maKhoi, #maMonHoc').on('input change', debouncedUpdatePreview);

        // Form validation and submission
        $('#formThongBao').on('submit', function(e) {
            e.preventDefault();
            
            if (this.checkValidity() === false) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                return;
            }

            submitForm();
        });

        // Drag and drop file upload - SỬA LẠI
        const fileUploadBox = $('.file-upload-box')[0];
        
        if (fileUploadBox) {
            fileUploadBox.addEventListener('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('border-primary bg-light-blue');
            });

            fileUploadBox.addEventListener('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('border-primary bg-light-blue');
            });

            fileUploadBox.addEventListener('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('border-primary bg-light-blue');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const fileInput = $('#fileDinhKem')[0];
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    fileInput.files = dt.files;
                    
                    // Kích hoạt sự kiện change
                    const event = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            });
        }

        // Click file upload box to trigger file input
        $('.file-upload-box').on('click', function() {
            $('#fileDinhKem').click();
        });

        // Xử lý khi thay đổi loại thông báo
        $('#loaiThongBao').on('change', function() {
            const loaiThongBao = $(this).val();
            
            // Ẩn/hiện các trường liên quan
            if (loaiThongBao === 'LOP') {
                $('#maLop').closest('.col-md-3').show();
                $('#maKhoi').closest('.col-md-3').show();
                $('#maMonHoc').closest('.col-md-3').hide();
                $('#maMonHoc').val('');
            } else if (loaiThongBao === 'MON_HOC') {
                $('#maLop').closest('.col-md-3').hide();
                $('#maKhoi').closest('.col-md-3').hide();
                $('#maMonHoc').closest('.col-md-3').show();
                $('#maLop').val('');
                $('#maKhoi').val('');
            } else {
                $('#maLop').closest('.col-md-3').show();
                $('#maKhoi').closest('.col-md-3').show();
                $('#maMonHoc').closest('.col-md-3').show();
            }
            
            updatePreview();
        });

        // Khởi tạo trạng thái ban đầu
        $('#loaiThongBao').trigger('change');
    });

    function removeFile() {
        $('#fileDinhKem').val('');
        $('#filePreview').addClass('d-none');
        updatePreview();
    }

    function updatePreview() {
        const title = $('#tieuDe').val() || 'Tiêu đề thông báo';
        const content = $('#noiDung').val() || 'Nội dung thông báo sẽ hiển thị ở đây...';
        const recipient = $('#nguoiNhan option:selected').text();
        const priority = $('#uuTien option:selected').text();
        const loaiThongBao = $('#loaiThongBao option:selected').text();
        const hasFile = $('#fileDinhKem').val() !== '';

        // Lấy thông tin lớp, khối, môn học nếu có
        const maLop = $('#maLop').val();
        const maKhoi = $('#maKhoi').val();
        const maMonHoc = $('#maMonHoc').val();
        
        let additionalInfo = '';
        if (maLop) {
            additionalInfo += `<span class="badge bg-secondary me-1"><i class="fas fa-door-open me-1"></i>Lớp: ${$('#maLop option:selected').text()}</span>`;
        }
        if (maKhoi) {
            additionalInfo += `<span class="badge bg-secondary me-1"><i class="fas fa-layer-group me-1"></i>Khối: ${$('#maKhoi option:selected').text()}</span>`;
        }
        if (maMonHoc) {
            additionalInfo += `<span class="badge bg-secondary me-1"><i class="fas fa-book me-1"></i>Môn: ${$('#maMonHoc option:selected').text()}</span>`;
        }

        const previewHtml = `
            <div class="preview-notification">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="text-primary mb-0">${title}</h5>
                    <div class="badge bg-warning">Xem trước</div>
                </div>
                <div class="content-preview mb-3">
                    <p class="mb-0" style="white-space: pre-wrap;">${content}</p>
                </div>
                <div class="preview-meta d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-users me-1"></i>${recipient}
                    </span>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-tag me-1"></i>${loaiThongBao}
                    </span>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-exclamation-circle me-1"></i>${priority}
                    </span>
                    ${hasFile ? '<span class="badge bg-info text-white"><i class="fas fa-paperclip me-1"></i>Có file đính kèm</span>' : ''}
                </div>
                ${additionalInfo ? `<div class="additional-info mb-3">${additionalInfo}</div>` : ''}
                <div class="preview-footer text-muted small">
                    <i class="fas fa-clock me-1"></i>Xem trước • ${new Date().toLocaleString('vi-VN')}
                </div>
            </div>
        `;

        $('#notificationPreview').html(previewHtml);
    }

    function submitForm() {
        // Ngăn chặn submit nhiều lần
        if (isSubmitting) {
            return;
        }
        
        const formData = new FormData($('#formThongBao')[0]);
        
        // Debug form data
        console.log("=== FORM DATA ===");
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Kiểm tra các trường bắt buộc
        const tieuDe = $('#tieuDe').val();
        const noiDung = $('#noiDung').val();
        
        if (!tieuDe.trim()) {
            showAlert('danger', 'Vui lòng nhập tiêu đề thông báo');
            $('#tieuDe').focus();
            return;
        }

        if (!noiDung.trim()) {
            showAlert('danger', 'Vui lòng nhập nội dung thông báo');
            $('#noiDung').focus();
            return;
        }
        
        // Show loading state
        isSubmitting = true;
        const submitBtn = $('#formThongBao').find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang đăng...');
        
        $.ajax({
            url: 'index.php?controller=thongbao&action=xulydangthongbao',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("=== AJAX RESPONSE ===");
                console.log("Raw response:", response);
                
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (result.success) {
                        showAlert('success', result.message);
                        console.log("Success, redirecting...");
                        
                        // Reset form
                        $('#formThongBao')[0].reset();
                        $('#formThongBao').removeClass('was-validated');
                        $('#filePreview').addClass('d-none');
                        updatePreview();
                        
                        // Chuyển hướng sau 1.5 giây
                        setTimeout(() => {
                            window.location.href = 'index.php?controller=thongbao&action=danhsach';
                        }, 1500);
                    } else {
                        showAlert('danger', result.message || 'Đăng thông báo thất bại');
                    }
                } catch (e) {
                    console.error("JSON parse error:", e);
                    console.log("Raw response:", response);
                    showAlert('danger', 'Lỗi xử lý dữ liệu từ server');
                }
            },
            error: function(xhr, status, error) {
                console.error("=== AJAX ERROR ===");
                console.error("Status:", status);
                console.error("Error:", error);
                console.log("XHR response:", xhr.responseText);
                
                let errorMessage = 'Lỗi kết nối: ';
                if (xhr.status === 0) {
                    errorMessage += 'Không thể kết nối đến server';
                } else if (xhr.status === 500) {
                    errorMessage += 'Lỗi server (500)';
                } else {
                    errorMessage += error;
                }
                
                showAlert('danger', errorMessage);
            },
            complete: function() {
                // Reset trạng thái submit
                isSubmitting = false;
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    function showAlert(type, message) {
        // Tạo alert element thủ công thay vì dùng Bootstrap alert function
        const alertId = 'alert-' + Date.now();
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                 style="z-index: 1060; min-width: 400px;" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    <div>${message}</div>
                    <button type="button" class="btn-close ms-auto" onclick="document.getElementById('${alertId}').remove()"></button>
                </div>
            </div>
        `;
        
        // Xóa alert cũ nếu có
        $('.alert.position-fixed').remove();
        
        // Thêm alert mới
        $('body').append(alertHtml);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            $('#' + alertId).remove();
        }, 5000);
    }

    // Initialize preview on page load
    updatePreview();

})(jQuery);
</script>