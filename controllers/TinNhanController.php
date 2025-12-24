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
        $filter = $_GET['filter'] ?? 'all'; 
        $tinNhan = $this->tinNhanModel->getTinNhanByNguoiDung($maNguoiDung, $filter);
        
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

        $maTruong = $_SESSION['user']['maTruong'] ?? null;

        if ($userRole === 'GIAOVIEN' && $maTruong) $danhSachLop = $this->hocSinhModel->getDanhSachLopByTruong($maTruong);
        else $danhSachLop = $this->hocSinhModel->getDanhSachLop();
        
        $danhSachHocSinh = [];
        $danhSachPhuHuynh = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->xuLyGuiTinNhan();

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
        $loaiNguoiNhan = $_POST['loaiNguoiNhan'] ?? '';
        $noiDungChuaLoc = trim($_POST['noiDung'] ?? '');
        $danhSachNguoiNhan = $_POST['nguoiNhan'] ?? '';

        $_SESSION['old_tinnhan'] = [
            'tieuDe' => $tieuDe,
            'noiDung' => $noiDungChuaLoc,
            'nguoiNhan' => $danhSachNguoiNhan
        ];

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $noiDung = $purifier->purify($noiDungChuaLoc);

        if (is_string($danhSachNguoiNhan) && !empty($danhSachNguoiNhan)) {
            $danhSachNguoiNhan = explode(',', $danhSachNguoiNhan);
        }

        if(empty($danhSachNguoiNhan)){
            $_SESSION['error'] = "Vui lòng chọn người nhận!";
            header("Location: index.php?controller=tinnhan&action=guitinnhan");
            exit;
        } elseif (empty($tieuDe)) {
            $_SESSION['error'] = "Vui lòng nhập tiêu đề!";
            header("Location: index.php?controller=tinnhan&action=guitinnhan");
            exit;
        } elseif( empty(trim(strip_tags($noiDung)))) {
            $_SESSION['error'] = "Vui lòng nhập nội dung tin nhắn!";
            header("Location: index.php?controller=tinnhan&action=guitinnhan");
            exit;
        }

        if (strlen($noiDung) > 1000) {
            $_SESSION['error'] = "Tin nhắn không được vượt quá 1000 ký tự!";
            header("Location: index.php?controller=tinnhan&action=guitinnhan");
            exit;
        }

        // Xử lý upload file
        $fileDinhKem = $this->xuLyUploadFile();
        
        if ($fileDinhKem === false) { 
            header("Location: index.php?controller=tinnhan&action=guitinnhan"); 
            exit;
        }
        
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
            unset($_SESSION['old_tinnhan']);
            $_SESSION['success'] = "Gửi tin nhắn thành công!";
            header('Location: index.php?controller=tinnhan&action=index');
            exit;
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi gửi tin nhắn!";
        }
    }

    private function xuLyUploadFile() {
        if (!isset($_FILES['fileDinhKem']) || empty($_FILES['fileDinhKem']['name'][0])) {
            return [];
        }

        $files = $_FILES['fileDinhKem'];
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx', 'xls'];
        $maxSize = 10 * 1024 * 1024;
        $uploadDir = 'uploads/tinnhan/';
        $uploadedFilesInfo = [];

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                continue; 
            }

            $fileSize = $files['size'][$key];
            $fileTmpName = $files['tmp_name'][$key];
            
            if ($fileSize > $maxSize) {
                $_SESSION['error'] = "File '" . htmlspecialchars($name) . "' vượt quá 10MB!";
                return false; 
            }

            $fileExtension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedTypes)) {
                $_SESSION['error'] = "File '" . htmlspecialchars($name) . "' có định dạng không hỗ trợ!";
                return false;
            }

            $fileName = uniqid() . '_' . time() . '_' . $key . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpName, $filePath)) {
                $uploadedFilesInfo[] = [
                    'tenFile' => $name,
                    'duongDan' => $filePath,
                    'kichThuoc' => $fileSize
                ];
            } else {
                $_SESSION['error'] = "Có lỗi khi lưu file '" . htmlspecialchars($name) . "'.";
                return false;
            }
        }
        return $uploadedFilesInfo;
    }

    public function chitiettinnhan($maHoiThoai) {
        $this->checkAuth();
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        if (!$this->tinNhanModel->kiemTraQuyenTruyCap($maHoiThoai, $maNguoiDung)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập hội thoại này!";
            header('Location: index.php?controller=tinnhan&action=index');
            exit;
        }

        $title = "Chi Tiết Tin Nhắn - QLHS";
        $showSidebar = true;

        $chiTietHoiThoai = $this->tinNhanModel->getChiTietHoiThoai($maHoiThoai, $maNguoiDung);
        $tinNhan = $this->tinNhanModel->getTinNhanByHoiThoai($maHoiThoai);
        $danhSachThanhVien = $this->tinNhanModel->getThanhVienHoiThoai($maHoiThoai);

        if (!$chiTietHoiThoai) {
            $_SESSION['error'] = "Không tìm thấy hội thoại!";
            header('Location: index.php?controller=tinnhan&action=index');
            exit;
        }

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
        $noiDungChuaLoc = $_POST['noiDung'] ?? '';
        $_SESSION['old_reply_'.$maHoiThoai] = $noiDungChuaLoc;

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $noiDung = $purifier->purify(trim($noiDungChuaLoc));

        if (empty(trim(strip_tags($noiDung)))) {
            $_SESSION['error'] = "Vui lòng nhập nội dung tin nhắn!";
            header("Location: index.php?controller=tinnhan&action=chitiettinnhan&maHoiThoai=$maHoiThoai");
            exit;
        }

        if (strlen($noiDung) > 1000) {
            $_SESSION['error'] = "Tin nhắn không được vượt quá 1000 ký tự!";
            header("Location: index.php?controller=tinnhan&action=chitiettinnhan&maHoiThoai=$maHoiThoai");
            exit;
        }

        // Xử lý upload file (nếu có)
        $fileDinhKem = $this->xuLyUploadFile();

        if (empty($fileDinhKem)) $fileDinhKem = null;
        
        if ($fileDinhKem === false) {
            header("Location: index.php?controller=tinnhan&action=chitiettinnhan&maHoiThoai=$maHoiThoai");
            exit;
        }

        $result = $this->tinNhanModel->guiTinNhanTrongHoiThoai(
            $maHoiThoai,
            $maNguoiGui,
            $noiDung,
            $fileDinhKem
        );

        if ($result) {
            unset($_SESSION['old_reply_'.$maHoiThoai]);
            $_SESSION['success'] = "Gửi tin nhắn thành công!";
            header("Location: index.php?controller=tinnhan&action=chitiettinnhan&maHoiThoai=$maHoiThoai");
            exit;
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi gửi tin nhắn!";
            header("Location: index.php?controller=tinnhan&action=chitiettinnhan&maHoiThoai=$maHoiThoai");
            exit;
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

    public function guitinnhangiaovien() {
        $this->checkAuth();

        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $allowedRoles = ['PHUHUYNH', 'HOCSINH'];
        
        if (!in_array($userRole, $allowedRoles)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
            header('Location: index.php?controller=tinnhan&action=index');
            exit;
        }

        $title = "Gửi Tin Nhắn Cho Giáo Viên - QLHS";
        $showSidebar = true;

        require_once 'models/GiaoVienModel.php';
        $giaoVienModel = new GiaoVienModel();
        $danhSachGiaoVien = $giaoVienModel->getAllGiaoVien();

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $thongTinNguoiGui = $this->layThongTinNguoiGui($userRole, $maNguoiDung);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->xuLyGuiTinNhanGiaoVien($thongTinNguoiGui);
        }

        require_once 'views/layouts/header.php';
        
        if ($userRole === 'PHUHUYNH') {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        } else {
            require_once 'views/layouts/sidebar/hocsinh.php';
        }
        
        require_once 'views/tinnhan/guitinnhangiaovien.php';
        require_once 'views/layouts/footer.php';
    }

    private function layThongTinNguoiGui($vaiTro, $maNguoiDung) {
        $thongTin = [];
        
        if ($vaiTro === 'PHUHUYNH') {
            $phuHuynh = $this->phuHuynhModel->getPhuHuynhByNguoiDung($maNguoiDung);
            $hocSinh = $this->phuHuynhModel->getHocSinhCuaPhuHuynh($phuHuynh['maPhuHuynh']);
            $thongTin = [
                'maNguoiDung' => $maNguoiDung,
                'hoTen' => $phuHuynh['hoTen'],
                'vaiTro' => 'PHUHUYNH',
                'hocSinh' => $hocSinh
            ];
        } elseif ($vaiTro === 'HOCSINH') {
            $hocSinh = $this->hocSinhModel->getHocSinhByNguoiDung($maNguoiDung);
            $thongTin = [
                'maNguoiDung' => $maNguoiDung,
                'hoTen' => $hocSinh['hoTen'],
                'vaiTro' => 'HOCSINH',
                'lop' => $hocSinh['tenLop']
            ];
        }
        
        return $thongTin;
    }

    private function xuLyGuiTinNhanGiaoVien($thongTinNguoiGui) {
        $maNguoiGui = $thongTinNguoiGui['maNguoiDung'];
        $tieuDe = $_POST['tieuDe'] ?? '';
        $noiDung = $_POST['noiDung'] ?? '';
        $danhSachGiaoVienNhan = $_POST['giaoVienNhan'] ?? [];

        if (is_string($danhSachGiaoVienNhan) && !empty($danhSachGiaoVienNhan)) {
            $danhSachGiaoVienNhan = explode(',', $danhSachGiaoVienNhan);
        }

        if (empty($tieuDe) || empty($noiDung) || empty($danhSachGiaoVienNhan)) {
            $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin tin nhắn và chọn giáo viên nhận!";
            return;
        }

        if (strlen($noiDung) > 1000) {
            $_SESSION['error'] = "Tin nhắn không được vượt quá 1000 ký tự!";
            return;
        }

        $danhSachNguoiNhan = $this->chuyenMaGiaoVienSangMaNguoiDung($danhSachGiaoVienNhan);
        
        if (empty($danhSachNguoiNhan)) {
            $_SESSION['error'] = "Không tìm thấy thông tin giáo viên!";
            return;
        }

        $noiDungHoanChinh = $noiDung;

        // Xử lý upload file
        $fileDinhKem = $this->xuLyUploadFile();
        
        // Tạo cuộc hội thoại và gửi tin nhắn
        $result = $this->tinNhanModel->taoTinNhan(
            $maNguoiGui,
            $danhSachNguoiNhan,
            $tieuDe,
            $noiDungHoanChinh,
            $fileDinhKem,
            'GIAOVIEN'
        );

        if ($result) {
            $_SESSION['success'] = "Gửi tin nhắn thành công!";
            header('Location: index.php?controller=tinnhan&action=index');
            exit;
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi gửi tin nhắn!";
        }
    }

    private function chuyenMaGiaoVienSangMaNguoiDung($danhSachMaGiaoVien) {
        require_once 'models/GiaoVienModel.php';
        $giaoVienModel = new GiaoVienModel();
        $danhSachMaNguoiDung = [];
        
        foreach ($danhSachMaGiaoVien as $maGiaoVien) {
            $giaoVien = $giaoVienModel->getGiaoVienById($maGiaoVien);
            if ($giaoVien && isset($giaoVien['maNguoiDung'])) {
                $danhSachMaNguoiDung[] = $giaoVien['maNguoiDung'];
            }
        }
        
        return $danhSachMaNguoiDung;
    }

    // AJAX: Lấy tất cả giáo viên
    public function getAllGiaoVien() {
        require_once 'models/GiaoVienModel.php';
        $giaoVienModel = new GiaoVienModel();
        $danhSachGiaoVien = $giaoVienModel->getAllGiaoVien();
        $formattedData = [];

        foreach ($danhSachGiaoVien as $gv) {
            $formattedData[] = [
                'maGiaoVien' => $gv['maGiaoVien'],
                'hoTen' => $gv['hoTen'],
                'toChuyenMon' => $gv['toChuyenMon'] ?? 'Giáo viên'
            ];
        }
        
        echo json_encode($formattedData);
        exit;
    }

    // AJAX: Lấy giáo viên theo lớp (cho phụ huynh)
    public function getGiaoVienByLop() {
        $maLop = $_GET['maLop'] ?? '';
        
        if (empty($maLop)) {
            echo json_encode([]);
            exit;
        }

        require_once 'models/GiaoVienModel.php';

        $giaoVienModel = new GiaoVienModel();
        $gvcn = $giaoVienModel->getGiaoVienChuNhiemByLop($maLop);
        $gvbm = $giaoVienModel->getGiaoVienBoMonByLop($maLop);
        $danhSachGiaoVien = array_merge($gvcn, $gvbm);
        
        echo json_encode($danhSachGiaoVien);
        exit;
    }
}
?>