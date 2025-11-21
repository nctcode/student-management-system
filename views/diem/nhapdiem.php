<?php
// Xử lý mảng phancong để lấy danh sách duy nhất
$lopHocList = [];
$monHocList = [];
foreach ($danhSachPhanCong as $pc) {
    $lopHocList[$pc['maLop']] = $pc['tenLop'];
    $monHocList[$pc['maMonHoc']] = $pc['tenMonHoc'];
}

// Tạo danh sách năm học
$namHocHienTai = (int)date('Y');
$namHocList = [];
for ($i = $namHocHienTai + 1; $i >= $namHocHienTai - 5; $i--) {
    $namHocList[] = ($i - 1) . '-' . $i;
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Nhập điểm</strong></h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4" style="position: relative; z-index: 20;">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Chọn thông tin để nhập điểm</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="maLop"><strong>Chọn Lớp:</strong></label>
                        <select id="maLop" class="form-control" required>
                            <option value="">Chọn Lớp</option>
                            <?php foreach ($lopHocList as $maLop => $tenLop): ?>
                                <option value="<?= $maLop ?>"><?= htmlspecialchars($tenLop) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="maMonHoc"><strong>Chọn Môn học:</strong></label>
                        <select id="maMonHoc" class="form-control" required>
                            <option value="">Chọn Môn học</option>
                            <?php foreach ($monHocList as $maMonHoc => $tenMonHoc): ?>
                                <option value="<?= $maMonHoc ?>"><?= htmlspecialchars($tenMonHoc) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="hocKy"><strong>Chọn Học kỳ:</strong></label>
                        <select id="hocKy" class="form-control" required>
                            <option value="">Chọn Học kỳ</option>
                            <option value="HK1">Học kỳ 1</option>
                            <option value="HK2">Học kỳ 2</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="namHoc"><strong>Chọn Năm học:</strong></label>
                        <select id="namHoc" class="form-control" required>
                            <?php foreach ($namHocList as $nam): ?>
                                <option value="<?= $nam ?>" <?= ($nam == date('Y').'-'.(date('Y')+1)) ? 'selected' : '' ?>>
                                    <?= $nam ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <div class="d-flex justify-content-end">
            <button type="button" id="btnXemBangDiem" class="btn btn-primary" disabled>
                <i class="fas fa-eye"></i> Xem bảng điểm
            </button>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4" id="cardBangDiem" style="display: none;">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Bảng điểm</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?controller=diem&action=luu" id="formLuuDiem">
                <div id="hiddenInputsContainer"></div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTableDiem" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã HS</th>
                                <th>Họ tên</th>
                                <th>Điểm Miệng</th>
                                <th>Điểm 15 Phút</th>
                                <th>Điểm 1 Tiết</th>
                                <th>Điểm Cuối Kỳ</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDiem">
                            </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <a href="index.php?controller=diem&action=index" class="btn btn-danger btn-lg">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-success btn-lg ms-2">
                        <i class="fas fa-save"></i> Lưu điểm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/diem.js"></script>
<link href="assets/css/diem.css" rel="stylesheet">