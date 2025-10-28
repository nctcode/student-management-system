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
                    nd.maNguoiDung,
                    nd.hoTen,
                    l.tenLop,
                    l.maLop,
                    tk.vaiTro
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan
                JOIN lophoc l ON hs.maLop = l.maLop
                WHERE l.maLop = ?
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function getHocSinhById($maHocSinh) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT h.*, nd.hoTen, nd.ngaySinh, nd.gioiTinh, nd.soDienThoai, nd.email, nd.diaChi
                FROM hocsinh h
                JOIN nguoidung nd ON h.maNguoiDung = nd.maNguoiDung
                WHERE h.maHocSinh = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug
        error_log("Query getHocSinhById: maHocSinh = " . $maHocSinh);
        error_log("Result: " . print_r($result, true));
        
        return $result;
    }

    // Lấy học sinh theo phụ huynh (cho phụ huynh xem TKB của con)
    public function getHocSinhByPhuHuynh($maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    hs.maHocSinh,
                    nd_hs.hoTen,
                    l.tenLop,
                    l.maLop
                FROM phuhuynh ph
                JOIN hocsinh hs ON ph.maPhuHuynh = hs.maPhuHuynh
                JOIN nguoidung nd_hs ON hs.maNguoiDung = nd_hs.maNguoiDung
                JOIN lophoc l ON hs.maLop = l.maLop
                WHERE ph.maNguoiDung = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
}
?>
