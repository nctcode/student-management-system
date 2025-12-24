<?php
require_once 'models/Database.php';

class TuyenSinhModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getConnection() {
        return $this->db->getConnection();
    }
    
    public function dangKyHoSo($data) {
        $conn = $this->db->getConnection();
        
        $sql = "INSERT INTO hosotuyensinh 
                (hoTen, gioiTinh, ngaySinh, noiSinh, danToc, tonGiao, quocTich,
                diaChiThuongTru, noiOHienNay, soDienThoaiHocSinh, soDienThoaiPhuHuynh, email,
                hoTenCha, namSinhCha, ngheNghiepCha, dienThoaiCha, noiCongTacCha,
                hoTenMe, namSinhMe, ngheNghiepMe, dienThoaiMe, noiCongTacMe,
                hoTenNguoiGiamHo, namSinhNguoiGiamHo, ngheNghiepNguoiGiamHo, dienThoaiNguoiGiamHo, noiCongTacNguoiGiamHo,
                truongTHCS, diaChiTruongTHCS, namTotNghiep, xepLoaiHocLuc, xepLoaiHanhKiem, diemTB_Lop9,
                nguyenVong1, nguyenVong2, nguyenVong3, hinhThucTuyenSinh,
                banSaoGiayKhaiSinh, banSaoHoKhau, hocBaTHCS, giayChungNhanTotNghiep, anh34, giayXacNhanUuTien) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Debug: đếm số dấu ?
        $questionCount = substr_count($sql, '?');
        error_log("SQL có {$questionCount} dấu ?");
        
        $params = [
            $data['hoTen'],
            $data['gioiTinh'],
            $data['ngaySinh'],
            $data['noiSinh'] ?? null,
            $data['danToc'] ?? null,
            $data['tonGiao'] ?? null,
            $data['quocTich'] ?? 'Việt Nam',
            $data['diaChiThuongTru'],
            $data['noiOHienNay'] ?? null,
            $data['soDienThoaiHocSinh'],
            $data['soDienThoaiPhuHuynh'],
            $data['email'] ?? null,
            $data['hoTenCha'] ?? null,
            $data['namSinhCha'] ?? null,
            $data['ngheNghiepCha'] ?? null,
            $data['dienThoaiCha'] ?? null,
            $data['noiCongTacCha'] ?? null,
            $data['hoTenMe'] ?? null,
            $data['namSinhMe'] ?? null,
            $data['ngheNghiepMe'] ?? null,
            $data['dienThoaiMe'] ?? null,
            $data['noiCongTacMe'] ?? null,
            $data['hoTenNguoiGiamHo'] ?? null,
            $data['namSinhNguoiGiamHo'] ?? null,
            $data['ngheNghiepNguoiGiamHo'] ?? null,
            $data['dienThoaiNguoiGiamHo'] ?? null,
            $data['noiCongTacNguoiGiamHo'] ?? null,
            $data['truongTHCS'] ?? null,
            $data['diaChiTruongTHCS'] ?? null,
            $data['namTotNghiep'] ?? null,
            $data['xepLoaiHocLuc'] ?? null,
            $data['xepLoaiHanhKiem'] ?? null,
            $data['diemTB_Lop9'] ?? null,
            $data['nguyenVong1'] ?? null,
            $data['nguyenVong2'] ?? null,
            $data['nguyenVong3'] ?? null,
            $data['hinhThucTuyenSinh'] ?? 'XET_TUYEN',
            $data['banSaoGiayKhaiSinh'] ?? null,
            $data['banSaoHoKhau'] ?? null,
            $data['hocBaTHCS'] ?? null,
            $data['giayChungNhanTotNghiep'] ?? null,
            $data['anh34'] ?? null,
            $data['giayXacNhanUuTien'] ?? null
        ];
        
        // Debug: đếm số params
        error_log("Có " . count($params) . " tham số");
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($params);
        
        if (!$result) {
            error_log("Lỗi PDO: " . print_r($stmt->errorInfo(), true));
        }
        
        return $result ? $conn->lastInsertId() : false;
    }

    // Lấy tất cả hồ sơ
    public function getAllHoSo() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, dt.diemToan, dt.diemVan, dt.diemAnh, dt.diemMon4, dt.diemTong
                FROM hosotuyensinh hs
                LEFT JOIN diemtuyensinh dt ON hs.maHoSo = dt.maHoSo
                ORDER BY hs.ngayDangKy DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy hồ sơ theo ID
    public function getHoSoById($maHoSo) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, dt.*
                FROM hosotuyensinh hs
                LEFT JOIN diemtuyensinh dt ON hs.maHoSo = dt.maHoSo
                WHERE hs.maHoSo = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHoSo]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Thêm phương thức này vào TuyenSinhModel
    public function getHoSoByMaHocSinh($maHocSinh) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, dt.*, h.maHocSinh
                FROM hosotuyensinh hs
                LEFT JOIN diemtuyensinh dt ON hs.maHoSo = dt.maHoSo
                LEFT JOIN hocsinh h ON hs.maHoSo = h.maHoSo
                WHERE h.maHocSinh = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug: kiểm tra kết quả query
        error_log("Query getHoSoByMaHocSinh: maHocSinh = " . $maHocSinh);
        error_log("Result: " . print_r($result, true));
        
        return $result;
    }

    // Phương thức lấy danh sách hồ sơ theo mã học sinh (nếu cần)
    public function getHoSoByMaHocSinhList($maHocSinh) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, dt.diemToan, dt.diemVan, dt.diemAnh, dt.diemMon4, dt.diemTong, dt.soBaoDanh
                FROM hosotuyensinh hs
                LEFT JOIN diemtuyensinh dt ON hs.maHoSo = dt.maHoSo
                LEFT JOIN hocsinh h ON hs.maHoSo = h.maHoSo
                WHERE h.maHocSinh = ?
                ORDER BY hs.ngayDangKy DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy hồ sơ theo số điện thoại
    public function getHoSoByPhone($soDienThoai) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, dt.diemToan, dt.diemVan, dt.diemAnh, dt.diemMon4, dt.diemTong, dt.soBaoDanh
                FROM hosotuyensinh hs
                LEFT JOIN diemtuyensinh dt ON hs.maHoSo = dt.maHoSo
                WHERE hs.soDienThoaiHocSinh = ? OR hs.soDienThoaiPhuHuynh = ? OR hs.dienThoaiCha = ? OR hs.dienThoaiMe = ?
                ORDER BY hs.ngayDangKy DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$soDienThoai, $soDienThoai, $soDienThoai, $soDienThoai]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xử lý hồ sơ (duyệt/từ chối)
    public function xuLyHoSo($maHoSo, $trangThai, $ketQua, $ghiChu) {
        $conn = $this->db->getConnection();
        
        $sql = "UPDATE hosotuyensinh 
                SET trangThai = ?, ketQua = ?, ghiChu = ? 
                WHERE maHoSo = ?";
        
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$trangThai, $ketQua, $ghiChu, $maHoSo]);
    }

    // Nhập điểm tuyển sinh
    public function nhapDiemTuyenSinh($maHoSo, $diemData) {
        $conn = $this->db->getConnection();
        
        try {
            $conn->beginTransaction();
            
            // Tính điểm tổng
            $diemTong = $diemData['diemToan'] + $diemData['diemVan'] + $diemData['diemAnh'] + 
                    $diemData['diemMon4'] + $diemData['diemCong'];
            
            // Tạo số báo danh tự động
            $soBaoDanh = 'TS' . date('Y') . str_pad($maHoSo, 4, '0', STR_PAD_LEFT);
            
            // Xác định kết quả dựa trên điểm tổng
            $ketQua = ($diemTong >= 32) ? 'TRUNG_TUYEN' : 'KHONG_TRUNG_TUYEN';
            
            // Insert/Update điểm tuyển sinh
            $sqlDiem = "INSERT INTO diemtuyensinh 
                        (maHoSo, soBaoDanh, diemToan, diemVan, diemAnh, diemMon4, diemCong, diemTong, dotThi) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        diemToan = ?, diemVan = ?, diemAnh = ?, diemMon4 = ?, 
                        diemCong = ?, diemTong = ?, dotThi = ?";
            
            $stmtDiem = $conn->prepare($sqlDiem);
            $stmtDiem->execute([
                $maHoSo, $soBaoDanh, 
                $diemData['diemToan'], $diemData['diemVan'], $diemData['diemAnh'], 
                $diemData['diemMon4'], $diemData['diemCong'], $diemTong, $diemData['dotThi'],
                // Update values
                $diemData['diemToan'], $diemData['diemVan'], $diemData['diemAnh'], 
                $diemData['diemMon4'], $diemData['diemCong'], $diemTong, $diemData['dotThi']
            ]);
            
            // Cập nhật trạng thái và kết quả cho hồ sơ
            $sqlHoSo = "UPDATE hosotuyensinh 
                        SET trangThai = 'DA_DUYET', 
                            ketQua = ?,
                            ghiChu = CONCAT(IFNULL(ghiChu, ''), ' Đã nhập điểm - Tổng: ', ?)
                        WHERE maHoSo = ?";
            
            $stmtHoSo = $conn->prepare($sqlHoSo);
            $stmtHoSo->execute([$ketQua, $diemTong, $maHoSo]);
            
            // Nếu trúng tuyển, tự động tạo học sinh
            if ($ketQua === 'TRUNG_TUYEN') {
                $this->taoHocSinhKhiTrungTuyen($maHoSo);
            }
            
            $conn->commit();
            return true;
            
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Error in nhapDiemTuyenSinh: " . $e->getMessage());
            return false;
        }
    }

    // Thống kê tuyển sinh
    public function getThongKeTuyenSinh() {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                COUNT(*) as tongHoSo,
                SUM(CASE WHEN trangThai = 'CHO_XET_DUYET' THEN 1 ELSE 0 END) as choDuyet,
                SUM(CASE WHEN trangThai = 'DA_DUYET' THEN 1 ELSE 0 END) as daDuyet,
                SUM(CASE WHEN trangThai = 'TU_CHOI' THEN 1 ELSE 0 END) as tuChoi,
                SUM(CASE WHEN ketQua = 'TRUNG_TUYEN' THEN 1 ELSE 0 END) as trungTuyen
                FROM hosotuyensinh";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo học sinh mới khi trúng tuyển
    public function taoHocSinhKhiTrungTuyen($maHoSo) {
        $conn = $this->db->getConnection();
        
        // Lấy thông tin hồ sơ
        $hoSo = $this->getHoSoById($maHoSo);
        
        if (!$hoSo || $hoSo['ketQua'] !== 'TRUNG_TUYEN') {
            return false;
        }
        
        // Tạo người dùng mới
        $sqlNguoiDung = "INSERT INTO nguoidung (hoTen, ngaySinh, gioiTinh, soDienThoai, email, diaChi, loaiNguoiDung) 
                         VALUES (?, ?, ?, ?, ?, ?, 'HOCSINH')";
        $stmtNguoiDung = $conn->prepare($sqlNguoiDung);
        $stmtNguoiDung->execute([
            $hoSo['hoTen'],
            $hoSo['ngaySinh'],
            $hoSo['gioiTinh'],
            $hoSo['soDienThoaiHocSinh'],
            $hoSo['email'],
            $hoSo['diaChiThuongTru']
        ]);
        
        $maNguoiDung = $conn->lastInsertId();
        
        // Tạo tài khoản đăng nhập
        $tenDangNhap = 'hs' . date('Y') . str_pad($maHoSo, 4, '0', STR_PAD_LEFT);
        $sqlTaiKhoan = "INSERT INTO taikhoan (tenDangNhap, matKhau, vaiTro) 
                        VALUES (?, ?, 'HOCSINH')";
        $stmtTaiKhoan = $conn->prepare($sqlTaiKhoan);
        $stmtTaiKhoan->execute([$tenDangNhap, password_hash('123456', PASSWORD_DEFAULT)]);
        
        $maTaiKhoan = $conn->lastInsertId();
        
        // Cập nhật mã tài khoản cho người dùng
        $sqlUpdate = "UPDATE nguoidung SET maTaiKhoan = ? WHERE maNguoiDung = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->execute([$maTaiKhoan, $maNguoiDung]);
        
        // Tạo học sinh
        $sqlHocSinh = "INSERT INTO hocsinh (maNguoiDung, maLop, maHoSo, trangThai) 
                       VALUES (?, NULL, ?, 'DANG_HOC')";
        $stmtHocSinh = $conn->prepare($sqlHocSinh);
        $stmtHocSinh->execute([$maNguoiDung, $maHoSo]);
        
        $maHocSinh = $conn->lastInsertId();
        
        return $maHocSinh;
    }
    // Lấy hồ sơ theo mã người dùng (cho học sinh)
    public function getHoSoByMaNguoiDung($maNguoiDung) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, dt.diemToan, dt.diemVan, dt.diemAnh, dt.diemMon4, dt.diemTong, dt.soBaoDanh
                FROM hosotuyensinh hs
                LEFT JOIN diemtuyensinh dt ON hs.maHoSo = dt.maHoSo
                LEFT JOIN hocsinh h ON hs.maHoSo = h.maHoSo
                WHERE h.maNguoiDung = ?
                ORDER BY hs.ngayDangKy DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cũng cần sửa phương thức getHoSoByMaPhuHuynh:
    public function getHoSoByMaPhuHuynh($maPhuHuynh) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT hs.*, dt.diemToan, dt.diemVan, dt.diemAnh, dt.diemMon4, dt.diemTong, dt.soBaoDanh,
                    nd.hoTen as tenHocSinh, h.maHocSinh
                FROM hosotuyensinh hs
                LEFT JOIN diemtuyensinh dt ON hs.maHoSo = dt.maHoSo
                INNER JOIN hocsinh h ON hs.maHoSo = h.maHoSo
                INNER JOIN nguoidung nd ON h.maNguoiDung = nd.maNguoiDung
                WHERE h.maPhuHuynh = ?
                ORDER BY hs.ngayDangKy DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maPhuHuynh]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


// Lấy mã phụ huynh từ mã người dùng
public function getMaPhuHuynhByMaNguoiDung($maNguoiDung) {
    $conn = $this->db->getConnection();
    
    $sql = "SELECT maPhuHuynh FROM phuhuynh WHERE maNguoiDung = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$maNguoiDung]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['maPhuHuynh'] : null;
}
}
?>