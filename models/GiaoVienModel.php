<?php
require_once 'models/Database.php';

class GiaoVienModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Lấy mã Giáo viên (maGiaoVien) từ mã Người dùng (maNguoiDung) - ĐÃ CẬP NHẬT
     */
    public function getMaGiaoVien($maNguoiDung) {
        $conn = $this->db->getConnection();
        $sql = "SELECT gv.maGiaoVien 
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE nd.maNguoiDung = :maNguoiDung AND nd.loaiNguoiDung = 'GIAOVIEN'";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maNguoiDung' => $maNguoiDung]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['maGiaoVien'] ?? null;
        } catch (PDOException $e) {
            error_log("Lỗi lấy mã giáo viên: " . $e->getMessage());
            return null;
        }
    }

    // *******************************************************
    // CÁC HÀM GỐC - LOẠI BỎ MATRUONG
    // *******************************************************
    
    // Lấy tất cả Giáo viên
    public function getAllTeachers() {
        $conn = $this->db->getConnection();
        $sql = "SELECT gv.maGiaoVien, nd.hoTen, gv.chuyenMon 
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                ORDER BY nd.hoTen ASC";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            error_log("Lỗi lấy danh sách giáo viên: " . $e->getMessage());
            return []; 
        }
    }

    // Lấy danh sách các Lớp
    public function getAllClasses() {
        $conn = $this->db->getConnection();
        $sql = "SELECT maLop, tenLop, maGiaoVien FROM lophoc ORDER BY tenLop ASC";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            error_log("Lỗi lấy danh sách lớp: " . $e->getMessage());
            return []; 
        }
    }
    
    // Lấy danh sách các Môn học
    public function getAllSubjects() {
        $conn = $this->db->getConnection();
        $sql = "SELECT maMonHoc, tenMonHoc FROM monhoc ORDER BY tenMonHoc ASC";
        try {
            $stmt = $conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            error_log("Lỗi lấy danh sách môn học: " . $e->getMessage());
            return []; 
        }
    }
    
    // Lấy phân công GVBM hiện tại của một lớp
    public function getSubjectAssignmentsByClass($maLop) {
        $conn = $this->db->getConnection();
        $sql = "SELECT pc.maPhanCong, mh.tenMonHoc, nd.hoTen AS tenGiaoVien
                FROM phanconggiangday pc
                JOIN monhoc mh ON pc.maMonHoc = mh.maMonHoc
                JOIN giaovien gv ON pc.maGiaoVien = gv.maGiaoVien
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE pc.maLop = :maLop";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maLop' => $maLop]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            error_log("Lỗi lấy phân công GVBM: " . $e->getMessage());
            return []; 
        }
    }

    // *******************************************************
    // HÀM THỐNG KÊ - LOẠI BỎ MATRUONG
    // *******************************************************

    /**
     * Lấy số lượng lớp học thực tế
     */
    public function getTotalClasses() {
        $conn = $this->db->getConnection();
        $sql = "SELECT COUNT(*) as total FROM lophoc";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Lỗi lấy tổng lớp: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy số lượng giáo viên thực tế
     */
    public function getTotalTeachers() {
        $conn = $this->db->getConnection();
        $sql = "SELECT COUNT(*) as total FROM giaovien";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Lỗi lấy tổng giáo viên: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy số lượng lớp đã có GVCN
     */
    public function getClassesWithGVCN() {
        $conn = $this->db->getConnection();
        $sql = "SELECT COUNT(*) as total FROM lophoc WHERE maGiaoVien IS NOT NULL AND maGiaoVien != '' AND maGiaoVien != 0";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Lỗi lấy lớp có GVCN: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy phân công GVCN hiện tại
     */
    public function getCurrentGVCNAssignments() {
        $conn = $this->db->getConnection();
        $sql = "SELECT l.maLop, l.tenLop, gv.maGiaoVien, nd.hoTen AS tenGV
                FROM lophoc l
                LEFT JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                ORDER BY l.tenLop";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy phân công GVCN: " . $e->getMessage());
            return [];
        }
    }

    // *******************************************************
    // LOGIC KIỂM TRA VÀ XỬ LÝ PHÂN CÔNG
    // *******************************************************

    /**
     * Kiểm tra xem giáo viên có đang chủ nhiệm lớp khác không
     */
    public function checkExistingGVCN($maGiaoVien, $maLopHienTai) {
        $conn = $this->db->getConnection();
        $sql = "SELECT tenLop FROM lophoc WHERE maGiaoVien = :maGV AND maLop != :maLop";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':maGV' => $maGiaoVien, ':maLop' => $maLopHienTai]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra chuyên môn của GV
     */
    public function checkGVChuyenMon($maGiaoVien, $maMonHoc) {
        $conn = $this->db->getConnection();
        
        // Cách 1: Sử dụng bảng chuyên môn nếu có
        $sql = "SELECT COUNT(*) as count FROM giaovien_monhoc 
                WHERE maGiaoVien = :maGV AND maMonHoc = :maMon";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maGV' => $maGiaoVien, ':maMon' => $maMonHoc]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                return true;
            }
        } catch (PDOException $e) {
            // Nếu bảng không tồn tại, dùng cách so sánh chuỗi
        }

        // Cách 2: So sánh với cột chuyenMon
        $sql = "SELECT gv.chuyenMon, mh.tenMonHoc 
                FROM giaovien gv, monhoc mh
                WHERE gv.maGiaoVien = :maGV AND mh.maMonHoc = :maMon";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':maGV' => $maGiaoVien, ':maMon' => $maMonHoc]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        $chuyenMon = $result['chuyenMon'] ?? '';
        $tenMonHoc = $result['tenMonHoc'] ?? '';
        
        if (empty($chuyenMon)) {
            return false;
        }

        // Kiểm tra nếu chuyên môn chứa tên môn học
        if (stripos($chuyenMon, $tenMonHoc) !== false) {
            return true;
        }
        
        return false;
    }

    /**
     * Thực hiện toàn bộ logic phân công GVCN và GVBM
     */
    public function processAssignment($maLop, $maGVCN, $assignments) {
        $conn = $this->db->getConnection();
        
        // --- 1. KIỂM TRA TRƯỚC KHI BẮT ĐẦU TRANSACTION ---
        
        // 1a. Kiểm tra GVCN trùng lặp
        $lopGVCNCu = $this->checkExistingGVCN($maGVCN, $maLop);
        if ($lopGVCNCu) {
            return ['error' => 'GVCN_DUPLICATE', 'lop' => $lopGVCNCu['tenLop']];
        }

        // 1b. Kiểm tra chuyên môn GVBM
        $errorsGVBM = [];
        foreach ($assignments as $assign) {
            if (!empty($assign['maMonHoc']) && !empty($assign['maGiaoVien'])) {
                if (!$this->checkGVChuyenMon($assign['maGiaoVien'], $assign['maMonHoc'])) {
                    $monHoc = $this->getMonHocById($assign['maMonHoc']);
                    $giaoVien = $this->getGiaoVienById($assign['maGiaoVien']);
                    
                    $errorsGVBM[] = [
                        'monHoc' => $monHoc['tenMonHoc'] ?? 'Môn học #' . $assign['maMonHoc'],
                        'giaoVien' => $giaoVien['hoTen'] ?? 'Giáo viên #' . $assign['maGiaoVien'],
                        'chuyenMon' => $giaoVien['chuyenMon'] ?? ''
                    ];
                }
            }
        }
        if (!empty($errorsGVBM)) {
            return ['error' => 'GVBM_INVALID_CONDITION', 'details' => $errorsGVBM];
        }

        // --- 2. THỰC THI TRANSACTION ---
        
        try {
            $conn->beginTransaction();

            // 2a. Phân công GVCN (UPDATE lophoc)
            $sql_gvcn = "UPDATE lophoc SET maGiaoVien = :maGVCN WHERE maLop = :maLop";
            $stmt_gvcn = $conn->prepare($sql_gvcn);
            $stmt_gvcn->execute([
                ':maGVCN' => $maGVCN, 
                ':maLop' => $maLop
            ]);

            // 2b. Xóa phân công GVBM cũ của lớp này
            $sql_delete = "DELETE FROM phanconggiangday WHERE maLop = :maLop";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->execute([':maLop' => $maLop]);

            // 2c. Phân công GVBM mới (INSERT phanconggiangday)
            $sql_insert = "INSERT INTO phanconggiangday (maLop, maGiaoVien, maMonHoc) VALUES (:maLop, :maGV, :maMon)";
            $stmt_insert = $conn->prepare($sql_insert);

            foreach ($assignments as $assign) {
                if (!empty($assign['maMonHoc']) && !empty($assign['maGiaoVien'])) {
                    $stmt_insert->execute([
                        ':maLop' => $maLop,
                        ':maGV' => $assign['maGiaoVien'],
                        ':maMon' => $assign['maMonHoc']
                    ]);
                }
            }

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Lỗi thực hiện phân công: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy thông tin môn học theo ID
     */
    private function getMonHocById($maMonHoc) {
        $conn = $this->db->getConnection();
        $sql = "SELECT tenMonHoc FROM monhoc WHERE maMonHoc = ?";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maMonHoc]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['tenMonHoc' => 'Môn học #' . $maMonHoc];
        }
    }

    /**
     * Lấy thông tin giáo viên theo ID
     */
    private function getGiaoVienById($maGiaoVien) {
        $conn = $this->db->getConnection();
        $sql = "SELECT gv.maGiaoVien, nd.hoTen, gv.chuyenMon 
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE gv.maGiaoVien = ?";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maGiaoVien]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['hoTen' => 'Giáo viên #' . $maGiaoVien];
        }
    }
}
?>