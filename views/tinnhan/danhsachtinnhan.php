<div class="container mt-4">
    <h4>📨 Danh sách cuộc hội thoại</h4>
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <div class="mb-3">
        <a href="index.php?controller=tinnhan&action=gui" class="btn btn-primary">+ Soạn tin nhắn</a>
        
        <a href="index.php" class="btn btn-outline-secondary ms-2">🏠 Về trang chủ</a>
        </div>

    <?php if (!empty($dscuoc)): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Tên hội thoại</th>
                    <th>Loại</th>
                    <th>Thời gian mới nhất</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dscuoc as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['tenHoiThoai']) ?></td>
                        <td><?= htmlspecialchars($c['loaiHoiThoai']) ?></td>
                        <td><?= htmlspecialchars($c['thoiGianMoiNhat']) ?></td>
                        <td>
                            <a href="index.php?controller=tinnhan&action=chitiet&maHoiThoai=<?= $c['maHoiThoai'] ?>"
                               class="btn btn-sm btn-outline-info">Xem</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Chưa có cuộc hội thoại nào.</div>
    <?php endif; ?>
</div>
