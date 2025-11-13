<?php
require_once __DIR__ . '/../models/PhanCongRaDeModel.php';

class PhanCongRaDeController
{
    private $model;

    public function __construct()
    {
        $this->model = new PhanCongRaDeModel();
    }

    public function index()
    {
        $danhSachPhanCong = $this->model->getAllPhanCong();
        include __DIR__ . '/../views/phancongrade/index.php';
    }

    public function create()
    {
        $danhSachKhoi = $this->model->getKhoi();
        $danhSachMonHoc = $this->model->getMonHoc();

        include __DIR__ . '/../views/phancongrade/create.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'tieuDe' => $_POST['tieuDe'] ?? '',
                'maKhoi' => $_POST['maKhoi'] ?? null,
                'maMonHoc' => $_POST['maMonHoc'] ?? null,
                'maGiaoVien' => $_POST['maGiaoVien'] ?? [], 
                'hanNopDe' => $_POST['hanNopDe'] ?? null,
                'soLuongDe' => $_POST['soLuongDe'] ?? 1,
                'noiDung' => $_POST['noiDung'] ?? '',
                'ghiChu' => $_POST['ghiChu'] ?? ''
            ];

            if (empty($data['maGiaoVien'])) {
                header('Location: index.php?controller=phancongrade&action=create&error=novp');
                exit;
            }

            $result = $this->model->createPhanCong($data);

            header('Location: index.php?controller=phancongrade');
            exit;
        }
    }
    public function getGiaoVienByMonHoc()
    {
        header('Content-Type: application/json');
        if (!isset($_GET['id_monhoc'])) {
            echo json_encode([]);
            exit;
        }

        $id_monhoc = $_GET['id_monhoc'];
        $danhSachGiaoVien = $this->model->getGiaoVienByMonHoc($id_monhoc);
        echo json_encode($danhSachGiaoVien);
        exit;
    }
}