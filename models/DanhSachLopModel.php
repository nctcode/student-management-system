<?php
require_once 'models/Database.php';

class DanhSachLopModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Lấy danh sách lớp mà giáo viên phụ trách (GVCN hoặc GVBM) - ĐÃ TỐI ƯU
     */
    public function getLopCuaGiaoVien($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, 
                CASE 
                    WHEN l.maGiaoVien = :maGiaoVien THEN 1 
                    ELSE 0 
                END as isGVCN
                FROM lophoc l
                WHERE l.maGiaoVien = :maGiaoVien 
                   OR EXISTS (
                       SELECT 1 FROM phanconggiangday 
                       WHERE maGiaoVien = :maGiaoVien AND maLop = l.maLop
                   )
                ORDER BY isGVCN DESC, l.tenLop";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maGiaoVien' => $maGiaoVien]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách lớp: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy thông tin lớp
     */
    public function getThongTinLop($maLop) {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM lophoc WHERE maLop = ?";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maLop]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông tin lớp: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy số lượng học sinh trong lớp
     */
    public function getSoHocSinh($maLop) {
        $conn = $this->db->getConnection();
        $sql = "SELECT COUNT(*) as total FROM hocsinh WHERE maLop = ?";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maLop]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Lỗi lấy số học sinh: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy danh sách học sinh trong lớp (ĐÃ CẬP NHẬT theo cấu trúc nguoidung) - ĐẦY ĐỦ cho GVCN
     */
    public function getHocSinhByLop($maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    hs.maHocSinh, 
                    nd.hoTen, 
                    nd.ngaySinh, 
                    nd.gioiTinh, 
                    hs.trangThai, 
                    l.tenLop,
                    nd.CCCD
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                JOIN lophoc l ON hs.maLop = l.maLop
                WHERE hs.maLop = :maLop
                ORDER BY nd.hoTen";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maLop' => $maLop]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách học sinh: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách học sinh cơ bản (cho GVBM) - CHỈ HIỂN THỊ THÔNG TIN CƠ BẢN
     */
    public function getHocSinhBasicByLop($maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    hs.maHocSinh, 
                    nd.hoTen,
                    l.tenLop
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                JOIN lophoc l ON hs.maLop = l.maLop
                WHERE hs.maLop = :maLop
                ORDER BY nd.hoTen";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maLop' => $maLop]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách học sinh cơ bản: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin chi tiết học sinh (ĐÃ CẬP NHẬT theo cấu trúc nguoidung)
     */
    public function getChiTietHocSinh($maHocSinh) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    hs.maHocSinh, 
                    nd_hs.hoTen, 
                    nd_hs.ngaySinh, 
                    nd_hs.gioiTinh, 
                    nd_hs.diaChi, 
                    nd_hs.email, 
                    nd_hs.soDienThoai,
                    nd_hs.CCCD,
                    nd_ph.hoTen as tenPhuHuynh, 
                    nd_ph.soDienThoai as sdtPhuHuynh, 
                    nd_ph.email as emailPhuHuynh,
                    ph.ngheNghiep, 
                    ph.moiQuanHe,
                    l.tenLop, 
                    l.maLop
                FROM hocsinh hs
                LEFT JOIN nguoidung nd_hs ON hs.maNguoiDung = nd_hs.maNguoiDung
                LEFT JOIN phuhuynh ph ON hs.maPhuHuynh = ph.maPhuHuynh
                LEFT JOIN nguoidung nd_ph ON ph.maNguoiDung = nd_ph.maNguoiDung
                LEFT JOIN lophoc l ON hs.maLop = l.maLop 
                WHERE hs.maHocSinh = :maHocSinh";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maHocSinh' => $maHocSinh]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy chi tiết học sinh: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy điểm số của học sinh (TẤT CẢ MÔN - cho GVCN)
     */
    public function getDiemHocSinh($maHocSinh) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT d.maMonHoc, mh.tenMonHoc, d.diemSo, d.loaiDiem, d.hocKy, d.namHoc
                FROM diem d
                JOIN monhoc mh ON d.maMonHoc = mh.maMonHoc
                WHERE d.maHocSinh = :maHocSinh
                ORDER BY d.hocKy, d.namHoc, mh.tenMonHoc";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maHocSinh' => $maHocSinh]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy điểm học sinh: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy điểm số của học sinh theo môn GV dạy (cho GVBM)
     */
    public function getDiemHocSinhByMonGiaoVien($maHocSinh, $maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT d.maMonHoc, mh.tenMonHoc, d.diemSo, d.loaiDiem, d.hocKy, d.namHoc
                FROM diem d
                JOIN monhoc mh ON d.maMonHoc = mh.maMonHoc
                WHERE d.maHocSinh = :maHocSinh
                AND mh.maMonHoc IN (
                    SELECT pc.maMonHoc 
                    FROM phanconggiangday pc
                    WHERE pc.maGiaoVien = :maGiaoVien
                )
                ORDER BY d.hocKy, d.namHoc, mh.tenMonHoc";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':maHocSinh' => $maHocSinh,
                ':maGiaoVien' => $maGiaoVien
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy điểm học sinh theo môn GV: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chuyên cần của học sinh (SỬA LẠI JOIN BẢNG)
     */
    public function getChuyenCanHocSinh($maHocSinh) {
        $conn = $this->db->getConnection();
        
        // SỬA: JOIN với bảng buoihoc để lấy ngayHoc
        $sql = "SELECT bh.ngayHoc, cc.trangThai, cc.ghiChu
                FROM chuyencan cc
                JOIN buoihoc bh ON cc.maBuoiHoc = bh.maBuoiHoc
                WHERE cc.maHocSinh = :maHocSinh
                ORDER BY bh.ngayHoc DESC
                LIMIT 30"; 
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maHocSinh' => $maHocSinh]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy chuyên cần học sinh: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra giáo viên có quyền xem lớp không
     */
    public function checkGiaoVienCoQuyenXemLop($maGiaoVien, $maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count 
                FROM lophoc l
                WHERE l.maLop = :maLop 
                  AND (l.maGiaoVien = :maGiaoVien 
                    OR EXISTS (
                        SELECT 1 FROM phanconggiangday 
                        WHERE maGiaoVien = :maGiaoVien AND maLop = :maLop
                    )
                  )";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':maLop' => $maLop,
                ':maGiaoVien' => $maGiaoVien
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra quyền giáo viên: " . $e->getMessage());
            return false;
        }
    }

    /**
     * KIỂM TRA GIÁO VIÊN CÓ PHẢI LÀ GVCN CỦA LỚP KHÔNG
     */
    public function checkGiaoVienLaGVCN($maGiaoVien, $maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count 
                FROM lophoc 
                WHERE maLop = :maLop AND maGiaoVien = :maGiaoVien";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':maLop' => $maLop,
                ':maGiaoVien' => $maGiaoVien
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra GVCN: " . $e->getMessage());
            return false;
        }
    }
}
?>