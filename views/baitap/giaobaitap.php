<?php
// Xử lý mảng phancong để lấy danh sách duy nhất
$lopHocList = [];
$monHocList = [];
foreach ($danhSachPhanCong as $pc) {
    $lopHocList[$pc['maLop']] = $pc['tenLop'];
    $monHocList[$pc['maMonHoc']] = $pc['tenMonHoc'];
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Giao bài tập</strong></h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Nội dung bài tập</h5>
        </div>
        <div class="card-body">
            
            <form method="POST" action="index.php?controller=baitap&action=luu" id="formGiaoBaiTap" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="maLop"><strong>Chọn Lớp (*):</strong></label>
                            <select name="maLop" id="maLop" class="form-control" required>
                                <option value="">Chọn Lớp</option>
                                <?php foreach ($lopHocList as $maLop => $tenLop): ?>
                                    <option value="<?= $maLop ?>"><?= htmlspecialchars($tenLop) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="maMonHoc"><strong>Chọn Môn học (*):</strong></label>
                            <select name="maMonHoc" id="maMonHoc" class="form-control" required>
                                <option value="">Chọn Môn học</option>
                                <?php foreach ($monHocList as $maMonHoc => $tenMonHoc): ?>
                                    <option value="<?= $maMonHoc ?>"><?= htmlspecialchars($tenMonHoc) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hanNop"><strong>Hạn nộp (*):</strong></label>
                            <input type="datetime-local" name="hanNop" id="hanNop" class="form-control" required>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <label for="tenBT"><strong>Tên bài tập (*):</strong></label>
                    <input type="text" name="tenBT" id="tenBT" class="form-control" required 
                           placeholder="Ví dụ: Bài tập tuần 1 - Giới thiệu">
                </div>

                <div class="form-group">
                    <label for="moTa"><strong>Mô tả chi tiết:</strong></label>
                    <textarea name="moTa" id="moTa" class="form-control" rows="5" 
                              placeholder="Nhập hướng dẫn hoặc yêu cầu cho học sinh..."
                              onkeyup="demKyTu(this)"></textarea>
                    <small class="form-text text-muted"><span id="soKyTu">0</span>/1000 ký tự</small>
                </div>
                <br>
                <div class="form-group">
                    <label><strong>Đính kèm file:</strong></label>
                    <div id="danhSachFile" class="mb-2">
                        </div>
                    <input type="file" name="fileDinhKem[]" id="fileDinhKem" class="form-control-file" 
                           onchange="hienThiFile()" multiple>
                    <br>
                    <small class="form-text text-muted">
                        • Có thể đính kèm nhiều file (tối đa 20MB).<br>
                        • Định dạng: PDF, DOC, JPG, PNG, MP4, ZIP...
                    </small>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <a href="index.php?controller=home&action=index" class="btn btn-danger btn-lg">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-success btn-lg ms-2">
                        <i class="fas fa-paper-plane"></i> Giao bài
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/baitap.js"></script>