<?php
require_once 'models/DethiModel.php';

class DeThiController
{
    private $model;

    public function __construct()
    {
        $this->model = new DethiModel();
        if (!isset($_SESSION)) session_start();
    }

    // Trang tạo đề thi
    public function index()
    {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        // Lấy danh sách đề thi của giáo viên
        $deThiList = $this->model->getDeThiByGiaoVien($maNguoiDung);

        // Gọi view
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php';
        require_once 'views/dethi/lapdethi.php';
    }

    // Xử lý tạo đề thi
    public function store()
    {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        // Lấy thông tin giáo viên
        $giaoVien = $this->model->getGiaoVienByMaNguoiDung($maNguoiDung);
        if (!$giaoVien) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Không tìm thấy giáo viên'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        // Lấy dữ liệu từ form
        $khoi   = $_POST['khoi'] ?? null;
        $hocKy  = $_POST['hocKy'] ?? null;
        $tieuDe = trim($_POST['tieuDe'] ?? '');

        if (!$khoi || !$hocKy || !$tieuDe) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Thiếu thông tin bắt buộc'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        // Kiểm tra file upload
        if (empty($_FILES['fileDeThi']['name'])) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Vui lòng chọn file đề thi'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        $fileTmp  = $_FILES['fileDeThi']['tmp_name'];
        $fileName = basename($_FILES['fileDeThi']['name']);
        $fileSize = $_FILES['fileDeThi']['size'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['pdf', 'doc', 'docx'];

        // Kiểm tra định dạng
        if (!in_array($fileExt, $allowedExt)) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Chỉ được tải lên file PDF hoặc Word'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        // Kiểm tra kích thước <= 10MB
        if ($fileSize > 10 * 1024 * 1024) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'File không được vượt quá 10MB'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        // Tạo thư mục nếu chưa tồn tạ
        $folder = 'uploads/dethi/';
        if (!file_exists($folder)) mkdir($folder, 0777, true);

        // Tạo tên file mới tránh trùng lặp
        $newFileName = time() . "_" . $fileName;
        if (!move_uploaded_file($fileTmp, $folder . $newFileName)) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Upload file thất bại'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        // Chuẩn bị dữ liệu insert
        $data = [
            'maGiaoVien' => $giaoVien['maGiaoVien'],
            'maMonHoc'   => $giaoVien['maMonHoc'],
            'maKhoi'     => $khoi,
            'maNienKhoa' => $hocKy,
            'tieuDe'     => $tieuDe,
            'noiDung'    => $newFileName,
            'ngayNop'    => date('Y-m-d H:i:s'),
            'trangThai'  => 'CHO_DUYET'
        ];

        // Thêm vào database
        $result = $this->model->createDeThi($data);

        $_SESSION['message'] = $result
            ? ['status' => 'success', 'text' => 'Tạo đề thi thành công']
            : ['status' => 'error', 'text' => 'Tạo đề thi thất bại'];

        header('Location: index.php?controller=deThi&action=index');
        exit;
    }


    ///////////////////////////////////////////////////////////////////////
    ////////////////////////////DUYỆT ĐỀ THI///////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function duyet()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $toTruong = $this->model->getToTruongByMaNguoiDung($maNguoiDung);
        if (!$toTruong) {
            $_SESSION['message'] = 'Không tìm thấy thông tin tổ trưởng chuyên môn';
            header('Location: index.php');
            exit;
        }

        $maMonHoc = $toTruong['maMonHoc'];
        $maKhoi = $_GET['maKhoi'] ?? null;
        $maNienKhoa = $_GET['maNienKhoa'] ?? null;
        $maDeThi = $_GET['maDeThi'] ?? null;

        // Lấy danh sách đề thi chưa duyệt
        $exams = $this->model->getDeThi($maMonHoc, $maKhoi, $maNienKhoa);
        $examDetail = $maDeThi ? $this->model->getDeThiById($maDeThi) : null;

        // --- Lấy danh sách Khối và Niên khóa ---
        $khoiHocModel = new DeThiModel();
        $khoiHocList = $khoiHocModel->getAllKhoiHoc();

        $nienKhoaModel = new DeThiModel();
        $nienKhoaList = $nienKhoaModel->getAllNienKhoa();

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/totruong.php';
        require_once 'views/dethi/duyetdethi.php';
        require_once 'views/layouts/footer.php';
    }


    public function capNhatTrangThai()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maDeThi = $_POST['maDeThi'] ?? null;
        $hanhDong = $_POST['hanhDong'] ?? null;
        $ghiChu = trim($_POST['ghiChu'] ?? '');

        $message = ['status' => 'danger', 'text' => '']; // mặc định đỏ

        if (!$maDeThi || !$hanhDong) {
            $message['text'] = 'Thiếu dữ liệu';
        } elseif ($hanhDong === 'duyet') {
            $trangThai = 'DA_DUYET';
            $result = $this->model->capNhatTrangThai($maDeThi, $trangThai);
            $message['status'] = $result ? 'success' : 'danger';
            $message['text'] = $result ? 'Duyệt đề thi thành công' : 'Cập nhật thất bại';
        } elseif ($hanhDong === 'tuchoi') {
            if (empty($ghiChu)) {
                $message['text'] = 'Vui lòng nhập lý do từ chối';
            } else {
                $trangThai = 'TU_CHOI';
                $result = $this->model->capNhatTrangThai($maDeThi, $trangThai, $ghiChu);
                $message['status'] = $result ? 'success' : 'danger';
                $message['text'] = $result ? 'Từ chối đề thi thành công' : 'Cập nhật thất bại';
            }
        } else {
            $message['text'] = 'Hành động không hợp lệ';
        }

        $_SESSION['message'] = $message;

        // Chuyển về trang duyệt, giữ nguyên khối/học kỳ nếu có
        $maKhoi = $_POST['maKhoi'] ?? '';
        $maNienKhoa = $_POST['maNienKhoa'] ?? '';
        header("Location: index.php?controller=dethi&action=duyet&maKhoi={$maKhoi}&maNienKhoa={$maNienKhoa}");
        exit;
    }


    ///////////////////////////////////////////////////////////////////////
    ////////////////////////////LỊCH SỬ DUYỆT//////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    public function lichSuDuyetDeThi()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $maKhoi = $_GET['maKhoi'] ?? null;
        $maNienKhoa = $_GET['maNienKhoa'] ?? null;
        $maDeThi = $_GET['maDeThi'] ?? null;

        // Chỉ lấy danh sách đề thi khi đã chọn khối hoặc học kỳ
        $exams = [];
        if ($maKhoi || $maNienKhoa) {
            $exams = $this->model->getLichSuDuyetDeThi($maNguoiDung, $maKhoi, $maNienKhoa);
        }

        // Nếu có maDeThi, lấy chi tiết đề thi
        $examDetail = null;
        if ($maDeThi) {
            $examDetail = $this->model->getDeThiById($maDeThi);
        }

        // Lấy danh sách Khối và Niên khóa để filter
        $khoiHocList = $this->model->getAllKhoiHoc();
        $nienKhoaList = $this->model->getAllNienKhoa();

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/totruong.php';
        require_once 'views/dethi/lichsuduyetde.php';
    }
}
