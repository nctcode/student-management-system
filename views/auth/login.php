<?php
$title = "Đăng Nhập - QLHS";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --accent: #3498db;
            --text-dark: #2c3e50;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }

        .login-card {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            text-align: center;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-logo {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.3);
        }

        .login-logo i {
            font-size: 40px;
            color: white;
        }

        .login-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .login-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .form-group {
            text-align: left;
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .form-control {
            border-radius: 12px;
            padding: 14px 16px;
            border: 2px solid #e1e5e9;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 10px;
            padding: 12px;
            font-size: 0.9rem;
            margin-top: 20px;
        }

        .demo-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid var(--accent);
            margin-top: 30px;
        }

        .demo-section h6 {
            color: var(--text-dark);
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1rem;
        }

        .demo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .demo-item {
            font-size: 0.9rem;
            text-align: left;
        }

        .demo-item strong {
            color: var(--primary);
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .demo-item div {
            color: #666;
            font-family: 'Courier New', monospace;
            background: white;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #e1e5e9;
            font-size: 0.85rem;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 40px 25px;
                margin: 10px;
            }
            
            .demo-grid {
                grid-template-columns: 1fr;
            }
            
            .login-logo {
                width: 70px;
                height: 70px;
            }
            
            .login-logo i {
                font-size: 32px;
            }
            
            .login-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="login-title">Đăng Nhập</h1>
                <p class="login-subtitle">Hệ thống Quản lý Học Sinh</p>
            </div>

            <form id="loginForm" action="index.php?controller=auth&action=processLogin" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" name="username" required 
                           placeholder="Nhập tên đăng nhập">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required 
                           placeholder="Nhập mật khẩu">
                </div>
                
                <button type="submit" class="btn-login">Đăng nhập</button>
                
                <?php if (isset($_GET['error'])): ?>
                <div class="alert-error" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Tên đăng nhập hoặc mật khẩu không đúng!
                </div>
                <?php endif; ?>
            </form>
            
            <!-- Demo Accounts -->
            <div class="demo-section">
                <h6>Tài khoản demo:</h6>
                <div class="demo-grid">
                    <div class="demo-item">
                        <strong>Quản trị viên:</strong>
                        <div>admin / 123456</div>
                    </div>
                    <div class="demo-item">
                        <strong>Giáo viên:</strong>
                        <div>gvcn01 / 123456</div>
                    </div>
                    <div class="demo-item">
                        <strong>Học sinh:</strong>
                        <div>hs01 / 123456</div>
                    </div>
                    <div class="demo-item">
                        <strong>Phụ huynh:</strong>
                        <div>ph01 / 123456</div>
                    </div>
                    <div class="demo-item">
                        <strong>Ban giám hiệu:</strong>
                        <div>bgh01 / 123456</div>
                    </div>
                    <div class="demo-item">
                        <strong>Ban giám hiệu 2:</strong>
                        <div>bgh02 / 123456</div>
                    </div>
                    <div class="demo-item">
                        <strong>Tổ trưởng:</strong>
                        <div>totruong01 / 123456</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>