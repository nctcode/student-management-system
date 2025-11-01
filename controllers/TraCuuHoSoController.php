<?php
require_once 'models/HoSoModel.php';

class TraCuuHoSoController { 
    private $model;

    public function __construct() {
        $this->model = new HoSoModel();
    }

    public function traCuuHoSo() { 
        if (empty($_SESSION['user']['maNguoiDung'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maHoSo = '';
        $hoSo = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['maHoSo'])) {
            $maHoSo = trim($_POST['maHoSo']);
            $hoSo = $this->model->getHoSoByMa($maHoSo);
        }

        require_once 'views/layouts/header.php';

        $role = $_SESSION['user']['vaiTro'] ?? '';
        if ($role === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } elseif ($role === 'PHUHUYNH') {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        }

        require_once 'views/tuyensinh/tracuuhosotuyensinh.php';
        require_once 'views/layouts/footer.php';
    }
}
?>
