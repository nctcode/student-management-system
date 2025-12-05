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
                if ($userRole === 'PHUHUYNH' && !empty($danhSachCon)): 
                ?>
                <div class="col-md-3 mb-2">
                    <label class="mr-2">Chọn học sinh:</label>
                    <select name="maHocSinh" class="form-control" onchange="document.getElementById('tkbForm').submit()">
                        <option value="">-- Chọn học sinh --</option>
                        <?php 
                        $maHocSinhHienTai = $_GET['maHocSinh'] ?? '';
                        foreach ($danhSachCon as $con): 
                            // Chỉ hiển thị nếu có maHocSinh
                            if (isset($con['maHocSinh'])): 
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
                if (count($danhSachCon) === 1 && isset($danhSachCon[0]['maHocSinh'])): 
                ?>
                    <input type="hidden" name="maHocSinh" value="<?= htmlspecialchars($danhSachCon[0]['maHocSinh']) ?>">
                <?php endif; ?>

                <?php endif; ?>
                
                <!-- XÓA TOÀN BỘ PHẦN CHỌN LỚP CHO QTV, BGH, GV -->
                <?php 
                // GIỮ LẠI maLop dưới dạng hidden input cho tất cả vai trò (nếu có)
                if (!empty($maLop)): 
                ?>
                    <input type="hidden" name="maLop" value="<?= htmlspecialchars($maLop) ?>">
                <?php endif; ?>
                
                <!-- THÊM: Phần chọn tuần cho tất cả người dùng -->
                <div class="col-md-4 mb-2">
                    <label class="mr-2">Chọn tuần:</label>
                    <input type="week" name="tuan" value="<?= htmlspecialchars($_GET['tuan'] ?? date('Y-\WW')) ?>" 
                        class="form-control" onchange="document.getElementById('tkbForm').submit()">
                </div>
                
                <!-- THÊM: Nút xem tuần hiện tại -->
                <div class="col-md-4 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-primary btn-block" 
                            onclick="window.location.href='index.php?controller=thoikhoabieu&action=xemluoi<?= !empty($queryStringNoTuan) ? "&" . $queryStringNoTuan : "" ?>'">
                        <i class="fas fa-calendar-week"></i> Tuần hiện tại
                    </button>
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
                                            <strong>Tuần xem:</strong> <?= $tuanDuocChon ?? 'Hiện tại' ?> 
                                            (<?= date('d/m/Y', strtotime($ngayApDungTuan ?? date('Y-m-d'))) ?>)
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
                <div class="alert alert-info">
                    Chưa có thời khóa biểu/lịch dạy cho 
                    <?php if (!empty($tuanDuocChon)): ?>
                        tuần <?= $tuanDuocChon ?>.
                    <?php else: ?>
                        lựa chọn này.
                    <?php endif; ?>
                </div>
            <?php elseif (empty($maLop) && in_array($userRole, ['QTV', 'BGH'])): ?>
                <div class="alert alert-warning">Vui lòng chọn lớp để xem thời khóa biểu.</div>
            <?php elseif (!empty($thoiKhoaBieu)): ?>
                
                <?php
                $tkbGrid = [];
                $days = ['THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6', 'THU_7'];
                $maxTiet = 10;
                
                // Tính toán ngày cho từng thứ trong tuần
                $ngayTrongTuan = [];
                if (!empty($ngayApDungTuan)) {
                    $startOfWeek = new DateTime($ngayApDungTuan);
                    foreach ($days as $index => $day) {
                        $currentDate = clone $startOfWeek;
                        $currentDate->modify("+{$index} days");
                        $ngayTrongTuan[$day] = $currentDate->format('d/m/Y');
                    }
                }
                
                foreach ($thoiKhoaBieu as $tkb) {
                    $ngayHoc = $tkb['ngayHoc'] ?? '';
                    $tietBatDau = (int)$tkb['tietBatDau'];
                    $tietKetThuc = (int)$tkb['tietKetThuc'];
                    
                    // Tìm thứ tương ứng với ngày học
                    $loaiLich = '';
                    foreach ($ngayTrongTuan as $day => $dateStr) {
                        if ($ngayHoc == date('Y-m-d', strtotime(str_replace('/', '-', $dateStr)))) {
                            $loaiLich = $day;
                            break;
                        }
                    }
                    
                    if (empty($loaiLich)) continue;
                    
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
                        'tietKetThuc' => $tietKetThuc,
                        'ngayHoc' => $ngayHoc
                    ];

                    // Chỉ điền nội dung vào tiết bắt đầu
                    $tkbGrid[$loaiLich][$tietBatDau] = $noiDung;

                    // Đánh dấu các ô tiết học giữa là 'merged' để ẩn nội dung
                    for ($tiet = $tietBatDau + 1; $tiet <= $tietKetThuc; $tiet++) {
                        $tkbGrid[$loaiLich][$tiet] = ['merged' => true];
                    }

                    // Cập nhật maxTiet
                    $maxTiet = max($maxTiet, $tietKetThuc);
                }
                ?>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th width="8%">Tiết</th>
                                <?php 
                                $days = ['THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6', 'THU_7'];
                                
                                foreach ($days as $index => $day): 
                                    $ngayHienThi = $ngayTrongTuan[$day] ?? '';
                                ?>
                                    <th width="15%" class="text-center">
                                        <div><?= convertDay($day) ?></div>
                                        <?php if (!empty($ngayHienThi)): ?>
                                            <div class="small text-muted text-center"><?= $ngayHienThi ?></div>
                                        <?php endif; ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            for ($tiet = 1; $tiet <= $maxTiet; $tiet++): 
                            ?>
                            <tr>
                                <td class="text-center font-weight-bold">Tiết <?= $tiet ?></td>
                                <?php foreach ($days as $day): ?>
                                <td>
                                    <?php
                                    $cell = $tkbGrid[$day][$tiet] ?? null;
                                    
                                    if ($cell && isset($cell['merged'])) {
                                        echo '';
                                    } elseif ($cell && !isset($cell['merged'])) {
                                        echo '<div class="text-center">';
                                        echo '<strong>' . htmlspecialchars($cell['monHoc']) . '</strong>';
                                        
                                        if (!empty($cell['infoText']) && $cell['infoText'] !== 'N/A') {
                                            echo '<br><small class="' . $cell['infoClass'] . '">' . htmlspecialchars($cell['infoLabel']) . ': ' . htmlspecialchars($cell['infoText']) . '</small>';
                                        }
                                        
                                        if (!empty($cell['phongHoc'])) {
                                            echo '<br><small class="text-info">Phòng: ' . htmlspecialchars($cell['phongHoc']) . '</small>';
                                        }
                                        
                                        
                                        
                                        echo '</div>';
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
                
                <!-- THÊM: Thông tin tuần -->
                <div class="mt-3">
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Tuần đang xem:</strong> <?= $tuanDuocChon ?? 'Hiện tại' ?> 
                                <?php if (!empty($ngayApDungTuan)): ?>
                                    (Từ <?= date('d/m/Y', strtotime($ngayApDungTuan)) ?> 
                                    đến <?= date('d/m/Y', strtotime($ngayApDungTuan . ' +6 days')) ?>)
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <strong>Tổng số tiết:</strong> <?= count($thoiKhoaBieu) ?> buổi học
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Hàm chuyển đổi thứ
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