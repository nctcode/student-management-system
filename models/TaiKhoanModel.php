<?php
class TaiKhoanModel {
    private $conn;

    public function __construct() {
        try {
            require_once __DIR__ . '/Database.php';
            $db = new Database();
            $this->conn = $db->getConnection();
            
            if (!$this->conn) {
                throw new Exception("Không thể kết nối database");
            }
            
        } catch (Exception $e) {
            error_log("💥 TaiKhoanModel constructor error: " . $e->getMessage());
            throw $e;
        }
    }

   public function authenticate($tenDangNhap, $matKhau) {
    try {
        error_log("🔐 === AUTHENTICATE DEBUG ===");
        error_log("👤 Username: " . $tenDangNhap);
        
        // CHỈ kiểm tra trong bảng taikhoan
        $sql = "SELECT maTaiKhoan, tenDangNhap, matKhau, vaiTro, trangThai 
                FROM taikhoan 
                WHERE tenDangNhap = ?";
        
        error_log("📝 SQL: " . $sql);
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tenDangNhap]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            error_log("❌ USER NOT FOUND in taikhoan OR ACCOUNT INACTIVE");
            return false;
        }
        // 🔥 KIỂM TRA TÀI KHOẢN BỊ KHÓA
        if ($user['trangThai'] === 'DA_KHOA') {
            return "LOCKED";
        }
        error_log("✅ USER FOUND in taikhoan:");
        error_log("   - maTaiKhoan: " . $user['maTaiKhoan']);
        error_log("   - tenDangNhap: " . $user['tenDangNhap']);
        error_log("   - vaiTro: " . $user['vaiTro']);
        error_log("   - trangThai: " . $user['trangThai']);
        
        // Kiểm tra mật khẩu
        $passwordValid = password_verify($matKhau, $user['matKhau']);
        error_log("🔐 Password verification: " . ($passwordValid ? "SUCCESS" : "FAILED"));
        
        if (!$passwordValid) {
            return false;
        }
        
        // Lấy tên từ bảng nguoidung
        error_log("🔍 Getting hoTen from nguoidung...");
        $hoTen = $this->getHoTen($user['maTaiKhoan']);
        $user['hoTen'] = $hoTen;
        unset($user['matKhau']);
        
        error_log("✅ FINAL USER DATA:");
        error_log("   - maTaiKhoan: " . $user['maTaiKhoan']);
        error_log("   - tenDangNhap: " . $user['tenDangNhap']);
        error_log("   - vaiTro: " . $user['vaiTro']);
        error_log("   - hoTen: " . $user['hoTen']);
        
        return $user;
        
    } catch (Exception $e) {
        error_log("💥 AUTHENTICATE EXCEPTION: " . $e->getMessage());
        return false;
    }
}

private function getHoTen($maTaiKhoan) {
    try {
        $sql = "SELECT hoTen FROM nguoidung WHERE maTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maTaiKhoan]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            error_log("✅ Found hoTen in nguoidung: " . $result['hoTen']);
            return $result['hoTen'];
        } else {
            error_log("⚠️ No hoTen found in nguoidung, using default");
            return 'User';
        }
        
    } catch (Exception $e) {
        error_log("💥 getHoTen ERROR: " . $e->getMessage());
        return 'User';
    }
}

