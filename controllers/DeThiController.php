<?php
require_once 'models/DethiModel.php';
require_once 'models/Database.php';
class DeThiController
{
    private $model;
    private $conn;
    public function __construct()
    {
        $this->model = new DethiModel();
        $db = new Database();
        $this->conn = $db->getConnection();
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

        // Lấy thông tin giáo viên
        $giaoVien = $this->model->getGiaoVienByMaNguoiDung($maNguoiDung);
        if (!$giaoVien) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Không tìm thấy giáo viên'];
            require_once 'views/dethi/lapdethi.php';
            return;
        }

        // KIỂM TRA PHÂN CÔNG MỚI
        $phanCong = $this->model->getPhanCongGiaoVien($giaoVien['maGiaoVien']);
        
        // Lấy danh sách đề thi của giáo viên
        $deThiList = $this->model->getDeThiByGiaoVien($maNguoiDung);

        // Gọi view với thông tin phân công
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php';
        require_once 'views/dethi/lapdethi.php';
        exit();
    }

    public function view()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maDeThi = $_GET['id'] ?? 0;
        if ($maDeThi <= 0) {
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        $deThi = $this->model->getDeThiById($maDeThi);
        if (!$deThi) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Không tìm thấy đề thi'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php';
        require_once 'views/dethi/chitiet.php';
        require_once 'views/layouts/footer.php';
        exit();
    }

    public function edit()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maDeThi = $_GET['id'] ?? 0;
        if ($maDeThi <= 0) {
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        $deThi = $this->model->getDeThiById($maDeThi);
        if (!$deThi) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Không tìm thấy đề thi'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        // Kiểm tra quyền sửa (chỉ giáo viên tạo và đề thi chờ duyệt/từ chối)
        $giaoVien = $this->model->getGiaoVienByMaNguoiDung($_SESSION['user']['maNguoiDung']);
        if (!$giaoVien || $giaoVien['maGiaoVien'] != $deThi['maGiaoVien']) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Bạn không có quyền sửa đề thi này'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        if (!in_array($deThi['trangThai'], ['CHO_DUYET', 'TU_CHOI'])) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Chỉ được sửa đề thi đang chờ duyệt hoặc bị từ chối'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php';
        require_once 'views/dethi/edit.php';
        require_once 'views/layouts/footer.php';
        exit();
    }

    public function update()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        $maDeThi = $_POST['maDeThi'] ?? 0;
        $tieuDe = trim($_POST['tieuDe'] ?? '');
        $noiDungBoSung = trim($_POST['noiDungBoSung'] ?? '');

        if ($maDeThi <= 0 || empty($tieuDe)) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Thiếu thông tin bắt buộc'];
            header('Location: index.php?controller=deThi&action=edit&id=' . $maDeThi);
            exit;
        }

        // Kiểm tra file upload nếu có
        $newFileName = null;
        if (!empty($_FILES['fileDeThi']['name'])) {
            $fileTmp = $_FILES['fileDeThi']['tmp_name'];
            $fileName = basename($_FILES['fileDeThi']['name']);
            $fileSize = $_FILES['fileDeThi']['size'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExt = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

            if (!in_array($fileExt, $allowedExt)) {
                $_SESSION['message'] = ['status' => 'error', 'text' => 'Chỉ được tải lên file PDF, Word hoặc hình ảnh'];
                header('Location: index.php?controller=deThi&action=edit&id=' . $maDeThi);
                exit;
            }

            if ($fileSize > 10 * 1024 * 1024) {
                $_SESSION['message'] = ['status' => 'error', 'text' => 'File không được vượt quá 10MB'];
                header('Location: index.php?controller=deThi&action=edit&id=' . $maDeThi);
                exit;
            }

            // Tạo thư mục nếu chưa tồn tại
            $folder = 'uploads/dethi/';
            if (!file_exists($folder)) mkdir($folder, 0777, true);

            // Tạo tên file mới
            $newFileName = time() . "_" . $fileName;
            if (!move_uploaded_file($fileTmp, $folder . $newFileName)) {
                $_SESSION['message'] = ['status' => 'error', 'text' => 'Upload file thất bại'];
                header('Location: index.php?controller=deThi&action=edit&id=' . $maDeThi);
                exit;
            }
        }

        // Cập nhật database
        try {
            $sql = "UPDATE dethi SET tieuDe = :tieuDe, ngayNop = NOW(), trangThai = 'CHO_DUYET'";
            $params = [
                'tieuDe' => $tieuDe,
                'maDeThi' => $maDeThi
            ];

            if ($newFileName) {
                $sql .= ", noiDung = :noiDung";
                $params['noiDung'] = $newFileName;
            }

            if (!empty($noiDungBoSung)) {
                $sql .= ", ghiChu = :ghiChu";
                $params['ghiChu'] = $noiDungBoSung;
            }

            $sql .= " WHERE maDeThi = :maDeThi";

            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($params);

            if ($result) {
                $_SESSION['message'] = ['status' => 'success', 'text' => 'Cập nhật đề thi thành công'];
            } else {
                $_SESSION['message'] = ['status' => 'error', 'text' => 'Cập nhật đề thi thất bại'];
            }
        } catch (Exception $e) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Lỗi: ' . $e->getMessage()];
        }

        header('Location: index.php?controller=deThi&action=view&id=' . $maDeThi);
        exit;
    }

    public function delete()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        $maDeThi = $_POST['id'] ?? 0;
        
        if ($maDeThi <= 0) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'ID không hợp lệ'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        // Kiểm tra quyền xóa
        $deThi = $this->model->getDeThiById($maDeThi);
        $giaoVien = $this->model->getGiaoVienByMaNguoiDung($_SESSION['user']['maNguoiDung']);
        
        if (!$giaoVien || $giaoVien['maGiaoVien'] != $deThi['maGiaoVien']) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Bạn không có quyền xóa đề thi này'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        if (!in_array($deThi['trangThai'], ['CHO_DUYET', 'TU_CHOI'])) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Chỉ được xóa đề thi đang chờ duyệt hoặc bị từ chối'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        // Xóa file vật lý nếu có
        if (!empty($deThi['noiDung'])) {
            $filePath = 'uploads/dethi/' . $deThi['noiDung'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Xóa trong database
        $sql = "DELETE FROM dethi WHERE maDeThi = :maDeThi";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute(['maDeThi' => $maDeThi]);

        if ($result) {
            $_SESSION['message'] = ['status' => 'success', 'text' => 'Xóa đề thi thành công'];
        } else {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Xóa đề thi thất bại'];
        }

        header('Location: index.php?controller=deThi&action=index');
        exit;
    }


    ///////////////////////////////////////////////////////////////////////
    ////////////////////////////LẬP ĐỀ THI///////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    // Xử lý tạo đề thi - CẬP NHẬT THAY VÌ TẠO MỚI NẾU ĐÃ CÓ PHÂN CÔNG
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

        // KIỂM TRA PHÂN CÔNG GIÁO VIÊN
        $phanCong = $this->model->getPhanCongGiaoVien($giaoVien['maGiaoVien']);
        if (!$phanCong) {
            $_SESSION['message'] = [
                'status' => 'error',
                'text' => 'Bạn chưa được phân công tạo đề thi'
            ];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        // Lấy dữ liệu từ form
        $khoi   = $_POST['khoi'] ?? null;
        $hocKy  = $_POST['hocKy'] ?? null;
        $tieuDe = trim($_POST['tieuDe'] ?? '');

        // SỬA: Kiểm tra phân công
        if (!$phanCong || empty($phanCong['maDeThi'])) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Không tìm thấy phân công đề thi'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        $maDeThi = $phanCong['maDeThi']; // Lấy mã đề từ phân công

        if (!$tieuDe) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Vui lòng nhập tiêu đề đề thi'];
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

        // Tạo thư mục nếu chưa tồn tại
        $folder = 'uploads/dethi/';
        if (!file_exists($folder)) mkdir($folder, 0777, true);

        // Tạo tên file mới tránh trùng lặp
        $newFileName = time() . "_" . $fileName;
        if (!move_uploaded_file($fileTmp, $folder . $newFileName)) {
            $_SESSION['message'] = ['status' => 'error', 'text' => 'Upload file thất bại'];
            header('Location: index.php?controller=deThi&action=index');
            exit;
        }

        // KIỂM TRA XEM ĐỀ THI ĐÃ TỒN TẠI CHƯA
        $existingExam = $this->model->getDeThiById($maDeThi);
        
        if ($existingExam) {
            // CẬP NHẬT ĐỀ THI ĐÃ TỒN TẠI
            try {
                $sql = "UPDATE dethi SET 
                        tieuDe = :tieuDe, 
                        noiDung = :noiDung, 
                        ngayNop = :ngayNop,
                        trangThai = 'CHO_DUYET'
                        WHERE maDeThi = :maDeThi";
                
                $stmt = $this->conn->prepare($sql);
                $result = $stmt->execute([
                    'tieuDe' => $tieuDe,
                    'noiDung' => $newFileName,
                    'ngayNop' => date('Y-m-d H:i:s'),
                    'maDeThi' => $maDeThi
                ]);
                
                if ($result) {
                    $_SESSION['message'] = ['status' => 'success', 'text' => 'Cập nhật đề thi thành công'];
                } else {
                    $_SESSION['message'] = ['status' => 'error', 'text' => 'Cập nhật đề thi thất bại'];
                }
                
            } catch (Exception $e) {
                $_SESSION['message'] = ['status' => 'error', 'text' => 'Lỗi: ' . $e->getMessage()];
            }
        } else {
            // TẠO MỚI NẾU CHƯA CÓ
            // Chuẩn bị dữ liệu insert
            $data = [
                'maDeThi'   => $maDeThi, // Sử dụng mã đề từ phân công
                'maGiaoVien' => $giaoVien['maGiaoVien'],
                'maMonHoc'   => $phanCong['maMonHoc'], // Lấy từ phân công
                'maKhoi'     => $phanCong['maKhoi'],    // Lấy từ phân công
                'maNienKhoa' => $phanCong['maNienKhoa'], // Lấy từ phân công
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
        }

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

        // Lấy các tham số lọc
        $maKhoi = $_GET['maKhoi'] ?? null;
        $maNienKhoa = $_GET['maNienKhoa'] ?? null;
        $maMonHoc = $_GET['maMonHoc'] ?? null;
        $maDeThi = $_GET['maDeThi'] ?? null;

        $maMonHocFilter = $maMonHoc ?? $toTruong['maMonHoc'];

        // Lấy danh sách đề thi ĐÃ NỘP
        $exams = $this->model->getDeThi($maMonHocFilter, $maKhoi, $maNienKhoa);
        
        $examDetail = $maDeThi ? $this->model->getDeThiById($maDeThi) : null;
        
        // KIỂM TRA: Nếu đề thi chi tiết chưa nộp, không hiển thị
        if ($examDetail && empty($examDetail['ngayNop'])) {
            $_SESSION['message'] = ['status' => 'danger', 'text' => 'Đề thi chưa được nộp'];
            $examDetail = null;
        }

        // Lấy danh sách Khối
        $khoiHocList = $this->model->getAllKhoiHoc();

        // Lấy danh sách Niên khóa
        $nienKhoaList = $this->model->getAllNienKhoa();

        // Lấy danh sách môn học
        $monHocList = $this->getMonHocList();

        // Truyền các biến ra view
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/totruong.php';
        require_once 'views/dethi/duyetdethi.php';
        require_once 'views/layouts/footer.php';
        exit();
    }

    // THÊM phương thức lấy danh sách môn học
    private function getMonHocList()
    {
        try {
            // Kiểm tra kết nối
            if (!$this->conn) {
                $db = new Database();
                $this->conn = $db->getConnection();
            }
            
            $sql = "SELECT maMonHoc, tenMonHoc FROM monhoc ORDER BY tenMonHoc";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Lỗi lấy danh sách môn học: " . $e->getMessage());
            return [];
        }
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
        $maMonHoc = $_GET['maMonHoc'] ?? null;

        // Kiểm tra chọn Khối, Học kỳ
        if ((isset($_GET['maKhoi']) || isset($_GET['maNienKhoa'])) && (empty($maKhoi) || empty($maNienKhoa))) {
            $_SESSION['message'] = [
                'status' => 'danger',
                'text'   => 'Vui lòng chọn đầy đủ Khối và Học kỳ!'
            ];
        }
        // Lấy danh sách đề thi với bộ lọc môn học
        if ($maKhoi || $maNienKhoa || $maMonHoc) {
            $exams = $this->model->getLichSuDuyetDeThi($maNguoiDung, $maKhoi, $maNienKhoa, $maMonHoc);
        } else {
            // Nếu không có bộ lọc, không lấy dữ liệu (hoặc lấy tất cả)
            $exams = [];
        }

        // Nếu có maDeThi, lấy chi tiết đề thi
        $examDetail = null;
        if ($maDeThi) {
            $examDetail = $this->model->getDeThiById($maDeThi);
        }

        // Lấy danh sách Khối và Niên khóa để filter
        $khoiHocList = $this->model->getAllKhoiHoc();
        $nienKhoaList = $this->model->getAllNienKhoa();

        // SỬA: Lấy danh sách môn học từ Model
        $monHocList = $this->model->getMonHocList();

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/totruong.php';
        require_once 'views/dethi/lichsuduyetde.php';
        exit();
    }

// THÊM: Phương thức lấy danh sách môn học cho tổ trưởng
private function getMonHocListForToTruong($maMonHocTruong = null)
{
    try {
        if (!$this->conn) {
            $db = new Database();
            $this->conn = $db->getConnection();
        }
        
        if ($maMonHocTruong) {
            // Nếu có mã môn của tổ trưởng, chỉ lấy môn đó
            $sql = "SELECT maMonHoc, tenMonHoc FROM monhoc 
                    WHERE maMonHoc = :maMonHoc 
                    ORDER BY tenMonHoc";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['maMonHoc' => $maMonHocTruong]);
        } else {
            // Nếu không, lấy tất cả môn học
            $sql = "SELECT maMonHoc, tenMonHoc FROM monhoc ORDER BY tenMonHoc";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Lỗi lấy danh sách môn học: " . $e->getMessage());
        return [];
    }
}
}
