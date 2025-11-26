<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <style>
        body {
            background: linear-gradient(135deg, #3b82f6, #4f46e5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }

        .register-container {
            background-color: #0f172a;
            color: white;
            padding: 40px 50px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            width: 420px;
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .register-container p {
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
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Đăng Ký</h2>
        <p>Tạo tài khoản người dùng mới</p>
        <form method="POST" action="index.php?controller=auth&action=register">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" required placeholder="Nhập tên đăng nhập">
            </div>
            <div class="form-group">
                <label for="fullname">Họ và tên</label>
                <input type="text" id="fullname" name="fullname" required placeholder="Nhập họ tên">
            </div>
            <div class="form-group">
                <label for="email">Email (tùy chọn)</label>
                <input type="email" id="email" name="email" placeholder="Nhập email">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required placeholder="Nhập mật khẩu">
            </div>
            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Nhập lại mật khẩu">
            </div>
            <button type="submit" class="btn">Đăng ký</button>

            <div class="link">
                <p>Đã có tài khoản? <a href="index.php?controller=auth&action=login">Đăng nhập</a></p>
            </div>
        </form>
    </div>
</body>
</html>
