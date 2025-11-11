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
            <div class="p-3 bg-light rounded" style="min-height: 100px;">
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
                    <h4 style="color: #155724;">Bài làm của bạn (Đã nộp)</h4>
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
                            
                            <?php if (!$hetHan): ?>
                            <a href="index.php?controller=baitap&action=xoaFileNop&maBaiTap=<?= $baiTap['maBaiTap'] ?>&key=<?= $key ?>" 
                               class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa file này không?');">
                                <i class="fas fa-times"></i> Xóa
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>


            <?php if (!$hetHan): ?>
                
                <h5 class="font-weight-bold">
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
                               onchange="hienThiFile()" multiple required>
                        <br>
                        <small class="form-text text-muted">
                            • Bạn có thể đính kèm nhiều file (tối đa 20MB).
                        </small>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-danger btn-lg" onclick="history.back()">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                        <button type="submit" class="btn btn-success btn-lg ms-2">
                            <i class="fas fa-paper-plane"></i> 
                            <?= (!empty($filesNop)) ? 'Thêm file' : 'Nộp bài' ?>
                        </button>
                    </div>
                </form>

            <?php else: ?>
                
                <?php if (empty($filesNop)): // Đã hết hạn VÀ chưa nộp ?>
                <div style="color: red;">
                    <i>Đã hết hạn nộp bài. Bạn không thể nộp bài tập này.</i>
                </div>
                <?php else: // Đã hết hạn VÀ đã nộp ?>
                <div style="color: red;">
                    <i>Đã hết hạn nộp bài. Bạn không thể thêm hoặc xóa file.</i>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
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
    
    const maxSize = 20 * 1024 * 1024; 
    const files = fileInput.files;
    const dt = new DataTransfer(); 
    
    fileList.innerHTML = '';

    for (let i = 0; i < files.length; i++) {
        const file = files.item(i);
        const fileSize = file.size;
        
        let fileItem = document.createElement('div');
        fileItem.className = 'd-flex justify-content-between align-items-center border rounded p-2 mb-2';

        if (fileSize === 0) {
            fileItem.classList.add('bg-light');
            fileItem.innerHTML = `
                <div><i class="fas fa-exclamation-triangle text-danger mr-2"></i> 
                     <strong>${file.name}</strong> (File rỗng!)</div>
                <small class="text-danger">File bị lỗi, sẽ bị loại bỏ!</small>
            `;
        } 
        else if (fileSize > maxSize) {
            const sizeMB = (fileSize / (1024 * 1024)).toFixed(1);
            fileItem.classList.add('bg-light');
            fileItem.innerHTML = `
                <div><i class="fas fa-exclamation-triangle text-danger mr-2"></i> 
                     <strong>${file.name}</strong> (${sizeMB} MB)</div>
                <small class="text-danger">File quá 20MB, sẽ bị loại bỏ!</small>
            `;
        } 
        else {
            fileItem.classList.add('bg-light');
            
            let fileSizeText = (fileSize / (1024 * 1024)).toFixed(1) + " MB";
            if (fileSize < (1024 * 1024)) { 
                fileSizeText = (fileSize / 1024).toFixed(0) + " KB";
            }

            fileItem.innerHTML = `
                <div><i class="fas fa-file mr-2"></i> <strong>${file.name}</strong> (${fileSizeText})</div>
                <button type="button" class="btn btn-sm btn-danger" onclick="xoaFileTam(${i})">×</button>
            `;
            dt.items.add(file); 
        }
        fileList.appendChild(fileItem);
    }
    fileInput.files = dt.files;
}
</script>
