<?php
// 1. Định nghĩa lại biến $isBasicView dựa trên biến $isGVCN từ Controller truyền sang
// Nếu là GVCN ($isGVCN = true) thì không phải Basic View. Ngược lại là Basic View.
$isBasicView = isset($isGVCN) ? !$isGVCN : true; 

$maLop = $_GET['maLop'] ?? 0;

// --- LOGIC XỬ LÝ DỮ LIỆU ĐIỂM ---
$groupedData = [];
if (!empty($diemSo)) {
    foreach ($diemSo as $row) {
        $hkKey = $row['hocKy'];
        $namHoc = $row['namHoc'];
        $monKey = $row['tenMonHoc'];
        $loaiDiem = $row['loaiDiem'];
        
        $fullKey = "Học Kỳ " . str_replace('HK', '', $hkKey) . " - Năm học " . $namHoc;

        if (!isset($groupedData[$fullKey][$monKey])) {
            $groupedData[$fullKey][$monKey] = ['MIENG' => [], '15_PHUT' => [], '1_TIET' => [], 'CUOI_KY' => []];
        }
        if (isset($groupedData[$fullKey][$monKey][$loaiDiem])) {
            $groupedData[$fullKey][$monKey][$loaiDiem][] = $row['diemSo'];
        }
    }
}

// --- HÀM TÍNH TBM ---
function calculateTBM($scores) {
    if (empty($scores['CUOI_KY'])) return null;

    $sum = 0; 
    $totalCoeff = 0;

    if (!empty($scores['MIENG'])) { 
        foreach ($scores['MIENG'] as $s) { $sum += $s; $totalCoeff++; } 
    }
    if (!empty($scores['15_PHUT'])) { 
        foreach ($scores['15_PHUT'] as $s) { $sum += $s; $totalCoeff++; } 
    }
    if (!empty($scores['1_TIET'])) { 
        foreach ($scores['1_TIET'] as $s) { $sum += $s*2; $totalCoeff+=2; } 
    }
    if (!empty($scores['CUOI_KY'])) { 
        foreach ($scores['CUOI_KY'] as $s) { $sum += $s*3; $totalCoeff+=3; } 
    }

    return ($totalCoeff == 0) ? null : round($sum / $totalCoeff, 1);
}
?>

