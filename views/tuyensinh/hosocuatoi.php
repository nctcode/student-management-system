<?php
$title = "Hồ sơ của tôi";
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <a href="index.php?controller=tuyensinh&action=dangkyhoso" class="btn btn-primary">
            <i class="fas fa-plus"></i> Đăng ký mới
        </a>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Danh sách hồ sơ -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($hoSo)): ?>
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có hồ sơ nào</h5>
                <p class="text-muted">Hãy đăng ký hồ sơ tuyển sinh đầu tiên của bạn</p>
                <a href="index.php?controller=tuyensinh&action=dangkyhoso" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Đăng ký ngay
                </a>
            </div>
            <?php else: ?>
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
                                <span class="text-muted">Chưa có</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?controller=tuyensinh&action=xemhoso&maHoSo=<?php echo $hs['maHoSo']; ?>" 
                                class="btn btn-info btn-sm" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>