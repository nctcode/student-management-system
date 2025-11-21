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
}
?>