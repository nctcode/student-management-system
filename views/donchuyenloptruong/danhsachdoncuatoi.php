<?php
$title = "Đơn chuyển lớp/trường của tôi";
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container">
    <div class="header">
        <h2>ĐƠN CHUYỂN LỚP / TRƯỜNG CỦA TÔI</h2>
    </div>

    <div class="card">
        <div class="action-buttons" style="margin-bottom: 20px;">
            <a href="index.php?controller=donchuyenloptruong&action=guidon" class="btn btn-primary">
                <i class="fas fa-plus"></i> Gửi đơn mới
            </a>
        </div>

        <?php if (empty($donChuyen)): ?>
            <div class="text-center text-muted p-4">
                <p>Chưa có đơn chuyển lớp/trường nào.</p>
            </div>
        <?php else: ?>
            <div class="request-list">
                <?php foreach ($donChuyen as $don): ?>
                    <div class="request-item">
                        <div class="request-info">
                            <div class="request-code">#<?= str_pad($don['maDon'], 3, '0', STR_PAD_LEFT) ?></div>
                            <div class="student-name"><?= htmlspecialchars($don['tenHS'] ?? 'N/A') ?></div>
                            <div class="transfer-info">
                                <?php if ($don['loaiDon'] === 'chuyen_lop'): ?>
                                    <?= htmlspecialchars($don['lopHienTai'] ?? 'N/A') ?> → <?= htmlspecialchars($don['lopDen'] ?? 'N/A') ?>
                                <?php else: ?>
                                    <?= htmlspecialchars($don['truongHienTai'] ?? 'N/A') ?> → <?= htmlspecialchars($don['truongDen'] ?? 'N/A') ?>
                                <?php endif; ?>
                            </div>
                            <div class="transfer-info">
                                Loại đơn: <strong><?= $don['loaiDon'] === 'chuyen_lop' ? 'Chuyển lớp' : 'Chuyển trường' ?></strong>
                                | Ngày gửi: <?= date('d/m/Y', strtotime($don['ngayGui'])) ?>
                            </div>
                        </div>
                        <?php
                        $status = $don['trangThaiTong'] ?? 'Không xác định';
                        $cls = match($status) {
                            'Hoàn tất' => 'status-approved',
                            'Bị từ chối' => 'status-rejected',
                            default => 'status-pending',
                        };
                        ?>
                        <span class="status <?= $cls ?>"><?= $status ?></span>
                        <a href="index.php?controller=donchuyenloptruong&action=chitiet&maDon=<?= $don['maDon'] ?>" 
                           class="btn btn-sm btn-outline-primary">Chi tiết</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>