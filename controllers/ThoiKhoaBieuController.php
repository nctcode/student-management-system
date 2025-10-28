<?php
require_once 'models/ThoiKhoaBieuModel.php';
require_once 'models/HocSinhModel.php';
require_once 'models/GiaoVienModel.php';

class ThoiKhoaBieuController {
    private $tkbModel;
    private $hocSinhModel;
    private $giaoVienModel;

    public function __construct() {
        $this->tkbModel = new ThoiKhoaBieuModel();
        $this->hocSinhModel = new HocSinhModel();
        $this->giaoVienModel = new GiaoVienModel();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    private function convertDayToVietnamese($loaiLich) {
        $days = [
            'THU_2' => 'Thứ 2',
            'THU_3' => 'Thứ 3', 
            'THU_4' => 'Thứ 4',
            'THU_5' => 'Thứ 5',
            'THU_6' => 'Thứ 6',
            'THU_7' => 'Thứ 7'
        ];
        return $days[$loaiLich] ?? $loaiLich;
    }

    public function getConvertDayFunction() {
        return function($loaiLich) {
            return $this->convertDayToVietnamese($loaiLich);
        };
    }

    // Tạo TKB (QTV)
    public function taotkb() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if ($userRole !== 'QTV') {
            $_SESSION['error'] = "Bạn không có quyền truy cập!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Tạo thời khóa biểu";

        // Lấy tham số từ URL
        $maKhoi = $_GET['maKhoi'] ?? '';
        $maLop = $_GET['maLop'] ?? '';

        // Lấy dữ liệu cho form
        $danhSachKhoi = $this->tkbModel->getKhoiHoc();
        $danhSachLop = $this->tkbModel->getLopHoc();

        // Lọc danh sách lớp theo khối
        $danhSachLopTheoKhoi = [];
        if (!empty($maKhoi)) {
            $danhSachLopTheoKhoi = $this->tkbModel->getLopHocByKhoi($maKhoi);
        }

        // Lấy môn học theo khối
        $monHoc = [];
        if (!empty($maKhoi)) {
            $monHoc = $this->tkbModel->getMonHocByKhoi($maKhoi);
        }

        // Lấy chi tiết lớp và TKB nếu có mã lớp
        $chiTietLop = null;
        $thoiKhoaBieu = [];
        if (!empty($maLop)) {
            $chiTietLop = $this->tkbModel->getChiTietLop($maLop);
            $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop);
        }

        $showSidebar = true;
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/admin.php';
        require_once 'views/thoikhoabieu/taotkb.php';
        require_once 'views/layouts/footer.php';
    }

    // Quản lý TKB (QTV) - CHỈ CÓ 1 PHƯƠNG THỨC NÀY
    public function quanlytkb() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if ($userRole !== 'QTV') {
            $_SESSION['error'] = "Bạn không có quyền truy cập!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Quản lý thời khóa biểu";
        
        // Lấy danh sách TKB
        $tuan = $_GET['tuan'] ?? date('Y-m-d');
        $thoiKhoaBieu = $this->tkbModel->getAllThoiKhoaBieu($tuan);
        
        // Lấy danh sách lớp để hiển thị
        $danhSachLop = $this->tkbModel->getLopHoc();

        $showSidebar = true;
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/admin.php';
        require_once 'views/thoikhoabieu/quanlytkb.php';
        require_once 'views/layouts/footer.php';
    }

    // Xem TKB dạng lưới (cho tất cả người dùng)
    public function xemluoi() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? '';
        
        $title = "Thời khóa biểu";
        $thoiKhoaBieu = [];
        $tuan = $_GET['tuan'] ?? date('Y-m-d');
        $maLop = $_GET['maLop'] ?? '';

        // Lấy danh sách lớp học để chọn
        $danhSachLop = $this->tkbModel->getLopHoc();

        switch ($userRole) {
            case 'HOCSINH':
                // Lấy thông tin học sinh
                $hocSinh = $this->hocSinhModel->getHocSinhByNguoiDung($maNguoiDung);
                if ($hocSinh && $hocSinh['maLop']) {
                    $maLop = $hocSinh['maLop'];
                    $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $tuan);
                }
                break;
                
