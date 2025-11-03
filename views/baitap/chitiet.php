<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Chi tiết bài tập</strong></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">
                <?= htmlspecialchars($baiTap['tenBT']) ?>
            </h4>
            <a href="index.php?controller=baitap&action=danhsach" class="btn btn-secondary btn-sm">
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
                <?= nl2br(htmlspecialchars($baiTap['moTa'])) ?>
            </div>

            <hr>
            <h6><strong>Tài liệu đính kèm:</strong></h6>
            <?php 
            if (!empty($baiTap['fileDinhKem'])): 
                $filesInfo = json_decode($baiTap['fileDinhKem'], true);

                if (isset($filesInfo['duongDan'])) { 
                    $filesInfo = [$filesInfo]; 
                }
                
                if (is_array($filesInfo) && !empty($filesInfo[0])):
            ?>
                <div class="list-group">
                <?php foreach ($filesInfo as $fileInfo): 
                    if (empty($fileInfo['duongDan'])) continue;
                    $fileSizeMB = round($fileInfo['kichThuoc'] / 1024 / 1024, 2);
                ?>
                    <a href="<?= htmlspecialchars($fileInfo['duongDan']) ?>" download class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-download mr-2"></i>
                            <strong><?= htmlspecialchars($fileInfo['tenFile']) ?></strong>
                        </div>
                        <span class="badge badge-primary badge-pill"><?= $fileSizeMB ?> MB</span>
                    </a>
                <?php endforeach; ?>
                </div>
            <?php 
                else:
                    echo '<p class="text-muted">Không có file đính kèm.</p>';
                endif; 
            else:
                echo '<p class="text-muted">Không có file đính kèm.</p>';
            endif; 
            ?>
        </div> 
    </div>
</div>