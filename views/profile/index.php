<?php
require_once __DIR__ . '/../layouts/header.php';

// Nạp sidebar dựa trên vai trò của người dùng
// Chúng ta cần một logic nhỏ ở đây
$roleSidebar = '';
if (isset($_SESSION['user']['vaiTro'])) {
    switch ($_SESSION['user']['vaiTro']) {
        case 'BGH':
            $roleSidebar = 'bangiamhieu.php';
            break;
        case 'GIAOVIEN':
            $roleSidebar = 'giaovien.php';
            break;
        case 'HOCSINH':
            $roleSidebar = 'hocsinh.php';
            break;
        // Thêm các trường hợp khác nếu cần
    }
}
if ($roleSidebar) {
    require_once __DIR__ . '/../layouts/sidebar/' . $roleSidebar;
}
?>

<div class="container" style="padding: 20px; margin-left: 250px;">
    <h2>Thông tin cá nhân</h2>

    <?php if (isset($userInfo) && $userInfo): ?>
        <table style="width: 100%; max-width: 700px; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px; background-color: #f9f9f9; font-weight: bold; width: 150px;">Họ và Tên:</td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($userInfo['hoTen']); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px; background-color: #f9f9f9; font-weight: bold;">Ngày sinh:</td>
                <td style="padding: 10px;"><?php echo date('d/m/Y', strtotime($userInfo['ngaySinh'])); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px; background-color: #f9f9f9; font-weight: bold;">Giới tính:</td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($userInfo['gioiTinh']); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px; background-color: #f9f9f9; font-weight: bold;">Email:</td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($userInfo['email']); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px; background-color: #f9f9f9; font-weight: bold;">Số điện thoại:</td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($userInfo['soDienThoai']); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px; background-color: #f9f9f9; font-weight: bold;">Địa chỉ:</td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($userInfo['diaChi']); ?></td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px; background-color: #f9f9f9; font-weight: bold;">CCCD/CMND:</td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($userInfo['CCCD']); ?></td>
            </tr>
        </table>

        <?php else: ?>
        <p style="color: red;">Không thể tải thông tin người dùng.</p>
    <?php endif; ?>

</div>

<?php
require_once __DIR__ . '/../layouts/footer.php'; 
?>