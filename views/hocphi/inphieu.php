<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Header phiếu thu -->
                    <div class="text-center mb-4">
                        <h2 class="mb-1">PHIẾU THU HỌC PHÍ</h2>
                        <p class="text-muted">TRƯỜNG THCS-THPT CHẤT LƯỢNG CAO</p>
                        <p class="text-muted">Mã học phí: <strong>#<?= $phieuThu['maHocPhi'] ?></strong></p>
                    </div>

                    <!-- Thông tin học phí -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Học sinh:</th>
                                    <td><?= $phieuThu['hoTen'] ?></td>
                                </tr>
                                <tr>
                                    <th>Lớp:</th>
                                    <td><?= $phieuThu['tenLop'] ?></td>
                                </tr>
                                <tr>
                                    <th>Kỳ học:</th>
                                    <td><?= $this->hocPhiModel->getTenKyHoc($phieuThu['kyHoc']) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Tháng:</th>
                                    <td>Tháng <?= $phieuThu['thang'] ?></td>
                                </tr>
                                <tr>
                                    <th>Năm học:</th>
                                    <td><?= $phieuThu['namHoc'] ?></td>
                                </tr>
                                <tr>
                                    <th>Hạn nộp:</th>
                                    <td><?= date('d/m/Y', strtotime($phieuThu['hanNop'])) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Chi tiết thanh toán -->
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Nội dung thu</th>
                                        <th width="30%" class="text-end">Số tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Học phí <?= $this->hocPhiModel->getTenKyHoc($phieuThu['kyHoc']) ?> - Tháng <?= $phieuThu['thang'] ?></td>
                                        <td class="text-end fw-bold text-danger">
                                            <?= number_format($phieuThu['soTien'], 0, ',', '.') ?> đ
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold">Tổng cộng:</td>
                                        <td class="text-end fw-bold text-danger">
                                            <?= number_format($phieuThu['soTien'], 0, ',', '.') ?> đ
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hướng dẫn -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-info-circle me-2"></i>Hướng dẫn thanh toán:</h6>
                        <p class="mb-1">1. Mang phiếu thu này đến văn phòng nhà trường</p>
                        <p class="mb-1">2. Thanh toán bằng tiền mặt hoặc quẹt thẻ</p>
                        <p class="mb-0">3. Nhận biên lai sau khi thanh toán thành công</p>
                    </div>

                    <!-- Nút in -->
                    <div class="text-center mt-4">
                        <button class="btn btn-primary me-2" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>In phiếu thu
                        </button>
                        <a href="index.php?controller=hocphi&action=donghocphi" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .navbar, .sidebar, .alert { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { font-size: 12pt; }
}
.card { border: 1px solid #ddd; }
.table th { background-color: #f8f9fa !important; }
</style>