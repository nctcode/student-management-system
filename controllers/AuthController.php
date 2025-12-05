<?php
require_once 'models/TaiKhoanModel.php';
class AuthController { 
    private $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        require_once 'models/TaiKhoanModel.php';
        $this->model = new TaiKhoanModel();
    }
    public function index() {
        // Kiểm tra nếu đã đăng nhập thì chuyển hướng về trang chủ
        if (isset($_SESSION['user'])) {
            $this->redirectByRole($_SESSION['user']['vaiTro']);
            return;
        }
        // Nếu chưa đăng nhập, chuyển đến trang login
        $this->login();
    }

    public function changePassword() {
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

            // Lấy thông tin tài khoản theo tên đăng nhập
            $user = $this->model->getUserByUsername($username);

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
            $this->model->updatePassword($username, $hashed);

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

    public function processRegister() {
        if ($_POST) {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $hoTen = trim($_POST['hoTen'] ?? '');
            $vaiTro = $_POST['vaiTro'] ?? 'PHUHUYNH'; // SỬA: PHUHUYNH thay vì PHU_HUYNH

            // Kiểm tra dữ liệu nhập
            $errors = [];
            if ($username === '' || $password === '' || $password_confirm === '' || $hoTen === '') {
                $errors[] = 'Vui lòng điền đầy đủ các trường bắt buộc.';
            }
            if ($password !== $password_confirm) {
                $errors[] = 'Mật khẩu và xác nhận mật khẩu không khớp.';
            }

            if (!empty($errors)) {
                $_SESSION['register_errors'] = $errors;
                $_SESSION['old'] = $_POST;
                header('Location: index.php?controller=auth&action=register');
                exit;
            }

            try {
                // Kiểm tra trùng username
                $existingUser = $this->model->getUserByUsername($username);
                if ($existingUser) {
                    $_SESSION['register_errors'] = ['Tên đăng nhập đã tồn tại.'];
                    $_SESSION['old'] = $_POST;
                    header('Location: index.php?controller=auth&action=register');
                    exit;
                }

                // Tạo user mới
                $data = [
                    'tenDangNhap' => $username,
                    'matKhau' => $password,
                    'hoTen' => $hoTen,
                    'vaiTro' => $vaiTro
                ];

                $result = $this->model->createUser($data);

                if ($result) {
                    // Lấy thông tin user vừa tạo để đăng nhập
                    $newUser = $this->model->getUserByUsername($username);
                    
                    if ($newUser) {
                        $_SESSION['user'] = [
                            'maTaiKhoan' => $newUser['maTaiKhoan'],
                            'tenDangNhap' => $newUser['tenDangNhap'],
                            'hoTen' => $hoTen,
                            'vaiTro' => $vaiTro
                        ];

                        $_SESSION['success'] = "Đăng ký thành công!";
                        header('Location: index.php?controller=home&action=index');
                        exit;
                    }
                }

                $_SESSION['register_errors'] = ['Lỗi đăng ký, vui lòng thử lại.'];
                header('Location: index.php?controller=auth&action=register');
                exit;

            } catch (Exception $e) {
                $_SESSION['register_errors'] = ['Lỗi đăng ký: ' . $e->getMessage()];
                $_SESSION['old'] = $_POST;
                header('Location: index.php?controller=auth&action=register');
                exit;
            }
        } else {
            header('Location: index.php?controller=auth&action=register');
            exit;
        }
    }
    
    public function login() {
        // Hiển thị view login
        require_once 'views/auth/login.php';
    }
    
 public function processLogin() {
    if ($_POST) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user = $this->model->authenticate($username, $password);
        
        // 🔥 KIỂM TRA TÀI KHOẢN BỊ KHÓA
        if ($user === "LOCKED") {
            $_SESSION['error'] = "⛔ Tài khoản của bạn đã bị khóa!";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // 🔥 KIỂM TRA SAI TÀI KHOẢN/ MẬT KHẨU
        if (!$user) {
            $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng!";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // ✅ NẾU ĐẾN ĐƯỢC ĐÂY THÌ ĐĂNG NHẬP THÀNH CÔNG
        // Lấy maNguoiDung từ database
        $maNguoiDung = $this->model->getMaNguoiDung($user['maTaiKhoan']);
        
        $_SESSION['user'] = [
            'maTaiKhoan' => $user['maTaiKhoan'],
            'tenDangNhap' => $user['tenDangNhap'],
            'hoTen' => $user['hoTen'],
            'vaiTro' => $user['vaiTro'],
            'maNguoiDung' => $maNguoiDung
        ];
        
        // 🆕 THÊM: Lấy mã phụ huynh nếu vai trò là PHUHUYNH
        if ($user['vaiTro'] === 'PHUHUYNH') {
            $maPhuHuynh = $this->model->getMaPhuHuynhByMaNguoiDung($maNguoiDung);
            if ($maPhuHuynh) {
                $_SESSION['user']['maPhuHuynh'] = $maPhuHuynh;
            }
        }
        
        // 🆕 THÊM: Lấy mã giáo viên nếu vai trò là GIAOVIEN
        if ($user['vaiTro'] === 'GIAOVIEN') {
            $maGiaoVien = $this->model->getMaGiaoVienByMaNguoiDung($maNguoiDung);
            if ($maGiaoVien) {
                $_SESSION['user']['maGiaoVien'] = $maGiaoVien;
            }
        }
        
        // Tìm đến phần này trong processLogin() (khoảng dòng 142-150):
        if ($user['vaiTro'] === 'HOCSINH') {
            $maHocSinh = $this->model->getMaHocSinhByMaNguoiDung($maNguoiDung);
            if ($maHocSinh) {
                $_SESSION['user']['maHocSinh'] = $maHocSinh;
                
                // 🔥 THÊM: Lấy thông tin lớp và khối của học sinh
                $studentInfo = $this->model->getStudentClassInfo($maHocSinh);
                if ($studentInfo) {
                    $_SESSION['user']['maLop'] = $studentInfo['maLop'];
                    $_SESSION['user']['tenLop'] = $studentInfo['tenLop'];
                    $_SESSION['user']['khoi'] = $studentInfo['khoi']; // Quan trọng: lấy khối
                    
                    // DEBUG: Ghi log để kiểm tra
                    error_log("Student Info for maHocSinh=$maHocSinh: " . print_r($studentInfo, true));
                } else {
                    error_log("WARNING: No student info found for maHocSinh=$maHocSinh");
                }
            }
        }
        
        // 🆕 THÊM: Lấy mã trường nếu vai trò là BGH
        if ($user['vaiTro'] === 'BGH') {
            $maTruong = $this->model->getMaTruongByMaNguoiDung($maNguoiDung);
            if ($maTruong) {
                $_SESSION['user']['maTruong'] = $maTruong;
            }
        }
        
        $this->redirectByRole($user['vaiTro']);
        return;
    }
    
    header('Location: index.php?controller=auth&action=login');
    exit;
}   private function redirectByRole($role) {
        error_log("Redirecting by role: " . $role);
        
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