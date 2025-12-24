<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/giaovien.php';
?>
<style>
/* Đảm bảo content không bị đè */
.content {
    margin-left: 250px !important;
    padding: 20px !important;
    min-height: 100vh;
    position: relative;
    z-index: 1;
}


</style>
<title>Thêm Điểm Hạnh Kiểm</title>

<div class="content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Thêm Điểm Hạnh Kiểm</h1>
            <a href="index.php?controller=hanhkiem&action=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-check"></i> Nhập Thông Tin Hạnh Kiểm</h5>
            </div>
            <div class="card-body">
                <form action="index.php?controller=hanhkiem&action=store" method="POST" id="formHanhKiem">
                    <div class="row">
                        <!-- Học sinh -->
                        <div class="col-md-6 mb-3">
                            <label for="maHocSinh" class="form-label">Chọn Học Sinh <span class="text-danger">*</span></label>
                            <select class="form-select" id="maHocSinh" name="maHocSinh" required>
                                <option value="">-- Chọn học sinh --</option>
                                <?php foreach ($dsHocSinh as $hs): ?>
                                    <option value="<?= $hs['maHocSinh'] ?>">
                                        <?= htmlspecialchars($hs['hoTen']) ?> 
                                        (<?= $hs['tenLop'] ? 'Lớp: ' . $hs['tenLop'] : 'Chưa phân lớp' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Học kỳ -->
                        <div class="col-md-6 mb-3">
                            <label for="hoc_ky" class="form-label">Học Kỳ <span class="text-danger">*</span></label>
                            <select class="form-select" id="hoc_ky" name="hoc_ky" required>
                                <option value="">-- Chọn học kỳ --</option>
                                <option value="HK1-2024">Học kỳ 1 - Năm học 2024-2025</option>
                                <option value="HK2-2024">Học kỳ 2 - Năm học 2024-2025</option>
                                <option value="HK1-2025">Học kỳ 1 - Năm học 2025-2026</option>
                                <option value="HK2-2025">Học kỳ 2 - Năm học 2025-2026</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Điểm số -->
                        <div class="col-md-4 mb-3">
                            <label for="diem_so" class="form-label">Điểm Số (0-100) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="diem_so" name="diem_so" 
                                   min="0" max="100" required placeholder="Nhập điểm từ 0 đến 100">
                            <div class="form-text">Điểm sẽ tự động xếp loại bên dưới</div>
                        </div>

                        <!-- Xếp loại (tự động) -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Xếp Loại</label>
                            <div class="form-control" id="xep_loai_display" style="background-color: #f8f9fa;">
                                <span class="text-muted">Chưa có điểm</span>
                            </div>
                            <input type="hidden" id="xep_loai" name="xep_loai" value="">
                        </div>

                        <!-- Màu xếp loại -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Trạng Thái</label>
                            <div class="form-control" id="status_display">
                                <span class="badge bg-secondary">Chưa xác định</span>
                            </div>
                        </div>
                    </div>

                    <!-- Nhận xét -->
                    <div class="mb-3">
                        <label for="nhan_xet" class="form-label">Nhận Xét</label>
                        <textarea class="form-control" id="nhan_xet" name="nhan_xet" 
                                  rows="4" placeholder="Nhận xét về hạnh kiểm của học sinh..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-between">
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Nhập lại
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Lưu Điểm Hạnh Kiểm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hướng dẫn -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Hướng dẫn xếp loại</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <span class="badge bg-success">Xuất sắc</span>: 90 - 100 điểm
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-primary">Tốt</span>: 80 - 89 điểm
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-info">Khá</span>: 65 - 79 điểm
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-warning">Trung bình</span>: 50 - 64 điểm
                    </div>
                    <div class="col-md-3 mt-2">
                        <span class="badge bg-danger">Yếu</span>: Dưới 50 điểm
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Tự động xếp loại khi điểm thay đổi
    $('#diem_so').on('input', function() {
        var diem = parseInt($(this).val());
        var xepLoai = '';
        var badgeClass = 'bg-secondary';
        var badgeText = 'Chưa xác định';

        if (diem >= 90) {
            xepLoai = 'Xuất sắc';
            badgeClass = 'bg-success';
            badgeText = 'Xuất sắc';
        } else if (diem >= 80) {
            xepLoai = 'Tốt';
            badgeClass = 'bg-primary';
            badgeText = 'Tốt';
        } else if (diem >= 65) {
            xepLoai = 'Khá';
            badgeClass = 'bg-info';
            badgeText = 'Khá';
        } else if (diem >= 50) {
            xepLoai = 'Trung bình';
            badgeClass = 'bg-warning';
            badgeText = 'Trung bình';
        } else if (diem >= 0) {
            xepLoai = 'Yếu';
            badgeClass = 'bg-danger';
            badgeText = 'Yếu';
        }

        $('#xep_loai_display').html(xepLoai || '<span class="text-muted">Chưa có điểm</span>');
        $('#xep_loai').val(xepLoai);
        $('#status_display').html('<span class="badge ' + badgeClass + '">' + badgeText + '</span>');
    });

    // Validate form
    $('#formHanhKiem').submit(function(e) {
        var diem = parseInt($('#diem_so').val());
        if (isNaN(diem) || diem < 0 || diem > 100) {
            alert('Vui lòng nhập điểm từ 0 đến 100!');
            e.preventDefault();
            return false;
        }
        return true;
    });
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>