<link rel="stylesheet" href="assets/css/danhsachlop.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-user-graduate me-2"></i>Hồ Sơ Học Sinh
            </h1>
            <p class="text-muted mb-0">
                <?= htmlspecialchars($hocSinh['hoTen'] ?? '') ?> 
                <span class="mx-2">|</span> 
                Lớp: <span class="fw-bold text-dark"><?= htmlspecialchars($hocSinh['tenLop'] ?? 'N/A') ?></span>
            </p>
        </div>
        <div>
            <a href="javascript:history.back()" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <?php if ($isBasicView): ?>
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-user-shield me-3 fa-2x"></i>
            <div>
                <strong>Chế độ Giáo Viên Bộ Môn:</strong><br>
                Bạn chỉ được quyền xem Họ tên, Mã HS và Điểm số môn bạn phụ trách. Thông tin cá nhân chi tiết đã bị ẩn.
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-lg-<?= $isBasicView ? '12' : '6' ?>">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center border-bottom-0">
                    <div class="icon-shape bg-primary text-white rounded-circle me-3" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold text-primary">Thông Tin Cơ Bản</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="small text-muted fw-bold text-uppercase">Họ và Tên</label><div class="fw-bold text-dark fs-5"><?= htmlspecialchars($hocSinh['hoTen'] ?? '') ?></div></div>
                        <div class="col-md-6"><label class="small text-muted fw-bold text-uppercase">Mã Học Sinh</label><div><span class="badge bg-light text-dark border px-3 py-2 fs-6"><?= $hocSinh['maHocSinh'] ?? '' ?></span></div></div>
                        <div class="col-md-6"><label class="small text-muted fw-bold text-uppercase">Lớp</label><div class="fw-bold"><?= htmlspecialchars($hocSinh['tenLop'] ?? '') ?></div></div>
                        <div class="col-md-6"><label class="small text-muted fw-bold text-uppercase">Trạng Thái</label><div><span class="badge bg-success">Đang học</span></div></div>

                        <?php if (!$isBasicView): ?>
                            <div class="col-12"><hr class="my-2 text-muted opacity-25"></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">NGÀY SINH</label><div><?= isset($hocSinh['ngaySinh']) ? date('d/m/Y', strtotime($hocSinh['ngaySinh'])) : '---' ?></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">GIỚI TÍNH</label><div><?= ($hocSinh['gioiTinh'] ?? '') == 'NAM' ? 'Nam' : (($hocSinh['gioiTinh'] == 'NU') ? 'Nữ' : 'Khác') ?></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">SĐT</label><div><?= htmlspecialchars($hocSinh['soDienThoai'] ?? '---') ?></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">EMAIL</label><div><?= htmlspecialchars($hocSinh['email'] ?? '---') ?></div></div>
                            <div class="col-12"><label class="small text-muted fw-bold">ĐỊA CHỈ</label><div><?= htmlspecialchars($hocSinh['diaChi'] ?? '---') ?></div></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$isBasicView): ?>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center border-bottom-0">
                    <div class="icon-shape bg-success text-white rounded-circle me-3" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-users"></i></div>
                    <h5 class="card-title mb-0 fw-bold text-success">Thông Tin Phụ Huynh</h5>
                </div>
                <div class="card-body pt-0">
                    <?php if (empty($hocSinh['tenPhuHuynh'])): ?>
                        <div class="text-center text-muted py-4">Chưa cập nhật thông tin</div>
                    <?php else: ?>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="small text-muted fw-bold">HỌ TÊN PH</label><div class="fw-bold text-dark"><?= htmlspecialchars($hocSinh['tenPhuHuynh']) ?></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">QUAN HỆ</label><div><span class="badge bg-success bg-opacity-10 text-success"><?= htmlspecialchars($hocSinh['moiQuanHe'] ?? '---') ?></span></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">SĐT LIÊN HỆ</label><div><?= htmlspecialchars($hocSinh['sdtPhuHuynh'] ?? '---') ?></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">NGHỀ NGHIỆP</label><div><?= htmlspecialchars($hocSinh['ngheNghiep'] ?? '---') ?></div></div>
                            <div class="col-12"><label class="small text-muted fw-bold">EMAIL</label><div><?= htmlspecialchars($hocSinh['emailPhuHuynh'] ?? '---') ?></div></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-warning text-white rounded-circle me-3" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-star"></i></div>
                        <h5 class="card-title mb-0 fw-bold text-dark">Bảng Điểm Chi Tiết</h5>
                    </div>
                    <?php if ($isBasicView): ?><span class="badge bg-warning text-dark"><i class="fas fa-lock me-1"></i>Chế độ GVBM</span><?php endif; ?>
                </div>
                
                <div class="card-body p-0">
                    <?php if (empty($groupedData)): ?>
                        <div class="text-center py-5"><p class="text-muted">Chưa có dữ liệu điểm số nào.</p></div>
                    <?php else: ?>
                        <div class="accordion accordion-flush" id="accordionGrades">
                            <?php $idx = 0; foreach ($groupedData as $hkTitle => $subjects): $show = $idx === 0 ? "show" : ""; ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingG<?=$idx?>">
                                        <button class="accordion-button <?=$idx===0?'':'collapsed'?> fw-bold bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#c<?=$idx?>">
                                            <i class="far fa-calendar-alt text-primary me-2"></i> <?= $hkTitle ?>
                                        </button>
                                    </h2>
                                    <div id="c<?=$idx?>" class="accordion-collapse collapse <?= $show ?>" data-bs-parent="#accordionGrades">
                                        <div class="accordion-body p-4"> 
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover align-middle mb-0 text-center">
                                                    <thead class="bg-primary text-white">
                                                        <tr>
                                                            <th style="width: 50px;">STT</th>
                                                            <th class="text-start" style="width: 250px;">Môn Học</th>
                                                            <th>Điểm Miệng</th>
                                                            <th>Điểm 15 Phút</th>
                                                            <th>Điểm 1 Tiết</th>
                                                            <th>Điểm Cuối Kỳ</th>
                                                            <th style="width: 80px;" class="bg-primary bg-opacity-75 text-white">TBM</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $stt = 1; foreach ($subjects as $tenMon => $cols): $tbm = calculateTBM($cols); ?>
                                                            <tr>
                                                                <td class="text-muted bg-light"><?= $stt++ ?></td>
                                                                <td class="text-start fw-bold text-dark bg-light"><?= htmlspecialchars($tenMon) ?></td>
                                                                <td class="text-secondary"><?php $s=array_map(function($d){return round((float)$d,1);},$cols['MIENG']); echo !empty($s)?implode(', ',$s):'-'; ?></td>
                                                                <td class="text-secondary"><?php $s=array_map(function($d){return round((float)$d,1);},$cols['15_PHUT']); echo !empty($s)?implode(', ',$s):'-'; ?></td>
                                                                <td class="text-primary fw-bold"><?php $s=array_map(function($d){return round((float)$d,1);},$cols['1_TIET']); echo !empty($s)?implode(', ',$s):'-'; ?></td>
                                                                <td class="text-danger fw-bold"><?php $s=array_map(function($d){return round((float)$d,1);},$cols['CUOI_KY']); echo !empty($s)?implode(', ',$s):'-'; ?></td>
                                                                <td class="fw-bold fs-6 bg-light"><?php if($tbm!==null): ?><span class="<?=($tbm>=5)?'text-success':'text-danger'?>"><?=$tbm?></span><?php else: ?><span class="text-muted small">-</span><?php endif; ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php $idx++; endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$isBasicView): // Chỉ GVCN mới thấy ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <div class="icon-shape bg-info text-white rounded-circle me-3" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-calendar-check"></i></div>
                    <h5 class="card-title mb-0 fw-bold text-info">Lịch Sử Chuyên Cần (30 ngày gần nhất)</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($chuyenCan)): ?>
                        <div class="text-center py-4 text-muted">Không có dữ liệu vắng nghỉ.</div>
                    <?php else: ?>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-light sticky-top">
                                    <tr><th style="width: 200px;">Ngày</th><th class="text-center" style="width: 150px;">Trạng Thái</th><th>Ghi Chú</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($chuyenCan as $cc): ?>
                                    <tr>
                                        <td class="fw-bold"><?= isset($cc['ngayHoc']) ? date('d/m/Y', strtotime($cc['ngayHoc'])) : '' ?></td>
                                        <td class="text-center">
                                            <?php 
                                                $status = $cc['trangThai'] ?? '';
                                                if ($status == 'VANG_KHONG_PHEP'): ?><span class="badge bg-danger rounded-pill px-3">Vắng KP</span>
                                            <?php elseif ($status == 'VANG_CO_PHEP'): ?><span class="badge bg-warning text-dark rounded-pill px-3">Vắng CP</span>
                                            <?php elseif ($status == 'DI_MUON'): ?><span class="badge bg-info text-dark rounded-pill px-3">Đi muộn</span>
                                            <?php elseif ($status == 'CO_MAT'): ?><span class="badge bg-success rounded-pill px-3">Có mặt</span>
                                            <?php else: ?><span class="badge bg-secondary"><?= htmlspecialchars($status) ?></span><?php endif; ?>
                                        </td>
                                        <td class="text-muted fst-italic small"><?= htmlspecialchars($cc['ghiChu'] ?? '') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>