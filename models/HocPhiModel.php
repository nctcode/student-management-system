<?php
require_once 'Database.php';    
class HocPhiModel {
    private $conn;
    private $table_name = "hocphi";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getHocPhiCanDong() {
        $query = "SELECT hp.*, nd.hoTen, l.tenLop 
                FROM " . $this->table_name . " hp
                INNER JOIN hocsinh hs ON hp.maHocSinh = hs.maHocSinh
                INNER JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON hs.maLop = l.maLop
                WHERE hp.trangThai IN ('CHUA_NOP', 'QUA_HAN') 
                ORDER BY hp.hanNop ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLichSuThanhToan() {
    // Giả sử lấy theo mã học sinh từ session
        $maHocSinh = $_SESSION['maHocSinh'] ?? 1; // Test với mã học sinh mẫu
        
        $query = "SELECT tt.*, hp.kyHoc, hp.thang, hp.namHoc
                FROM thanhtoan tt
                INNER JOIN hocphi hp ON tt.maHocPhi = hp.maHocPhi
                WHERE hp.maHocSinh = :maHocSinh
                ORDER BY tt.ngayGiaoDich DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maHocSinh', $maHocSinh);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTenKyHoc($kyHoc) {
        $kyHocMap = [
            'HK1' => 'Học kỳ 1',
            'HK2' => 'Học kỳ 2',
            'CA_NAM' => 'Cả năm'
        ];
        return $kyHocMap[$kyHoc] ?? $kyHoc;
    }
    
    public function xuLyThanhToan($maHocPhi, $phuongThuc) {
    try {
        // 1. Tạo mã giao dịch duy nhất
        $maGiaoDich = 'GD' . date('YmdHis') . rand(100, 999);
        
        // 2. Bắt đầu transaction
        $this->conn->beginTransaction();
        
        // 3. Lấy thông tin học phí
        $queryInfo = "SELECT soTien, kyHoc FROM hocphi WHERE maHocPhi = :maHocPhi";
        $stmtInfo = $this->conn->prepare($queryInfo);
        $stmtInfo->bindParam(':maHocPhi', $maHocPhi);
        $stmtInfo->execute();
        $hocPhiInfo = $stmtInfo->fetch(PDO::FETCH_ASSOC);
        
        if (!$hocPhiInfo) {
            throw new Exception("Không tìm thấy học phí");
        }
        
        $soTien = $hocPhiInfo['soTien'];
        $kyHoc = $hocPhiInfo['kyHoc'];
        
        // 4. Cập nhật trạng thái học phí thành "ĐÃ NỘP"
        $queryUpdate = "UPDATE hocphi SET trangThai = 'DA_NOP' WHERE maHocPhi = :maHocPhi";
        $stmtUpdate = $this->conn->prepare($queryUpdate);
        $stmtUpdate->bindParam(':maHocPhi', $maHocPhi);
        $stmtUpdate->execute();
        
        // 5. Thêm bản ghi thanh toán
        $queryInsert = "INSERT INTO thanhtoan 
                       (maGiaoDich, maHocPhi, tenGiaoDich, soTien, trangThai, phuongThuc) 
                       VALUES 
                       (:maGiaoDich, :maHocPhi, :tenGiaoDich, :soTien, 'THANH_CONG', :phuongThuc)";
        
        $stmtInsert = $this->conn->prepare($queryInsert);
        $tenGiaoDich = "Thanh toán học phí " . $this->getTenKyHoc($kyHoc);
        
        $stmtInsert->bindParam(':maGiaoDich', $maGiaoDich);
        $stmtInsert->bindParam(':maHocPhi', $maHocPhi);
        $stmtInsert->bindParam(':tenGiaoDich', $tenGiaoDich);
        $stmtInsert->bindParam(':soTien', $soTien);
        $stmtInsert->bindParam(':phuongThuc', $phuongThuc);
        $stmtInsert->execute();
        
        // 6. Commit transaction
        $this->conn->commit();
        
        return $maGiaoDich;
        
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $this->conn->rollBack();
        error_log("Lỗi thanh toán: " . $e->getMessage());
        return false;
    }
}

// Thêm method xử lý thanh toán tại trường
    public function taoPhieuThu($maHocPhi) {
        try {
            $query = "SELECT hp.*, nd.hoTen, l.tenLop 
                    FROM hocphi hp
                    INNER JOIN hocsinh hs ON hp.maHocSinh = hs.maHocSinh
                    INNER JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                    LEFT JOIN lophoc l ON hs.maLop = l.maLop
                    WHERE hp.maHocPhi = :maHocPhi";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maHocPhi', $maHocPhi);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Lỗi tạo phiếu thu: " . $e->getMessage());
            return false;
        }
    }

    // Thêm method lấy thông tin biên lai
    public function getThongTinBienLai($maGiaoDich) {
        try {
            $query = "SELECT tt.*, hp.kyHoc, hp.thang, hp.namHoc, 
                            nd.hoTen, l.tenLop, hs.maHocSinh
                    FROM thanhtoan tt
                    INNER JOIN hocphi hp ON tt.maHocPhi = hp.maHocPhi
                    INNER JOIN hocsinh hs ON hp.maHocSinh = hs.maHocSinh
                    INNER JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                    LEFT JOIN lophoc l ON hs.maLop = l.maLop
                    WHERE tt.maGiaoDich = :maGiaoDich";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maGiaoDich', $maGiaoDich);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Lỗi lấy thông tin biên lai: " . $e->getMessage());
            return false;
        }
    }
}
?>