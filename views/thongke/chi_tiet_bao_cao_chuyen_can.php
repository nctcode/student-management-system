<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-clipboard-check me-2"></i><?= htmlspecialchars($baoCaoTitle) ?>
            </h1>
        </div>
        <div>
            <a href="index.php?controller=ThongKe&action=index" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
            <button class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Xuất Excel
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
                    <table id="baoCaoTable" class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">STT</th>
                                <th>Khối/Lớp</th>
                                <th class="text-center">Sĩ số</th>
                                <th class="text-center table-warning">Tổng số lần Vắng</th>
                                <th class="text-center table-danger">TB lần Vắng/HS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $stt = 1; foreach ($baoCaoData as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $stt++ ?></td>
                                    <td><?= htmlspecialchars($row['tenKhoi']) ?> - <strong><?= htmlspecialchars($row['tenLop']) ?></strong></td>
                                    <td class="text-center"><?= $row['tongSoHocSinh'] ?></td>
                                    <td class="text-center fw-bold table-warning"><?= $row['tongSoLanVang'] ?></td>
                                    <td class="text-center fw-bold table-danger"><?= $row['TBSoLanVang'] ?> lần</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0 py-4 text-center">Không tìm thấy dữ liệu Chuyên cần cho bộ lọc đã chọn.</div>
            <?php endif; ?>
        </div>
    </div>
</div>