<?php
require_once 'models/DethiModel.php';

class TaoDeThiController
{
    private $model;

    public function __construct()
    {
        $this->model = new DethiModel();
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    // Hiển thị form tạo đề thi và danh sách đề thi đã tạo
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $userId = $_SESSION['user']['maNguoiDung'];

        // Lấy danh sách đề thi đã tạo của giáo viên này
        $deThiList = $this->model->getDeThiByGiaoVien($userId);

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php';
        require_once 'views/dethi/lapdethi.php';
    }

    // Xử lý submit tạo đề thi
    public function store()
    {
        $userId = $_SESSION['user']['maNguoiDung'];

        $maKhoi = $_POST['khoi'] ?? null;
        $maNienKhoa = $_POST['hocKy'] ?? null;
        $tieuDe = trim($_POST['tieuDe'] ?? '');
        $noiDungDeThi = trim($_POST['noiDungDeThi'] ?? '');
        $noiDungArr = $_POST['noiDung'] ?? [];
        $mucDiemArr = $_POST['mucDiem'] ?? [];

        if (!$maKhoi || !$maNienKhoa || !$tieuDe || !$noiDungDeThi || empty($noiDungArr)) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Thiếu dữ liệu'];
            header('Location: index.php?controller=taodethi&action=index');
            exit;
        }

        // Tạo đề thi 
        $maDeThi = $this->model->createExam($maKhoi, $maNienKhoa, $userId, $tieuDe, $noiDungDeThi);

        if (!$maDeThi) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Không tạo được đề thi'];
            header('Location: index.php?controller=taodethi&action=index');
            exit;
        }

        // Thêm câu hỏi kèm điểm
        $cauHoiArr = [];
        for ($i = 0; $i < count($noiDungArr); $i++) {
            if (trim($noiDungArr[$i]) !== '') {
                $cauHoiArr[] = [
                    'noiDung' => $noiDungArr[$i],
                    'mucDiem' => $mucDiemArr[$i] ?? 0
                ];
            }
        }
        $this->model->addQuestionsBatch($maDeThi, $cauHoiArr);

        $_SESSION['message'] = ['status' => 'success', 'text' => 'Tạo đề thi thành công'];
        header('Location: index.php?controller=taodethi&action=index');
        exit;
    }
}
