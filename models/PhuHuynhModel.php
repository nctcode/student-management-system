<?php
require_once 'models/Database.php';

class PhuHuynhModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy danh sách phụ huynh theo lớp
    public function getPhuHuynhByLop($maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    ph.maPhuHuynh,
                    nd_ph.maNguoiDung,
                    nd_ph.hoTen,
                    nd_ph.email,
                    nd_ph.soDienThoai,
                    hs.maHocSinh,
                    nd_hs.hoTen as tenHocSinh,
                    l.tenLop,
                    tk.vaiTro
                FROM phuhuynh ph
                JOIN nguoidung nd_ph ON ph.maNguoiDung = nd_ph.maNguoiDung
                JOIN taikhoan tk ON nd_ph.maTaiKhoan = tk.maTaiKhoan
                JOIN hocsinh hs ON ph.maPhuHuynh = hs.maPhuHuynh
                JOIN nguoidung nd_hs ON hs.maNguoiDung = nd_hs.maNguoiDung
                JOIN lophoc l ON hs.maLop = l.maLop
                WHERE l.maLop = ?
                ORDER BY nd_ph.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy phụ huynh theo mã người dùng
    public function getPhuHuynhByNguoiDung($maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT ph.*, nd.hoTen, nd.email, nd.soDienThoai
                FROM phuhuynh ph
                JOIN nguoidung nd ON ph.maNguoiDung = nd.maNguoiDung
                WHERE ph.maNguoiDung = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách con của phụ huynh
    public function getHocSinhCuaPhuHuynh($maPhuHuynh) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    hs.maHocSinh,
                    nd.hoTen,
                    l.tenLop,
                    l.maLop
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                JOIN lophoc l ON hs.maLop = l.maLop
                WHERE hs.maPhuHuynh = ?
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maPhuHuynh]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>