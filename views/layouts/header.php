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

// Lấy số thông báo chưa đọc
$soThongBaoChuaDoc = 0;
if (isset($_SESSION['user'])) {
    require_once 'models/ThongBaoModel.php';
    $thongBaoModel = new ThongBaoModel();
    $userRole = $_SESSION['user']['vaiTro'] ?? '';
    
    switch ($userRole) {
        case 'HOCSINH':
            $soThongBaoChuaDoc = $thongBaoModel->demThongBaoChuaDoc('HOC_SINH');
            break;
        case 'PHUHUYNH':
            $soThongBaoChuaDoc = $thongBaoModel->demThongBaoChuaDoc('PHU_HUYNH');
            break;
        case 'GIAOVIEN':
            $soThongBaoChuaDoc = $thongBaoModel->demThongBaoChuaDoc('GIAO_VIEN');
            break;
        case 'QTV':
        case 'BGH':
            // QTV và BGH xem tất cả thông báo
            $thongBao = $thongBaoModel->layTatCaThongBao();
            $soThongBaoChuaDoc = count(array_filter($thongBao, function($tb) {
                return $tb['trangThai'] === 'Chưa xem';
            }));
            break;
    }
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
    <style>
    /* Modern Notification Styles */
    .notification-dropdown {
        position: relative;
    }

    .notification-menu {
        position: absolute;
        top: 100%;
        right: 0;
        width: 420px;
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        z-index: 1050;
        display: none;
        margin-top: 8px;
    }

    .notification-menu.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .notification-header {
        padding: 20px;
        border-bottom: 1px solid #f1f3f4;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }

    .notification-header h6 {
        margin: 0;
        font-weight: 600;
    }

    .notification-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .notification-item {
        padding: 16px 20px;
        border-bottom: 1px solid #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item:hover {
        background: #f8f9ff;
        transform: translateX(4px);
    }

    .notification-item.unread {
        background: #f0f7ff;
        border-left: 4px solid #007bff;
    }

    .notification-title {
        font-weight: 600;
        margin-bottom: 6px;
        color: #2c3e50;
        font-size: 0.95rem;
    }

    .notification-content {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #adb5bd;
    }

    .notification-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 0.65rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        border: 2px solid white;
    }

    .view-all-notifications {
        padding: 16px 20px;
        text-align: center;
        border-top: 1px solid #f1f3f4;
        background: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }

    .view-all-notifications a {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .view-all-notifications a:hover {
        color: #0056b3;
    }

    /* Scrollbar styling */
    .notification-list::-webkit-scrollbar {
        width: 6px;
    }

    .notification-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .notification-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .notification-list::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
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
                <?php if (isset($_SESSION['user'])): ?>
                <!-- Notification bell với dropdown -->
                <div class="notification-dropdown">
                    <div class="notification-bell" id="notificationBell">
                        <i class="fas fa-bell"></i>
                        <?php if ($soThongBaoChuaDoc > 0): ?>
                        <span class="notification-badge"><?php echo $soThongBaoChuaDoc; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Notification dropdown menu -->
                    <div class="notification-menu" id="notificationMenu">
                        <div class="notification-header">
                            <h6 class="mb-0">Thông báo</h6>
                            <?php if (in_array($_SESSION['user']['vaiTro'] ?? '', ['QTV', 'BGH'])): ?>
                            <a href="index.php?controller=thongbao&action=dangthongbao" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Đăng thông báo
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="notification-list" id="notificationList">
                            <!-- Nội dung thông báo sẽ được tải bằng AJAX -->
                            <div class="text-center p-3">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="ms-2">Đang tải thông báo...</span>
                            </div>
                        </div>
                        
                        <div class="view-all-notifications">
                            <a href="index.php?controller=thongbao&action=danhsach" class="text-primary">
                                Xem tất cả thông báo
                            </a>
                        </div>
                    </div>
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
                            echo getRoleName($role); 
                            ?>
                        </div>
                    </div>
                </div>
                <a href="index.php?controller=auth&action=changePassword" class="logout-btn">
                    <i class="fas fa-key"></i>
                    <span>Đổi mật khẩu</span>
                </a>
                <!-- Logout button -->
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
        
        <!-- Content area -->
        <main class="content-area" style="<?php echo (!isset($_SESSION['user']) || !isset($showSidebar) || $showSidebar === false) ? 'margin-left: 0;' : ''; ?>">