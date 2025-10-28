<?php
require_once 'models/TinNhanModel.php';
require_once 'models/HocSinhModel.php';
require_once 'models/PhuHuynhModel.php';

class TinNhanController {
    private $tinNhanModel;
    private $hocSinhModel;
    private $phuHuynhModel;

    public function __construct() {
        $this->tinNhanModel = new TinNhanModel();
        $this->hocSinhModel = new HocSinhModel();
        $this->phuHuynhModel = new PhuHuynhModel();
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }
    
    public function index() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $allowedRoles = ['QTV', 'BGH', 'PHUHUYNH', 'HOCSINH', 'GIAOVIEN'];
        
        if (!in_array($userRole, $allowedRoles)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Tin Nhắn - QLHS";
        $showSidebar = true;
        
        // Lấy danh sách tin nhắn
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $tinNhan = $this->tinNhanModel->getTinNhanByNguoiDung($maNguoiDung);
        
        require_once 'views/layouts/header.php';
        
        // Sửa lỗi: sửa 'role' thành 'vaiTro'
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if ($userRole === 'GIAOVIEN') {
            require_once 'views/layouts/sidebar/giaovien.php';
        } elseif ($userRole === 'PHUHUYNH') {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        } elseif ($userRole === 'BGH') {
            require_once 'views/layouts/sidebar/bangiamhieu.php';
        } elseif ($userRole === 'QTV') {
            require_once 'views/layouts/sidebar/admin.php';
        } else {  
            require_once 'views/layouts/sidebar/hocsinh.php';
        }
        
        require_once 'views/tinnhan/danhsachtinnhan.php';
        require_once 'views/layouts/footer.php';
    }

    public function guitinnhan() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $allowedRoles = ['QTV', 'BGH', 'GIAOVIEN'];
        
        if (!in_array($userRole, $allowedRoles)) {
            $_SESSION['error'] = "Bạn không có quyền gửi tin nhắn!";
            header('Location: index.php?controller=tinnhan&action=index');
            exit;
        }

        $title = "Gửi Tin Nhắn - QLHS";
        $showSidebar = true;

