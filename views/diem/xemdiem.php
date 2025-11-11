<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Xem bảng điểm</strong></h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">
                    Chọn thông tin xem điểm
                    <?php if ($userRole === 'PHUHUYNH' && $hocSinhInfo) echo " cho " . htmlspecialchars($hocSinhInfo['hoTen']); ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="index.php" id="formChonKy">
                    <input type="hidden" name="controller" value="diem">
                    <input type="hidden" name="action" value="xemdiem">

                    <div class="row">
                        <?php if ($userRole === 'PHUHUYNH' && count($danhSachCon) > 1): ?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="maHocSinhSelect"><strong>Chọn Học sinh:</strong></label>
                                <select id="maHocSinhSelect" name="maHocSinh" class="form-control" required>
                                    <option value="">Chọn Học sinh</option>
                                    <?php foreach ($danhSachCon as $con): ?>
                                        <option value="<?= $con['maHocSinh'] ?>" <?= ($con['maHocSinh'] == $maHocSinhChon) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($con['hoTen']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php else: ?>
                            <input type="hidden" name="maHocSinh" value="<?= htmlspecialchars($maHocSinhChon) ?>">
                        <?php endif; ?>

                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="kyHocSelect"><strong>Chọn Học kỳ - Năm học:</strong></label>
                            <select id="kyHocSelect" class="form-control" required
                                <?= !$maHocSinhChon ? 'disabled' : '' ?>>
                                
                                <?php if (empty($danhSachKyHoc)): ?>
                                    <option value="">
                                        <?= !$maHocSinhChon ? 'Vui lòng chọn học sinh' : 'Chưa có dữ liệu điểm' ?>
                                    </option>
                                <?php else: ?>
                                    <option value="all" <?= ($namHocChon == 'all') ? 'selected' : '' ?>>
                                        Xem Toàn Bộ
                                    </option>
                                    
                                    <?php foreach ($danhSachKyHoc as $ky): ?>
                                        <?php 
                                            $value = $ky['namHoc'] . '|' . $ky['hocKy'];
                                            $text = $ky['hocKy'] . ' (' . $ky['namHoc'] . ')';
                                            $selected = ($ky['namHoc'] == $namHocChon && $ky['hocKy'] == $hocKyChon) ? 'selected' : '';
                                        ?>
                                        <option value="<?= $value ?>" <?= $selected ?>><?= $text ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-md-12 d-flex justify-content-start">
                        <div class="form-group">
                            <?php if ($maHocSinhChon): ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> Xem điểm
                                </button>

                                <?php if ($viewMode !== 'none'): ?>
                                    <a href="index.php?controller=diem&action=taibangdiem&maHocSinh=<?= htmlspecialchars($maHocSinhChon) ?>&namHoc=<?= htmlspecialchars($namHocChon) ?>&hocKy=<?= htmlspecialchars($hocKyChon) ?>"
                                       class="btn btn-success ml-2" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Tải về (PDF)
                                    </a>
                                <?php endif; ?>
                                
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" id="namHoc" name="namHoc" value="<?= htmlspecialchars($namHocChon) ?>">
                <input type="hidden" id="hocKy" name="hocKy" value="<?= htmlspecialchars($hocKyChon) ?>">
            </form>
        </div>
    </div>

    <?php if ($maHocSinhChon): ?>
    <?php if ($viewMode === 'single'): ?>
        <?php $bangDiem = $bangDiemData['single']; ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">
                    Bảng điểm chi tiết <?= htmlspecialchars($hocKyChon) ?> (<?= htmlspecialchars($namHocChon) ?>)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Môn học</th>
                                <th>Điểm Miệng</th>
                                <th>Điểm 15 Phút</th>
                                <th>Điểm 1 Tiết</th>
                                <th>Điểm Cuối Kỳ</th>
                                <th>TBM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $bangDiem = $bangDiemData['single']['bangDiem'];
                                $TBM_HocKy = $bangDiemData['single']['TBM_HocKy'];
                            ?>
                            <?php if (empty($bangDiem)): ?>
                                <tr><td colspan="6" class="text-center">Chưa có dữ liệu!</td></tr>
                            <?php else: ?>
                                <?php foreach ($bangDiem as $mon): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($mon['tenMonHoc']) ?></strong></td>
                                        <td><?= implode(', ', $mon['MIENG']) ?></td>
                                        <td><?= implode(', ', $mon['15_PHUT']) ?></td>
                                        <td><?= implode(', ', $mon['1_TIET']) ?></td>
                                        <td><?= implode(', ', $mon['CUOI_KY']) ?></td>
                                        <td style="color: red;"><?= $mon['TBM'] !== null ? number_format($mon['TBM'], 2) : ' ' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>

                        <?php if ($TBM_HocKy !== null): ?>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">Trung bình Học kỳ (GPA)</td>
                                <td style="color: red; font-weight: bold; font-size: 1rem;">
                                    <?= number_format($TBM_HocKy, 2) ?>
                                </td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>

                    </table>
                </div>

                <hr>
                <h6 class="font-weight-bold text-primary">Biểu đồ TBM các môn</h6>
                <div style="max-height: 400px;">
                    <canvas id="myGradeChart"></canvas>
                </div>
            </div>
        </div>

    <?php elseif ($viewMode === 'all'): ?>
        <?php foreach ($bangDiemData as $key => $result): ?>
            <?php 
                list($namHoc, $hocKy) = explode('|', $key);
                $bangDiem = $result['bangDiem'];
                $TBM_HocKy = $result['TBM_HocKy'];
            ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="m-0 font-weight-bold text-primary">
                        Bảng điểm chi tiết <?= htmlspecialchars($hocKy) ?> (<?= htmlspecialchars($namHoc) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Môn học</th>
                                    <th>Điểm Miệng</th>
                                    <th>Điểm 15 Phút</th>
                                    <th>Điểm 1 Tiết</th>
                                    <th>Điểm Cuối Kỳ</th>
                                    <th>TBM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bangDiem)): ?>
                                    <tr><td colspan="6" class="text-center">Chưa có dữ liệu!</td></tr>
                                <?php else: ?>
                                    <?php foreach ($bangDiem as $mon): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($mon['tenMonHoc']) ?></strong></td>
                                            <td><?= implode(', ', $mon['MIENG']) ?></td>
                                            <td><?= implode(', ', $mon['15_PHUT']) ?></td>
                                            <td><?= implode(', ', $mon['1_TIET']) ?></td>
                                            <td><?= implode(', ', $mon['CUOI_KY']) ?></td>
                                            <td style="color: red;"><?= $mon['TBM'] !== null ? number_format($mon['TBM'], 2) : ' ' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <?php if ($TBM_HocKy !== null): ?>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">Trung bình Học kỳ (GPA)</td>
                                    <td style="color: red; font-weight: bold; font-size: 1rem;">
                                        <?= number_format($TBM_HocKy, 2) ?>
                                    </td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php elseif ($viewMode === 'none' && !empty($danhSachKyHoc)): ?>
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                Vui lòng chọn một kỳ học (hoặc xem toàn bộ) để xem điểm!
            </div>
        </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kyHocSelect = document.getElementById('kyHocSelect');
    const formChonKy = document.getElementById('formChonKy');
    const namHocInput = document.getElementById('namHoc');
    const hocKyInput = document.getElementById('hocKy');
    const maHocSinhSelect = document.getElementById('maHocSinhSelect');

    if (maHocSinhSelect) {
        maHocSinhSelect.addEventListener('change', function() {
            formChonKy.submit();
        });
    }

    function updateHiddenInputs() {
        const selectedValue = kyHocSelect.value;
        
        if (selectedValue === 'all') {
            // Nếu chọn "Xem Toàn Bộ"
            namHocInput.value = 'all';
            hocKyInput.value = 'all';
        } else if (selectedValue) {
            // Nếu chọn một kỳ cụ thể
            const parts = selectedValue.split('|');
            if (parts.length === 2) {
                namHocInput.value = parts[0];
                hocKyInput.value = parts[1];
            }
        } else {
            namHocInput.value = '';
            hocKyInput.value = '';
        }
    }

    kyHocSelect.addEventListener('change', updateHiddenInputs);
    updateHiddenInputs();
});
</script>

<?php 
if ($viewMode === 'single' && !empty($bangDiemData['single']['bangDiem'])): 
    
    $bangDiemJS = $bangDiemData['single']['bangDiem'];
    $labels = [];
    $dataTBM = [];
    foreach ($bangDiemJS as $mon) {
        $labels[] = $mon['tenMonHoc'];
        $dataTBM[] = $mon['TBM'] ?? 0; 
    }
?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('myGradeChart');
        if (!ctx) return;
        
        const labels = <?= json_encode($labels) ?>;
        const dataTBM = <?= json_encode($dataTBM) ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Điểm Trung bình môn (TBM)',
                    data: dataTBM,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10 
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
    </script>
<?php endif; ?>