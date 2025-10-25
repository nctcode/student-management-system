<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Hệ thống QLHS'; ?></title>
    
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
            --text-light: #ecf0f1;
            --text-dark: #2c3e50;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        /* HEADER STYLES */
        .main-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--text-light);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 70px;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 20px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            width: 45px;
            height: 45px;
            background: linear-gradient(45deg, #3498db, #2980b9);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .logo i {
            font-size: 24px;
            color: white;
        }
        
        .system-info h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            color: white;
            line-height: 1.2;
        }
        
        .system-info .subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
            font-weight: 400;
            color: #ecf0f1;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 15px;
            border-radius: 25px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(45deg, #e74c3c, #e67e22);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: white;
        }
        
        .user-role {
            font-size: 0.75rem;
            opacity: 0.8;
            color: #ecf0f1;
        }
        
        .notification-bell {
            position: relative;
            color: white;
            font-size: 1.2rem;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .notification-bell:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            transform: translateY(-1px);
        }
        
        /* MAIN CONTENT AREA */
        .main-container {
            margin-top: 70px;
            min-height: calc(100vh - 70px);
            display: flex;
        }
        
        .sidebar {
            width: 260px;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 70px;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 999;
        }
        
        .content-area {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
            background: #f8f9fa;
            min-height: calc(100vh - 70px);
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .system-info h1 {
                font-size: 1.1rem;
            }
            
            .system-info .subtitle {
                font-size: 0.75rem;
            }
            
            .user-details {
                display: none;
            }
            
            .sidebar {
                width: 70px;
            }
            
            .content-area {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="main-header">
        <div class="header-content">
            <!-- Logo và tên hệ thống -->
            <div class="logo-section">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="system-info">
                    <h1>HỆ THỐNG QUẢN LÝ HỌC SINH</h1>
                    <div class="subtitle">Trường THCS-THPT Chất lượng cao</div>
                </div>
            </div>
            
            <!-- User actions -->
            <div class="header-actions">
                <!-- Notification bell -->
                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <!-- User info -->
                <div class="user-info">
                    <div class="user-avatar">
                        <?php 
                        $userName = $_SESSION['user']['hoTen'] ?? 'User';
                        echo strtoupper(substr($userName, 0, 1)); 
                        ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?php echo $userName; ?></div>
                        <div class="user-role">
                            <?php 
                            $role = $_SESSION['user']['vaiTro'] ?? 'GUEST';
                            echo $role; 
                            ?>
                        </div>
                    </div>
                </div>
                
                <!-- Logout button -->
                <a href="index.php?controller=auth&action=logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Đăng xuất</span>
                </a>
            </div>
        </div>
    </header>
    
    <!-- MAIN CONTENT -->
    <div class="main-container">
        <!-- Sidebar sẽ được include sau -->
        <?php if (isset($showSidebar) && $showSidebar !== false): ?>
        <nav class="sidebar">
            <!-- Sidebar content sẽ được thêm sau -->
        </nav>
        <?php endif; ?>
        
        <!-- Content area -->
        <main class="content-area">