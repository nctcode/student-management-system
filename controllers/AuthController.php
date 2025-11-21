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
            
<<<<<<< HEAD
            // KHẮC PHỤC: Lấy nd.maTruong từ database
=======
            // Kiểm tra thông tin đăng nhập
            // $sql = "SELECT tk.*, nd.maNguoiDung, nd.hoTen, nd.loaiNguoiDung 
            //         FROM taikhoan tk 
            //         JOIN nguoidung nd ON tk.maTaiKhoan = nd.maTaiKhoan 
            //         WHERE tk.tenDangNhap = ? AND tk.trangThai = 'HOAT_DONG'";
>>>>>>> b9b80e75bb6b4268557a0dd832104badc968ba5b
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
                
<<<<<<< HEAD
                // KHẮC PHỤC: Lưu maTruong vào session
                $_SESSION['user'] = [
=======
                // Kiểm tra mật khẩu (trong thực tế dùng password_verify)
                // Demo: so sánh với mật khẩu cố định 123456
               if (password_verify($password, $user['matKhau']) || md5($password) === $user['matKhau']) {
                    // Lưu thông tin người dùng vào session
                    // $_SESSION['user'] = [
                    //     'maTaiKhoan' => $user['maTaiKhoan'],
                    //     'maNguoiDung' => $user['maNguoiDung'],
                    //     'tenDangNhap' => $user['tenDangNhap'],
                    //     'hoTen' => $user['hoTen'],
                    //     'vaiTro' => $user['loaiNguoiDung']
                    // ];
                    $_SESSION['user'] = [
>>>>>>> b9b80e75bb6b4268557a0dd832104badc968ba5b
                    'maNguoiDung' => $user['maNguoiDung'],
                    'hoTen' => $user['hoTen'],
                    'vaiTro' => $user['loaiNguoiDung'],
                    'maHocSinh' => $user['maHocSinh'] ?? null,
                    'tenLop' => $user['tenLop'] ?? null,
                    'khoi' => $user['khoi'] ?? null,
                    'maTruong' => $user['maTruong'] ?? null // DÒNG QUAN TRỌNG ĐÃ CÓ
                ];
<<<<<<< HEAD
                
                // Chuyển hướng theo vai trò
                $this->redirectByRole($user['loaiNguoiDung']);
                return;
=======
                    
                    // Chuyển hướng theo vai trò
                    $this->redirectByRole($user['loaiNguoiDung']);
                    return;
                }
>>>>>>> b9b80e75bb6b4268557a0dd832104badc968ba5b
            }
            
            // Đăng nhập thất bại
            header('Location: index.php?controller=auth&action=login&error=1');
            exit;
        }
    }
    public function changePassword() {
    // Đảm bảo chỉ start session 1 lần
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Kiểm tra người dùng đã đăng nhập chưa
    if (!isset($_SESSION['user'])) {
        header("Location: index.php?controller=auth&action=login");
        exit;
    }

    // Khi người dùng submit form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $old = $_POST['old_password'];
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        // Lấy username từ session (đúng với cấu trúc bảng taikhoan)
        $username = $_SESSION['user']['tenDangNhap'];

        // Gọi model xử lý
        require_once __DIR__ . '/../models/TaiKhoanModel.php';
        $userModel = new TaiKhoanModel();

        // Lấy thông tin tài khoản theo tên đăng nhập
        $user = $userModel->getUserByUsername($username);

        // Kiểm tra mật khẩu cũ
        if (!$user || !password_verify($old, $user['matKhau'])) {
            $_SESSION['message'] = "❌ Mật khẩu cũ không đúng!";
            header("Location: index.php?controller=auth&action=changePassword");
            exit;
        }

        // Kiểm tra xác nhận mật khẩu mới
        if ($new !== $confirm) {
            $_SESSION['message'] = "⚠️ Mật khẩu xác nhận không khớp!";
            header("Location: index.php?controller=auth&action=changePassword");
            exit;
        }

        // Mã hoá và cập nhật mật khẩu
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $userModel->updatePassword($username, $hashed);

        $_SESSION['message'] = "✅ Đổi mật khẩu thành công!";
        header("Location: index.php?controller=auth&action=changePassword");
        exit;
    }

    // Hiển thị form đổi mật khẩu
    include 'views/auth/change_password.php';
}

        public function register() {
        // Hiển thị form đăng ký
        require_once 'views/auth/register.php';
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