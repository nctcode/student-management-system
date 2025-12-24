<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tra cứu hồ sơ tuyển sinh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
        }

        .search-card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .detail-card {
            margin-top: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 20px 0 15px 0;
            font-weight: bold;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .info-value {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 8px 12px;
            min-height: 42px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="card search-card">
        <div class="card-body">
            <h4 class="card-title text-center mb-4 text-primary fw-bold">Tra cứu hồ sơ tuyển sinh</h4>

            <!-- Ô tìm kiếm -->
            <form class="d-flex mb-4" method="post">
                <input class="form-control me-2" type="search" name="maHoSo"
                    placeholder="Nhập mã hồ sơ..."
                    value="<?= htmlspecialchars($_POST['maHoSo'] ?? '') ?>" required>
                <button class="btn btn-primary" type="submit">Tìm kiếm</button>
            </form>

            <?php if (!empty($message)): ?>
                <div class="alert alert-warning text-center"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <!-- Thông tin hồ sơ -->
            <?php if ($hoSo): ?>
                <div class="card detail-card p-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-file-alt me-2"></i>Chi tiết hồ sơ tuyển sinh
                    </h5>
                    
                    <!-- Thông tin cá nhân -->
                    <div class="section-title">Thông tin cá nhân</div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="info-label">Mã hồ sơ</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['maHoSo']) ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Họ tên thí sinh</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['hoTen']) ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Ngày sinh</div>
                            <div class="info-value"><?= date('d/m/Y', strtotime($hoSo['ngaySinh'])) ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Giới tính</div>
                            <div class="info-value">
                                <?php 
                                switch($hoSo['gioiTinh']) {
                                    case 'NAM': echo 'Nam'; break;
                                    case 'NU': echo 'Nữ'; break;
                                    case 'KHAC': echo 'Khác'; break;
                                    default: echo $hoSo['gioiTinh'];
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">SĐT học sinh</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['soDienThoaiHocSinh'] ?? 'Chưa cập nhật') ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">SĐT phụ huynh</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['soDienThoaiPhuHuynh'] ?? 'Chưa cập nhật') ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Địa chỉ thường trú</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['diaChiThuongTru'] ?? 'Chưa cập nhật') ?></div>
                        </div>
                    </div>

                    <!-- Thông tin học vấn -->
                    <div class="section-title">Thông tin học vấn</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-label">Trường THCS</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['truongTHCS'] ?? 'Chưa cập nhật') ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Năm tốt nghiệp</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['namTotNghiep'] ?? 'Chưa cập nhật') ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Điểm TB lớp 9</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['diemTB_Lop9'] ?? 'Chưa cập nhật') ?></div>
                        </div>
                    </div>

                    <!-- Nguyện vọng -->
                    <div class="section-title">Nguyện vọng đăng ký</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-label">Nguyện vọng 1</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['nguyenVong1'] ?? 'Chưa cập nhật') ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Nguyện vọng 2</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['nguyenVong2'] ?? 'Chưa cập nhật') ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Nguyện vọng 3</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['nguyenVong3'] ?? 'Chưa cập nhật') ?></div>
                        </div>
                    </div>

                    <!-- Kết quả -->
                    <div class="section-title">Kết quả xét tuyển</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-label">Trạng thái</div>
                            <div class="info-value">
                                <?php 
                                switch($hoSo['trangThai']) {
                                    case 'CHO_XET_DUYET': 
                                        echo '<span class="badge bg-warning">Chờ xét duyệt</span>';
                                        break;
                                    case 'DA_DUYET': 
                                        echo '<span class="badge bg-success">Đã duyệt</span>';
                                        break;
                                    case 'TU_CHOI': 
                                        echo '<span class="badge bg-danger">Từ chối</span>';
                                        break;
                                    default: 
                                        echo $hoSo['trangThai'];
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Kết quả</div>
                            <div class="info-value">
                                <?php if ($hoSo['ketQua']): ?>
                                    <?php 
                                    switch($hoSo['ketQua']) {
                                        case 'TRUNG_TUYEN': 
                                            echo '<span class="badge bg-success">Trúng tuyển</span>';
                                            break;
                                        case 'KHONG_TRUNG_TUYEN': 
                                            echo '<span class="badge bg-danger">Không trúng tuyển</span>';
                                            break;
                                        default: 
                                            echo $hoSo['ketQua'];
                                    }
                                    ?>
                                <?php else: ?>
                                    <span class="text-muted">Chưa có kết quả</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Ngày đăng ký</div>
                            <div class="info-value"><?= date('d/m/Y H:i', strtotime($hoSo['ngayDangKy'])) ?></div>
                        </div>
                        
                        <?php if (!empty($hoSo['soBaoDanh'])): ?>
                        <div class="col-md-4">
                            <div class="info-label">Số báo danh</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['soBaoDanh']) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($hoSo['diemTong'])): ?>
                        <div class="col-md-4">
                            <div class="info-label">Tổng điểm</div>
                            <div class="info-value"><?= htmlspecialchars($hoSo['diemTong']) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($hoSo['ghiChu'])): ?>
                        <div class="col-12">
                            <div class="info-label">Ghi chú</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($hoSo['ghiChu'])) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Không tìm thấy hồ sơ với mã: <?= htmlspecialchars($_POST['maHoSo'] ?? '') ?>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-search fa-2x mb-3"></i>
                    <p>Nhập mã hồ sơ để tra cứu thông tin</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>