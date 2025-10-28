<?php
$title = "Hồ sơ tuyển sinh - " . $hocSinh['hoTen'];
$userRole = $_SESSION['user']['vaiTro'] ?? '';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <div>
            <?php if (in_array($userRole, ['QTV', 'BGH', 'GIAOVIEN'])): ?>
            <a href="index.php?controller=hocsinh&action=danhsach" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <?php else: ?>
            <a href="index.php?controller=home&action=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <?php endif; ?>
            
            <?php if (in_array($userRole, ['QTV', 'BGH'])): ?>
            <a href="index.php?controller=tuyensinh&action=chitiethoso&maHoSo=<?php echo $hoSo['maHoSo']; ?>" 
               class="btn btn-primary">
                <i class="fas fa-external-link-alt"></i> Xem chi tiết đầy đủ
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thông tin học sinh -->
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-graduate"></i> Thông tin học sinh
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Mã học sinh:</strong> #<?php echo $hocSinh['maHocSinh']; ?></p>
                    <p><strong>Họ tên:</strong> <?php echo $hocSinh['hoTen']; ?></p>
                    <p><strong>Ngày sinh:</strong> <?php echo date('d/m/Y', strtotime($hocSinh['ngaySinh'])); ?></p>
                    <p><strong>Giới tính:</strong> 
                        <?php echo $hocSinh['gioiTinh'] === 'NAM' ? 'Nam' : ($hocSinh['gioiTinh'] === 'NU' ? 'Nữ' : 'Khác'); ?>
                    </p>
                    <p><strong>SĐT:</strong> <?php echo $hocSinh['soDienThoai']; ?></p>
                    <p><strong>Email:</strong> <?php echo $hocSinh['email'] ?? 'N/A'; ?></p>
                    <p><strong>Địa chỉ:</strong> <?php echo $hocSinh['diaChi'] ?? 'N/A'; ?></p>
                    <p><strong>Trạng thái:</strong> 
                        <span class="badge bg-<?php echo $hocSinh['trangThai'] === 'DANG_HOC' ? 'success' : 'secondary'; ?>">
                            <?php echo $hocSinh['trangThai'] === 'DANG_HOC' ? 'Đang học' : 'Đã nghỉ'; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Thông tin hồ sơ tuyển sinh -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt"></i> Thông tin hồ sơ tuyển sinh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã hồ sơ:</strong> #<?php echo $hoSo['maHoSo']; ?></p>
                            <p><strong>Trường THCS:</strong> <?php echo $hoSo['truongTHCS'] ?? 'N/A'; ?></p>
                            <p><strong>Năm tốt nghiệp:</strong> <?php echo $hoSo['namTotNghiep'] ?? 'N/A'; ?></p>
                            <p><strong>Xếp loại học lực:</strong> 
                                <?php 
                                $hocLuc = [
                                    'GIOI' => 'Giỏi',
                                    'KHA' => 'Khá', 
                                    'TRUNG_BINH' => 'Trung bình',
                                    'YEU' => 'Yếu'
                                ];
                                echo $hocLuc[$hoSo['xepLoaiHocLuc']] ?? 'N/A'; 
                                ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Xếp loại hạnh kiểm:</strong> 
                                <?php 
                                $hanhKiem = [
                                    'TOT' => 'Tốt',
                                    'KHA' => 'Khá',
                                    'TRUNG_BINH' => 'Trung bình', 
                                    'YEU' => 'Yếu'
                                ];
                                echo $hanhKiem[$hoSo['xepLoaiHanhKiem']] ?? 'N/A';
                                ?>
                            </p>
                            <p><strong>Điểm TB lớp 9:</strong> <?php echo $hoSo['diemTB_Lop9'] ?? 'N/A'; ?></p>
                            <p><strong>Hình thức tuyển sinh:</strong> 
                                <?php 
                                $hinhThuc = [
                                    'XET_TUYEN' => 'Xét tuyển',
                                    'THI_TUYEN' => 'Thi tuyển',
                                    'KET_HOP' => 'Kết hợp'
                                ];
                                echo $hinhThuc[$hoSo['hinhThucTuyenSinh']] ?? 'Xét tuyển';
                                ?>
                            </p>
                            <p><strong>Ngày đăng ký:</strong> <?php echo date('d/m/Y H:i', strtotime($hoSo['ngayDangKy'])); ?></p>
                        </div>
                    </div>

                    <!-- Nguyện vọng -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Nguyện vọng tuyển sinh:</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Nguyện vọng 1:</strong><br>
                                    <?php echo $hoSo['nguyenVong1'] ?? 'N/A'; ?>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Nguyện vọng 2:</strong><br>
                                    <?php echo $hoSo['nguyenVong2'] ?? 'N/A'; ?>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Nguyện vọng 3:</strong><br>
                                    <?php echo $hoSo['nguyenVong3'] ?? 'N/A'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kết quả tuyển sinh -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Kết quả tuyển sinh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            $badgeClass = [
                                'CHO_XET_DUYET' => 'bg-warning',
                                'DA_DUYET' => 'bg-success',
                                'TU_CHOI' => 'bg-danger'
                            ];
                            $statusText = [
                                'CHO_XET_DUYET' => 'Chờ xét duyệt',
                                'DA_DUYET' => 'Đã duyệt', 
                                'TU_CHOI' => 'Từ chối'
                            ];
                            ?>
                            <p><strong>Trạng thái:</strong><br>
                            <span class="badge <?php echo $badgeClass[$hoSo['trangThai']] ?? 'bg-secondary'; ?> fs-6">
                                <?php echo $statusText[$hoSo['trangThai']] ?? $hoSo['trangThai']; ?>
                            </span>
                            </p>

                            <p><strong>Kết quả:</strong><br>
                            <?php if ($hoSo['ketQua'] === 'TRUNG_TUYEN'): ?>
                            <span class="badge bg-success fs-6">Trúng tuyển</span>
                            <?php elseif ($hoSo['ketQua'] === 'KHONG_TRUNG_TUYEN'): ?>
                            <span class="badge bg-danger fs-6">Không trúng tuyển</span>
                            <?php else: ?>
                            <span class="badge bg-secondary fs-6">Chưa xét</span>
                            <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <?php if ($hoSo['soBaoDanh']): ?>
                            <p><strong>Số báo danh:</strong> <?php echo $hoSo['soBaoDanh']; ?></p>
                            <p><strong>Tổng điểm:</strong> 
                                <span class="fw-bold text-primary"><?php echo $hoSo['diemTong'] ?? 'N/A'; ?></span>
                            </p>
                            <p><strong>Đợt thi:</strong> <?php echo $hoSo['dotThi'] ?? 'N/A'; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($hoSo['ghiChu']): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Ghi chú từ nhà trường:</strong><br>
                            <?php echo nl2br(htmlspecialchars($hoSo['ghiChu'])); ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>