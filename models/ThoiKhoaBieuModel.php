<?php
require_once 'models/Database.php';

class ThoiKhoaBieuModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy môn học theo khối
    public function getMonHocByKhoi($maKhoi) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT mh.*, mk.soTiet 
                FROM monhoc mh 
                JOIN monhoc_khoi mk ON mh.maMonHoc = mk.maMonHoc 
                WHERE mk.maKhoi = ? 
                ORDER BY mh.tenMonHoc"; 

        $stmt = $conn->prepare($sql);
        $stmt->execute([$maKhoi]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tạo TKB cố định
    public function taoThoiKhoaBieu($data) {
        $conn = $this->db->getConnection();
        
        // 1. Kiểm tra trùng lịch dạy của giáo viên
        if ($this->kiemTraTrungLichGiaoVien(
            $data['maGiaoVien'], 
            $data['loaiLich'], 
            $data['tietBatDau'], 
            $data['tietKetThuc']
        )) {
            $_SESSION['error'] = "Giáo viên đã có lịch dạy trùng giờ!";
            return false;
        }
        
        // 2. Kiểm tra trùng lịch của Lớp học
        if ($this->kiemTraTrungLich(
            $data['maLop'], 
            $data['loaiLich'], 
            $data['tietBatDau'], 
            $data['tietKetThuc']
        )) {
            $_SESSION['error'] = "Lớp học đã có tiết học trùng giờ!";
            return false;
        }
        
        $sql = "INSERT INTO thoikhoabieu 
                (maLop, maMonHoc, maGiaoVien, tietBatDau, tietKetThuc, phongHoc, loaiLich, maKhoi, maNienKhoa) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            $data['maLop'], 
            $data['maMonHoc'],
            $data['maGiaoVien'], 
            $data['tietBatDau'],
            $data['tietKetThuc'],
            $data['phongHoc'],
            $data['loaiLich'],
            $data['maKhoi'],
            $data['maNienKhoa']
        ]);
    }

    // Lấy TKB cố định theo lớp
    public function getTKBTheoLop($maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT tkb.*, mh.tenMonHoc, 
                        nd.hoTen as tenGiaoVien, l.tenLop
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON tkb.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON tkb.maLop = l.maLop
                WHERE tkb.maLop = ? 
                ORDER BY FIELD(tkb.loaiLich, 'THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6', 'THU_7'), tkb.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy lịch dạy của giáo viên (TKB cố định)
    public function getLichDayByGiaoVien($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT tkb.*, mh.tenMonHoc, l.tenLop, l.maLop
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                JOIN lophoc l ON tkb.maLop = l.maLop
                WHERE tkb.maGiaoVien = ?
                ORDER BY FIELD(tkb.loaiLich, 'THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6', 'THU_7'), tkb.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả TKB cố định
    public function getAllThoiKhoaBieu() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT tkb.*, mh.tenMonHoc, gv.maGiaoVien, nd.hoTen as tenGiaoVien, 
                        l.tenLop
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON tkb.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON tkb.maLop = l.maLop
                ORDER BY tkb.loaiLich, tkb.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Xóa tiết học TKB cố định
    public function xoaTietHoc($maLop, $loaiLich, $tietBatDau, $tietKetThuc) {
        $conn = $this->db->getConnection();
        
        $sql = "DELETE FROM thoikhoabieu 
                WHERE maLop = ? 
                AND loaiLich = ? 
                AND tietBatDau = ? 
                AND tietKetThuc = ?";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maLop, $loaiLich, $tietBatDau, $tietKetThuc]);
    }

    // Kiểm tra trùng lịch giáo viên (TKB cố định)
    public function kiemTraTrungLichGiaoVien($maGiaoVien, $loaiLich, $tietBatDau, $tietKetThuc) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count 
                FROM thoikhoabieu tkb
                WHERE tkb.maGiaoVien = ? 
                AND tkb.loaiLich = ? 
                AND ((tkb.tietBatDau BETWEEN ? AND ?) 
                    OR (tkb.tietKetThuc BETWEEN ? AND ?) 
                    OR (? BETWEEN tkb.tietBatDau AND tkb.tietKetThuc) 
                    OR (? BETWEEN tkb.tietBatDau AND tkb.tietKetThuc))";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien, $loaiLich, $tietBatDau, $tietKetThuc, $tietBatDau, $tietKetThuc, $tietBatDau, $tietKetThuc]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Kiểm tra trùng lịch lớp (TKB cố định)
    public function kiemTraTrungLich($maLop, $loaiLich, $tietBatDau, $tietKetThuc) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count 
                FROM thoikhoabieu tkb
                WHERE tkb.maLop = ? 
                AND tkb.loaiLich = ? 
                AND (
                    (tkb.tietBatDau BETWEEN ? AND ?) 
                    OR (tkb.tietKetThuc BETWEEN ? AND ?) 
                    OR (? <= tkb.tietKetThuc AND ? >= tkb.tietBatDau) 
                )";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([
            $maLop, $loaiLich, 
            $tietBatDau, $tietKetThuc, 
            $tietBatDau, $tietKetThuc, 
            $tietBatDau, $tietKetThuc
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Lấy chi tiết lớp
    public function getChiTietLop($maLop) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, k.tenKhoi, gv.maGiaoVien, nd.hoTen as tenGiaoVien
                FROM lophoc l
                JOIN khoi k ON l.maKhoi = k.maKhoi
                LEFT JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE l.maLop = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Xóa TKB cố định
    public function xoaThoiKhoaBieu($maThoiKhoaBieu) {
        $conn = $this->db->getConnection();
        
        $sql = "DELETE FROM thoikhoabieu WHERE maThoiKhoaBieu = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maThoiKhoaBieu]);
    }
    
    // Lấy giáo viên theo môn học
    public function getGiaoVienByMonHoc($maMonHoc) {
        $conn = $this->db->getConnection();
        try {
            $sql = "SELECT 
                        gv.maGiaoVien,
                        nd.hoTen,
                        gv.chuyenMon,
                        gv.loaiGiaoVien
                    FROM giaovien gv
                    INNER JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                    WHERE gv.maMonHoc = :maMonHoc
                    ORDER BY nd.hoTen";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':maMonHoc', $maMonHoc, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy danh sách giáo viên theo môn học: " . $e->getMessage());
            return [];
        }
    }

    // ========== CÁC HÀM CHO BUỔI HỌC THEO TUẦN ==========

    // Lấy TKB theo lớp và tuần (từ bảng buoihoc)
    public function getTKBTheoLopVaTuan($maLop, $ngayApDungTuan) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT bh.*, mh.tenMonHoc, 
                        nd.hoTen as tenGiaoVien, l.tenLop
                FROM buoihoc bh
                JOIN monhoc mh ON bh.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON bh.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON bh.maLop = l.maLop
                WHERE bh.maLop = ? 
                AND YEARWEEK(bh.ngayHoc, 3) = YEARWEEK(?, 3)
                ORDER BY bh.ngayHoc, bh.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop, $ngayApDungTuan]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    // Tạo buổi học (lịch theo tuần)
    public function taoBuoiHoc($data) {
        $conn = $this->db->getConnection();
        
        // 1. Kiểm tra trùng lịch dạy của giáo viên
        if ($this->kiemTraTrungLichGiaoVienBuoiHoc(
            $data['maGiaoVien'], 
            $data['tietBatDau'], 
            $data['tietKetThuc'],
            $data['ngayHoc']
        )) {
            $_SESSION['error'] = "Giáo viên đã có lịch dạy trùng giờ trong tuần này!";
            return false;
        }
        
        // 2. Kiểm tra trùng lịch của Lớp học
        if ($this->kiemTraTrungLichBuoiHoc(
            $data['maLop'], 
            $data['tietBatDau'], 
            $data['tietKetThuc'],
            $data['ngayHoc']
        )) {
            $_SESSION['error'] = "Lớp học đã có tiết học trùng giờ trong tuần này!";
            return false;
        }
        
        // Lấy niên khóa hiện tại (hoặc theo logic của bạn)
        $maNienKhoa = $this->getNienKhoaHienTai();
        
        // Sửa câu SQL để bao gồm maNienKhoa
        $sql = "INSERT INTO buoihoc 
                (maLop, maMonHoc, maGiaoVien, tietBatDau, tietKetThuc, phongHoc, ngayHoc, maNienKhoa) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            $data['maLop'], 
            $data['maMonHoc'],
            $data['maGiaoVien'], 
            $data['tietBatDau'],
            $data['tietKetThuc'],
            $data['phongHoc'],
            $data['ngayHoc'],
            $maNienKhoa  // Thêm giá trị niên khóa
        ]);
    }

    // Thêm phương thức lấy niên khóa hiện tại
    private function getNienKhoaHienTai() {
        $conn = $this->db->getConnection();
        
        // Lấy niên khóa hiện tại dựa trên ngày
        $sql = "SELECT maNienKhoa FROM nienkhoa 
                WHERE ? BETWEEN ngayBatDau AND ngayKetThuc
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $currentDate = date('Y-m-d');
        $stmt->execute([$currentDate]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result['maNienKhoa'];
        } else {
            // Trả về niên khóa mặc định nếu không tìm thấy
            return 1; // Hoặc logic khác phù hợp với hệ thống
        }
    }

    // Xóa buổi học theo tuần
    public function xoaBuoiHoc($maLop, $tietBatDau, $tietKetThuc, $ngayHoc) {
        $conn = $this->db->getConnection();
        
        $sql = "DELETE FROM buoihoc 
                WHERE maLop = ? 
                AND tietBatDau = ? 
                AND tietKetThuc = ?
                AND ngayHoc = ?";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maLop, $tietBatDau, $tietKetThuc, $ngayHoc]);
    }

    // Kiểm tra trùng lịch giáo viên cho buổi học
    public function kiemTraTrungLichGiaoVienBuoiHoc($maGiaoVien, $tietBatDau, $tietKetThuc, $ngayHoc) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count 
                FROM buoihoc bh
                WHERE bh.maGiaoVien = ? 
                AND bh.ngayHoc = ?
                AND ((bh.tietBatDau BETWEEN ? AND ?) 
                    OR (bh.tietKetThuc BETWEEN ? AND ?) 
                    OR (? BETWEEN bh.tietBatDau AND bh.tietKetThuc) 
                    OR (? BETWEEN bh.tietBatDau AND bh.tietKetThuc))";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien, $ngayHoc, $tietBatDau, $tietKetThuc, $tietBatDau, $tietKetThuc, $tietBatDau, $tietKetThuc]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Kiểm tra trùng lịch lớp cho buổi học
    public function kiemTraTrungLichBuoiHoc($maLop, $tietBatDau, $tietKetThuc, $ngayHoc) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count 
                FROM buoihoc bh
                WHERE bh.maLop = ? 
                AND bh.ngayHoc = ?
                AND (
                    (bh.tietBatDau BETWEEN ? AND ?) 
                    OR (bh.tietKetThuc BETWEEN ? AND ?) 
                    OR (? <= bh.tietKetThuc AND ? >= bh.tietBatDau) 
                )";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([
            $maLop, $ngayHoc,
            $tietBatDau, $tietKetThuc, 
            $tietBatDau, $tietKetThuc, 
            $tietBatDau, $tietKetThuc
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Lấy tất cả buổi học
    public function getAllBuoiHoc() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT bh.*, mh.tenMonHoc, nd.hoTen as tenGiaoVien, l.tenLop
                FROM buoihoc bh
                JOIN monhoc mh ON bh.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON bh.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON bh.maLop = l.maLop
                ORDER BY bh.ngayHoc, bh.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xóa buổi học theo ID
    public function xoaBuoiHocById($maBuoiHoc) {
        $conn = $this->db->getConnection();
        
        $sql = "DELETE FROM buoihoc WHERE maBuoiHoc = ?";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maBuoiHoc]);
    }

    // Kiểm tra buổi học đã tồn tại
    public function kiemTraBuoiHocTonTai($maLop, $tietBatDau, $tietKetThuc, $ngayHoc) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count 
                FROM buoihoc 
                WHERE maLop = ? 
                AND ngayHoc = ?
                AND ((tietBatDau BETWEEN ? AND ?) 
                    OR (tietKetThuc BETWEEN ? AND ?) 
                    OR (? BETWEEN tietBatDau AND tietKetThuc) 
                    OR (? BETWEEN tietBatDau AND tietKetThuc))";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop, $ngayHoc, $tietBatDau, $tietKetThuc, $tietBatDau, $tietKetThuc, $tietBatDau, $tietKetThuc]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    // Lấy lịch dạy của giáo viên theo tuần
    public function getLichDayByGiaoVienVaTuan($maGiaoVien, $ngayApDungTuan) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT bh.*, mh.tenMonHoc, l.tenLop, l.maLop,
                    nd.hoTen as tenGiaoVien
                FROM buoihoc bh
                JOIN monhoc mh ON bh.maMonHoc = mh.maMonHoc
                JOIN lophoc l ON bh.maLop = l.maLop
                LEFT JOIN giaovien gv ON bh.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE bh.maGiaoVien = ?
                AND YEARWEEK(bh.ngayHoc, 3) = YEARWEEK(?, 3)
                ORDER BY bh.ngayHoc, bh.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien, $ngayApDungTuan]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}