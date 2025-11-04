<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: index.php?controller=auth&action=login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <style>
        body {
            background: linear-gradient(135deg, #bf349aff, #4f46e5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }
        .change-container {
            background-color: #0f172a;
            color: white;
            padding: 40px 50px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            width: 420px;
        }
        .change-container h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .change-container p {
            text-align: center;
            margin-bottom: 30px;
            color: #94a3b8;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #cbd5e1;
        }
        input {
            width: 100%;
            padding: 10px 12px;
            border: none;
            border-radius: 8px;
            background-color: #1e293b;
            color: white;
            font-size: 15px;
        }
        input:focus {
            outline: 2px solid #3b82f6;
        }
        .btn {
            width: 100%;
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn:hover {
            background-color: #2563eb;
        }
        .link {
            text-align: center;
            margin-top: 18px;
        }
        .link a {
            color: #3b82f6;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            color: #f87171;
        }
    </style>
</head>
<body>
    <div class="change-container">
        <h2>Đổi Mật Khẩu</h2>
        <p>Cập nhật mật khẩu tài khoản của bạn</p>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="message"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?controller=auth&action=changePassword">
            <div class="form-group">
                <label for="old_password">Mật khẩu cũ</label>
                <input type="password" id="old_password" name="old_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Mật khẩu mới</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu mới</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Cập nhật mật khẩu</button>

            <div class="link">
                <a href="index.php">← Quay lại trang chủ</a>
            </div>
        </form>
    </div>
</body>
</html>
