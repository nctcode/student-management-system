<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý thời khóa biểu</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách thời khóa biểu</h6>
            <a href="index.php?controller=thoikhoabieu&action=taotkb" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tạo mới
            </a>
        </div>
        <div class="card-body">
            <!-- Form chọn tuần -->
            <form method="GET" class="mb-3">
                <input type="hidden" name="controller" value="thoikhoabieu">
                <input type="hidden" name="action" value="quanlytkb">
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
                <div class="alert alert-info">Chưa có thời khóa biểu nào.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Thứ</th>
                                <th>Tiết</th>
                                <th>Môn học</th>
                                <th>Giáo viên</th>
                                <th>Lớp</th>
                                <th>Phòng</th>
                                <th>Ngày áp dụng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($thoiKhoaBieu as $tkb): ?>
                            <tr>
                                <td><?= $this->convertDayToVietnamese($tkb['loaiLich']) ?></td>
                                <td><?= $tkb['tietBatDau'] ?>-<?= $tkb['tietKetThuc'] ?></td>
                                <td><?= $tkb['tenMonHoc'] ?></td>
                                <td><?= $tkb['tenGiaoVien'] ?? 'Chưa phân công' ?></td>
                                <td><?= $tkb['tenLop'] ?></td>
                                <td><?= $tkb['phongHoc'] ?></td>
                                <td><?= date('d/m/Y', strtotime($tkb['ngayApDung'])) ?></td>
                                <td>
                                    <a href="index.php?controller=thoikhoabieu&action=xoatkb&maThoiKhoaBieu=<?= $tkb['maThoiKhoaBieu'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Bạn có chắc muốn xóa?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Hàm chuyển đổi thứ (đặt ở cuối file, ngoài class)
function convertDayToVietnamese($loaiLich) {
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