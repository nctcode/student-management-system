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
                Lớp: <span class="fw-bold text-dark"><?= htmlspecialchars($hocSinh['tenLop'] ?? 'Chưa phân lớp') ?></span>
            </p>
        </div>
        <div>
            <a href="javascript:history.back()" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-lg-<?= $isGVCN ? '6' : '12' ?>">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center border-bottom-0">
                    <div class="icon-shape bg-primary text-white rounded-circle me-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold text-primary">Thông Tin Cá Nhân</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="small text-muted fw-bold">HỌ TÊN</label><div class="fw-bold text-dark fs-5"><?= htmlspecialchars($hocSinh['hoTen'] ?? '') ?></div></div>
                        <div class="col-md-6"><label class="small text-muted fw-bold">MÃ HS</label><div><span class="badge bg-light text-dark border"><?= $hocSinh['maHocSinh'] ?? '' ?></span></div></div>
                        <?php if ($isGVCN): ?>
                            <div class="col-md-6"><label class="small text-muted fw-bold">NGÀY SINH</label><div><?= isset($hocSinh['ngaySinh']) ? date('d/m/Y', strtotime($hocSinh['ngaySinh'])) : 'N/A' ?></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">GIỚI TÍNH</label><div><?= ($hocSinh['gioiTinh'] ?? '') == 'NAM' ? 'Nam' : (($hocSinh['gioiTinh'] == 'NU') ? 'Nữ' : 'Khác') ?></div></div>
                            <div class="col-12"><label class="small text-muted fw-bold">ĐỊA CHỈ</label><div><?= htmlspecialchars($hocSinh['diaChi'] ?? 'N/A') ?></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">SĐT</label><div><?= htmlspecialchars($hocSinh['soDienThoai'] ?? 'N/A') ?></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">EMAIL</label><div><?= htmlspecialchars($hocSinh['email'] ?? 'N/A') ?></div></div>
                        <?php else: ?>
                            <div class="col-12"><div class="alert alert-light border small"><i class="fas fa-info-circle me-1"></i> Thông tin liên hệ chỉ hiển thị với GVCN.</div></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($isGVCN): ?>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center border-bottom-0">
                    <div class="icon-shape bg-success text-white rounded-circle me-3"><i class="fas fa-users"></i></div>
                    <h5 class="card-title mb-0 fw-bold text-success">Thông Tin Phụ Huynh</h5>
                </div>
                <div class="card-body pt-0">
                    <?php if (empty($hocSinh['tenPhuHuynh'])): ?>
                        <div class="text-center text-muted py-4">Chưa cập nhật thông tin</div>
                    <?php else: ?>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="small text-muted fw-bold">HỌ TÊN PH</label><div class="fw-bold text-dark"><?= htmlspecialchars($hocSinh['tenPhuHuynh']) ?></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">QUAN HỆ</label><div><span class="badge bg-success bg-opacity-10 text-success"><?= htmlspecialchars($hocSinh['moiQuanHe'] ?? 'N/A') ?></span></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">SĐT</label><div><?= htmlspecialchars($hocSinh['sdtPhuHuynh'] ?? 'N/A') ?></div></div>
                            <div class="col-md-6"><label class="small text-muted fw-bold">NGHỀ NGHIỆP</label><div><?= htmlspecialchars($hocSinh['ngheNghiep'] ?? 'N/A') ?></div></div>
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
                        <div class="icon-shape bg-warning text-white rounded-circle me-3"><i class="fas fa-star"></i></div>
                        <h5 class="card-title mb-0 fw-bold text-dark">Bảng Điểm Chi Tiết</h5>
                    </div>
                    <?php if (!$isGVCN): ?><span class="badge bg-warning text-dark"><i class="fas fa-lock me-1"></i>Chế độ GVBM</span><?php endif; ?>
                </div>
                
                <div class="card-body p-0">
                    <?php if (empty($diemSo)): ?>
                        <div class="text-center py-5"><p class="text-muted">Chưa có dữ liệu điểm số nào.</p></div>
                    <?php else: ?>
                        <?php 
                            // --- LOGIC GOM NHÓM DỮ LIỆU ---
                            $groupedData = [];
                            foreach ($diemSo as $row) {
                                $hkKey = "Học Kỳ " . $row['hocKy'] . " - Năm học " . $row['namHoc'];
                                $monKey = $row['tenMonHoc'];
                                $loaiDiem = strtoupper($row['loaiDiem']);
                                
                                if (!isset($groupedData[$hkKey][$monKey])) {
                                    $groupedData[$hkKey][$monKey] = ['M'=>[], '15P'=>[], '1T'=>[]];
                                }
                                
                                // --- SỬA LẠI ĐOẠN NÀY ĐỂ BẮT ĐÚNG ĐIỂM 1 TIẾT ---
                                if (strpos($loaiDiem, 'MIENG') !== false || $loaiDiem == 'M') {
                                    $groupedData[$hkKey][$monKey]['M'][] = $row['diemSo'];
                                } 
                                elseif (strpos($loaiDiem, '15') !== false || $loaiDiem == '15P') {
                                    $groupedData[$hkKey][$monKey]['15P'][] = $row['diemSo'];
                                } 
                                // Kiểm tra kỹ các từ khóa cho điểm 1 Tiết / Giữa kỳ
                                elseif (strpos($loaiDiem, '1_TIET') !== false || strpos($loaiDiem, '1T') !== false || strpos($loaiDiem, 'GIUA') !== false || strpos($loaiDiem, 'G') !== false) {
                                    $groupedData[$hkKey][$monKey]['1T'][] = $row['diemSo'];
                                } 
                                // Nếu không phải HK thì ném vào 15p (hoặc để trống tùy bạn)
                                elseif (strpos($loaiDiem, 'HK') === false && strpos($loaiDiem, 'THI') === false) {
                                    $groupedData[$hkKey][$monKey]['15P'][] = $row['diemSo']; 
                                }
                            }
                        ?>

                        <div class="accordion accordion-flush" id="accordionGrades">
                            <?php $idx = 0; foreach ($groupedData as $hkTitle => $subjects): 
                                $show = $idx === 0 ? "show" : ""; 
                            ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingG<?=$idx?>">
                                        <button class="accordion-button <?=$idx===0?'':'collapsed'?> fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#c<?=$idx?>">
                                            <i class="far fa-calendar-alt text-primary me-2"></i> <?= $hkTitle ?>
                                        </button>
                                    </h2>
                                    <div id="c<?=$idx?>" class="accordion-collapse collapse <?= $show ?>" data-bs-parent="#accordionGrades">
                                        <div class="accordion-body p-4"> 
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover align-middle mb-0 text-center custom-table">
                                                    <thead class="bg-primary text-white">
                                                        <tr>
                                                            <th style="width: 50px;">STT</th>
                                                            <th class="text-start" style="width: 300px;">Môn Học</th>
                                                            <th>Điểm Miệng</th>
                                                            <th>Điểm 15 Phút</th>
                                                            <th>Điểm 1 Tiết</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $stt = 1; foreach ($subjects as $tenMon => $cols): ?>
                                                            <tr>
                                                                <td class="text-muted bg-light"><?= $stt++ ?></td>
                                                                <td class="text-start fw-bold text-dark bg-light"><?= htmlspecialchars($tenMon) ?></td>
                                                                
                                                                <td class="fw-bold text-secondary">
                                                                    <?php 
                                                                    $scores = array_map(function($d) { return round((float)$d, 1); }, $cols['M']);
                                                                    echo !empty($scores) ? implode(', ', $scores) : '-'; 
                                                                    ?>
                                                                </td>
                                                                
                                                                <td class="fw-bold text-secondary">
                                                                    <?php 
                                                                    $scores = array_map(function($d) { return round((float)$d, 1); }, $cols['15P']);
                                                                    echo !empty($scores) ? implode(', ', $scores) : '-'; 
                                                                    ?>
                                                                </td>
                                                                
                                                                <td class="fw-bold text-primary">
                                                                    <?php 
                                                                    $scores = array_map(function($d) { return round((float)$d, 1); }, $cols['1T']);
                                                                    echo !empty($scores) ? implode(', ', $scores) : '-'; 
                                                                    ?>
                                                                </td>
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

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <div class="icon-shape bg-info text-white rounded-circle me-3"><i class="fas fa-calendar-check"></i></div>
                    <h5 class="card-title mb-0 fw-bold text-info">Lịch Sử Chuyên Cần</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($chuyenCan)): ?>
                        <div class="text-center py-4 text-muted">Không có dữ liệu vắng nghỉ.</div>
                    <?php else: ?>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr><th style="width: 200px;">Ngày</th><th class="text-center" style="width: 150px;">Trạng Thái</th><th>Ghi Chú</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($chuyenCan as $cc): ?>
                                    <tr>
                                        <td class="fw-bold"><?= isset($cc['ngayHoc']) ? date('d/m/Y', strtotime($cc['ngayHoc'])) : '' ?></td>
                                        <td class="text-center">
                                            <?php if (($cc['trangThai'] ?? '') == 'Vang'): ?><span class="badge bg-danger">Vắng</span>
                                            <?php elseif (($cc['trangThai'] ?? '') == 'Co_mat'): ?><span class="badge bg-success">Có mặt</span>
                                            <?php else: ?><span class="badge bg-warning text-dark"><?= $cc['trangThai'] ?></span><?php endif; ?>
                                        </td>
                                        <td class="text-muted fst-italic"><?= htmlspecialchars($cc['ghiChu'] ?? '') ?></td>
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
</div>