<?php
require_once 'models/Database.php'; 

class HoSoModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection(); 
    }

    // Hàm lấy thông tin hồ sơ theo mã hồ sơ
    public function getHoSoByMa($maHoSo)
    {
        $sql = "SELECT 
                    maHoSo,
                    hoTen,
                    ngaySinh,
                    gioiTinh,
                    soDienThoai,
                    diaChi,
                    truongTHCS,
                    hoTenPhuHuynh,
                    soDTPhuHuynh,
                    nguyenVong,
                    ngayDangKy,
                    trangThai,
                    ketQua
                FROM hosotuyensinh
                WHERE maHoSo = :maHoSo";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['maHoSo' => $maHoSo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
