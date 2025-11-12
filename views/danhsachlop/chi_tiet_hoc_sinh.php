<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-user-graduate me-2"></i>Thông Tin Học Sinh
            </h1>
            <p class="text-muted">
                <?= htmlspecialchars($hocSinh['hoTen'] ?? '') ?> - 
                Lớp: <?= htmlspecialchars($hocSinh['tenLop'] ?? 'Chưa phân lớp') ?>
            </p>
            <?php if (!$isGVCN): ?>
                <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Chế độ xem hạn chế:</strong> Bạn đang xem ở chế độ GVBM, chỉ hiển thị thông tin cơ bản và điểm môn bạn dạy.
                </div>
            <?php endif; ?>
        </div>
        <div>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
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

    <div class="row">
        <!-- Thông tin cá nhân - GIAO DIỆN MỚI -->
        <div class="col-lg-6 mb-4">
            <div class="card student-card border-0 shadow-sm h-100">
                <div class="card-header card-header-custom">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle">
                            <i class="fas fa-user-graduate fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Thông Tin Cá Nhân</h5>
                            <p class="mb-0 opacity-75">Thông tin chi tiết về học sinh</p>
                        </div>
                    </div>
                    <?php if (!$isGVCN): ?>
                        <span class="badge bg-warning ms-2">Giới hạn</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Họ Tên</span>
                            <span class="info-value"><?= htmlspecialchars($hocSinh['hoTen'] ?? '') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Mã Học Sinh</span>
                            <span class="info-value">
                                <span class="badge highlight-badge"><?= $hocSinh['maHocSinh'] ?? '' ?></span>
                            </span>
                        </div>
                        
                        <?php if ($isGVCN): ?>
                            <div class="info-item">
                                <span class="info-label">Ngày Sinh</span>
                                <span class="info-value">
                                    <?= isset($hocSinh['ngaySinh']) ? date('d/m/Y', strtotime($hocSinh['ngaySinh'])) : 'N/A' ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Giới Tính</span>
                                <span class="info-value">
                                    <?php if (($hocSinh['gioiTinh'] ?? '') == 'NAM'): ?>
                                        <span class="badge bg-primary">Nam</span>
                                    <?php elseif (($hocSinh['gioiTinh'] ?? '') == 'NU'): ?>
                                        <span class="badge bg-pink">Nữ</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Khác</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">CCCD</span>
                                <span class="info-value"><?= htmlspecialchars($hocSinh['CCCD'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Địa Chỉ</span>
                                <span class="info-value"><?= htmlspecialchars($hocSinh['diaChi'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    <?= htmlspecialchars($hocSinh['email'] ?? 'N/A') ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Số Điện Thoại</span>
                                <span class="info-value">
                                    <i class="fas fa-phone me-2 text-success"></i>
                                    <?= htmlspecialchars($hocSinh['soDienThoai'] ?? 'N/A') ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Chỉ GVCN mới có quyền xem thông tin chi tiết học sinh.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin phụ huynh - GIAO DIỆN MỚI - CHỈ GVCN ĐƯỢC XEM -->
        <?php if ($isGVCN): ?>
            <div class="col-lg-6 mb-4">
                <div class="card student-card border-0 shadow-sm h-100">
                    <div class="card-header card-header-custom parent-card">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Thông Tin Phụ Huynh</h5>
                                <p class="mb-0 opacity-75">Thông tin người giám hộ</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($hocSinh['tenPhuHuynh'])): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-exclamation-circle fa-2x mb-3 opacity-25"></i>
                                <p>Thông tin phụ huynh chưa được cập nhật</p>
                            </div>
                        <?php else: ?>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Họ Tên Phụ Huynh</span>
                                    <span class="info-value"><?= htmlspecialchars($hocSinh['tenPhuHuynh']) ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Mối Quan Hệ</span>
                                    <span class="info-value">
                                        <span class="badge bg-success"><?= htmlspecialchars($hocSinh['moiQuanHe'] ?? 'N/A') ?></span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">SĐT Phụ Huynh</span>
                                    <span class="info-value">
                                        <i class="fas fa-phone me-2 text-success"></i>
                                        <?= htmlspecialchars($hocSinh['sdtPhuHuynh'] ?? 'N/A') ?>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Email Phụ Huynh</span>
                                    <span class="info-value">
                                        <i class="fas fa-envelope me-2 text-primary"></i>
                                        <?= htmlspecialchars($hocSinh['emailPhuHuynh'] ?? 'N/A') ?>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Nghề Nghiệp</span>
                                    <span class="info-value"><?= htmlspecialchars($hocSinh['ngheNghiep'] ?? 'N/A') ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Điểm số và chuyên cần -->
    <div class="row">
        <!-- Điểm số -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-warning text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Điểm Số
                        <?php if (!$isGVCN): ?>
                            <span class="badge bg-info ms-2">Chỉ môn bạn dạy</span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($diemSo)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-bar fa-2x mb-3 opacity-25"></i>
                            <p>Chưa có điểm số</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Môn Học</th>
                                        <th>Điểm</th>
                                        <th>Loại</th>
                                        <th>Học Kỳ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($diemSo as $diem): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($diem['tenMonHoc'] ?? '') ?></td>
                                            <td>
                                                <span class="badge bg-<?= ($diem['diemSo'] ?? 0) >= 5 ? 'success' : 'danger' ?>">
                                                    <?= $diem['diemSo'] ?? 0 ?>
                                                </span>
                                            </td>
                                            <td><?= $diem['loaiDiem'] ?? '' ?></td>
                                            <td><?= $diem['hocKy'] ?? '' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chuyên cần - CHỈ GVCN ĐƯỢC XEM -->
        <?php if ($isGVCN): ?>
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-info text-white py-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-check me-2"></i>Chuyên Cần (30 ngày)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($chuyenCan)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-calendar fa-2x mb-3 opacity-25"></i>
                                <p>Chưa có dữ liệu chuyên cần</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Trạng Thái</th>
                                            <th>Ghi Chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($chuyenCan as $cc): ?>
                                            <tr>
                                                <td><?= isset($cc['ngayHoc']) ? date('d/m/Y', strtotime($cc['ngayHoc'])) : '' ?></td>
                                                <td>
                                                    <?php if (($cc['trangThai'] ?? '') == 'Co_mat'): ?>
                                                        <span class="badge bg-success">Có mặt</span>
                                                    <?php elseif (($cc['trangThai'] ?? '') == 'Vang'): ?>
                                                        <span class="badge bg-danger">Vắng</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning"><?= $cc['trangThai'] ?? '' ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($cc['ghiChu'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.student-card {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: none;
    overflow: hidden;
}
.card-header-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-bottom: none;
}
.parent-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
}
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    padding: 1.5rem;
}
.info-item {
    display: flex;
    flex-direction: column;
    margin-bottom: 1rem;
}
.info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}
.info-value {
    color: #2c3e50;
    font-weight: 500;
    font-size: 1rem;
}
.highlight-badge {
    background: linear-gradient(45deg, #FF6B6B, #FF8E53);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
}
.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    background: rgba(255,255,255,0.2);
}
.badge.bg-pink {
    background-color: #e83e8c !important;
}

/* Responsive */
@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
        padding: 1rem;
    }
    
    .icon-circle {
        width: 40px;
        height: 40px;
        margin-right: 0.75rem;
    }
    
    .card-header-custom {
        padding: 1rem;
    }
}
</style>