public function getMaNguoiDung($maTaiKhoan) {
    try {
        $sql = "SELECT maNguoiDung FROM nguoidung WHERE maTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maTaiKhoan]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['maNguoiDung'] : $maTaiKhoan; // Fallback to maTaiKhoan
        
    } catch (Exception $e) {
        return $maTaiKhoan; // Fallback to maTaiKhoan
    }
}
   public function createUser($data) {
    $tenDangNhap = $data['tenDangNhap'] ?? '';
    $matKhau = $data['matKhau'] ?? '';
    $hoTen = $data['hoTen'] ?? 'User';
    $vaiTro = $data['vaiTro'] ?? 'USER';
    
    try {
        $this->conn->beginTransaction();
        
        // 1. Kiểm tra username trùng
        if ($this->isUsernameExists($tenDangNhap)) {
            throw new Exception("Tên đăng nhập '$tenDangNhap' đã tồn tại");
        }
        
        // 2. Tạo taikhoan
        $sql1 = "INSERT INTO taikhoan (tenDangNhap, matKhau, vaiTro, trangThai) 
                VALUES (?, ?, ?, 'HOAT_DONG')";
        $stmt1 = $this->conn->prepare($sql1);
        $hashedPassword = password_hash($matKhau, PASSWORD_DEFAULT);
        $stmt1->execute([$tenDangNhap, $hashedPassword, $vaiTro]);
        
        $maTaiKhoan = $this->conn->lastInsertId();
        
        // 3. Tạo nguoidung - QUAN TRỌNG: phải tạo bảng này
        $sql2 = "INSERT INTO nguoidung (maTaiKhoan, hoTen, loaiNguoiDung) 
                VALUES (?, ?, ?)";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute([$maTaiKhoan, $hoTen, $vaiTro]);
        
        $maNguoiDung = $this->conn->lastInsertId();
        
        // 4. Tạo thông tin chi tiết theo vai trò - QUAN TRỌNG
        $this->createUserDetail($vaiTro, $maNguoiDung, $data);
        
        $this->conn->commit();
        return true;
        
    } catch (Exception $e) {
        $this->conn->rollBack();
        throw $e;
    }
}

