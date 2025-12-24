<?php
require_once 'models/TuyenSinhModel.php';
require_once 'models/BanHocModel.php';
require_once 'models/HocSinhModel.php';
require_once 'models/Database.php';

class TuyenSinhController {
    private $tuyenSinhModel;
    private $banHocModel;

    public function __construct() {
        $this->tuyenSinhModel = new TuyenSinhModel();
        $this->banHocModel = new BanHocModel();
        
    }

    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }
    // Thêm hàm xử lý upload file
    private function handleFileUpload() {
        $uploadDir = 'uploads/hosotuyensinh/';
        $result = ['success' => true, 'files' => [], 'error' => ''];
        
        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileFields = [
            'banSaoGiayKhaiSinh',
            'banSaoHoKhau', 
            'hocBaTHCS',
            'giayChungNhanTotNghiep',
            'anh34',
            'giayXacNhanUuTien'
        ];
        
        foreach ($fileFields as $field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$field];
                
                // Kiểm tra kích thước file
                if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                    $result['success'] = false;
                    $result['error'] = "File $field vượt quá kích thước cho phép (5MB)";
                    break;
                }
                
                // Kiểm tra loại file
                $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
                $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if (!in_array($fileExt, $allowedTypes)) {
                    $result['success'] = false;
                    $result['error'] = "File $field không đúng định dạng (chỉ chấp nhận PDF, JPG, PNG)";
                    break;
                }
                
                // Tạo tên file mới
                $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
                $uploadPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $result['files'][$field] = $uploadPath;
                } else {
                    $result['success'] = false;
                    $result['error'] = "Lỗi khi upload file $field";
                    break;
                }
            }
        }
        
        return $result;
    }

    // Trang đăng ký hồ sơ (cho phụ huynh/học sinh)
