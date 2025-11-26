<?php
require_once 'models/Database.php';

class GiaoVienModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy thông tin giáo viên theo mã người dùng
    public function getGiaoVienByNguoiDung($maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT gv.*, nd.hoTen, nd.ngaySinh, nd.gioiTinh, nd.soDienThoai, nd.email, nd.diaChi,
                       tt.toChuyenMon
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN totruongchuyenmon tt ON gv.maToTruong = tt.maToTruong
                WHERE nd.maNguoiDung = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

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

    // Lấy thông tin giáo viên theo mã giáo viên
    public function getGiaoVienById($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT gv.*, nd.hoTen, nd.ngaySinh, nd.gioiTinh, nd.soDienThoai, nd.email, nd.diaChi,
                       tt.toChuyenMon
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN totruongchuyenmon tt ON gv.maToTruong = tt.maToTruong
                WHERE gv.maGiaoVien = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách tất cả giáo viên
    public function getAllGiaoVien() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT gv.*, nd.hoTen, nd.soDienThoai, nd.email, tt.toChuyenMon,
                       (SELECT COUNT(*) FROM phanconggiangday pc WHERE pc.maGiaoVien = gv.maGiaoVien) as soLopPhuTrach
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN totruongchuyenmon tt ON gv.maToTruong = tt.maToTruong
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách giáo viên chủ nhiệm
    public function getGiaoVienChuNhiem() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT gv.*, nd.hoTen, l.tenLop, l.maLop
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                JOIN lophoc l ON gv.maGiaoVien = l.maGiaoVien
                WHERE gv.loaiGiaoVien = 'GV_CHU_NHIEM'
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách lớp mà giáo viên phụ trách
    public function getLopByGiaoVien($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, pc.loaiPhanCong, mh.tenMonHoc
                FROM phanconggiangday pc
                JOIN lophoc l ON pc.maLop = l.maLop
                LEFT JOIN monhoc mh ON pc.maMonHoc = mh.maMonHoc
                WHERE pc.maGiaoVien = ? AND pc.trangThai = 'Hoạt động'
                ORDER BY l.tenLop";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách môn học mà giáo viên giảng dạy
    public function getMonHocByGiaoVien($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT DISTINCT mh.*
                FROM phanconggiangday pc
                JOIN monhoc mh ON pc.maMonHoc = mh.maMonHoc
                WHERE pc.maGiaoVien = ? AND pc.trangThai = 'Hoạt động'
                ORDER BY mh.tenMonHoc";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin phân công giảng dạy của giáo viên
    public function getPhanCongGiangDay($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT pc.*, l.tenLop, mh.tenMonHoc, nk.namHoc
                FROM phanconggiangday pc
                JOIN lophoc l ON pc.maLop = l.maLop
                JOIN monhoc mh ON pc.maMonHoc = mh.maMonHoc
                JOIN nienkhoa nk ON pc.maNienKhoa = nk.maNienKhoa
                WHERE pc.maGiaoVien = ? AND pc.trangThai = 'Hoạt động'
                ORDER BY l.tenLop, mh.tenMonHoc";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy lịch dạy trong tuần của giáo viên
    public function getLichDayTrongTuan($maGiaoVien, $tuan = null) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT tkb.*, mh.tenMonHoc, l.tenLop, l.maLop
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                JOIN lophoc l ON mh.maKhoi = l.maKhoi
                WHERE tkb.maGiaoVien = ?
                AND (? IS NULL OR tkb.ngayApDung >= ?)
                ORDER BY tkb.loaiLich, tkb.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        
        if ($tuan) {
            $ngayDauTuan = date('Y-m-d', strtotime($tuan));
            $stmt->execute([$maGiaoVien, $ngayDauTuan, $ngayDauTuan]);
        } else {
            $stmt->execute([$maGiaoVien, null, null]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm giáo viên mới
    public function themGiaoVien($data) {
        $conn = $this->db->getConnection();
        
        $sql = "INSERT INTO giaovien (maNguoiDung, chuyenMon, loaiGiaoVien, maToTruong) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            $data['maNguoiDung'],
            $data['chuyenMon'],
            $data['loaiGiaoVien'],
            $data['maToTruong'] ?? null
        ]);
    }

    // Cập nhật thông tin giáo viên
    public function capNhatGiaoVien($maGiaoVien, $data) {
        $conn = $this->db->getConnection();
        
        $sql = "UPDATE giaovien 
                SET chuyenMon = ?, loaiGiaoVien = ?, maToTruong = ? 
                WHERE maGiaoVien = ?";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            $data['chuyenMon'],
            $data['loaiGiaoVien'],
            $data['maToTruong'] ?? null,
            $maGiaoVien
        ]);
    }

    // Xóa giáo viên
    public function xoaGiaoVien($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        // Kiểm tra xem giáo viên có đang được phân công không
        $sqlCheck = "SELECT COUNT(*) as count FROM phanconggiangday WHERE maGiaoVien = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->execute([$maGiaoVien]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            return false; // Không thể xóa vì có phân công
        }
        
        $sql = "DELETE FROM giaovien WHERE maGiaoVien = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maGiaoVien]);
    }

    // Lấy thống kê giảng dạy của giáo viên
    public function getThongKeGiangDay($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                COUNT(DISTINCT pc.maLop) as soLop,
                COUNT(DISTINCT pc.maMonHoc) as soMonHoc,
                (SELECT COUNT(*) FROM thoikhoabieu WHERE maGiaoVien = ?) as soTietTrongTuan,
                (SELECT COUNT(*) FROM diem WHERE maGiaoVien = ?) as soDiemDaNhap
                FROM phanconggiangday pc
                WHERE pc.maGiaoVien = ? AND pc.trangThai = 'Hoạt động'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien, $maGiaoVien, $maGiaoVien]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách tổ trưởng chuyên môn
    public function getToTruongChuyenMon() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT tt.*, 
                       (SELECT COUNT(*) FROM giaovien g WHERE g.maToTruong = tt.maToTruong) as soGiaoVien
                FROM totruongchuyenmon tt
                ORDER BY tt.toChuyenMon";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy học sinh theo lớp mà giáo viên chủ nhiệm
    public function getHocSinhTheoLopChuNhiem($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, nd.hoTen, nd.ngaySinh, nd.gioiTinh, nd.soDienThoai
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                JOIN lophoc l ON hs.maLop = l.maLop
                WHERE l.maGiaoVien = ? AND hs.trangThai = 'DANG_HOC'
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy điểm số của học sinh trong các lớp mà giáo viên giảng dạy
    public function getDiemHocSinhByGiaoVien($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT d.*, mh.tenMonHoc, nd.hoTen as tenHocSinh, l.tenLop
                FROM diem d
                JOIN monhoc mh ON d.maMonHoc = mh.maMonHoc
                JOIN hocsinh hs ON d.maHocSinh = hs.maHocSinh
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                JOIN lophoc l ON hs.maLop = l.maLop
                JOIN phanconggiangday pc ON (pc.maLop = l.maLop AND pc.maMonHoc = mh.maMonHoc)
                WHERE pc.maGiaoVien = ? AND d.maGiaoVien = ?
                ORDER BY l.tenLop, nd.hoTen, d.loaiDiem";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien, $maGiaoVien]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


        ///////////// Yến //////////////
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
    // private function getGiaoVienById($maGiaoVien) {
    //     $conn = $this->db->getConnection();
    //     $sql = "SELECT gv.maGiaoVien, nd.hoTen, gv.chuyenMon 
    //             FROM giaovien gv
    //             JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
    //             WHERE gv.maGiaoVien = ?";
    //     try {
    //         $stmt = $conn->prepare($sql);
    //         $stmt->execute([$maGiaoVien]);
    //         return $stmt->fetch(PDO::FETCH_ASSOC);
    //     } catch (PDOException $e) {
    //         return ['hoTen' => 'Giáo viên #' . $maGiaoVien];
    //     }
    // }
    /**
     * Lấy danh sách tất cả Khối
     */
    public function getAllKhoi() {
        $conn = $this->db->getConnection();
        $sql = "SELECT maKhoi, tenKhoi FROM khoi ORDER BY tenKhoi ASC";
        try {
            $stmt = $conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            error_log("Lỗi lấy danh sách khối: " . $e->getMessage());
            return []; 
        }
    }
    
    /**
     * Lấy danh sách Lớp, có thể lọc theo Khối
     */
    public function getAllClassesByKhoi($maKhoi = null) {
        $conn = $this->db->getConnection();
        $sql = "SELECT maLop, tenLop, maGiaoVien FROM lophoc WHERE 1=1";
        $params = [];
        
        if ($maKhoi && $maKhoi !== 'all') {
            $sql .= " AND maKhoi = :maKhoi";
            $params[':maKhoi'] = $maKhoi;
        }
        
        $sql .= " ORDER BY tenLop ASC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { 
            error_log("Lỗi lấy danh sách lớp theo khối: " . $e->getMessage());
            return []; 
        }
    }

    // Thêm vào GiaoVienModel.php

// Lấy giáo viên chủ nhiệm theo lớp
public function getGiaoVienChuNhiemByLop($maLop) {
    $conn = $this->db->getConnection();
    
    $sql = "SELECT gv.maGiaoVien, nd.hoTen, 'GVCN' as loaiGiaoVien
            FROM giaovien gv
            JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
            JOIN lophoc l ON gv.maGiaoVien = l.maGiaoVien
            WHERE l.maLop = ?";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Lỗi lấy GVCN theo lớp: " . $e->getMessage());
        return [];
    }
}

// Lấy giáo viên bộ môn theo lớp
public function getGiaoVienBoMonByLop($maLop) {
    $conn = $this->db->getConnection();
    
    $sql = "SELECT DISTINCT gv.maGiaoVien, nd.hoTen, mh.tenMonHoc as loaiGiaoVien
            FROM giaovien gv
            JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
            JOIN phanconggiangday pc ON gv.maGiaoVien = pc.maGiaoVien
            JOIN monhoc mh ON pc.maMonHoc = mh.maMonHoc
            WHERE pc.maLop = ? AND pc.trangThai = 'Hoạt động'";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Lỗi lấy GVBM theo lớp: " . $e->getMessage());
        return [];
    }
}

}
?>