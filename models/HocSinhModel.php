<?php
require_once 'models/Database.php';

class HocSinhModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy danh sách lớp học
    public function getDanhSachLop() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, k.tenKhoi 
                FROM lophoc l 
                JOIN khoi k ON l.maKhoi = k.maKhoi 
                ORDER BY k.tenKhoi, l.tenLop";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy học sinh theo lớp
    public function getHocSinhByLop($maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    hs.maHocSinh,
                    hs.maNguoiDung,
                    COALESCE(nd.hoTen, CONCAT('Học sinh ', hs.maHocSinh)) as hoTen,
                    l.tenLop,
                    l.maLop,
                    COALESCE(tk.vaiTro, 'HOCSINH') as vaiTro,
                    hs.trangThai
                FROM hocsinh hs
                LEFT JOIN lophoc l ON hs.maLop = l.maLop
                LEFT JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan
                WHERE hs.maLop = ?
                ORDER BY hs.maHocSinh";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop]);
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug
        error_log("HocSinh query for maLop $maLop returned " . count($result) . " rows");
        
        return $result;
    }

    // Lấy học sinh theo mã người dùng
    public function getHocSinhByNguoiDung($maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, nd.hoTen, l.tenLop, l.maLop
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON hs.maLop = l.maLop
                WHERE hs.maNguoiDung = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin học sinh theo mã học sinh
    // public function getHocSinhById($maHocSinh) {
    //     $conn = $this->db->getConnection();
        
    //     $sql = "SELECT h.*, nd.hoTen, nd.ngaySinh, nd.gioiTinh, nd.soDienThoai, nd.email, nd.diaChi
    //             FROM hocsinh h
    //             JOIN nguoidung nd ON h.maNguoiDung = nd.maNguoiDung
    //             WHERE h.maHocSinh = ?";
        
    //     $stmt = $conn->prepare($sql);
    //     $stmt->execute([$maHocSinh]);
        
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
    //     // Debug
    //     error_log("Query getHocSinhById: maHocSinh = " . $maHocSinh);
    //     error_log("Result: " . print_r($result, true));
        
    //     return $result;
    // }

    public function getHocSinhById($maHocSinh) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    h.maHocSinh, 
                    h.maLop,
                    nd.hoTen as tenHocSinh,
                    l.tenLop,
                    l.maTruong,
                    t.tenTruong
                FROM hocsinh h
                JOIN nguoidung nd ON h.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON h.maLop = l.maLop
                LEFT JOIN truong t ON l.maTruong = t.maTruong
                WHERE h.maHocSinh = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug
        error_log("Query getHocSinhById: maHocSinh = " . $maHocSinh);
        error_log("Result: " . print_r($result, true));
        
        return $result;
    }

    // Lấy tất cả học sinh (cho admin)
    public function getAllHocSinh() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    hs.maHocSinh,
                    nd.hoTen,
                    l.tenLop,
                    nd.email,
                    nd.soDienThoai,
                    hs.trangThai
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON hs.maLop = l.maLop
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy sĩ số của một lớp
    public function getSoLuongHocSinhByLop($maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as siSo FROM hocsinh WHERE maLop = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maLop]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['siSo'] ?? 0;
        } catch (Exception $e) {
            error_log("Lỗi CSDL khi lấy sĩ số: " . $e->getMessage());
            return 0;
        }
    }
    
    // Thêm vào HocSinhModel.php

    // Lấy danh sách lớp học theo trường
    public function getDanhSachLopByTruong($maTruong) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, k.tenKhoi 
                FROM lophoc l 
                JOIN khoi k ON l.maKhoi = k.maKhoi 
                WHERE l.maTruong = ?
                ORDER BY k.tenKhoi, l.tenLop";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maTruong]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy học sinh theo lớp và trường
    public function getHocSinhByLopAndTruong($maLop, $maTruong) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    hs.maHocSinh,
                    nd.maNguoiDung,
                    nd.hoTen,
                    l.tenLop,
                    l.maLop,
                    tk.vaiTro
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan
                JOIN lophoc l ON hs.maLop = l.maLop
                WHERE l.maLop = ? AND l.maTruong = ?
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop, $maTruong]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Lấy danh sách học sinh theo phụ huynh (dựa vào khóa ngoại maphuhuynh)
    public function getHocSinhByPhuHuynh($maPhuHuynh) {
        $conn = $this->db->getConnection();
        try {
            $sql = "SELECT 
                        hs.maHocSinh,
                        hs.maLop,
                        nd.hoTen,
                        nd.ngaySinh,
                        nd.gioiTinh,
                        l.tenLop,
                        k.tenKhoi as khoi
                    FROM hocsinh hs
                    JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                    JOIN lophoc l ON hs.maLop = l.maLop
                    JOIN khoi k ON l.maKhoi = k.maKhoi
                    WHERE hs.maPhuHuynh = ?
                    AND hs.trangThai = 'DANG_HOC'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maPhuHuynh]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug
            error_log("DEBUG getHocSinhByPhuHuynh: maPhuHuynh=$maPhuHuynh, Count=" . count($results));
            
            return $results;
        } catch (Exception $e) {
            error_log("ERROR in getHocSinhByPhuHuynh: " . $e->getMessage());
            return [];
        }
    }
}
?>
