<style>
/* Đảm bảo tất cả chữ đều hiển thị màu đen */
.badge, .progress-bar, .table th, .table td {
    color: #000000 !important;
}


</style>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tạo thời khóa biểu</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quản lý thời khóa biểu</h6>
        </div>
        <div class="card-body">
 <form method="get" action="index.php">
    <input type="hidden" name="controller" value="thoikhoabieu">
    <input type="hidden" name="action" value="taotkb">
    <div class="row mb-3">
        
        <div class="col-md-3">
            <div class="form-group">
                <label><strong>Khối học</strong></label>
                <select name="maKhoi" class="form-control" required onchange="this.form.submit()">
                    <option value="">-- Chọn khối --</option>
                    <?php 
                        if (!empty($danhSachKhoi)):
                            foreach ($danhSachKhoi as $khoi): 
                    ?>
                        <option value="<?= htmlentities($khoi['maKhoi']) ?>" 
                            <?= (isset($maKhoi) && $maKhoi == $khoi['maKhoi']) ? 'selected' : '' ?>>
                            Khối <?= htmlentities($khoi['tenKhoi'] ?? '') ?>
                        </option>
                    <?php 
                            endforeach; 
                        endif;
                    ?>
                </select>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                <label><strong>Lớp học</strong></label>
                <select name="maLop" class="form-control" required onchange="this.form.submit()">
                    <option value="">-- Chọn lớp --</option>
                    <?php 
                        if (!empty($danhSachLopTheoKhoi)):
                            foreach ($danhSachLopTheoKhoi as $lop): 
                    ?>
                        <option value="<?= htmlentities($lop['maLop']) ?>" 
                            <?= (isset($maLop) && $maLop == $lop['maLop']) ? 'selected' : '' ?>>
                            <?= htmlentities($lop['tenLop'] ?? '') ?> - Khối <?= htmlentities($lop['tenKhoi'] ?? '') ?>
                        </option>
                    <?php 
                            endforeach;
                        else:
                    ?>
                        <option value="">Chọn khối trước</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                <label><strong>Chọn Tuần</strong></label>
                <input type="week" name="tuan" class="form-control" value="<?= htmlentities($tuanInput) ?>" 
                    onchange="this.form.submit()">
                <small class="form-text text-muted">
                    Tuần hiện tại: <?= date('W') ?> | 
                    <?php if (!empty($ngayApDungTuan)): ?>
                        Đang xem: Tuần <?= $tuanDuocChon ?> (<?= date('d/m/Y', strtotime($ngayApDungTuan)) ?>)
                    <?php endif; ?>
                </small>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <div class="form-group w-100">
                <label>&nbsp;</label>
                <a href="index.php?controller=thoikhoabieu&action=taotkb" class="btn btn-secondary btn-block">
                    <i class="fas fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </div>
