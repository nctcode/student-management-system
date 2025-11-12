<?php
$title = "Chi tiết đơn chuyển lớp/trường";
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container">
    <div class="header">
        <h2>CHI TIẾT ĐƠN CHUYỂN LỚP / TRƯỜNG</h2>
    </div>

    <div class="card">
        <div class="detail-grid">
            <div><span class="detail-label">Mã đơn:</span> <?= $don['maDon'] ?></div>
            <div><span class="detail-label">Ngày gửi:</span> <?= date('d/m/Y H:i', strtotime($don['ngayGui'])) ?></div>
            <div><span class="detail-label">Loại đơn:</span> <strong><?= $don['loaiDon'] === 'chuyen_lop' ? 'Chuyển lớp' : 'Chuyển trường' ?></strong></div>
            <div><span class="detail-label">Học sinh:</span> <?= htmlspecialchars($don['tenHS'] ?? 'N/A') ?></div>
        </div>

        <?php if ($don['loaiDon'] === 'chuyen_lop'): ?>
            <div class="school-status">
                <div class="school-status-item school-current">
                    <div class="detail-label">Lớp hiện tại</div>
                    <div><?= htmlspecialchars($don['lopHienTai'] ?? 'N/A') ?></div>
                    <div class="status status-pending">Hiện tại</div>
                </div>
                <div class="school-status-item school-destination">
                    <div class="detail-label">Lớp chuyển đến</div>
                    <div><?= htmlspecialchars($don['lopDen'] ?? 'N/A') ?></div>
                    <div class="status <?= $don['trangThaiLop'] === 'Đã duyệt' ? 'status-approved' : ($don['trangThaiLop'] === 'Từ chối' ? 'status-rejected' : 'status-pending') ?>">
                        <?= $don['trangThaiLop'] ?? 'Chờ duyệt' ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="school-status">
                <div class="school-status-item school-current">
                    <div class="detail-label">Trường hiện tại</div>
                    <div><?= htmlspecialchars($don['truongHienTai'] ?? 'N/A') ?></div>
                    <div class="status <?= $don['trangThaiTruongDi'] === 'Đã duyệt' ? 'status-approved' : ($don['trangThaiTruongDi'] === 'Từ chối' ? 'status-rejected' : 'status-pending') ?>">
                        <?= $don['trangThaiTruongDi'] ?? 'Chờ duyệt' ?>
                    </div>
                </div>
                <div class="school-status-item school-destination">
                    <div class="detail-label">Trường chuyển đến</div>
                    <div><?= htmlspecialchars($don['truongDen'] ?? 'N/A') ?></div>
                    <div class="status <?= $don['trangThaiTruongDen'] === 'Đã duyệt' ? 'status-approved' : ($don['trangThaiTruongDen'] === 'Từ chối' ? 'status-rejected' : 'status-pending') ?>">
                        <?= $don['trangThaiTruongDen'] ?? 'Chờ duyệt' ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label class="detail-label">Lý do chuyển:</label>
            <div class="p-3 bg-light rounded"><?= nl2br(htmlspecialchars($don['lyDoChuyen'] ?? 'N/A')) ?></div>
        </div>

        <?php if (!empty($don['lyDoTuChoiLop']) || !empty($don['lyDoTuChoiTruongDi']) || !empty($don['lyDoTuChoiTruongDen'])): ?>
            <div class="alert alert-danger">
                <strong><i class="fas fa-exclamation-triangle"></i> Lý do từ chối:</strong><br>
                <?php if (!empty($don['lyDoTuChoiLop'])): ?>
                    <?= nl2br(htmlspecialchars($don['lyDoTuChoiLop'])) ?>
                <?php elseif (!empty($don['lyDoTuChoiTruongDi'])): ?>
                    Trường đi: <?= nl2br(htmlspecialchars($don['lyDoTuChoiTruongDi'])) ?>
                <?php elseif (!empty($don['lyDoTuChoiTruongDen'])): ?>
                    Trường đến: <?= nl2br(htmlspecialchars($don['lyDoTuChoiTruongDen'])) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="index.php?controller=donchuyenloptruong&action=danhsachdoncuatoi" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>