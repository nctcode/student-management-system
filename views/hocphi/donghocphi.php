<style>
.table-responsive {
    overflow-x: auto;
    white-space: nowrap;
}
.table {
    width: 100%;
    border-collapse: collapse;
}
.table th,
.table td {
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    vertical-align: middle;
}
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
.badge {
    font-size: 12px;
    padding: 4px 8px;
}
.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}
</style>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th width="80">Mã học phí</th>
                <th width="150">Học sinh</th>
                <th width="80">Lớp</th>
                <th width="100">Kỳ học</th>
                <th width="80">Tháng</th>
                <th width="100">Năm học</th>
                <th width="120">Số tiền</th>
                <th width="100">Hạn nộp</th>
                <th width="100">Trạng thái</th>
                <th width="120">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($hocPhiCanDong)): ?>
                <?php foreach ($hocPhiCanDong as $hocphi): ?>
                <tr>
                    <td class="text-center">#<?= $hocphi['maHocPhi'] ?></td>
                    <td><?= $hocphi['hoTen'] ?? 'N/A' ?></td>
                    <td class="text-center"><?= $hocphi['tenLop'] ?? $hocphi['maLop'] ?? 'N/A' ?></td>
                    <td class="text-center"><?= $this->hocPhiModel->getTenKyHoc($hocphi['kyHoc']) ?></td>
                    <td class="text-center">Tháng <?= $hocphi['thang'] ?></td>
                    <td class="text-center"><?= $hocphi['namHoc'] ?></td>
                    <td class="text-end fw-bold text-danger"><?= number_format($hocphi['soTien'], 0, ',', '.') ?> đ</td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($hocphi['hanNop'])) ?></td>
                    <td class="text-center">
                        <?php if ($hocphi['trangThai'] == 'QUA_HAN'): ?>
                            <span class="badge bg-danger">Quá hạn</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Chưa đóng</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-primary btn-sm" onclick="chonHocPhi(<?= $hocphi['maHocPhi'] ?>, <?= $hocphi['soTien'] ?>, '<?= $hocphi['kyHoc'] ?>')">
                            <i class="fas fa-credit-card me-1"></i>Thanh toán
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>Không có học phí nào cần đóng</h5>
                        <p class="text-muted">Tất cả học phí đã được thanh toán.</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- Modal Chọn phương thức thanh toán -->
<div class="modal fade" id="modalPhuongThuc" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-credit-card me-2"></i>Chọn phương thức thanh toán</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Mã học phí:</label>
                            <input type="text" id="displayMaHocPhi" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Số tiền:</label>
                            <input type="text" id="displaySoTien" class="form-control fw-bold text-danger" readonly>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Kỳ học:</label>
                            <input type="text" id="displayKyHoc" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <hr>
                
                <h6 class="mb-3">Chọn phương thức thanh toán</h6>
                
                <div class="row">
                    <!-- Thanh toán Online -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                                <h5>Thanh toán Online</h5>
                                <p class="text-muted small">Thanh toán nhanh chóng qua <br> ví điện tử hoặc Internet Banking</p>
                                <button type="button" class="btn btn-primary" onclick="chonOnline()">
                                    Chọn phương thức này
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Thanh toán tại trường -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-success">
                            <div class="card-body text-center">
                                <i class="fas fa-school fa-3x text-success mb-3"></i>
                                <h5>Thanh toán tại trường</h5>
                                <p class="text-muted small">In phiếu thu và thanh toán trực tiếp <br>tại văn phòng nhà trường</p>
                                <button type="button" class="btn btn-success" onclick="chonTruong()">
                                    Chọn phương thức này
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Thanh toán (CHỈ 1 MODAL DUY NHẤT) -->
<div class="modal fade" id="modalQR" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-qrcode me-2"></i>QR thanh toán</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrLoading" class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Đang tạo mã QR...</p>
                </div>
                <div id="qrContent" style="display: none;">
                    <img src="" id="qrImage" class="img-fluid mb-3" alt="QR Code">
                    <p class="small text-muted">Mở ứng dụng MoMo/VNPay <br> và quét mã QR để thanh toán</p>
                    
                    <!-- PHẦN TỬ paymentStatus -->
                    <div class="alert alert-warning mb-3" id="paymentStatus">
                        <i class="fas fa-clock me-1"></i>
                        <span>ĐANG CHỜ THANH TOÁN...</span>
                    </div>
                    
                    <!-- NÚT TEST -->
                    <div class="test-buttons mt-3">
                        <button class="btn btn-success w-100 mb-2" onclick="testThanhCong()">
                            <i class="fas fa-check me-2"></i>TEST THÀNH CÔNG
                        </button>
                        <br>
                        <button class="btn btn-danger w-100" onclick="testThatBai()">
                            <i class="fas fa-times me-2"></i>TEST THẤT BẠI
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="huyThanhToan()">
                    <i class="fas fa-times me-1"></i>Hủy
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thanh toán thành công (THÊM MODAL NÀY) -->
<div class="modal fade" id="modalSuccess" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Thanh toán thành công</h5>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h5>Thanh toán thành công!</h5>
                <p class="text-muted">Học phí đã được thanh toán thành công.</p>
                <p class="text-muted">Mã giao dịch: <strong id="successMaGiaoDich">GD123456789</strong></p>
                <p class="text-muted">Biên lai điện tử đã được tạo.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-success me-2" onclick="xemBienLai()">
                    <i class="fas fa-receipt me-1"></i>XEM BIÊN LAI
                </button>
                <button type="button" class="btn btn-success" onclick="dongModal()">
                    <i class="fas fa-check me-1"></i>ĐỒNG Ý
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thanh toán thất bại -->
<div class="modal fade" id="modalFail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Thanh toán thất bại</h5>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                <h5>Thanh toán thất bại</h5>
                <p class="text-muted">Giao dịch không thành công. Vui lòng thử lại.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-warning me-2" onclick="thuLai()">
                    <i class="fas fa-redo me-1"></i>THỬ LẠI
                </button>
                <button type="button" class="btn btn-secondary" onclick="chonPhuongThucKhac()">
                    <i class="fas fa-credit-card me-1"></i>CHỌN PHƯƠNG THỨC KHÁC
                </button>
            </div>
        </div>
    </div>
</div>
</div>

<script src="assets/js/hocphi.js"></script>