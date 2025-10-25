<?php
$controllers = [
    'HomeController', 'HocPhiController', 'TinNhanController', 
    'DonChuyenLopTruongController', 'ThoiKhoaBieuController', 
    'TuyenSinhController', 'PhanCongDeThiController', 'DuyetDeThiController'
];

foreach ($controllers as $controller) {
    $file = "controllers/$controller.php";
    if (file_exists($file)) {
        echo "✅ $controller - TỒN TẠI\n";
        echo"<br>";
    } else {
        echo "❌ $controller - THIẾU\n";
        echo"<br>";
    }
}
?>