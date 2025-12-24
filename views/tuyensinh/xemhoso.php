<?php
$title = "Chi tiết hồ sơ tuyển sinh";
$userRole = $_SESSION['user']['vaiTro'] ?? '';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <div>
            <a href="index.php?controller=tuyensinh&action=hosocuatoi" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <a href="index.php?controller=tuyensinh&action=dangkyhoso" class="btn btn-primary">
                <i class="fas fa-plus"></i> Đăng ký mới
            </a>
        </div>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Thông tin chính -->
        <div class="col-md-8">
            <!-- Thông tin cá nhân -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user"></i> Thông tin cá nhân
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
                            <p><strong>Nơi sinh:</strong> <?php echo $hoSo['noiSinh'] ?? 'N/A'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Dân tộc:</strong> <?php echo $hoSo['danToc'] ?? 'N/A'; ?></p>
                            <p><strong>Tôn giáo:</strong> <?php echo $hoSo['tonGiao'] ?? 'N/A'; ?></p>
                            <p><strong>Quốc tịch:</strong> <?php echo $hoSo['quocTich'] ?? 'Việt Nam'; ?></p>
                            <p><strong>SĐT học sinh:</strong> <?php echo $hoSo['soDienThoaiHocSinh']; ?></p>
                            <p><strong>Email:</strong> <?php echo $hoSo['email'] ?? 'N/A'; ?></p>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Địa chỉ thường trú:</strong> <?php echo $hoSo['diaChiThuongTru']; ?></p>
                        </div>
                        <div class="col-12">
                            <p><strong>Nơi ở hiện nay:</strong> <?php echo $hoSo['noiOHienNay'] ?? $hoSo['diaChiThuongTru']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin phụ huynh -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users"></i> Thông tin phụ huynh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Cha</h6>
                            <p><strong>Họ tên:</strong> <?php echo $hoSo['hoTenCha'] ?? 'N/A'; ?></p>
                           
                            <p><strong>Nghề nghiệp:</strong> <?php echo $hoSo['ngheNghiepCha'] ?? 'N/A'; ?></p>
                            <p><strong>SĐT:</strong> <?php echo $hoSo['dienThoaiCha'] ?? 'N/A'; ?></p>
                            
                        </div>
                        <div class="col-md-6">
                            <h6>Mẹ</h6>
                            <p><strong>Họ tên:</strong> <?php echo $hoSo['hoTenMe'] ?? 'N/A'; ?></p>
                           
                            <p><strong>Nghề nghiệp:</strong> <?php echo $hoSo['ngheNghiepMe'] ?? 'N/A'; ?></p>
                            <p><strong>SĐT:</strong> <?php echo $hoSo['dienThoaiMe'] ?? 'N/A'; ?></p>
                           
                        </div>
                    </div>
                    
                    <?php if ($hoSo['hoTenNguoiGiamHo']): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Người giám hộ</h6>
                            <p><strong>Họ tên:</strong> <?php echo $hoSo['hoTenNguoiGiamHo']; ?></p>
                           
                            <p><strong>Nghề nghiệp:</strong> <?php echo $hoSo['ngheNghiepNguoiGiamHo'] ?? 'N/A'; ?></p>
                            <p><strong>SĐT:</strong> <?php echo $hoSo['dienThoaiNguoiGiamHo'] ?? 'N/A'; ?></p>
                            
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thông tin học tập -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
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
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list-alt"></i> Nguyện vọng tuyển sinh
                    </h5>
                </div>
                <div class="card-body">
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
                    <div class="row">
                       
                        <div class="col-md-6">
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
        </div>

        <!-- Thông tin xét tuyển -->
        <div class="col-md-4">
            <!-- Trạng thái -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
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

                    <?php if ($hoSo['ghiChu']): ?>
                    <p><strong>Ghi chú từ nhà trường:</strong><br>
                    <?php echo nl2br(htmlspecialchars($hoSo['ghiChu'])); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Điểm tuyển sinh -->
            <?php if ($hoSo['soBaoDanh']): ?>
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Điểm tuyển sinh
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Số báo danh:</strong> <?php echo $hoSo['soBaoDanh']; ?></p>
                    <p><strong>Toán:</strong> <?php echo $hoSo['diemToan'] ?? 'N/A'; ?></p>
                    <p><strong>Văn:</strong> <?php echo $hoSo['diemVan'] ?? 'N/A'; ?></p>
                    <p><strong>Anh:</strong> <?php echo $hoSo['diemAnh'] ?? 'N/A'; ?></p>
                    <p><strong>Môn 4:</strong> <?php echo $hoSo['diemMon4'] ?? 'N/A'; ?></p>
                    <p><strong>Điểm cộng:</strong> <?php echo $hoSo['diemCong'] ?? '0'; ?></p>
                    <hr>
                    <p><strong>Tổng điểm:</strong> 
                        <span class="fw-bold text-primary"><?php echo $hoSo['diemTong'] ?? 'N/A'; ?></span>
                    </p>
                    <p><strong>Đợt thi:</strong> <?php echo $hoSo['dotThi'] ?? 'N/A'; ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Hồ sơ đính kèm -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-download"></i> Hồ sơ đính kèm
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($hoSo['banSaoGiayKhaiSinh']): ?>
                        <a href="<?php echo $hoSo['banSaoGiayKhaiSinh']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Giấy khai sinh
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($hoSo['banSaoHoKhau']): ?>
                        <a href="<?php echo $hoSo['banSaoHoKhau']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Hộ khẩu
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($hoSo['hocBaTHCS']): ?>
                        <a href="<?php echo $hoSo['hocBaTHCS']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Học bạ THCS
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($hoSo['giayChungNhanTotNghiep']): ?>
                        <a href="<?php echo $hoSo['giayChungNhanTotNghiep']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Giấy tốt nghiệp
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($hoSo['anh34']): ?>
                        <a href="<?php echo $hoSo['anh34']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Ảnh 3x4
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($hoSo['giayXacNhanUuTien']): ?>
                        <a href="<?php echo $hoSo['giayXacNhanUuTien']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Giấy ưu tiên
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>