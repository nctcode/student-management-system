<?php
require_once 'models/Database.php';

class ThoiKhoaBieuModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // --- SỬA: taoThoiKhoaBieu() - Thêm kiểm tra trùng lịch theo Tuần ---
    public function taoThoiKhoaBieu($data) {
        $conn = $this->db->getConnection();
        
        // $data['ngayApDung'] là ngày đại diện cho tuần đang tạo
        $ngayApDung = $data['ngayApDung']; 
        
        // 1. Kiểm tra trùng lịch dạy của giáo viên TRONG TUẦN ĐANG TẠO
        if ($this->kiemTraTrungLichGiaoVien(
            $data['maGiaoVien'], 
            $data['loaiLich'], 
            $data['tietBatDau'], 
            $data['tietKetThuc'], 
            $ngayApDung // <-- Truyền ngày áp dụng (để lấy tuần)
        )) {
            $_SESSION['error'] = "Giáo viên đã có lịch dạy trùng giờ trong tuần này!";
            return false;
        }
        
        // 2. Kiểm tra trùng lịch của Lớp học TRONG TUẦN ĐANG TẠO
        if ($this->kiemTraTrungLich(
            $data['maLop'], 
            $data['loaiLich'], 
            $data['tietBatDau'], 
            $data['tietKetThuc'], 
            $ngayApDung // <-- Truyền ngày áp dụng (để lấy tuần)
        )) {
            $_SESSION['error'] = "Lớp học đã có tiết học trùng giờ trong tuần này!";
            return false;
        }

        $maMonHoc = $data['maMonHoc'];
        if ($maMonHoc > 5) {
            $this->themMonHocMoi($maMonHoc, $data['maKhoi']);
        }
        
        $sql = "INSERT INTO thoikhoabieu 
                (ngayApDung, maLop, maMonHoc, maGiaoVien, tietBatDau, tietKetThuc, phongHoc, loaiLich) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            $ngayApDung,
            $data['maLop'], 
            $maMonHoc,
            $data['maGiaoVien'], 
            $data['tietBatDau'],
            $data['tietKetThuc'],
            $data['phongHoc'],
            $data['loaiLich']
        ]);
    }

    // Các hàm helper (Giữ nguyên)
    private function themMonHocMoi($maMonHoc, $maKhoi) {
        $conn = $this->db->getConnection();
        
        $monHocList = [
            6 => 'Lịch Sử', 7 => 'Địa Lý', 8 => 'Sinh Học',
            9 => 'Tin Học', 10 => 'Thể Dục', 11 => 'GD Quốc Phòng',
            12 => 'GD Công Dân', 13 => 'Công Nghệ', 14 => 'Mỹ Thuật',
            15 => 'Âm Nhạc'
        ];
        
        if (isset($monHocList[$maMonHoc])) {
            $sqlCheck = "SELECT COUNT(*) as count FROM monhoc WHERE maMonHoc = ?";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->execute([$maMonHoc]);
            $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] == 0) {
                $sqlInsert = "INSERT INTO monhoc (maMonHoc, tenMonHoc, soTiet, maKhoi) VALUES (?, ?, 70, ?)";
                $stmtInsert = $conn->prepare($sqlInsert);
                $stmtInsert->execute([$maMonHoc, $monHocList[$maMonHoc], $maKhoi]);
            }
        }
    }

    // --- SỬA: getTKBTheoLop() - Lọc theo Tuần ---
    public function getTKBTheoLop($maLop, $ngayApDungTuan = null) {
        $conn = $this->db->getConnection();
        
        $conditionTuan = "";
        $params = [$maLop];
        
        if ($ngayApDungTuan) {
            // Lọc theo tuần (MySQL YEARWEEK mode 3: Thứ 2 là ngày đầu tuần)
            $conditionTuan = " AND YEARWEEK(tkb.ngayApDung, 3) = YEARWEEK(?, 3)"; 
            $params[] = $ngayApDungTuan; 
        }

        $sql = "SELECT tkb.*, mh.tenMonHoc, 
                        nd.hoTen as tenGiaoVien, l.tenLop
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON tkb.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON tkb.maLop = l.maLop
                WHERE tkb.maLop = ? 
                {$conditionTuan}
                ORDER BY FIELD(tkb.loaiLich, 'THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6', 'THU_7'), tkb.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // getMonHocByKhoi (Giữ nguyên)
    public function getMonHocByKhoi($maKhoi) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT * FROM monhoc 
                WHERE maKhoi = ? OR maKhoi IS NULL 
                ORDER BY tenMonHoc"; 

        $stmt = $conn->prepare($sql);
        $stmt->execute([$maKhoi]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // getAllMonHoc (Giữ nguyên)
    public function getAllMonHoc() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT maMonHoc, tenMonHoc, soTiet FROM monhoc ORDER BY tenMonHoc";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // --- SỬA: getLichDayByGiaoVien() - Lọc theo Tuần ---
    public function getLichDayByGiaoVien($maGiaoVien, $ngayApDungTuan = null) {
        $conn = $this->db->getConnection();
        
        $conditionTuan = "";
        $params = [$maGiaoVien];
        
        if ($ngayApDungTuan) {
            $conditionTuan = " AND YEARWEEK(tkb.ngayApDung, 3) = YEARWEEK(?, 3)";
            $params[] = $ngayApDungTuan; 
        }
        
        $sql = "SELECT tkb.*, mh.tenMonHoc, l.tenLop, l.maLop
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                JOIN lophoc l ON tkb.maLop = l.maLop
                WHERE tkb.maGiaoVien = ?
                {$conditionTuan}
                ORDER BY FIELD(tkb.loaiLich, 'THU_2', 'THU_3', 'THU_4', 'THU_5', 'THU_6', 'THU_7'), tkb.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- SỬA: getAllThoiKhoaBieu (cho QTV) - Lọc theo Tuần ---
    public function getAllThoiKhoaBieu($ngayApDungTuan = null) {
        $conn = $this->db->getConnection();
        
        $conditionTuan = "";
        $params = [];
        
        if ($ngayApDungTuan) {
            $conditionTuan = " AND YEARWEEK(tkb.ngayApDung, 3) = YEARWEEK(?, 3)";
            $params[] = $ngayApDungTuan;
        }
        
        $sql = "SELECT tkb.*, mh.tenMonHoc, gv.maGiaoVien, nd.hoTen as tenGiaoVien, 
                        l.tenLop
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON tkb.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON tkb.maLop = l.maLop
                WHERE 1=1 
                {$conditionTuan}
                ORDER BY tkb.ngayApDung, tkb.loaiLich, tkb.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // --- SỬA: xoaTietHoc() - Thêm điều kiện ngayApDung để xóa chính xác tuần ---
    public function xoaTietHoc($maLop, $loaiLich, $tietBatDau, $tietKetThuc, $ngayApDung) {
        $conn = $this->db->getConnection();
        
        $sql = "DELETE FROM thoikhoabieu 
                WHERE maLop = ? 
                AND loaiLich = ? 
                AND tietBatDau = ? 
                AND tietKetThuc = ?
                AND YEARWEEK(ngayApDung, 3) = YEARWEEK(?, 3)"; // <-- Lọc theo tuần
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maLop, $loaiLich, $tietBatDau, $tietKetThuc, $ngayApDung]);
    }

    // --- SỬA: kiemTraTrungLichGiaoVien - Lọc theo Tuần ---
    public function kiemTraTrungLichGiaoVien($maGiaoVien, $loaiLich, $tietBatDau, $tietKetThuc, $ngayApDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count 
                FROM thoikhoabieu tkb
                WHERE tkb.maGiaoVien = ? 
                AND tkb.loaiLich = ? 
                AND YEARWEEK(tkb.ngayApDung, 3) = YEARWEEK(?, 3) -- <-- LỌC THEO TUẦN
                AND ((tkb.tietBatDau BETWEEN ? AND ?) 
                     OR (tkb.tietKetThuc BETWEEN ? AND ?) 
                     OR (? BETWEEN tkb.tietBatDau AND tkb.tietKetThuc) 
                     OR (? BETWEEN tkb.tietBatDau AND tkb.tietKetThuc))";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien, $loaiLich, $ngayApDung, $tietBatDau, $tietKetThuc, $tietBatDau, $tietKetThuc, $tietBatDau, $tietKetThuc]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // --- SỬA: kiemTraTrungLich - Lọc theo Tuần ---
    public function kiemTraTrungLich($maLop, $loaiLich, $tietBatDau, $tietKetThuc, $ngayApDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count 
                FROM thoikhoabieu tkb
                WHERE tkb.maLop = ? 
                AND tkb.loaiLich = ? 
                AND YEARWEEK(tkb.ngayApDung, 3) = YEARWEEK(?, 3) -- <-- LỌC THEO TUẦN
                AND (
                    (tkb.tietBatDau BETWEEN ? AND ?) 
                    OR (tkb.tietKetThuc BETWEEN ? AND ?) 
                    OR (? <= tkb.tietKetThuc AND ? >= tkb.tietBatDau) 
                )";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([
            $maLop, $loaiLich, $ngayApDung, 
            $tietBatDau, $tietKetThuc, 
            $tietBatDau, $tietKetThuc, 
            $tietBatDau, $tietKetThuc
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Các hàm khác giữ nguyên...
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
    
    public function xoaThoiKhoaBieu($maThoiKhoaBieu) {
        $conn = $this->db->getConnection();
        
        $sql = "DELETE FROM thoikhoabieu WHERE maThoiKhoaBieu = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maThoiKhoaBieu]);
    }
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
}