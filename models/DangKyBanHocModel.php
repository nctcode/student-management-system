<?php
class DangKyBanHocModel {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // 1. Kiểm tra học sinh đã đăng ký chưa
    public function kiemTraDaDangKy($maHocSinh) {
        $sql = "SELECT COUNT(*) as count FROM dangkybanhoc WHERE maHocSinh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    // 2. Lấy danh sách ban học còn chỉ tiêu
    public function getDanhSachBanConChiTieu() {
        $sql = "SELECT * FROM banhoc WHERE trangThai = 1 AND soLuongDaDangKy < chiTieu";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 3. Kiểm tra xem một ban học cụ thể có còn chỉ tiêu không
    public function kiemTraBanConChiTieu($maBan) {
        $sql = "SELECT chiTieu, soLuongDaDangKy FROM banhoc WHERE maBan = ? AND trangThai = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maBan]);
        $ban = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($ban && $ban['soLuongDaDangKy'] < $ban['chiTieu']) {
            return true;
        }
        return false;
    }
    
    // 4. Thực hiện đăng ký (Bao gồm Transaction)
    public function dangKyBanHoc($maHocSinh, $maBan) {
        $this->conn->beginTransaction();
        
        try {
            // Thêm đăng ký (Đã thống nhất tên bảng)
            $sql = "INSERT INTO dangkybanhoc (maHocSinh, maBan) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$maHocSinh, $maBan]);
            
            // Cập nhật số lượng đã đăng ký trong bảng banhoc
            $sqlUpdate = "UPDATE banhoc SET soLuongDaDangKy = soLuongDaDangKy + 1 WHERE maBan = ?";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->execute([$maBan]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi đăng ký ban học: " . $e->getMessage());
            return false;
        }
    }
    
    // 5. Lấy thông tin đăng ký của học sinh
    public function getThongTinDangKy($maHocSinh) {
        $sql = "SELECT dk.*, bh.tenBan 
                FROM dangkybanhoc dk 
                JOIN banhoc bh ON dk.maBan = bh.maBan 
                WHERE dk.maHocSinh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>