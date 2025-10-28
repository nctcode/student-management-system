<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tin nhắn</h1>
    
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
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách tin nhắn</h6>
                    <?php if (in_array($_SESSION['user']['vaiTro'], ['QTV', 'BGH', 'GIAOVIEN'])): ?>
                    <a href="index.php?controller=tinnhan&action=guitinnhan" class="btn btn-primary btn-sm">
                        <i class="fas fa-paper-plane"></i> Gửi tin nhắn mới
                    </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($tinNhan)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có tin nhắn nào</p>
                            <?php if (in_array($_SESSION['user']['vaiTro'], ['QTV', 'BGH', 'GIAOVIEN'])): ?>
                            <a href="index.php?controller=tinnhan&action=guitinnhan" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Gửi tin nhắn đầu tiên
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($tinNhan as $tn): ?>
                            <a href="index.php?controller=tinnhan&action=chitiettinnhan&maHoiThoai=<?= $tn['maHoiThoai'] ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-1 font-weight-bold">
                                                <?= htmlspecialchars($tn['tenHoiThoai']) ?>
                                                <?php if ($tn['soTinChuaDoc'] > 0): ?>
                                                <span class="badge badge-danger ml-2"><?= $tn['soTinChuaDoc'] ?> mới</span>
                                                <?php endif; ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?= date('H:i d/m/Y', strtotime($tn['thoiGianGui'])) ?>
                                            </small>
                                        </div>
                                        <p class="mb-1 text-truncate"><?= htmlspecialchars($tn['noiDung']) ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?= htmlspecialchars($tn['nguoiGui']) ?>
                                            </small>
                                            <?php if (!empty($tn['fileDinhKem'])): 
                                                $fileInfo = json_decode($tn['fileDinhKem'], true);
                                                if ($fileInfo): ?>
                                            <small class="text-primary">
                                                <i class="fas fa-paperclip"></i> <?= htmlspecialchars($fileInfo['tenFile']) ?>
                                            </small>
                                            <?php endif; endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    transition: all 0.2s;
}

.list-group-item {
    border-left: 4px solid transparent;
    margin-bottom: 5px;
}

.list-group-item:hover {
    border-left-color: #007bff;
}

.text-truncate {
    max-width: 600px;
}
</style>