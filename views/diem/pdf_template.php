<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng điểm</title>
    <style>
        body { 
            font-family: 'dejavusans', sans-serif; 
            font-size: 11px; 
            color: #333; 
        }
        h1 { 
            text-align: center; 
            color: #0056b3; 
            border-bottom: 2px solid #ccc; 
            padding-bottom: 10px; 
        }
        h3 { 
            color: #0056b3; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 5px; 
        }
        .info { 
            margin-bottom: 20px; 
            width: 100%; 
        }
        .info td { 
            padding: 5px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        th, td { 
            border: 1px solid #999; 
            padding: 6px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
            font-weight: bold; 
        }
        td.mon { 
            font-weight: bold; 
        }
        td.tbm {
            color: red; 
            font-weight: bold; 
        }
        tfoot td { 
            font-weight: bold; 
            background-color: #f9f9f9; 
        }
    </style>
</head>
<body>
    <h1>BẢNG ĐIỂM CÁ NHÂN</h1>

    <table class="info">
        <tr>
            <td><strong>Học sinh:</strong></td>
            <td><?= htmlspecialchars($hocSinhInfo['hoTen']) ?></td>
        </tr>
        <?php if ($viewMode === 'single'): ?>
        <tr>
            <td><strong>Học kỳ:</strong></td>
            <td><?= htmlspecialchars($hocKyChon) ?> (<?= htmlspecialchars($namHocChon) ?>)</td>
        </tr>
        <?php else: ?>
         <tr>
            <td><strong>Xem:</strong></td>
            <td>Toàn bộ các kỳ</td>
        </tr>
        <?php endif; ?>
    </table>
    
    <?php if ($viewMode === 'single'): ?>
        <?php 
            $bangDiem = $bangDiemData['single']['bangDiem'];
            $TBM_HocKy = $bangDiemData['single']['TBM_HocKy'];
        ?>
        <h3>Bảng điểm chi tiết <?= htmlspecialchars($hocKyChon) ?> (<?= htmlspecialchars($namHocChon) ?>)</h3>
        <table>
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
                <?php foreach ($bangDiem as $mon): ?>
                    <tr>
                        <td class="mon"><?= htmlspecialchars($mon['tenMonHoc']) ?></td>
                        <td><?= implode(', ', $mon['MIENG']) ?></td>
                        <td><?= implode(', ', $mon['15_PHUT']) ?></td>
                        <td><?= implode(', ', $mon['1_TIET']) ?></td>
                        <td><?= implode(', ', $mon['CUOI_KY']) ?></td>
                        <td class="tbm"><?= $mon['TBM'] !== null ? number_format($mon['TBM'], 2) : ' ' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <?php if ($TBM_HocKy !== null): ?>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: left;">Trung bình Học kỳ (GPA)</td>
                    <td class="tbm"><?= number_format($TBM_HocKy, 2) ?></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>

    <?php elseif ($viewMode === 'all'): ?>
        <?php foreach ($bangDiemData as $key => $result): ?>
            <?php 
                list($namHoc, $hocKy) = explode('|', $key);
                $bangDiem = $result['bangDiem'];
                $TBM_HocKy = $result['TBM_HocKy'];
            ?>
            <h3>Bảng điểm chi tiết <?= htmlspecialchars($hocKy) ?> (<?= htmlspecialchars($namHoc) ?>)</h3>
            <table>
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
                    <?php foreach ($bangDiem as $mon): ?>
                        <tr>
                            <td class="mon"><?= htmlspecialchars($mon['tenMonHoc']) ?></td>
                            <td><?= implode(', ', $mon['MIENG']) ?></td>
                            <td><?= implode(', ', $mon['15_PHUT']) ?></td>
                            <td><?= implode(', ', $mon['1_TIET']) ?></td>
                            <td><?= implode(', ', $mon['CUOI_KY']) ?></td>
                            <td class="tbm"><?= $mon['TBM'] !== null ? number_format($mon['TBM'], 2) : ' ' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php if ($TBM_HocKy !== null): ?>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: left;">Trung bình Học kỳ (GPA)</td>
                        <td class="tbm"><?= number_format($TBM_HocKy, 2) ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>