<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-chart-bar me-2"></i>Dashboard Tổng Quan Trường Học
            </h1>
            <p class="text-muted">Các chỉ số nhanh về Phân công, Học lực và Chuyên cần (HK <?= $currentHocKy ?>)</p>
        </div>
    </div>

    <?php 
    if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <div><?= $_SESSION['error']; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <h5 class="text-primary mb-3 mt-4"><i class="fas fa-tasks me-2"></i>Phân Công Giáo Viên</h5>
    <div class="row mb-4">
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Tổng Số Lớp</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $phanCongData['tongSoLop'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-school fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Tỷ lệ PC GVCN</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $phanCongData['tyLePCGVCN'] ?? 0 ?>%</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-tie fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Tỷ lệ PC GVBM</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $phanCongData['tyLePCGVBM'] ?? 0 ?>%</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Tổng Môn cần dạy</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $phanCongData['tongMonHocTrongTruong'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-book fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="text-primary mb-3 mt-4"><i class="fas fa-graduation-cap me-2"></i>Xếp loại Học lực</h5>
    <div class="row mb-4">
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-secondary text-uppercase mb-1">Tổng số HS Xếp loại</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $hocLucData['tongSoHocSinh'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Tỷ lệ Xếp loại Giỏi</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $hocLucData['tyLeGIOI'] ?? 0 ?>%</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-star fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Tỷ lệ Xếp loại Khá</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $hocLucData['tyLeKHA'] ?? 0 ?>%</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">TB / Yếu (Tỷ lệ rủi ro)</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= round(($hocLucData['tyLeTB'] ?? 0) + ($hocLucData['tyLeYEU'] ?? 0), 1) ?>%</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <h5 class="text-primary mb-3 mt-4"><i class="fas fa-clipboard-check me-2"></i>Chuyên Cần</h5>
    <div class="row mb-4">
         <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-secondary text-uppercase mb-1">Tổng số HS đang học</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $chuyenCanData['tongSoHocSinh'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-graduate fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">TB Lần Vắng/Học sinh</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $chuyenCanData['TBSoLanVangTrenHS'] ?? 0 ?> lần</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-times fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
</div>
<style>
.border-left-primary { border-left: .25rem solid #4e73df!important;}
.border-left-success { border-left: .25rem solid #1cc88a!important;}
.border-left-warning { border-left: .25rem solid #f6c23e!important;}
.border-left-info { border-left: .25rem solid #36b9cc!important;}
.border-left-danger { border-left: .25rem solid #e74a3b!important;}
.border-left-secondary { border-left: .25rem solid #858796!important;}
.text-xs { font-size: 0.7rem; }
.text-gray-300 { color: #dddfeb!important;}
</style>