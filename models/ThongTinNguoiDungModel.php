<?php
require_once 'models/Database.php';

class ThongTinNguoiDungModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy thông tin người dùng theo mã
    public function getUserById($maNguoiDung)
    {
        $sql = "SELECT 
                    maNguoiDung,
                    hoTen,
                    ngaySinh,
                    gioiTinh,
                    soDienThoai,
                    email,
                    diaChi,
                    cccd,
                    loaiNguoiDung
                FROM nguoidung
                WHERE maNguoiDung = :maNguoiDung";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['maNguoiDung' => $maNguoiDung]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin người dùng
    public function updateUser($maNguoiDung, $data)
    {
        $sql = "UPDATE nguoidung SET
                    hoTen = :hoTen,
                    ngaySinh = :ngaySinh,
                    gioiTinh = :gioiTinh,
                    soDienThoai = :soDienThoai,
                    email = :email,
                    diaChi = :diaChi,
                    cccd = :cccd
                WHERE maNguoiDung = :maNguoiDung";

        $stmt = $this->conn->prepare($sql);

        $params = [
            ':hoTen' => $data['hoTen'],
            ':ngaySinh' => $data['ngaySinh'],
            ':gioiTinh' => $data['gioiTinh'],
            ':soDienThoai' => $data['soDienThoai'],
            ':email' => $data['email'],
            ':diaChi' => $data['diaChi'],
            ':cccd' => $data['cccd'],
            ':maNguoiDung' => $maNguoiDung
        ];

        return $stmt->execute($params); // trả về true/false
    }
}
?>
