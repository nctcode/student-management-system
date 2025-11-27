<?php
require_once __DIR__ . '/models/Database.php';
require_once __DIR__ . '/models/DonChuyenLopTruongModel.php';

session_start();
// Giả lập session BGH
$_SESSION['user'] = [
    'vaiTro' => 'BGH',
    'maTruong' => 1, // Thay bằng mã trường thực tế
    'maNguoiDung' => 2
];

$model = new DonChuyenLopTruongModel();
$requests = $model->getAll('', 1, 'tat_ca'); // Thay 1 bằng mã trường

echo "<h2>DEBUG: All Transfer Requests</h2>";
echo "<pre>";
print_r($requests);
echo "</pre>";