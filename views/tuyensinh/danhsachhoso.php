<?php
$title = "Danh sách hồ sơ tuyển sinh";
$thongKe = $this->tuyenSinhModel->getThongKeTuyenSinh();
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <div>
            <!-- <a href="index.php?controller=tuyensinh&action=dangkyhoso" class="btn btn-outline-primary">
                <i class="fas fa-plus"></i> Đăng ký mới
            </a> -->
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4><?php echo $thongKe['tongHoSo'] ?? 0; ?></h4>
                    <p class="mb-0">Tổng hồ sơ</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4><?php echo $thongKe['choDuyet'] ?? 0; ?></h4>
                    <p class="mb-0">Chờ duyệt</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4><?php echo $thongKe['daDuyet'] ?? 0; ?></h4>
                    <p class="mb-0">Đã duyệt</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4><?php echo $thongKe['tuChoi'] ?? 0; ?></h4>
                    <p class="mb-0">Từ chối</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4><?php echo $thongKe['trungTuyen'] ?? 0; ?></h4>
                    <p class="mb-0">Trúng tuyển</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="controller" value="tuyensinh">
                <input type="hidden" name="action" value="danhsachhoso">
                
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="trangThai" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="CHO_XET_DUYET" <?php echo ($_GET['trangThai'] ?? '') === 'CHO_XET_DUYET' ? 'selected' : ''; ?>>Chờ xét duyệt</option>
                        <option value="DA_DUYET" <?php echo ($_GET['trangThai'] ?? '') === 'DA_DUYET' ? 'selected' : ''; ?>>Đã duyệt</option>
                        <option value="TU_CHOI" <?php echo ($_GET['trangThai'] ?? '') === 'TU_CHOI' ? 'selected' : ''; ?>>Từ chối</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Kết quả</label>
                    <select name="ketQua" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="TRUNG_TUYEN" <?php echo ($_GET['ketQua'] ?? '') === 'TRUNG_TUYEN' ? 'selected' : ''; ?>>Trúng tuyển</option>
                        <option value="KHONG_TRUNG_TUYEN" <?php echo ($_GET['ketQua'] ?? '') === 'KHONG_TRUNG_TUYEN' ? 'selected' : ''; ?>>Không trúng tuyển</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="tuNgay" class="form-control" value="<?php echo $_GET['tuNgay'] ?? ''; ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="denNgay" class="form-control" value="<?php echo $_GET['denNgay'] ?? ''; ?>">
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                    <a href="index.php?controller=tuyensinh&action=danhsachhoso" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách hồ sơ -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Mã HS</th>
                            <th>Học sinh</th>
                            <th>Ngày sinh</th>
                            <th>SĐT HS</th>
                            <th>SĐT PH</th>
                            <th>Trường THCS</th>
                            <th>Ngày ĐK</th>
                            <th>Trạng thái</th>
                            <th>Kết quả</th>
                            <th>Điểm</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($hoSo)): ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">Không có hồ sơ nào</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($hoSo as $hs): ?>
                        <tr>
                            <td>#<?php echo $hs['maHoSo']; ?></td>
                            <td>
                                <div><strong><?php echo $hs['hoTen']; ?></strong></div>
                                <small class="text-muted"><?php echo $hs['gioiTinh'] === 'NAM' ? 'Nam' : ($hs['gioiTinh'] === 'NU' ? 'Nữ' : 'Khác'); ?></small>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($hs['ngaySinh'])); ?></td>
                            <td><?php echo $hs['soDienThoaiHocSinh']; ?></td>
                            <td><?php echo $hs['soDienThoaiPhuHuynh']; ?></td>
                            <td><?php echo $hs['truongTHCS'] ?? 'N/A'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($hs['ngayDangKy'])); ?></td>
                            <td>
                                <?php
                                $badgeClass = [
                                    'CHO_XET_DUYET' => 'bg-warning',
                                    'DA_DUYET' => 'bg-success', 
                                    'TU_CHOI' => 'bg-danger'
                                ];
                                $statusText = [
                                    'CHO_XET_DUYET' => 'Chờ duyệt',
                                    'DA_DUYET' => 'Đã duyệt',
                                    'TU_CHOI' => 'Từ chối'
                                ];
                                ?>
                                <span class="badge <?php echo $badgeClass[$hs['trangThai']] ?? 'bg-secondary'; ?>">
                                    <?php echo $statusText[$hs['trangThai']] ?? $hs['trangThai']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($hs['ketQua'] === 'TRUNG_TUYEN'): ?>
                                <span class="badge bg-success">Trúng tuyển</span>
                                <?php elseif ($hs['ketQua'] === 'KHONG_TRUNG_TUYEN'): ?>
                                <span class="badge bg-danger">Không trúng</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Chưa xét</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($hs['diemTong']): ?>
                                <strong><?php echo $hs['diemTong']; ?></strong>
                                <?php else: ?>
                                <span class="text-muted">Chưa nhập</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?controller=tuyensinh&action=chitiethoso&maHoSo=<?php echo $hs['maHoSo']; ?>" 
                                       class="btn btn-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if ($hs['trangThai'] === 'CHO_XET_DUYET'): ?>
                                    <a href="index.php?controller=tuyensinh&action=xulyhoso&maHoSo=<?php echo $hs['maHoSo']; ?>" 
                                       class="btn btn-warning" title="Xử lý hồ sơ">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!$hs['diemTong'] && $hs['trangThai'] === 'DA_DUYET'): ?>
                                    <a href="index.php?controller=tuyensinh&action=nhapdiem&maHoSo=<?php echo $hs['maHoSo']; ?>" 
                                       class="btn btn-success" title="Nhập điểm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>