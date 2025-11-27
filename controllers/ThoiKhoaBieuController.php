<?php
require_once 'models/ThoiKhoaBieuModel.php';
require_once 'models/HocSinhModel.php';
require_once 'models/GiaoVienModel.php';
require_once 'models/LopHocModel.php';

class ThoiKhoaBieuController {
    private $tkbModel;
    private $hocSinhModel;
    private $giaoVienModel;
    private $lopHocModel;

    public function __construct() {
        $this->tkbModel = new ThoiKhoaBieuModel();
        $this->hocSinhModel = new HocSinhModel();
        $this->giaoVienModel = new GiaoVienModel();
        $this->lopHocModel = new LopHocModel();
    }
    
    // Hàm Helper: Chuyển đổi số tuần thành ngày đầu tuần (Thứ Hai)
    private function getStartOfWeekDate($weekNumber, $year = null) {
        if ($year === null) {
            $year = date('Y');
        }
        
        $dateTime = new DateTime();
        // setISODate(Năm, Số tuần, Ngày trong tuần - 1=Thứ 2)
        $dateTime->setISODate($year, (int)$weekNumber, 1); 
        return $dateTime->format('Y-m-d'); 
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

    public function taotkb() {
    $this->checkAuth();
    
    $userRole = $_SESSION['user']['vaiTro'] ?? '';
    if ($userRole !== 'QTV') {
        $_SESSION['error'] = "Bạn không có quyền truy cập!";
        header('Location: index.php?controller=home&action=index');
        exit;
    }

    $title = "Tạo thời khóa biểu";

    // 1. XỬ LÝ LỌC VÀ THIẾT LẬP GIÁ TRỊ MẶC ĐỊNH
    $danhSachLop = $this->lopHocModel->getAllLopHoc();
    $danhSachKhoi = $this->lopHocModel->getKhoiHoc(); 
    $danhSachGiaoVien = $this->giaoVienModel->getAllGiaoVien();
    
    $maKhoi = $_GET['maKhoi'] ?? '';
    $maLop = $_GET['maLop'] ?? '';

    // A. Thiết lập Lớp Mặc Định nếu chưa có lớp nào được chọn
    if (empty($maLop) && !empty($danhSachLop)) {
        // Lấy mã lớp của lớp đầu tiên làm mặc định để TKB không trống
        $maLop = $danhSachLop[0]['maLop'] ?? '';
    }

    // B. Xử lý Lọc theo Khối
    $danhSachLopTheoKhoi = [];
    if (!empty($maKhoi)) {
        $danhSachLopTheoKhoi = $this->lopHocModel->getLopHocByKhoi($maKhoi);
        // Nếu đã lọc theo khối, gán lớp đầu tiên của khối đó làm mặc định
        if (empty($maLop) && !empty($danhSachLopTheoKhoi)) {
             $maLop = $danhSachLopTheoKhoi[0]['maLop'] ?? '';
        }
    }
    
    // C. Xử lý Tuần Mặc Định và Ngày Áp Dụng
    // Lấy số tuần (WNN) từ URL hoặc tuần hiện tại
    $tuanDuocChon = $_GET['tuan'] ?? date('W'); 
    $namHienTai = date('Y');
    
    // Chuyển số tuần thành ngày đại diện đầu tuần (YYYY-MM-DD)
    $ngayApDungTuan = $this->getStartOfWeekDate((int)$tuanDuocChon, (int)$namHienTai);
    
    // 2. LẤY DỮ LIỆU TKB
    $chiTietLop = null;
    $thoiKhoaBieu = [];
    $thongKeMonHoc = [];
    $danhSachMonHoc = [];
    $monHoc = []; // Danh sách môn học theo khối

    if (!empty($maLop)) {
        $chiTietLop = $this->lopHocModel->getChiTietLop($maLop);
        
        if ($chiTietLop) {
            // Cập nhật maKhoi theo lớp đã chọn/mặc định (để lọc môn học)
            $maKhoi = $chiTietLop['maKhoi'] ?? $maKhoi; 

            // Lấy TKB, cần $maLop và $ngayApDungTuan
            $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $ngayApDungTuan);
            $thongKeMonHoc = $this->calculateSubjectStatistics($maLop, $ngayApDungTuan); 
        }
        
        $danhSachMonHoc = $this->tkbModel->getAllMonHoc();
    }
    
    if (!empty($maKhoi)) {
        $monHoc = $this->tkbModel->getMonHocByKhoi($maKhoi);
    }
    
    // 3. LOAD VIEW
    $showSidebar = true;
    require_once 'views/layouts/header.php';
    require_once 'views/layouts/sidebar/admin.php';
    
    // Cần truyền $ngayApDungTuan và $tuanDuocChon vào view
    require_once 'views/thoikhoabieu/taotkb.php';
    require_once 'views/layouts/footer.php';
}
  




// SỬA: calculateSubjectStatistics - Lọc theo Tuần
    private function calculateSubjectStatistics($maLop, $ngayApDungTuan) { 
        $allMonHoc = $this->tkbModel->getAllMonHoc(); 
        
        // LẤY TKB CHỈ CỦA TUẦN ĐƯỢC CHỌN
        $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $ngayApDungTuan); 
        
