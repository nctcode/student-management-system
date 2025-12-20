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

    // Lấy thông tin giáo viên theo mã giáo viên (PHIÊN BẢN CHÍNH)
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
        
        // CHÚ Ý: Hàm này trả về đầy đủ thông tin, đủ dùng cho cả truy cập ngoài và nội bộ
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
    // Đã sửa lỗi JOIN để lấy chính xác tên lớp từ maLop trong bảng thoikhoabieu
    public function getLichDayTrongTuan($maGiaoVien, $tuan = null) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT tkb.*, mh.tenMonHoc, l.tenLop, l.maLop
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                -- SỬA LỖI JOIN: JOIN lớp học bằng maLop có sẵn trong tkb
                JOIN lophoc l ON tkb.maLop = l.maLop
                WHERE tkb.maGiaoVien = ?
                AND (? IS NULL OR tkb.ngayApDung >= ?)
                ORDER BY tkb.loaiLich, tkb.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        
        if ($tuan) {
            // Lấy ngày đầu tiên của tuần (Thứ Hai)
            $ngayDauTuan = date('Y-m-d', strtotime($tuan . ' Monday'));
            $stmt->execute([$maGiaoVien, $ngayDauTuan, $ngayDauTuan]);
        } else {
            // Nếu không có tuần, chỉ lấy những TKB không lọc theo ngày
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
    
    // Thay thế hàm getAllTeachers cũ bằng hàm này
    public function getAllTeachers($maTruong = null) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    g.maGiaoVien, 
                    nd.hoTen, 
                    g.chuyenMon, 
                    g.maMonHoc,
                    m.tenMonHoc,
                    tt.maToTruong,
                    tt.toChuyenMon,
                    tt.maMonHoc as maMonTrucThuoc
                FROM giaovien g
                JOIN nguoidung nd ON g.maNguoiDung = nd.maNguoiDung
                LEFT JOIN monhoc m ON g.maMonHoc = m.maMonHoc
                LEFT JOIN totruongchuyenmon tt ON g.maToTruong = tt.maToTruong
                WHERE nd.loaiNguoiDung = 'GIAOVIEN'";
        
        if ($maTruong) {
            $sql .= " AND nd.maTruong = :maTruong";
        }
        
        $sql .= " ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        if ($maTruong) {
            $stmt->bindParam(':maTruong', $maTruong);
        }
        $stmt->execute();
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Số giáo viên lấy được: " . count($teachers) . " (maTruong: " . ($maTruong ?? 'null') . ")");
        
        return $teachers;
    }

    public function getTeachersBySubject($maMonHoc, $maTruong = null) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT DISTINCT 
                    g.maGiaoVien, 
                    nd.hoTen, 
                    g.chuyenMon,
                    g.maMonHoc,
                    tt.maToTruong,
                    tt.toChuyenMon
                FROM giaovien g
                JOIN nguoidung nd ON g.maNguoiDung = nd.maNguoiDung
                LEFT JOIN totruongchuyenmon tt ON g.maToTruong = tt.maToTruong
                LEFT JOIN totruongchuyenmon tt_subject ON tt_subject.maMonHoc = :maMonHoc
                WHERE nd.loaiNguoiDung = 'GIAOVIEN'
                AND (g.maMonHoc = :maMonHoc OR tt.maToTruong = tt_subject.maToTruong)";
        
        if ($maTruong) {
            $sql .= " AND nd.maTruong = :maTruong";
        }
        
        $sql .= " ORDER BY nd.hoTen";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':maMonHoc', $maMonHoc);
            if ($maTruong) {
                $stmt->bindParam(':maTruong', $maTruong);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy giáo viên theo môn: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin tổ chuyên môn theo môn học
     */
    public function getToChuyenMonByMonHoc($maMonHoc) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT tt.* FROM totruongchuyenmon tt WHERE tt.maMonHoc = ?";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maMonHoc]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy tổ chuyên môn: " . $e->getMessage());
            return null;
        }
    }


    // Lấy danh sách các Lớp
    // Trong GiaoVienModel.php, sửa hàm getAllClasses
    public function getAllClasses($maTruong = null) {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM lophoc WHERE 1=1";
        
        if ($maTruong) {
            $sql .= " AND maTruong = :maTruong";
        }
        
        // Sắp xếp theo khối và tên lớp
        $sql .= " ORDER BY maKhoi ASC, tenLop ASC";
        
        $stmt = $conn->prepare($sql);
        if ($maTruong) {
            $stmt->bindParam(':maTruong', $maTruong);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    // Thêm vào GiaoVienModel.php
/**
 * Lấy phân công GVCN của lớp
 */
    public function getGVCNAssignmentByClass($maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    pc.maGiaoVien,
                    nd.hoTen as tenGiaoVien
                FROM phanconggiangday pc
                JOIN giaovien g ON pc.maGiaoVien = g.maGiaoVien
                JOIN nguoidung nd ON g.maNguoiDung = nd.maNguoiDung
                WHERE pc.maLop = :maLop 
                AND pc.loaiPhanCong = 'GVCN'
                AND pc.trangThai = 'Hoạt động'
                LIMIT 1";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maLop' => $maLop]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy GVCN: " . $e->getMessage());
            return null;
        }
    }
    
   
    // Lấy phân công GVBM hiện tại của một lớp
    public function getSubjectAssignmentsByClass($maLop, $maTruong = null) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    pc.maMonHoc, 
                    m.tenMonHoc, 
                    pc.maGiaoVien, 
                    nd.hoTen as tenGiaoVien,
                    pc.loaiPhanCong,
                    nk.namHoc,
                    g.chuyenMon
                FROM phanconggiangday pc
                JOIN monhoc m ON pc.maMonHoc = m.maMonHoc
                JOIN giaovien g ON pc.maGiaoVien = g.maGiaoVien
                JOIN nguoidung nd ON g.maNguoiDung = nd.maNguoiDung
                LEFT JOIN nienkhoa nk ON pc.maNienKhoa = nk.maNienKhoa
                WHERE pc.maLop = :maLop 
                AND pc.trangThai = 'Hoạt động'
                AND pc.loaiPhanCong = 'GVBM'";
        
        if ($maTruong) {
            $sql .= " AND EXISTS (
                        SELECT 1 FROM lophoc l 
                        WHERE l.maLop = pc.maLop 
                        AND l.maTruong = :maTruong
                    )";
        }
        
        $sql .= " ORDER BY m.tenMonHoc";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':maLop', $maLop);
        if ($maTruong) {
            $stmt->bindParam(':maTruong', $maTruong);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $assignments = [];
        foreach ($results as $row) {
            $assignments[$row['maMonHoc']] = [
                'maGiaoVien' => $row['maGiaoVien'],
                'tenGiaoVien' => $row['tenGiaoVien'],
                'loaiPhanCong' => $row['loaiPhanCong'],
                'namHoc' => $row['namHoc'],
                'chuyenMon' => $row['chuyenMon']
            ];
        }
        
        // Ghi log để debug
        error_log("Số phân công GVBM tìm thấy cho lớp $maLop: " . count($assignments));
        
        return $assignments;
    }

    // *******************************************************
    // HÀM THỐNG KÊ - LOẠI BỎ MATRUONG
    // *******************************************************

    /**
     * Lấy số lượng lớp học thực tế
     */
    public function getTotalClasses($maTruong = null) {
        $conn = $this->db->getConnection();
        $sql = "SELECT COUNT(*) as total FROM lophoc WHERE 1=1";
        
        $params = [];
        if ($maTruong) {
            $sql .= " AND maTruong = :maTruong";
            $params[':maTruong'] = $maTruong;
        }
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
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
    public function getTotalTeachers($maTruong = null) {
        $conn = $this->db->getConnection();
        
        // Lấy giáo viên thuộc trường thông qua bảng nguoidung
        $sql = "SELECT COUNT(DISTINCT g.maGiaoVien) as total 
                FROM giaovien g
                JOIN nguoidung nd ON g.maNguoiDung = nd.maNguoiDung
                WHERE nd.maTruong IS NOT NULL";
        
        if ($maTruong) {
            $sql .= " AND nd.maTruong = :maTruong";
        }
        
        try {
            $stmt = $conn->prepare($sql);
            if ($maTruong) {
                $stmt->bindParam(':maTruong', $maTruong);
            }
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
    public function getClassesWithGVCN($maTruong = null) {
        $conn = $this->db->getConnection();
        $sql = "SELECT COUNT(*) as total FROM lophoc WHERE maGiaoVien IS NOT NULL";
        
        if ($maTruong) {
            $sql .= " AND maTruong = :maTruong";
        }
        
        try {
            $stmt = $conn->prepare($sql);
            if ($maTruong) {
                $stmt->bindParam(':maTruong', $maTruong);
            }
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
    // Tìm hàm này trong GiaoVienModel.php
    public function getCurrentGVCNAssignments($maTruong = null) {
        $conn = $this->db->getConnection();
        
        // SỬA LẠI CÂU SQL: thay nd.hoTen as tenGV
        $sql = "SELECT l.maLop, l.tenLop, g.maGiaoVien, nd.hoTen as tenGV 
                FROM lophoc l
                LEFT JOIN giaovien g ON l.maGiaoVien = g.maGiaoVien
                LEFT JOIN nguoidung nd ON g.maNguoiDung = nd.maNguoiDung
                WHERE 1=1";
        
        if ($maTruong) {
            $sql .= " AND l.maTruong = :maTruong";
        }
        
        $sql .= " ORDER BY l.tenLop";
        
        $stmt = $conn->prepare($sql);
        if ($maTruong) {
            $stmt->bindParam(':maTruong', $maTruong);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // *******************************************************
    // LOGIC KIỂM TRA VÀ XỬ LÝ PHÂN CÔNG
    // *******************************************************

    /**
     * Kiểm tra xem giáo viên có đang chủ nhiệm lớp khác không
     * CHỈ kiểm tra khi đang phân công GVCN mới
     */
    public function checkExistingGVCN($maGiaoVien, $maLopHienTai = null) {
        $conn = $this->db->getConnection();
        
        // Tìm lớp nào đang có giáo viên này làm GVCN
        $sql = "SELECT l.maLop, l.tenLop 
                FROM lophoc l
                WHERE l.maGiaoVien = :maGV";
        
        $params = [':maGV' => $maGiaoVien];
        
        if ($maLopHienTai) {
            $sql .= " AND l.maLop != :maLop";
            $params[':maLop'] = $maLopHienTai;
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
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
    // Thêm vào GiaoVienModel.php

    /**
     * Kiểm tra giáo viên đã là GVCN của lớp nào khác chưa
     * Trả về tên lớp nếu đã là GVCN, ngược lại trả về false
     */
    public function checkGVCNExisted($maGiaoVien, $maLopHienTai, $maTruong = null) {
        $conn = $this->db->getConnection();
        $sql = "SELECT l.tenLop 
                FROM lophoc l
                WHERE l.maGiaoVien = :maGiaoVien 
                AND l.maLop != :maLopHienTai";
        
        $params = [
            ':maGiaoVien' => $maGiaoVien,
            ':maLopHienTai' => $maLopHienTai
        ];

        if ($maTruong) {
            $sql .= " AND l.maTruong = :maTruong";
            $params[':maTruong'] = $maTruong;
        }

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result['tenLop'] : false;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra trùng GVCN: " . $e->getMessage());
            return false;
        }
    }

    // Tìm hàm processAssignment trong GiaoVienModel.php và sửa lại như sau:
    public function processAssignment($maLop, $maGVCN, $assignments, $maTruong = null) {
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
                // SỬ DỤNG HÀM NỘI BỘ getGiaoVienById ĐÃ ĐƯỢC XÓA Ở CUỐI, THAY THẾ BẰNG HÀM PUBLIC ĐỂ LẤY THÔNG TIN
                if (!$this->checkGVChuyenMon($assign['maGiaoVien'], $assign['maMonHoc'])) {
                    $monHoc = $this->getMonHocById($assign['maMonHoc']);
                    // Gọi hàm public getGiaoVienById để lấy thông tin
                    $giaoVien = $this->getGiaoVienById($assign['maGiaoVien']); 
                    
                    $errorsGVBM[] = [
                        'monHoc' => $monHoc['tenMonHoc'] ?? 'Môn học #' . $assign['maMonHoc'],
                        // Lấy hoTen và chuyenMon từ kết quả của hàm public getGiaoVienById
                        'giaoVien' => $giaoVien['hoTen'] ?? 'Giáo viên #' . $assign['maGiaoVien'],
                        'chuyenMon' => $giaoVien['chuyênMon'] ?? ''
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

            // Kiểm tra lớp có thuộc trường không
            if ($maTruong) {
                $checkLop = $conn->prepare("SELECT maLop FROM lophoc WHERE maLop = :maLop AND maTruong = :maTruong");
                $checkLop->bindParam(':maLop', $maLop);
                $checkLop->bindParam(':maTruong', $maTruong);
                $checkLop->execute();
                if ($checkLop->rowCount() === 0) {
                    $conn->rollBack();
                    return ['error' => 'Lớp không thuộc trường quản lý'];
                }
            }
            
            // Lấy mã năm học hiện tại
            $currentYear = $this->getCurrentNienKhoa();
            
            // 2a. Phân công GVCN (UPDATE lophoc)
            $sql_gvcn = "UPDATE lophoc SET maGiaoVien = :maGVCN WHERE maLop = :maLop";
            $stmt_gvcn = $conn->prepare($sql_gvcn);
            $stmt_gvcn->execute([
                ':maGVCN' => $maGVCN, 
                ':maLop' => $maLop
            ]);

            // 2b. Cập nhật phân công GVCN trong bảng phanconggiangday
            $this->updateGVCNAssignment($maLop, $maGVCN, $currentYear);

            // 2c. Xử lý phân công GVBM - CẬP NHẬT THAY VÌ XÓA
            foreach ($assignments as $assign) {
                if (!empty($assign['maMonHoc']) && !empty($assign['maGiaoVien'])) {
                    $this->updateGVBMAssignment($maLop, $assign['maMonHoc'], $assign['maGiaoVien'], $currentYear);
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
     * Cập nhật phân công GVBM (UPDATE nếu đã có, INSERT nếu chưa có)
     */
    private function updateGVBMAssignment($maLop, $maMonHoc, $maGiaoVien, $maNienKhoa) {
        $conn = $this->db->getConnection();
        
        // Kiểm tra xem đã có phân công cho môn học này trong lớp chưa
        $sql_check = "SELECT maPhanCongGiangDay FROM phanconggiangday 
                    WHERE maLop = :maLop AND maMonHoc = :maMonHoc AND loaiPhanCong = 'GVBM'";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([
            ':maLop' => $maLop,
            ':maMonHoc' => $maMonHoc
        ]);
        
        if ($stmt_check->rowCount() > 0) {
            // CẬP NHẬT phân công hiện có - chỉ đổi giáo viên
            $sql_update = "UPDATE phanconggiangday 
                        SET maGiaoVien = :maGV, ngayPhanCong = CURDATE()
                        WHERE maLop = :maLop AND maMonHoc = :maMonHoc AND loaiPhanCong = 'GVBM'";
            $stmt_update = $conn->prepare($sql_update);
            return $stmt_update->execute([
                ':maGV' => $maGiaoVien,
                ':maLop' => $maLop,
                ':maMonHoc' => $maMonHoc
            ]);
        } else {
            // THÊM MỚI phân công
            $sql_insert = "INSERT INTO phanconggiangday 
                        (ngayPhanCong, trangThai, loaiPhanCong, maNienKhoa, maGiaoVien, maLop, maMonHoc) 
                        VALUES (CURDATE(), 'Hoạt động', 'GVBM', :maNienKhoa, :maGV, :maLop, :maMon)";
            $stmt_insert = $conn->prepare($sql_insert);
            return $stmt_insert->execute([
                ':maNienKhoa' => $maNienKhoa,
                ':maGV' => $maGiaoVien,
                ':maLop' => $maLop,
                ':maMon' => $maMonHoc
            ]);
        }
    }

    /**
     * Cập nhật phân công GVCN (UPDATE nếu đã có, INSERT nếu chưa có)
     */
    private function updateGVCNAssignment($maLop, $maGiaoVien, $maNienKhoa) {
        $conn = $this->db->getConnection();
        
        // Kiểm tra xem đã có phân công GVCN cho lớp này chưa
        $sql_check = "SELECT maPhanCongGiangDay FROM phanconggiangday 
                    WHERE maLop = :maLop AND loaiPhanCong = 'GVCN'";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([':maLop' => $maLop]);
        
        if ($stmt_check->rowCount() > 0) {
            // CẬP NHẬT phân công hiện có - chỉ đổi giáo viên
            $sql_update = "UPDATE phanconggiangday 
                        SET maGiaoVien = :maGV, ngayPhanCong = CURDATE()
                        WHERE maLop = :maLop AND loaiPhanCong = 'GVCN'";
            $stmt_update = $conn->prepare($sql_update);
            return $stmt_update->execute([
                ':maGV' => $maGiaoVien,
                ':maLop' => $maLop
            ]);
        } else {
            // THÊM MỚI phân công
            $sql_insert = "INSERT INTO phanconggiangday 
                        (ngayPhanCong, trangThai, loaiPhanCong, maNienKhoa, maGiaoVien, maLop) 
                        VALUES (CURDATE(), 'Hoạt động', 'GVCN', :maNienKhoa, :maGV, :maLop)";
            $stmt_insert = $conn->prepare($sql_insert);
            return $stmt_insert->execute([
                ':maNienKhoa' => $maNienKhoa,
                ':maGV' => $maGiaoVien,
                ':maLop' => $maLop
            ]);
        }
    }

    /**
     * Lấy mã năm học hiện tại
     */
    private function getCurrentNienKhoa() {
        $conn = $this->db->getConnection();
        
        // Tìm năm học mà ngày hiện tại nằm trong khoảng
        $sql = "SELECT maNienKhoa FROM nienkhoa 
                WHERE CURDATE() BETWEEN ngayBatDau AND ngayKetThuc 
                LIMIT 1";
        
        try {
            $stmt = $conn->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return $result['maNienKhoa'];
            } else {
                // Nếu không tìm thấy, lấy năm học gần nhất
                $sql = "SELECT maNienKhoa FROM nienkhoa 
                        ORDER BY ABS(DATEDIFF(CURDATE(), ngayBatDau)) 
                        LIMIT 1";
                $stmt = $conn->query($sql);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['maNienKhoa'] ?? 1; // Mặc định là 1
            }
        } catch (Exception $e) {
            error_log("Lỗi lấy năm học hiện tại: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Thêm phân công GVCN vào bảng phanconggiangday
     */
    private function addGVCNAssignment($maLop, $maGiaoVien, $maNienKhoa) {
        $conn = $this->db->getConnection();
        
        // Kiểm tra xem đã có phân công GVCN cho lớp này chưa
        $sql_check = "SELECT maPhanCongGiangDay FROM phanconggiangday 
                    WHERE maLop = :maLop AND loaiPhanCong = 'GVCN'";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([':maLop' => $maLop]);
        
        if ($stmt_check->rowCount() > 0) {
            // Cập nhật phân công hiện có
            $sql_update = "UPDATE phanconggiangday 
                        SET maGiaoVien = :maGV, ngayPhanCong = CURDATE()
                        WHERE maLop = :maLop AND loaiPhanCong = 'GVCN'";
            $stmt_update = $conn->prepare($sql_update);
            return $stmt_update->execute([':maGV' => $maGiaoVien, ':maLop' => $maLop]);
        } else {
            // Thêm phân công mới
            $sql_insert = "INSERT INTO phanconggiangday 
                        (ngayPhanCong, trangThai, loaiPhanCong, maNienKhoa, maGiaoVien, maLop) 
                        VALUES (CURDATE(), 'Hoạt động', 'GVCN', :maNienKhoa, :maGV, :maLop)";
            $stmt_insert = $conn->prepare($sql_insert);
            return $stmt_insert->execute([
                ':maNienKhoa' => $maNienKhoa,
                ':maGV' => $maGiaoVien,
                ':maLop' => $maLop
            ]);
        }
    }

    /**
     * Lấy tất cả phân công hiện tại của lớp
     */
    public function getAllAssignmentsByClass($maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    pc.maPhanCongGiangDay,
                    pc.ngayPhanCong,
                    pc.trangThai,
                    pc.loaiPhanCong,
                    pc.maNienKhoa,
                    pc.maGiaoVien,
                    pc.maLop,
                    pc.maMonHoc,
                    gv.maGiaoVien,
                    nd.hoTen as tenGiaoVien,
                    mh.tenMonHoc,
                    l.tenLop,
                    nk.namHoc
                FROM phanconggiangday pc
                LEFT JOIN giaovien gv ON pc.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN monhoc mh ON pc.maMonHoc = mh.maMonHoc
                LEFT JOIN lophoc l ON pc.maLop = l.maLop
                LEFT JOIN nienkhoa nk ON pc.maNienKhoa = nk.maNienKhoa
                WHERE pc.maLop = :maLop 
                AND pc.trangThai = 'Hoạt động'
                ORDER BY 
                    CASE WHEN pc.loaiPhanCong = 'GVCN' THEN 1 ELSE 2 END,
                    mh.tenMonHoc";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([':maLop' => $maLop]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy phân công lớp: " . $e->getMessage());
            return [];
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
     * Lấy danh sách tất cả Khối
     */
    public function getAllKhoi($maTruong = null) {
        $conn = $this->db->getConnection();
        
        // Lấy các khối có lớp thuộc trường này
        $sql = "SELECT DISTINCT k.maKhoi, k.tenKhoi 
                FROM khoi k
                JOIN lophoc l ON k.maKhoi = l.maKhoi
                WHERE 1=1";
        
        if ($maTruong) {
            $sql .= " AND l.maTruong = :maTruong";
        }
        
        $sql .= " ORDER BY k.tenKhoi";
        
        $stmt = $conn->prepare($sql);
        if ($maTruong) {
            $stmt->bindParam(':maTruong', $maTruong);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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