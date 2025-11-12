<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Thời khóa biểu</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <?php
                $userRole = $_SESSION['user']['vaiTro'] ?? '';
                if ($userRole === 'HOCSINH') echo 'Thời khóa biểu của tôi';
                elseif ($userRole === 'GIAOVIEN') echo 'Lịch dạy của tôi';
                elseif ($userRole === 'PHUHUYNH') echo 'Thời khóa biểu của con';
                else echo 'Thời khóa biểu';
                ?>
            </h6>
        </div>
        <div class="card-body">
            <!-- Form chọn tuần -->
            <form method="GET" class="mb-3">
                <input type="hidden" name="controller" value="thoikhoabieu">
                <input type="hidden" name="action" value="xemtkb">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label>Chọn tuần:</label>
                    </div>
                    <div class="col-auto">
                        <input type="week" name="tuan" value="<?= $_GET['tuan'] ?? date('Y-\WW') ?>" 
                               class="form-control" onchange="this.form.submit()">
                    </div>
                </div>
            </form>

            <?php if (empty($thoiKhoaBieu)): ?>
                <div class="alert alert-info">Chưa có thời khóa biểu cho tuần này.</div>
            <?php else: ?>
                <!-- Hiển thị dạng bảng cho học sinh/phụ huynh -->
                <?php if (in_array($userRole, ['HOCSINH', 'PHUHUYNH', 'QTV', 'BGH'])): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Thứ</th>
                                    <th>Tiết 1-2</th>
                                    <th>Tiết 3-4</th>
                                    <th>Tiết 5-6</th>
                                    <th>Tiết 7-8</th>
                                    <th>Tiết 9-10</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $days = ['THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6', 'THU_7'];
                                $periods = [1, 3, 5, 7, 9];
                                
                                foreach ($days as $day): 
                                ?>
                                <tr>
                                    <td class="font-weight-bold"><?= $this->convertDay($day) ?></td>
                                    <?php foreach ($periods as $period): ?>
                                    <td>
                                        <?php
                                        $found = false;
                                        foreach ($thoiKhoaBieu as $tkb) {
                                            if ($tkb['loaiLich'] === $day && 
                                                $tkb['tietBatDau'] <= $period && 
                                                $tkb['tietKetThuc'] >= $period + 1) {
                                                echo $tkb['tenMonHoc'];
                                                if (!empty($tkb['tenGiaoVien'])) {
                                                    echo '<br><small class="text-muted">' . $tkb['tenGiaoVien'] . '</small>';
                                                }
                                                if (!empty($tkb['phongHoc'])) {
                                                    echo '<br><small class="text-muted">' . $tkb['phongHoc'] . '</small>';
                                                }
                                                $found = true;
                                                break;
                                            }
                                        }
                                        if (!$found) echo '-';
                                        ?>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <!-- Hiển thị dạng danh sách cho giáo viên -->
                    <div class="list-group">
                        <?php foreach ($thoiKhoaBieu as $tkb): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><?= $tkb['tenMonHoc'] ?> - <?= $tkb['tenLop'] ?></h5>
                                <small><?= $this->convertDay($tkb['loaiLich']) ?></small>
                            </div>
                            <p class="mb-1">
                                Tiết: <?= $tkb['tietBatDau'] ?>-<?= $tkb['tietKetThuc'] ?> | 
                                Phòng: <?= $tkb['phongHoc'] ?> |
                                Ngày: <?= date('d/m/Y', strtotime($tkb['ngayApDung'])) ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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