private function createUserDetail($vaiTro, $maNguoiDung, $data) {
    try {
        error_log("🎯 Creating user detail for role: " . $vaiTro);
        
        switch ($vaiTro) {
            case 'HOCSINH':
                $sql = "INSERT INTO hocsinh (maNguoiDung, maLop, maPhuHuynh, ngayNhapHoc, trangThai) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    $maNguoiDung,
                    !empty($data['maLop']) ? $data['maLop'] : NULL,
                    !empty($data['maPhuHuynh']) ? $data['maPhuHuynh'] : NULL,
                    $data['ngayNhapHoc'] ?? date('Y-m-d'),
                    $data['trangThai'] ?? 'DANG_HOC'
                ]);
                error_log("✅ Created HOCSINH record");
                break;
                
            case 'GIAOVIEN':
                $sql = "INSERT INTO giaovien (maNguoiDung, chuyenMon, loaiGiaoVien, maToTruong) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    $maNguoiDung,
                    $data['chuyenMon'] ?? NULL,
                    $data['loaiGiaoVien'] ?? NULL,
                    !empty($data['maToTruong']) ? $data['maToTruong'] : NULL
                ]);
                error_log("✅ Created GIAOVIEN record");
                break;
                
            case 'PHUHUYNH':
                $sql = "INSERT INTO phuhuynh (maNguoiDung, ngheNghiep, moiQuanHe) 
                        VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    $maNguoiDung,
                    $data['ngheNghiep'] ?? NULL,
                    $data['moiQuanHe'] ?? NULL
                ]);
                error_log("✅ Created PHUHUYNH record");
                break;
                
            case 'BGH':
                $sql = "INSERT INTO bangiamhieu (maNguoiDung) VALUES (?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$maNguoiDung]);
                error_log("✅ Created BGH record");
                break;
                
            default:
                error_log("ℹ️ No detail table needed for role: " . $vaiTro);
                break;
        }
        
    } catch (Exception $e) {
        error_log("💥 createUserDetail error: " . $e->getMessage());
        // KHÔNG throw exception - vẫn cho tạo user thành công
    }
}
    private function isUsernameExists($tenDangNhap) {
        $sql = "SELECT COUNT(*) as count FROM taikhoan WHERE tenDangNhap = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tenDangNhap]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    public function getAllUsers($search_id = '', $search_username = '') {
        try {
            $sql = "SELECT tk.*, nd.hoTen, nd.loaiNguoiDung 
                    FROM taikhoan tk 
                    JOIN nguoidung nd ON tk.maTaiKhoan = nd.maTaiKhoan 
                    WHERE 1=1";
            $params = [];
            
            if (!empty($search_id)) {
                $sql .= " AND tk.maTaiKhoan = ?";
                $params[] = $search_id;
            }
            
            if (!empty($search_username)) {
                $sql .= " AND tk.tenDangNhap LIKE ?";
                $params[] = '%' . $search_username . '%';
            }
            
            $sql .= " ORDER BY tk.maTaiKhoan DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }

    public function getUserById($id) {
        try {
            $sql = "SELECT tk.*, nd.hoTen, nd.loaiNguoiDung 
                    FROM taikhoan tk 
                    JOIN nguoidung nd ON tk.maTaiKhoan = nd.maTaiKhoan 
                    WHERE tk.maTaiKhoan = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateUser($data) {
    $maTaiKhoan = $data['maTaiKhoan'] ?? null;
    
    if (!$maTaiKhoan) {
        throw new Exception("Thiếu mã tài khoản");
    }
    
    try {
        $this->conn->beginTransaction();
        
        $vaiTroMoi = $data['vaiTro'] ?? 'USER';
        
        error_log("🎯 Updating user: " . $maTaiKhoan . " to role: " . $vaiTroMoi);
        
        // 1. Cập nhật taikhoan
        $sql1 = "UPDATE taikhoan SET vaiTro = ?";
        $params1 = [$vaiTroMoi];
        
        if (!empty($data['matKhau'])) {
            $sql1 .= ", matKhau = ?";
            $params1[] = password_hash($data['matKhau'], PASSWORD_DEFAULT);
        }
        
        $sql1 .= " WHERE maTaiKhoan = ?";
        $params1[] = $maTaiKhoan;
        
        $stmt1 = $this->conn->prepare($sql1);
        $result1 = $stmt1->execute($params1);
        
        if (!$result1) {
            throw new Exception("Lỗi cập nhật tài khoản");
        }
        
        error_log("✅ Updated taikhoan");
        
        // 2. Cập nhật nguoidung
        $sql2 = "UPDATE nguoidung SET loaiNguoiDung = ? WHERE maTaiKhoan = ?";
        $stmt2 = $this->conn->prepare($sql2);
        $result2 = $stmt2->execute([$vaiTroMoi, $maTaiKhoan]);
        
        if (!$result2) {
            throw new Exception("Lỗi cập nhật thông tin người dùng");
        }
        
        error_log("✅ Updated nguoidung");
        
        // 3. Cập nhật thông tin chi tiết theo vai trò mới
        $this->updateUserDetail($maTaiKhoan, $vaiTroMoi, $data);
        
        $this->conn->commit();
        error_log("🎉 UPDATE USER SUCCESS");
        return true;
        
    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("💥 updateUser error: " . $e->getMessage());
        throw $e;
    }
}

private function updateUserDetail($maTaiKhoan, $vaiTroMoi, $data) {
    try {
        // Lấy maNguoiDung từ nguoidung
        $maNguoiDung = $this->getMaNguoiDung($maTaiKhoan);
        if (!$maNguoiDung) {
            error_log("❌ Cannot find maNguoiDung for update detail");
            return;
        }
        
        error_log("🔄 Updating user detail for maNguoiDung: " . $maNguoiDung);
        
        // Lấy vai trò cũ để xóa record cũ
        $vaiTroCu = $this->getCurrentRole($maTaiKhoan);
        
        // Xóa record chi tiết cũ (nếu có)
        $this->deleteOldDetail($vaiTroCu, $maNguoiDung);
        
        // Tạo record chi tiết mới (nếu vai trò mới cần)
        switch ($vaiTroMoi) {
            case 'HOCSINH':
                $sql = "INSERT INTO hocsinh (maNguoiDung, maLop, ngayNhapHoc, trangThai) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    $maNguoiDung,
                    $data['maLop'] ?? 1,
                    $data['ngayNhapHoc'] ?? date('Y-m-d'),
                    $data['trangThai'] ?? 'DANG_HOC'
                ]);
                error_log("✅ Created new HOCSINH record");
                break;
                
            case 'GIAOVIEN':
                $sql = "INSERT INTO giaovien (maNguoiDung, chuyenMon, loaiGiaoVien) 
                        VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    $maNguoiDung,
                    $data['chuyenMon'] ?? 'Toán',
                    $data['loaiGiaoVien'] ?? 'GV_BO_MON'
                ]);
                error_log("✅ Created new GIAOVIEN record");
                break;
                
            case 'PHUHUYNH':
                $sql = "INSERT INTO phuhuynh (maNguoiDung, ngheNghiep, moiQuanHe) 
                        VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    $maNguoiDung,
                    $data['ngheNghiep'] ?? 'Kinh doanh',
                    $data['moiQuanHe'] ?? 'Cha'
                ]);
                error_log("✅ Created new PHUHUYNH record");
                break;
                
            case 'BGH':
                $sql = "INSERT INTO bangiamhieu (maNguoiDung) VALUES (?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$maNguoiDung]);
                error_log("✅ Created new BGH record");
                break;
                
            default:
                error_log("ℹ️ No detail table needed for role: " . $vaiTroMoi);
                break;
        }
        
    } catch (Exception $e) {
        error_log("⚠️ updateUserDetail warning: " . $e->getMessage());
        // KHÔNG throw - tiếp tục cập nhật
    }
}

