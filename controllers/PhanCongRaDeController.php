<?php
require_once __DIR__ . '/../models/PhanCongRaDeModel.php';

class PhanCongRaDeController {
    private $model;

    public function __construct() {
        $this->model = new PhanCongRaDeModel();
    }

    public function index() {
        $danhSachPhanCong = $this->model->getAllPhanCong();
        include __DIR__ . '/../views/phancongrade/index.php';
    }

    public function create() {
        $danhSachGiaoVien = $this->model->getGiaoVien();
        $danhSachKhoi = $this->model->getKhoi();
        $danhSachMonHoc = $this->model->getMonHoc();
        
        include __DIR__ . '/../views/phancongrade/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'tieuDe' => $_POST['tieuDe'] ?? '',
                'maKhoi' => $_POST['maKhoi'] ?? null,
                'maMonHoc' => $_POST['maMonHoc'] ?? null,
                'maGiaoVien' => $_POST['maGiaoVien'] ?? null,
                'hanNopDe' => $_POST['hanNopDe'] ?? null,
                'soLuongDe' => $_POST['soLuongDe'] ?? 1,
                'noiDung' => $_POST['noiDung'] ?? '',
                'ghiChu' => $_POST['ghiChu'] ?? ''
            ];

            $result = $this->model->createPhanCong($data);

            header('Location: index.php?controller=phancongrade');
            exit;
        }
    }
}
?>