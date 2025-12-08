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
                    <i class="fas fa-list-check"></i> Xem danh sách
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

<script>
    const buoiHocData = <?= json_encode($lopHocList) ?>;
</script>
<script src="assets/js/chuyencan.js"></script>