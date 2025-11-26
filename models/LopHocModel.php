<?php
require_once 'models/Database.php';

class LopHocModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy tất cả lớp học (đã có)
    public function getAllLopHoc() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, k.tenKhoi, gv.maGiaoVien, nd.hoTen as tenGiaoVien
                FROM lophoc l
                JOIN khoi k ON l.maKhoi = k.maKhoi
                LEFT JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE l.siSo < 40 
                ORDER BY k.tenKhoi, l.tenLop";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy lớp học theo khối (đã có)
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

    public function getKhoiHoc() {
    $conn = $this->db->getConnection();
    $sql = "SELECT * FROM khoi ORDER BY tenKhoi ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
    // Lấy thông tin chi tiết một lớp học (PHƯƠNG THỨC MỚI ĐÃ THÊM)
    public function getChiTietLop($maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, k.tenKhoi, gv.maGiaoVien, nd.hoTen as tenGiaoVien
                FROM lophoc l
                JOIN khoi k ON l.maKhoi = k.maKhoi
                LEFT JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE l.maLop = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }
}
?>