        // Lấy danh sách học sinh và lớp học
        $danhSachLop = $this->hocSinhModel->getDanhSachLop();
        $danhSachHocSinh = [];
        $danhSachPhuHuynh = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->xuLyGuiTinNhan();
        }

        require_once 'views/layouts/header.php';
        
        if ($userRole === 'GIAOVIEN') {
            require_once 'views/layouts/sidebar/giaovien.php';
        } elseif ($userRole === 'BGH') {
            require_once 'views/layouts/sidebar/bangiamhieu.php';
        } else {
            require_once 'views/layouts/sidebar/admin.php';
        }
        
        require_once 'views/tinnhan/guitinnhan.php';
        require_once 'views/layouts/footer.php';
    }

    private function xuLyGuiTinNhan() {
        $maNguoiGui = $_SESSION['user']['maNguoiDung'];
        $tieuDe = $_POST['tieuDe'] ?? '';
        $noiDung = $_POST['noiDung'] ?? '';
        $loaiNguoiNhan = $_POST['loaiNguoiNhan'] ?? '';
        $danhSachNguoiNhan = $_POST['nguoiNhan'] ?? [];

        // Kiểm tra nếu danhSachNguoiNhan là string (từ hidden input) thì chuyển thành array
        if (is_string($danhSachNguoiNhan) && !empty($danhSachNguoiNhan)) {
            $danhSachNguoiNhan = explode(',', $danhSachNguoiNhan);
        }

        if (empty($tieuDe) || empty($noiDung) || empty($danhSachNguoiNhan)) {
            $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin tin nhắn và chọn người nhận!";
            return;
        }

        if (strlen($noiDung) > 1000) {
            $_SESSION['error'] = "Tin nhắn không được vượt quá 1000 ký tự!";
            return;
        }

        // Xử lý upload file
        $fileDinhKem = $this->xuLyUploadFile();

        // Tạo cuộc hội thoại và gửi tin nhắn
        $result = $this->tinNhanModel->taoTinNhan(
            $maNguoiGui,
            $danhSachNguoiNhan,
            $tieuDe,
            $noiDung,
            $fileDinhKem,
            $loaiNguoiNhan
        );

        if ($result) {
            $_SESSION['success'] = "Gửi tin nhắn thành công!";
            header('Location: index.php?controller=tinnhan&action=index');
            exit;
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi gửi tin nhắn!";
        }
    }

    private function xuLyUploadFile() {
        if (!isset($_FILES['fileDinhKem']) || $_FILES['fileDinhKem']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES['fileDinhKem'];
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx', 'xls'];
        $maxSize = 10 * 1024 * 1024; // 10MB

        // Kiểm tra kích thước file
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = "File đính kèm không được vượt quá 10MB!";
            return null;
        }

        // Kiểm tra loại file
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedTypes)) {
            $_SESSION['error'] = "Định dạng file không được hỗ trợ! Chỉ chấp nhận PDF, DOC, JPG, PNG, XLSX.";
            return null;
        }

        // Tạo thư mục upload nếu chưa tồn tại
        $uploadDir = 'uploads/tinnhan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Tạo tên file mới
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'tenFile' => $file['name'],
                'duongDan' => $filePath,
                'kichThuoc' => $file['size']
            ];
        }

        return null;
    }

    // Trong phương thức chitiettinnhan, thêm kiểm tra quyền truy cập:

    public function chitiettinnhan($maHoiThoai) {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        // Kiểm tra quyền truy cập
        if (!$this->tinNhanModel->kiemTraQuyenTruyCap($maHoiThoai, $maNguoiDung)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập hội thoại này!";
            header('Location: index.php?controller=tinnhan&action=index');
            exit;
        }

        $title = "Chi Tiết Tin Nhắn - QLHS";
        $showSidebar = true;

        // Lấy chi tiết hội thoại
        $chiTietHoiThoai = $this->tinNhanModel->getChiTietHoiThoai($maHoiThoai, $maNguoiDung);
        $tinNhan = $this->tinNhanModel->getTinNhanByHoiThoai($maHoiThoai);

        if (!$chiTietHoiThoai) {
            $_SESSION['error'] = "Không tìm thấy hội thoại!";
            header('Location: index.php?controller=tinnhan&action=index');
            exit;
        }

        // Đánh dấu đã đọc
        $this->tinNhanModel->danhDauDaDoc($maHoiThoai, $maNguoiDung);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->xuLyGuiTinNhanTrongHoiThoai($maHoiThoai);
        }

        require_once 'views/layouts/header.php';
        
        if ($userRole === 'GIAOVIEN') {
            require_once 'views/layouts/sidebar/giaovien.php';
        } elseif ($userRole === 'PHUHUYNH') {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        } elseif ($userRole === 'BGH') {
            require_once 'views/layouts/sidebar/bangiamhieu.php';
        } elseif ($userRole === 'QTV') {
            require_once 'views/layouts/sidebar/admin.php';
        } else {  
            require_once 'views/layouts/sidebar/hocsinh.php';
        }
        
        require_once 'views/tinnhan/chitiettinnhan.php';
        require_once 'views/layouts/footer.php';
    }

    private function xuLyGuiTinNhanTrongHoiThoai($maHoiThoai) {
        $maNguoiGui = $_SESSION['user']['maNguoiDung'];
        $noiDung = $_POST['noiDung'] ?? '';

        if (empty($noiDung)) {
            $_SESSION['error'] = "Vui lòng nhập nội dung tin nhắn!";
            return;
        }

        // Xử lý upload file (nếu có)
        $fileDinhKem = $this->xuLyUploadFile();

        $result = $this->tinNhanModel->guiTinNhanTrongHoiThoai(
            $maHoiThoai,
            $maNguoiGui,
            $noiDung,
            $fileDinhKem
        );

        if ($result) {
            $_SESSION['success'] = "Gửi tin nhắn thành công!";
            header("Location: index.php?controller=tinnhan&action=chitiettinnhan&maHoiThoai=$maHoiThoai");
            exit;
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi gửi tin nhắn!";
        }
    }

    // AJAX: Lấy danh sách học sinh theo lớp
    public function getHocSinhByLop() {
        $maLop = $_GET['maLop'] ?? '';
        
        if (empty($maLop)) {
            echo json_encode([]);
            exit;
        }

        $hocSinh = $this->hocSinhModel->getHocSinhByLop($maLop);
        echo json_encode($hocSinh);
        exit;
    }

    // AJAX: Lấy danh sách phụ huynh theo lớp
    public function getPhuHuynhByLop() {
        $maLop = $_GET['maLop'] ?? '';
        
        if (empty($maLop)) {
            echo json_encode([]);
            exit;
        }

        $phuHuynh = $this->phuHuynhModel->getPhuHuynhByLop($maLop);
        echo json_encode($phuHuynh);
        exit;
    }
}
?>