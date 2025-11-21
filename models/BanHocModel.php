<?php
require_once 'models/Database.php';

class BanHocModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy tất cả ban học
    public function getAllBanHoc() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT * FROM banhoc WHERE trangThai = 1 ORDER BY tenBan";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy ban học theo ID
    public function getBanHocById($maBan) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT * FROM banhoc WHERE maBan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maBan]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật số lượng đăng ký
    public function updateSoLuongDangKy($maBan) {
        $conn = $this->db->getConnection();
        
        $sql = "UPDATE banhoc SET soLuongDaDangKy = soLuongDaDangKy + 1 WHERE maBan = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maBan]);
    }
}
?>