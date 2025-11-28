<?php
// models/NguoiDungModel.php

class NguoiDungModel {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=127.0.0.1;dbname=qlhs;charset=utf8mb4", "root", "");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Kết nối database thất bại: " . $e->getMessage());
        }
    }

    // Lấy thông tin người dùng theo mã
    public function layThongTinNguoiDung($maNguoiDung) {
        $sql = "SELECT nd.*, tk.tenDangNhap, tk.vaiTro, tk.trangThai as trangThaiTaiKhoan
                FROM nguoidung nd 
                LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                WHERE nd.maNguoiDung = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$maNguoiDung]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông tin người dùng: " . $e->getMessage());
            return null;
        }
    }

    // Lấy danh sách người dùng theo vai trò
    public function layNguoiDungTheoVaiTro($loaiNguoiDung) {
        $sql = "SELECT nd.*, tk.tenDangNhap, tk.vaiTro, tk.trangThai as trangThaiTaiKhoan
                FROM nguoidung nd 
                LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                WHERE nd.loaiNguoiDung = ? 
                ORDER BY nd.hoTen";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$loaiNguoiDung]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy người dùng theo vai trò: " . $e->getMessage());
            return [];
        }
    }

    // Lấy thông tin người dùng theo mã tài khoản
    public function layNguoiDungTheoTaiKhoan($maTaiKhoan) {
        $sql = "SELECT nd.*, tk.tenDangNhap, tk.vaiTro, tk.trangThai as trangThaiTaiKhoan
                FROM nguoidung nd 
                LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                WHERE nd.maTaiKhoan = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$maTaiKhoan]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy người dùng theo tài khoản: " . $e->getMessage());
            return null;
        }
    }

    // Lấy thông tin người dùng theo email
    public function layNguoiDungTheoEmail($email) {
        $sql = "SELECT nd.*, tk.tenDangNhap, tk.vaiTro, tk.trangThai as trangThaiTaiKhoan
                FROM nguoidung nd 
                LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                WHERE nd.email = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy người dùng theo email: " . $e->getMessage());
            return null;
        }
    }

    // Lấy thông tin người dùng theo số điện thoại
    public function layNguoiDungTheoSoDienThoai($soDienThoai) {
        $sql = "SELECT nd.*, tk.tenDangNhap, tk.vaiTro, tk.trangThai as trangThaiTaiKhoan
                FROM nguoidung nd 
                LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                WHERE nd.soDienThoai = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$soDienThoai]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy người dùng theo số điện thoại: " . $e->getMessage());
            return null;
        }
    }

    // Lấy thông tin người dùng theo CCCD
    public function layNguoiDungTheoCCCD($cccd) {
        $sql = "SELECT nd.*, tk.tenDangNhap, tk.vaiTro, tk.trangThai as trangThaiTaiKhoan
                FROM nguoidung nd 
                LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                WHERE nd.CCCD = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cccd]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy người dùng theo CCCD: " . $e->getMessage());
            return null;
        }
    }

    // Thêm người dùng mới
    public function themNguoiDung($data) {
        $sql = "INSERT INTO nguoidung (hoTen, ngaySinh, gioiTinh, soDienThoai, email, diaChi, CCCD, loaiNguoiDung, maTaiKhoan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['hoTen'],
                $data['ngaySinh'],
                $data['gioiTinh'],
                $data['soDienThoai'],
                $data['email'],
                $data['diaChi'],
                $data['CCCD'],
                $data['loaiNguoiDung'],
                $data['maTaiKhoan']
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi thêm người dùng: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật thông tin người dùng
    public function capNhatThongTin($maNguoiDung, $data) {
        $sql = "UPDATE nguoidung 
                SET hoTen = ?, ngaySinh = ?, gioiTinh = ?, soDienThoai = ?, email = ?, diaChi = ?, CCCD = ? 
                WHERE maNguoiDung = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['hoTen'],
                $data['ngaySinh'],
                $data['gioiTinh'],
                $data['soDienThoai'],
                $data['email'],
                $data['diaChi'],
                $data['CCCD'],
                $maNguoiDung
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật thông tin người dùng: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật mã tài khoản cho người dùng
    public function capNhatMaTaiKhoan($maNguoiDung, $maTaiKhoan) {
        $sql = "UPDATE nguoidung SET maTaiKhoan = ? WHERE maNguoiDung = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$maTaiKhoan, $maNguoiDung]);
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật mã tài khoản: " . $e->getMessage());
            return false;
        }
    }

    // Xóa người dùng
    public function xoaNguoiDung($maNguoiDung) {
        $sql = "DELETE FROM nguoidung WHERE maNguoiDung = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$maNguoiDung]);
        } catch (PDOException $e) {
            error_log("Lỗi xóa người dùng: " . $e->getMessage());
            return false;
        }
    }

    // Tìm kiếm người dùng theo tên
    public function timKiemNguoiDung($keyword, $loaiNguoiDung = null) {
        $sql = "SELECT nd.*, tk.tenDangNhap, tk.vaiTro, tk.trangThai as trangThaiTaiKhoan
                FROM nguoidung nd 
                LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                WHERE (nd.hoTen LIKE ? OR nd.email LIKE ? OR nd.soDienThoai LIKE ?)";
        
        $params = ["%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($loaiNguoiDung) {
            $sql .= " AND nd.loaiNguoiDung = ?";
            $params[] = $loaiNguoiDung;
        }
        
        $sql .= " ORDER BY nd.hoTen";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi tìm kiếm người dùng: " . $e->getMessage());
            return [];
        }
    }

    // Lấy tổng số người dùng theo vai trò
    public function demNguoiDungTheoVaiTro($loaiNguoiDung = null) {
        $sql = "SELECT COUNT(*) as total FROM nguoidung";
        
        if ($loaiNguoiDung) {
            $sql .= " WHERE loaiNguoiDung = ?";
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($loaiNguoiDung) {
                $stmt->execute([$loaiNguoiDung]);
            } else {
                $stmt->execute();
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Lỗi đếm người dùng: " . $e->getMessage());
            return 0;
        }
    }

    // Kiểm tra email đã tồn tại chưa
    public function kiemTraEmailTonTai($email, $maNguoiDung = null) {
        $sql = "SELECT COUNT(*) as total FROM nguoidung WHERE email = ?";
        $params = [$email];
        
        if ($maNguoiDung) {
            $sql .= " AND maNguoiDung != ?";
            $params[] = $maNguoiDung;
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['total'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra email: " . $e->getMessage());
            return false;
        }
    }

    // Kiểm tra số điện thoại đã tồn tại chưa
    public function kiemTraSoDienThoaiTonTai($soDienThoai, $maNguoiDung = null) {
        $sql = "SELECT COUNT(*) as total FROM nguoidung WHERE soDienThoai = ?";
        $params = [$soDienThoai];
        
        if ($maNguoiDung) {
            $sql .= " AND maNguoiDung != ?";
            $params[] = $maNguoiDung;
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['total'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra số điện thoại: " . $e->getMessage());
            return false;
        }
    }

    // Kiểm tra CCCD đã tồn tại chưa
    public function kiemTraCCCDTonTai($cccd, $maNguoiDung = null) {
        $sql = "SELECT COUNT(*) as total FROM nguoidung WHERE CCCD = ?";
        $params = [$cccd];
        
        if ($maNguoiDung) {
            $sql .= " AND maNguoiDung != ?";
            $params[] = $maNguoiDung;
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['total'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra CCCD: " . $e->getMessage());
            return false;
        }
    }
    // Lấy thông tin chi tiết theo vai trò
    public function layThongTinChiTietTheoVaiTro($maNguoiDung, $vaiTro) {
        $sql = "SELECT nd.*, tk.tenDangNhap, tk.vaiTro, tk.trangThai as trangThaiTaiKhoan";
        
        // Thêm các trường cụ thể theo vai trò
        switch($vaiTro) {
            case 'HOCSINH':
                $sql .= ", hs.maHocSinh, hs.maLop, hs.ngayNhapHoc, hs.trangThai as trangThaiHocSinh,
                         l.tenLop, k.tenKhoi, ph.hoTen as tenPhuHuynh";
                $sql .= " FROM nguoidung nd 
                         LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                         LEFT JOIN hocsinh hs ON nd.maNguoiDung = hs.maNguoiDung
                         LEFT JOIN lophoc l ON hs.maLop = l.maLop
                         LEFT JOIN khoi k ON l.maKhoi = k.maKhoi
                         LEFT JOIN phuhuynh phs ON hs.maPhuHuynh = phs.maPhuHuynh
                         LEFT JOIN nguoidung ph ON phs.maNguoiDung = ph.maNguoiDung";
                break;
                
            case 'GIAOVIEN':
                $sql .= ", gv.maGiaoVien, gv.chuyenMon, gv.loaiGiaoVien, gv.maMonHoc,
                         mh.tenMonHoc, tt.toChuyenMon";
                $sql .= " FROM nguoidung nd 
                         LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                         LEFT JOIN giaovien gv ON nd.maNguoiDung = gv.maNguoiDung
                         LEFT JOIN monhoc mh ON gv.maMonHoc = mh.maMonHoc
                         LEFT JOIN totruongchuyenmon tt ON gv.maToTruong = tt.maToTruong";
                break;
                
            case 'PHUHUYNH':
                $sql .= ", ph.maPhuHuynh, ph.ngheNghiep, ph.moiQuanHe";
                $sql .= " FROM nguoidung nd 
                         LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                         LEFT JOIN phuhuynh ph ON nd.maNguoiDung = ph.maNguoiDung";
                break;
                
            case 'BGH':
                $sql .= ", bgh.maBanGiamHieu";
                $sql .= " FROM nguoidung nd 
                         LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                         LEFT JOIN bangiamhieu bgh ON nd.maNguoiDung = bgh.maNguoiDung";
                break;
                
            case 'QTV':
                $sql .= ", qtv.maQuanTriVien";
                $sql .= " FROM nguoidung nd 
                         LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                         LEFT JOIN quantrivien qtv ON nd.maNguoiDung = qtv.maNguoiDung";
                break;
                
            case 'TOTRUONG':
                $sql .= ", tt.maToTruong, tt.toChuyenMon, tt.maMonHoc,
                         mh.tenMonHoc, COUNT(gv.maGiaoVien) as soLuongGiaoVien";
                $sql .= " FROM nguoidung nd 
                         LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan 
                         LEFT JOIN totruongchuyenmon tt ON nd.maNguoiDung = tt.maNguoiDung
                         LEFT JOIN monhoc mh ON tt.maMonHoc = mh.maMonHoc
                         LEFT JOIN giaovien gv ON tt.maToTruong = gv.maToTruong";
                break;
                
            default:
                $sql .= " FROM nguoidung nd 
                         LEFT JOIN taikhoan tk ON nd.maTaiKhoan = tk.maTaiKhoan";
        }
        
        $sql .= " WHERE nd.maNguoiDung = ?";
        
        // Thêm GROUP BY cho TOTRUONG để đếm số lượng giáo viên
        if ($vaiTro == 'TOTRUONG') {
            $sql .= " GROUP BY tt.maToTruong";
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$maNguoiDung]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông tin chi tiết: " . $e->getMessage());
            return null;
        }
    }

    // Lấy danh sách giáo viên trong tổ chuyên môn (dành cho TOTRUONG)
    public function layGiaoVienTrongTo($maToTruong) {
        $sql = "SELECT gv.maGiaoVien, nd.hoTen, nd.email, nd.soDienThoai, 
                       gv.chuyenMon, gv.loaiGiaoVien, mh.tenMonHoc
                FROM giaovien gv
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN monhoc mh ON gv.maMonHoc = mh.maMonHoc
                WHERE gv.maToTruong = ?
                ORDER BY nd.hoTen";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$maToTruong]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách giáo viên trong tổ: " . $e->getMessage());
            return [];
        }
    }

    // Lấy thông tin chi tiết về tổ chuyên môn
    public function layThongTinToChuyenMon($maToTruong) {
        $sql = "SELECT tt.*, mh.tenMonHoc, 
                       COUNT(DISTINCT gv.maGiaoVien) as soGiaoVien,
                       COUNT(DISTINCT mh.maMonHoc) as soMonHoc
                FROM totruongchuyenmon tt
                LEFT JOIN monhoc mh ON tt.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON tt.maToTruong = gv.maToTruong
                WHERE tt.maToTruong = ?
                GROUP BY tt.maToTruong";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$maToTruong]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông tin tổ chuyên môn: " . $e->getMessage());
            return null;
        }
    }

    // Lấy tất cả thông tin của 1 người dùng bằng ID
    public function getUserById($id) {
        $sql = "SELECT * FROM nguoidung WHERE maNguoiDung = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
?>