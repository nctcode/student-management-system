<?php
session_start();
echo "<h2>Debug Session</h2>";
echo "<pre>";
echo "SESSION data:\n";
print_r($_SESSION);
echo "\nGET data:\n";
print_r($_GET);
echo "</pre>";

// Kiểm tra cụ thể user session
if (isset($_SESSION['user'])) {
    echo "<h3>User Info:</h3>";
    echo "Vai trò: " . ($_SESSION['user']['vaiTro'] ?? 'Không có') . "<br>";
    echo "Mã PH: " . ($_SESSION['user']['maPhuHuynh'] ?? 'Không có') . "<br>";
    echo "Tên: " . ($_SESSION['user']['hoTen'] ?? 'Không có') . "<br>";
} else {
    echo "<h3 style='color:red'>Không có session user</h3>";
}

echo "<br><a href='index.php?controller=donchuyenloptruong&action=danhsachdoncuatoi'>Test truy cập đơn</a>";