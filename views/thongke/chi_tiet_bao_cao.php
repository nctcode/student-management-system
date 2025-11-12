<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-file-alt me-2"></i><?= htmlspecialchars($baoCaoTitle) ?>
            </h1>
            <p class="text-muted">Kết quả báo cáo được lọc theo khối/lớp</p>
        </div>
        <div>
            <a href="index.php?controller=ThongKe&action=index" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Quay lại Thống kê
            </a>
            <button class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Xuất Excel
            </button>
        </div>
    </div>
    
    <?php 
    if (isset($_SESSION['warning'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div><?= $_SESSION['warning']; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['warning']); ?>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (!empty($baoCaoData)): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Khối</th>
                                <th>Lớp Học</th>
                                <th>GV Chủ Nhiệm</th>
                                <th>Tổng số Môn</th>
                                <th>Môn đã PC (GVBM)</th>
                                <th class="text-center">Tỷ lệ PC</th>
                                <th class="text-center">Trạng Thái PC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($baoCaoData as $item): 
                                $tyLePC = $item['tyLePC'] ?? 0;
                                $classPC = ($tyLePC == 100) ? 'bg-success' : 'bg-warning';
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['tenKhoi'] ?? 'N/A') ?></td>
                                    <td><strong><?= htmlspecialchars($item['tenLop']) ?></strong></td>
                                    <td>
                                        <i class="fas fa-user-tie me-1"></i>
                                        <?= htmlspecialchars($item['gvcnHoTen'] ?? 'Chưa phân công') ?>
                                    </td>
                                    <td><?= $item['tongSoMon'] ?? 'N/A' ?></td>
                                    <td><?= $item['soMonDaPhanCong'] ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $classPC ?>"><?= $tyLePC ?>%</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= ($item['trangThaiPC'] === 'Hoàn thành' && $item['gvcnHoTen']) ? 'success' : 'warning' ?>">
                                            <?= $item['trangThaiPC'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0 py-4 text-center">Không tìm thấy dữ liệu Phân công cho bộ lọc đã chọn.</div>
            <?php endif; ?>
        </div>
    </div>
</div>