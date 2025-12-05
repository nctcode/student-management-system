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
            
            // KHẮC PHỤC: Lấy nd.maTruong từ database
            $sql = "SELECT tk.*, nd.maNguoiDung, nd.hoTen, nd.loaiNguoiDung, nd.maTruong,
                           hs.maHocSinh, l.tenLop, k.tenKhoi as khoi
                    FROM taikhoan tk 
                    JOIN nguoidung nd ON tk.maTaiKhoan = nd.maTaiKhoan 
                    LEFT JOIN hocsinh hs ON nd.maNguoiDung = hs.maNguoiDung
                    LEFT JOIN lophoc l ON hs.maLop = l.maLop
                    LEFT JOIN khoi k ON l.maKhoi = k.maKhoi
                    WHERE tk.tenDangNhap = ? AND tk.trangThai = 'HOAT_DONG'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['matKhau'])) {
                
                // KHẮC PHỤC: Lưu maTruong vào session
                $_SESSION['user'] = [
                    'maNguoiDung' => $user['maNguoiDung'],
                    'hoTen' => $user['hoTen'],
                    'vaiTro' => $user['loaiNguoiDung'],
                    'maHocSinh' => $user['maHocSinh'] ?? null,
                    'tenLop' => $user['tenLop'] ?? null,
                    'khoi' => $user['khoi'] ?? null,
                    'maTruong' => $user['maTruong'] ?? null // DÒNG QUAN TRỌNG ĐÃ CÓ
                ];
                
                // Chuyển hướng theo vai trò
                $this->redirectByRole($user['loaiNguoiDung']);
                return;
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
            case 'TOTRUONG':
                header('Location: index.php?controller=home&action=leader');
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