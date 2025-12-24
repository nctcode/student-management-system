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
    
    // 4. Thực hiện đăng ký HOẶC CẬP NHẬT ban học
    public function dangKyBanHoc($maHocSinh, $maBan) {
        $this->conn->beginTransaction();
        
        try {
            // Kiểm tra xem học sinh đã đăng ký chưa
            $sqlCheck = "SELECT id, maBan FROM dangkybanhoc WHERE maHocSinh = ?";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([$maHocSinh]);
            $existingRegistration = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($existingRegistration) {
                // ĐÃ ĐĂNG KÝ RỒI: CẬP NHẬT ban học
                $oldMaBan = $existingRegistration['maBan'];
                
                if ($oldMaBan == $maBan) {
                    // Nếu chọn cùng ban, không làm gì
                    $this->conn->rollBack();
                    return "same"; // Không thay đổi
                }
                
                // 1. Giảm số lượng đăng ký của ban cũ
                $sqlDecrease = "UPDATE banhoc SET soLuongDaDangKy = soLuongDaDangKy - 1 WHERE maBan = ?";
                $stmtDecrease = $this->conn->prepare($sqlDecrease);
                $stmtDecrease->execute([$oldMaBan]);
                
                // 2. Cập nhật đăng ký mới
                $sqlUpdate = "UPDATE dangkybanhoc SET maBan = ?, ngayDangKy = NOW() WHERE maHocSinh = ?";
                $stmtUpdate = $this->conn->prepare($sqlUpdate);
                $stmtUpdate->execute([$maBan, $maHocSinh]);
                
                // 3. Tăng số lượng đăng ký của ban mới
                $sqlIncrease = "UPDATE banhoc SET soLuongDaDangKy = soLuongDaDangKy + 1 WHERE maBan = ?";
                $stmtIncrease = $this->conn->prepare($sqlIncrease);
                $stmtIncrease->execute([$maBan]);
                
                $this->conn->commit();
                return "updated"; // Cập nhật thành công
            } else {
                // CHƯA ĐĂNG KÝ: THÊM MỚI
                $sqlInsert = "INSERT INTO dangkybanhoc (maHocSinh, maBan) VALUES (?, ?)";
                $stmtInsert = $this->conn->prepare($sqlInsert);
                $stmtInsert->execute([$maHocSinh, $maBan]);
                
                // Cập nhật số lượng đã đăng ký trong bảng banhoc
                $sqlUpdateBan = "UPDATE banhoc SET soLuongDaDangKy = soLuongDaDangKy + 1 WHERE maBan = ?";
                $stmtUpdateBan = $this->conn->prepare($sqlUpdateBan);
                $stmtUpdateBan->execute([$maBan]);
                
                $this->conn->commit();
                return "created"; // Tạo mới thành công
            }
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi đăng ký ban học: " . $e->getMessage());
            return false;
        }
    }
    
    // 6. Lấy thông tin đăng ký của học sinh
    public function getThongTinDangKy($maHocSinh) {
        $sql = "SELECT dk.*, bh.tenBan, bh.chiTieu, bh.soLuongDaDangKy
                FROM dangkybanhoc dk 
                JOIN banhoc bh ON dk.maBan = bh.maBan 
                WHERE dk.maHocSinh = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 7. Kiểm tra học sinh có thể chọn lại (thời hạn đăng ký)
    public function kiemTraThoiHanDangKy() {
        // Giả sử thời hạn đăng ký
        $now = date('Y-m-d');
        $startDate = '2025-12-15';
        $endDate = '2025-12-31';
        
        return ($now >= $startDate && $now <= $endDate);
    }
}
?>