            case 'PHUHUYNH':
                // Phụ huynh xem TKB của con
                $hocSinh = $this->hocSinhModel->getHocSinhByPhuHuynh($maNguoiDung);
                if ($hocSinh && $hocSinh['maLop']) {
                    $maLop = $hocSinh['maLop'];
                    $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $tuan);
                }
                break;
                
            case 'GIAOVIEN':
                // Giáo viên có thể chọn lớp để xem
                if (!empty($maLop)) {
                    $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $tuan);
                }
                break;
                
            case 'QTV':
            case 'BGH':
                // QTV và BGH có thể chọn lớp để xem
                if (!empty($maLop)) {
                    $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $tuan);
                }
                break;
                
            default:
                $_SESSION['error'] = "Bạn không có quyền xem thời khóa biểu!";
                header('Location: index.php?controller=home&action=index');
                exit;
        }

        // Lấy thông tin chi tiết lớp nếu có mã lớp
        $chiTietLop = null;
        if (!empty($maLop)) {
            $chiTietLop = $this->tkbModel->getChiTietLop($maLop);
        }

        $showSidebar = true;
        require_once 'views/layouts/header.php';
        
        // Load sidebar theo role
        switch ($userRole) {
            case 'HOCSINH':
                require_once 'views/layouts/sidebar/hocsinh.php';
                break;
            case 'PHUHUYNH':
                require_once 'views/layouts/sidebar/phuhuynh.php';
                break;
            case 'GIAOVIEN':
                require_once 'views/layouts/sidebar/giaovien.php';
                break;
            case 'QTV':
                require_once 'views/layouts/sidebar/admin.php';
                break;
            case 'BGH':
                require_once 'views/layouts/sidebar/bangiamhieu.php';
                break;
        }
        
        require_once 'views/thoikhoabieu/xemluoi.php';
        require_once 'views/layouts/footer.php';
    }

    // Thêm phương thức mới để lấy giáo viên theo môn học (AJAX)
    public function getGiaoVienByMon() {
        $maMonHoc = $_GET['maMonHoc'] ?? '';
        
        if (empty($maMonHoc)) {
            echo json_encode([]);
            exit;
        }

        $giaoVien = $this->tkbModel->getGiaoVienByMonHoc($maMonHoc);
        echo json_encode($giaoVien);
        exit;
    }

    // Thêm phương thức lưu tiết học
    public function luutiet() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if ($userRole !== 'QTV') {
            $_SESSION['error'] = "Bạn không có quyền thực hiện!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actionType = $_POST['actionType'] ?? '';
            $maLop = $_POST['maLop'] ?? '';
            
            if (empty($maLop)) {
                $_SESSION['error'] = "Vui lòng chọn lớp học!";
                header('Location: index.php?controller=thoikhoabieu&action=taotkb');
                exit;
            }
            
            // Lấy thông tin khối từ lớp
            $chiTietLop = $this->tkbModel->getChiTietLop($maLop);
            if (!$chiTietLop) {
                $_SESSION['error'] = "Không tìm thấy thông tin lớp!";
                header('Location: index.php?controller=thoikhoabieu&action=taotkb');
                exit;
            }
            
            $maKhoi = $chiTietLop['maKhoi'];

            if ($actionType === 'save') {
                // Kiểm tra dữ liệu
                $maMonHoc = $_POST['maMonHoc'] ?? '';
                $loaiLich = $_POST['loaiLich'] ?? '';
                $tietBatDau = (int)$_POST['tietBatDau'] ?? 1;
                $tietKetThuc = (int)$_POST['tietKetThuc'] ?? 1;
                $phongHoc = $_POST['phongHoc'] ?? '';
                
                if (empty($maMonHoc) || empty($loaiLich)) {
                    $_SESSION['error'] = "Vui lòng chọn môn học và thứ!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop);
                    exit;
                }
                
                // Kiểm tra tiết hợp lệ
                if ($tietBatDau > $tietKetThuc) {
                    $_SESSION['error'] = "Tiết bắt đầu phải nhỏ hơn hoặc bằng tiết kết thúc!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop);
                    exit;
                }
                
                if ($tietBatDau < 1 || $tietKetThuc > 10) {
                    $_SESSION['error'] = "Tiết học phải từ 1 đến 10!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop);
                    exit;
                }
                
                // Lưu tiết học mới
                $data = [
                    'ngayApDung' => date('Y-m-d'),
                    'maMonHoc' => $maMonHoc,
                    'tietBatDau' => $tietBatDau,
                    'tietKetThuc' => $tietKetThuc,
                    'phongHoc' => $phongHoc,
                    'loaiLich' => $loaiLich,
                    'maKhoi' => $maKhoi
                ];

                $result = $this->tkbModel->taoThoiKhoaBieu($data);
                
                if ($result) {
                    $_SESSION['success'] = "Lưu tiết học thành công!";
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra khi lưu tiết học!";
                }
            } elseif ($actionType === 'delete') {
                // Xóa tiết học
                $loaiLich = $_POST['loaiLich'] ?? '';
                $tietBatDau = (int)$_POST['tietBatDau'] ?? 0;
                $tietKetThuc = (int)$_POST['tietKetThuc'] ?? 0;
                
                if (empty($loaiLich) || $tietBatDau === 0 || $tietKetThuc === 0) {
                    $_SESSION['error'] = "Vui lòng chọn đầy đủ thông tin để xóa tiết học!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop);
                    exit;
                }
                
                // Kiểm tra tiết hợp lệ
                if ($tietBatDau > $tietKetThuc) {
                    $_SESSION['error'] = "Tiết bắt đầu phải nhỏ hơn hoặc bằng tiết kết thúc!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop);
                    exit;
                }
                
                // Xóa tiết học
                $result = $this->tkbModel->xoaTietHoc($maKhoi, $loaiLich, $tietBatDau, $tietKetThuc);
                
                if ($result) {
                    $_SESSION['success'] = "Xóa tiết học thành công!";
                } else {
                    $_SESSION['error'] = "Không tìm thấy tiết học để xóa hoặc có lỗi xảy ra!";
                }
            }
            
            header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop);
            exit;
        }
    }

    // Xóa TKB (QTV)
    public function xoatkb() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if ($userRole !== 'QTV') {
            $_SESSION['error'] = "Bạn không có quyền thực hiện!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $maThoiKhoaBieu = $_GET['maThoiKhoaBieu'] ?? '';
        
        if (empty($maThoiKhoaBieu)) {
            $_SESSION['error'] = "Không tìm thấy thời khóa biểu!";
            header('Location: index.php?controller=thoikhoabieu&action=quanlytkb');
            exit;
        }

        $result = $this->tkbModel->xoaThoiKhoaBieu($maThoiKhoaBieu);

        if ($result) {
            $_SESSION['success'] = "Xóa thời khóa biểu thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi xóa thời khóa biểu!";
        }

        header('Location: index.php?controller=thoikhoabieu&action=quanlytkb');
        exit;
    }
}
?>