public function dangkyhoso() {
    $title = "Đăng ký tuyển sinh";
    
    // Lấy danh sách ban học
    $banHoc = $this->banHocModel->getAllBanHoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy thông tin user
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? '';
        
        // Xử lý dữ liệu form
        $data = [
            'hoTen' => $_POST['hoTen'] ?? '',
            'gioiTinh' => $_POST['gioiTinh'] ?? 'NAM',
            'ngaySinh' => $_POST['ngaySinh'] ?? '',
            'noiSinh' => $_POST['noiSinh'] ?? '',
            'danToc' => $_POST['danToc'] ?? '',
            'tonGiao' => $_POST['tonGiao'] ?? '',
            'quocTich' => $_POST['quocTich'] ?? 'Việt Nam',
            'diaChiThuongTru' => $_POST['diaChiThuongTru'] ?? '',
            'noiOHienNay' => $_POST['noiOHienNay'] ?? '',
            'soDienThoaiHocSinh' => $_POST['soDienThoaiHocSinh'] ?? '',
            'soDienThoaiPhuHuynh' => $_POST['soDienThoaiPhuHuynh'] ?? '',
            'email' => $_POST['email'] ?? '',
            'hoTenCha' => $_POST['hoTenCha'] ?? '',
            'namSinhCha' => $_POST['namSinhCha'] ?? null,
            'ngheNghiepCha' => $_POST['ngheNghiepCha'] ?? '',
            'dienThoaiCha' => $_POST['dienThoaiCha'] ?? '',
            'noiCongTacCha' => $_POST['noiCongTacCha'] ?? '',
            'hoTenMe' => $_POST['hoTenMe'] ?? '',
            'namSinhMe' => $_POST['namSinhMe'] ?? null,
            'ngheNghiepMe' => $_POST['ngheNghiepMe'] ?? '',
            'dienThoaiMe' => $_POST['dienThoaiMe'] ?? '',
            'noiCongTacMe' => $_POST['noiCongTacMe'] ?? '',
            'hoTenNguoiGiamHo' => $_POST['hoTenNguoiGiamHo'] ?? '',
            'namSinhNguoiGiamHo' => $_POST['namSinhNguoiGiamHo'] ?? null,
            'ngheNghiepNguoiGiamHo' => $_POST['ngheNghiepNguoiGiamHo'] ?? '',
            'dienThoaiNguoiGiamHo' => $_POST['dienThoaiNguoiGiamHo'] ?? '',
            'noiCongTacNguoiGiamHo' => $_POST['noiCongTacNguoiGiamHo'] ?? '',
            'truongTHCS' => $_POST['truongTHCS'] ?? '',
            'diaChiTruongTHCS' => $_POST['diaChiTruongTHCS'] ?? '',
            'namTotNghiep' => $_POST['namTotNghiep'] ?? null,
            'xepLoaiHocLuc' => $_POST['xepLoaiHocLuc'] ?? null,
            'xepLoaiHanhKiem' => $_POST['xepLoaiHanhKiem'] ?? null,
            'diemTB_Lop9' => $_POST['diemTB_Lop9'] ?? null,
            'nguyenVong1' => $_POST['nguyenVong1'] ?? '',
            'nguyenVong2' => $_POST['nguyenVong2'] ?? '',
            'nguyenVong3' => $_POST['nguyenVong3'] ?? '',
            'nganhHoc' => $_POST['nganhHoc'] ?? '',
            'hinhThucTuyenSinh' => $_POST['hinhThucTuyenSinh'] ?? 'XET_TUYEN',
            'maBan' => $_POST['maBan'] ?? null
        ];

       // ===== VALIDATE DỮ LIỆ =====
if (empty($data['hoTen']) || empty($data['ngaySinh']) || empty($data['diaChiThuongTru']) || 
    empty($data['soDienThoaiHocSinh']) || empty($data['soDienThoaiPhuHuynh'])) {

    $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin bắt buộc!";

}
// Validate họ tên
elseif (!preg_match('/^[A-Za-zÀ-ỹ\s]+$/u', $data['hoTen'])) {

    $_SESSION['error'] = "Họ tên không được chứa số hoặc ký tự đặc biệt!";

}
// ===== VALIDATE TẤT CẢ SỐ ĐIỆN THOẠI =====
else {
    $phoneFields = [
        'soDienThoaiHocSinh',
        'soDienThoaiPhuHuynh',
        'dienThoaiCha',
        'dienThoaiMe',
        'dienThoaiNguoiGiamHo'
    ];

    foreach ($phoneFields as $field) {
        if (!empty($data[$field]) && !preg_match('/^0[0-9]{9}$/', $data[$field])) {
            $_SESSION['error'] = "Số điện thoại không hợp lệ! (phải gồm 10 số, bắt đầu bằng 0)";
            break;
        }
    }
}

if (!empty($_SESSION['error'])) {
    // Có lỗi → không xử lý tiếp
} else {
            // Xử lý upload file
            $uploadResult = $this->handleFileUpload();
            if ($uploadResult['success']) {
                $data = array_merge($data, $uploadResult['files']);
                
                // Đăng ký hồ sơ
                $result = $this->tuyenSinhModel->dangKyHoSo($data);

                if ($result) {
                    // LẤY KẾT NỐI DATABASE
                    $conn = $this->tuyenSinhModel->getConnection();
                    
                    // NẾU LÀ HỌC SINH - Tạo/cập nhật bản ghi học sinh
                    if ($userRole === 'HOCSINH') {
                        require_once 'models/HocSinhModel.php';
                        $hocSinhModel = new HocSinhModel();
                        
                        // Kiểm tra xem học sinh này đã có bản ghi chưa
                        $existingHocSinh = $hocSinhModel->getHocSinhByNguoiDung($maNguoiDung);
                        
                        if (!$existingHocSinh) {
                            // QUAN TRỌNG: Tạo bản ghi học sinh mới với maHoSo
                            $sqlHocSinh = "INSERT INTO hocsinh (maNguoiDung, maHoSo, trangThai, ngayNhapHoc) 
                                           VALUES (?, ?, 'DANG_HOC', CURDATE())";
                            $stmtHocSinh = $conn->prepare($sqlHocSinh);
                            $stmtHocSinh->execute([$maNguoiDung, $result]);
                            
                            error_log("✓ Created hocsinh: maNguoiDung={$maNguoiDung}, maHoSo={$result}");
                        } else {
                            // QUAN TRỌNG: Cập nhật maHoSo cho học sinh đã tồn tại
                            $sqlUpdate = "UPDATE hocsinh SET maHoSo = ? WHERE maNguoiDung = ?";
                            $stmtUpdate = $conn->prepare($sqlUpdate);
                            $stmtUpdate->execute([$result, $maNguoiDung]);
                            
                            error_log("✓ Updated hocsinh: maNguoiDung={$maNguoiDung}, maHoSo={$result}");
                        }
                        
                        // Verify: Kiểm tra lại xem đã cập nhật thành công chưa
                        $verifyHocSinh = $hocSinhModel->getHocSinhByNguoiDung($maNguoiDung);
                        if ($verifyHocSinh && $verifyHocSinh['maHoSo'] == $result) {
                            error_log("✓✓ VERIFIED: Học sinh có maHoSo = {$result}");
                        } else {
                            error_log("✗✗ ERROR: Không verify được maHoSo!");
                        }
                    }
                    
                    // NẾU LÀ PHỤ HUYNH - Tạo học sinh mới
                    elseif ($userRole === 'PHUHUYNH') {
                        $maPhuHuynh = $this->tuyenSinhModel->getMaPhuHuynhByMaNguoiDung($maNguoiDung);
                        
                        if ($maPhuHuynh) {
                            try {
                                // Bắt đầu transaction
                                $conn->beginTransaction();
                                
                                // Tạo người dùng mới cho học sinh
                                $sqlNguoiDung = "INSERT INTO nguoidung (hoTen, ngaySinh, gioiTinh, soDienThoai, email, diaChi, loaiNguoiDung) 
                                                 VALUES (?, ?, ?, ?, ?, ?, 'HOCSINH')";
                                $stmtNguoiDung = $conn->prepare($sqlNguoiDung);
                                $stmtNguoiDung->execute([
                                    $data['hoTen'],
                                    $data['ngaySinh'],
                                    $data['gioiTinh'],
                                    $data['soDienThoaiHocSinh'],
                                    $data['email'],
                                    $data['diaChiThuongTru']
                                ]);
                                
                                $maNguoiDungMoi = $conn->lastInsertId();
                                
                                // Tạo học sinh liên kết với phụ huynh và hồ sơ
                                $sqlHocSinh = "INSERT INTO hocsinh (maNguoiDung, maPhuHuynh, maHoSo, trangThai, ngayNhapHoc) 
                                               VALUES (?, ?, ?, 'DANG_HOC', CURDATE())";
                                $stmtHocSinh = $conn->prepare($sqlHocSinh);
                                $stmtHocSinh->execute([$maNguoiDungMoi, $maPhuHuynh, $result]);
                                
                                // Commit transaction
                                $conn->commit();
                                
                                error_log("✓ Created student for parent: maPhuHuynh={$maPhuHuynh}, maHoSo={$result}");
                            } catch (Exception $e) {
                                // Rollback nếu có lỗi
                                $conn->rollBack();
                                error_log("✗ Error creating student: " . $e->getMessage());
                                $_SESSION['error'] = "Có lỗi khi tạo học sinh: " . $e->getMessage();
                            }
                        }
                    }
                    
                    $_SESSION['success'] = "Đăng ký hồ sơ thành công! Mã hồ sơ: #" . str_pad($result, 4, '0', STR_PAD_LEFT);
                    
                    // Chuyển hướng đến trang danh sách hồ sơ
                    header('Location: index.php?controller=tuyensinh&action=hosocuatoi');
                    exit;
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra khi đăng ký hồ sơ!";
                }
            } else {
                $_SESSION['error'] = $uploadResult['error'];
            }
        }
    }
    
    $showSidebar = true;
    require_once 'views/layouts/header.php';
    require_once 'views/tuyensinh/dangkyhoso.php';
    $userRole = $_SESSION['user']['vaiTro'] ?? '';
    if ($userRole === 'PHUHUYNH') {
        require_once 'views/layouts/sidebar/phuhuynh.php';
    } else {
        require_once 'views/layouts/sidebar/hocsinh.php';
    }
    require_once 'views/layouts/footer.php';
}

    public function danhsachhoso() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if (!in_array($userRole, ['QTV', 'BGH'])) {
            $_SESSION['error'] = "Bạn không có quyền truy cập!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Danh sách hồ sơ tuyển sinh";
        $hoSo = $this->tuyenSinhModel->getAllHoSo();
        $showSidebar = true;
        require_once 'views/layouts/header.php';
        if ($userRole === 'QTV') {
            require_once 'views/layouts/sidebar/admin.php';
        } else{
            require_once 'views/layouts/sidebar/bangiamhieu.php';
        }
        require_once 'views/tuyensinh/danhsachhoso.php';
        require_once 'views/layouts/footer.php';
    }

    public function hosocuatoi() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? '';
        
        $title = "Hồ sơ của tôi";
        $hoSo = [];
        
        // Nếu là học sinh - lấy hồ sơ theo maNguoiDung
        if ($userRole === 'HOCSINH') {
            $hoSo = $this->tuyenSinhModel->getHoSoByMaNguoiDung($maNguoiDung);
        } 
        // Nếu là phụ huynh - lấy hồ sơ của con theo maPhuHuynh
        elseif ($userRole === 'PHUHUYNH') {
            $maPhuHuynh = $this->tuyenSinhModel->getMaPhuHuynhByMaNguoiDung($maNguoiDung);
            if ($maPhuHuynh) {
                $hoSo = $this->tuyenSinhModel->getHoSoByMaPhuHuynh($maPhuHuynh);
            }
        }
        
        $showSidebar = true;
        require_once 'views/layouts/header.php';
        
        if ($userRole === 'PHUHUYNH') {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        } else if ($userRole === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } else {
            require_once 'views/layouts/sidebar/admin.php';
        }
        
        require_once 'views/tuyensinh/hosocuatoi.php';
        require_once 'views/layouts/footer.php';
    }

    // Xem chi tiết hồ sơ (cho học sinh/phụ huynh) - theo maHoSo
    // Xem chi tiết hồ sơ (cho học sinh/phụ huynh) - theo maHoSo
    // Xem chi tiết hồ sơ (cho học sinh/phụ huynh) - theo maHoSo
    public function xemhoso($maHoSo) {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? '';
        
        $title = "Chi tiết hồ sơ";

        // Lấy thông tin hồ sơ
        $hoSo = $this->tuyenSinhModel->getHoSoById($maHoSo);
        
        if (!$hoSo) {
            $_SESSION['error'] = "Không tìm thấy hồ sơ!";
            header('Location: index.php?controller=tuyensinh&action=hosocuatoi');
            exit;
        }
        
        // Kiểm tra quyền xem hồ sơ
        $allowed = false;
        
        // Admin và BGH được xem tất cả
        if (in_array($userRole, ['QTV', 'BGH'])) {
            $allowed = true;
        }
        // Học sinh chỉ được xem hồ sơ của chính mình
        elseif ($userRole === 'HOCSINH') {
            $hocSinhModel = new HocSinhModel();
            $hocSinh = $hocSinhModel->getHocSinhByNguoiDung($maNguoiDung);
            
            // Debug
            error_log("Checking permission for HOCSINH");
            error_log("maNguoiDung: " . $maNguoiDung);
            error_log("HocSinh info: " . print_r($hocSinh, true));
            error_log("maHoSo requested: " . $maHoSo);
            
            if ($hocSinh && $hocSinh['maHoSo'] == $maHoSo) {
                $allowed = true;
                error_log("Permission GRANTED for HOCSINH");
            } else {
                error_log("Permission DENIED for HOCSINH - maHoSo mismatch");
            }
        }
        // Phụ huynh được xem hồ sơ của con
        elseif ($userRole === 'PHUHUYNH') {
            $maPhuHuynh = $this->tuyenSinhModel->getMaPhuHuynhByMaNguoiDung($maNguoiDung);
            
            // Debug
            error_log("Checking permission for PHUHUYNH");
            error_log("maPhuHuynh: " . $maPhuHuynh);
            
            if ($maPhuHuynh) {
                // Kiểm tra xem hồ sơ này có thuộc về con của phụ huynh không
                $conn = $this->tuyenSinhModel->getConnection();
                $sql = "SELECT COUNT(*) as count FROM hocsinh 
                        WHERE maHoSo = ? AND maPhuHuynh = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$maHoSo, $maPhuHuynh]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result['count'] > 0) {
                    $allowed = true;
                    error_log("Permission GRANTED for PHUHUYNH");
                } else {
                    error_log("Permission DENIED for PHUHUYNH - not their child's hoso");
                }
            }
        }
        
        if (!$allowed) {
            error_log("Final permission check: DENIED");
            $_SESSION['error'] = "Bạn không có quyền xem hồ sơ này!";
            header('Location: index.php?controller=tuyensinh&action=hosocuatoi');
            exit;
        }
        
        error_log("Final permission check: GRANTED - Displaying hoso");
        
        $showSidebar = true;
        require_once 'views/layouts/header.php';
        
        // Sidebar theo role
        if ($userRole === 'PHUHUYNH') {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        } else if ($userRole === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } else {
            require_once 'views/layouts/sidebar/admin.php';
        }
        
        require_once 'views/tuyensinh/xemhoso.php';
        require_once 'views/layouts/footer.php';
    }

    // Thêm action xem hồ sơ theo mã học sinh
    public function xemhoso_theohocsinh($maHocSinh) {
    $this->checkAuth();
    
    $userRole = $_SESSION['user']['vaiTro'] ?? '';
    $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? '';
    
    $title = "Hồ sơ tuyển sinh";

    // Debug
    error_log("xemhoso_theohocsinh called with maHocSinh: " . $maHocSinh);
    error_log("User role: " . $userRole);
    error_log("User maNguoiDung: " . $maNguoiDung);

    // Lấy thông tin học sinh
    $hocSinhModel = new HocSinhModel();
    $hocSinh = $hocSinhModel->getHocSinhById($maHocSinh);
    
    if (!$hocSinh) {
        error_log("Không tìm thấy học sinh với maHocSinh: " . $maHocSinh);
        $_SESSION['error'] = "Không tìm thấy học sinh!";
        header('Location: index.php?controller=home&action=index');
        exit;
    }
    
    // Lấy thông tin hồ sơ tuyển sinh theo mã học sinh
    $hoSo = $this->tuyenSinhModel->getHoSoByMaHocSinh($maHocSinh);
    
    if (!$hoSo) {
        error_log("Không tìm thấy hồ sơ tuyển sinh cho maHocSinh: " . $maHocSinh);
        $_SESSION['error'] = "Không tìm thấy hồ sơ tuyển sinh cho học sinh này!";
        header('Location: index.php?controller=home&action=index');
        exit;
    }
    
    // Kiểm tra quyền xem hồ sơ
    $allowed = in_array($userRole, ['QTV', 'BGH', 'GIAOVIEN']);
    
    if (!$allowed) {
        // Học sinh chỉ được xem hồ sơ của chính mình
        if ($userRole === 'HOCSINH') {
            $hocSinhCuaToi = $hocSinhModel->getHocSinhByNguoiDung($maNguoiDung);
            error_log("Học sinh của tôi: " . print_r($hocSinhCuaToi, true));
            if ($hocSinhCuaToi && $hocSinhCuaToi['maHocSinh'] == $maHocSinh) {
                $allowed = true;
            }
        }
        // Phụ huynh có thể xem hồ sơ của con
        elseif ($userRole === 'PHUHUYNH') {
            // Tạm thời cho phép tất cả phụ huynh
            $allowed = true;
        }
    }
    
    if (!$allowed) {
        error_log("User không có quyền xem hồ sơ này");
        $_SESSION['error'] = "Bạn không có quyền xem hồ sơ này!";
        header('Location: index.php?controller=home&action=index');
        exit;
    }
    
    // Debug thành công
    error_log("Cho phép xem hồ sơ thành công");
    
    $showSidebar = true;
    require_once 'views/layouts/header.php';
    
    // Sidebar theo role
    if ($userRole === 'PHUHUYNH') {
        require_once 'views/layouts/sidebar/phuhuynh.php';
    } else if ($userRole === 'HOCSINH') {
        require_once 'views/layouts/sidebar/hocsinh.php';
    } else if (in_array($userRole, ['QTV', 'BGH', 'GIAOVIEN'])) {
        require_once 'views/layouts/sidebar/admin.php';
    }
    
    require_once 'views/tuyensinh/xemhoso_theohocsinh.php';
    require_once 'views/layouts/footer.php';
}

    // Xử lý hồ sơ (duyệt/từ chối)
    public function xulyhoso() {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if (!in_array($userRole, ['QTV', 'BGH'])) {
            $_SESSION['error'] = "Bạn không có quyền xử lý hồ sơ!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $maHoSo = $_GET['maHoSo'] ?? '';
        $title = "Xử lý hồ sơ tuyển sinh";

        if (empty($maHoSo)) {
            $_SESSION['error'] = "Không tìm thấy hồ sơ!";
            header('Location: index.php?controller=tuyensinh&action=danhsachhoso');
            exit;
        }

        // Lấy thông tin hồ sơ
        $hoSo = $this->tuyenSinhModel->getHoSoById($maHoSo);
        if (!$hoSo) {
            $_SESSION['error'] = "Không tìm thấy hồ sơ!";
            header('Location: index.php?controller=tuyensinh&action=danhsachhoso');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $trangThai = $_POST['trangThai'] ?? '';
            $ketQua = $_POST['ketQua'] ?? '';
            $ghiChu = $_POST['ghiChu'] ?? '';

            $result = $this->tuyenSinhModel->xuLyHoSo($maHoSo, $trangThai, $ketQua, $ghiChu);

            if ($result) {
                $_SESSION['success'] = "Cập nhật hồ sơ thành công!";
                header('Location: index.php?controller=tuyensinh&action=danhsachhoso');
                exit;
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật hồ sơ!";
            }
        }
        $showSidebar = true;
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/admin.php';
        require_once 'views/tuyensinh/xulyhoso.php';
        require_once 'views/layouts/footer.php';
    }

    // Xem chi tiết hồ sơ
    public function chitiethoso($maHoSo) {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if (!in_array($userRole, ['QTV', 'BGH'])) {
            $_SESSION['error'] = "Bạn không có quyền xem hồ sơ!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Chi tiết hồ sơ tuyển sinh";
        $hoSo = $this->tuyenSinhModel->getHoSoById($maHoSo);

        if (!$hoSo) {
            $_SESSION['error'] = "Không tìm thấy hồ sơ!";
            header('Location: index.php?controller=tuyensinh&action=danhsachhoso');
            exit;
        }
        $showSidebar = true;
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/admin.php';
        require_once 'views/tuyensinh/chitiethoso.php';
        require_once 'views/layouts/footer.php';
    }

    // Nhập điểm tuyển sinh
    public function nhapdiem($maHoSo) {
        $this->checkAuth();
        
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if (!in_array($userRole, ['QTV', 'BGH'])) {
            $_SESSION['error'] = "Bạn không có quyền nhập điểm!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $title = "Nhập điểm tuyển sinh";
        $hoSo = $this->tuyenSinhModel->getHoSoById($maHoSo);

        if (!$hoSo) {
            $_SESSION['error'] = "Không tìm thấy hồ sơ!";
            header('Location: index.php?controller=tuyensinh&action=danhsachhoso');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $diemToan = $_POST['diemToan'] ?? 0;
            $diemVan = $_POST['diemVan'] ?? 0;
            $diemAnh = $_POST['diemAnh'] ?? 0;
            $diemMon4 = $_POST['diemMon4'] ?? 0;
            $diemCong = $_POST['diemCong'] ?? 0;
            $dotThi = $_POST['dotThi'] ?? 'DOT1';

            $result = $this->tuyenSinhModel->nhapDiemTuyenSinh($maHoSo, [
                'diemToan' => $diemToan,
                'diemVan' => $diemVan,
                'diemAnh' => $diemAnh,
                'diemMon4' => $diemMon4,
                'diemCong' => $diemCong,
                'dotThi' => $dotThi
            ]);

            if ($result) {
                $_SESSION['success'] = "Nhập điểm thành công!";
                header('Location: index.php?controller=tuyensinh&action=chitiethoso&maHoSo=' . $maHoSo);
                exit;
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi nhập điểm!";
            }
        }
        $showSidebar = true;
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/admin.php';
        require_once 'views/tuyensinh/nhapdiem.php';
        require_once 'views/layouts/footer.php';
    }
}
?>