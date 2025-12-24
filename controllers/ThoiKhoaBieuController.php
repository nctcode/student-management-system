<?php
require_once 'models/ThoiKhoaBieuModel.php';
require_once 'models/HocSinhModel.php';
require_once 'models/GiaoVienModel.php';
require_once 'models/LopHocModel.php';
require_once 'models/PhuHuynhModel.php';

class ThoiKhoaBieuController {
    private $tkbModel;
    private $hocSinhModel;
    private $giaoVienModel;
    private $lopHocModel;
    private $phuHuynhModel;

    public function __construct() {
        $this->tkbModel = new ThoiKhoaBieuModel();
        $this->hocSinhModel = new HocSinhModel();
        $this->giaoVienModel = new GiaoVienModel();
        $this->lopHocModel = new LopHocModel();
        $this->phuHuynhModel = new PhuHuynhModel();
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
        $maTruong = $_SESSION['user']['maTruong'] ?? 1;

        // Xử lý lọc và thiết lập giá trị mặc định
        $danhSachLop = $this->lopHocModel->getLopHocByTruong($maTruong);
        $danhSachKhoi = $this->lopHocModel->getKhoiHocByTruong($maTruong);
        
        // Lấy giá trị từ GET request
        $maKhoi = $_GET['maKhoi'] ?? '';
        $maLop = $_GET['maLop'] ?? '';
        $tuanInput = $_GET['tuan'] ?? date('Y-\WW');

        // Xử lý tuần từ input type="week" (YYYY-WNN) - FIX QUAN TRỌNG
        $tuanDuocChon = date('W'); // Mặc định tuần hiện tại
        $ngayApDungTuan = date('Y-m-d'); // Mặc định ngày hiện tại
        
        if (preg_match('/^(\d{4})-W(\d{2})$/', $tuanInput, $matches)) {
            $year = $matches[1];
            $week = $matches[2];
            $tuanDuocChon = $week;
            
            // Lấy ngày đầu tuần (Thứ 2)
            $ngayApDungTuan = $this->getStartOfWeekDate((int)$week, (int)$year);
            
            // Nếu ngày đầu tuần tính ra trong tương lai (vượt quá ngày hiện tại)
            // thì giữ nguyên, nếu không sẽ điều chỉnh về năm hiện tại
            $currentYear = date('Y');
            $currentWeek = date('W');
            
            if ($year > $currentYear) {
                // Nếu chọn năm lớn hơn năm hiện tại, tính tuần đầu tiên của năm đó
                $ngayApDungTuan = $this->getStartOfWeekDate(1, (int)$year);
            } elseif ($year == $currentYear && $week > $currentWeek) {
                // Nếu cùng năm nhưng tuần lớn hơn tuần hiện tại, giữ nguyên
                // Không cần điều chỉnh
            }
        } else {
            // Nếu không có tuần được chọn, lấy tuần hiện tại
            $currentYear = date('Y');
            $currentWeek = date('W');
            $tuanDuocChon = $currentWeek;
            $ngayApDungTuan = $this->getStartOfWeekDate($currentWeek, $currentYear);
            $tuanInput = $currentYear . '-W' . str_pad($currentWeek, 2, '0', STR_PAD_LEFT);
        }

        // Xử lý lọc lớp theo khối
        $danhSachLopTheoKhoi = $danhSachLop;
        
        if (!empty($maKhoi)) {
            $danhSachLopTheoKhoi = $this->lopHocModel->getLopHocByKhoi($maKhoi, $maTruong);
            
            if (empty($maLop) && !empty($danhSachLopTheoKhoi)) {
                $maLop = $danhSachLopTheoKhoi[0]['maLop'] ?? '';
            }
        }

        // Lấy dữ liệu TKB và thông tin
        $chiTietLop = null;
        $thoiKhoaBieu = [];
        $thongKeMonHoc = [];
        $danhSachMonHoc = [];

        if (!empty($maLop)) {
            $chiTietLop = $this->lopHocModel->getChiTietLop($maLop);
            
            if ($chiTietLop) {
                // Cập nhật maKhoi theo lớp đã chọn
                $maKhoi = $chiTietLop['maKhoi'] ?? $maKhoi;

                // Lấy TKB theo tuần từ bảng buoihoc
                $thoiKhoaBieu = $this->tkbModel->getTKBTheoLopVaTuan($maLop, $ngayApDungTuan);
                
                // Lấy thống kê môn học từ TKB theo tuần
                $thongKeMonHoc = $this->calculateSubjectStatisticsByWeek($maLop, $maKhoi, $ngayApDungTuan);
            }
        }
        
        // Lấy danh sách môn học theo khối
        if (!empty($maKhoi)) {
            $danhSachMonHoc = $this->tkbModel->getMonHocByKhoi($maKhoi);
        }

        // Load view
        $showSidebar = true;
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/admin.php';
        
        // Truyền dữ liệu cần thiết cho view
        require_once 'views/thoikhoabieu/taotkb.php';
        require_once 'views/layouts/footer.php';
    }

