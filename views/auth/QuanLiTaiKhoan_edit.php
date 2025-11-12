<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem biến $user có được truyền từ controller không
if (!isset($user) || empty($user)) {
    echo "<script>alert('Lỗi: Không có dữ liệu tài khoản!'); window.history.back();</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh Sửa Tài Khoản</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #555;
        }

        .display-info {
            padding: 10px 12px;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            color: #333;
            font-weight: 500;
        }

        small {
            color: #6c757d;
            font-size: 12px;
            display: block;
            margin-top: 5px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #555;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus,
        select:focus {
            border-color: #2196F3;
            outline: none;
        }

        .password-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .password-section label {
            color: #495057;
            margin-bottom: 10px;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 14px;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2196F3;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1976D2;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #555;
            text-decoration: none;
        }

        .back-link:hover {
            color: #000;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Chỉnh Sửa Tài Khoản</h2>
    
    <form action="index.php?controller=QuanLyTaiKhoan&action=update&id=<?php echo htmlspecialchars($user['id']); ?>" method="post">
    
    <!-- Thông tin username (chỉ hiển thị, không chỉnh sửa) -->
    <div class="info-group">
        <label>Tên đăng nhập:</label>
        <div class="display-info"><?php echo htmlspecialchars($user['username']); ?></div>
        <small>Tên đăng nhập không thể thay đổi</small>
    </div>

    <!-- Phần thay đổi mật khẩu -->
    <div class="password-section">
        <label>Thay đổi mật khẩu:</label>
        <div class="alert alert-info">
            Để trống nếu không muốn thay đổi mật khẩu
        </div>
        <input type="password" name="new_password" placeholder="Mật khẩu mới">
        <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu mới">
    </div>

    <!-- Vai trò (chỉ Quản trị viên mới được chỉnh sửa) -->
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['vaiTro'] === 'QTV'): ?>
        <label for="vaiTro">Vai trò:</label>
        <select id="vaiTro" name="vaiTro">
            <option value="USER" <?php echo ($user['vaiTro'] == 'USER') ? 'selected' : ''; ?>>Người dùng</option>
            <option value="QTV" <?php echo ($user['vaiTro'] == 'QTV') ? 'selected' : ''; ?>>Quản trị viên</option>
            <option value="BGH" <?php echo ($user['vaiTro'] == 'BGH') ? 'selected' : ''; ?>>Ban giám hiệu</option>
            <option value="GIAOVIEN" <?php echo ($user['vaiTro'] == 'GIAOVIEN') ? 'selected' : ''; ?>>Giáo viên</option>
            <option value="HOCSINH" <?php echo ($user['vaiTro'] == 'HOCSINH') ? 'selected' : ''; ?>>Học sinh</option>
            <option value="PHUHUYNH" <?php echo ($user['vaiTro'] == 'PHUHUYNH') ? 'selected' : ''; ?>>Phụ huynh</option>
        </select>
    <?php else: ?>
        <!-- Nếu không phải QTV, hiển thị thông tin vai trò -->
        <div class="info-group">
            <label>Vai trò:</label>
            <div class="display-info">
                <?php 
                $vaiTroText = [
                    'QTV' => 'Quản trị viên',
                    'BGH' => 'Ban giám hiệu', 
                    'GIAOVIEN' => 'Giáo viên',
                    'HOCSINH' => 'Học sinh',
                    'PHUHUYNH' => 'Phụ huynh',
                    'USER' => 'Người dùng'
                ];
                echo $vaiTroText[$user['vaiTro']] ?? 'Người dùng';
                ?>
            </div>
            <small>Chỉ Quản trị viên mới có thể thay đổi vai trò</small>
        </div>
    <?php endif; ?>

    <button type="submit">Cập nhật tài khoản</button>
</form>
    
    <a class="back-link" href="index.php?controller=QuanLyTaiKhoan&action=index">← Quay lại danh sách</a>
</div>

</body>
</html>