</form>
            <div class="row">
                <!-- Cột trái: Thông tin chi tiết lớp -->
                <div class="col-md-4">
                    <?php if (!empty($chiTietLop)): ?>
                    <div class="card border-left-primary shadow h-100">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin chi tiết lớp <?= $chiTietLop['tenLop'] ?></h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Tên lớp:</strong> <?= $chiTietLop['tenLop'] ?>
                            </div>
                            <div class="mb-3">
                                <strong>Khối:</strong> <?= $chiTietLop['tenKhoi'] ?>
                            </div>
                            <div class="mb-3">
                                <strong>Giáo viên chủ nhiệm:</strong> <?= $chiTietLop['tenGiaoVien'] ?? 'Chưa phân công' ?>
                            </div>
                            <div class="mb-3">
                                <strong>Năm học:</strong> <?= $chiTietLop['namHoc'] ?? '2024-2025' ?>
                            </div>
                            <div class="mb-3">
                                <strong>Trạng thái:</strong> 
                                <span class="badge badge-success">Đang cập nhật</span>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card border-left-warning shadow h-100">
                        <div class="card-body text-center text-muted">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <p>Vui lòng chọn lớp để xem thông tin chi tiết</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Cột phải: Danh sách lớp học -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Danh sách lớp học 
                                <?php if (!empty($maKhoi)): ?>
                                    - Khối <?= htmlentities($danhSachKhoi[array_search($maKhoi, array_column($danhSachKhoi, 'maKhoi'))]['tenKhoi'] ?? '' )?>
                                <?php endif; ?>
                            </h6>
                            <span class="badge badge-info"><?= count($danhSachLopTheoKhoi) ?> lớp</span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($danhSachLopTheoKhoi)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="15%">Mã lớp</th>
                                            <th width="25%">Tên lớp</th>
                                            <th width="25%">Khối</th>
                                            <th width="25%">Giáo viên chủ nhiệm</th>
                                            <th width="10%">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($danhSachLopTheoKhoi as $lop): ?>
                                        <tr>
                                            <td><?= $lop['maLop'] ?></td>
                                            <td><strong><?= $lop['tenLop'] ?></strong></td>
                                            <td>Khối <?= $lop['tenKhoi'] ?></td>
                                            <td><?= $lop['tenGiaoVien'] ?? '<span class="text-muted">Chưa phân công</span>' ?></td>
                                            <td>
                                                <a href="index.php?controller=thoikhoabieu&action=taotkb&maLop=<?= $lop['maLop'] ?>&maKhoi=<?= $lop['maKhoi'] ?>&tuan=<?= $tuanInput ?>" 
                                                class="btn btn-info btn-sm btn-block">
                                                    <i class="fas fa-eye"></i> Xem
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> 
                                <?php if (empty($maKhoi)): ?>
                                    Vui lòng chọn khối học để xem danh sách lớp
                                <?php else: ?>
                                    Không có lớp học nào cho khối đã chọn
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                    <!-- Bảng thống kê môn học (PHẦN MỚI THÊM) -->
                    <?php if (!empty($maLop) && !empty($chiTietLop) && !empty($thongKeMonHoc)): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Thống kê môn học</h6>
                            <small class="text-muted">Tổng quan số tiết đã xếp và còn lại</small>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="25%">Môn học</th>
                                            <th width="15%" class="text-center">Số tiết quy định</th>
                                            <th width="15%" class="text-center">Đã xếp</th>
                                            <th width="15%" class="text-center">Còn lại</th>
                                            <th width="20%" class="text-center">Tiến độ</th>
                                            <th width="10%" class="text-center">Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $tongTietQuyDinh = 0;
                                        $tongTietDaXep = 0;
                                        foreach ($thongKeMonHoc as $maMon => $mon): 
                                            $tongTietQuyDinh += $mon['soTietQuyDinh'];
                                            $tongTietDaXep += $mon['soTietDaXep'];
                                            $phanTram = $mon['soTietQuyDinh'] > 0 ? round(($mon['soTietDaXep'] / $mon['soTietQuyDinh']) * 100) : 0;
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= $mon['tenMonHoc'] ?></strong>
                                                <?php if ($mon['soTietConLai'] < 0): ?>
                                                    <br><small class="text-danger">⚠ Vượt <?= abs($mon['soTietConLai']) ?> tiết</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center font-weight-bold"><?= $mon['soTietQuyDinh'] ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-info"><?= $mon['soTietDaXep'] ?> tiết</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= 
                                                    $mon['soTietConLai'] == 0 ? 'success' : 
                                                    ($mon['soTietConLai'] > 0 ? 'warning' : 'danger')
                                                ?>">
                                                    <?= $mon['soTietConLai'] ?> tiết
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar 
                                                        <?= $phanTram == 100 ? 'bg-success' : 
                                                           ($phanTram >= 50 ? 'bg-warning' : 'bg-danger') ?>" 
                                                        role="progressbar" 
                                                        style="width: <?= min($phanTram, 100) ?>%;" 
                                                        aria-valuenow="<?= $phanTram ?>" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                        <?= $phanTram ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($mon['soTietConLai'] == 0): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check"></i> Đã đủ</span>
                                                <?php elseif ($mon['soTietConLai'] > 0): ?>
                                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Thiếu</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Vượt</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="thead-dark">
                                        <tr>
                                            <th class="text-right">TỔNG CỘNG:</th>
                                            <th class="text-center"><?= $tongTietQuyDinh ?></th>
                                            <th class="text-center"><?= $tongTietDaXep ?></th>
                                            <th class="text-center"><?= $tongTietQuyDinh - $tongTietDaXep ?></th>
                                            <th colspan="2">
                                                <?php 
                                                $tongPhanTram = $tongTietQuyDinh > 0 ? round(($tongTietDaXep / $tongTietQuyDinh) * 100) : 0;
                                                ?>
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar 
                                                        <?= $tongPhanTram == 100 ? 'bg-success' : 
                                                           ($tongPhanTram >= 50 ? 'bg-warning' : 'bg-danger') ?>" 
                                                        role="progressbar" 
                                                        style="width: <?= min($tongPhanTram, 100) ?>%; font-weight: bold;" 
                                                        aria-valuenow="<?= $tongPhanTram ?>" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                        Tổng tiến độ: <?= $tongPhanTram ?>%
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Thông tin tuần hiện tại -->
                    <?php if (!empty($maLop) && !empty($chiTietLop)): ?>
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Lớp:</strong> <?= $chiTietLop['tenLop'] ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Khối:</strong> <?= $chiTietLop['tenKhoi'] ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Tuần xem:</strong> <?= $tuanDuocChon ?> 
                                (Từ <?= date('d/m/Y', strtotime($ngayApDungTuan)) ?> 
                                đến <?= date('d/m/Y', strtotime($ngayApDungTuan . ' +6 days')) ?>)
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Bảng thời khóa biểu chi tiết -->
                    <?php if (!empty($maLop) && !empty($chiTietLop)): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Thời khóa biểu chi tiết - Tuần <?= $tuanDuocChon ?> 
                                (<?= date('d/m/Y', strtotime($ngayApDungTuan)) ?>)
                            </h6>
                            <small class="text-muted">
                                Ngày áp dụng: <?= date('d/m/Y', strtotime($ngayApDungTuan)) ?>
                                | Từ <?= date('d/m/Y', strtotime($ngayApDungTuan)) ?> đến <?= date('d/m/Y', strtotime($ngayApDungTuan . ' +6 days')) ?>
                            </small>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="8%">Tiết</th>
                                            <?php
                                            $days = ['THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6', 'THU_7'];
                                            $startOfWeek = new DateTime($ngayApDungTuan);
                                            foreach ($days as $index => $day): 
                                                $currentDate = clone $startOfWeek;
                                                $currentDate->modify("+{$index} days");
                                                $dateDisplay = $currentDate->format('d/m/Y');
                                                $dateForQuery = $currentDate->format('Y-m-d');
                                                $dayName = $this->convertDayToVietnamese($day);
                                            ?>
                                                <th width="15%">
                                                    <?= $dayName ?><br>
                                                    <small class="text-muted"><?= $dateDisplay ?></small>
                                                </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($tiet = 1; $tiet <= 10; $tiet++): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold align-middle">Tiết <?= $tiet ?></td>
                                            <?php foreach ($days as $index => $day): 
                                                $currentDate = clone $startOfWeek;
                                                $currentDate->modify("+{$index} days");
                                                $dateForQuery = $currentDate->format('Y-m-d');
                                            ?>
                                            <td class="text-center position-relative">
                                                <?php
                                                $found = false;
                                                if (!empty($thoiKhoaBieu)) {
                                                    foreach ($thoiKhoaBieu as $tkb) {
                                                        // Kiểm tra theo ngày học và tiết
                                                        if ($tkb['ngayHoc'] == $dateForQuery && 
                                                            $tkb['tietBatDau'] <= $tiet && 
                                                            $tkb['tietKetThuc'] >= $tiet) {
                                                            
                                                            echo '<div class="p-2 border rounded bg-light position-relative tiet-hoc-item">';
                                                            echo '<strong>' . $tkb['tenMonHoc'] . '</strong>';
                                                            if (!empty($tkb['tenGiaoVien'])) {
                                                                echo '<br><small class="text-muted">' . $tkb['tenGiaoVien'] . '</small>';
                                                            }
                                                            if (!empty($tkb['phongHoc'])) {
                                                                echo '<br><small class="text-info">' . $tkb['phongHoc'] . '</small>';
                                                            }
                                                            
                                                            // Nút X để xóa
                                                            echo '<button type="button" class="btn btn-danger btn-sm xoa-tiet-btn" 
                                                                    style="position: absolute; top: 2px; right: 2px; display: none; padding: 0 4px; font-size: 12px;"
                                                                    onclick="xoaTietHoc(' . $tkb['maBuoiHoc'] . ')">
                                                                    ×
                                                                </button>';
                                                            
                                                            echo '</div>';
                                                            $found = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                                if (!$found) {
                                                    echo '<div class="p-2 border rounded">';
                                                    echo '<span class="text-muted">-</span>';
                                                    echo '</div>';
                                                }
                                                ?>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Form thêm chi tiết tiết học -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Chi tiết tiết học</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="index.php?controller=thoikhoabieu&action=luutiet" id="tietHocForm">
                                <input type="hidden" name="maLop" value="<?= $maLop ?>">
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><strong>Môn học</strong></label>
                                            <select name="maMonHoc" class="form-control" required id="maMonHoc" onchange="loadGiaoVienTheoMon(this.value)">
                                                <option value="">-- Chọn môn học --</option>
                                                <?php if (!empty($danhSachMonHoc)): ?>
                                                    <?php foreach ($danhSachMonHoc as $mon): ?>
                                                        <?php 
                                                            $thongKeMon = $thongKeMonHoc[$mon['maMonHoc']] ?? [
                                                                'soTietQuyDinh' => $mon['soTiet'] ?? 0,
                                                                'soTietDaXep' => 0,
                                                                'soTietConLai' => $mon['soTiet'] ?? 0
                                                            ];
                                                        ?>
                                                        <option value="<?= htmlspecialchars($mon['maMonHoc']) ?>" 
                                                            data-tiet-quy-dinh="<?= htmlspecialchars($thongKeMon['soTietQuyDinh']) ?>"
                                                            data-tiet-da-xep="<?= htmlspecialchars($thongKeMon['soTietDaXep']) ?>"
                                                            data-tiet-con-lai="<?= htmlspecialchars($thongKeMon['soTietConLai']) ?>">
                                                            <?= htmlspecialchars($mon['tenMonHoc']) ?> 
                                                            (<?= htmlspecialchars($thongKeMon['soTietDaXep']) ?>/<?= htmlspecialchars($thongKeMon['soTietQuyDinh']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="">Không có môn học cho khối này</option>
                                                <?php endif; ?>
                                            </select>
                                            <small id="subjectInfo" class="form-text text-muted">
                                                Chọn môn học để xem thông tin số tiết
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><strong>Giáo viên giảng dạy</strong></label>
                                            <select name="maGiaoVien" class="form-control" required id="maGiaoVien">
                                                <option value="">-- Chọn môn học trước --</option>
                                            </select>
                                            <small class="form-text text-muted" id="giaoVienInfo">
                                                Chọn môn học để hiển thị danh sách giáo viên
                                            </small>
                                        </div>
                                    </div>
                                    
                                    

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><strong>Phòng học (Tùy chọn)</strong></label>
                                            <input type="text" name="phongHoc" class="form-control" 
                                                    placeholder="Để trống nếu học tại lớp" id="phongHoc">
                                            <small class="form-text text-muted">
                                                Chỉ nhập khi học ở phòng chức năng (Tin, Lý, Hóa...).
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><strong>Thứ</strong></label>
                                            <select name="loaiLich" class="form-control" required id="loaiLich">
                                                <option value="">-- Chọn thứ --</option>
                                                <option value="THU_2">Thứ 2</option>
                                                <option value="THU_3">Thứ 3</option>
                                                <option value="THU_4">Thứ 4</option>
                                                <option value="THU_5">Thứ 5</option>
                                                <option value="THU_6">Thứ 6</option>
                                                <option value="THU_7">Thứ 7</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><strong>Tiết bắt đầu</strong></label>
                                            <select name="tietBatDau" class="form-control" required id="tietBatDau">
                                                <option value="">-- Chọn --</option>
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <option value="<?= $i ?>">Tiết <?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><strong>Tiết kết thúc</strong></label>
                                            <select name="tietKetThuc" class="form-control" required id="tietKetThuc">
                                                <option value="">-- Chọn --</option>
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <option value="<?= $i ?>">Tiết <?= $i ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-5 d-flex align-items-end">
                                        <div class="form-group w-100">
                                            <label>&nbsp;</label> 
                                            <div class="d-flex gap-2">
                                                <button type="submit" name="actionType" value="save" class="btn btn-success flex-grow-1">
                                                    <i class="fas fa-save"></i> Lưu tiết học
                                                </button>
                                                <button type="submit" name="actionType" value="delete" class="btn btn-danger flex-grow-1" 
                                                        onclick="return confirmDelete()">
                                                    <i class="fas fa-trash"></i> Xóa tiết học
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                                           
                    <!-- Nút xác nhận cuối cùng -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Lưu thời khóa biểu
                                </button>
                                <a href="index.php?controller=thoikhoabieu&action=quanlytkb" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('tietHocForm').reset();
}

function confirmDelete() {
    const loaiLich = document.getElementById('loaiLich').value;
    const tietBatDau = document.getElementById('tietBatDau').value;
    const tietKetThuc = document.getElementById('tietKetThuc').value;
    
    if (!loaiLich || !tietBatDau || !tietKetThuc) {
        alert('Vui lòng chọn đầy đủ thông tin thứ, tiết bắt đầu và tiết kết thúc để xóa!');
        return false;
    }
    
    return confirm(`Bạn có chắc muốn xóa tiết học:\nThứ: ${loaiLich.replace('THU_', '')}\nTiết: ${tietBatDau} - ${tietKetThuc}?`);
}

// HÀM MỚI: Hiển thị thông tin môn học
function showSubjectInfo(maMonHoc) {
    const selectedOption = document.querySelector(`#maMonHoc option[value="${maMonHoc}"]`);
    const infoElement = document.getElementById('subjectInfo');
    
    if (selectedOption && maMonHoc) {
        const tietQuyDinh = selectedOption.getAttribute('data-tiet-quy-dinh');
        const tietDaXep = selectedOption.getAttribute('data-tiet-da-xep');
        const tietConLai = selectedOption.getAttribute('data-tiet-con-lai');
        
        let statusClass = 'text-success';
        let statusIcon = '✅';
        
        if (tietConLai > 0) {
            statusClass = 'text-warning';
            statusIcon = '⏳';
        } else if (tietConLai < 0) {
            statusClass = 'text-danger';
            statusIcon = '⚠️';
        }
        
        infoElement.innerHTML = `
            <span class="text-primary">📚 Quy định: ${tietQuyDinh} tiết/tuần</span> | 
            <span class="text-info">📊 Đã xếp: ${tietDaXep} tiết</span> | 
            <span class="${statusClass}">${statusIcon} Còn lại: ${tietConLai} tiết</span>
        `;
        
        // Highlight nếu môn đã đủ tiết
        if (tietConLai == 0) {
            infoElement.innerHTML += ' <span class="badge badge-success">ĐÃ ĐỦ TIẾT</span>';
        }
    } else {
        infoElement.innerHTML = '📝 Chọn môn học để xem thông tin số tiết';
    }
}

// Tự động cập nhật khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const maMonHocSelect = document.getElementById('maMonHoc');
    if (maMonHocSelect) {
        showSubjectInfo(maMonHocSelect.value);
        
        // Cập nhật real-time khi thay đổi selection
        maMonHocSelect.addEventListener('change', function() {
            showSubjectInfo(this.value);
        });
    }
});
// Hàm load giáo viên theo môn học
function loadGiaoVienTheoMon(maMonHoc) {
    const giaoVienSelect = document.getElementById('maGiaoVien');
    const giaoVienInfo = document.getElementById('giaoVienInfo');
    
    if (!maMonHoc) {
        giaoVienSelect.innerHTML = '<option value="">-- Chọn môn học trước --</option>';
        giaoVienInfo.innerHTML = 'Chọn môn học để hiển thị danh sách giáo viên';
        return;
    }
    
    // Hiển thị loading
    giaoVienSelect.innerHTML = '<option value="">Đang tải...</option>';
    giaoVienInfo.innerHTML = 'Đang tải danh sách giáo viên...';
    
    // Gọi AJAX để lấy danh sách giáo viên
    fetch(`index.php?controller=thoikhoabieu&action=getGiaoVienByMon&maMonHoc=${maMonHoc}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                giaoVienSelect.innerHTML = '<option value="">-- Chọn giáo viên --</option>';
                data.forEach(gv => {
                    giaoVienSelect.innerHTML += `<option value="${gv.maGiaoVien}">${gv.hoTen}${gv.chuyenMon ? ' - ' + gv.chuyenMon : ''}</option>`;
                });
                giaoVienInfo.innerHTML = `Tìm thấy ${data.length} giáo viên`;
            } else {
                giaoVienSelect.innerHTML = '<option value="">Không có giáo viên nào</option>';
                giaoVienInfo.innerHTML = 'Không tìm thấy giáo viên nào cho môn học này';
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            giaoVienSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            giaoVienInfo.innerHTML = 'Có lỗi xảy ra khi tải danh sách giáo viên';
        });
    
    // Cập nhật thông tin môn học
    showSubjectInfo(maMonHoc);
}

// Cập nhật sự kiện change cho select môn học
document.addEventListener('DOMContentLoaded', function() {
    const maMonHocSelect = document.getElementById('maMonHoc');
    if (maMonHocSelect) {
        maMonHocSelect.addEventListener('change', function() {
            loadGiaoVienTheoMon(this.value);
            showSubjectInfo(this.value);
        });
        
        // Load giáo viên nếu đã có môn học được chọn
        if (maMonHocSelect.value) {
            loadGiaoVienTheoMon(maMonHocSelect.value);
        }
    }
});
</script>
<!-- Thêm CSS -->
<style>
.tiet-hoc-item:hover .xoa-tiet-btn {
    display: block !important;
}
</style>

<!-- Thêm JavaScript -->
<script>
function xoaTietHoc(maBuoiHoc) {
    if (confirm('Bạn có chắc muốn xóa tiết học này?')) {
        // Gửi AJAX request để xóa
        fetch('index.php?controller=thoikhoabieu&action=xoaBuoiHoc&maBuoiHoc=' + maBuoiHoc, {
            method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Reload trang để cập nhật TKB
            } else {
                alert(data.message || 'Có lỗi xảy ra khi xóa tiết học');
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            alert('Có lỗi xảy ra khi xóa tiết học');
        });
    }
}

</script>
