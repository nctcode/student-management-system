<?php
// Dữ liệu người dùng từ controller
$user = is_array($userData) ? $userData : [
    'maNguoiDung' => '',
    'hoTen' => '',
    'ngaySinh' => '',
    'gioiTinh' => '',
    'soDienThoai' => '',
    'email' => '',
    'diaChi' => '',
    'cccd' => '',
    'loaiNguoiDung' => ''
];

// Thông báo lỗi hoặc thành công
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật thông tin người dùng</title>
    <link rel="stylesheet" href="assets/css/capnhatthongtinnguoidung.css">
</head>

<body>

    <div class="main-content">
        <div class="form-container">
            <h2>Cập nhật thông tin người dùng</h2>

            <?php if ($message): ?>
                <div class="alert <?php echo $message_type ?: 'alert-success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Form nhập mã người dùng -->
            <form method="POST" class="user-id-form">
                <input type="text" name="maNguoiDung" placeholder="Nhập mã người dùng..." required
                    value="<?php echo htmlspecialchars($user['maNguoiDung']); ?>">
                <button type="submit">Xác nhận</button>
            </form>

            <!-- Luôn hiển thị 2 cột thông tin -->
            <form action="?controller=QuanLyNguoiDung&action=update" method="POST" id="updateForm">
                <input type="hidden" name="maNguoiDung" value="<?php echo htmlspecialchars($user['maNguoiDung']); ?>">

                <div class="info-section">
                    <!-- Thông tin hiện tại -->
                    <div class="info-box">
                        <h3>Thông tin hiện tại</h3>
                        <?php foreach (['maNguoiDung', 'hoTen', 'ngaySinh', 'gioiTinh', 'soDienThoai', 'email', 'diaChi', 'cccd', 'loaiNguoiDung'] as $field): ?>
                            <div class="form-group">
                                <label><?php echo ucfirst($field); ?></label>
                                <input type="text" value="<?php echo htmlspecialchars($user[$field] ?: ''); ?>" readonly
                                    placeholder="Chưa có dữ liệu">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Thông tin mới -->
                    <div class="info-box">
                        <h3>Thông tin mới</h3>
                        <div class="form-group">
                            <label>Họ tên</label>
                            <input type="text" name="hoTen" placeholder="<?php echo htmlspecialchars($user['hoTen'] ?: 'Nhập họ tên'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="date" name="ngaySinh" value="<?php echo htmlspecialchars($user['ngaySinh']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Giới tính</label>
                            <select name="gioiTinh">
                                <option value="">-- Giữ nguyên --</option>
                                <option value="Nam" <?php if ($user['gioiTinh'] == 'Nam') echo 'selected'; ?>>Nam</option>
                                <option value="Nữ" <?php if ($user['gioiTinh'] == 'Nữ') echo 'selected'; ?>>Nữ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>SĐT</label>
                            <input type="text" name="soDienThoai" placeholder="<?php echo htmlspecialchars($user['soDienThoai'] ?: 'Nhập số điện thoại'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="<?php echo htmlspecialchars($user['email'] ?: 'Nhập email'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <input type="text" name="diaChi" placeholder="<?php echo htmlspecialchars($user['diaChi'] ?: 'Nhập địa chỉ'); ?>">
                        </div>
                        <div class="form-group">
                            <label>CCCD</label>
                            <input type="text" name="cccd" placeholder="<?php echo htmlspecialchars($user['cccd'] ?: 'Nhập CCCD'); ?>">
                        </div>
                    </div>
                </div>

                <button type="button" class="update-btn" onclick="openPopup()">Cập nhật</button>
            </form>

        </div>
    </div>

    <!-- Popup xác nhận -->
    <div class="popup" id="popup">
        <div class="popup-content">
            <h3>Bạn có chắc muốn cập nhật thông tin không?</h3>
            <button class="yes" onclick="submitForm()">Xác nhận</button>
            <button class="no" onclick="closePopup()">Hủy</button>
        </div>
    </div>

    <script>
        function openPopup() {
            document.getElementById('popup').style.display = 'flex';
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        function submitForm() {
            document.getElementById('updateForm').submit();
        }
    </script>

</body>
</html>
