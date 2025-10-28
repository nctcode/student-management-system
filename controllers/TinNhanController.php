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
        // Kiểm tra xem có file nào được tải lên không
        if (!isset($_FILES['fileDinhKem']) || empty($_FILES['fileDinhKem']['name'][0])) {
            return null; // Không có file nào
        }

        $files = $_FILES['fileDinhKem'];
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx', 'xls'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        $uploadDir = 'uploads/tinnhan/';
        
        $uploadedFilesInfo = []; // Mảng để lưu thông tin các file đã upload thành công

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Lặp qua từng file được tải lên
        // (files['name'] là một mảng)
        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                continue; 
            }

            $fileSize = $files['size'][$key];
            $fileTmpName = $files['tmp_name'][$key];
            
            // Kiểm tra kích thước file
            if ($fileSize > $maxSize) {
                $_SESSION['error'] = "File '" . htmlspecialchars($name) . "' vượt quá 10MB!";
                // Khi 1 file lỗi, dừng toàn bộ việc upload
                return null; 
            }

            // Kiểm tra loại file
            $fileExtension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedTypes)) {
                $_SESSION['error'] = "File '" . htmlspecialchars($name) . "' có định dạng không hỗ trợ!";
                return null;
            }

            // Tạo tên file mới
            $fileName = uniqid() . '_' . time() . '_' . $key . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpName, $filePath)) {
                // Nếu thành công, thêm thông tin file vào mảng
                $uploadedFilesInfo[] = [
                    'tenFile' => $name,
                    'duongDan' => $filePath,
                    'kichThuoc' => $fileSize
                ];
            } else {
                // Nếu di chuyển file thất bại
                $_SESSION['error'] = "Có lỗi khi lưu file '" . htmlspecialchars($name) . "'.";
                return null;
            }
        }

        // Trả về mảng chứa thông tin của TẤT CẢ các file đã upload
        return $uploadedFilesInfo;
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

        $danhSachThanhVien = $this->tinNhanModel->getThanhVienHoiThoai($maHoiThoai);

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