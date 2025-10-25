<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Header biên lai -->
                    <div class="text-center mb-4">
                        <h2 class="mb-1">BIÊN LAI THANH TOÁN HỌC PHÍ</h2>
                        <p class="text-muted">TRƯỜNG THCS-THPT CHẤT LƯỢNG CAO</p>
                        <p class="text-muted">Mã giao dịch: <strong><?= $bienLai['maGiaoDich'] ?></strong></p>
                    </div>

                    <!-- Thông tin giao dịch -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Học sinh:</th>
                                    <td><?= $bienLai['hoTen'] ?></td>
                                </tr>
                                <tr>
                                    <th>Lớp:</th>
                                    <td><?= $bienLai['tenLop'] ?></td>
                                </tr>
                                <tr>
                                    <th>Kỳ học:</th>
                                    <td><?= $this->hocPhiModel->getTenKyHoc($bienLai['kyHoc']) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Ngày thanh toán:</th>
                                    <td><?= date('d/m/Y H:i', strtotime($bienLai['ngayGiaoDich'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Phương thức:</th>
                                    <td>
                                        <?php 
                                        $phuongThuc = [
                                            'TIEN_MAT' => 'Tiền mặt',
                                            'CHUYEN_KHOAN' => 'Chuyển khoản',
                                            'VI_DIEN_TU' => 'Ví điện tử'
                                        ];
                                        echo $phuongThuc[$bienLai['phuongThuc']] ?? $bienLai['phuongThuc'];
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái:</th>
                                    <td><span class="badge bg-success">THÀNH CÔNG</span></td>
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
                                        <th>Mô tả</th>
                                        <th width="30%" class="text-end">Số tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= $bienLai['tenGiaoDich'] ?></td>
                                        <td class="text-end fw-bold text-danger">
                                            <?= number_format($bienLai['soTien'], 0, ',', '.') ?> đ
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold">Tổng cộng:</td>
                                        <td class="text-end fw-bold text-danger">
                                            <?= number_format($bienLai['soTien'], 0, ',', '.') ?> đ
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Chữ ký -->
                    <div class="row mt-5">
                        <div class="col-6 text-center">
                            <p>Người nộp tiền</p>
                            <p class="fst-italic">(Ký và ghi rõ họ tên)</p>
                        </div>
                        <div class="col-6 text-center">
                            <p>Thủ quỹ</p>
                            <p class="fst-italic">(Ký và ghi rõ họ tên)</p>
                        </div>
                    </div>

                    <!-- Nút in -->
                    <div class="text-center mt-4">
                        <button class="btn btn-primary me-2" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>In biên lai
                        </button>
                        <a href="index.php?controller=hocphi&action=index" class="btn btn-secondary">
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
    .btn, .navbar, .sidebar { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { font-size: 12pt; }
}
.card { border: 1px solid #ddd; }
.table th { background-color: #f8f9fa !important; }
</style>