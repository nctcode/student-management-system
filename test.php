<?php
session_start();
require_once 'models/Database.php';
require_once 'models/GiaoVienModel.php';

$model = new GiaoVienModel();

echo "<h2>Test lấy giáo viên</h2>";

// Test 1: Không có maTruong
echo "<h3>Test 1: getAllTeachers(null)</h3>";
$teachers1 = $model->getAllTeachers(null);
echo "Số lượng: " . count($teachers1) . "<br>";
echo "<pre>";
print_r($teachers1);
echo "</pre>";

// Test 2: Với maTruong = 1
echo "<h3>Test 2: getAllTeachers(1)</h3>";
$teachers2 = $model->getAllTeachers(1);
echo "Số lượng: " . count($teachers2) . "<br>";
echo "<pre>";
print_r($teachers2);
echo "</pre>";

// Test SQL trực tiếp
$db = new Database();
$conn = $db->getConnection();

echo "<h3>Test SQL trực tiếp</h3>";
$sql = "SELECT COUNT(*) as count FROM giaovien";
$stmt = $conn->prepare($sql);
$stmt->execute();
$count = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Tổng số giáo viên trong bảng: " . $count['count'];
?>