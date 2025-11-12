<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-chart-line me-2"></i>Thống Kê Báo Cáo
            </h1>
            <p class="text-muted">Tổng quan hệ thống và các tùy chọn tạo báo cáo chi tiết.</p>
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
    
    <?php 
    if (isset($_SESSION['warning'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div><?= $_SESSION['warning']; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['warning']); ?>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-12">
            <h5 class="text-primary mb-3"><i class="fas fa-layer-group me-2"></i>Tổng Quan Hệ Thống</h5>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Tổng Số Lớp</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $tongSoLop ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Lớp Đã Có GVCN</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $lopCoGVCN ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            $lopChuaGVCN = $tongSoLop - $lopCoGVCN;
            $tyLeHoanThanh = ($tongSoLop > 0) ? round(($lopCoGVCN / $tongSoLop) * 100) : 0;
        ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Chưa PC GVCN / Hoàn thành</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $lopChuaGVCN ?> / <?= $tyLeHoanThanh ?>%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Tổng Số Giáo Viên</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $tongSoGV ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0 text-primary">
                <i class="fas fa-filter me-2"></i>Tạo Báo Cáo Chi Tiết
            </h5>
        </div>
        <div class="card-body">
            <form action="index.php" method="GET">
                <input type="hidden" name="controller" value="ThongKe">
                <input type="hidden" name="action" value="chiTietBaoCao">

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="loaiBaoCao" class="form-label fw-semibold">1. Loại Báo Cáo</label>
                        <select name="loaiBaoCao" id="loaiBaoCao" class="form-select" required>
                            <option value="">-- Chọn Loại Báo Cáo --</option>
                            <option value="phanCong">Báo cáo Phân công Giáo viên</option>
                            <option value="hocLuc">Báo cáo Học lực theo Khối/Lớp</option>
                            <option value="chuyenCan">Báo cáo Chuyên cần theo Khối/Lớp</option>
                            </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="maKhoi" class="form-label fw-semibold">2. Chọn Khối</label>
                        <select name="maKhoi" id="maKhoi" class="form-select">
                             <option value="all">Tất cả Khối</option>
                             <?php foreach ($danhSachKhoi as $khoi): ?>
                                <option value="<?= $khoi['maKhoi'] ?>"><?= htmlspecialchars($khoi['tenKhoi']) ?></option>
                             <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="maLop" class="form-label fw-semibold">3. Chọn Lớp</label>
                        <select name="maLop" id="maLop" class="form-select">
                             <option value="all">Tất cả Lớp</option>
                             <?php foreach ($danhSachLop as $lop): ?>
                                <option value="<?= $lop['maLop'] ?>"><?= htmlspecialchars($lop['tenLop']) ?></option>
                             <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-1 mb-3">
                        <label for="hocKy" class="form-label fw-semibold">4. HK</label>
                        <select name="hocKy" id="hocKy" class="form-select">
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3 align-self-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-chart-bar me-1"></i> Tạo Báo Cáo
                        </button>
                    </div>
                </div>
                
            </form>
        </div>
    </div>
</div>
<style>
/* CSS cho card thống kê */
.border-left-primary { border-left: .25rem solid #4e73df!important;}
.border-left-success { border-left: .25rem solid #1cc88a!important;}
.border-left-warning { border-left: .25rem solid #f6c23e!important;}
.border-left-info { border-left: .25rem solid #36b9cc!important;}
.text-xs { font-size: 0.7rem; }
.text-gray-300 { color: #dddfeb!important;}
</style>