<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Xem bảng điểm</strong></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">
                Chọn thông tin xem điểm
                <?php if ($userRole === 'PHUHUYNH' && isset($hocSinhInfo['hoTen'])) echo " cho " . htmlspecialchars($hocSinhInfo['hoTen']); ?>
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" id="formChonKy">
                <input type="hidden" name="controller" value="diem">
                <input type="hidden" name="action" value="xemdiem">

                <div class="row">
                    <?php if ($userRole === 'PHUHUYNH' && is_array($danhSachCon) && count($danhSachCon) > 1): ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="maHocSinhSelect"><strong>Chọn Học sinh:</strong></label>
                            <select id="maHocSinhSelect" name="maHocSinh" class="form-control" required>
                                <option value="">Chọn Học sinh</option>
                                <?php foreach ($danhSachCon as $con): ?>
                                    <?php if (is_array($con)): ?>
                                        <option value="<?= $con['maHocSinh'] ?? '' ?>" <?= (($con['maHocSinh'] ?? '') == $maHocSinhChon) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($con['hoTen'] ?? '') ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="maHocSinh" value="<?= htmlspecialchars($maHocSinhChon ?? '') ?>">
                    <?php endif; ?>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kyHocSelect"><strong>Chọn Học kỳ - Năm học:</strong></label>
                            <select id="kyHocSelect" name="kyHocSelect" class="form-control" 
                                <?= (!isset($maHocSinhChon) || !$maHocSinhChon) ? 'disabled' : '' ?>>
                                
                                <?php if (empty($danhSachKyHoc) || !is_array($danhSachKyHoc)): ?>
                                    <option value="">
                                        <?= !isset($maHocSinhChon) || !$maHocSinhChon ? 'Vui lòng chọn học sinh' : 'Chưa có dữ liệu điểm' ?>
                                    </option>
                                <?php else: ?>
                                    <option value="all" <?= (($namHocChon ?? '') == 'all') ? 'selected' : '' ?>>
                                        Xem Toàn Bộ
                                    </option>
                                    
                                    <?php foreach ($danhSachKyHoc as $ky): ?>
                                        <?php if (is_array($ky)): ?>
                                            <?php 
                                                $value = ($ky['namHoc'] ?? '') . '|' . ($ky['hocKy'] ?? '');
                                                $text = ($ky['hocKy'] ?? '') . ' (' . ($ky['namHoc'] ?? '') . ')';
                                                $selected = (($ky['namHoc'] ?? '') == ($namHocChon ?? '') && ($ky['hocKy'] ?? '') == ($hocKyChon ?? '')) ? 'selected' : '';
                                            ?>
                                            <option value="<?= $value ?>" <?= $selected ?>><?= $text ?></option>
                                        <?php endif; ?>
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
                            <?php if (isset($maHocSinhChon) && $maHocSinhChon): ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> Xem điểm
                                </button>

                                <?php if (isset($viewMode) && $viewMode !== 'none'): ?>
                                    <a href="index.php?controller=diem&action=taibangdiem&maHocSinh=<?= htmlspecialchars($maHocSinhChon) ?>&namHoc=<?= htmlspecialchars($namHocChon ?? '') ?>&hocKy=<?= htmlspecialchars($hocKyChon ?? '') ?>"
                                       class="btn btn-success ml-2" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Tải về (PDF)
                                    </a>
                                <?php endif; ?>
                                
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" id="namHoc" name="namHoc" value="<?= htmlspecialchars($namHocChon ?? '') ?>">
                <input type="hidden" id="hocKy" name="hocKy" value="<?= htmlspecialchars($hocKyChon ?? '') ?>">
            </form>
        </div>
    </div>

    <?php if (isset($maHocSinhChon) && $maHocSinhChon): ?>
    <?php if (isset($viewMode) && $viewMode === 'single'): ?>
        <?php $bangDiem = $bangDiemData['single'] ?? []; ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">
                    Bảng điểm chi tiết <?= htmlspecialchars($hocKyChon ?? '') ?> (<?= htmlspecialchars($namHocChon ?? '') ?>)
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
                                $bangDiem = $bangDiem['bangDiem'] ?? [];
                                $TBM_HocKy = $bangDiem['TBM_HocKy'] ?? null;
                            ?>
                            <?php if (empty($bangDiem)): ?>
                                <tr><td colspan="6" class="text-center">Chưa có dữ liệu!</td></tr>
                            <?php else: ?>
                                <?php foreach ($bangDiem as $mon): ?>
                                    <?php if (is_array($mon)): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($mon['tenMonHoc'] ?? '') ?></strong></td>
                                            <td><?= is_array($mon['MIENG'] ?? []) ? implode(', ', $mon['MIENG']) : '' ?></td>
                                            <td><?= is_array($mon['15_PHUT'] ?? []) ? implode(', ', $mon['15_PHUT']) : '' ?></td>
                                            <td><?= is_array($mon['1_TIET'] ?? []) ? implode(', ', $mon['1_TIET']) : '' ?></td>
                                            <td><?= is_array($mon['CUOI_KY'] ?? []) ? implode(', ', $mon['CUOI_KY']) : '' ?></td>
                                            <td style="color: red;"><?= isset($mon['TBM']) && $mon['TBM'] !== null ? number_format($mon['TBM'], 2) : ' ' ?></td>
                                        </tr>
                                    <?php endif; ?>
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
                <div style="height: 600px;">
                    <canvas id="myGradeChart"></canvas>
                </div>
            </div>
        </div>

    <?php elseif (isset($viewMode) && $viewMode === 'all'): ?>
        <?php if (is_array($bangDiemData)): ?>
            <?php foreach ($bangDiemData as $key => $result): ?>
                <?php 
                    if (!is_array($result)) continue;
                    list($namHoc, $hocKy) = explode('|', $key);
                    $bangDiem = $result['bangDiem'] ?? [];
                    $TBM_HocKy = $result['TBM_HocKy'] ?? null;
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
                                            <?php if (is_array($mon)): ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($mon['tenMonHoc'] ?? '') ?></strong></td>
                                                    <td><?= is_array($mon['MIENG'] ?? []) ? implode(', ', $mon['MIENG']) : '' ?></td>
                                                    <td><?= is_array($mon['15_PHUT'] ?? []) ? implode(', ', $mon['15_PHUT']) : '' ?></td>
                                                    <td><?= is_array($mon['1_TIET'] ?? []) ? implode(', ', $mon['1_TIET']) : '' ?></td>
                                                    <td><?= is_array($mon['CUOI_KY'] ?? []) ? implode(', ', $mon['CUOI_KY']) : '' ?></td>
                                                    <td style="color: red;"><?= isset($mon['TBM']) && $mon['TBM'] !== null ? number_format($mon['TBM'], 2) : ' ' ?></td>
                                                </tr>
                                            <?php endif; ?>
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
        <?php endif; ?>

    <?php elseif (isset($viewMode) && $viewMode === 'none' && !empty($danhSachKyHoc)): ?>
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

    // Khi thay đổi học sinh, submit form ngay lập tức
    if (maHocSinhSelect) {
        maHocSinhSelect.addEventListener('change', function() {
            formChonKy.submit();
        });
    }

    // Khi thay đổi kỳ học, cập nhật hidden inputs và submit form
    if (kyHocSelect) {
        kyHocSelect.addEventListener('change', function() {
            updateHiddenInputs();
            formChonKy.submit();
        });
    }

    function updateHiddenInputs() {
        const selectedValue = kyHocSelect?.value;
        
        if (selectedValue === 'all') {
            namHocInput.value = 'all';
            hocKyInput.value = 'all';
        } else if (selectedValue) {
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

    // Khởi tạo giá trị ban đầu
    updateHiddenInputs();

    // Bật dropdown kỳ học nếu đã có học sinh được chọn
    if (kyHocSelect && <?= (isset($maHocSinhChon) && $maHocSinhChon) ? 'true' : 'false' ?>) {
        kyHocSelect.disabled = false;
    }
});
</script>

