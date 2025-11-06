<?php
$maLop = $_GET['maLop'] ?? 0;

// Xác định xem danh sách có phải là basic không (cho GVBM)
$isBasicView = isset($hocSinh[0]) && !array_key_exists('ngaySinh', $hocSinh[0]);
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-users me-2"></i>Danh Sách Học Sinh
            </h1>
            <p class="text-muted">Lớp: <?= htmlspecialchars($lop['tenLop'] ?? '') ?></p>
            <?php if ($isBasicView): ?>
                <div class="alert alert-info alert-dismissible fade show mt-2" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Chế độ xem hạn chế:</strong> Bạn đang xem ở chế độ GVBM, chỉ hiển thị thông tin cơ bản.
                </div>
            <?php endif; ?>
        </div>
        <div>
            <a href="index.php?controller=danhsachlop&action=index" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div><?= $_SESSION['error']; ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['info'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <div><?= $_SESSION['info']; ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['info']); ?>
    <?php endif; ?>

    <!-- Danh sách học sinh -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0 text-primary">
                <i class="fas fa-list-ul me-2"></i>Học Sinh Trong Lớp
                <?php if ($isBasicView): ?>
                    <span class="badge bg-warning ms-2">Chế độ GVBM</span>
                <?php else: ?>
                    <span class="badge bg-success ms-2">Chế độ GVCN</span>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($hocSinh)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-user-graduate fa-3x mb-3 opacity-25"></i>
                    <p>Không có học sinh nào trong lớp</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Họ Tên</th>
                                <th>Mã HS</th>
                                <?php if (!$isBasicView): ?>
                                    <th>Ngày Sinh</th>
                                    <th>Giới Tính</th>
                                    <th>CCCD</th>
                                    <th>Trạng Thái</th>
                                <?php endif; ?>
                                <th class="text-end pe-4">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hocSinh as $hs): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-sm bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong><?= htmlspecialchars($hs['hoTen'] ?? '') ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $hs['maHocSinh'] ?? '' ?></span>
                                    </td>
                                    <?php if (!$isBasicView): ?>
                                        <td>
                                            <?= isset($hs['ngaySinh']) ? date('d/m/Y', strtotime($hs['ngaySinh'])) : 'N/A' ?>
                                        </td>
                                        <td>
                                            <?php if (($hs['gioiTinh'] ?? '') == 'NAM'): ?>
                                                <span class="badge bg-primary">Nam</span>
                                            <?php elseif (($hs['gioiTinh'] ?? '') == 'NU'): ?>
                                                <span class="badge bg-pink" style="background-color: #e83e8c!important;">Nữ</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Khác</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($hs['CCCD'] ?? 'N/A') ?>
                                        </td>
                                        <td>
                                            <?php if (($hs['trangThai'] ?? '') == 'DANG_HOC'): ?>
                                                <span class="badge bg-success">Đang học</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning"><?= $hs['trangThai'] ?? '' ?></span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                    <td class="text-end pe-4">
                                        <a href="index.php?controller=danhsachlop&action=chiTietHocSinh&maHocSinh=<?= $hs['maHocSinh'] ?? '' ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> Chi tiết
                                        </a>
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

<style>
.icon-sm {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>