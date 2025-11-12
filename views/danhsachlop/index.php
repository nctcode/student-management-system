<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-school me-2"></i>Danh Sách Lớp
            </h1>
            <p class="text-muted">Chọn lớp để xem danh sách học sinh</p>
        </div>
    </div>

    <?php 
    // Hiển thị thông báo
    if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div><?= $_SESSION['error']; ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div><?= $_SESSION['success']; ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>Lớp Phụ Trách
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($danhSachLop)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                    <p>Bạn chưa được phân công phụ trách lớp nào</p>
                </div>
            <?php else: ?>
                <!-- Thống kê nhanh -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <?php 
                        $soLopGVCN = count(array_filter($danhSachLop, function($lop) {
                            return ($lop['isGVCN'] ?? 0) == 1;
                        }));
                        $soLopGVBM = count($danhSachLop) - $soLopGVCN;
                        ?>
                        <div class="d-flex gap-3">
                            <span class="badge bg-success fs-6 p-2">
                                <i class="fas fa-crown me-1"></i> 
                                <?= $soLopGVCN ?> lớp chủ nhiệm
                            </span>
                            <span class="badge bg-info fs-6 p-2">
                                <i class="fas fa-book me-1"></i> 
                                <?= $soLopGVBM ?> lớp bộ môn
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php 
                    // Sử dụng model từ controller
                    foreach ($danhSachLop as $lop): 
                        $isGVCN = ($lop['isGVCN'] ?? 0) == 1;
                        $soHocSinh = $model->getSoHocSinh($lop['maLop']);
                    ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100 hover-card <?= $isGVCN ? 'gvcn-card' : 'gvbm-card' ?> position-relative">
                                <!-- Ribbon cho lớp GVCN -->
                                <?php if ($isGVCN): ?>
                                    <div class="ribbon ribbon-top-right">
                                        <span class="bg-success">
                                            <i class="fas fa-crown me-1"></i>Chủ nhiệm
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="icon-circle <?= $isGVCN ? 'bg-success text-white' : 'bg-primary bg-opacity-10 text-primary' ?> me-3">
                                            <i class="fas <?= $isGVCN ? 'fa-crown' : 'fa-school' ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="card-title <?= $isGVCN ? 'text-success' : 'text-primary' ?> mb-1">
                                                <?= htmlspecialchars($lop['tenLop']) ?>
                                                <?php if (!$isGVCN): ?>
                                                    <small class="text-muted fs-6">(Bộ môn)</small>
                                                <?php endif; ?>
                                            </h5>
                                            <p class="card-text text-muted small mb-0">Mã lớp: <?= $lop['maLop'] ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-users me-1"></i>
                                            <?= $soHocSinh ?> học sinh
                                        </span>
                                        <a href="index.php?controller=danhsachlop&action=danhSachHocSinh&maLop=<?= $lop['maLop'] ?>" 
                                           class="btn <?= $isGVCN ? 'btn-success' : 'btn-primary' ?> btn-sm">
                                            <i class="fas fa-eye me-1"></i> Xem DS
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Card lớp GVCN */
.gvcn-card {
    border-left: 4px solid #198754 !important;
    background: linear-gradient(135deg, #f8fff8 0%, #f0f9f0 100%);
}

/* Card lớp GVBM */
.gvbm-card {
    border-left: 4px solid #0d6efd !important;
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
}

/* Ribbon style */
.ribbon {
    position: absolute;
    right: -5px; 
    top: -5px;
    z-index: 1;
    overflow: hidden;
    width: 75px; 
    height: 75px; 
    text-align: right;
}

.ribbon span {
    font-size: 0.7rem;
    font-weight: bold;
    color: white;
    text-transform: uppercase;
    text-align: center;
    line-height: 20px;
    transform: rotate(45deg);
    -webkit-transform: rotate(45deg);
    width: 100px;
    display: block;
    background: #198754;
    box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 0.5);
    position: absolute;
    top: 19px;
    right: -21px;
}

.ribbon span::before {
    content: "";
    position: absolute;
    left: 0px;
    top: 100%;
    z-index: -1;
    border-left: 3px solid #13653f;
    border-right: 3px solid transparent;
    border-bottom: 3px solid transparent;
    border-top: 3px solid #13653f;
}

.ribbon span::after {
    content: "";
    position: absolute;
    right: 0px;
    top: 100%;
    z-index: -1;
    border-left: 3px solid transparent;
    border-right: 3px solid #13653f;
    border-bottom: 3px solid transparent;
    border-top: 3px solid #13653f;
}
</style>