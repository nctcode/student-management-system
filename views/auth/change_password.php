<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: index.php?controller=auth&action=login");
    exit;
}

// Lấy thông tin user
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu - Hệ thống QLHS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --sidebar-width: 250px;
        }

        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            min-height: 100vh;
        }

        .auth-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .change-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            border: none;
        }

        .change-header {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .change-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 28px;
        }

        .change-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
            font-size: 20px;
            font-weight: bold;
        }

        .user-details h5 {
            margin: 0;
            font-size: 16px;
        }

        .user-details small {
            opacity: 0.8;
        }

        .change-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 10px;
            color: var(--primary-color);
            width: 20px;
        }

        .form-control {
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
            height: 50px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        .password-strength {
            height: 5px;
            background: #e1e5eb;
            border-radius: 5px;
            margin-top: 5px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            background: var(--danger-color);
            transition: width 0.3s, background 0.3s;
        }

        .form-text {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }

        .btn-change {
            background: linear-gradient(135deg, var(--primary-color), #2980b9);
            border: none;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-change:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-change:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e1e5eb;
        }

        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .back-link a i {
            margin-right: 8px;
        }

        .alert-message {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            border: none;
            display: flex;
            align-items: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid var(--warning-color);
        }

        .alert-message i {
            margin-right: 10px;
            font-size: 20px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
        }

        .password-input {
            position: relative;
        }

        @media (max-width: 576px) {
            .change-card {
                margin: 10px;
            }
            
            .change-header {
                padding: 20px;
            }
            
            .change-body {
                padding: 20px;
            }
            
            .change-header h2 {
                font-size: 24px;
            }
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
            text-transform: uppercase;
        }

        .role-admin {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .role-teacher {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .role-student {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
        }

        .role-parent {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="change-card">
            <!-- Header -->
            <div class="change-header">
                <h2><i class="fas fa-key"></i> Đổi Mật Khẩu</h2>
                <p>Bảo mật tài khoản của bạn</p>
                
                <!-- User Info -->
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['hoTen'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <h5><?php echo htmlspecialchars($user['hoTen']); ?></h5>
                        <small>
                            <?php echo htmlspecialchars($user['tenDangNhap']); ?>
                            <span class="role-badge role-<?php echo strtolower($user['vaiTro']); ?>">
                                <?php 
                                $roleNames = [
                                    'QTV' => 'Quản trị viên',
                                    'GIAOVIEN' => 'Giáo viên',
                                    'HOCSINH' => 'Học sinh',
                                    'PHUHUYNH' => 'Phụ huynh',
                                    'BGH' => 'Ban giám hiệu',
                                    'TOTRUONG' => 'Tổ trưởng'
                                ];
                                echo $roleNames[$user['vaiTro']] ?? $user['vaiTro'];
                                ?>
                            </span>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="change-body">
                <!-- Messages -->
                <?php if (!empty($_SESSION['message'])): ?>
                    <div class="alert-message alert-<?php 
                        echo (strpos($_SESSION['message'], '❌') !== false || strpos($_SESSION['message'], 'lỗi') !== false) ? 'danger' : 
                             (strpos($_SESSION['message'], '⚠️') !== false ? 'warning' : 'success'); 
                    ?>">
                        <i class="fas fa-<?php 
                            echo (strpos($_SESSION['message'], '❌') !== false || strpos($_SESSION['message'], 'lỗi') !== false) ? 'exclamation-circle' : 
                                 (strpos($_SESSION['message'], '⚠️') !== false ? 'exclamation-triangle' : 'check-circle'); 
                        ?>"></i>
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>

                <!-- Password Change Form -->
                <form method="POST" action="index.php?controller=auth&action=changePassword" id="changePasswordForm">
                    <!-- Old Password -->
                    <div class="form-group">
                        <label class="form-label" for="old_password">
                            <i class="fas fa-lock"></i> Mật khẩu hiện tại
                        </label>
                        <div class="password-input">
                            <input type="password" 
                                   class="form-control" 
                                   id="old_password" 
                                   name="old_password" 
                                   required 
                                   placeholder="Nhập mật khẩu hiện tại">
                            <button type="button" class="password-toggle" onclick="togglePassword('old_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="form-group">
                        <label class="form-label" for="new_password">
                            <i class="fas fa-key"></i> Mật khẩu mới
                        </label>
                        <div class="password-input">
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password" 
                                   name="new_password" 
                                   required 
                                   minlength="6"
                                   placeholder="Nhập mật khẩu mới (ít nhất 6 ký tự)"
                                   oninput="checkPasswordStrength(this.value)">
                            <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="passwordStrengthBar"></div>
                        </div>
                        <small class="form-text">
                            <i class="fas fa-info-circle"></i> Mật khẩu phải có ít nhất 6 ký tự, bao gồm chữ và số
                        </small>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">
                            <i class="fas fa-check-circle"></i> Xác nhận mật khẩu mới
                        </label>
                        <div class="password-input">
                            <input type="password" 
                                   class="form-control" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required 
                                   placeholder="Nhập lại mật khẩu mới"
                                   oninput="checkPasswordMatch()">
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="form-text" id="passwordMatchText"></small>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-change">
                        <i class="fas fa-sync-alt"></i> Cập nhật mật khẩu
                    </button>
                </form>

                <!-- Back Link -->
                <div class="back-link">
                    <a href="index.php">
                        <i class="fas fa-arrow-left"></i> Quay lại trang chủ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
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

        // Check password strength
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('passwordStrengthBar');
            let strength = 0;
            
            if (password.length >= 6) strength += 20;
            if (password.length >= 8) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;
            
            strengthBar.style.width = strength + '%';
            
            if (strength < 40) {
                strengthBar.style.backgroundColor = '#e74c3c'; // Red
            } else if (strength < 80) {
                strengthBar.style.backgroundColor = '#f39c12'; // Orange
            } else {
                strengthBar.style.backgroundColor = '#2ecc71'; // Green
            }
        }

        // Check password match
        function checkPasswordMatch() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchText = document.getElementById('passwordMatchText');
            
            if (confirmPassword === '') {
                matchText.innerHTML = '';
                matchText.style.color = '';
            } else if (password === confirmPassword) {
                matchText.innerHTML = '<i class="fas fa-check"></i> Mật khẩu khớp';
                matchText.style.color = '#2ecc71';
            } else {
                matchText.innerHTML = '<i class="fas fa-times"></i> Mật khẩu không khớp';
                matchText.style.color = '#e74c3c';
            }
        }

        // Form validation
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            const oldPassword = document.getElementById('old_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword.length < 6) {
                e.preventDefault();
                alert('Mật khẩu mới phải có ít nhất 6 ký tự!');
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                return false;
            }
            
            if (oldPassword === newPassword) {
                e.preventDefault();
                alert('Mật khẩu mới không được trùng với mật khẩu cũ!');
                return false;
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Focus on old password field
            document.getElementById('old_password').focus();
        });
    </script>
</body>
</html>