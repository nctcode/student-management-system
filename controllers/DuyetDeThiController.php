<?php
require_once __DIR__ . '/../models/PhanCongRaDeModel.php';

class DuyetdethiController {
    private $model;

    public function __construct() {
        $this->model = new PhanCongRaDeModel();
    }

    public function index() {
        if (!isset($_GET['id'])) {
            die("Thiếu ID đề thi.");
        }
        $id = $_GET['id'];
        $dethi = $this->model->getDeThiById($id);

        if (!$dethi) {
            die("Không tìm thấy đề thi.");
        }
        
        include __DIR__ . '/../views/duyetdethi/index.php';
    }

    public function approve() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->model->updateTrangThaiDeThi($id, 'DA_DUYET');
        }
        header('Location: index.php?controller=phancongrade');
        exit;
    }

    public function reject() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->model->updateTrangThaiDeThi($id, 'TU_CHOI');
        }
        header('Location: index.php?controller=phancongrade');
        exit;
    }
}
?>