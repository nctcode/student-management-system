<?php
class QuanLyTaiKhoanController {
    private $model;

public function __construct() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 🔍 DEBUG CHI TIẾT
    error_log("=== QUANLYTAIKHOAN DEBUG ===");
    error_log("📋 Full SESSION: " . print_r($_SESSION, true));
    error_log("👤 User data: " . print_r($_SESSION['user'] ?? 'NO USER', true));
    
    $userRole = $_SESSION['user']['vaiTro'] ?? $_SESSION['user']['loaiNguoiDung'] ?? 'NO_ROLE';
    error_log("🎯 Detected role: " . $userRole);
    error_log("🔒 Required role: QTV");
    error_log("✅ Access granted: " . ($userRole === 'QTV' ? 'YES' : 'NO'));

    // Chỉ cho phép quản trị viên truy cập
    if (!isset($_SESSION['user']) || $userRole !== 'QTV') {
        error_log("❌ ACCESS DENIED - Redirecting to login");
        header('Location: index.php?controller=auth&action=login');
        exit;
    }

    error_log("✅ ACCESS GRANTED - Loading model");
    require_once 'models/TaiKhoanModel.php';
    $this->model = new TaiKhoanModel();
}
    // ✅ Hiển thị danh sách tài khoản (ĐÃ SỬA VỚI TÌM KIẾM)
    public function index() {
        $search_id = $_GET['search_id'] ?? '';
        $search_username = $_GET['search_username'] ?? '';
        
        // Gọi model để lấy danh sách tài khoản với điều kiện tìm kiếm
        $accounts = $this->model->getAllUsers($search_id, $search_username);
        
        require 'views/auth/QuanLiTaiKhoan.php';
    }

    // ✅ Hiển thị form tạo tài khoản riêng
    public function create() {
        require 'views/auth/QuanLiTaiKhoan_create.php';
    }
// ✅ Xử lý thêm tài khoản
public function store() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Chuẩn hóa dữ liệu từ POST
            $data = [
                'tenDangNhap' => $_POST['tenDangNhap'] ?? '',
                'matKhau' => $_POST['matKhau'] ?? '',
                'hoTen' => $_POST['hoTen'] ?? '',
                'vaiTro' => $_POST['vaiTro'] ?? 'USER',
                // HOCSINH
                'maLop' => $_POST['maLop'] ?? null,
                'maPhuHuynh' => $_POST['maPhuHuynh'] ?? null,
                'maHoSo' => $_POST['maHoSo'] ?? null,
                'ngayNhapHoc' => $_POST['ngayNhapHoc'] ?? date('Y-m-d'),
                'trangThai' => $_POST['trangThai'] ?? 'DANG_HOC',
                // PHUHUYNH
                'ngheNghiep' => $_POST['ngheNghiep'] ?? null,
                'moiQuanHe' => $_POST['moiQuanHe'] ?? null,
                // GIAOVIEN
                'chuyenMon' => $_POST['chuyenMon'] ?? null,
                'loaiGiaoVien' => $_POST['loaiGiaoVien'] ?? null,
                'maToTruong' => $_POST['maToTruong'] ?? null
            ];

            error_log("🎯 Creating user with details: " . $data['tenDangNhap']);

            $result = $this->model->createUser($data);

            if ($result) {
                $_SESSION['success'] = "Thêm tài khoản thành công!";
            } else {
                $_SESSION['error'] = "Không thể thêm tài khoản!";
            }

        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi thêm tài khoản: " . $e->getMessage();
            error_log("Store error: " . $e->getMessage());
        }

        header('Location: index.php?controller=QuanLyTaiKhoan&action=index');
        exit;
    }
}

    // ✅ Hiển thị form sửa tài khoản riêng
public function edit() {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        header('Location: index.php?controller=QuanLyTaiKhoan&action=index');
        exit;
    }

    // Lấy thông tin user từ model
    $userFromDB = $this->model->getUserById($id);

    if (!$userFromDB) {
        $_SESSION['error'] = "Không tìm thấy tài khoản!";
        header('Location: index.php?controller=QuanLyTaiKhoan&action=index');
        exit;
    }

    // ✅ SỬA: Ưu tiên vaiTro, nếu không có thì dùng loaiNguoiDung
    $user = [
        'id' => $userFromDB['maTaiKhoan'] ?? '',
        'username' => $userFromDB['tenDangNhap'] ?? '',
        'email' => $userFromDB['email'] ?? '',
        'vaiTro' => $userFromDB['vaiTro'] ?? $userFromDB['loaiNguoiDung'] ?? 'USER'
    ];

    require 'views/auth/QuanLiTaiKhoan_edit.php';
}

    // ✅ Cập nhật tài khoản
  // ✅ Cập nhật tài khoản
public function update() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            try {
                $data = [
                    'maTaiKhoan' => $id
                ];
                
                error_log("📤 POST data in update: " . print_r($_POST, true));
                
                // Chỉ cho phép QTV thay đổi vai trò
                if (isset($_SESSION['user']['vaiTro']) && $_SESSION['user']['vaiTro'] === 'QTV') {
                    $data['vaiTro'] = $_POST['vaiTro'] ?? 'USER';
                    error_log("🎯 Changing role to: " . $data['vaiTro']);
                    
                    // Thêm thông tin chi tiết nếu có
                    $data['maLop'] = $_POST['maLop'] ?? null;
                    $data['chuyenMon'] = $_POST['chuyenMon'] ?? null;
                    $data['ngheNghiep'] = $_POST['ngheNghiep'] ?? null;
                }
                
                // ✅ SỬA: Xử lý mật khẩu nếu có nhập - THÊM HASH
                if (!empty($_POST['new_password'])) {
                    if ($_POST['new_password'] === $_POST['confirm_password']) {
                        // THÊM DÒNG NÀY: HASH MẬT KHẨU TRƯỚC KHI LƯU
                        $data['matKhau'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                        error_log("🔑 Password will be updated (HASHED)");
                    } else {
                        $_SESSION['error'] = "Mật khẩu xác nhận không khớp!";
                        header('Location: index.php?controller=QuanLyTaiKhoan&action=edit&id=' . $id);
                        exit;
                    }
                }
                
                $result = $this->model->updateUser($data);
                
                if ($result) {
                    $_SESSION['success'] = "Cập nhật tài khoản thành công!";
                } else {
                    $_SESSION['error'] = "Cập nhật tài khoản thất bại!";
                }
                
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi khi cập nhật tài khoản: " . $e->getMessage();
                error_log("Update error: " . $e->getMessage());
            }
        }
        
        header('Location: index.php?controller=QuanLyTaiKhoan&action=index');
        exit;
    }
}
    // ✅ Xóa tài khoản
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model->deleteUser($id);
        }
        header('Location: index.php?controller=QuanLyTaiKhoan&action=index');
        exit;
    }

    // ✅ Khóa / Mở khóa tài khoản
    public function toggleStatus() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->model->toggleUserStatus($id);
        }
        header('Location: index.php?controller=QuanLyTaiKhoan&action=index');
        exit;
    }
}
?>