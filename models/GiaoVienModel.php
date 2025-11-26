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
            // Nếu không có tuần, chỉ lấy những TKB có ngày áp dụng gần nhất (tùy thuộc vào logic controller xử lý ngày áp dụng)
            // Hiện tại, để đơn giản, chỉ lấy TKB không lọc theo ngày (nếu logic controller đã lọc tuần hiện tại)
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
}
?>