<?php
require_once 'models/Database.php';

class TinNhanModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Tạo tin nhắn mới
    public function taoTinNhan($maNguoiGui, $danhSachNguoiNhan, $tieuDe, $noiDung, $fileDinhKem = null, $loaiNguoiNhan = 'HOCSINH') {
        $conn = $this->db->getConnection();
        
        try {
            $conn->beginTransaction();

            $sqlHoiThoai = "INSERT INTO cuochoithoai (tenHoiThoai, loaiHoiThoai, maNguoiDung, ngayTao) 
                           VALUES (?, 'NHOM', ?, NOW())";
            $stmtHoiThoai = $conn->prepare($sqlHoiThoai);
            $stmtHoiThoai->execute([$tieuDe, $maNguoiGui]);
            $maHoiThoai = $conn->lastInsertId();

            $sqlThamGia = "INSERT INTO thanhviengoi (maHoiThoai, maNguoiDung, ngayThamGia) 
                          VALUES (?, ?, NOW())";
            $stmtThamGia = $conn->prepare($sqlThamGia);
            
            $stmtThamGia->execute([$maHoiThoai, $maNguoiGui]);
            
            foreach ($danhSachNguoiNhan as $maNguoiNhan) {
                $stmtThamGia->execute([$maHoiThoai, $maNguoiNhan]);
            }

            $sqlTinNhan = "INSERT INTO tinnhan (maHoiThoai, tieuDe, noiDung, fileDinhKem, thoiGianGui, maNguoiDung, trangThai) 
                          VALUES (?, ?, ?, ?, NOW(), ?, 0)";
            $stmtTinNhan = $conn->prepare($sqlTinNhan);
            
            $filePath = $fileDinhKem ? json_encode($fileDinhKem) : null;
            $stmtTinNhan->execute([$maHoiThoai, $tieuDe, $noiDung, $filePath, $maNguoiGui]);

            $conn->commit();
            return $maHoiThoai;
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Lỗi tạo tin nhắn: " . $e->getMessage());
            return false;
        }
    }

    // Lấy tin nhắn theo người dùng
    public function getTinNhanByNguoiDung($maNguoiDung, $filter = 'all') {
        $conn = $this->db->getConnection();
        
        $sql = "
            SELECT 
                cht.maHoiThoai, cht.tenHoiThoai, cht.loaiHoiThoai,
                tn.tieuDe, tn.noiDung, tn.thoiGianGui, tn.fileDinhKem, tn.trangThai,
                nd.hoTen as nguoiGui,
                tk.vaiTro,
                
                -- Đếm số tin nhắn chưa đọc trong hội thoại này (mà không phải của mình)
                (SELECT COUNT(*) FROM tinnhan tn2 
                 WHERE tn2.maHoiThoai = cht.maHoiThoai 
                 AND tn2.trangThai = 0 
                 AND tn2.maNguoiDung != ?) as soTinChuaDoc, 
                 
                -- Lấy danh sách thành viên
                (SELECT GROUP_CONCAT(nd_tv.hoTen SEPARATOR ', ') 
                 FROM thanhviengoi tv_sub
                 JOIN nguoidung nd_tv ON tv_sub.maNguoiDung = nd_tv.maNguoiDung
                 WHERE tv_sub.maHoiThoai = cht.maHoiThoai
                ) as danhSachThanhVien
            
            FROM cuochoithoai cht
            
            -- Join để đảm bảo người dùng này có trong hội thoại
            JOIN thanhviengoi tv ON cht.maHoiThoai = tv.maHoiThoai
            
            -- Join CHỈ tin nhắn MỚI NHẤT (dựa vào maTinNhan lớn nhất)
            JOIN tinnhan tn ON tn.maTinNhan = (
                SELECT MAX(maTinNhan) 
                FROM tinnhan 
                WHERE maHoiThoai = cht.maHoiThoai
            )
            
            -- Join thông tin người gửi của tin nhắn mới nhất đó
            JOIN nguoidung nd ON tn.maNguoiDung = nd.maNguoiDung
            JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan
            
            -- Điều kiện WHERE chính
            WHERE tv.maNguoiDung = ?
        ";
        
        $params = [$maNguoiDung, $maNguoiDung];
        
        if ($filter === 'unread') {
            $sql .= " HAVING soTinChuaDoc > 0";
        }

        // Sắp xếp theo thời gian của tin nhắn mới nhất
        $sql .= " ORDER BY tn.thoiGianGui DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết hội thoại
    public function getChiTietHoiThoai($maHoiThoai, $maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT cht.*, tv.maNguoiDung
                FROM cuochoithoai cht
                JOIN thanhviengoi tv ON cht.maHoiThoai = tv.maHoiThoai
                WHERE cht.maHoiThoai = ? AND tv.maNguoiDung = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHoiThoai, $maNguoiDung]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy tin nhắn theo hội thoại
    public function getTinNhanByHoiThoai($maHoiThoai) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT tn.*, nd.hoTen as nguoiGui, tk.vaiTro
                FROM tinnhan tn
                JOIN nguoidung nd ON tn.maNguoiDung = nd.maNguoiDung
                JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan
                WHERE tn.maHoiThoai = ?
                ORDER BY tn.thoiGianGui ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHoiThoai]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Gửi tin nhắn trong hội thoại
    public function guiTinNhanTrongHoiThoai($maHoiThoai, $maNguoiGui, $noiDung, $fileDinhKem = null) {
        $conn = $this->db->getConnection();
        
        $sql = "INSERT INTO tinnhan (maHoiThoai, noiDung, fileDinhKem, thoiGianGui, maNguoiDung, trangThai) 
                VALUES (?, ?, ?, NOW(), ?, 0)";
        
        $stmt = $conn->prepare($sql);
        $filePath = $fileDinhKem ? json_encode($fileDinhKem) : null;
        
        return $stmt->execute([$maHoiThoai, $noiDung, $filePath, $maNguoiGui]);
    }

    // Đánh dấu đã đọc
    public function danhDauDaDoc($maHoiThoai, $maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "UPDATE tinnhan SET trangThai = 1 
                WHERE maHoiThoai = ? AND maNguoiDung != ? AND trangThai = 0";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maHoiThoai, $maNguoiDung]);
    }

    // Lấy số tin nhắn chưa đọc
    public function getSoTinNhanChuaDoc($maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as soTinChuaDoc
                FROM tinnhan tn
                JOIN thanhviengoi tv ON tn.maHoiThoai = tv.maHoiThoai
                WHERE tv.maNguoiDung = ? AND tn.trangThai = 0 AND tn.maNguoiDung != ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung, $maNguoiDung]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['soTinChuaDoc'] ?? 0;
    }

    // Lấy danh sách thành viên trong hội thoại
    public function getThanhVienHoiThoai($maHoiThoai) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT tv.maNguoiDung, nd.hoTen, tk.vaiTro
                FROM thanhviengoi tv
                JOIN nguoidung nd ON tv.maNguoiDung = nd.maNguoiDung
                JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan
                WHERE tv.maHoiThoai = ?
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHoiThoai]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kiểm tra quyền truy cập hội thoại
    public function kiemTraQuyenTruyCap($maHoiThoai, $maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT COUNT(*) as coQuyen
                FROM thanhviengoi 
                WHERE maHoiThoai = ? AND maNguoiDung = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHoiThoai, $maNguoiDung]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['coQuyen'] > 0;
    }
}
?>