private function getCurrentRole($maTaiKhoan) {
    try {
        $sql = "SELECT vaiTro FROM taikhoan WHERE maTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maTaiKhoan]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['vaiTro'] : null;
    } catch (Exception $e) {
        return null;
    }
}

private function deleteOldDetail($vaiTroCu, $maNguoiDung) {
    if (!$vaiTroCu || !$maNguoiDung) return;
    
    try {
        $tableMap = [
            'HOCSINH' => 'hocsinh',
            'GIAOVIEN' => 'giaovien',
            'PHUHUYNH' => 'phuhuynh',
            'BGH' => 'bangiamhieu'
        ];
        
        if (isset($tableMap[$vaiTroCu])) {
            $table = $tableMap[$vaiTroCu];
            $sql = "DELETE FROM $table WHERE maNguoiDung = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$maNguoiDung]);
            error_log("✅ Deleted old detail from " . $table);
        }
    } catch (Exception $e) {
        error_log("⚠️ deleteOldDetail warning: " . $e->getMessage());
    }
}

   public function deleteUser($id) {
    try {
        $this->conn->beginTransaction();
        
        // TẮT kiểm tra khóa ngoại tạm thời
        $this->conn->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // 1. Xóa tất cả dữ liệu liên quan từ các bảng chi tiết
        $this->deleteAllRelatedData($id);
        
        // 2. Xóa từ nguoidung
        $sql1 = "DELETE FROM nguoidung WHERE maTaiKhoan = ?";
        $stmt1 = $this->conn->prepare($sql1);
        $stmt1->execute([$id]);
        
        // 3. Xóa từ taikhoan
        $sql2 = "DELETE FROM taikhoan WHERE maTaiKhoan = ?";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute([$id]);
        
        // BẬT lại kiểm tra khóa ngoại
        $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        $this->conn->commit();
        return true;
        
    } catch (Exception $e) {
        $this->conn->rollBack();
        // Đảm bảo luôn bật lại khóa ngoại
        $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        error_log("💥 deleteUser error: " . $e->getMessage());
        throw new Exception("Không thể xóa tài khoản: " . $e->getMessage());
    }
}

private function deleteAllRelatedData($maTaiKhoan) {
    try {
        // Lấy maNguoiDung từ nguoidung
        $sql = "SELECT maNguoiDung FROM nguoidung WHERE maTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maTaiKhoan]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result || !isset($result['maNguoiDung'])) {
            return;
        }
        
        $maNguoiDung = $result['maNguoiDung'];
        
        // Xóa từ TẤT CẢ các bảng chi tiết có thể
        $tables = ['giaovien', 'hocsinh', 'phuhuynh', 'bangiamhieu'];
        
        foreach ($tables as $table) {
            try {
                $sql = "DELETE FROM $table WHERE maNguoiDung = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$maNguoiDung]);
                error_log("✅ Cleaned $table for user $maTaiKhoan");
            } catch (Exception $e) {
                // Bỏ qua lỗi nếu bảng không tồn tại hoặc không có dữ liệu
                error_log("⚠️ No data in $table for user $maTaiKhoan");
            }
        }
        
    } catch (Exception $e) {
        error_log("⚠️ deleteAllRelatedData warning: " . $e->getMessage());
        // KHÔNG throw - tiếp tục xóa
    }
}
  public function toggleUserStatus($id) {
    try {
        // CÁCH ĐƠN GIẢN NHẤT - update trực tiếp
        $sql = "UPDATE taikhoan SET trangThai = IF(trangThai = 'HOAT_DONG', 'DA_KHOA', 'HOAT_DONG') WHERE maTaiKhoan = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
        
    } catch (Exception $e) {
        return false;
    }
}

    public function updatePassword($tenDangNhap, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE taikhoan SET matKhau = ? WHERE tenDangNhap = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$hashedPassword, $tenDangNhap]);
            
        } catch (Exception $e) {
            return false;
        }
    }
}
?>