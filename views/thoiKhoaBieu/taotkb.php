<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tạo thời khóa biểu</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quản lý thời khóa biểu</h6>
        </div>
        <div class="card-body">
            <!-- Form chọn khối/lớp -->
            <form method="GET" class="mb-4">
                <input type="hidden" name="controller" value="thoikhoabieu">
                <input type="hidden" name="action" value="taotkb">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Khối học</strong></label>
                            <select name="maKhoi" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Chọn khối --</option>
                                <?php foreach ($danhSachKhoi as $khoi): ?>
                                    <option value="<?= $khoi['maKhoi'] ?>" 
                                        <?= ($maKhoi == $khoi['maKhoi']) ? 'selected' : '' ?>>
                                        Khối <?= $khoi['tenKhoi'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Lớp học</strong></label>
                            <select name="maLop" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Chọn lớp --</option>
                                <?php foreach ($danhSachLop as $lop): ?>
                                    <option value="<?= $lop['maLop'] ?>" 
                                        <?= ($maLop == $lop['maLop']) ? 'selected' : '' ?>>
                                        <?= $lop['tenLop'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Xác nhận</button>
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
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách lớp học</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($danhSachLopTheoKhoi)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Mã lớp</th>
                                            <th>Tên lớp</th>
                                            <th>Giáo viên chủ nhiệm</th>
                                            <th>Xem chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($danhSachLopTheoKhoi as $lop): ?>
                                        <tr>
                                            <td><?= $lop['maLop'] ?></td>
                                            <td><?= $lop['tenLop'] ?></td>
                                            <td><?= $lop['tenGiaoVien'] ?? 'Chưa phân công' ?></td>
                                            <td>
                                                <a href="index.php?controller=thoikhoabieu&action=taotkb&maLop=<?= $lop['maLop'] ?>" 
                                                   class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Xem chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">
                                Vui lòng chọn khối học để xem danh sách lớp
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Bảng thời khóa biểu chi tiết -->
                    <?php if (!empty($maLop) && !empty($chiTietLop)): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thời khóa biểu chi tiết</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="8%">Tiết</th>
                                            <?php
                                            $days = ['THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6'];
                                            $currentDate = date('Y-m-d');
                                            foreach ($days as $day): 
                                                $date = date('d/m/Y', strtotime($currentDate . ' + ' . (array_search($day, $days)) . ' days'));
                                            ?>
                                            <th width="18%">
                                                <?= $this->convertDayToVietnamese($day) ?><br>
                                                <small class="text-muted"><?= $date ?></small>
                                            </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($tiet = 1; $tiet <= 10; $tiet++): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold align-middle">Tiết <?= $tiet ?></td>
                                            <?php foreach ($days as $day): ?>
                                            <td class="text-center">
                                                <?php
                                                $found = false;
                                                if (!empty($thoiKhoaBieu)) {
                                                    foreach ($thoiKhoaBieu as $tkb) {
                                                        if ($tkb['loaiLich'] === $day && 
                                                            $tkb['tietBatDau'] <= $tiet && 
                                                            $tkb['tietKetThuc'] >= $tiet) {
                                                            echo '<div class="p-2 border rounded bg-light">';
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
                <!-- Môn học -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Môn học</strong></label>
                        <select name="maMonHoc" class="form-control" required id="maMonHoc">
                            <option value="">-- Chọn môn học --</option>
                            <option value="1">Toán</option>
                            <option value="2">Ngữ Văn</option>
                            <option value="3">Tiếng Anh</option>
                            <option value="4">Vật Lý</option>
                            <option value="5">Hóa Học</option>
                            <option value="6">Lịch Sử</option>
                            <option value="7">Địa Lý</option>
                            <option value="8">Sinh Học</option>
                            <option value="9">Tin Học</option>
                            <option value="10">Thể Dục</option>
                            <option value="11">GD Quốc Phòng</option>
                            <option value="12">GD Công Dân</option>
                            <option value="13">Công Nghệ</option>
                            <option value="14">Mỹ Thuật</option>
                            <option value="15">Âm Nhạc</option>
                        </select>
                    </div>
                </div>
                
                <!-- Phòng học -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Phòng học (Tùy chọn)</strong></label>
                        <input type="text" name="phongHoc" class="form-control" 
                               placeholder="Để trống nếu học tại lớp" id="phongHoc">
                        <small class="form-text text-muted">
                            Chỉ cần nhập cho các môn đặc biệt như Tin học, Tiếng Anh
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Thứ -->
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
                
                <!-- Tiết bắt đầu -->
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
                
                <!-- Tiết kết thúc -->
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
                
                <!-- Nút hành động -->
                <div class="col-md-5">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="d-flex gap-2 mt-2">
                            <button type="submit" name="actionType" value="save" class="btn btn-success btn-block">
                                <i class="fas fa-save"></i> Lưu tiết học
                            </button>
                            <button type="submit" name="actionType" value="delete" class="btn btn-danger btn-block" 
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



// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const maMonHoc = document.getElementById('maMonHoc').value;
    if (maMonHoc) {
        loadGiaoVienTheoMon(maMonHoc);
    }
});
</script>