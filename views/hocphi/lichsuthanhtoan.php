<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-history me-2"></i>Lịch sử Thanh toán</h1>
                    <a href="index.php?controller=hocphi&action=index" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại Dashboard
                    </a>
                </div>

                <!-- Bộ lọc -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tháng</label>
                                <select class="form-select">
                                    <option selected>Tất cả tháng</option>
                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>">Tháng <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Năm học</label>
                                <select class="form-select">
                                    <option selected>Tất cả năm</option>
                                    <option>2024-2025</option>
                                    <option>2023-2024</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select">
                                    <option selected>Tất cả</option>
                                    <option value="success">Thành công</option>
                                    <option value="fail">Thất bại</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Tìm kiếm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Danh sách lịch sử -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã GD</th>
                                        <th>Mã HP</th>
                                        <th>Ngày GD</th>
                                        <th>Số tiền</th>
                                        <th>Phương thức</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($lichSuThanhToan)): ?>
                                        <?php foreach ($lichSuThanhToan as $gd): ?>
                                        <tr>
                                            <td><strong><?= $gd['maGiaoDich'] ?></strong></td>
                                            <td>#<?= $gd['maHocPhi'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($gd['ngayGiaoDich'])) ?></td>
                                            <td class="fw-bold text-success"><?= number_format($gd['soTien'], 0, ',', '.') ?> đ</td>
                                            <td>
                                                <?php
                                                $phuongThuc = [
                                                    'TIEN_MAT' => 'Tiền mặt',
                                                    'CHUYEN_KHOAN' => 'Chuyển khoản', 
                                                    'VI_DIEN_TU' => 'Ví điện tử'
                                                ];
                                                echo $phuongThuc[$gd['phuongThuc']] ?? $gd['phuongThuc'];
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $gd['trangThai'] == 'THANH_CONG' ? 'success' : 'danger' ?>">
                                                    <?= $gd['trangThai'] == 'THANH_CONG' ? 'Thành công' : 'Thất bại' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="index.php?controller=hocphi&action=bienlai&maGiaoDich=<?= $gd['maGiaoDich'] ?>" 
                                                   class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-receipt me-1"></i>Biên lai
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                                <h5>Chưa có giao dịch nào</h5>
                                                <p class="text-muted">Bạn chưa thực hiện thanh toán học phí nào.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>