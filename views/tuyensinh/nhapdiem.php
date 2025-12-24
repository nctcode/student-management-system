<?php
$title = "Nhập điểm tuyển sinh";
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <a href="index.php?controller=tuyensinh&action=chitiethoso&maHoSo=<?php echo $hoSo['maHoSo']; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit"></i> Nhập điểm cho hồ sơ #<?php echo 'HS' . date('Y') . str_pad(($hoSo['maHoSo'] ?? 0), 4, '0', STR_PAD_LEFT); ?>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Thông tin thí sinh -->
                    <div class=" alert-info mb-4">
                        <h6><i class="fas fa-user-graduate"></i> Thông tin thí sinh:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Họ tên:</strong> <?php echo $hoSo['hoTen']; ?></p>
                                <p><strong>Ngày sinh:</strong> <?php echo date('d/m/Y', strtotime($hoSo['ngaySinh'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Trường THCS:</strong> <?php echo !empty($hoSo['truongTHCS']) ? $hoSo['truongTHCS'] : 'N/A'; ?></p>
                                <p><strong>Hình thức TS:</strong> 
                                    <?php 
                                    $hinhThuc = [
                                        'THI_TUYEN' => 'Thi tuyển',
                                        'XET_TUYEN' => 'Xét tuyển',
                                        'KET_HOP' => 'Kết hợp'
                                    ];
                                    echo isset($hoSo['hinhThucTuyenSinh']) ? $hinhThuc[$hoSo['hinhThucTuyenSinh']] : 'Xét tuyển';
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Toán <span class="text-danger">*</span></label>
                                    <input type="number" name="diemToan" class="form-control" min="0" max="10" step="0.1" 
                                           value="<?php echo isset($hoSo['diemToan']) ? $hoSo['diemToan'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Văn <span class="text-danger">*</span></label>
                                    <input type="number" name="diemVan" class="form-control" min="0" max="10" step="0.1" 
                                           value="<?php echo isset($hoSo['diemVan']) ? $hoSo['diemVan'] : ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Anh <span class="text-danger">*</span></label>
                                    <input type="number" name="diemAnh" class="form-control" min="0" max="10" step="0.1" 
                                           value="<?php echo isset($hoSo['diemAnh']) ? $hoSo['diemAnh'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Môn 4 <span class="text-danger">*</span></label>
                                    <input type="number" name="diemMon4" class="form-control" min="0" max="10" step="0.1" 
                                           value="<?php echo isset($hoSo['diemMon4']) ? $hoSo['diemMon4'] : ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm cộng</label>
                                    <input type="number" name="diemCong" class="form-control" min="0" max="5" step="0.1" 
                                           value="<?php echo isset($hoSo['diemCong']) ? $hoSo['diemCong'] : '0'; ?>">
                                    <small class="text-muted">Điểm ưu tiên, khu vực, đối tượng... (0-5 điểm)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Đợt thi</label>
                                    <select name="dotThi" class="form-select">
                                        <option value="DOT1" <?php echo (isset($hoSo['dotThi']) ? $hoSo['dotThi'] : 'DOT1') === 'DOT1' ? 'selected' : ''; ?>>Đợt 1</option>
                                        <option value="DOT2" <?php echo (isset($hoSo['dotThi']) ? $hoSo['dotThi'] : '') === 'DOT2' ? 'selected' : ''; ?>>Đợt 2</option>
                                        <option value="DOT3" <?php echo (isset($hoSo['dotThi']) ? $hoSo['dotThi'] : '') === 'DOT3' ? 'selected' : ''; ?>>Đợt 3</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Sửa phần Thông báo quan trọng -->
                        <div class=" alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Lưu ý quan trọng:</h6>
                            <ul class="mb-0">
                                <li>Điểm số nhập từ 0 đến 10, có thể nhập số thập phân (ví dụ: 8.5)</li>
                                <li>Điểm cộng từ 0 đến 5 điểm</li>
                                <li><strong>Ngưỡng điểm: ≥ 32 điểm = Trúng tuyển | < 32 điểm = Không trúng tuyển</strong></li>
                                <li>Hệ thống sẽ tự động:
                                    <ul>
                                        <li>Cập nhật tổng điểm và kết quả</li>
                                        <li>Đổi trạng thái hồ sơ thành "Đã duyệt"</li>
                                        <li>Tự động tạo tài khoản học sinh nếu trúng tuyển</li>
                                    </ul>
                                </li>
                                <li>Kiểm tra kỹ điểm số trước khi lưu</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Lưu điểm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

// Tính toán tổng điểm tự động
function calculateTotalScore() {
    const diemToan = parseFloat(document.querySelector('input[name="diemToan"]').value) || 0;
    const diemVan = parseFloat(document.querySelector('input[name="diemVan"]').value) || 0;
    const diemAnh = parseFloat(document.querySelector('input[name="diemAnh"]').value) || 0;
    const diemMon4 = parseFloat(document.querySelector('input[name="diemMon4"]').value) || 0;
    const diemCong = parseFloat(document.querySelector('input[name="diemCong"]').value) || 0;
    
    const totalScore = diemToan + diemVan + diemAnh + diemMon4 + diemCong;
    document.getElementById('totalScore').textContent = totalScore.toFixed(2);
    
    // Đổi màu và hiển thị thông báo kết quả
    // Đổi màu cảnh báo nếu điểm cao/thấp
    const alertElement = document.getElementById('totalScoreAlert');
    if (totalScore >= 30) {
        alertElement.className = 'alert alert-success mb-4';
    } else if (totalScore >= 20) {
        alertElement.className = 'alert alert-warning mb-4';
    } else {
        alertElement.className = 'alert alert-danger mb-4';
    }

    const nguyenDoElement = document.getElementById('nguyenDo');
    
    if (totalScore >= 32) {
        alertElement.className = 'alert alert-success mb-4';
        if (nguyenDoElement) {
            nguyenDoElement.innerHTML = '<strong>Kết quả dự kiến:</strong> <span class="text-success">TRÚNG TUYỂN</span> (≥ 32 điểm)';
        }
    } else {
        alertElement.className = 'alert alert-danger mb-4';
        if (nguyenDoElement) {
            nguyenDoElement.innerHTML = '<strong>Kết quả dự kiến:</strong> <span class="text-danger">KHÔNG TRÚNG TUYỂN</span> (< 32 điểm)';
        }
    }
    // Gắn sự kiện tính toán khi thay đổi điểm
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', calculateTotalScore);
});

// Tính toán lần đầu khi trang load
document.addEventListener('DOMContentLoaded', calculateTotalScore);
}
</script>