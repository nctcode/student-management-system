<?php
require_once 'models/HanhKiemModel.php';

class HanhKiemController {
    private $model;

    public function __construct($db) {
        $this->model = new HanhKiemModel($db);
    }

    public function index() {
        $dsHanhKiem = $this->model->getAll();
        require 'views/hanh_kiem/index.php';
    }

    public function add() {
        // [ĐÃ SỬA]: Mình đã xóa dòng checkPermission()
        // Bây giờ ai vào cũng được, không bị chặn nữa
        $dsHocSinh = $this->model->getDanhSachHocSinh();
        require 'views/hanh_kiem/add.php';
    }

    public function store() {
        // [ĐÃ SỬA]: Xóa checkPermission() ở đây luôn
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sv_id = $_POST['sinh_vien_id'];
            $hoc_ky = $_POST['hoc_ky'];
            $diem = intval($_POST['diem_so']);
            $nhan_xet = $_POST['nhan_xet'];

            // Logic tự động xếp loại
            $xep_loai = 'Yếu';
            if ($diem >= 90) $xep_loai = 'Xuất sắc';
            elseif ($diem >= 80) $xep_loai = 'Tốt';
            elseif ($diem >= 65) $xep_loai = 'Khá';
            elseif ($diem >= 50) $xep_loai = 'Trung bình';

            if ($this->model->create($sv_id, $hoc_ky, $diem, $xep_loai, $nhan_xet)) {
                header("Location: index.php?controller=hanhkiem&action=index");
            } else {
                echo "Lỗi hệ thống!";
            }
        }
    }
}
?>