    // private function calculateSubjectStatistics($maLop, $maKhoi = null) { 
    //     // Lấy chi tiết lớp để có maKhoi
    //     $chiTietLop = $this->lopHocModel->getChiTietLop($maLop);
    //     if (!$chiTietLop || !$chiTietLop['maKhoi']) {
    //         return [];
    //     }
        
    //     $maKhoi = $chiTietLop['maKhoi'];
        
    //     // Lấy môn học theo khối từ bảng monhoc_khoi
    //     $allMonHoc = $this->tkbModel->getMonHocByKhoi($maKhoi);
        
    //     // Lấy TKB cố định
    //     $thoiKhoaBieu = $this->tkbModel->getTKBTheoLop($maLop);
        
    //     $thongKe = [];
        
    //     foreach ($allMonHoc as $mon) {
    //         $maMonHoc = $mon['maMonHoc'];
    //         $soTietDaXep = 0;
            
    //         // Tính số tiết đã xếp cho môn này trong TKB cố định
    //         foreach ($thoiKhoaBieu as $tkb) {
    //             if ($tkb['maMonHoc'] == $maMonHoc) {
    //                 $soTietDaXep += ($tkb['tietKetThuc'] - $tkb['tietBatDau'] + 1);
    //             }
    //         }
            
    //         $thongKe[$maMonHoc] = [
    //             'tenMonHoc' => $mon['tenMonHoc'],
    //             'soTietQuyDinh' => $mon['soTiet'] ?? 0, 
    //             'soTietDaXep' => $soTietDaXep,
    //             'soTietConLai' => ($mon['soTiet'] ?? 0) - $soTietDaXep
    //         ];
    //     }
        
    //     return $thongKe;
    // }

