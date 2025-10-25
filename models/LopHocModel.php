<?php
require_once 'models/Database.php';

class LopHocModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy tất cả lớp học
    public function getAllLopHoc() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, k.tenKhoi, gv.maGiaoVien, nd.hoTen as tenGiaoVien
                FROM lophoc l
                JOIN khoi k ON l.maKhoi = k.maKhoi
                LEFT JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE l.siSo < 40  -- Chỉ hiển thị lớp còn chỗ
                ORDER BY k.tenKhoi, l.tenLop";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy lớp học theo khối
    public function getLopHocByKhoi($maKhoi) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, k.tenKhoi, gv.maGiaoVien, nd.hoTen as tenGiaoVien
                FROM lophoc l
                JOIN khoi k ON l.maKhoi = k.maKhoi
                LEFT JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE l.maKhoi = ? AND l.siSo < 40
                ORDER BY l.tenLop";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maKhoi]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>