<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-graduation-cap me-2"></i><?= htmlspecialchars($baoCaoTitle) ?>
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
                                <th rowspan="2" class="text-center">STT</th>
                                <th rowspan="2">Khối/Lớp</th>
                                <th rowspan="2" class="text-center">Sĩ số</th>
                                <th colspan="2" class="text-center table-info">Giỏi</th>
                                <th colspan="2" class="text-center table-success">Khá</th>
                                <th colspan="2" class="text-center table-warning">TB</th>
                                <th colspan="2" class="text-center table-danger">Yếu</th>
                            </tr>
                            <tr>
                                <th class="text-center table-info">SL</th>
                                <th class="text-center table-info">%</th>
                                <th class="text-center table-success">SL</th>
                                <th class="text-center table-success">%</th>
                                <th class="text-center table-warning">SL</th>
                                <th class="text-center table-warning">%</th>
                                <th class="text-center table-danger">SL</th>
                                <th class="text-center table-danger">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $stt = 1; foreach ($baoCaoData as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $stt++ ?></td>
                                    <td><?= htmlspecialchars($row['tenKhoi']) ?> - <strong><?= htmlspecialchars($row['tenLop']) ?></strong></td>
                                    <td class="text-center"><?= $row['tongSoHocSinh'] ?></td>
                                    <td class="text-center table-info"><?= $row['slGIOI'] ?></td>
                                    <td class="text-center table-info fw-bold"><?= $row['tyLeGIOI'] ?>%</td>
                                    <td class="text-center table-success"><?= $row['slKHA'] ?></td>
                                    <td class="text-center table-success fw-bold"><?= $row['tyLeKHA'] ?>%</td>
                                    <td class="text-center table-warning"><?= $row['slTB'] ?></td>
                                    <td class="text-center table-warning fw-bold"><?= $row['tyLeTB'] ?>%</td>
                                    <td class="text-center table-danger"><?= $row['slYEU'] ?></td>
                                    <td class="text-center table-danger fw-bold"><?= $row['tyLeYEU'] ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0 py-4 text-center">Không tìm thấy dữ liệu Học lực cho bộ lọc đã chọn.</div>
            <?php endif; ?>
        </div>
    </div>
</div>