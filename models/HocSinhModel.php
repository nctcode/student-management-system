<?php
require_once 'models/Database.php';

class HocSinhModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy học sinh theo phụ huynh
    public function getHocSinhByPhuHuynh($maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, nd.hoTen, nd.ngaySinh, l.tenLop, k.tenKhoi
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                JOIN lophoc l ON hs.maLop = l.maLop
                JOIN khoi k ON l.maKhoi = k.maKhoi
                JOIN phuhuynh ph ON hs.maPhuHuynh = ph.maPhuHuynh
                WHERE ph.maNguoiDung = ? AND hs.trangThai = 'DANG_HOC'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy học sinh theo người dùng
    public function getHocSinhByNguoiDung($maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, nd.hoTen, nd.ngaySinh, l.tenLop, k.tenKhoi
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                JOIN lophoc l ON hs.maLop = l.maLop
                JOIN khoi k ON l.maKhoi = k.maKhoi
                WHERE hs.maNguoiDung = ? AND hs.trangThai = 'DANG_HOC'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>