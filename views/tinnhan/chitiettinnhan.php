<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Chi tiết tin nhắn</strong></h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h3 class="m-0 font-weight-bold text-primary">
                        <?= htmlspecialchars($chiTietHoiThoai['tenHoiThoai'] ?? 'Tin nhắn') ?>
                    </h3>
                    <a href="index.php?controller=tinnhan&action=index" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
                <div class="card-body">
                    <!-- Khu vực hiển thị tin nhắn -->
                    <div id="khungTinNhan" style="height: 500px; overflow-y: auto; border: 1px solid #e3e6f0; border-radius: 5px; padding: 15px; margin-bottom: 20px; background-color: #f8f9fc;">
                        <?php if (empty($tinNhan)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-comments fa-2x mb-3"></i>
                                <p>Chưa có tin nhắn nào trong hội thoại này</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($tinNhan as $tn): ?>
                            <div class="mb-4 <?= $tn['maNguoiDung'] == $_SESSION['user']['maNguoiDung'] ? 'text-right' : '' ?>">
                                <div class="d-flex <?= $tn['maNguoiDung'] == $_SESSION['user']['maNguoiDung'] ? 'justify-content-end' : 'justify-content-start' ?>">
                                    <div class="message-bubble <?= $tn['maNguoiDung'] == $_SESSION['user']['maNguoiDung'] ? 'bg-primary text-white' : 'bg-light' ?>" 
                                         style="max-width: 70%; padding: 12px 16px; border-radius: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                        
                                        <!-- Thông tin người gửi -->
                                        <?php if ($tn['maNguoiDung'] != $_SESSION['user']['maNguoiDung']): ?>
                                        <div class="font-weight-bold mb-1" style="font-size: 0.9em;">
                                            <?= htmlspecialchars($tn['nguoiGui']) ?>
                                            <small class="text-muted ml-2">(<?= $tn['vaiTro'] ?>)</small>
                                        </div>
                                        <?php else: ?>
                                        <div class="font-weight-bold mb-1 text-right" style="font-size: 0.9em;">
                                            Bạn (<?= htmlspecialchars($tn['nguoiGui']) ?>)
                                        </div>
                                        <?php endif; ?>

                                        <!-- Nội dung tin nhắn -->
                                        <div class="mb-2"><?= nl2br(htmlspecialchars($tn['noiDung'])) ?></div>

                                        <!-- File đính kèm -->
                                        <?php 
                                            if (!empty($tn['fileDinhKem'])): 
                                                $filesInfo = json_decode($tn['fileDinhKem'], true);
                                                if (isset($filesInfo['duongDan'])) {
                                                    $filesInfo = [$filesInfo];
                                                }
                                                if (is_array($filesInfo)):
                                                    foreach ($filesInfo as $fileInfo): // Lặp qua từng file
                                                        if (empty($fileInfo['duongDan'])) continue; // Bỏ qua nếu file không hợp lệ
                                            ?>
                                            <div class="file-attachment mt-2 p-2 border rounded" style="background: rgba(255,255,255,0.1);">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-paperclip mr-2"></i>
                                                    <div class="flex-grow-1">
                                                        <div class="font-weight-bold"><?= htmlspecialchars($fileInfo['tenFile']) ?></div>
                                                        <small class="text-muted">
                                                            <?= round($fileInfo['kichThuoc'] / 1024 / 1024, 2) ?> MB
                                                        </small>
                                                    </div>
                                                    <a href="<?= htmlspecialchars($fileInfo['duongDan']) ?>" download class="btn btn-sm <?= $tn['maNguoiDung'] == $_SESSION['user']['maNguoiDung'] ? 'btn-light' : 'btn-primary' ?> ml-2">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php 
                                                    endforeach; // Kết thúc vòng lặp file
                                                endif; 
                                            endif; 
                                            ?>

                                        <!-- Thời gian -->
                                        <div class="text-end" style="font-size: 0.8em; margin-top: 5px;">
                                            <small class="<?= $tn['maNguoiDung'] == $_SESSION['user']['maNguoiDung'] ? 'text-light' : 'text-muted' ?>">
                                                <?= date('H:i d/m/Y', strtotime($tn['thoiGianGui'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Form gửi tin nhắn mới -->
                    <form method="POST" enctype="multipart/form-data" id="formGuiTinNhan">
                        <div class="form-group">
                            <label><strong>Tin nhắn mới</strong></label>
                            <textarea name="noiDung" class="form-control" rows="3" 
                                      placeholder="Nhập tin nhắn của bạn..." 
                                      onkeyup="demKyTu(this)" required></textarea>
                            <small class="form-text text-muted">
                                <span id="soKyTu">0</span>/1000 ký tự
                            </small>
                        </div>

                        <div class="form-group">
                            <label><strong>Đính kèm file</strong></label>
                            <div id="danhSachFile" class="mb-2">
                                <!-- Danh sách file sẽ hiển thị ở đây -->
                            </div>
                            <input type="file" name="fileDinhKem[]" id="fileDinhKem" class="form-control-file" 
                                onchange="hienThiFile()" multiple>
                            <br>
                            <small class="form-text text-muted">
                                • File đính kèm tối đa 10MB<br>
                                • Định dạng hỗ trợ: PDF, DOC, JPG, PNG, XLSX<br>
                                • Không gửi nội dung không phù hợp
                            </small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-danger btn-lg" onclick="history.back()">
                                <i class="fas fa-times"></i> Hủy
                            </button>
                            <button type="submit" class="btn btn-success btn-lg ms-2">
                                <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tự động scroll xuống cuối khung tin nhắn
function scrollToBottom() {
    const khungTinNhan = document.getElementById('khungTinNhan');
    khungTinNhan.scrollTop = khungTinNhan.scrollHeight;
}

// Đếm ký tự
function demKyTu(textarea) {
    const soKyTu = document.getElementById('soKyTu');
    soKyTu.textContent = textarea.value.length;
    
    if (textarea.value.length > 1000) {
        soKyTu.className = 'text-danger';
        textarea.classList.add('is-invalid');
    } else {
        soKyTu.className = 'text-muted';
        textarea.classList.remove('is-invalid');
    }
}

// Hiển thị file đính kèm
function hienThiFile() {
    const fileInput = document.getElementById('fileDinhKem');
    const fileList = document.getElementById('danhSachFile');
    fileList.innerHTML = '';
    
    for (let i = 0; i < fileInput.files.length; i++) {
        const file = fileInput.files[i];
        const fileSize = (file.size / (1024 * 1024)).toFixed(1);
        
        const fileItem = document.createElement('div');
        fileItem.className = 'd-flex justify-content-between align-items-center border rounded p-2 mb-2 bg-light';
        fileItem.innerHTML = `
            <div>
                <i class="fas fa-file mr-2"></i>
                <strong>${file.name}</strong> (${fileSize}MB)
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="xoaFile(${i})">
                <i class="fas fa-times"></i>
            </button>
        `;
        fileList.appendChild(fileItem);
    }
}

// Xóa file
function xoaFile(index) {
    const fileInput = document.getElementById('fileDinhKem');
    const files = Array.from(fileInput.files);
    files.splice(index, 1);
    
    const dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
    
    hienThiFile();
}

// Xử lý submit form
document.getElementById('formGuiTinNhan').addEventListener('submit', function(e) {
    const textarea = this.querySelector('textarea[name="noiDung"]');
    if (textarea.value.length > 1000) {
        e.preventDefault();
        alert('Tin nhắn không được vượt quá 1000 ký tự!');
        return;
    }
    
    // Hiển thị loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    submitBtn.disabled = true;
});

// Tự động scroll khi trang load
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    
    // Focus vào textarea
    document.querySelector('textarea[name="noiDung"]').focus();
});

// Auto-refresh tin nhắn mỗi 30 giây (tùy chọn)
setInterval(function() {
}, 30000);
</script>

<style>
    .message-bubble.bg-primary {
    background-color: #cfe2ff !important; 
    color: #000 !important; 
}

.message-bubble.bg-primary small.text-light {
    color: #495057 !important; 
}

.message-bubble.bg-primary:after {
    border-left-color: #cfe2ff !important; 
}

.message-bubble.bg-primary .file-attachment {
    background: rgba(255,255,255,0.4) !important;
}

.message-bubble.bg-primary .file-attachment a.btn-light {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}
</style>