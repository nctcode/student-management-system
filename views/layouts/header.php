<?php
function getRoleName($role) {
    $roles = [
        'QTV' => 'Quản trị viên',
        'GIAOVIEN' => 'Giáo viên',
        'HOCSINH' => 'Học sinh',
        'PHUHUYNH' => 'Phụ huynh',
        'BGH' => 'Ban giám hiệu',
        'GUEST' => 'Khách'
    ];
    return $roles[$role] ?? $role;
}
?>
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
    <!-- Custom CSS -->
    <link href="assets/css/header.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/phzgc5fpe6tw4kpsx3qymxa2vd9r6rgbdipsroc4ufsscz71/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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
                <?php if (isset($_SESSION['user'])): ?>
                <!-- Notification bell - chỉ hiển thị khi đã đăng nhập -->
                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <!-- User info - chỉ hiển thị khi đã đăng nhập -->
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
                            echo getRoleName($role); 
                            ?>
                        </div>
                    </div>
                </div>
                
                <!-- Logout button - chỉ hiển thị khi đã đăng nhập -->
                <a href="index.php?controller=auth&action=logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Đăng xuất</span>
                </a>
                <?php else: ?>
                <!-- Hiển thị nút đăng nhập khi chưa đăng nhập -->
                <a href="index.php?controller=auth&action=login" class="logout-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Đăng nhập</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
   <!-- MAIN CONTENT -->
<div class="main-container">
    <!-- Sidebar -->
    <?php if (isset($_SESSION['user']) && isset($showSidebar) && $showSidebar !== false): ?>
    <nav class="sidebar">
        <!-- Sidebar content -->
    </nav>
    <?php endif; ?>
    
    <!-- Content area - MỞ thẻ main -->
    <main class="content-area" style="<?php echo (!isset($_SESSION['user']) || !isset($showSidebar) || $showSidebar === false) ? 'margin-left: 0;' : ''; ?>">