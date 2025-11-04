<?php
class AuthController {
    
    public function login() {
        // Hiển thị view login
        require_once 'views/auth/login.php';
    }
    
    public function processLogin() {
        if ($_POST) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Kết nối database
            require_once 'models/Database.php';
            $db = new Database();
            $conn = $db->getConnection();
            
            // Kiểm tra thông tin đăng nhập - CẬP NHẬT TRUY VẤN
            $sql = "SELECT tk.*, nd.maNguoiDung, nd.hoTen, nd.loaiNguoiDung,
                           hs.maHocSinh, l.tenLop, k.tenKhoi as khoi
                    FROM taikhoan tk 
                    JOIN nguoidung nd ON tk.maTaiKhoan = nd.maTaiKhoan 
                    LEFT JOIN hocsinh hs ON nd.maNguoiDung = hs.maNguoiDung
                    LEFT JOIN lophoc l ON hs.maLop = l.maLop
                    LEFT JOIN khoi k ON l.maKhoi = k.maKhoi
                    WHERE tk.tenDangNhap = ? AND tk.trangThai = 'HOAT_DONG'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($result) > 0) {
                $user = $result[0];
                
                // Kiểm tra mật khẩu (trong thực tế dùng password_verify)
                // Demo: so sánh với mật khẩu cố định 123456
                if ($password === '123456' || password_verify($password, $user['matKhau'])) {
                    // Lưu thông tin người dùng vào session - CẬP NHẬT THÔNG TIN
                    $_SESSION['user'] = [
                        'maTaiKhoan' => $user['maTaiKhoan'],
                        'maNguoiDung' => $user['maNguoiDung'],
                        'tenDangNhap' => $user['tenDangNhap'],
                        'hoTen' => $user['hoTen'],
                        'vaiTro' => $user['loaiNguoiDung'],
                        'maHocSinh' => $user['maHocSinh'] ?? null,
                        'tenLop' => $user['tenLop'] ?? null,
                        'khoi' => $user['khoi'] ?? null
                    ];
                    
                    // Chuyển hướng theo vai trò
                    $this->redirectByRole($user['loaiNguoiDung']);
                    return;
                }
            }
            
            // Đăng nhập thất bại
            header('Location: index.php?controller=auth&action=login&error=1');
            exit;
        }
    }
    
    private function redirectByRole($role) {
        switch ($role) {
            case 'QTV':
                header('Location: index.php?controller=home&action=admin');
                break;
            case 'GIAOVIEN':
                header('Location: index.php?controller=home&action=teacher');
                break;
            case 'HOCSINH':
                header('Location: index.php?controller=home&action=student');
                break;
            case 'PHUHUYNH':
                header('Location: index.php?controller=home&action=parent');
                break;
            case 'BGH':
                header('Location: index.php?controller=home&action=principal');
                break;
            default:
                header('Location: index.php?controller=home&action=index');
        }
        exit;
    }
    
    public function logout() {
        session_destroy();
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}
?>