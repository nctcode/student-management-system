<?php
require_once __DIR__ . '/../models/NguoiDungModel.php';

class ProfileController {
    private $model;

    public function __construct() {
        $this->model = new NguoiDungModel();
    }

    // Hành động mặc định (index)
    public function index() {
        // Kiểm tra xem session 'user' và 'maNguoiDung' có tồn tại không
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['maNguoiDung'])) {
            // Nếu không, quay về trang đăng nhập
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Lấy ID người dùng từ session
        $userId = $_SESSION['user']['maNguoiDung'];
        
        // Gọi model để lấy thông tin chi tiết
        $userInfo = $this->model->getUserById($userId);

        // Nạp file view và truyền dữ liệu $userInfo sang
        include __DIR__ . '/../views/profile/index.php';
    }
}
?>