<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem biến $user có được truyền từ controller không
if (!isset($user) || empty($user)) {
    $_SESSION['error'] = 'Lỗi: Không có dữ liệu tài khoản!';
    header('Location: index.php?controller=QuanLyTaiKhoan&action=index');
    exit;
}

// Lấy thông tin user từ session để hiển thị sidebar đúng
$currentUserRole = $_SESSION['user']['vaiTro'] ?? '';
$showSidebar = true;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Tài Khoản - Hệ Thống Quản Lý Học Sinh</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #2e59d9;
            --secondary: #858796;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        #wrapper {
            display: flex;
            width: 100%;
        }
        
        #content-wrapper {
            width: 100%;
            overflow-x: hidden;
            margin-left: 0;
            transition: margin-left 0.3s;
        }
        
        .container-fluid {
            padding: 20px;
        }
        
        @media (min-width: 768px) {
            #content-wrapper {
                margin-left: 250px; /* Chiều rộng sidebar */
            }
        }
        
        .card {
            border-radius: 10px;
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: var(--primary);
            color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.35rem;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .info-display {
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
        }
        
        .password-section {
            background-color: #f8f9fc;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary);
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .progress {
            height: 5px;
        }
        
        .required-field::after {
            content: " *";
            color: var(--danger);
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }
        
        .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: var(--secondary);
        }
        
        /* Page Heading */
        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }
        
        /* Alert */
        .alert {
            border-radius: 0.5rem;
            border: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .page-heading {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .page-title {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar (đã được include từ controller) -->
        
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar (nếu có) -->
                
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="page-heading">
                        <h1 class="page-title">
                            <i class="fas fa-user-edit me-2"></i>Chỉnh Sửa Tài Khoản
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php?controller=home&action=admin"><i class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item"><a href="index.php?controller=QuanLyTaiKhoan&action=index">Quản lý tài khoản</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                            </ol>
                        </nav>
                    </div>
                    
                    <!-- Thông báo -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Main Content -->
                    <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-10">
                            <div class="card shadow">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fas fa-user-cog me-2"></i>Thông tin tài khoản
                                        <span class="badge bg-light text-dark float-end">
                                            ID: #<?php echo htmlspecialchars($user['id'] ?? 'N/A'); ?>
                                        </span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form action="index.php?controller=QuanLyTaiKhoan&action=update&id=<?php echo htmlspecialchars($user['id']); ?>" method="post" id="editUserForm">
                                        
                                        <!-- Thông tin cơ bản -->
                                        <div class="row mb-4">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Tên đăng nhập</label>
                                                <div class="info-display">
                                                    <i class="fas fa-user me-2 text-primary"></i>
                                                    <?php echo htmlspecialchars($user['username']); ?>
                                                </div>
                                                <small class="form-text text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Tên đăng nhập không thể thay đổi
                                                </small>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Trạng thái</label>
                                                <div class="info-display">
                                                    <?php 
                                                    $status = $user['trangThai'] ?? 'HOAT_DONG';
                                                    $statusBadge = $status === 'HOAT_DONG' ? 'success' : 'danger';
                                                    $statusText = $status === 'HOAT_DONG' ? 'Hoạt động' : 'Bị khóa';
                                                    ?>
                                                    <span class="badge bg-<?php echo $statusBadge; ?>">
                                                        <i class="fas fa-<?php echo $status === 'HOAT_DONG' ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                                                        <?php echo $statusText; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Thông tin người dùng liên kết -->
                                        <?php if (isset($user['hoTen']) && !empty($user['hoTen'])): ?>
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <label class="form-label">Người dùng liên kết</label>
                                                <div class="info-display">
                                                    <i class="fas fa-id-card me-2 text-primary"></i>
                                                    <?php echo htmlspecialchars($user['hoTen']); ?>
                                                    <?php if (isset($user['email']) && !empty($user['email'])): ?>
                                                        <span class="ms-3">
                                                            <i class="fas fa-envelope me-1 text-muted"></i>
                                                            <?php echo htmlspecialchars($user['email']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <!-- Phần thay đổi mật khẩu -->
                                        <div class="password-section">
                                            <h6 class="mb-3 text-primary">
                                                <i class="fas fa-key me-2"></i>Thay đổi mật khẩu
                                            </h6>
                                            <div class="alert alert-info mb-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Chỉ điền thông tin bên dưới nếu muốn thay đổi mật khẩu
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                        <input type="password" class="form-control" id="new_password" name="new_password" 
                                                               placeholder="Nhập mật khẩu mới">
                                                        <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                    <small class="form-text text-muted">Để trống nếu không muốn thay đổi</small>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                                               placeholder="Nhập lại mật khẩu mới">
                                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="progress mb-3">
                                                <div class="progress-bar" id="passwordStrengthBar" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <small class="form-text text-muted" id="passwordStrengthText">
                                                Mật khẩu phải có ít nhất 8 ký tự
                                            </small>
                                        </div>
                                        
                                        <!-- Vai trò -->
                                        <div class="mb-4">
                                            <?php if (isset($_SESSION['user']) && $_SESSION['user']['vaiTro'] === 'QTV'): ?>
                                                <label for="vaiTro" class="form-label required-field">Vai trò</label>
                                                <select id="vaiTro" name="vaiTro" class="form-select" required>
                                                    <option value="" disabled>Chọn vai trò</option>
                                                    <option value="QTV" <?php echo ($user['vaiTro'] == 'QTV') ? 'selected' : ''; ?>>Quản trị viên</option>
                                                    <option value="BGH" <?php echo ($user['vaiTro'] == 'BGH') ? 'selected' : ''; ?>>Ban giám hiệu</option>
                                                    <option value="GIAOVIEN" <?php echo ($user['vaiTro'] == 'GIAOVIEN') ? 'selected' : ''; ?>>Giáo viên</option>
                                                    <option value="HOCSINH" <?php echo ($user['vaiTro'] == 'HOCSINH') ? 'selected' : ''; ?>>Học sinh</option>
                                                    <option value="PHUHUYNH" <?php echo ($user['vaiTro'] == 'PHUHUYNH') ? 'selected' : ''; ?>>Phụ huynh</option>
                                                    <option value="TOTRUONG" <?php echo ($user['vaiTro'] == 'TOTRUONG') ? 'selected' : ''; ?>>Tổ trưởng chuyên môn</option>
                                                </select>
                                                <small class="form-text text-muted">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Thay đổi vai trò có thể ảnh hưởng đến quyền truy cập
                                                </small>
                                            <?php else: ?>
                                                <label class="form-label">Vai trò</label>
                                                <div class="info-display">
                                                    <?php 
                                                    $vaiTroText = [
                                                        'QTV' => 'Quản trị viên',
                                                        'BGH' => 'Ban giám hiệu', 
                                                        'GIAOVIEN' => 'Giáo viên',
                                                        'HOCSINH' => 'Học sinh',
                                                        'PHUHUYNH' => 'Phụ huynh',
                                                        'TOTRUONG' => 'Tổ trưởng chuyên môn',
                                                        'USER' => 'Người dùng'
                                                    ];
                                                    $currentRole = $user['vaiTro'];
                                                    $roleBadgeColor = [
                                                        'QTV' => 'danger',
                                                        'BGH' => 'warning',
                                                        'GIAOVIEN' => 'info',
                                                        'HOCSINH' => 'success',
                                                        'PHUHUYNH' => 'primary',
                                                        'TOTRUONG' => 'secondary'
                                                    ];
                                                    ?>
                                                    <span class="badge bg-<?php echo $roleBadgeColor[$currentRole] ?? 'secondary'; ?> p-2">
                                                        <i class="fas fa-user-tag me-1"></i>
                                                        <?php echo $vaiTroText[$currentRole] ?? 'Người dùng'; ?>
                                                    </span>
                                                </div>
                                                <small class="form-text text-muted">
                                                    <i class="fas fa-lock me-1"></i>
                                                    Chỉ Quản trị viên mới có thể thay đổi vai trò
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Nút hành động -->
                                        <div class="d-flex justify-content-between pt-3 border-top">
                                            <a href="index.php?controller=QuanLyTaiKhoan&action=index" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                                            </a>
                                            
                                            <div class="d-flex gap-2">
                                                
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-2"></i>Cập nhật
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer (nếu có) -->
            
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle hiển thị mật khẩu
        document.getElementById('toggleNewPassword').addEventListener('click', function() {
            togglePasswordVisibility('new_password', this);
        });
        
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            togglePasswordVisibility('confirm_password', this);
        });
        
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Kiểm tra độ mạnh mật khẩu
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrengthText');
        
        newPasswordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });
        
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        
        function checkPasswordStrength(password) {
            let strength = 0;
            let text = '';
            let color = '';
            
            if (password.length === 0) {
                strength = 0;
                text = 'Mật khẩu phải có ít nhất 8 ký tự';
                color = '';
            } else if (password.length < 8) {
                strength = 25;
                text = 'Quá ngắn';
                color = 'danger';
            } else {
                strength = 25;
                
                // Kiểm tra độ phức tạp
                if (/[a-z]/.test(password)) strength += 25;
                if (/[A-Z]/.test(password)) strength += 25;
                if (/[0-9]/.test(password)) strength += 25;
                if (/[^A-Za-z0-9]/.test(password)) strength += 25;
                
                if (strength <= 50) {
                    text = 'Yếu';
                    color = 'danger';
                } else if (strength <= 75) {
                    text = 'Trung bình';
                    color = 'warning';
                } else {
                    text = 'Mạnh';
                    color = 'success';
                }
            }
            
            strengthBar.style.width = strength + '%';
            strengthBar.className = 'progress-bar bg-' + color;
            strengthText.textContent = text;
            strengthText.className = 'form-text ' + (color ? 'text-' + color : 'text-muted');
        }
        
        function checkPasswordMatch() {
            const newPass = newPasswordInput.value;
            const confirmPass = confirmPasswordInput.value;
            
            if (newPass && confirmPass) {
                if (newPass === confirmPass) {
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                } else {
                    confirmPasswordInput.classList.remove('is-valid');
                    confirmPasswordInput.classList.add('is-invalid');
                }
            } else {
                confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
            }
        }
        
        // Xác nhận trước khi gửi form
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            const newPass = newPasswordInput.value;
            const confirmPass = confirmPasswordInput.value;
            
            if (newPass && newPass !== confirmPass) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                return false;
            }
            
            if (newPass && newPass.length < 8) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 8 ký tự!');
                return false;
            }
            
            return true;
        });
    });
    </script>
</body>
</html>