<?php
require_once 'models/DethiModel.php';

class DuyetDeThiController
{
    private $model;

    public function __construct()
    {
        $this->model = new DethiModel();
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    // Trang duyệt đề thi
    public function duyet()
    {
        $user = $_SESSION['user'];

        // Lấy danh sách Khối học và Niên khóa để combobox
        $khoiHocList = $this->model->getKhoiHoc();
        $nienKhoaList = $this->model->getNienKhoa();

        // Lấy dữ liệu filter nếu có
        $maKhoi = $_GET['maKhoi'] ?? null;
        $maNienKhoa = $_GET['maNienKhoa'] ?? null;

        //Kiểm tra dữ liệu lọc
        if ((isset($_GET['maKhoi']) || isset($_GET['maNienKhoa'])) && (empty($maKhoi) || empty($maNienKhoa))) {
            $_SESSION['message'] = 'Vui lòng chọn đầy đủ Khối học và Học kỳ trước khi lọc!';
            $_SESSION['type'] = 'error';
        }
        // Danh sách đề thi
        $exams = [];
        if ($maKhoi && $maNienKhoa) {
            $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? null;
            $exams = $this->model->getExams($maKhoi, $maNienKhoa, $maNguoiDung);
        }

        // Nếu user chọn một đề thi, lấy chi tiết và câu hỏi
        $maDeThi = $_GET['maDeThi'] ?? null;
        $examDetail = [];
        $questions = [];
        if ($maDeThi) {
            $examDetail = $this->model->getExamDetail($maDeThi);
            $questions = $this->model->getQuestions($maDeThi);
        }

        // Load view
        require 'views/duyetdethi/pheduyetdethi.php';
    }


    // Xử lý duyệt/từ chối đề thi
    public function capNhatTrangThai()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maDeThi = $_POST['maDeThi'] ?? null;
            $hanhDong = $_POST['hanhDong'] ?? '';
            $ghiChu = trim($_POST['ghiChu'] ?? '');
            $maKhoi = $_POST['maKhoi'] ?? null;
            $maNienKhoa = $_POST['maNienKhoa'] ?? null;

            if (!$maKhoi || !$maNienKhoa) {
                $_SESSION['message'] = '❗ Vui lòng chọn Khối và Niên khóa trước khi duyệt!';
                $_SESSION['type'] = 'error';
            } elseif ($hanhDong === 'tuchoi' && $ghiChu === '') {
                $_SESSION['message'] = '❗ Vui lòng nhập lý do từ chối!';
                $_SESSION['type'] = 'error';
            } else {
                $trangThai = $hanhDong === 'duyet' ? 'DA_DUYET' : 'TU_CHOI';
                require_once 'models/Database.php';
                $db = new Database();
                $pdo = $db->getConnection();
                $sql = "UPDATE dethi SET trangThai = :trangThai, ghiChu = :ghiChu WHERE maDeThi = :maDeThi";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'trangThai' => $trangThai,
                    'ghiChu' => $ghiChu,
                    'maDeThi' => $maDeThi
                ]);

                $msg = $trangThai === 'DA_DUYET' ? '✅ Đã duyệt đề thi thành công!' : '❌ Đã từ chối đề thi!';
                $_SESSION['message'] = $msg;
                $_SESSION['type'] = 'success';
            }

            // **Redirect về trang duyệt nhưng bỏ maDeThi để ẩn chi tiết**
            header("Location: index.php?controller=duyetdethi&action=duyet&maKhoi=$maKhoi&maNienKhoa=$maNienKhoa");
            exit;
        }
    }


    // Trang lịch sử duyệt đề
    public function lichSuDuyetDeThi()
    {
        require_once 'models/DethiModel.php';
        $model = new DethiModel();

        $khoiHocList = $model->getKhoiHoc();
        $nienKhoaList = $model->getNienKhoa();

        $maKhoi = $_GET['maKhoi'] ?? null;
        $maNienKhoa = $_GET['maNienKhoa'] ?? null;
        $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? null;

        $deDaDuyet = [];
        $deTuChoi = [];
        $message = '';
        $type = '';

        // Xử lý lọc
        if (isset($_GET['maKhoi']) || isset($_GET['maNienKhoa'])) {
            if (empty($maKhoi) || empty($maNienKhoa)) {
                $message = 'Vui lòng chọn đầy đủ Khối học và Học kỳ trước khi lọc!';
                $type = 'error';
            } else {
                $lichSu = $model->getLichSuDuyet($maKhoi, $maNienKhoa, $maNguoiDung);

                foreach ($lichSu as $row) {
                    if ($row['trangThai'] === 'DA_DUYET') {
                        $deDaDuyet[] = $row;
                    } elseif ($row['trangThai'] === 'TU_CHOI') {
                        $deTuChoi[] = $row;
                    }
                }
            }
        }

        // Xử lý chi tiết đề thi
        $examDetail = null;
        $questions = [];
        if (!empty($_GET['maDeThi'])) {
            $maDeThi = $_GET['maDeThi'];
            $examDetail = $model->getExamDetail($maDeThi);
            $questions = $model->getQuestions($maDeThi);
        }

        // Gọi view, truyền đầy đủ biến
        require_once 'views/duyetdethi/lichsuduyetde.php';
    }
}
