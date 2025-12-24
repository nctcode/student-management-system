<?php
// Nhóm các buổi học theo lớp
$lopHocList = [];
foreach ($danhSachBuoiHoc as $buoi) {
    $lopHocList[$buoi['maLop']]['tenLop'] = $buoi['tenLop'];
    $lopHocList[$buoi['maLop']]['buoiHoc'][] = $buoi;
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Ghi nhận chuyên cần</strong></h1>

    <div id="js-alert-container"></div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Chọn thông tin để điểm danh</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="maLop"><strong>Chọn Lớp:</strong></label>
                        <select id="maLop" class="form-control" required>
                            <option value="">Chọn Lớp</option>
                            <?php foreach ($lopHocList as $maLop => $data): ?>
                                <option value="<?= $maLop ?>"><?= htmlspecialchars($data['tenLop']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="ngayDiemDanh"><strong>Chọn Ngày:</strong></label>
                        <input type="date" id="ngayDiemDanh" 
                            class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="maBuoiHoc"><strong>Chọn Buổi học:</strong></label>
                        <select id="maBuoiHoc" class="form-control" required disabled>
                            <option value="">Chọn lớp và ngày trước</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mt-3 d-flex justify-content-end">
                <button type="button" class="btn btn-primary" id="btnXemDiemDanh" disabled>
                    <i class="fas fa-sync"></i> Xem danh sách
                </button>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4" id="cardDiemDanh" style="display: none;">
        <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="m-0 font-weight-bold text-primary">Bảng điểm danh</h5>
                <div id="cardSubTitleDiemDanh" class="mt-1 text-muted small"></div>
            </div>
            
            <div class="mt-2 mt-md-0">
                <button type="button" id="btnDiemDanhNhanh" class="btn btn-success btn-sm">
                    <i class="fas fa-check-double"></i> Có mặt tất cả
                </button>
                <button type="button" id="btnApDungNhom" class="btn btn-warning btn-sm">
                    <i class="fas fa-users"></i> Áp dụng cho nhóm
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?controller=chuyencan&action=luu" id="formLuuChuyenCan">
                
                <div id="hiddenInputsContainer"></div>

                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-bordered" id="dataTableChuyenCan" width="100%" cellspacing="0">
                        <thead style="position: sticky; top: 0; background: white; z-index: 10;">
                            <tr>
                                <th width="3%"><input type="checkbox" id="checkAllNhom"></th>
                                <th width="5%">STT</th>
                                <th>Họ tên</th>
                                <th width="45%">Trạng thái</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDiemDanh"></tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <button type="button" id="btnHuy" class="btn btn-danger btn-lg">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-success btn-lg ms-2">
                        <i class="fas fa-save"></i> Lưu chuyên cần
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalApDungNhom" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title d-flex align-items-center" id="modalLabel">
                    <i class="fas fa-users-cog mr-2"></i> 
                    <span class="font-weight-bold">Thiết lập nhanh cho nhóm</span>
                </h5>
                <button type="button" class="btn-close-custom" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary mb-3">Chọn trạng thái để áp dụng cho các học sinh đã chọn:</p>
                
                <div class="row no-gutters status-grid">
                    <div class="col-6 p-1">
                        <label class="status-option w-100 mb-0">
                            <input type="radio" name="groupStatus" value="CO_MAT" checked>
                            <div class="status-card border rounded p-3 text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <div class="font-weight-bold">Có mặt</div>
                            </div>
                        </label>
                    </div>
                    <div class="col-6 p-1">
                        <label class="status-option w-100 mb-0">
                            <input type="radio" name="groupStatus" value="DI_MUON">
                            <div class="status-card border rounded p-3 text-center">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <div class="font-weight-bold">Đi muộn</div>
                            </div>
                        </label>
                    </div>
                    <div class="col-6 p-1">
                        <label class="status-option w-100 mb-0">
                            <input type="radio" name="groupStatus" value="VANG_CO_PHEP">
                            <div class="status-card border rounded p-3 text-center">
                                <i class="fas fa-envelope-open-text fa-2x text-info mb-2"></i>
                                <div class="font-weight-bold">Vắng (P)</div>
                            </div>
                        </label>
                    </div>
                    <div class="col-6 p-1">
                        <label class="status-option w-100 mb-0">
                            <input type="radio" name="groupStatus" value="VANG_KHONG_PHEP">
                            <div class="status-card border rounded p-3 text-center">
                                <i class="fas fa-user-times fa-2x text-danger mb-2"></i>
                                <div class="font-weight-bold">Vắng (K)</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="alert alert-light border mt-3 mb-0 py-2 px-3 small">
                    <i class="fas fa-info-circle text-primary"></i> Trạng thái sẽ thay đổi đồng loạt cho các dòng có tích chọn.
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-danger px-4 rounded-pill shadow-sm transition-hover" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fas fa-times-circle mr-1"></i> Đóng
                </button>
                
                <button type="button" id="btnXacNhanApDung" class="btn btn-primary px-4 rounded-pill shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> Xác nhận áp dụng
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmTatCa" data-backdrop="static" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-check-double mr-2"></i> Xác nhận điểm danh
                </h5>
                <button type="button" class="btn-close-custom" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body text-center p-4">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <p class="mb-0 text-secondary">Bạn có chắc chắn muốn đánh dấu <strong>"Có mặt"</strong> cho tất cả học sinh trong danh sách?</p>
            </div>

            <div class="modal-footer border-0 py-3 justify-content-center">
                <button type="button" class="btn btn-danger px-4 rounded-pill shadow-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fas fa-times-circle mr-1"></i> Hủy
                </button>
                <button type="button" id="btnXacNhanDiemDanhTatCa" class="btn btn-primary px-4 rounded-pill shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmHuy" data-backdrop="static" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Xác nhận hủy
                </h5>
                <button type="button" class="btn-close-custom" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                <p class="mb-0 text-secondary">Bạn có chắc chắn muốn hủy? <br><strong>Bảng điểm danh sẽ bị đóng và các thay đổi chưa lưu sẽ bị mất.</strong></p>
            </div>
            <div class="modal-footer border-0 py-3 justify-content-center">
                <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    Quay lại
                </button>
                <button type="button" id="btnXacNhanHuyThaoTac" class="btn btn-danger px-4 rounded-pill shadow-sm">
                    Xác nhận hủy
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const buoiHocData = <?= json_encode($lopHocList) ?>;
</script>
<script src="assets/js/chuyencan.js"></script>
<style>
    #modalApDungNhom .modal-content {
        border-radius: 15px;
        overflow: hidden;
    }

    .status-option input[type="radio"] {
        display: none;
    }

    .status-card {
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fff;
        border: 2px solid #eee !important;
    }

    .status-card:hover {
        background-color: #f8f9fa;
        border-color: #007bff !important;
        transform: translateY(-2px);
    }

    .status-option input[type="radio"]:checked + .status-card {
        background-color: #e7f1ff;
        border-color: #007bff !important;
        box-shadow: 0 4px 12px rgba(0,123,255,0.15);
    }

    .status-option input[type="radio"]:checked + .status-card i {
        transform: scale(1.1);
    }

    .status-card div {
        font-size: 0.9rem;
        color: #444;
    }

    .btn-close-custom {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        outline: none !important;
    }

    .btn-close-custom:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .btn-close-custom i {
        font-size: 14px;
    }

    .rounded-pill {
        border-radius: 50px !important;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .btn-secondary.rounded-pill:hover {
        transform: translateY(-1px);
    }

    .btn-danger.rounded-pill {
        background-color: #dc3545;
        border: none;
    }

    .btn-danger.rounded-pill:hover {
        background-color: #c82333;
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3) !important;
        transform: translateY(-1px);
    }

    .btn-primary.rounded-pill:hover {
        transform: translateY(-1px);
    }

    .btn-close-custom:hover {
        background: rgba(220, 53, 69, 0.8);
        transform: rotate(90deg);
    }
</style>