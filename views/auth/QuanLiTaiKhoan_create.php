<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/admin.php';
?>

<!-- Content wrapper để tránh sidebar -->
<div class="content-wrapper">
    <div class="container py-4">
        <!-- Card chính -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>Thêm tài khoản mới
                    </h3>
                    <a href="index.php?controller=QuanLyTaiKhoan&action=index" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="index.php?controller=QuanLyTaiKhoan&action=store" method="post">
                    <!-- Thông tin đăng nhập -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-key me-2"></i>Thông tin đăng nhập
                            </h5>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tenDangNhap" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" id="tenDangNhap" name="tenDangNhap" class="form-control" required 
                                   placeholder="Nhập tên đăng nhập" value="<?= $_POST['tenDangNhap'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="matKhau" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" id="matKhau" name="matKhau" class="form-control" required 
                                   placeholder="Nhập mật khẩu">
                        </div>
                    </div>

                    <!-- Thông tin cá nhân -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-user me-2"></i>Thông tin cá nhân
                            </h5>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="hoTen" class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" id="hoTen" name="hoTen" class="form-control" required 
                                   placeholder="Nhập họ tên đầy đủ" value="<?= $_POST['hoTen'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   placeholder="example@email.com" value="<?= $_POST['email'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="soDienThoai" class="form-label">Số điện thoại</label>
                            <input type="text" id="soDienThoai" name="soDienThoai" class="form-control" 
                                   placeholder="Nhập số điện thoại" value="<?= $_POST['soDienThoai'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="vaiTro" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select id="vaiTro" name="vaiTro" class="form-select" required onchange="showRoleFields()">
                                <option value="">-- Chọn vai trò --</option>
                                <option value="QTV" <?= (($_POST['vaiTro'] ?? '') == 'QTV') ? 'selected' : '' ?>>Quản trị viên</option>
                                <option value="BGH" <?= (($_POST['vaiTro'] ?? '') == 'BGH') ? 'selected' : '' ?>>Ban giám hiệu</option>
                                <option value="GIAOVIEN" <?= (($_POST['vaiTro'] ?? '') == 'GIAOVIEN') ? 'selected' : '' ?>>Giáo viên</option>
                                <option value="HOCSINH" <?= (($_POST['vaiTro'] ?? '') == 'HOCSINH') ? 'selected' : '' ?>>Học sinh</option>
                                <option value="PHUHUYNH" <?= (($_POST['vaiTro'] ?? '') == 'PHUHUYNH') ? 'selected' : '' ?>>Phụ huynh</option>
                                <option value="TOTRUONG" <?= (($_POST['vaiTro'] ?? '') == 'TOTRUONG') ? 'selected' : '' ?>>Tổ trưởng</option>
                            </select>
                        </div>
                    </div>

                    <!-- Thông tin giáo viên -->
                    <div id="giaovienFields" class="role-section" style="display: none;">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3 text-success">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>Thông tin giáo viên
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="chuyenMon" class="form-label">Chuyên môn</label>
                                <input type="text" id="chuyenMon" name="chuyenMon" class="form-control" 
                                       placeholder="Ví dụ: Toán, Văn, Anh..." value="<?= $_POST['chuyenMon'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="loaiGiaoVien" class="form-label">Loại giáo viên</label>
                                <select id="loaiGiaoVien" name="loaiGiaoVien" class="form-select">
                                    <option value="">-- Chọn loại giáo viên --</option>
                                    <option value="GV_BO_MON" <?= (($_POST['loaiGiaoVien'] ?? '') == 'GV_BO_MON') ? 'selected' : '' ?>>Giáo viên bộ môn</option>
                                    <option value="GV_CHU_NHIEM" <?= (($_POST['loaiGiaoVien'] ?? '') == 'GV_CHU_NHIEM') ? 'selected' : '' ?>>Giáo viên chủ nhiệm</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="maToTruong" class="form-label">Mã tổ trưởng (nếu có)</label>
                                <input type="number" id="maToTruong" name="maToTruong" class="form-control" 
                                       placeholder="Để trống nếu không có" value="<?= $_POST['maToTruong'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="maMonHoc" class="form-label">Mã môn học</label>
                                <input type="number" id="maMonHoc" name="maMonHoc" class="form-control" 
                                       placeholder="Nhập mã môn học" value="<?= $_POST['maMonHoc'] ?? '' ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin học sinh -->
                    <div id="hocsinhFields" class="role-section" style="display: none;">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3 text-warning">
                                    <i class="fas fa-user-graduate me-2"></i>Thông tin học sinh
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="maLop" class="form-label">Mã lớp</label>
                                <input type="number" id="maLop" name="maLop" class="form-control" 
                                       placeholder="Nhập mã lớp" value="<?= $_POST['maLop'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="maPhuHuynh" class="form-label">Mã phụ huynh</label>
                                <input type="number" id="maPhuHuynh" name="maPhuHuynh" class="form-control" 
                                       placeholder="Nhập mã phụ huynh" value="<?= $_POST['maPhuHuynh'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="maHoSo" class="form-label">Mã hồ sơ</label>
                                <input type="number" id="maHoSo" name="maHoSo" class="form-control" 
                                       placeholder="Nhập mã hồ sơ" value="<?= $_POST['maHoSo'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="ngayNhapHoc" class="form-label">Ngày nhập học</label>
                                <input type="date" id="ngayNhapHoc" name="ngayNhapHoc" class="form-control" 
                                       value="<?= $_POST['ngayNhapHoc'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="trangThai" class="form-label">Trạng thái</label>
                                <select id="trangThai" name="trangThai" class="form-select">
                                    <option value="DANG_HOC" <?= (($_POST['trangThai'] ?? '') == 'DANG_HOC') ? 'selected' : '' ?>>Đang học</option>
                                    <option value="DA_TOT_NGHIEP" <?= (($_POST['trangThai'] ?? '') == 'DA_TOT_NGHIEP') ? 'selected' : '' ?>>Đã tốt nghiệp</option>
                                    <option value="CHUYEN_TRUONG" <?= (($_POST['trangThai'] ?? '') == 'CHUYEN_TRUONG') ? 'selected' : '' ?>>Chuyển trường</option>
                                    <option value="THOI_HOC" <?= (($_POST['trangThai'] ?? '') == 'THOI_HOC') ? 'selected' : '' ?>>Thôi học</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin phụ huynh -->
                    <div id="phuhuynhFields" class="role-section" style="display: none;">
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mb-3 text-info">
                                    <i class="fas fa-users me-2"></i>Thông tin phụ huynh
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="ngheNghiep" class="form-label">Nghề nghiệp</label>
                                <input type="text" id="ngheNghiep" name="ngheNghiep" class="form-control" 
                                       placeholder="Ví dụ: Kỹ sư, Bác sĩ, Giáo viên..." value="<?= $_POST['ngheNghiep'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="moiQuanHe" class="form-label">Mối quan hệ</label>
                                <select id="moiQuanHe" name="moiQuanHe" class="form-select">
                                    <option value="">-- Chọn mối quan hệ --</option>
                                    <option value="CHA" <?= (($_POST['moiQuanHe'] ?? '') == 'CHA') ? 'selected' : '' ?>>Cha</option>
                                    <option value="ME" <?= (($_POST['moiQuanHe'] ?? '') == 'ME') ? 'selected' : '' ?>>Mẹ</option>
                                    <option value="NGUOI_GIAM_HO" <?= (($_POST['moiQuanHe'] ?? '') == 'NGUOI_GIAM_HO') ? 'selected' : '' ?>>Người giám hộ</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Trạng thái tài khoản -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-cog me-2"></i>Cài đặt tài khoản
                            </h5>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="trangThaiTaiKhoan" class="form-label">Trạng thái tài khoản</label>
                            <select id="trangThaiTaiKhoan" name="trangThaiTaiKhoan" class="form-select">
                                <option value="HOAT_DONG" selected>Hoạt động</option>
                                <option value="KHOA">Khóa</option>
                            </select>
                        </div>
                    </div>

                    <!-- Nút submit -->
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-save me-2"></i>Tạo tài khoản
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="card-footer text-center">
                <a href="index.php?controller=QuanLyTaiKhoan&action=index" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách tài khoản
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Content wrapper để tránh sidebar */
.content-wrapper {
    margin-left: 250px;
    min-height: 100vh;
    background-color: #f5f7fb;
    transition: margin-left 0.3s;
}

@media (max-width: 768px) {
    .content-wrapper {
        margin-left: 0;
    }
}

/* Card styling */
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    padding: 15px 25px;
}

