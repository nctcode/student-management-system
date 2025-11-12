<?php
require_once 'models/Database.php';

class ThoiKhoaBieuModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Tạo thời khóa biểu mới (PHIÊN BẢN ĐƠN GIẢN)
public function taoThoiKhoaBieu($data) {
    $conn = $this->db->getConnection();
    
    // Kiểm tra xem môn học có tồn tại không, nếu không thì tạo mới
    $maMonHoc = $data['maMonHoc'];
    if ($maMonHoc > 5) {
        $this->themMonHocMoi($maMonHoc, $data['maKhoi']);
    }
    
    $sql = "INSERT INTO thoikhoabieu 
            (ngayApDung, maMonHoc, tietBatDau, tietKetThuc, phongHoc, loaiLich, maGiaoVien) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    return $stmt->execute([
        $data['ngayApDung'],
        $maMonHoc,
        $data['tietBatDau'],
        $data['tietKetThuc'],
        $data['phongHoc'],
        $data['loaiLich'],
        1 // Mã giáo viên mặc định
    ]);
}

    // Thêm phương thức để tạo môn học mới
    private function themMonHocMoi($maMonHoc, $maKhoi) {
        $conn = $this->db->getConnection();
        
        $monHocList = [
            6 => 'Lịch Sử',
            7 => 'Địa Lý', 
            8 => 'Sinh Học',
            9 => 'Tin Học',
            10 => 'Thể Dục',
            11 => 'GD Quốc Phòng',
            12 => 'GD Công Dân',
            13 => 'Công Nghệ',
            14 => 'Mỹ Thuật',
            15 => 'Âm Nhạc'
        ];
        
        if (isset($monHocList[$maMonHoc])) {
            // Kiểm tra xem môn học đã tồn tại chưa
            $sqlCheck = "SELECT COUNT(*) as count FROM monhoc WHERE maMonHoc = ?";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->execute([$maMonHoc]);
            $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] == 0) {
                // Thêm môn học mới
                $sqlInsert = "INSERT INTO monhoc (maMonHoc, tenMonHoc, soTiet, maKhoi) VALUES (?, ?, 70, ?)";
                $stmtInsert = $conn->prepare($sqlInsert);
                $stmtInsert->execute([$maMonHoc, $monHocList[$maMonHoc], $maKhoi]);
            }
        }
    }

    // Lấy TKB theo lớp
    public function getTKBTheoLop($maLop, $tuan = null) {
        $conn = $this->db->getConnection();
        
        // Lấy mã khối từ lớp học
        $sqlKhoi = "SELECT maKhoi FROM lophoc WHERE maLop = ?";
        $stmtKhoi = $conn->prepare($sqlKhoi);
        $stmtKhoi->execute([$maLop]);
        $khoi = $stmtKhoi->fetch(PDO::FETCH_ASSOC);
        
        if (!$khoi) {
            return [];
        }
        
        $maKhoi = $khoi['maKhoi'];
        
        $sql = "SELECT tkb.*, mh.tenMonHoc, l.tenLop
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                JOIN lophoc l ON mh.maKhoi = l.maKhoi
                WHERE l.maLop = ? AND mh.maKhoi = ?
                ORDER BY tkb.loaiLich, tkb.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maLop, $maKhoi]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy TKB theo giáo viên
    public function getTKBTheoGiaoVien($maGiaoVien, $tuan = null) {
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

    // Lấy tất cả TKB (cho QTV)
    public function getAllThoiKhoaBieu($tuan = null) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT tkb.*, mh.tenMonHoc, gv.maGiaoVien, nd.hoTen as tenGiaoVien, 
                       l.tenLop, l.maLop
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON tkb.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                JOIN lophoc l ON mh.maKhoi = l.maKhoi
                WHERE (? IS NULL OR tkb.ngayApDung >= ?)
                ORDER BY tkb.ngayApDung, tkb.loaiLich, tkb.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        
        if ($tuan) {
            $ngayDauTuan = date('Y-m-d', strtotime($tuan));
            $stmt->execute([$ngayDauTuan, $ngayDauTuan]);
        } else {
            $stmt->execute([null, null]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết lớp học
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

    // Lấy danh sách môn học
    public function getMonHoc() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT * FROM monhoc ORDER BY tenMonHoc";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách môn học theo khối
    public function getMonHocByKhoi($maKhoi) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT * FROM monhoc WHERE maKhoi = ? ORDER BY tenMonHoc";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maKhoi]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách giáo viên
    public function getGiaoVien() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT gv.maGiaoVien, nd.hoTen 
                FROM giaovien gv 
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung 
                ORDER BY nd.hoTen";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách lớp học
    public function getLopHoc() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, k.tenKhoi 
                FROM lophoc l 
                JOIN khoi k ON l.maKhoi = k.maKhoi 
                ORDER BY k.tenKhoi, l.tenLop";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xóa thời khóa biểu
    public function xoaThoiKhoaBieu($maThoiKhoaBieu) {
        $conn = $this->db->getConnection();
        
        $sql = "DELETE FROM thoikhoabieu WHERE maThoiKhoaBieu = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maThoiKhoaBieu]);
    }

    // Lấy danh sách khối học
    public function getKhoiHoc() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT * FROM khoi ORDER BY tenKhoi";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách lớp học theo khối
    public function getLopHocByKhoi($maKhoi) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT l.*, gv.maGiaoVien, nd.hoTen as tenGiaoVien
                FROM lophoc l
                LEFT JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE l.maKhoi = ?
                ORDER BY l.tenLop";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maKhoi]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy giáo viên theo môn học
    public function getGiaoVienByMonHoc($maMonHoc) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT DISTINCT gv.maGiaoVien, nd.hoTen
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                JOIN phanconggiangday pc ON gv.maGiaoVien = pc.maGiaoVien
                WHERE pc.maMonHoc = ?
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maMonHoc]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xóa tiết học
    public function xoaTietHoc($maKhoi, $loaiLich, $tietBatDau, $tietKetThuc) {
        $conn = $this->db->getConnection();
        
        $sql = "DELETE tkb FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                WHERE mh.maKhoi = ? 
                AND tkb.loaiLich = ? 
                AND tkb.tietBatDau = ? 
                AND tkb.tietKetThuc = ?";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maKhoi, $loaiLich, $tietBatDau, $tietKetThuc]);
    }

    // Kiểm tra trùng lịch
    public function kiemTraTrungLich($maKhoi, $loaiLich, $tietBatDau, $tietKetThuc) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as count 
                FROM thoikhoabieu tkb
                JOIN monhoc mh ON tkb.maMonHoc = mh.maMonHoc
                WHERE mh.maKhoi = ? 
                AND tkb.loaiLich = ? 
                AND ((tkb.tietBatDau BETWEEN ? AND ?) OR (tkb.tietKetThuc BETWEEN ? AND ?) 
                     OR (? BETWEEN tkb.tietBatDau AND tkb.tietKetThuc))";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maKhoi, $loaiLich, $tietBatDau, $tietKetThuc, $tietBatDau, $tietKetThuc, $tietBatDau]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
?>