        $thongKe = [];
        
        foreach ($allMonHoc as $mon) {
            $maMonHoc = $mon['maMonHoc'];
            $soTietDaXep = 0;
            
            // Tính số tiết đã xếp cho môn này
            foreach ($thoiKhoaBieu as $tkb) {
                if ($tkb['maMonHoc'] == $maMonHoc) {
                    $soTietDaXep += ($tkb['tietKetThuc'] - $tkb['tietBatDau'] + 1);
                }
            }
            
            $thongKe[$maMonHoc] = [
                'tenMonHoc' => $mon['tenMonHoc'],
                'soTietQuyDinh' => $mon['soTiet'] ?? 0, 
                'soTietDaXep' => $soTietDaXep,
                'soTietConLai' => max(0, ($mon['soTiet'] ?? 0) - $soTietDaXep)
            ];
        }
        
        return $thongKe;
    }

    // Quản lý TKB (QTV)
    public function quanlytkb() {
        $this->checkAuth();
        // ... (Giữ nguyên logic xử lý tuần đã có) ...
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if ($userRole !== 'QTV') {
            $_SESSION['error'] = "Bạn không có quyền truy cập!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Quản lý thời khóa biểu";
        
        // Hàm helper để chuẩn hóa giá trị tuần từ form (YYYY-WNN) thành ngày đầu tuần (YYYY-MM-DD)
        $tuanInput = $_GET['tuan'] ?? date('Y-\WW');
        $ngayApDungTuan = null;

        if (preg_match('/^(\d{4})-W(\d{2})$/', $tuanInput, $matches)) {
            $year = $matches[1];
            $week = $matches[2];
            
            $ngayApDungTuan = $this->getStartOfWeekDate($week, $year);
        } else {
            $ngayApDungTuan = date('Y-m-d');
        }

        $maLop = $_GET['maLop'] ?? '';
        // TRUYỀN NGÀY ÁP DỤNG TUẦN VÀO MODEL
        $thoiKhoaBieu = $this->tkbModel->getAllThoiKhoaBieu($ngayApDungTuan); 
        
        $danhSachLop = $this->lopHocModel->getAllLopHoc(); 
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
        
        // Lấy tuần hiện tại hoặc tuần được chọn (chỉ là số WN)
        $tuanDuocChon = $_GET['tuan'] ?? date('W'); 
        $namHienTai = date('Y');
        // TÍNH TOÁN NGÀY ĐẠI DIỆN ĐẦU TUẦN ĐƯỢC CHỌN (YYYY-MM-DD)
        $ngayApDungTuan = $this->getStartOfWeekDate((int)$tuanDuocChon, (int)$namHienTai);
        
        $maLop = $_GET['maLop'] ?? ''; 
        $danhSachLop = $this->lopHocModel->getAllLopHoc();
        
        // --- XỬ LÝ LỌC TKB DỰA TRÊN VAI TRÒ VÀ THAM SỐ ---
        switch ($userRole) {
            case 'HOCSINH':
                $hocSinh = $this->hocSinhModel->getHocSinhByNguoiDung($maNguoiDung);
                if ($hocSinh && $hocSinh['maLop']) {
                    $maLop = $hocSinh['maLop'];
                    $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $ngayApDungTuan);
                }
                break;
                
            case 'PHUHUYNH':
                $danhSachCon = $this->hocSinhModel->getHocSinhByPhuHuynh($maNguoiDung);
                
                // XỬ LÝ PHỤ HUYNH CÓ NHIỀU CON
                if (!empty($danhSachCon)) {
                    $maHocSinhFromGet = $_GET['maHocSinh'] ?? null;
                    
                    if ($maHocSinhFromGet) {
                        // Tìm học sinh được chọn trong danh sách con
                        foreach ($danhSachCon as $con) {
                            if ($con['maHocSinh'] == $maHocSinhFromGet && $con['maLop']) {
                                $maLop = $con['maLop'];
                                $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $ngayApDungTuan);
                                break;
                            }
                        }
                    } else if (count($danhSachCon) === 1) {
                        // Nếu chỉ có 1 con, lấy lớp của con đầu tiên
                        $maLop = $danhSachCon[0]['maLop'] ?? '';
                        $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $ngayApDungTuan);
                    }
                    // Nếu có nhiều con và chưa chọn con nào, $maLop sẽ rỗng -> hiển thị form chọn
                }
                break;
                
            case 'GIAOVIEN':
                $giaoVien = $this->giaoVienModel->getGiaoVienByMaNguoiDung($maNguoiDung);
                $maGiaoVien = $giaoVien['maGiaoVien'] ?? null;
                
                if (!empty($maLop)) {
                    $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $ngayApDungTuan);
                } elseif ($maGiaoVien) {
                    $thoiKhoaBieu = $this->tkbModel->getLichDayByGiaoVien($maGiaoVien, $ngayApDungTuan);
                    $maLop = ''; 
                }
                break;
                
            case 'QTV':
            case 'BGH':
                if (!empty($maLop)) {
                    $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop, $ngayApDungTuan);
                }
                break;
                
            default:
                $_SESSION['error'] = "Bạn không có quyền xem thời khóa biểu!";
                header('Location: index.php?controller=home&action=index');
                exit;
        }

        $chiTietLop = null;
        if (!empty($maLop)) {
            $chiTietLop = $this->lopHocModel->getChiTietLop($maLop);
        }

        $showSidebar = true;
        require_once 'views/layouts/header.php';
        // ... (Load sidebar theo role) ...
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

    // Lấy giáo viên theo môn học (AJAX) - Giữ nguyên
    public function getGiaoVienByMon() {
        $maMonHoc = $_GET['maMonHoc'] ?? '';
        
        if (empty($maMonHoc)) {
            echo json_encode([]);
            exit;
        }

        // Hàm getGiaoVienByMonHoc() NÊN nằm trong TKBModel hoặc GiaoVienModel (Giả định nằm TKBModel)
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
            
            // Lấy ngày áp dụng (đã được chuẩn hóa là ngày đầu tuần)
            $ngayApDungTuan = $_POST['ngayApDungTuan'] ?? date('Y-m-d'); 
            
            if (empty($maLop)) {
                $_SESSION['error'] = "Vui lòng chọn lớp học!";
                header('Location: index.php?controller=thoikhoabieu&action=taotkb');
                exit;
            }
            
            $chiTietLop = $this->lopHocModel->getChiTietLop($maLop); 
            if (!$chiTietLop) {
                $_SESSION['error'] = "Không tìm thấy thông tin lớp!";
                header('Location: index.php?controller=thoikhoabieu&action=taotkb');
                exit;
            }
            
            $maKhoi = $chiTietLop['maKhoi'];

            if ($actionType === 'save') {
                $maMonHoc = $_POST['maMonHoc'] ?? '';
                $loaiLich = $_POST['loaiLich'] ?? '';
                $tietBatDau = (int)$_POST['tietBatDau'] ?? 1;
                $tietKetThuc = (int)$_POST['tietKetThuc'] ?? 1;
                $phongHoc = $_POST['phongHoc'] ?? '';
                $maGiaoVien = $_POST['maGiaoVien'] ?? null; 
                
                if (empty($maMonHoc) || empty($loaiLich) || empty($maGiaoVien)) { 
                    $_SESSION['error'] = "Vui lòng chọn môn học, thứ và Giáo viên!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop);
                    exit;
                }
                
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
                    'ngayApDung' => $ngayApDungTuan, // <-- Ngày đầu tuần
                    'maLop' => $maLop, 
                    'maGiaoVien' => $maGiaoVien, 
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
                    // Lỗi đã được set trong Model (trùng lịch)
                    $_SESSION['error'] = $_SESSION['error'] ?? "Có lỗi xảy ra khi lưu tiết học!"; 
                }
            } elseif ($actionType === 'delete') {
                $loaiLich = $_POST['loaiLich'] ?? '';
                $tietBatDau = (int)$_POST['tietBatDau'] ?? 0;
                $tietKetThuc = (int)$_POST['tietKetThuc'] ?? 0;
                
                if (empty($loaiLich) || $tietBatDau === 0 || $tietKetThuc === 0) {
                    $_SESSION['error'] = "Vui lòng chọn đầy đủ thông tin để xóa tiết học!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop);
                    exit;
                }
                
                if ($tietBatDau > $tietKetThuc) {
                    $_SESSION['error'] = "Tiết bắt đầu phải nhỏ hơn hoặc bằng tiết kết thúc!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop);
                    exit;
                }
                
                // Xóa tiết học
                $result = $this->tkbModel->xoaTietHoc($maLop, $loaiLich, $tietBatDau, $tietKetThuc, $ngayApDungTuan); // <-- Truyền ngày áp dụng
                
                if ($result) {
                    $_SESSION['success'] = "Xóa tiết học thành công!";
                } else {
                    $_SESSION['error'] = "Không tìm thấy tiết học để xóa hoặc có lỗi xảy ra!";
                }
            }
            
            // Lấy lại số tuần để truyền lại lên URL
            $tuanDeRedirect = date('W', strtotime($ngayApDungTuan));
            
            header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop . '&tuan=' . $tuanDeRedirect);
            exit;
        }
    }
    
    // ... (Các hàm khác giữ nguyên)
}