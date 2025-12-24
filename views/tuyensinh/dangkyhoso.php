<?php
$title = "Đăng ký tuyển sinh";
?>

<!-- Thêm CSS sang trọng -->
<style>
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --accent-color: #1abc9c;
    --light-bg: #f8f9fa;
    --dark-text: #2c3e50;
    --light-text: #7f8c8d;
    --border-color: #e0e6ed;
    --shadow-light: 0 5px 15px rgba(0, 0, 0, 0.05);
    --shadow-medium: 0 10px 30px rgba(0, 0, 0, 0.1);
    --shadow-deep: 0 20px 50px rgba(0, 0, 0, 0.15);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Cải thiện toàn bộ layout */
.container-fluid {
    padding: 2rem 1rem;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
    min-height: 100vh;
}

.card {
    border: none;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-deep);
    overflow: hidden;
    background: white;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}

.card-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #1a2530 100%);
    border-bottom: none;
    padding: 2rem 2.5rem;
    position: relative;
    overflow: hidden;
}

.card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
    background-size: cover;
    opacity: 0.1;
}

.card-header h4 {
    font-weight: 700;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.card-header h4 i {
    color: var(--accent-color);
    margin-right: 10px;
}

.card-body {
    padding: 2.5rem;
    background: linear-gradient(to bottom, white 0%, #fafbfd 100%);
}

/* Tiêu đề section */
h5.border-bottom {
    border-bottom: 3px solid var(--accent-color) !important;
    padding-bottom: 1rem;
    margin-bottom: 2rem;
    color: var(--primary-color);
    font-weight: 700;
    letter-spacing: 0.3px;
    position: relative;
}

h5.border-bottom::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 60px;
    height: 3px;
    background: var(--secondary-color);
    border-radius: 2px;
}

h5.border-bottom i {
    color: var(--secondary-color);
    margin-right: 10px;
    background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Form controls nâng cao */
.form-label {
    font-weight: 600;
    color: var(--dark-text);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
}

.form-label .text-danger {
    color: #e74c3c !important;
    margin-left: 4px;
    font-weight: 700;
}

.form-control, .form-select {
    border: 2px solid var(--border-color);
    border-radius: var(--radius-sm);
    padding: 0.85rem 1rem;
    font-size: 0.95rem;
    transition: var(--transition);
    background: white;
    color: var(--dark-text);
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
}

.form-control:focus, .form-select:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15), inset 0 1px 3px rgba(0, 0, 0, 0.05);
    background: white;
    transform: translateY(-1px);
}

.form-control::placeholder {
    color: #95a5a6;
    opacity: 0.7;
}

/* Select2 custom */
.select2-container--default .select2-selection--single {
    height: 48px;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-sm);
    padding: 0.5rem 1rem;
    background: white;
    transition: var(--transition);
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 46px;
    color: var(--dark-text);
    font-size: 0.95rem;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 46px;
    right: 10px;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
}

/* Grid spacing */
.row.g-3 > [class*="col-"] {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
}

/* Buttons */
.btn-primary {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #2980b9 100%);
    border: none;
    border-radius: var(--radius-sm);
    padding: 1rem 2.5rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: var(--transition);
    font-size: 1rem;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2980b9 0%, var(--secondary-color) 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary i {
    margin-right: 8px;
}

.btn-outline-primary, .btn-outline-success {
    border: 2px solid;
    border-radius: var(--radius-sm);
    font-weight: 500;
    transition: var(--transition);
    padding: 0.5rem 1.5rem;
}

.btn-outline-primary {
    border-color: var(--secondary-color);
    color: var(--secondary-color);
}

.btn-outline-success {
    border-color: var(--accent-color);
    color: var(--accent-color);
}

.btn-outline-primary:hover, .btn-outline-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.btn-outline-primary:hover {
    background: var(--secondary-color);
    color: white;
}

.btn-outline-success:hover {
    background: var(--accent-color);
    color: white;
}

/* Alerts */
.alert {
    border: none;
    border-radius: var(--radius-md);
    padding: 1.25rem 1.5rem;
    box-shadow: var(--shadow-light);
    border-left: 4px solid;
    position: relative;
    overflow: hidden;
}

.alert::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: inherit;
    opacity: 0.05;
    z-index: 0;
}

.alert > * {
    position: relative;
    z-index: 1;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-left-color: #28a745;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-left-color: #dc3545;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-left-color: var(--secondary-color);
    border: 1px solid rgba(52, 152, 219, 0.1);
}

.alert-info h6 {
    color: var(--primary-color);
    font-weight: 600;
}

