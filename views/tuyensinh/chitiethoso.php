<?php
$title = "Chi tiết hồ sơ tuyển sinh";
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <div>
            <a href="index.php?controller=tuyensinh&action=danhsachhoso" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <?php if ($hoSo['trangThai'] === 'CHO_XET_DUYET'): ?>
            <a href="index.php?controller=tuyensinh&action=xulyhoso&maHoSo=<?php echo $hoSo['maHoSo']; ?>" 
               class="btn btn-warning">
                <i class="fas fa-cog"></i> Xử lý
            </a>
            <?php endif; ?>
            <?php if (!$hoSo['diemTong'] && $hoSo['trangThai'] === 'DA_DUYET'): ?>
            <a href="index.php?controller=tuyensinh&action=nhapdiem&maHoSo=<?php echo $hoSo['maHoSo']; ?>" 
               class="btn btn-success">
                <i class="fas fa-edit"></i> Nhập điểm
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin chính -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-graduate"></i> Thông tin học sinh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã hồ sơ:</strong> #<?php echo $hoSo['maHoSo']; ?></p>
                            <p><strong>Họ tên:</strong> <?php echo $hoSo['hoTen']; ?></p>
                            <p><strong>Ngày sinh:</strong> <?php echo date('d/m/Y', strtotime($hoSo['ngaySinh'])); ?></p>
                            <p><strong>Giới tính:</strong> 
                                <?php echo $hoSo['gioiTinh'] === 'NAM' ? 'Nam' : ($hoSo['gioiTinh'] === 'NU' ? 'Nữ' : 'Khác'); ?>
                            </p>
                            <p><strong>SĐT học sinh:</strong> <?php echo $hoSo['soDienThoaiHocSinh'] ?? 'N/A'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> <?php echo $hoSo['email'] ?? 'N/A'; ?></p>
                            <p><strong>Địa chỉ:</strong> <?php echo $hoSo['diaChiThuongTru'] ?? 'N/A'; ?></p>
                            <p><strong>Trường THCS:</strong> <?php echo $hoSo['truongTHCS'] ?? 'N/A'; ?></p>
                            <p><strong>Ngày đăng ký:</strong> <?php echo date('d/m/Y H:i', strtotime($hoSo['ngayDangKy'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin phụ huynh -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users"></i> Thông tin phụ huynh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Thông tin liên hệ</h6>
                            <p><strong>SĐT phụ huynh:</strong> <?php echo $hoSo['soDienThoaiPhuHuynh'] ?? 'N/A'; ?></p>
                            <p><strong>SĐT cha:</strong> <?php echo $hoSo['dienThoaiCha'] ?? 'N/A'; ?></p>
                            <p><strong>SĐT mẹ:</strong> <?php echo $hoSo['dienThoaiMe'] ?? 'N/A'; ?></p>
                            <?php if (!empty($hoSo['dienThoaiNguoiGiamHo'])): ?>
                            <p><strong>SĐT người giám hộ:</strong> <?php echo $hoSo['dienThoaiNguoiGiamHo']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6>Thông tin cha mẹ</h6>
                            <p><strong>Họ tên cha:</strong> <?php echo $hoSo['hoTenCha'] ?? 'N/A'; ?></p>
                            <p><strong>Nghề nghiệp cha:</strong> <?php echo $hoSo['ngheNghiepCha'] ?? 'N/A'; ?></p>
                            <p><strong>Họ tên mẹ:</strong> <?php echo $hoSo['hoTenMe'] ?? 'N/A'; ?></p>
                            <p><strong>Nghề nghiệp mẹ:</strong> <?php echo $hoSo['ngheNghiepMe'] ?? 'N/A'; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin học tập -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-graduation-cap"></i> Thông tin học tập
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Trường THCS:</strong> <?php echo $hoSo['truongTHCS'] ?? 'N/A'; ?></p>
                            <p><strong>Địa chỉ trường:</strong> <?php echo $hoSo['diaChiTruongTHCS'] ?? 'N/A'; ?></p>
                            <p><strong>Năm tốt nghiệp:</strong> <?php echo $hoSo['namTotNghiep'] ?? 'N/A'; ?></p>
                        </div>
                        <div class="col-md-6">
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nguyện vọng -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list-alt"></i> Nguyện vọng tuyển sinh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Nguyện vọng 1:</strong><br>
                            <?php echo !empty($hoSo['nguyenVong1']) ? $hoSo['nguyenVong1'] : 'N/A'; ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Nguyện vọng 2:</strong><br>
                            <?php echo !empty($hoSo['nguyenVong2']) ? $hoSo['nguyenVong2'] : 'N/A'; ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Nguyện vọng 3:</strong><br>
                            <?php echo !empty($hoSo['nguyenVong3']) ? $hoSo['nguyenVong3'] : 'N/A'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <p><strong>Hình thức tuyển sinh:</strong><br>
                            <?php 
                            $hinhThuc = [
                                'XET_TUYEN' => 'Xét tuyển',
                                'THI_TUYEN' => 'Thi tuyển',
                                'KET_HOP' => 'Kết hợp'
                            ];
                            echo $hinhThuc[$hoSo['hinhThucTuyenSinh']] ?? 'Xét tuyển';
                            ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hồ sơ đính kèm -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-download"></i> Hồ sơ đính kèm
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Giấy khai sinh:</strong> 
                                <?php if (!empty($hoSo['banSaoGiayKhaiSinh'])): ?>
                                <a href="<?php echo $hoSo['banSaoGiayKhaiSinh']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Tải xuống
                                </a>
                                <?php else: ?>
                                <span class="text-muted">Chưa có</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Hộ khẩu:</strong> 
                                <?php if (!empty($hoSo['banSaoHoKhau'])): ?>
                                <a href="<?php echo $hoSo['banSaoHoKhau']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Tải xuống
                                </a>
                                <?php else: ?>
                                <span class="text-muted">Chưa có</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Học bạ THCS:</strong> 
                                <?php if (!empty($hoSo['hocBaTHCS'])): ?>
                                <a href="<?php echo $hoSo['hocBaTHCS']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Tải xuống
                                </a>
                                <?php else: ?>
                                <span class="text-muted">Chưa có</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Giấy tốt nghiệp:</strong> 
                                <?php if (!empty($hoSo['giayChungNhanTotNghiep'])): ?>
                                <a href="<?php echo $hoSo['giayChungNhanTotNghiep']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Tải xuống
                                </a>
                                <?php else: ?>
                                <span class="text-muted">Chưa có</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Ảnh 3x4:</strong> 
                                <?php if (!empty($hoSo['anh34'])): ?>
                                <a href="<?php echo $hoSo['anh34']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Tải xuống
                                </a>
                                <?php else: ?>
                                <span class="text-muted">Chưa có</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Giấy ưu tiên:</strong> 
                                <?php if (!empty($hoSo['giayXacNhanUuTien'])): ?>
                                <a href="<?php echo $hoSo['giayXacNhanUuTien']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Tải xuống
                                </a>
                                <?php else: ?>
                                <span class="text-muted">Không có</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin xét tuyển -->
        <div class="col-md-4">
            <!-- Trạng thái -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Trạng thái hồ sơ
                    </h5>
                </div>
                <div class="card-body">
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

                    <p><strong>Ngày đăng ký:</strong><br>
                    <?php echo date('d/m/Y H:i', strtotime($hoSo['ngayDangKy'])); ?>
                    </p>

                    <?php if (!empty($hoSo['ghiChu'])): ?>
                    <p><strong>Ghi chú:</strong><br>
                    <?php echo nl2br(htmlspecialchars($hoSo['ghiChu'])); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Điểm tuyển sinh -->
            <?php if (!empty($hoSo['soBaoDanh'])): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Điểm tuyển sinh
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Số báo danh:</strong> <?php echo $hoSo['soBaoDanh']; ?></p>
                    <p><strong>Toán:</strong> <?php echo isset($hoSo['diemToan']) ? $hoSo['diemToan'] : 'N/A'; ?></p>
                    <p><strong>Văn:</strong> <?php echo isset($hoSo['diemVan']) ? $hoSo['diemVan'] : 'N/A'; ?></p>
                    <p><strong>Anh:</strong> <?php echo isset($hoSo['diemAnh']) ? $hoSo['diemAnh'] : 'N/A'; ?></p>
                    <p><strong>Môn 4:</strong> <?php echo isset($hoSo['diemMon4']) ? $hoSo['diemMon4'] : 'N/A'; ?></p>
                    <p><strong>Điểm cộng:</strong> <?php echo isset($hoSo['diemCong']) ? $hoSo['diemCong'] : '0'; ?></p>
                    <hr>
                    <p><strong>Tổng điểm:</strong> 
                        <span class="fw-bold text-primary"><?php echo isset($hoSo['diemTong']) ? $hoSo['diemTong'] : 'N/A'; ?></span>
                    </p>
                    <p><strong>Đợt thi:</strong> <?php echo $hoSo['dotThi'] ?? 'N/A'; ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thông báo đã trúng tuyển -->
    <?php if (!empty($hoSo['maHocSinh'])): ?>
    <div class="alert alert-success mt-3">
        <h6><i class="fas fa-check-circle"></i> Đã trúng tuyển và tạo hồ sơ học sinh</h6>
        <p class="mb-0">
            <strong>Mã học sinh:</strong> #<?php echo $hoSo['maHocSinh']; ?> | 
            <strong>Tài khoản:</strong> hs<?php echo date('Y') . str_pad($hoSo['maHoSo'], 4, '0', STR_PAD_LEFT); ?>
        </p>
    </div>
    <?php endif; ?>
</div>