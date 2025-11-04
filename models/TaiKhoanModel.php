<?php
class TaiKhoanModel {
    private $conn;

    public function __construct() {
require_once __DIR__ . '/Database.php';


        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getUserByUsername($tendangnhap) {
        $sql = "SELECT * FROM taikhoan WHERE tendangnhap = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tendangnhap]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($tendangnhap, $newPassword) {
        $sql = "UPDATE taikhoan SET matkhau = ? WHERE tendangnhap = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$newPassword, $tendangnhap]);
    }
}
