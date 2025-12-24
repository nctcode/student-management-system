<?php 
$showSidebar = false; 
require_once 'views/layouts/header.php'; 

// Xử lý Active Tab
$tab = isset($_GET['loaiBaoCao']) ? $_GET['loaiBaoCao'] : 'hocLuc';
$mapTab = ['hocLuc'=>'hoctap', 'hanhKiem'=>'hanhkiem', 'nhanSu'=>'nhansu', 'quyMo'=>'quymo', 'taiChinh'=>'taichinh'];
$activeTab = $mapTab[$tab] ?? 'hoctap';
?>

<link rel="stylesheet" href="assets/css/thongke.css">

<div style="position: fixed; top: 70px; left: 0; width: 260px; bottom: 0; z-index: 1000; background: #fff; border-right: 1px solid #eaecf4; overflow-y: auto;">
    <?php require_once 'views/layouts/sidebar/bangiamhieu.php'; ?>
</div>

<div style="margin-left: 260px; padding: 25px; min-height: 100vh; background-color: #f8f9fc;">
    <div class="container-fluid p-0">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-primary fw-bold"><i class="fas fa-chart-pie me-2"></i>Thống Kê & Báo Cáo</h1>
                <p class="text-muted mb-0 small">Hệ thống quản lý thông tin giáo dục</p>
            </div>
            <div class="badge bg-primary p-2 shadow-sm">
                <i class="far fa-calendar-alt me-1"></i> Năm học: 2024-2025
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4 border-top-primary">
            <div class="card-body bg-white py-3">
                <form action="index.php" method="GET" id="filterForm">
                    <input type="hidden" name="controller" value="thongke">
                    
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Học Kỳ</label>
                            <select name="hk" class="form-select bg-light border-0">
                                <option value="1" <?= (isset($_GET['hk']) && $_GET['hk']==1)?'selected':'' ?>>Học kỳ 1</option>
                                <option value="2" <?= (isset($_GET['hk']) && $_GET['hk']==2)?'selected':'' ?>>Học kỳ 2</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Loại Báo Cáo</label>
                            <select name="loaiBaoCao" class="form-select bg-light border-0">
                                <option value="hocLuc" <?= $tab=='hocLuc'?'selected':'' ?>>1. Học tập & Dự báo</option>
                                <option value="hanhKiem" <?= $tab=='hanhKiem'?'selected':'' ?>>2. Hạnh kiểm</option>
                                <option value="nhanSu" <?= $tab=='nhanSu'?'selected':'' ?>>3. Nhân sự</option>
                                <option value="quyMo" <?= $tab=='quyMo'?'selected':'' ?>>4. Quy mô</option>
                                <option value="taiChinh" <?= $tab=='taiChinh'?'selected':'' ?>>5. Tài chính</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Khối</label>
                            <select name="maKhoi" id="selectKhoi" class="form-select bg-light border-0">
                                <option value="all">Toàn trường</option>
                                <?php foreach($danhSachKhoi as $k): ?>
                                    <option value="<?= $k['maKhoi'] ?>" <?= (isset($_GET['maKhoi']) && $_GET['maKhoi']==$k['maKhoi'])?'selected':'' ?>><?= $k['tenKhoi'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Lớp</label>
                            <select name="maLop" id="selectLop" class="form-select bg-light border-0">
                                <option value="all">Tất cả lớp</option>
                                <?php 
                                    $selLop = isset($_GET['maLop']) ? $_GET['maLop'] : 'all';
                                    foreach($danhSachLop as $l): 
                                ?>
                                    <option value="<?= $l['maLop'] ?>" data-khoi="<?= $l['maKhoi'] ?>" <?= ($selLop==$l['maLop'])?'selected':'' ?>><?= $l['tenLop'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" name="action" value="index" class="btn btn-primary fw-bold flex-grow-1 shadow-sm">
                                    <i class="fas fa-filter me-1"></i> Xem
                                </button>
                                <button type="submit" name="action" value="export" class="btn btn-success fw-bold flex-grow-1 shadow-sm">
                                    <i class="fas fa-file-excel me-1"></i> Tải về
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mb-4 border-0">
            <div class="card-header border-bottom bg-white py-3">
                <ul class="nav nav-pills card-header-pills" id="mainTabs">
                    <li class="nav-item"><a class="nav-link fw-bold <?= $activeTab=='hoctap'?'active':'' ?>" data-bs-toggle="tab" href="#hoctap">Học Tập</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold <?= $activeTab=='hanhkiem'?'active':'' ?>" data-bs-toggle="tab" href="#hanhkiem">Hạnh Kiểm</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold <?= $activeTab=='nhansu'?'active':'' ?>" data-bs-toggle="tab" href="#nhansu">Nhân Sự</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold <?= $activeTab=='quymo'?'active':'' ?>" data-bs-toggle="tab" href="#quymo">Quy Mô</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold <?= $activeTab=='taichinh'?'active':'' ?>" data-bs-toggle="tab" href="#taichinh">Tài Chính</a></li>
                </ul>
            </div>
            
            <div class="card-body bg-white p-4">
                <div class="tab-content">
                    
                    <div class="tab-pane fade <?= $activeTab=='hoctap'?'show active':'' ?>" id="hoctap">
                        <div class="alert alert-light border-start border-4 border-info shadow-sm mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="fw-bold text-info mb-1"><i class="fas fa-chart-line me-2"></i>Dự Báo Tốt Nghiệp (Khối 12)</h5>
                                    <p class="mb-0 small text-muted">Dựa trên kết quả học tập hiện tại.</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="d-inline-block me-3 text-center">
                                        <span class="d-block display-6 fw-bold text-success"><?= $duBaoTN['TyLeDau'] ?>%</span>
                                        <small class="text-muted fw-bold">TỶ LỆ ĐẬU</small>
                                    </span>
                                    <span class="d-inline-block text-center">
                                        <span class="d-block display-6 fw-bold text-danger"><?= $duBaoTN['NguyCoRot'] ?></span>
                                        <small class="text-muted fw-bold">NGUY CƠ</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-white border-bottom-0 pb-0">
                                        <h6 class="fw-bold text-dark border-start border-4 border-primary ps-2">PHỔ ĐIỂM HỌC TẬP</h6>
                                    </div>
                                    <div class="card-body">
                                        <div style="height:320px; position: relative;"><canvas id="chartPhoDiem"></canvas></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-white border-bottom-0 pb-0">
                                        <h6 class="fw-bold text-dark border-start border-4 border-success ps-2">SO SÁNH TIẾN BỘ</h6>
                                    </div>
                                    <div class="card-body">
                                        <div style="height:320px; position: relative;"><canvas id="chartSoSanh"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade <?= $activeTab=='hanhkiem'?'show active':'' ?>" id="hanhkiem">
                        <div class="row justify-content-center">
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <h5 class="fw-bold mb-4">Cơ Cấu Hạnh Kiểm Toàn Trường</h5>
                                        <div style="height:350px"><canvas id="chartHanhKiem"></canvas></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade <?= $activeTab=='nhansu'?'show active':'' ?>" id="nhansu">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3">Giáo Viên Chủ Nhiệm Nhiều Lớp Nhất</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 250px;">Giáo Viên</th>
                                                <th class="text-center" style="width:100px">Số Lớp</th>
                                                <th>Danh Sách Lớp Chủ Nhiệm</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($gvTaiCongViec)): foreach($gvTaiCongViec as $g): ?>
                                            <tr>
                                                <td class="fw-bold text-dark"><?= $g['hoTen'] ?></td>
                                                <td class="text-center"><span class="badge bg-danger rounded-pill px-3"><?= $g['soLopCN'] ?></span></td>
                                                <td class="text-primary fw-bold"><?= $g['danhSachLop'] ?></td>
                                            </tr>
                                            <?php endforeach; else: ?><tr><td colspan="3" class="text-center text-muted">Dữ liệu ổn định</td></tr><?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade <?= $activeTab=='quymo'?'show active':'' ?>" id="quymo">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-bold text-dark mb-3">Thống Kê Sĩ Số Theo Khối</h6>
                                <table class="table table-bordered border-light">
                                    <thead class="bg-primary text-white"><tr><th>Khối</th><th class="text-center">Số Lớp</th><th class="text-center">Tổng HS</th><th class="text-center">TB/Lớp</th></tr></thead>
                                    <tbody>
                                        <?php if(!empty($siSoKhoi)): foreach($siSoKhoi as $k): $tb = ($k['tong_lop'] > 0) ? round($k['tong_hs']/$k['tong_lop'], 1) : 0; ?>
                                        <tr>
                                            <td class="fw-bold"><?= $k['tenKhoi'] ?></td>
                                            <td class="text-center"><?= $k['tong_lop'] ?></td>
                                            <td class="text-center fw-bold text-primary"><?= $k['tong_hs'] ?></td>
                                            <td class="text-center <?= $tb>45?'text-danger fw-bold':'' ?>"><?= $tb ?></td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade <?= $activeTab=='taichinh'?'show active':'' ?>" id="taichinh">
                        <div class="row g-4 mb-4">
                            <div class="col-md-3"><div class="p-3 bg-white border rounded shadow-sm h-100"><div class="d-flex justify-content-between mb-2"><span class="text-muted small fw-bold">PHẢI THU</span><i class="fas fa-money-bill-wave text-primary opacity-25 fa-2x"></i></div><h4 class="fw-bold text-primary"><?= number_format($taiChinhKPI['phaiThu']) ?> đ</h4></div></div>
                            <div class="col-md-3"><div class="p-3 bg-white border rounded shadow-sm h-100"><div class="d-flex justify-content-between mb-2"><span class="text-muted small fw-bold">ĐÃ THU</span><i class="fas fa-check-circle text-success opacity-25 fa-2x"></i></div><h4 class="fw-bold text-success"><?= number_format($taiChinhKPI['thucThu']) ?> đ</h4></div></div>
                            <div class="col-md-3"><div class="p-3 bg-white border rounded shadow-sm h-100"><div class="d-flex justify-content-between mb-2"><span class="text-muted small fw-bold">CÔNG NỢ</span><i class="fas fa-exclamation-circle text-danger opacity-25 fa-2x"></i></div><h4 class="fw-bold text-danger"><?= number_format($taiChinhKPI['congNo']) ?> đ</h4></div></div>
                            <div class="col-md-3"><div class="p-3 bg-white border rounded shadow-sm h-100"><div class="d-flex justify-content-between mb-2"><span class="text-muted small fw-bold">TIẾN ĐỘ</span><i class="fas fa-chart-line text-info opacity-25 fa-2x"></i></div><h4 class="fw-bold text-info"><?= $taiChinhKPI['tyLe'] ?>%</h4><div class="progress mt-2" style="height:4px"><div class="progress-bar bg-info" style="width:<?= $taiChinhKPI['tyLe'] ?>%"></div></div></div></div>
                        </div>
                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-white"><h6 class="m-0 fw-bold">Dòng Tiền Thực Thu</h6></div>
                                    <div class="card-body"><div style="height:320px"><canvas id="chartDoanhThu"></canvas></div></div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-white"><h6 class="m-0 fw-bold text-danger">Top Nợ Học Phí</h6></div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped mb-0 small">
                                            <thead class="table-light"><tr><th>Lớp</th><th class="text-center">SL</th><th class="text-end">Tiền Nợ</th></tr></thead>
                                            <tbody><?php if(!empty($topLopNo)) foreach($topLopNo as $n) echo "<tr><td class='fw-bold'>{$n['tenLop']}</td><td class='text-center'><span class='badge bg-danger'>{$n['soHocSinhNo']}</span></td><td class='text-end fw-bold text-danger'>".number_format($n['tongNo'])."</td></tr>"; else echo "<tr><td colspan='3' class='text-center py-3'>Không có nợ</td></tr>"; ?></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ThongKeData = {
        phoDiem: <?= $jsonPhoDiem ?: '[0,0,0,0,0]' ?>,
        soSanhHK1: <?= $jsonSS_HK1 ?: '[]' ?>,
        soSanhHK2: <?= $jsonSS_HK2 ?: '[]' ?>,
        hanhKiem: <?= $jsonHanhKiem ?: '[]' ?>,
        doanhThuLabels: <?= $jsonLabelsDT ?: '[]' ?>,
        doanhThuData: <?= $jsonDataDT ?: '[]' ?>
    };
</script>

<script src="assets/js/thongke.js"></script>