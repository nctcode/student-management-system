<?php
// Tạo file check_hoso.php trong thư mục gốc để kiểm tra sau khi đăng ký

session_start();
require_once 'models/Database.php';
require_once 'models/HocSinhModel.php';
require_once 'models/TuyenSinhModel.php';

if (!isset($_SESSION['user'])) {
    die("Vui lòng đăng nhập trước");
}

$maNguoiDung = $_SESSION['user']['maNguoiDung'];
$vaiTro = $_SESSION['user']['vaiTro'];

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Kiểm tra dữ liệu</title>";
echo "<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;width:100%;margin:10px 0;} ";
echo "th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#4CAF50;color:white;} ";
echo ".error{color:red;font-weight:bold;} .success{color:green;font-weight:bold;}</style></head><body>";

echo "<h2>🔍 KIỂM TRA DỮ LIỆU HỒ SƠ</h2>";
echo "<hr>";

echo "<h3>1. Thông tin User đang đăng nhập:</h3>";
echo "<table><tr><th>Thuộc tính</th><th>Giá trị</th></tr>";
foreach ($_SESSION['user'] as $key => $value) {
    echo "<tr><td>{$key}</td><td>{$value}</td></tr>";
}
echo "</table>";

$db = new Database();
$conn = $db->getConnection();

if ($vaiTro === 'HOCSINH') {
    echo "<h3>2. Thông tin từ bảng HOCSINH:</h3>";
    $sql = "SELECT * FROM hocsinh WHERE maNguoiDung = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$maNguoiDung]);
    $hocSinh = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($hocSinh) {
        echo "<table><tr><th>Thuộc tính</th><th>Giá trị</th></tr>";
        foreach ($hocSinh as $key => $value) {
            $highlight = ($key === 'maHoSo') ? 'style="background:#ffffcc;"' : '';
            echo "<tr {$highlight}><td>{$key}</td><td>{$value}</td></tr>";
        }
        echo "</table>";
        
        if ($hocSinh['maHoSo']) {
            echo "<p class='success'>✓ Học sinh CÓ maHoSo = {$hocSinh['maHoSo']}</p>";
            
            echo "<h3>3. Thông tin HỒ SƠ TUYỂN SINH (maHoSo = {$hocSinh['maHoSo']}):</h3>";
            $sql2 = "SELECT * FROM hosotuyensinh WHERE maHoSo = ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute([$hocSinh['maHoSo']]);
            $hoSo = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($hoSo) {
                echo "<table><tr><th>Thuộc tính</th><th>Giá trị</th></tr>";
                foreach ($hoSo as $key => $value) {
                    echo "<tr><td>{$key}</td><td>{$value}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='error'>✗ KHÔNG tìm thấy hồ sơ với maHoSo = {$hocSinh['maHoSo']}</p>";
            }
        } else {
            echo "<p class='error'>✗ Học sinh CHƯA CÓ maHoSo!</p>";
            echo "<p>Nguyên nhân: Sau khi đăng ký hồ sơ, bản ghi trong bảng hocsinh chưa được cập nhật maHoSo</p>";
        }
    } else {
        echo "<p class='error'>✗ KHÔNG tìm thấy bản ghi học sinh với maNguoiDung = {$maNguoiDung}</p>";
    }
    
    echo "<h3>4. TEST Query getHoSoByMaNguoiDung:</h3>";
    $sql3 = "SELECT hs.*, b.tenBan, h.maHocSinh
            FROM hosotuyensinh hs
            LEFT JOIN banhoc b ON hs.maBan = b.maBan
            LEFT JOIN hocsinh h ON hs.maHoSo = h.maHoSo
            WHERE h.maNguoiDung = ?
            ORDER BY hs.ngayDangKy DESC";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->execute([$maNguoiDung]);
    $hoSoList = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Số lượng hồ sơ tìm thấy: <strong>" . count($hoSoList) . "</strong></p>";
    if (count($hoSoList) > 0) {
        echo "<table><tr><th>maHoSo</th><th>Họ tên</th><th>Ngày sinh</th><th>Ban học</th><th>Trạng thái</th></tr>";
        foreach ($hoSoList as $hs) {
            echo "<tr><td>{$hs['maHoSo']}</td><td>{$hs['hoTen']}</td><td>{$hs['ngaySinh']}</td><td>{$hs['tenBan']}</td><td>{$hs['trangThai']}</td></tr>";
        }
        echo "</table>";
    }
}

echo "<hr><p><a href='index.php?controller=tuyensinh&action=hosocuatoi' style='padding:10px 20px;background:#4CAF50;color:white;text-decoration:none;border-radius:4px;'>← Quay lại Hồ sơ của tôi</a></p>";
echo "</body></html>";
?>