    private function calculateSubjectStatisticsByWeek($maLop, $maKhoi, $ngayApDungTuan) {
        $chiTietLop = $this->lopHocModel->getChiTietLop($maLop);
        if (!$chiTietLop || !$chiTietLop['maKhoi']) {
            return [];
        }
        
        $maKhoi = $chiTietLop['maKhoi'];
        
        // Lấy môn học theo khối từ bảng monhoc_khoi
        $allMonHoc = $this->tkbModel->getMonHocByKhoi($maKhoi);
        
        // Lấy TKB theo tuần từ buoihoc
        $thoiKhoaBieu = $this->tkbModel->getTKBTheoLopVaTuan($maLop, $ngayApDungTuan);
        
        $thongKe = [];
        
        foreach ($allMonHoc as $mon) {
            $maMonHoc = $mon['maMonHoc'];
            $soTietDaXep = 0;
            
            // Tính số tiết đã xếp cho môn này trong TKB theo tuần
            foreach ($thoiKhoaBieu as $tkb) {
                if ($tkb['maMonHoc'] == $maMonHoc) {
                    $soTietDaXep += ($tkb['tietKetThuc'] - $tkb['tietBatDau'] + 1);
                }
            }
            
            $thongKe[$maMonHoc] = [
                'tenMonHoc' => $mon['tenMonHoc'],
                'soTietQuyDinh' => $mon['soTiet'] ?? 0, 
                'soTietDaXep' => $soTietDaXep,
                'soTietConLai' => ($mon['soTiet'] ?? 0) - $soTietDaXep
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

        $maLop = $_GET['maLop'] ?? '';
        // TRUYỀN NGÀY ÁP DỤNG TUẦN VÀO MODEL
        $thoiKhoaBieu = $this->tkbModel->getAllThoiKhoaBieu(); 
        
        $danhSachLop = $this->lopHocModel->getAllLopHoc(); 
        $showSidebar = true;
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/admin.php';
        require_once 'views/thoikhoabieu/quanlytkb.php';
        require_once 'views/layouts/footer.php';
    }

    public function xemluoi() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? ''; 
        
        $title = "Thời khóa biểu";
        $thoiKhoaBieu = [];
        
        // Lấy thông tin tuần
        $tuanInput = $_GET['tuan'] ?? date('Y-\WW');
        $tuanDuocChon = date('W');
        $ngayApDungTuan = date('Y-m-d');
        
        if (preg_match('/^(\d{4})-W(\d{2})$/', $tuanInput, $matches)) {
            $year = $matches[1];
            $week = $matches[2];
            $tuanDuocChon = $week;
            $ngayApDungTuan = $this->getStartOfWeekDate((int)$week, (int)$year);
        }
        
        $maLop = $_GET['maLop'] ?? ''; 
        $maHocSinh = $_GET['maHocSinh'] ?? '';
        $danhSachLop = $this->lopHocModel->getAllLopHoc();
        
        // Tạo query string không có tuần cho nút "Tuần hiện tại"
        $queryParams = $_GET;
        unset($queryParams['tuan']);
        $queryStringNoTuan = http_build_query($queryParams);
        
        // Khởi tạo danh sách con cho phụ huynh
        $danhSachCon = [];
        
        // Xử lý lọc TKB dựa trên vai trò và tham số
        switch ($userRole) {
            case 'HOCSINH':
                $hocSinh = $this->hocSinhModel->getHocSinhByNguoiDung($maNguoiDung);
                if ($hocSinh && $hocSinh['maLop']) {
                    $maLop = $hocSinh['maLop'];
                    $thoiKhoaBieu = $this->tkbModel->getTKBTheoLopVaTuan($maLop, $ngayApDungTuan);
                }
                break;
                
            case 'PHUHUYNH':
                // Cách 1: Sử dụng Database class trực tiếp
                require_once 'models/Database.php';
                $database = new Database();
                $conn = $database->getConnection();
                
                // Lấy mã phụ huynh từ bảng phuhuynh
                $sql = "SELECT maPhuHuynh FROM phuhuynh WHERE maNguoiDung = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$maNguoiDung]);
                $phuHuynh = $stmt->fetch(PDO::FETCH_ASSOC);
                $maPhuHuynh = $phuHuynh['maPhuHuynh'] ?? null;
                
                if ($maPhuHuynh) {
                    // Lấy danh sách con của phụ huynh
                    $sql = "SELECT 
                                hs.maHocSinh,
                                hs.maLop,
                                nd.hoTen,
                                l.tenLop,
                                k.tenKhoi
                            FROM hocsinh hs
                            JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                            LEFT JOIN lophoc l ON hs.maLop = l.maLop
                            LEFT JOIN khoi k ON l.maKhoi = k.maKhoi
                            WHERE hs.maPhuHuynh = ? 
                            AND hs.trangThai = 'DANG_HOC'
                            ORDER BY nd.hoTen";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$maPhuHuynh]);
                    $danhSachCon = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($danhSachCon)) {
                        $maHocSinhFromGet = $_GET['maHocSinh'] ?? null;
                        
                        if ($maHocSinhFromGet) {
                            // Kiểm tra học sinh được chọn có thuộc phụ huynh không
                            $sql = "SELECT maLop FROM hocsinh 
                                    WHERE maHocSinh = ? AND maPhuHuynh = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute([$maHocSinhFromGet, $maPhuHuynh]);
                            $hocSinh = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($hocSinh && $hocSinh['maLop']) {
                                $maLop = $hocSinh['maLop'];
                                $thoiKhoaBieu = $this->tkbModel->getTKBTheoLopVaTuan($maLop, $ngayApDungTuan);
                            }
                        } else if (count($danhSachCon) === 1) {
                            // Tự động chọn nếu chỉ có 1 con
                            $maLop = $danhSachCon[0]['maLop'] ?? '';
                            $thoiKhoaBieu = $this->tkbModel->getTKBTheoLopVaTuan($maLop, $ngayApDungTuan);
                            $maHocSinh = $danhSachCon[0]['maHocSinh'] ?? '';
                        }
                    }
                }
                break;
                
            case 'GIAOVIEN':
                $giaoVien = $this->giaoVienModel->getGiaoVienByMaNguoiDung($maNguoiDung);
                $maGiaoVien = $giaoVien['maGiaoVien'] ?? null;
                
                if (!empty($maLop)) {
                    // Xem TKB của lớp cụ thể theo tuần
                    $thoiKhoaBieu = $this->tkbModel->getTKBTheoLopVaTuan($maLop, $ngayApDungTuan);
                } elseif ($maGiaoVien) {
                    // Xem lịch dạy cá nhân theo tuần
                    $thoiKhoaBieu = $this->tkbModel->getLichDayByGiaoVienVaTuan($maGiaoVien, $ngayApDungTuan);
                    $maLop = ''; 
                }
                break;
                
            case 'QTV':
            case 'BGH':
                if (!empty($maLop)) {
                    $thoiKhoaBieu = $this->tkbModel->getTKBTheoLopVaTuan($maLop, $ngayApDungTuan);
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
        
        // TRUYỀN BIẾN TRỰC TIẾP KHÔNG DÙNG viewData
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
            
            $chiTietLop = $this->lopHocModel->getChiTietLop($maLop); 
            if (!$chiTietLop) {
                $_SESSION['error'] = "Không tìm thấy thông tin lớp!";
                header('Location: index.php?controller=thoikhoabieu&action=taotkb');
                exit;
            }
            
            $maKhoi = $chiTietLop['maKhoi'];

            // Lấy thông tin tuần từ GET parameters - FIX QUAN TRỌNG
            $tuanInput = $_GET['tuan'] ?? date('Y-\WW');
            $ngayApDungTuan = date('Y-m-d'); // Mặc định
            
            if (preg_match('/^(\d{4})-W(\d{2})$/', $tuanInput, $matches)) {
                $year = $matches[1];
                $week = $matches[2];
                $ngayApDungTuan = $this->getStartOfWeekDate((int)$week, (int)$year);
            }

            if ($actionType === 'save') {
                $maMonHoc = $_POST['maMonHoc'] ?? '';
                $loaiLich = $_POST['loaiLich'] ?? '';
                $tietBatDau = (int)$_POST['tietBatDau'] ?? 1;
                $tietKetThuc = (int)$_POST['tietKetThuc'] ?? 1;
                $phongHoc = $_POST['phongHoc'] ?? '';
                $maGiaoVien = $_POST['maGiaoVien'] ?? null; 
                
                if (empty($maMonHoc) || empty($loaiLich) || empty($maGiaoVien)) { 
                    $_SESSION['error'] = "Vui lòng chọn môn học, thứ và Giáo viên!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop . '&tuan=' . urlencode($tuanInput));
                    exit;
                }
                
                // Chuyển loạiLich (THU_2, THU_3, ...) thành ngày học cụ thể
                $daysMapping = [
                    'THU_2' => 0,
                    'THU_3' => 1,
                    'THU_4' => 2,
                    'THU_5' => 3,
                    'THU_6' => 4,
                    'THU_7' => 5
                ];
                
                if (!isset($daysMapping[$loaiLich])) {
                    $_SESSION['error'] = "Thứ không hợp lệ!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop . '&tuan=' . urlencode($tuanInput));
                    exit;
                }
                
                $daysToAdd = $daysMapping[$loaiLich];
                $ngayHoc = date('Y-m-d', strtotime($ngayApDungTuan . " +{$daysToAdd} days"));
                
                if ($tietBatDau > $tietKetThuc) {
                    $_SESSION['error'] = "Tiết bắt đầu phải nhỏ hơn hoặc bằng tiết kết thúc!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop . '&tuan=' . urlencode($tuanInput));
                    exit;
                }
                
                if ($tietBatDau < 1 || $tietKetThuc > 10) {
                    $_SESSION['error'] = "Tiết học phải từ 1 đến 10!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop . '&tuan=' . urlencode($tuanInput));
                    exit;
                }
                
                // Kiểm tra xem đã có TKB cố định cho tiết này chưa (tùy chọn)
                // Nếu có thể tạo bản sao từ TKB cố định
                
                // Lưu vào bảng buoihoc (theo tuần)
                $data = [
                    'maLop' => $maLop, 
                    'maGiaoVien' => $maGiaoVien, 
                    'maMonHoc' => $maMonHoc,
                    'tietBatDau' => $tietBatDau,
                    'tietKetThuc' => $tietKetThuc,
                    'phongHoc' => $phongHoc,
                    'ngayHoc' => $ngayHoc
                ];

                $result = $this->tkbModel->taoBuoiHoc($data);
                
                if ($result) {
                    $_SESSION['success'] = "Lưu tiết học thành công!";
                } else {
                    $_SESSION['error'] = $_SESSION['error'] ?? "Có lỗi xảy ra khi lưu tiết học!"; 
                }
            } elseif ($actionType === 'delete') {
                $loaiLich = $_POST['loaiLich'] ?? '';
                $tietBatDau = (int)$_POST['tietBatDau'] ?? 0;
                $tietKetThuc = (int)$_POST['tietKetThuc'] ?? 0;
                
                if (empty($loaiLich) || $tietBatDau === 0 || $tietKetThuc === 0) {
                    $_SESSION['error'] = "Vui lòng chọn đầy đủ thông tin để xóa tiết học!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop . '&tuan=' . urlencode($tuanInput));
                    exit;
                }
                
                if ($tietBatDau > $tietKetThuc) {
                    $_SESSION['error'] = "Tiết bắt đầu phải nhỏ hơn hoặc bằng tiết kết thúc!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop . '&tuan=' . urlencode($tuanInput));
                    exit;
                }
                
                // Chuyển loạiLich thành ngày học cụ thể
                $daysMapping = [
                    'THU_2' => 0,
                    'THU_3' => 1,
                    'THU_4' => 2,
                    'THU_5' => 3,
                    'THU_6' => 4,
                    'THU_7' => 5
                ];
                
                if (!isset($daysMapping[$loaiLich])) {
                    $_SESSION['error'] = "Thứ không hợp lệ!";
                    header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop . '&tuan=' . urlencode($tuanInput));
                    exit;
                }
                
                $daysToAdd = $daysMapping[$loaiLich];
                $ngayHoc = date('Y-m-d', strtotime($ngayApDungTuan . " +{$daysToAdd} days"));
                
                // Xóa từ bảng buoihoc (theo tuần)
                $result = $this->tkbModel->xoaBuoiHoc($maLop, $tietBatDau, $tietKetThuc, $ngayHoc);
                
                if ($result) {
                    $_SESSION['success'] = "Xóa tiết học thành công!";
                } else {
                    $_SESSION['error'] = "Không tìm thấy tiết học để xóa hoặc có lỗi xảy ra!";
                }
            }
            
            header('Location: index.php?controller=thoikhoabieu&action=taotkb&maLop=' . $maLop . '&tuan=' . urlencode($tuanInput));
            exit;
        }
    }
    public function xoaBuoiHoc() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if ($userRole !== 'QTV') {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện!']);
            exit;
        }

