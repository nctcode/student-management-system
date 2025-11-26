<?php
$title = "Chi tiết đơn chuyển lớp/trường";
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.detail-card {
    background: #ffffff;
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.detail-card .card-header {
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
}

.detail-card .card-body {
    background: #ffffff;
    border-radius: 0 0 12px 12px;
    padding: 1.5rem;
}

.info-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.info-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.transfer-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin: 1rem 0;
}

.transfer-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.2s ease;
    height: 100%;
}

.transfer-card:hover {
    border-color: #6c757d;
}

.transfer-card.current {
    border-left: 4px solid #6c757d;
}

.transfer-card.destination {
    border-left: 4px solid #28a745;
}

.arrow-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.arrow-icon {
    font-size: 1.5rem;
    color: #6c757d;
    background: #f8f9fa;
    border-radius: 50%;
    padding: 0.75rem;
}

.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.8rem;
    white-space: nowrap;
}

.bg-status-approved {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.bg-status-rejected {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.bg-status-pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.bg-status-current {
    background: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}

.reason-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    color: #495057;
    line-height: 1.6;
}

.alert-rejection {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    color: #721c24;
    padding: 1.25rem;
}

.detail-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 1rem;
    color: #212529;
    font-weight: 500;
}

.card-title {
    color: #495057;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 1rem;
}

.badge-type {
    background: #e9ecef;
    color: #495057;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.8rem;
}

.transfer-info {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.transfer-school {
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529;
    margin-bottom: 1rem;
}
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card detail-card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-dark">CHI TIẾT ĐƠN CHUYỂN LỚP / TRƯỜNG</h4>
                            <p class="mb-0 text-muted">Mã đơn: #<?= $don['maDon'] ?></p>
                        </div>
                        <a href="index.php?controller=donchuyenloptruong&action=danhsachdoncuatoi" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Thông tin chung -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="info-card">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-info-circle me-2 text-primary"></i>Thông tin chung</h6>
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <div class="detail-label">Mã đơn</div>
                                            <div class="detail-value">#<?= $don['maDon'] ?></div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="detail-label">Ngày gửi</div>
                                            <div class="detail-value"><?= date('d/m/Y H:i', strtotime($don['ngayGui'])) ?></div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="detail-label">Loại đơn</div>
                                            <span class="badge-type">
                                                <?= $don['loaiDon'] === 'chuyen_lop' ? 'Chuyển lớp' : 'Chuyển trường' ?>
                                            </span>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="detail-label">Học sinh</div>
                                            <div class="detail-value"><?= htmlspecialchars($don['tenHS'] ?? 'N/A') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin chuyển đổi -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="transfer-section">
                                <h6 class="mb-3 text-dark"><i class="fas fa-exchange-alt me-2 text-primary"></i>THÔNG TIN CHUYỂN ĐỔI</h6>
                                
                                <?php if ($don['loaiDon'] === 'chuyen_lop'): ?>
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <div class="transfer-card current">
                                                <div class="transfer-info">Lớp hiện tại</div>
                                                <div class="transfer-school"><?= htmlspecialchars($don['lopHienTai'] ?? 'N/A') ?></div>
                                                <span class="status-badge bg-status-current">Hiện tại</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="arrow-container">
                                                <div class="arrow-icon">
                                                    <i class="fas fa-arrow-right"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="transfer-card destination">
                                                <div class="transfer-info">Lớp chuyển đến</div>
                                                <div class="transfer-school"><?= htmlspecialchars($don['lopDen'] ?? 'N/A') ?></div>
                                                <?php
                                                $lopStatus = $don['trangThaiLop'] ?? 'Chờ duyệt';
                                                $lopStatusClass = match($lopStatus) {
                                                    'Đã duyệt' => 'bg-status-approved',
                                                    'Từ chối' => 'bg-status-rejected',
                                                    default => 'bg-status-pending',
                                                };
                                                ?>
                                                <span class="status-badge <?= $lopStatusClass ?>"><?= $lopStatus ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <div class="transfer-card current">
                                                <div class="transfer-info">Trường hiện tại</div>
                                                <div class="transfer-school"><?= htmlspecialchars($don['truongHienTai'] ?? 'N/A') ?></div>
                                                <?php
                                                $truongDiStatus = $don['trangThaiTruongDi'] ?? 'Chờ duyệt';
                                                $truongDiStatusClass = match($truongDiStatus) {
                                                    'Đã duyệt' => 'bg-status-approved',
                                                    'Từ chối' => 'bg-status-rejected',
                                                    default => 'bg-status-pending',
                                                };
                                                ?>
                                                <span class="status-badge <?= $truongDiStatusClass ?>"><?= $truongDiStatus ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="arrow-container">
                                                <div class="arrow-icon">
                                                    <i class="fas fa-arrow-right"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="transfer-card destination">
                                                <div class="transfer-info">Trường chuyển đến</div>
                                                <div class="transfer-school"><?= htmlspecialchars($don['truongDen'] ?? 'N/A') ?></div>
                                                <?php
                                                $truongDenStatus = $don['trangThaiTruongDen'] ?? 'Chờ duyệt';
                                                $truongDenStatusClass = match($truongDenStatus) {
                                                    'Đã duyệt' => 'bg-status-approved',
                                                    'Từ chối' => 'bg-status-rejected',
                                                    default => 'bg-status-pending',
                                                };
                                                ?>
                                                <span class="status-badge <?= $truongDenStatusClass ?>"><?= $truongDenStatus ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Lý do chuyển -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="info-card">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-edit me-2 text-primary"></i>LÝ DO CHUYỂN</h6>
                                    <div class="reason-box">
                                        <?= nl2br(htmlspecialchars($don['lyDoChuyen'] ?? 'N/A')) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lý do từ chối (nếu có) -->
                    <?php if (!empty($don['lyDoTuChoiLop']) || !empty($don['lyDoTuChoiTruongDi']) || !empty($don['lyDoTuChoiTruongDen'])): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert-rejection">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-exclamation-triangle me-3"></i>
                                        <h6 class="mb-0 me-auto fw-bold">Lý do từ chối</h6>
                                    </div>
                                    <div class="ps-4">
                                        <?php if (!empty($don['lyDoTuChoiLop'])): ?>
                                            <p class="mb-0"><?= nl2br(htmlspecialchars($don['lyDoTuChoiLop'])) ?></p>
                                        <?php elseif (!empty($don['lyDoTuChoiTruongDi'])): ?>
                                            <p class="mb-0"><strong>Trường đi:</strong> <?= nl2br(htmlspecialchars($don['lyDoTuChoiTruongDi'])) ?></p>
                                        <?php elseif (!empty($don['lyDoTuChoiTruongDen'])): ?>
                                            <p class="mb-0"><strong>Trường đến:</strong> <?= nl2br(htmlspecialchars($don['lyDoTuChoiTruongDen'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>