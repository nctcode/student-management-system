<?php
require_once 'models/Database.php';

class DonChuyenLopTruongModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy tất cả đơn chuyển trường
    public function getAllDonChuyenTruong() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT dclt.*, 
                       hs.maHocSinh, nd_hs.hoTen as tenHocSinh, nd_hs.ngaySinh,
                       lh.tenLop as tenLopHienTai,
                       tht.tenTruong as tenTruongHienTai,
                       tden.tenTruong as tenTruongDen,
                       nd_ph.hoTen as tenPhuHuynh,
                       nd_hs.soDienThoai as sdtPhuHuynh
                FROM donchuyenloptruong dclt
                JOIN hocsinh hs ON dclt.maHocSinh = hs.maHocSinh
                JOIN nguoidung nd_hs ON hs.maNguoiDung = nd_hs.maNguoiDung
                JOIN lophoc lh ON hs.maLop = lh.maLop
                JOIN truong tht ON dclt.maTruongHienTai = tht.maTruong
                LEFT JOIN truong tden ON dclt.maTruongDen = tden.maTruong
                LEFT JOIN phuhuynh ph ON hs.maPhuHuynh = ph.maPhuHuynh
                LEFT JOIN nguoidung nd_ph ON ph.maNguoiDung = nd_ph.maNguoiDung
                ORDER BY dclt.ngayGui DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy đơn theo ID
    public function getDonChuyenTruongById($maDon) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT dclt.*, 
                       hs.maHocSinh, nd_hs.hoTen as tenHocSinh, nd_hs.ngaySinh, nd_hs.soDienThoai as sdtHocSinh,
                       lh.tenLop as tenLopHienTai, lh.maLop as maLopHienTai,
                       tht.tenTruong as tenTruongHienTai, tht.diaChi as diaChiTruongHienTai,
                       tden.tenTruong as tenTruongDen, tden.diaChi as diaChiTruongDen,
                       nd_ph.hoTen as tenPhuHuynh, nd_ph.soDienThoai as sdtPhuHuynh
                FROM donchuyenloptruong dclt
                JOIN hocsinh hs ON dclt.maHocSinh = hs.maHocSinh
                JOIN nguoidung nd_hs ON hs.maNguoiDung = nd_hs.maNguoiDung
                JOIN lophoc lh ON hs.maLop = lh.maLop
                JOIN truong tht ON dclt.maTruongHienTai = tht.maTruong
                LEFT JOIN truong tden ON dclt.maTruongDen = tden.maTruong
                LEFT JOIN phuhuynh ph ON hs.maPhuHuynh = ph.maPhuHuynh
                LEFT JOIN nguoidung nd_ph ON ph.maNguoiDung = nd_ph.maNguoiDung
                WHERE dclt.maDon = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maDon]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo đơn chuyển trường mới
    public function createDonChuyenTruong($data) {
        $conn = $this->db->getConnection();
        
        $sql = "INSERT INTO donchuyenloptruong 
                (lyDoChuyen, maHocSinh, maTruongHienTai, maTruongDen, ngayGui, trangThaiTruongDi, trangThaiTruongDen) 
                VALUES (?, ?, ?, ?, CURDATE(), 'Chờ duyệt', 'Chờ duyệt')";
        
        $stmt = $conn->prepare($sql);
        
        return $stmt->execute([
            $data['lyDoChuyen'],
            $data['maHocSinh'],
            $data['maTruongHienTai'],
            $data['maTruongDen']
        ]);
    }

    // Duyệt đơn từ trường đi
    public function duyetDonTruongDi($maDon, $trangThai, $lyDoTuChoi = null) {
        $conn = $this->db->getConnection();
        
        $sql = "UPDATE donchuyenloptruong 
                SET trangThaiTruongDi = ?, 
                    lyDoTuChoiTruongDi = ?,
                    ngayDuyetTruongDi = CURDATE() 
                WHERE maDon = ?";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$trangThai, $lyDoTuChoi, $maDon]);
    }

    // Duyệt đơn từ trường đến
    public function duyetDonTruongDen($maDon, $trangThai, $lyDoTuChoi = null) {
        $conn = $this->db->getConnection();
        
        $sql = "UPDATE donchuyenloptruong 
                SET trangThaiTruongDen = ?, 
                    lyDoTuChoiTruongDen = ?,
                    ngayDuyetTruongDen = CURDATE() 
                WHERE maDon = ?";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$trangThai, $lyDoTuChoi, $maDon]);

        // Nếu cả 2 trường đều duyệt, thực hiện chuyển trường
        if ($result && $trangThai === 'Đã duyệt') {
            $don = $this->getDonChuyenTruongById($maDon);
            if ($don['trangThaiTruongDi'] === 'Đã duyệt' && $don['trangThaiTruongDen'] === 'Đã duyệt') {
                $this->thucHienChuyenTruong($maDon);
            }
        }

        return $result;
    }

    // Hủy đơn
    public function cancelDonChuyenTruong($maDon, $maNguoiDung, $vaiTro) {
        $conn = $this->db->getConnection();
        
        // Kiểm tra quyền hủy
        if ($vaiTro === 'QTV' || $vaiTro === 'BGH') {
            $sql = "UPDATE donchuyenloptruong 
                    SET trangThaiTruongDi = 'Đã hủy', trangThaiTruongDen = 'Đã hủy' 
                    WHERE maDon = ?";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$maDon]);
        } else {
            // Kiểm tra xem người dùng có phải là phụ huynh của học sinh không
            $sql = "UPDATE donchuyenloptruong 
                    SET trangThaiTruongDi = 'Đã hủy', trangThaiTruongDen = 'Đã hủy' 
                    WHERE maDon = ? AND maHocSinh IN (
                        SELECT hs.maHocSinh FROM hocsinh hs
                        JOIN phuhuynh ph ON hs.maPhuHuynh = ph.maPhuHuynh
                        WHERE ph.maNguoiDung = ?
                    ) AND trangThaiTruongDi = 'Chờ duyệt'";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$maDon, $maNguoiDung]);
        }
    }

    // Thực hiện chuyển trường sau khi cả 2 trường duyệt
    private function thucHienChuyenTruong($maDon) {
        $conn = $this->db->getConnection();
        
        $don = $this->getDonChuyenTruongById($maDon);
        
        if ($don) {
            // Cập nhật trạng thái học sinh
            $sql = "UPDATE hocsinh SET trangThai = 'CHUYEN_TRUONG' WHERE maHocSinh = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$don['maHocSinh']]);
        }
        
        // Ghi log hành động
        $this->ghiLogChuyenTruong($maDon, $don);
    }

    // Ghi log chuyển trường
    private function ghiLogChuyenTruong($maDon, $don) {
        $conn = $this->db->getConnection();
        
        $sql = "INSERT INTO log (maNguoiDung, hanhDong, doiTuong, maDoiTuong, moTa, thoiGian) 
                VALUES (?, 'CHUYEN_TRUONG', 'HOCSINH', ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $moTa = "Đã chuyển học sinh " . $don['tenHocSinh'] . " từ trường " . $don['tenTruongHienTai'] . " sang trường " . $don['tenTruongDen'];
        
        $stmt->execute([$_SESSION['user']['maNguoiDung'], $don['maHocSinh'], $moTa]);
    }

    // Kiểm tra quyền xem đơn
    public function checkPermission($maDon, $maNguoiDung, $vaiTro) {
        $conn = $this->db->getConnection();
        
        if ($vaiTro === 'PHUHUYNH') {
            $sql = "SELECT 1 FROM donchuyenloptruong dclt
                    JOIN hocsinh hs ON dclt.maHocSinh = hs.maHocSinh
                    JOIN phuhuynh ph ON hs.maPhuHuynh = ph.maPhuHuynh
                    WHERE dclt.maDon = ? AND ph.maNguoiDung = ?";
        } else if ($vaiTro === 'HOCSINH') {
            $sql = "SELECT 1 FROM donchuyenloptruong dclt
                    JOIN hocsinh hs ON dclt.maHocSinh = hs.maHocSinh
                    WHERE dclt.maDon = ? AND hs.maNguoiDung = ?";
        } else {
            return true; // Admin/BGH có quyền xem tất cả
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maDon, $maNguoiDung]);
        
        return $stmt->fetch() !== false;
    }

    // Lấy đơn theo học sinh
    public function getDonByHocSinh($maHocSinh) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT dclt.*, tht.tenTruong as tenTruongHienTai, tden.tenTruong as tenTruongDen
                FROM donchuyenloptruong dclt
                JOIN truong tht ON dclt.maTruongHienTai = tht.maTruong
                LEFT JOIN truong tden ON dclt.maTruongDen = tden.maTruong
                WHERE dclt.maHocSinh = ?
                ORDER BY dclt.ngayGui DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách trường
    public function getAllTruong() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT * FROM truong ORDER BY tenTruong";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>