.alert-info h6 i {
    color: var(--secondary-color);
    margin-right: 8px;
}

.alert-info ul {
    list-style: none;
    padding-left: 0;
}

.alert-info ul li {
    padding: 0.25rem 0;
    color: var(--dark-text);
    position: relative;
    padding-left: 1.5rem;
}

.alert-info ul li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: var(--accent-color);
    font-weight: bold;
}

/* File upload area */
input[type="file"] {
    border: 2px dashed var(--border-color);
    border-radius: var(--radius-sm);
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: var(--transition);
    cursor: pointer;
}

input[type="file"]:hover {
    border-color: var(--secondary-color);
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}

input[type="file"]::file-selector-button {
    background: var(--secondary-color);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    margin-right: 1rem;
    transition: var(--transition);
    font-weight: 500;
}

input[type="file"]::file-selector-button:hover {
    background: #2980b9;
    transform: translateY(-1px);
}

small.text-muted {
    color: #95a5a6 !important;
    font-size: 0.85rem;
    margin-top: 0.25rem;
    display: block;
}

/* Spacing between sections */
.mt-4 {
    margin-top: 2.5rem !important;
}

/* Submit button container */
.d-grid.gap-2 {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem;
    }
    
    .card-header {
        padding: 1.5rem;
    }
    
    .container-fluid {
        padding: 1rem;
    }
}