<?php 
if (isset($viewMode) && $viewMode === 'single' && isset($bangDiemData['single']['bangDiem']) && is_array($bangDiemData['single']['bangDiem'])): 
    
    $bangDiemJS = $bangDiemData['single']['bangDiem'];
    
    $labels = [];
    $dataTBM = [];
    $dataTBM_Lop = []; 
    
    foreach ($bangDiemJS as $mon) {
        if (is_array($mon) && isset($mon['TBM']) && $mon['TBM'] !== null) { 
            $labels[] = $mon['tenMonHoc'] ?? '';
            $dataTBM[] = $mon['TBM']; 
            $dataTBM_Lop[] = $mon['TBM_Lop'] ?? null;
        }
    }
?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (<?= count($labels) ?> > 0) {
            const ctx = document.getElementById('myGradeChart');
            if (!ctx) return;
            
            const labels = <?= json_encode($labels) ?>;
            const dataTBM = <?= json_encode($dataTBM) ?>;
            const dataTBM_Lop = <?= json_encode($dataTBM_Lop) ?>; 

            new Chart(ctx, {
                type: 'bar', 
                data: {
                    labels: labels,
                    datasets: [
                    {
                        label: 'Điểm của bạn',
                        data: dataTBM,
                        backgroundColor: 'rgba(231, 76, 60, 0.8)', 
                        borderColor: 'rgba(231, 76, 60, 1)',
                        borderWidth: 1,
                        type: 'bar', 
                        order: 2 
                    },
                    {
                        label: 'Điểm TB của lớp',
                        data: dataTBM_Lop,
                        borderColor: 'rgba(241, 196, 15, 1)', 
                        backgroundColor: 'rgba(241, 196, 15, 1)',
                        borderWidth: 3,
                        type: 'line', 
                        fill: false,
                        tension: 0.1, 
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        order: 1 
                    }
                ]
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
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
    </script>
<?php endif; ?>