        $maBuoiHoc = $_GET['maBuoiHoc'] ?? 0;
        
        if (!$maBuoiHoc) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin buổi học!']);
            exit;
        }

        // Gọi model để xóa buổi học
        $result = $this->tkbModel->xoaBuoiHocById($maBuoiHoc);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Xóa tiết học thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa tiết học!']);
        }
        exit;
    }

    public function saoChepTuTKB() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if ($userRole !== 'QTV') {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện!']);
            exit;
        }

        $maLop = $_GET['maLop'] ?? 0;
        $tuanInput = $_GET['tuan'] ?? date('Y-\WW');
        
        if (!$maLop) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin lớp học!']);
            exit;
        }
        
        // Lấy ngày đầu tuần
        $ngayApDungTuan = date('Y-m-d');
        if (preg_match('/^(\d{4})-W(\d{2})$/', $tuanInput, $matches)) {
            $year = $matches[1];
            $week = $matches[2];
            $ngayApDungTuan = $this->getStartOfWeekDate((int)$week, (int)$year);
        }
        
        // Lấy TKB cố định
        $tkbCoDinh = $this->tkbModel->getTKBTheoLop($maLop);
        
        if (empty($tkbCoDinh)) {
            echo json_encode(['success' => false, 'message' => 'Không có TKB cố định để sao chép!']);
            exit;
        }
        
        $count = 0;
        foreach ($tkbCoDinh as $tkb) {
            // Chuyển loaiLich thành ngày học cụ thể
            $daysMapping = [
                'THU_2' => 0,
                'THU_3' => 1,
                'THU_4' => 2,
                'THU_5' => 3,
                'THU_6' => 4,
                'THU_7' => 5
            ];
            
            if (isset($daysMapping[$tkb['loaiLich']])) {
                $daysToAdd = $daysMapping[$tkb['loaiLich']];
                $ngayHoc = date('Y-m-d', strtotime($ngayApDungTuan . " +{$daysToAdd} days"));
                
                // Kiểm tra xem đã có buổi học này chưa
                $existing = $this->tkbModel->kiemTraBuoiHocTonTai(
                    $maLop, 
                    $tkb['tietBatDau'], 
                    $tkb['tietKetThuc'], 
                    $ngayHoc
                );
                
                if (!$existing) {
                    // Tạo buổi học mới từ TKB cố định
                    $data = [
                        'maLop' => $maLop, 
                        'maGiaoVien' => $tkb['maGiaoVien'], 
                        'maMonHoc' => $tkb['maMonHoc'],
                        'tietBatDau' => $tkb['tietBatDau'],
                        'tietKetThuc' => $tkb['tietKetThuc'],
                        'phongHoc' => $tkb['phongHoc'] ?? '',
                        'ngayHoc' => $ngayHoc
                    ];
                    
                    if ($this->tkbModel->taoBuoiHoc($data)) {
                        $count++;
                    }
                }
            }
        }
        
        if ($count > 0) {
            echo json_encode(['success' => true, 'message' => "Đã sao chép {$count} tiết học từ TKB cố định!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không có tiết học nào được sao chép!']);
        }
        exit;
    }
}