.card-title {
    font-weight: 600;
}

/* Role section styling */
.role-section {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid #007bff;
    animation: fadeIn 0.5s ease-out;
}

#giaovienFields {
    border-left-color: #28a745;
}

#hocsinhFields {
    border-left-color: #ffc107;
}

#phuhuynhFields {
    border-left-color: #17a2b8;
}

/* Form controls */
.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-label {
    font-weight: 500;
    margin-bottom: 8px;
    color: #495057;
}

.text-danger {
    color: #dc3545 !important;
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Button styling */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 12px 24px;
    font-weight: 500;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Border bottom for section headers */
.border-bottom {
    border-color: #dee2e6 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container {
        padding: 15px;
    }
    
    .card-header {
        padding: 15px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    .role-section {
        padding: 15px;
    }
}
</style>

<script>
function showRoleFields() {
    // Ẩn tất cả các section role
    const roleSections = document.querySelectorAll('.role-section');
    roleSections.forEach(section => {
        section.style.display = 'none';
    });
    
    // Hiện section tương ứng với vai trò được chọn
    const role = document.getElementById('vaiTro').value;
    let targetSection = null;
    
    switch(role) {
        case 'GIAOVIEN':
            targetSection = document.getElementById('giaovienFields');
            break;
        case 'HOCSINH':
            targetSection = document.getElementById('hocsinhFields');
            break;
        case 'PHUHUYNH':
            targetSection = document.getElementById('phuhuynhFields');
            break;
        case 'TOTRUONG':
            // Bạn có thể thêm section cho tổ trưởng nếu cần
            break;
        // QTV và BGH không cần thêm thông tin
    }
    
    if (targetSection) {
        targetSection.style.display = 'block';
    }
}

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    showRoleFields();
    
    // Thêm sự kiện change cho select vai trò
    document.getElementById('vaiTro').addEventListener('change', showRoleFields);
    
    // Xử lý giữ giá trị khi submit fail
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');
    if (action === 'create' || action === 'store') {
        // Form đã submit và quay lại, giữ lại giá trị
    }
});
</script>

<?php
require_once 'views/layouts/footer.php';
?>