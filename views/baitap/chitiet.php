<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Chi tiết bài tập</strong></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">
                <?= htmlspecialchars($baiTap['tenBT']) ?>
            </h5>
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
                <?= $baiTap['moTa'] ?>
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
            <?php 
                else:
                    echo '<p class="text-muted">Không có file đính kèm!</p>';
                endif; 
            else:
                echo '<p class="text-muted">Không có file đính kèm!</p>';
            endif; 
            ?>
        </div> 
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-info"><strong>Tiến độ nộp bài</strong></h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3 col-6">
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $thongKe['siSo'] ?></div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sĩ số</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $thongKe['tongDaNop'] ?></div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đã nộp</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $thongKe['nopTre'] ?></div>
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Nộp trễ</div>
                </div>
                 <div class="col-md-3 col-6">
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $thongKe['chuaNop'] ?></div>
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Chưa nộp</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-info"><strong>Danh sách học sinh đã nộp</strong></h5>
            
            <?php if (!empty($danhSachNopBai)): ?>
                <a href="index.php?controller=baitap&action=taiTatCaBaiNop&maBaiTap=<?= $baiTap['maBaiTap'] ?>" 
                    class="btn btn-sm btn-success">
                    <i class="fas fa-archive"></i> Tải về tất cả (ZIP)
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (empty($danhSachNopBai)): ?>
                <p class="text-muted text-center">Chưa có học sinh nào nộp bài!</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="tableNopBai">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên Học sinh</th>
                                <th>Ngày nộp</th>
                                <th>Trạng thái</th>
                                <th>File bài nộp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $stt = 1; ?>
                            <?php foreach ($danhSachNopBai as $baiNop): ?>
                            <tr>
                                <td><?= $stt++ ?></td>
                                <td><strong><?= htmlspecialchars($baiNop['tenHocSinh']) ?></strong></td>
                                <td><?= date('d/m/Y H:i', strtotime($baiNop['ngayNop'])) ?></td>
                                <td>
                                    <?php
                                    if ($baiNop['trangThai'] === 'Đã nộp') {
                                        echo '<span class="badge badge-success" style="font-size: 0.9rem; color: green">Đã nộp</span>';
                                    } elseif ($baiNop['trangThai'] === 'Nộp trễ') {
                                        echo '<span class="badge badge-warning" style="font-size: 0.9rem; color: #e77f0f">Nộp trễ</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $filesHS = json_decode($baiNop['fileDinhKem'], true);
                                    if (isset($filesHS['duongDan'])) { $filesHS = [$filesHS]; }

                                    foreach ($filesHS as $fileInfo):
                                        $fileSizeMB = round($fileInfo['kichThuoc'] / 1024 / 1024, 2);
                                    ?>
                                    <a href="<?= htmlspecialchars($fileInfo['duongDan']) ?>" download="<?= htmlspecialchars($fileInfo['tenFile']) ?>" 
                                        class="btn btn-sm btn-outline-primary mb-1">
                                        <i class="fas fa-download mr-1"></i>
                                        <?= htmlspecialchars($fileInfo['tenFile']) ?> (<?= $fileSizeMB ?> MB)
                                    </a><br>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>