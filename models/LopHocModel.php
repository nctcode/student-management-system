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
    // public function getLopHocByKhoi($maKhoi) {
    //     $conn = $this->db->getConnection();
        
    //     $sql = "SELECT l.*, k.tenKhoi, gv.maGiaoVien, nd.hoTen as tenGiaoVien
    //             FROM lophoc l
    //             JOIN khoi k ON l.maKhoi = k.maKhoi
    //             LEFT JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
    //             LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
    //             WHERE l.maKhoi = ? AND l.siSo < 40
    //             ORDER BY l.tenLop";
        
    //     $stmt = $conn->prepare($sql);
    //     $stmt->execute([$maKhoi]);
        
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }
    public function getTatCaLop() {
        $conn = $this->db->getConnection();
        $stmt = $this->$conn->query("SELECT maLop, tenLop FROM lophoc ORDER BY tenLop");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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
    public function getKhoiHoc() {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM khoi ORDER BY tenKhoi ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Thêm các phương thức này vào LopHocModel

    public function getLopHocByTruong($maTruong) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, gv.maGiaoVien, nd.hoTen as tenGiaoVien, k.tenKhoi
                FROM lophoc l
                LEFT JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                JOIN khoi k ON l.maKhoi = k.maKhoi
                WHERE l.maTruong = ?
                ORDER BY k.tenKhoi, l.tenLop";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maTruong]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getKhoiHocByTruong($maTruong) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT DISTINCT k.maKhoi, k.tenKhoi
                FROM khoi k
                JOIN lophoc l ON k.maKhoi = l.maKhoi
                WHERE l.maTruong = ?
                ORDER BY k.tenKhoi";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maTruong]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLopHocByKhoi($maKhoi, $maTruong = null) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, gv.maGiaoVien, nd.hoTen as tenGiaoVien, k.tenKhoi
                FROM lophoc l
                LEFT JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                JOIN khoi k ON l.maKhoi = k.maKhoi
                WHERE l.maKhoi = ?";
        
        $params = [$maKhoi];
        
        if ($maTruong) {
            $sql .= " AND l.maTruong = ?";
            $params[] = $maTruong;
        }
        
        $sql .= " ORDER BY l.tenLop";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>