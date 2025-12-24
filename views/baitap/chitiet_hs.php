<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Chi tiết bài tập</strong></h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">
                <?= htmlspecialchars($baiTap['tenBT']) ?>
            </h5>
            <a href="index.php?controller=baitap&action=danhsach_hs" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Lớp:</strong> <?= htmlspecialchars($baiTap['tenLop']) ?></p>
                    <p><strong>Môn học:</strong> <?= htmlspecialchars($baiTap['tenMonHoc']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Người giao:</strong> <?= htmlspecialchars($baiTap['tenGiaoVien']) ?></p>
                    <p><strong>Ngày giao:</strong> <?= date('d/m/Y H:i', strtotime($baiTap['ngayGiao'])) ?></p>
                    <p><strong>Hạn nộp:</strong> <strong class="text-danger"><?= date('d/m/Y H:i', strtotime($baiTap['hanNop'])) ?></strong></p>
                </div>
            </div>
            <hr>
            <h6><strong>Mô tả bài tập:</strong></h6>
            <div class="p-3 bg-light rounded" style="min-height: 100px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;">
                <?= $baiTap['moTa'] ?>
            </div>
            <hr>
            <h6><strong>Tài liệu đính kèm (của giáo viên):</strong></h6>
            <?php 
            if (!empty($baiTap['fileDinhKem'])): 
                $filesInfo = json_decode($baiTap['fileDinhKem'], true);
                if (isset($filesInfo['duongDan'])) { $filesInfo = [$filesInfo]; }
                
                if (is_array($filesInfo) && !empty($filesInfo[0])):
            ?>
                <div class="list-group">
                <?php foreach ($filesInfo as $fileInfo): 
                    if (empty($fileInfo['duongDan'])) continue;
                    $fileSizeMB = round($fileInfo['kichThuoc'] / 1024 / 1024, 2);
                ?>
                    <a href="<?= htmlspecialchars($fileInfo['duongDan']) ?>" download="<?= htmlspecialchars($fileInfo['tenFile']) ?>"
                     class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-download mr-2"></i>
                            <strong><?= htmlspecialchars($fileInfo['tenFile']) ?></strong>
                        </div>
                        <span class="badge badge-primary badge-pill"><?= $fileSizeMB ?> MB</span>
                    </a>
                <?php endforeach; ?>
                </div>
            <?php else: echo '<p class="text-muted">Không có file đính kèm!</p>'; endif; 
            else: echo '<p class="text-muted">Không có file đính kèm!</p>'; endif; 
            ?>
        </div> 
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-success"><strong>Nộp bài tập</strong></h5>
        </div>
        <div class="card-body">
            <?php 
            $baiNop = $baiTap['baiNopCuaToi'];
            $hetHan = new DateTime() > new DateTime($baiTap['hanNop']);
            $filesNop = [];

            // ===== HIỂN THỊ BÀI ĐÃ NỘP (NẾU CÓ) =====
            if ($baiNop && !empty($baiNop['fileDinhKem'])): 
                $filesNop = json_decode($baiNop['fileDinhKem'], true);
                if (isset($filesNop['duongDan'])) { $filesNop = [$filesNop]; }
                if (!is_array($filesNop)) { $filesNop = []; }
            ?>
                <div class="hop-bai-nop mb-4" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 1.25rem; border-radius: 0.25rem;">
                    <h4 style="color: #155724;">Bài làm của bạn</h4>
                    <p>
                        <strong>Ngày nộp:</strong> <?= date('d/m/Y H:i', strtotime($baiNop['ngayNop'])) ?> <br>
                        <strong>Trạng thái:</strong> 
                        <?php
                        if ($baiNop['trangThai'] === 'Đã nộp') {
                            echo '<span class="badge badge-success" style="font-size: 0.9rem; color: green">Đã nộp (Đúng hạn)</span>';
                        } elseif ($baiNop['trangThai'] === 'Nộp trễ') {
                            echo '<span class="badge badge-warning" style="font-size: 0.9rem; color: #e77f0f">Nộp trễ</span>';
                        }
                        ?>
                    </p>
                    <hr>
                    <p class="mb-0"><strong>File bạn đã nộp:</strong></p>
                    <div class="list-group mt-2">
                    <?php if (empty($filesNop)): ?>
                        <p class="text-muted" style="color: #155724 !important;">Không có file nào!</p>
                    <?php else: ?>
                        <?php foreach ($filesNop as $key => $fileInfo): 
                            if (empty($fileInfo['duongDan'])) continue;
                            $fileSizeMB = round($fileInfo['kichThuoc'] / 1024 / 1024, 2);
                        ?>
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-download mr-2"></i>
                                <a href="<?= htmlspecialchars($fileInfo['duongDan']) ?>" download="<?= htmlspecialchars($fileInfo['tenFile']) ?>">
                                    <strong><?= htmlspecialchars($fileInfo['tenFile']) ?></strong>
                                </a>
                                <small class="text-muted">(<?= $fileSizeMB ?> MB)</small>
                            </div>
                            <a href="javascript:void(0)" 
                                class="btn btn-sm btn-outline-danger btn-xoa-file" 
                                data-url="index.php?controller=baitap&action=xoaFileNop&maBaiTap=<?= $baiTap['maBaiTap'] ?>&key=<?= $key ?>">
                                <i class="fas fa-times"></i> Xóa
                            </a>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php 
                if ($hetHan): 
                ?>
                    <div style="background-color: #fef1d6ff; color: #dfb737ff; border: 1px solid #f9f5d1ff; padding: 1.25rem; border-radius: 0.25rem;">
                        <strong>Đã hết hạn nộp bài!</strong> 
                        Mọi file nộp hoặc xóa bây giờ sẽ được ghi nhận là "Nộp trễ"!
                    </div>
                <?php endif; ?>

                <h5 class="font-weight-bold mt-4">
                    <?php if (!empty($filesNop)): ?>
                        <i class="fas fa-plus text-primary"></i> Thêm file
                    <?php else: ?>
                        <i class="fas fa-upload text-success"></i> Tải bài làm lên
                    <?php endif; ?>
                </h5>
                
                <form method="POST" action="index.php?controller=baitap&action=nopbai" id="formNopBaiTap" enctype="multipart/form-data">
                    <input type="hidden" name="maBaiTap" value="<?= $baiTap['maBaiTap'] ?>">
                    
                    <div class="form-group">
                        <div id="danhSachFile" class="mb-2"></div>
                        <input type="file" name="fileDinhKem[]" id="fileDinhKem" class="form-control-file" 
                            onchange="hienThiFile()" multiple>
                        <br>
                        <small class="form-text text-muted">
                            • Có thể đính kèm nhiều file (tối đa 20MB).
                            <br>• Định dạng: pdf, doc, jpg, png, xlsx, mp4, zip...
                        </small>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-danger btn-lg" onclick="window.location.href='index.php'">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                        <button type="submit" class="btn btn-success btn-lg ms-2">
                            <i class="fas fa-paper-plane"></i> 
                            <?php if ($hetHan): ?>
                                <?= (!empty($filesNop)) ? 'Thêm file (Nộp trễ)' : 'Nộp bài (Nộp trễ)' ?>
                            <?php else: ?>
                                <?= (!empty($filesNop)) ? 'Thêm file' : 'Nộp bài' ?>
                            <?php endif; ?>
                        </button>
                    </div>
                </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmXoaFile" data-backdrop="static" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Xác nhận xóa
                </h5>
                <button type="button" class="btn-close-custom" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <p class="mb-0 text-secondary">Bạn có chắc chắn muốn xóa file này không? <br> Hành động này sẽ không thể hoàn tác!</p>
            </div>
            <div class="modal-footer border-0 py-3 justify-content-center">
                <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fas fa-times-circle mr-1"></i> Hủy
                </button>
                <button type="button" id="btnConfirmDeleteFile" class="btn btn-danger px-4 rounded-pill shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> Xác nhận xóa
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLoiNopBai" data-backdrop="static" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-exclamation-circle mr-2"></i> Thông báo
                </h5>
                <button type="button" class="btn-close-custom" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <i class="fas fa-file-excel fa-3x text-danger mb-3"></i>
                <p id="msgLoiNopBai" class="mb-0 text-secondary font-weight-bold"></p>
            </div>
            <div class="modal-footer border-0 py-3 justify-content-center">
                <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fas fa-check-circle mr-1"></i> Đã hiểu
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="assets/css/baitap.css">

<script>
    const MAX_FILE_SIZE_HS = 20 * 1024 * 1024;
    const ALLOWED_EXTENSIONS_HS = [
        'pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx', 'xls', 
        'mp4', 'mov', 'avi', 'mp3', 'zip', 'rar', 'txt', 'ppt', 'pptx'
    ];

    function getFileExtensionHs(filename) {
        return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2).toLowerCase();
    }

    window.xoaFileTam = function(index) {
        const fileInput = document.getElementById('fileDinhKem');
        if (!fileInput) return;
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        hienThiFile();
    }

    window.hienThiFile = function() {
        const fileInput = document.getElementById('fileDinhKem');
        const fileList = document.getElementById('danhSachFile');
        if (!fileInput || !fileList) return;
        
        const dt = new DataTransfer();
        fileList.innerHTML = '';
        let errors = [];

        for (let i = 0; i < fileInput.files.length; i++) {
            const file = fileInput.files[i];
            const fileExt = getFileExtensionHs(file.name);
            let error = "";

            if (file.size === 0) error = "file rỗng";
            else if (file.size > MAX_FILE_SIZE_HS) error = "quá 20MB";
            else if (!ALLOWED_EXTENSIONS_HS.includes(fileExt)) error = `định dạng .${fileExt} không hỗ trợ`;

            if (error !== "") {
                errors.push(`<li>File <b>${file.name}</b> (${error})</li>`);
            } else {
                dt.items.add(file);
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                const fileItem = document.createElement('div');
                fileItem.className = 'd-flex justify-content-between align-items-center border rounded p-2 mb-2 bg-light';
                fileItem.innerHTML = `
                    <div><i class="fas fa-file mr-2 text-primary"></i><strong>${file.name}</strong> (${fileSizeMB}MB)</div>
                    <button type="button" class="btn btn-sm btn-danger" onclick="xoaFileTam(${dt.items.length - 1})">×</button>
                `;
                fileList.appendChild(fileItem);
            }
        }
        fileInput.files = dt.files;

        if (errors.length > 0) {
            const msgElement = document.getElementById('msgLoiNopBai');
            if (msgElement) {
                msgElement.innerHTML = `Đã loại bỏ ${errors.length} file không hợp lệ: <ul class='text-left mt-2'>${errors.join('')}</ul>`;
                new bootstrap.Modal(document.getElementById('modalLoiNopBai')).show();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        let deleteUrl = '';
        const modalConfirm = new bootstrap.Modal(document.getElementById('modalConfirmXoaFile'));
        const btnConfirm = document.getElementById('btnConfirmDeleteFile');

        document.querySelectorAll('.btn-xoa-file').forEach(btn => {
            btn.addEventListener('click', function() {
                deleteUrl = this.getAttribute('data-url');
                modalConfirm.show();
            });
        });

        btnConfirm?.addEventListener('click', function() {
            if (deleteUrl) {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xóa...';
                window.location.href = deleteUrl;
            }
        });

        window.showUploadError = function(message) {
            const modalElement = document.getElementById('modalLoiNopBai');
            const textElement = document.getElementById('msgLoiNopBai');
            if (modalElement && textElement) {
            textElement.innerText = message;
            $(modalElement).modal('show');
        } else {
            alert(message);
        }
    };
    const formNopBai = document.getElementById('formNopBaiTap');
    
    formNopBai?.addEventListener('submit', function(e) {
        const fileInput = document.getElementById('fileDinhKem');
        
        if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            showUploadError("Vui lòng chọn ít nhất một tệp tin hợp lệ trước khi nhấn nộp bài!");
            return false;
        }

        const btnSubmit = this.querySelector('button[type="submit"]');
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải lên...';
        }
    });
});
</script>