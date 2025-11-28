<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        Thời khóa biểu 
        <?php if ($userRole === 'GIAOVIEN'): ?>
            / Lịch Dạy
        <?php endif; ?>
    </h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?php
                $userRole = $_SESSION['user']['vaiTro'] ?? '';
                $isViewingSelfSchedule = ($userRole === 'GIAOVIEN' && empty($maLop));
                
                if ($isViewingSelfSchedule) {
                    echo 'Lịch Dạy Cá Nhân (Giáo Viên)';
                } elseif ($userRole === 'HOCSINH' || $userRole === 'PHUHUYNH') {
                    echo 'Thời khóa biểu của tôi';
                } elseif (!empty($maLop)) {
                    // Khi QTV/BGH/GV chọn xem TKB lớp
                    echo 'Thời khóa biểu Lớp ' . ($chiTietLop['tenLop'] ?? '');
                } else {
                    echo 'Quản lý Thời khóa biểu'; // Trường hợp QTV/BGH chưa chọn lớp
                }
                ?>
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" id="tkbForm" action="index.php" class="mb-4">
                <input type="hidden" name="controller" value="thoikhoabieu">
                <input type="hidden" name="action" value="xemluoi">
                <div class="form-row align-items-center">
                    
                    <?php 
                    // PHUHUYNH: Hiển thị dropdown chọn học sinh nếu có nhiều con
                    if ($userRole === 'PHUHUYNH' && !empty($danhSachCon) && count($danhSachCon) > 1): 
                    ?>
                    <div class="col-md-4 mb-2">
                        <label class="mr-2">Chọn học sinh:</label>
                        <select name="maHocSinh" class="form-control" onchange="document.getElementById('tkbForm').submit()">
                            <option value="">-- Chọn học sinh --</option>
                            <?php 
                            $maHocSinhHienTai = $_GET['maHocSinh'] ?? '';
                            foreach ($danhSachCon as $con): 
                                if (is_array($con) && isset($con['maHocSinh'])): 
                            ?>
                                <option value="<?= htmlspecialchars($con['maHocSinh']) ?>" 
                                    <?= ($maHocSinhHienTai == $con['maHocSinh']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($con['hoTen'] ?? 'Học sinh') ?> 
                                    - Lớp <?= htmlspecialchars($con['tenLop'] ?? '') ?>
                                </option>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </select>
                    </div>
                    <?php 
                    // PHUHUYNH có 1 con: giữ maHocSinh dạng hidden
                    elseif ($userRole === 'PHUHUYNH' && !empty($danhSachCon) && count($danhSachCon) === 1): 
                    ?>
                        <input type="hidden" name="maHocSinh" value="<?= htmlspecialchars($danhSachCon[0]['maHocSinh'] ?? '') ?>">
                    <?php 
                    endif; 
                    ?>
                    
                    <?php 
                    // QTV, BGH, GV (chọn lớp khác để xem TKB lớp) được chọn lớp
                    if (in_array($userRole, ['GIAOVIEN', 'QTV', 'BGH'])): 
                    ?>
                    <div class="col-md-4 mb-2">
                        <label class="mr-2">Chọn lớp (hoặc để trống xem Lịch Dạy):</label>
                        <select name="maLop" class="form-control" onchange="document.getElementById('tkbForm').submit()">
                            <option value="">
                                <?= $userRole === 'GIAOVIEN' ? '-- Lịch Dạy Của Tôi --' : '-- Chọn lớp --' ?>
                            </option>
                            <?php foreach ($danhSachLop as $lop): ?>
                                <option value="<?= htmlspecialchars($lop['maLop']) ?>" 
                                    <?= ($maLop == $lop['maLop']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($lop['tenLop']) ?> - Khối <?= htmlspecialchars($lop['tenKhoi'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php 
                    // HS/PH: Giữ lại maLop dưới dạng hidden input để filter TKB theo lớp của họ
                    elseif (in_array($userRole, ['HOCSINH', 'PHUHUYNH'])): 
                    ?>
                        <input type="hidden" name="maLop" value="<?= htmlspecialchars($maLop) ?>">
                    <?php endif; ?>
                    
                    <div class="col-md-4 mb-2">
                        <label class="mr-2">Chọn tuần:</label>
                        <input type="week" name="tuan" value="<?= htmlspecialchars($_GET['tuan'] ?? date('Y-\WW')) ?>" 
                            class="form-control" onchange="document.getElementById('tkbForm').submit()">
                    </div>
                </div>
            </form>

            <?php 
            // Hiển thị thông báo nếu phụ huynh có nhiều con nhưng chưa chọn
            if ($userRole === 'PHUHUYNH' && !empty($danhSachCon) && count($danhSachCon) > 1 && empty($maLop)): 
            ?>
            <div class="alert alert-info">
                <strong>Thông báo:</strong> Bạn có <?= count($danhSachCon) ?> học sinh. Vui lòng chọn một học sinh để xem thời khóa biểu.
            </div>
            <?php endif; ?>

            <?php if (!empty($maLop) && !empty($chiTietLop) && !$isViewingSelfSchedule): ?>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Thông tin lớp **<?= htmlspecialchars($chiTietLop['tenLop']) ?>**
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Tên lớp:</strong> <?= htmlspecialchars($chiTietLop['tenLop']) ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Khối:</strong> <?= htmlspecialchars($chiTietLop['tenKhoi']) ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Giáo viên CN:</strong> <?= htmlspecialchars($chiTietLop['tenGiaoVien'] ?? 'Chưa phân công') ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Sĩ số:</strong> <?= htmlspecialchars($chiTietLop['siSo']) ?> học sinh
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($thoiKhoaBieu) && (!empty($maLop) || $isViewingSelfSchedule)): ?>
                <div class="alert alert-info">Chưa có thời khóa biểu/lịch dạy cho lựa chọn này.</div>
            <?php elseif (empty($maLop) && in_array($userRole, ['QTV', 'BGH'])): ?>
                <div class="alert alert-warning">Vui lòng chọn lớp để xem thời khóa biểu.</div>
            <?php elseif (!empty($thoiKhoaBieu)): ?>
                
                <?php
                $tkbGrid = [];
                $days = ['THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6', 'THU_7'];
                $maxTiet = 10;
                
                foreach ($thoiKhoaBieu as $tkb) {
                    $loaiLich = $tkb['loaiLich'];
                    $tietBatDau = (int)$tkb['tietBatDau'];
                    $tietKetThuc = (int)$tkb['tietKetThuc'];
                    
                    // Xác định nội dung phụ cần hiển thị
                    if ($isViewingSelfSchedule) {
                        // GV xem lịch cá nhân -> hiển thị Tên Lớp
                        $infoText = $tkb['tenLop'] ?? 'N/A';
                        $infoLabel = 'Lớp';
                        $infoClass = 'text-primary';
                    } else {
                        // Xem theo Lớp (HS/PH/QTV) -> hiển thị Tên Giáo viên
                        $infoText = $tkb['tenGiaoVien'] ?? 'N/A';
                        $infoLabel = 'GV';
                        $infoClass = 'text-success';
                    }

                    $noiDung = [
                        'monHoc' => $tkb['tenMonHoc'] ?? 'N/A',
                        'phongHoc' => $tkb['phongHoc'] ?? '',
                        'infoText' => $infoText,
                        'infoLabel' => $infoLabel,
                        'infoClass' => $infoClass,
                        'tietKetThuc' => $tietKetThuc // Lưu lại để dùng cho logic gộp ô (nếu cần)
                    ];

                    // Chỉ điền nội dung vào tiết bắt đầu
                    $tkbGrid[$loaiLich][$tietBatDau] = $noiDung;

                    // Đánh dấu các ô tiết học giữa là 'merged' để ẩn nội dung
                    for ($tiet = $tietBatDau + 1; $tiet <= $tietKetThuc; $tiet++) {
                        $tkbGrid[$loaiLich][$tiet] = ['merged' => true];
                    }

                    // Cập nhật maxTiet (để render bảng không quá dài)
                    $maxTiet = max($maxTiet, $tietKetThuc);
                }
                ?>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th width="8%">Tiết</th>
                                <?php foreach ($days as $day): ?>
                                    <th width="15%"><?= convertDay($day) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Render bảng
                            for ($tiet = 1; $tiet <= $maxTiet; $tiet++): 
                            ?>
                            <tr>
                                <td class="text-center font-weight-bold">Tiết <?= $tiet ?></td>
                                <?php foreach ($days as $day): ?>
                                <td>
                                    <?php
                                    $cell = $tkbGrid[$day][$tiet] ?? null;
                                    
                                    if ($cell && isset($cell['merged'])) {
                                        // Ô bị gộp, không hiển thị gì
                                        echo '';
                                    } elseif ($cell && !isset($cell['merged'])) {
                                        // Ô bắt đầu của một tiết học
                                        echo '<div class="text-center">';
                                        echo '<strong>' . htmlspecialchars($cell['monHoc']) . '</strong>';
                                        
                                        if (!empty($cell['infoText']) && $cell['infoText'] !== 'N/A') {
                                            echo '<br><small class="' . $cell['infoClass'] . '">' . htmlspecialchars($cell['infoLabel']) . ': ' . htmlspecialchars($cell['infoText']) . '</small>';
                                        }
                                        
                                        if (!empty($cell['phongHoc'])) {
                                            echo '<br><small class="text-info">Phòng: ' . htmlspecialchars($cell['phongHoc']) . '</small>';
                                        }
                                        echo '</div>';

                                        // Logic colspan/rowspan (Nếu muốn gộp ô HTML, bạn phải dùng rowspan/colspan
                                        // và logic gộp phải nằm ngoài vòng lặp table/tr/td này.
                                        // Hiện tại, tôi giữ lại cách dùng CSS đơn giản, chỉ hiển thị nội dung ở ô bắt đầu).
                                    } else {
                                        echo '<div class="text-center text-muted">-</div>';
                                    }
                                    ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Hàm chuyển đổi thứ (Giữ nguyên)
function convertDay($loaiLich) {
    $days = [
        'THU_2' => 'Thứ 2',
        'THU_3' => 'Thứ 3', 
        'THU_4' => 'Thứ 4',
        'THU_5' => 'Thứ 5',
        'THU_6' => 'Thứ 6',
        'THU_7' => 'Thứ 7'
    ];
    return $days[$loaiLich] ?? $loaiLich;
}
?>