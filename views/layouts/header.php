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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/header.css" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo-section">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="system-info">
                    <h1>HỆ THỐNG QUẢN LÝ HỌC SINH</h1>
                    <div class="subtitle">Trường THCS-THPT Chất lượng cao</div>
                </div>
            </div>
            
                    
                    <div class="header-actions">
                <?php if (isset($_SESSION['user'])): ?>
                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <div class="user-info">
                    <div class="user-avatar">
                        <?php 
                        $userName = $_SESSION['user']['hoTen'] ?? 'User';
                        echo strtoupper(substr($userName, 0, 1)); 
                        ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name">
                            <a href="index.php?controller=profile" style="color: inherit; text-decoration: none;">
                                <?php echo $userName; ?>
                            </a>
                        </div>
                        <div class="user-role">
                            <?php 
                            $role = $_SESSION['user']['vaiTro'] ?? 'GUEST';
                            echo getRoleName($role); 
                            ?>
                        </div>
                    </div>
                </div>
                
                <a href="index.php?controller=auth&action=logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Đăng xuất</span>
                </a>
                <?php else: ?>
                <a href="index.php?controller=auth&action=login" class="logout-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Đăng nhập</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
   <div class="main-container">
    <?php if (isset($_SESSION['user']) && isset($showSidebar) && $showSidebar !== false): ?>
    <nav class="sidebar">
        </nav>
    <?php endif; ?>
    
    <main class="content-area" style="<?php echo (!isset($_SESSION['user']) || !isset($showSidebar) || $showSidebar === false) ? 'margin-left: 0;' : ''; ?>">