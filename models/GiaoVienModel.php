<?php
require_once 'models/Database.php';

class GiaoVienModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy thông tin giáo viên dựa trên mã người dùng.
    public function getGiaoVienByMaNguoiDung($maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT gv.*, nd.hoTen 
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE gv.maNguoiDung = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maNguoiDung]);
            return $stmt->fetch(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy giáo viên theo mã người dùng: " . $e->getMessage());
            return false;
        }
    }

    // Lấy danh sách tất cả giáo viên
    public function getAllGiaoVien() {
        $conn = $this->db->getConnection();
        $sql = "SELECT gv.maGiaoVien, nd.hoTen, nd.email, nd.soDienThoai 
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                ORDER BY nd.hoTen";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>