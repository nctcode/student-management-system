<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Bài tập đã giao</strong></h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Danh sách bài tập</h5>
            <a href="index.php?controller=baitap&action=index" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Giao bài tập mới
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($danhSachBaiTap)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-book-reader fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Bạn chưa giao bài tập nào!</p>
                    <a href="index.php?controller=baitap&action=index" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Giao bài tập ngay
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên bài tập</th>
                                <th>Lớp</th>
                                <th>Môn</th>
                                <th>Ngày giao</th>
                                <th>Hạn nộp</th>
                                <th>Đính kèm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $stt = 1; ?>
                            <?php foreach ($danhSachBaiTap as $bt): ?>
                            <tr>
                                <td><?= $stt++ ?></td>
                                <td>
                                    <a href="index.php?controller=baitap&action=chitiet&maBaiTap=<?= $bt['maBaiTap'] ?>">
                                        <?= htmlspecialchars($bt['tenBT']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($bt['tenLop']) ?></td>
                                <td><?= htmlspecialchars($bt['tenMonHoc']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($bt['ngayGiao'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($bt['hanNop'])) ?></td>
                                <td>
                                    <?php 
                                    if (!empty($bt['fileDinhKem'])): 
                                        $filesInfo = json_decode($bt['fileDinhKem'], true);
                                        $displayName = null;
                                        $extraFiles = 0;
                                    
                                        if (is_array($filesInfo)) {
                                            if (isset($filesInfo[0]['tenFile'])) {
                                                $displayName = $filesInfo[0]['tenFile'];
                                                if (count($filesInfo) > 1) {
                                                    $extraFiles = count($filesInfo) - 1;
                                                }
                                            } 
                                            elseif (isset($filesInfo['tenFile'])) {
                                                $displayName = $filesInfo['tenFile'];
                                            }
                                        }
                                    
                                        if ($displayName): 
                                    ?>
                                    <small class="text-primary">
                                        <i class="fas fa-paperclip"></i> <?= htmlspecialchars($displayName) ?>
                                        <?php if ($extraFiles > 0): ?>
                                            <span class="badge badge-pill badge-secondary ml-1">+<?= $extraFiles ?></span>
                                        <?php endif; ?>
                                    </small>
                                    <?php 
                                        endif; 
                                    else:
                                        echo '<small class="text-muted">Không có</small>';
                                    endif; 
                                    ?>
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

<link rel="stylesheet" href="assets/css/baitap.css">