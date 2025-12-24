<?php
$title = "Xử lý hồ sơ tuyển sinh";
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <a href="index.php?controller=tuyensinh&action=danhsachhoso" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_SESSION['error'])): ?>
    <div class=" alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Thông tin hồ sơ -->
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt"></i> Thông tin hồ sơ #<?php echo $hoSo['maHoSo']; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Họ tên:</strong> <?php echo $hoSo['hoTen']; ?></p>
                            <p><strong>Ngày sinh:</strong> <?php echo date('d/m/Y', strtotime($hoSo['ngaySinh'])); ?></p>
                            <p><strong>Giới tính:</strong> 
                                <?php echo $hoSo['gioiTinh'] === 'NAM' ? 'Nam' : ($hoSo['gioiTinh'] === 'NU' ? 'Nữ' : 'Khác'); ?>
                            </p>
                            <p><strong>SĐT HS:</strong> <?php echo $hoSo['soDienThoaiHocSinh']; ?></p>
                            <p><strong>SĐT PH:</strong> <?php echo $hoSo['soDienThoaiPhuHuynh']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Trường THCS:</strong> <?php echo !empty($hoSo['truongTHCS']) ? $hoSo['truongTHCS'] : 'N/A'; ?></p>
                            <p><strong>Điểm TB lớp 9:</strong> <?php echo !empty($hoSo['diemTB_Lop9']) ? $hoSo['diemTB_Lop9'] : 'N/A'; ?></p>
                            <p><strong>Hình thức TS:</strong> 
                                <?php 
                                $hinhThuc = [
                                    'XET_TUYEN' => 'Xét tuyển',
                                    'THI_TUYEN' => 'Thi tuyển',
                                    'KET_HOP' => 'Kết hợp'
                                ];
                                echo isset($hoSo['hinhThucTuyenSinh']) ? $hinhThuc[$hoSo['hinhThucTuyenSinh']] : 'Xét tuyển';
                                ?>
                            </p>
                            <p><strong>Nguyện vọng 1:</strong> <?php echo !empty($hoSo['nguyenVong1']) ? $hoSo['nguyenVong1'] : 'N/A'; ?></p>
                            <p><strong>Ngày đăng ký:</strong> <?php echo date('d/m/Y H:i', strtotime($hoSo['ngayDangKy'])); ?></p>
                        </div>
                    </div>
                    
                    <!-- Điểm thi nếu có -->
                    <?php if (!empty($hoSo['soBaoDanh'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class=" alert-info">
                                <h6><i class="fas fa-chart-bar"></i> Điểm tuyển sinh:</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Toán:</strong> <?php echo $hoSo['diemToan'] ?? 'N/A'; ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Văn:</strong> <?php echo $hoSo['diemVan'] ?? 'N/A'; ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Anh:</strong> <?php echo $hoSo['diemAnh'] ?? 'N/A'; ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Tổng điểm:</strong> <span class="fw-bold text-primary"><?php echo $hoSo['diemTong'] ?? 'N/A'; ?></span></p>
                                    </div>
                                </div>
                                <p class="mb-0"><strong>Số báo danh:</strong> <?php echo $hoSo['soBaoDanh']; ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Form xử lý -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> Xử lý hồ sơ
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select name="trangThai" class="form-select" required>
                                <option value="CHO_XET_DUYET" <?php echo (isset($hoSo['trangThai']) && $hoSo['trangThai'] === 'CHO_XET_DUYET') ? 'selected' : ''; ?>>Chờ xét duyệt</option>
                                <option value="DA_DUYET" <?php echo (isset($hoSo['trangThai']) && $hoSo['trangThai'] === 'DA_DUYET') ? 'selected' : ''; ?>>Đã duyệt</option>
                                <option value="TU_CHOI" <?php echo (isset($hoSo['trangThai']) && $hoSo['trangThai'] === 'TU_CHOI') ? 'selected' : ''; ?>>Từ chối</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kết quả</label>
                            <select name="ketQua" class="form-select">
                                <option value="">-- Chọn kết quả --</option>
                                <option value="TRUNG_TUYEN" <?php echo (isset($hoSo['ketQua']) && $hoSo['ketQua'] === 'TRUNG_TUYEN') ? 'selected' : ''; ?>>Trúng tuyển</option>
                                <option value="KHONG_TRUNG_TUYEN" <?php echo (isset($hoSo['ketQua']) && $hoSo['ketQua'] === 'KHONG_TRUNG_TUYEN') ? 'selected' : ''; ?>>Không trúng tuyển</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="ghiChu" class="form-control" rows="4" 
                                      placeholder="Nhập ghi chú (nếu có)..."><?php echo !empty($hoSo['ghiChu']) ? htmlspecialchars($hoSo['ghiChu']) : ''; ?></textarea>
                        </div>

                        <div class=" alert-warning">
                            <small>
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Lưu ý quan trọng:</strong>
                                <ul class="mb-0 mt-1">
                                    <li>Khi chọn "Trúng tuyển", hệ thống sẽ tự động tạo tài khoản học sinh</li>
                                    <li>Tài khoản mặc định: hs<?php echo date('Y') . str_pad(($hoSo['maHoSo'] ?? 0), 4, '0', STR_PAD_LEFT); ?></li>
                                    <li>Mật khẩu mặc định: 123456</li>
                                    <li>Học sinh có thể đổi mật khẩu sau khi đăng nhập</li>
                                </ul>
                            </small>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>