/* Animation for form elements */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.col-12, .col-md-6, .col-md-4, .col-md-3, .col-md-2 {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

/* Delay animations for better visual flow */
.col-md-6 { animation-delay: 0.1s; }
.col-md-3 { animation-delay: 0.2s; }
.col-md-4 { animation-delay: 0.3s; }
.col-md-2 { animation-delay: 0.4s; }

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--light-bg);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: var(--secondary-color);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #2980b9;
}
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-graduate"></i> ĐĂNG KÝ TUYỂN SINH
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['success']) && isset($_SESSION['new_maHoSo'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; ?>
                        <div class="mt-2">
                            <a href="index.php?controller=tuyensinh&action=xemhoso&maHoSo=<?php echo $_SESSION['new_maHoSo']; ?>" 
                            class="btn btn-sm btn-outline-success">
                                <i class="fas fa-eye"></i> Xem hồ sơ đã đăng ký
                            </a>
                            <a href="index.php?controller=tuyensinh&action=hosocuatoi" 
                            class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-list"></i> Danh sách hồ sơ của tôi
                            </a>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php 
                    unset($_SESSION['success']);
                    unset($_SESSION['new_maHoSo']);
                    endif; 
                    ?>

                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" class="row g-3" id="formTuyenSinh" enctype="multipart/form-data">
                        <!-- THÔNG TIN CÁ NHÂN HỌC SINH -->
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-user"></i> THÔNG TIN CÁ NHÂN HỌC SINH
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="hoTen" class="form-control" value="<?php echo $_POST['hoTen'] ?? ''; ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                            <select name="gioiTinh" class="form-select" required>
                                <option value="NAM" <?php echo ($_POST['gioiTinh'] ?? 'NAM') === 'NAM' ? 'selected' : ''; ?>>Nam</option>
                                <option value="NU" <?php echo ($_POST['gioiTinh'] ?? '') === 'NU' ? 'selected' : ''; ?>>Nữ</option>
                                <option value="KHAC" <?php echo ($_POST['gioiTinh'] ?? '') === 'KHAC' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                            <input type="date" name="ngaySinh" class="form-control" value="<?php echo $_POST['ngaySinh'] ?? ''; ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nơi sinh</label>
                            <input type="text" name="noiSinh" class="form-control" value="<?php echo $_POST['noiSinh'] ?? ''; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Dân tộc</label>
                            <input type="text" name="danToc" class="form-control" value="<?php echo $_POST['danToc'] ?? 'Kinh'; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tôn giáo</label>
                            <input type="text" name="tonGiao" class="form-control" value="<?php echo $_POST['tonGiao'] ?? 'Không'; ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Quốc tịch</label>
                            <input type="text" name="quocTich" class="form-control" value="<?php echo $_POST['quocTich'] ?? 'Việt Nam'; ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">SĐT học sinh <span class="text-danger">*</span></label>
                            <input type="tel" name="soDienThoaiHocSinh" class="form-control" value="<?php echo $_POST['soDienThoaiHocSinh'] ?? ''; ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $_POST['email'] ?? ''; ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">SĐT phụ huynh <span class="text-danger">*</span></label>
                            <input type="tel" name="soDienThoaiPhuHuynh" class="form-control" value="<?php echo $_POST['soDienThoaiPhuHuynh'] ?? ''; ?>" required>
                        </div>

                        <!-- Địa chỉ thường trú -->
                        <div class="col-12">
                            <label class="form-label">Địa chỉ thường trú <span class="text-danger">*</span></label>
                            
                            <!-- Tỉnh/Thành phố -->
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label class="form-label">Tỉnh/Thành phố</label>
                                    <select name="tinhThanh" id="tinhThanh" class="form-select select2-address" required>
                                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                    </select>
                                </div>
                                
                                <!-- Quận/Huyện -->
                                <div class="col-md-4">
                                    <label class="form-label">Quận/Huyện</label>
                                    <select name="quanHuyen" id="quanHuyen" class="form-select select2-address" disabled required>
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                    </select>
                                </div>
                                
                                <!-- Xã/Phường/Thị trấn -->
                                <div class="col-md-4">
                                    <label class="form-label">Xã/Phường/Thị trấn</label>
                                    <select name="xaPhuong" id="xaPhuong" class="form-select select2-address" disabled required>
                                        <option value="">-- Chọn Xã/Phường --</option>
                                    </select>
                                </div>
                            </div>
                            
                      <!-- Địa chỉ chi tiết -->
<div class="row">
    <div class="col-12">
        <label class="form-label">Địa chỉ chi tiết (Số nhà, tên đường...)</label>
        <input type="text" name="diaChiChiTiet" id="diaChiChiTiet" class="form-control" value="<?php echo $_POST['diaChiChiTiet'] ?? ''; ?>">
    </div>
</div>

<!-- Input ẩn để lưu địa chỉ đầy đủ -->
<input type="hidden" name="diaChiThuongTru" id="diaChiThuongTru" value="<?php echo $_POST['diaChiThuongTru'] ?? ''; ?>">
</div>

<div class="col-12">
    <label class="form-label">Nơi ở hiện nay (nếu khác)</label>
    <input type="text" name="noiOHienNay" class="form-control" value="<?php echo $_POST['noiOHienNay'] ?? ''; ?>">
</div>
                        <!-- THÔNG TIN CHA -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-male"></i> THÔNG TIN CHA
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Họ tên cha</label>
                            <input type="text" name="hoTenCha" class="form-control" value="<?php echo $_POST['hoTenCha'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Nghề nghiệp</label>
                            <input type="text" name="ngheNghiepCha" class="form-control" value="<?php echo $_POST['ngheNghiepCha'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">SĐT cha</label>
                            <input type="tel" name="dienThoaiCha" class="form-control" value="<?php echo $_POST['dienThoaiCha'] ?? ''; ?>">
                        </div>

                        <!-- THÔNG TIN MẸ -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-female"></i> THÔNG TIN MẸ
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Họ tên mẹ</label>
                            <input type="text" name="hoTenMe" class="form-control" value="<?php echo $_POST['hoTenMe'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Nghề nghiệp</label>
                            <input type="text" name="ngheNghiepMe" class="form-control" value="<?php echo $_POST['ngheNghiepMe'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">SĐT mẹ</label>
                            <input type="tel" name="dienThoaiMe" class="form-control" value="<?php echo $_POST['dienThoaiMe'] ?? ''; ?>">
                        </div>

                        <!-- THÔNG TIN NGƯỜI GIÁM HỘ (Nếu có) -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-user-shield"></i> THÔNG TIN NGƯỜI GIÁM HỘ (Nếu khác cha/mẹ)
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Họ tên người giám hộ</label>
                            <input type="text" name="hoTenNguoiGiamHo" class="form-control" value="<?php echo $_POST['hoTenNguoiGiamHo'] ?? ''; ?>">
                        </div>

                       

                        <div class="col-md-3">
                            <label class="form-label">Nghề nghiệp</label>
                            <input type="text" name="ngheNghiepNguoiGiamHo" class="form-control" value="<?php echo $_POST['ngheNghiepNguoiGiamHo'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">SĐT người giám hộ</label>
                            <input type="tel" name="dienThoaiNguoiGiamHo" class="form-control" value="<?php echo $_POST['dienThoaiNguoiGiamHo'] ?? ''; ?>">
                        </div>

                       

                        <!-- THÔNG TIN HỌC TẬP -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-graduation-cap"></i> THÔNG TIN HỌC TẬP
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Trường THCS</label>
                            <input type="text" name="truongTHCS" class="form-control" value="<?php echo $_POST['truongTHCS'] ?? ''; ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Địa chỉ trường THCS</label>
                            <input type="text" name="diaChiTruongTHCS" class="form-control" value="<?php echo $_POST['diaChiTruongTHCS'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Năm tốt nghiệp</label>
                            <input type="number" name="namTotNghiep" class="form-control" min="2020" max="2025" value="<?php echo $_POST['namTotNghiep'] ?? '2024'; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Xếp loại học lực</label>
                            <select name="xepLoaiHocLuc" class="form-select">
                                <option value="">-- Chọn --</option>
                                <option value="GIOI" <?php echo ($_POST['xepLoaiHocLuc'] ?? '') === 'GIOI' ? 'selected' : ''; ?>>Giỏi</option>
                                <option value="KHA" <?php echo ($_POST['xepLoaiHocLuc'] ?? '') === 'KHA' ? 'selected' : ''; ?>>Khá</option>
                                <option value="TRUNG_BINH" <?php echo ($_POST['xepLoaiHocLuc'] ?? '') === 'TRUNG_BINH' ? 'selected' : ''; ?>>Trung bình</option>
                                <option value="YEU" <?php echo ($_POST['xepLoaiHocLuc'] ?? '') === 'YEU' ? 'selected' : ''; ?>>Yếu</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Xếp loại hạnh kiểm</label>
                            <select name="xepLoaiHanhKiem" class="form-select">
                                <option value="">-- Chọn --</option>
                                <option value="TOT" <?php echo ($_POST['xepLoaiHanhKiem'] ?? '') === 'TOT' ? 'selected' : ''; ?>>Tốt</option>
                                <option value="KHA" <?php echo ($_POST['xepLoaiHanhKiem'] ?? '') === 'KHA' ? 'selected' : ''; ?>>Khá</option>
                                <option value="TRUNG_BINH" <?php echo ($_POST['xepLoaiHanhKiem'] ?? '') === 'TRUNG_BINH' ? 'selected' : ''; ?>>Trung bình</option>
                                <option value="YEU" <?php echo ($_POST['xepLoaiHanhKiem'] ?? '') === 'YEU' ? 'selected' : ''; ?>>Yếu</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Điểm TB lớp 9</label>
                            <input type="number" name="diemTB_Lop9" class="form-control" min="0" max="10" step="0.1" value="<?php echo $_POST['diemTB_Lop9'] ?? ''; ?>">
                        </div>

                        <!-- NGUYỆN VỌNG TUYỂN SINH -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-list-alt"></i> NGUYỆN VỌNG TUYỂN SINH
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Hình thức tuyển sinh</label>
                            <select name="hinhThucTuyenSinh" class="form-select">
                                <option value="XET_TUYEN" <?php echo ($_POST['hinhThucTuyenSinh'] ?? 'XET_TUYEN') === 'XET_TUYEN' ? 'selected' : ''; ?>>Xét tuyển</option>
                                <option value="THI_TUYEN" <?php echo ($_POST['hinhThucTuyenSinh'] ?? '') === 'THI_TUYEN' ? 'selected' : ''; ?>>Thi tuyển</option>
                                <option value="KET_HOP" <?php echo ($_POST['hinhThucTuyenSinh'] ?? '') === 'KET_HOP' ? 'selected' : ''; ?>>Kết hợp</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 1</label>
                            <input type="text" name="nguyenVong1" class="form-control" value="<?php echo $_POST['nguyenVong1'] ?? ''; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 2</label>
                            <input type="text" name="nguyenVong2" class="form-control" value="<?php echo $_POST['nguyenVong2'] ?? ''; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 3</label>
                            <input type="text" name="nguyenVong3" class="form-control" value="<?php echo $_POST['nguyenVong3'] ?? ''; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 4</label>
                            <input type="text" name="nguyenVong4" class="form-control" value="<?php echo $_POST['nguyenVong4'] ?? ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 5</label>
                            <input type="text" name="nguyenVong5" class="form-control" value="<?php echo $_POST['nguyenVong5'] ?? ''; ?>">
                        </div>

                        <!-- THÔNG TIN HỒ SƠ ĐÍNH KÈM -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-file-upload"></i> HỒ SƠ ĐÍNH KÈM
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bản sao giấy khai sinh</label>
                            <input type="file" name="banSaoGiayKhaiSinh" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file PDF, JPG, PNG (tối đa 5MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bản sao hộ khẩu</label>
                            <input type="file" name="banSaoHoKhau" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file PDF, JPG, PNG (tối đa 5MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Học bạ THCS</label>
                            <input type="file" name="hocBaTHCS" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file PDF, JPG, PNG (tối đa 5MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Giấy chứng nhận tốt nghiệp</label>
                            <input type="file" name="giayChungNhanTotNghiep" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file PDF, JPG, PNG (tối đa 5MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ảnh 3x4</label>
                            <input type="file" name="anh34" class="form-control" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file JPG, PNG (tối đa 2MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Giấy xác nhận ưu tiên (nếu có)</label>
                            <input type="file" name="giayXacNhanUuTien" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file PDF, JPG, PNG (tối đa 5MB)</small>
                        </div>

                        <!-- THÔNG BÁO QUAN TRỌNG -->
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Thông tin quan trọng:</h6>
                                <ul class="mb-0">
                                    <li>Các trường có dấu <span class="text-danger">*</span> là bắt buộc</li>
                                    <li>Hồ sơ sẽ được xét duyệt trong vòng 3-5 ngày làm việc</li>
                                    <li>Thí sinh cần chuẩn bị đầy đủ hồ sơ gốc khi đến nhập học</li>
                                    <li>Mọi thông tin cần được điền chính xác theo giấy tờ tùy thân</li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane"></i> Gửi đăng ký
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Gọi API lấy toàn bộ dữ liệu tỉnh/quan/phuong
    fetch("https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json")
        .then(res => res.json())
        .then(data => {
            let tinh = document.getElementById("tinhThanh");
            let quan = document.getElementById("quanHuyen");
            let xa   = document.getElementById("xaPhuong");

            // -----------------------------
            // 1. Load TỈNH / THÀNH PHỐ
            // -----------------------------
            data.forEach(item => {
                tinh.innerHTML += `<option value="${item.Id}">${item.Name}</option>`;
            });

            // -----------------------------
            // 2. Khi chọn TỈNH → load QUẬN
            // -----------------------------
            tinh.addEventListener("change", function () {
                let idTinh = this.value;
                quan.innerHTML = `<option value="">-- Chọn Quận/Huyện --</option>`;
                xa.innerHTML = `<option value="">-- Chọn Xã/Phường --</option>`;
                quan.disabled = true;
                xa.disabled = true;

                if (!idTinh) return;

                let dsQuan = data.find(t => t.Id == idTinh)?.Districts || [];

                dsQuan.forEach(q => {
                    quan.innerHTML += `<option value="${q.Id}">${q.Name}</option>`;
                });

                quan.disabled = false;
            });

            // -----------------------------
            // 3. Khi chọn QUẬN → load XÃ
            // -----------------------------
            quan.addEventListener("change", function () {
                let idTinh  = tinh.value;
                let idQuan  = this.value;

                xa.innerHTML = `<option value="">-- Chọn Xã/Phường --</option>`;
                xa.disabled = true;

                if (!idQuan) return;

                let dsXa =
                    data.find(t => t.Id == idTinh)?.Districts
                        .find(d => d.Id == idQuan)?.Wards || [];

                dsXa.forEach(x => {
                    xa.innerHTML += `<option value="${x.Id}">${x.Name}</option>`;
                });

                xa.disabled = false;
            });
        });// CHỈ CẦN DÒNG NÀY
$('#formTuyenSinh').on('submit', function(e) {
    updateFullAddress();
    return true;
});// Trong view, thêm đoạn này:
$('#formTuyenSinh').on('submit', function(e) {
    // BUỘC cập nhật địa chỉ trước
    updateFullAddress();
    
    // Kiểm tra xem đã có địa chỉ chưa
    if (!$('#diaChiThuongTru').val()) {
        alert('Vui lòng chọn đầy đủ thông tin địa chỉ!');
        e.preventDefault();
        return false;
    }
    
    return true;
});

// Và đảm bảo updateFullAddress() hoạt động đúng:
function updateFullAddress() {
    const tinhId = $('#tinhThanh').val();
    const quanId = $('#quanHuyen').val();
    const xaId = $('#xaPhuong').val();
    const chiTiet = $('#diaChiChiTiet').val();
    
    // Lấy tên từ select (option text)
    const tinhText = $('#tinhThanh option:selected').text();
    const quanText = $('#quanHuyen option:selected').text();
    const xaText = $('#xaPhuong option:selected').text();
    
    let diaChiArray = [];
    if (chiTiet) diaChiArray.push(chiTiet);
    if (xaText && xaText !== '-- Chọn Xã/Phường --') diaChiArray.push(xaText);
    if (quanText && quanText !== '-- Chọn Quận/Huyện --') diaChiArray.push(quanText);
    if (tinhText && tinhText !== '-- Chọn Tỉnh/Thành phố --') diaChiArray.push(tinhText);
    
    const fullAddress = diaChiArray.join(', ');
    $('#diaChiThuongTru').val(fullAddress);
    
    console.log('Địa chỉ đầy đủ:', fullAddress);
}
});
</script>
