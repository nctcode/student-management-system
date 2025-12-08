<?php
class HanhKiemModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách học sinh của lớp chủ nhiệm
    public function getHocSinhByLopChuNhiem($maGiaoVien, $hocKy) {
        // Nếu maGiaoVien là null, trả về mảng rỗng
        if ($maGiaoVien === null) {
            return [];
        }
        
        $query = "SELECT hs.maHocSinh, nd.hoTen, nd.ngaySinh, nd.gioiTinh, 
                         l.tenLop, l.maLop,
                         hk.id AS maHanhKiem, hk.hoc_ky, hk.diem_so, hk.xep_loai, hk.nhan_xet, hk.created_at
                  FROM hocsinh hs
                  JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                  JOIN lophoc l ON hs.maLop = l.maLop
                  LEFT JOIN hanh_kiem hk ON hs.maHocSinh = hk.sinh_vien_id 
                      AND hk.hoc_ky = :hocKy
                  WHERE l.maGiaoVien = :maGiaoVien
                  ORDER BY nd.hoTen ASC";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            // Sửa lỗi: Không thể bind null value trực tiếp
            // Cần kiểm tra và sử dụng giá trị mặc định
            $maGiaoVienValue = $maGiaoVien ?: 0;
            $hocKyValue = $hocKy ?: 'HK1-2024';
            
            $stmt->bindParam(':maGiaoVien', $maGiaoVienValue, PDO::PARAM_INT);
            $stmt->bindParam(':hocKy', $hocKyValue, PDO::PARAM_STR);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Lỗi truy vấn getHocSinhByLopChuNhiem: " . $e->getMessage());
            return [];
        }
    }

    // Lấy thông tin lớp chủ nhiệm của giáo viên
    public function getLopChuNhiem($maGiaoVien) {
        if ($maGiaoVien === null) {
            return null;
        }
        
        $query = "SELECT l.*, k.tenKhoi, nk.namHoc,
                         CONCAT('Học kỳ ', 
                                CASE nk.hocKy 
                                    WHEN 'HK1' THEN '1' 
                                    WHEN 'HK2' THEN '2' 
                                    ELSE 'Cả năm' 
                                END
                         ) AS ten_hoc_ky
                  FROM lophoc l
                  LEFT JOIN khoi k ON l.maKhoi = k.maKhoi
                  LEFT JOIN nienkhoa nk ON l.maNienKhoa = nk.maNienKhoa
                  WHERE l.maGiaoVien = :maGiaoVien
                  LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $maGiaoVienValue = $maGiaoVien ?: 0;
            $stmt->bindParam(':maGiaoVien', $maGiaoVienValue, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Lỗi truy vấn getLopChuNhiem: " . $e->getMessage());
            return null;
        }
    }

    // Cập nhật hoặc thêm điểm hạnh kiểm
    public function saveHanhKiem($maHocSinh, $hocKy, $diemSo, $xepLoai, $nhanXet) {
        // Kiểm tra xem đã có bản ghi chưa
        $checkQuery = "SELECT id FROM hanh_kiem 
                       WHERE sinh_vien_id = :maHocSinh AND hoc_ky = :hocKy";
        
        try {
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':maHocSinh', $maHocSinh, PDO::PARAM_INT);
            $stmt->bindParam(':hocKy', $hocKy, PDO::PARAM_STR);
            $stmt->execute();
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                // Update
                $query = "UPDATE hanh_kiem 
                          SET diem_so = :diemSo, xep_loai = :xepLoai, nhan_xet = :nhanXet, 
                              created_at = NOW()
                          WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $existing['id'], PDO::PARAM_INT);
            } else {
                // Insert
                $query = "INSERT INTO hanh_kiem (sinh_vien_id, hoc_ky, diem_so, xep_loai, nhan_xet) 
                          VALUES (:maHocSinh, :hocKy, :diemSo, :xepLoai, :nhanXet)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':maHocSinh', $maHocSinh, PDO::PARAM_INT);
                $stmt->bindParam(':hocKy', $hocKy, PDO::PARAM_STR);
            }
            
            $stmt->bindParam(':diemSo', $diemSo, PDO::PARAM_INT);
            $stmt->bindParam(':xepLoai', $xepLoai, PDO::PARAM_STR);
            $stmt->bindParam(':nhanXet', $nhanXet, PDO::PARAM_STR);
            
            return $stmt->execute();
            
        } catch(PDOException $e) {
            error_log("Lỗi truy vấn saveHanhKiem: " . $e->getMessage());
            return false;
        }
    }

    // Xóa điểm hạnh kiểm
    public function delete($id) {
        $query = "DELETE FROM hanh_kiem WHERE id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Lỗi truy vấn delete: " . $e->getMessage());
            return false;
        }
    }

    // Kiểm tra xem giáo viên có phải là GVCN của học sinh không
    public function isGiaoVienChuNhiem($maGiaoVien, $maHocSinh) {
        if ($maGiaoVien === null) {
            return false;
        }
        
        $query = "SELECT 1 
                  FROM hocsinh hs
                  JOIN lophoc l ON hs.maLop = l.maLop
                  WHERE hs.maHocSinh = :maHocSinh 
                  AND l.maGiaoVien = :maGiaoVien";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maHocSinh', $maHocSinh, PDO::PARAM_INT);
            $maGiaoVienValue = $maGiaoVien ?: 0;
            $stmt->bindParam(':maGiaoVien', $maGiaoVienValue, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Lỗi truy vấn isGiaoVienChuNhiem: " . $e->getMessage());
            return false;
        }
    }
}
?>