<div class="container mt-4">
    <h4>📩 Chi tiết hội thoại</h4>
    <a href="index.php?controller=tinnhan&action=index" class="btn btn-secondary mb-3">← Quay lại</a>

    <?php if (!empty($dsNguoiNhan)): ?>
        <div class="mb-3">
            <strong>Những người tham gia:</strong>
            <p class="text-muted"><?= htmlspecialchars(implode(', ', $dsNguoiNhan)) ?></p>
        </div>
    <?php endif; ?>
    <?php if (!empty($tinnhans)): ?>
        <?php foreach ($tinnhans as $tn): ?>
            <div class="card mb-3 p-3">
                <div class="d-flex justify-content-between">
                    <strong><?= htmlspecialchars($tn['tenNguoiGui']) ?></strong>
                    <small class="text-muted"><?= htmlspecialchars($tn['thoiGianGui']) ?></small>
                </div>

                <h6 class="mt-2 mb-1"><?= htmlspecialchars($tn['tieuDe']) ?></h6>
                
                <div class="mt-2"><?= nl2br(htmlspecialchars($tn['noiDung'])) ?></div>
                
                <?php if (!empty($tn['fileDinhKem'])): ?>
                    <div class="mt-2">
                        📎 <a href="<?= htmlspecialchars($tn['fileDinhKem']) ?>" target="_blank">Xem file đính kèm</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">Không có tin nhắn nào trong cuộc hội thoại này.</div>
    <?php endif; ?>
</div>
