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

        // Lấy ID người dùng và vai trò từ session
        $userId = $_SESSION['user']['maNguoiDung'];
        $userRole = $_SESSION['user']['vaiTro'];
        
        // Gọi model để lấy thông tin chi tiết theo vai trò
        $userInfo = $this->model->layThongTinChiTietTheoVaiTro($userId, $userRole);

        // Lấy thêm thông tin cho TOTRUONG
        if ($userRole == 'TOTRUONG' && isset($userInfo['maToTruong'])) {
            $danhSachGiaoVien = $this->model->layGiaoVienTrongTo($userInfo['maToTruong']);
            $thongTinTo = $this->model->layThongTinToChuyenMon($userInfo['maToTruong']);
        }

        // Nạp file view và truyền dữ liệu
        include __DIR__ . '/../views/profile/index.php';
        exit();
    }
}
?>