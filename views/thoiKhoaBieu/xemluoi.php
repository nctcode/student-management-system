<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Thời khóa biểu</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?php
                $userRole = $_SESSION['user']['vaiTro'] ?? '';
                if ($userRole === 'HOCSINH') echo 'Thời khóa biểu của tôi';
                elseif ($userRole === 'GIAOVIEN') echo 'Thời khóa biểu lớp học';
                elseif ($userRole === 'PHUHUYNH') echo 'Thời khóa biểu của con';
                else echo 'Thời khóa biểu';
                ?>
            </h6>
        </div>
        <div class="card-body">
            <!-- Form chọn lớp và tuần -->
            <form method="GET" class="mb-4">
                <input type="hidden" name="controller" value="thoikhoabieu">
                <input type="hidden" name="action" value="xemluoi">
                <div class="form-row align-items-center">
                    <?php if (in_array($userRole, ['GIAOVIEN', 'QTV', 'BGH'])): ?>
                    <div class="col-md-4 mb-2">
                        <label class="mr-2">Chọn lớp:</label>
                        <select name="maLop" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Chọn lớp --</option>
                            <?php foreach ($danhSachLop as $lop): ?>
                                <option value="<?= $lop['maLop'] ?>" 
                                    <?= ($maLop == $lop['maLop']) ? 'selected' : '' ?>>
                                    <?= $lop['tenLop'] ?> - Khối <?= $lop['tenKhoi'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-md-4 mb-2">
                        <label class="mr-2">Chọn tuần:</label>
                        <input type="week" name="tuan" value="<?= $_GET['tuan'] ?? date('Y-\WW') ?>" 
                               class="form-control" onchange="this.form.submit()">
                    </div>
                </div>
            </form>

            <?php if (!empty($maLop) && !empty($chiTietLop)): ?>
            <!-- Thông tin chi tiết lớp -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Thông tin lớp <?= $chiTietLop['tenLop'] ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Tên lớp:</strong> <?= $chiTietLop['tenLop'] ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Khối:</strong> <?= $chiTietLop['tenKhoi'] ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Giáo viên CN:</strong> <?= $chiTietLop['tenGiaoVien'] ?? 'Chưa phân công' ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Sĩ số:</strong> <?= $chiTietLop['siSo'] ?> học sinh
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($thoiKhoaBieu) && !empty($maLop)): ?>
                <div class="alert alert-info">Chưa có thời khóa biểu cho lớp này.</div>
            <?php elseif (empty($maLop) && in_array($userRole, ['GIAOVIEN', 'QTV', 'BGH'])): ?>
                <div class="alert alert-warning">Vui lòng chọn lớp để xem thời khóa biểu.</div>
            <?php elseif (!empty($thoiKhoaBieu)): ?>
                <!-- Hiển thị thời khóa biểu dạng lưới -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th width="8%">Tiết</th>
                                <th width="15%">Thứ 2</th>
                                <th width="15%">Thứ 3</th>
                                <th width="15%">Thứ 4</th>
                                <th width="15%">Thứ 5</th>
                                <th width="15%">Thứ 6</th>
                                <th width="15%">Thứ 7</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($tiet = 1; $tiet <= 10; $tiet++): ?>
                            <tr>
                                <td class="text-center font-weight-bold">Tiết <?= $tiet ?></td>
                                <?php 
                                $days = ['THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6', 'THU_7'];
                                foreach ($days as $day): 
                                ?>
                                <td>
                                    <?php
                                    $found = false;
                                    foreach ($thoiKhoaBieu as $tkb) {
                                        if ($tkb['loaiLich'] === $day && 
                                            $tkb['tietBatDau'] <= $tiet && 
                                            $tkb['tietKetThuc'] >= $tiet) {
                                            echo '<div class="text-center">';
                                            echo '<strong>' . $tkb['tenMonHoc'] . '</strong>';
                                            if (!empty($tkb['tenGiaoVien'])) {
                                                echo '<br><small class="text-muted">' . $tkb['tenGiaoVien'] . '</small>';
                                            }
                                            if (!empty($tkb['phongHoc'])) {
                                                echo '<br><small class="text-info">' . $tkb['phongHoc'] . '</small>';
                                            }
                                            echo '</div>';
                                            $found = true;
                                            break;
                                        }
                                    }
                                    if (!$found) {
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