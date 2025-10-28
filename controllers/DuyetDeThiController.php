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

        // Danh sách đề thi
        $exams = [];
        if ($maKhoi && $maNienKhoa) {
            $exams = $this->model->getExams($maKhoi, $maNienKhoa);
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
            $hanhDong = $_POST['hanhDong'] ?? null;

            if ($maDeThi && $hanhDong) {
                if ($hanhDong === 'duyet') {
                    $trangThai = 'DA_DUYET';
                } elseif ($hanhDong === 'tuchoi') {
                    $trangThai = 'TU_CHOI';
                } else {
                    return;
                }

                // Kết nối CSDL
                require_once 'models/Database.php';
                $db = new Database();
                $pdo = $db->getConnection();

                // Cập nhật trạng thái
                $sql = "UPDATE dethi SET trangThai = :trangThai WHERE maDeThi = :maDeThi";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'trangThai' => $trangThai,
                    'maDeThi' => $maDeThi
                ]);

                // Hiển thị thông báo
                if ($trangThai === 'DA_DUYET') {
                    echo "<script>
                            alert('✅ Đã duyệt đề thi thành công!');
                            window.location.href = 'index.php?controller=duyetdethi&action=duyet';
                        </script>";
                } elseif ($trangThai === 'TU_CHOI') {
                    echo "<script>
                            alert('❌ Đã từ chối đề thi!');
                            window.location.href = 'index.php?controller=duyetdethi&action=duyet';
                        </script>";
                }
            }
        }
    }
}
