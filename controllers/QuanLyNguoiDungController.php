<?php
require_once 'models/ThongTinNguoiDungModel.php';

class QuanLyNguoiDungController
{
    private $model;

    public function __construct()
    {
        $this->model = new ThongTinNguoiDungModel();
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    // Trang cập nhật thông tin
    public function index()
    {
        $userData = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maNguoiDung'])) {
            $maNguoiDung = trim($_POST['maNguoiDung']);
            $userData = $this->model->getUserById($maNguoiDung);
            if (!$userData) {
                $_SESSION['message'] = "Không tìm thấy người dùng có mã: $maNguoiDung";
                $_SESSION['message_type'] = 'alert-error';
                header("Location: ?controller=QuanLyNguoiDung&action=index");
                exit;
            } else {
                // Nếu tìm thấy thì redirect để URL chứa mã người dùng, tránh POST resubmission
                header("Location: ?controller=QuanLyNguoiDung&action=index&maNguoiDung=$maNguoiDung");
                exit;
            }
        }

        // Nếu có maNguoiDung trên URL (sau redirect)
        if (isset($_GET['maNguoiDung'])) {
            $maNguoiDung = trim($_GET['maNguoiDung']);
            $userData = $this->model->getUserById($maNguoiDung);
            if (!$userData) {
                $_SESSION['message'] = "Không tìm thấy người dùng có mã: $maNguoiDung";
                $_SESSION['message_type'] = 'alert-error';
                header("Location: ?controller=QuanLyNguoiDung&action=index");
                exit;
            }
        }

        require 'views/layouts/header.php';
        require 'views/layouts/sidebar/admin.php';
        require 'views/nguoidung/capnhatthongtin.php';
        exit();
    }

    // Xử lý cập nhật thông tin người dùng
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maNguoiDung = trim($_POST['maNguoiDung']);

            // Lấy dữ liệu hiện tại
            $currentData = $this->model->getUserById($maNguoiDung);
            if (!$currentData) {
                $_SESSION['message'] = "Người dùng không tồn tại!";
                $_SESSION['message_type'] = 'alert-error';
                header("Location: ?controller=QuanLyNguoiDung&action=index");
                exit;
            }

            // Lấy dữ liệu mới, nếu rỗng giữ nguyên
            $hoTen = !empty($_POST['hoTen']) ? trim($_POST['hoTen']) : $currentData['hoTen'];
            $ngaySinh = !empty($_POST['ngaySinh']) ? trim($_POST['ngaySinh']) : $currentData['ngaySinh'];
            $gioiTinh = !empty($_POST['gioiTinh']) ? trim($_POST['gioiTinh']) : $currentData['gioiTinh'];
            $soDienThoai = !empty($_POST['soDienThoai']) ? trim($_POST['soDienThoai']) : $currentData['soDienThoai'];
            $email = !empty($_POST['email']) ? trim($_POST['email']) : $currentData['email'];
            $diaChi = !empty($_POST['diaChi']) ? trim($_POST['diaChi']) : $currentData['diaChi'];
            $cccd = !empty($_POST['cccd']) ? trim($_POST['cccd']) : $currentData['cccd'];

            // VALIDATION
            $errors = [];

            if (!empty($_POST['ngaySinh']) && (!strtotime($ngaySinh) || strtotime($ngaySinh) > time())) {
                $errors[] = "Ngày sinh không hợp lệ!";
            }

            if (!empty($_POST['gioiTinh']) && !in_array($gioiTinh, ['Nam', 'Nữ'])) {
                $errors[] = "Giới tính phải là Nam hoặc Nữ!";
            }

            if (!empty($_POST['soDienThoai']) && !preg_match("/^\d{9,12}$/", $soDienThoai)) {
                $errors[] = "Số điện thoại phải là 9-12 chữ số!";
            }

            if (!empty($_POST['email']) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không hợp lệ!";
            }

            if (!empty($_POST['cccd']) && !preg_match("/^\d{9,12}$/", $cccd)) {
                $errors[] = "CCCD phải là 9-12 chữ số!";
            }

            // Nếu có lỗi thì chuyển về trang index
            if (!empty($errors)) {
                $_SESSION['message'] = implode("<br>", $errors);
                $_SESSION['message_type'] = 'alert-error';
                header("Location: ?controller=QuanLyNguoiDung&action=index&maNguoiDung=$maNguoiDung");
                exit;
            }

            // Cập nhật dữ liệu
            $data = [
                'hoTen' => $hoTen,
                'ngaySinh' => $ngaySinh,
                'gioiTinh' => $gioiTinh,
                'soDienThoai' => $soDienThoai,
                'email' => $email,
                'diaChi' => $diaChi,
                'cccd' => $cccd
            ];

            $result = $this->model->updateUser($maNguoiDung, $data);

            if ($result) {
                $_SESSION['message'] = "Cập nhật thông tin người dùng thành công!";
                $_SESSION['message_type'] = 'alert-success';
            } else {
                $_SESSION['message'] = "Cập nhật thất bại, vui lòng thử lại!";
                $_SESSION['message_type'] = 'alert-error';
            }

            header("Location: ?controller=QuanLyNguoiDung&action=index&maNguoiDung=$maNguoiDung");
            exit;
        }
    }
}
