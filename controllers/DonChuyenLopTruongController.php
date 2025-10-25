<?php
require_once 'models/DonChuyenLopTruongModel.php';
require_once 'models/HocSinhModel.php';

class DonChuyenLopTruongController {
    private $donChuyenTruongModel;
    private $hocSinhModel;

    public function __construct() {
        $this->donChuyenTruongModel = new DonChuyenLopTruongModel();
        $this->hocSinhModel = new HocSinhModel();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    // Danh sách đơn chuyển trường
    public function index() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $allowedRoles = ['QTV', 'BGH', 'PHUHUYNH'];
        
        if (!in_array($userRole, $allowedRoles)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Quản lý đơn chuyển trường";
        $donChuyenTruong = $this->donChuyenTruongModel->getAllDonChuyenTruong();
        $showSidebar = true;
        require_once 'views/layouts/header.php';
        
        // Sidebar theo role
        if ($userRole === 'QTV' || $userRole === 'BGH') {
            require_once 'views/layouts/sidebar/admin.php';
        } else {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        }
        
        require_once 'views/donchuyenloptruong/danhsachdon.php';
        require_once 'views/layouts/footer.php';
    }

    // Tạo đơn chuyển trường (cho Phụ huynh)
    public function create() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        
        if ($userRole !== 'PHUHUYNH') {
            $_SESSION['error'] = "Chỉ phụ huynh mới có quyền tạo đơn chuyển trường!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Tạo đơn chuyển trường";
        $showSidebar = true;
        // Lấy thông tin học sinh của phụ huynh
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $hocSinh = $this->hocSinhModel->getHocSinhByPhuHuynh($maNguoiDung);

        // Lấy danh sách trường
        $truong = $this->donChuyenTruongModel->getAllTruong();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maHocSinh = $_POST['maHocSinh'] ?? '';
            $maTruongDen = $_POST['maTruongDen'] ?? '';
            $lyDoChuyen = $_POST['lyDoChuyen'] ?? '';

            if (empty($maHocSinh) || empty($maTruongDen) || empty($lyDoChuyen)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin!";
            } else {
                // Lấy thông tin trường hiện tại (mặc định là trường này)
                $maTruongHienTai = 1; // Giả sử mã trường hiện tại là 1

                $result = $this->donChuyenTruongModel->createDonChuyenTruong([
                    'maHocSinh' => $maHocSinh,
                    'maTruongHienTai' => $maTruongHienTai,
                    'maTruongDen' => $maTruongDen,
                    'lyDoChuyen' => $lyDoChuyen
                ]);

                if ($result) {
                    $_SESSION['success'] = "Gửi đơn chuyển trường thành công!";
                    header('Location: index.php?controller=donchuyenloptruong&action=index');
                    exit;
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra khi gửi đơn!";
                }
            }
        }

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/phuhuynh.php';
        require_once 'views/donchuyenloptruong/create.php';
        require_once 'views/layouts/footer.php';
    }

    // Xem chi tiết đơn
    public function detail($maDon) {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $allowedRoles = ['QTV', 'BGH', 'PHUHUYNH'];
        
        if (!in_array($userRole, $allowedRoles)) {
            $_SESSION['error'] = "Bạn không có quyền xem đơn này!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Chi tiết đơn chuyển trường";
        $don = $this->donChuyenTruongModel->getDonChuyenTruongById($maDon);
        $showSidebar = true;
        if (!$don) {
            $_SESSION['error'] = "Không tìm thấy đơn chuyển trường!";
            header('Location: index.php?controller=donchuyenloptruong&action=index');
            exit;
        }

        // Kiểm tra quyền xem đơn
        if ($userRole === 'PHUHUYNH') {
            $maNguoiDung = $_SESSION['user']['maNguoiDung'];
            if (!$this->donChuyenTruongModel->checkPermission($maDon, $maNguoiDung, $userRole)) {
                $_SESSION['error'] = "Bạn không có quyền xem đơn này!";
                header('Location: index.php?controller=home&action=index');
                exit;
            }
        }

        require_once 'views/layouts/header.php';
        
        // Sidebar theo role
        if ($userRole === 'QTV' || $userRole === 'BGH') {
            require_once 'views/layouts/sidebar/admin.php';
        } else {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        }
        
        require_once 'views/donchuyenloptruong/chitietdon.php';
        require_once 'views/layouts/footer.php';
    }

    // Duyệt đơn từ trường đi
    public function pheduyetdon($maDon) {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        
        if (!in_array($userRole, ['QTV', 'BGH'])) {
            $_SESSION['error'] = "Bạn không có quyền duyệt đơn!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Phê duyệt đơn chuyển trường";
        $don = $this->donChuyenTruongModel->getDonChuyenTruongById($maDon);
        $showSidebar = true;
        if (!$don) {
            $_SESSION['error'] = "Không tìm thấy đơn chuyển trường!";
            header('Location: index.php?controller=donchuyenloptruong&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $trangThai = $_POST['trangThai'] ?? '';
            $lyDoTuChoi = $_POST['lyDoTuChoi'] ?? '';

            $result = $this->donChuyenTruongModel->duyetDonTruongDi($maDon, $trangThai, $lyDoTuChoi);

            if ($result) {
                $_SESSION['success'] = "Cập nhật trạng thái đơn thành công!";
                header('Location: index.php?controller=donchuyenloptruong&action=detail&maDon=' . $maDon);
                exit;
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật!";
            }
        }

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/admin.php';
        require_once 'views/donchuyenloptruong/pheduyetdon.php';
        require_once 'views/layouts/footer.php';
    }

    // Hủy đơn
    public function cancel($maDon) {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        $result = $this->donChuyenTruongModel->cancelDonChuyenTruong($maDon, $maNguoiDung, $userRole);
        $showSidebar = true;
        if ($result) {
            $_SESSION['success'] = "Hủy đơn thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi hủy đơn hoặc bạn không có quyền!";
        }

        header('Location: index.php?controller=donchuyenloptruong&action=index');
        exit;
    }
}
?>