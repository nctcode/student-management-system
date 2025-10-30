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
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit"></i> Nhập điểm cho hồ sơ #<?php echo $hoSo['maHoSo']; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Thông tin thí sinh -->
                    <div class="alert alert-info mb-4">
                        <h6>Thông tin thí sinh:</h6>
                        <p><strong>Họ tên:</strong> <?php echo $hoSo['hoTen']; ?></p>
                        <p><strong>Ngày sinh:</strong> <?php echo date('d/m/Y', strtotime($hoSo['ngaySinh'])); ?></p>
                        <p><strong>Trường THCS:</strong> <?php echo $hoSo['truongTHCS'] ?? 'N/A'; ?></p>
                        <p><strong>Hình thức TS:</strong> 
                            <?php echo $hoSo['hinhThucTuyenSinh'] === 'THI_TUYEN' ? 'Thi tuyển' : 'Xét tuyển'; ?>
                        </p>
                    </div>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Toán <span class="text-danger">*</span></label>
                                    <input type="number" name="diemToan" class="form-control" min="0" max="10" step="0.1" 
                                           value="<?php echo $hoSo['diemToan'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Văn <span class="text-danger">*</span></label>
                                    <input type="number" name="diemVan" class="form-control" min="0" max="10" step="0.1" 
                                           value="<?php echo $hoSo['diemVan'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Anh <span class="text-danger">*</span></label>
                                    <input type="number" name="diemAnh" class="form-control" min="0" max="10" step="0.1" 
                                           value="<?php echo $hoSo['diemAnh'] ?? ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm Môn 4 <span class="text-danger">*</span></label>
                                    <input type="number" name="diemMon4" class="form-control" min="0" max="10" step="0.1" 
                                           value="<?php echo $hoSo['diemMon4'] ?? ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm cộng</label>
                                    <input type="number" name="diemCong" class="form-control" min="0" max="5" step="0.1" 
                                           value="<?php echo $hoSo['diemCong'] ?? '0'; ?>">
                                    <small class="text-muted">Điểm ưu tiên, khu vực, đối tượng...</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Đợt thi</label>
                                    <select name="dotThi" class="form-select">
                                        <option value="DOT1" <?php echo ($hoSo['dotThi'] ?? 'DOT1') === 'DOT1' ? 'selected' : ''; ?>>Đợt 1</option>
                                        <option value="DOT2" <?php echo ($hoSo['dotThi'] ?? '') === 'DOT2' ? 'selected' : ''; ?>>Đợt 2</option>
                                        <option value="DOT3" <?php echo ($hoSo['dotThi'] ?? '') === 'DOT3' ? 'selected' : ''; ?>>Đợt 3</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Thông báo quan trọng -->
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Lưu ý quan trọng:</h6>
                            <ul class="mb-0">
                                <li>Điểm số nhập từ 0 đến 10, có thể nhập số thập phân (ví dụ: 8.5)</li>
                                <li>Hệ thống sẽ tự động tính tổng điểm sau khi lưu</li>
                                <li>Sau khi nhập điểm, hồ sơ sẽ được đưa vào danh sách xét tuyển</li>
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