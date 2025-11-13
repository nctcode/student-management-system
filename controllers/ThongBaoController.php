<?php
// controllers/ThongBaoController.php
require_once 'models/ThongBaoModel.php';

class ThongBaoController {
    private $thongBaoModel;

    public function __construct() {
        $this->thongBaoModel = new ThongBaoModel();
    }

    // Hiển thị form đăng thông báo
    public function dangthongbao() {
        // Kiểm tra quyền truy cập
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if (!in_array($userRole, ['QTV', 'BGH'])) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này";
            header('Location: index.php?controller=home');
            exit;
        }

        // Lấy danh sách lớp, khối, môn học
        $danhSachLop = $this->thongBaoModel->layDanhSachLop();
        $danhSachKhoi = $this->thongBaoModel->layDanhSachKhoi();
        $danhSachMonHoc = $this->thongBaoModel->layDanhSachMonHoc();

        $data = [
            'title' => 'Đăng Thông Báo',
            'danhSachLop' => $danhSachLop,
            'danhSachKhoi' => $danhSachKhoi,
            'danhSachMonHoc' => $danhSachMonHoc,
            'userRole' => $userRole
        ];
        
        $showSidebar = true;
        
        require_once 'views/layouts/header.php';
        
        // Load sidebar theo vai trò
        if ($userRole === 'BGH') {
            require_once 'views/layouts/sidebar/bangiamhieu.php';
        } elseif ($userRole === 'GIAOVIEN') {
            require_once 'views/layouts/sidebar/giaovien.php';
        } elseif ($userRole === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } elseif ($userRole === 'QTV') {
            require_once 'views/layouts/sidebar/quantrivien.php';
        } elseif ($userRole === 'PHUHUYNH') {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        } elseif ($userRole === 'TOTRUONG') {
            require_once 'views/layouts/sidebar/totruong.php';
        }
        require_once 'views/thongbao/dangthongbao.php';
        require_once 'views/layouts/footer.php';
    }

    // Xử lý đăng thông báo - ĐÃ SỬA HOÀN TOÀN
    public function xulydangthongbao() {
        // Kiểm tra quyền truy cập
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if (!in_array($userRole, ['QTV', 'BGH'])) {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thực hiện chức năng này']);
            exit;
        }

        // Debug: Log dữ liệu nhận được
        error_log("=== DEBUG THONG BAO ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));
        error_log("User role: " . $userRole);
        error_log("User ID: " . ($_SESSION['user']['maNguoiDung'] ?? 'NULL'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tieuDe = $_POST['tieuDe'] ?? '';
            $noiDung = $_POST['noiDung'] ?? '';
            $nguoiNhan = $_POST['nguoiNhan'] ?? 'TAT_CA';
            $maNguoiGui = $_SESSION['user']['maNguoiDung'] ?? '';
            
            // Các trường mới theo bảng - ĐÃ THÊM
            $thoiGianKetThuc = !empty($_POST['thoiGianKetThuc']) ? $_POST['thoiGianKetThuc'] : null;
            $maLop = !empty($_POST['maLop']) ? $_POST['maLop'] : null;
            $maKhoi = !empty($_POST['maKhoi']) ? $_POST['maKhoi'] : null;
            $maMonHoc = !empty($_POST['maMonHoc']) ? $_POST['maMonHoc'] : null;
            $loaiThongBao = $_POST['loaiThongBao'] ?? 'CHUNG';
            $uuTien = $_POST['uuTien'] ?? 'TRUNG_BINH';

            // Debug các field mới
            error_log("Thoi gian ket thuc: " . ($thoiGianKetThuc ?? 'NULL'));
            error_log("Ma lop: " . ($maLop ?? 'NULL'));
            error_log("Ma khoi: " . ($maKhoi ?? 'NULL'));
            error_log("Ma mon hoc: " . ($maMonHoc ?? 'NULL'));
            error_log("Loai thong bao: " . $loaiThongBao);
            error_log("Uu tien: " . $uuTien);

            // Validate dữ liệu cơ bản
            if (empty($tieuDe) || empty($noiDung)) {
                error_log("Validation failed: Empty title or content");
                echo json_encode(['success' => false, 'message' => 'Tiêu đề và nội dung không được để trống']);
                exit;
            }

            // Xử lý file đính kèm - ĐÃ SỬA
            $fileDinhKem = null;
            if (isset($_FILES['fileDinhKem']) && $_FILES['fileDinhKem']['error'] === 0) {
                error_log("File uploaded: " . $_FILES['fileDinhKem']['name']);
                
                $uploadDir = 'uploads/thongbao/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Kiểm tra loại file
                $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                $fileExtension = strtolower(pathinfo($_FILES['fileDinhKem']['name'], PATHINFO_EXTENSION));
                
                if (!in_array($fileExtension, $allowedTypes)) {
                    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận file PDF, DOC, DOCX, JPG, PNG']);
                    exit;
                }

                // Kiểm tra kích thước file (tối đa 5MB)
                if ($_FILES['fileDinhKem']['size'] > 5 * 1024 * 1024) {
                    echo json_encode(['success' => false, 'message' => 'File không được vượt quá 5MB']);
                    exit;
                }

                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['fileDinhKem']['tmp_name'], $uploadFile)) {
                    $fileDinhKem = $fileName; // CHỈ LƯU TÊN FILE, KHÔNG LƯU ĐƯỜNG DẪN ĐẦY ĐỦ
                    error_log("File saved to: " . $fileDinhKem);
                } else {
                    error_log("File upload failed");
                    echo json_encode(['success' => false, 'message' => 'Lỗi upload file']);
                    exit;
                }
            } else {
                error_log("No file uploaded or upload error: " . ($_FILES['fileDinhKem']['error'] ?? 'No file'));
            }

            // Thêm thông báo - ĐÃ SỬA VỚI ĐẦY ĐỦ THAM SỐ
            try {
                error_log("Calling themThongBao method with full parameters...");
                $result = $this->thongBaoModel->themThongBao(
                    $tieuDe, 
                    $noiDung, 
                    $maNguoiGui, 
                    $nguoiNhan, 
                    $fileDinhKem,
                    $thoiGianKetThuc,
                    $maLop,
                    $maKhoi,
                    $maMonHoc,
                    $loaiThongBao,
                    $uuTien
                );
                
                error_log("Result from themThongBao: " . ($result ? 'TRUE' : 'FALSE'));
                
                if ($result) {
                    error_log("Thong bao created successfully");
                    echo json_encode(['success' => true, 'message' => 'Đăng thông báo thành công']);
                } else {
                    error_log("Thong bao creation failed");
                    echo json_encode(['success' => false, 'message' => 'Đăng thông báo thất bại']);
                }
            } catch (Exception $e) {
                error_log("Exception in themThongBao: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
            }
        } else {
            error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
        }
    }

    // Hiển thị danh sách thông báo
    public function danhsach() {
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        
        // Lấy thông báo theo vai trò người dùng
        switch ($userRole) {
            case 'HOCSINH':
                $thongBao = $this->thongBaoModel->layThongBaoTheoNguoiNhan('HOC_SINH');
                break;
            case 'PHUHUYNH':
                $thongBao = $this->thongBaoModel->layThongBaoTheoNguoiNhan('PHU_HUYNH');
                break;
            case 'GIAOVIEN':
                $thongBao = $this->thongBaoModel->layThongBaoTheoNguoiNhan('GIAO_VIEN');
                break;
            case 'QTV':
            case 'BGH':
                $thongBao = $this->thongBaoModel->layTatCaThongBao();
                break;
            default:
                $thongBao = $this->thongBaoModel->layThongBaoTheoNguoiNhan('TAT_CA');
        }

        $data = [
            'title' => 'Danh Sách Thông Báo',
            'thongBao' => $thongBao,
            'userRole' => $userRole
        ];

        $showSidebar = true;
        
        require_once 'views/layouts/header.php';
        
        // Load sidebar theo vai trò
        if ($userRole === 'BGH') {
            require_once 'views/layouts/sidebar/bangiamhieu.php';
        } elseif ($userRole === 'GIAOVIEN') {
            require_once 'views/layouts/sidebar/giaovien.php';
        } elseif ($userRole === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } elseif ($userRole === 'QTV') {
            require_once 'views/layouts/sidebar/quantrivien.php';
        } elseif ($userRole === 'PHUHUYNH') {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        } elseif ($userRole === 'TOTRUONG') {
            require_once 'views/layouts/sidebar/totruong.php';
        }
        
        require_once 'views/thongbao/danhsach.php';
        require_once 'views/layouts/footer.php';
    }

    // Xem chi tiết thông báo
    // Xem chi tiết thông báo - SỬA LẠI
    public function chitiet($maThongBao = null) {
        // Debug để xem có nhận được maThongBao không
        error_log("=== CHI TIET THONG BAO ===");
        error_log("maThongBao from param: " . ($maThongBao ?? 'NULL'));
        error_log("GET maThongBao: " . ($_GET['maThongBao'] ?? 'NULL'));
        
        // Ưu tiên lấy từ parameter trước
        if (empty($maThongBao)) {
            $maThongBao = $_GET['maThongBao'] ?? '';
        }
        
        error_log("Final maThongBao: " . $maThongBao);
        
        if (empty($maThongBao) || !is_numeric($maThongBao)) {
            $_SESSION['error'] = "Mã thông báo không hợp lệ";
            header('Location: index.php?controller=thongbao&action=danhsach');
            exit;
        }

        $thongBao = $this->thongBaoModel->layThongBaoTheoMa($maThongBao);
        
        error_log("Thong bao result: " . ($thongBao ? 'FOUND' : 'NOT FOUND'));
        
        if (!$thongBao) {
            $_SESSION['error'] = "Thông báo không tồn tại";
            header('Location: index.php?controller=thongbao&action=danhsach');
            exit;
        }

        // Kiểm tra quyền xem thông báo
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $allowed = false;
        
        switch ($userRole) {
            case 'HOCSINH':
                $allowed = in_array($thongBao['nguoiNhan'], ['HOC_SINH', 'TAT_CA']);
                break;
            case 'PHUHUYNH':
                $allowed = in_array($thongBao['nguoiNhan'], ['PHU_HUYNH', 'TAT_CA']);
                break;
            case 'GIAOVIEN':
                $allowed = in_array($thongBao['nguoiNhan'], ['GIAO_VIEN', 'TAT_CA']);
                break;
            case 'QTV':
            case 'BGH':
                $allowed = true;
                break;
            default:
                $allowed = false;
        }

        if (!$allowed) {
            $_SESSION['error'] = "Bạn không có quyền xem thông báo này";
            header('Location: index.php?controller=thongbao&action=danhsach');
            exit;
        }

        // Cập nhật trạng thái đã xem (chỉ cho học sinh, phụ huynh, giáo viên)
        if (in_array($userRole, ['HOCSINH', 'PHUHUYNH', 'GIAOVIEN'])) {
            $this->thongBaoModel->capNhatTrangThai($maThongBao, 'Đã xem');
        }

        $data = [
            'title' => $thongBao['tieuDe'],
            'thongBao' => $thongBao,
            'userRole' => $userRole
        ];

        $showSidebar = true;
        
        require_once 'views/layouts/header.php';
        
        // Load sidebar theo vai trò
        if ($userRole === 'BGH') {
            require_once 'views/layouts/sidebar/bangiamhieu.php';
        } elseif ($userRole === 'GIAOVIEN') {
            require_once 'views/layouts/sidebar/giaovien.php';
        } elseif ($userRole === 'HOCSINH') {
            require_once 'views/layouts/sidebar/hocsinh.php';
        } elseif ($userRole === 'QTV') {
            require_once 'views/layouts/sidebar/quantrivien.php';
        } elseif ($userRole === 'PHUHUYNH') {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        } elseif ($userRole === 'TOTRUONG') {
            require_once 'views/layouts/sidebar/totruong.php';
        }
        
        require_once 'views/thongbao/chitiet.php';
        require_once 'views/layouts/footer.php';
    }  
     
    // Xóa thông báo (chỉ QTV và BGH)
    public function xoa($maThongBao) {
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if (!in_array($userRole, ['QTV', 'BGH'])) {
            $_SESSION['error'] = "Bạn không có quyền xóa thông báo";
            header('Location: index.php?controller=thongbao&action=danhsach');
            exit;
        }

        $result = $this->thongBaoModel->xoaThongBao($maThongBao);
        
        if ($result) {
            $_SESSION['success'] = "Xóa thông báo thành công";
        } else {
            $_SESSION['error'] = "Xóa thông báo thất bại";
        }

        header('Location: index.php?controller=thongbao&action=danhsach');
    }
    // Thêm vào ThongBaoController.php

    // Tải thông báo cho dropdown
    public function loadNotifications() {
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $thongBao = [];
        
        // Lấy thông báo mới nhất (tối đa 10 cái)
        switch ($userRole) {
            case 'HOCSINH':
                $thongBao = $this->thongBaoModel->layThongBaoTheoNguoiNhan('HOC_SINH');
                break;
            case 'PHUHUYNH':
                $thongBao = $this->thongBaoModel->layThongBaoTheoNguoiNhan('PHU_HUYNH');
                break;
            case 'GIAOVIEN':
                $thongBao = $this->thongBaoModel->layThongBaoTheoNguoiNhan('GIAO_VIEN');
                break;
            case 'QTV':
            case 'BGH':
                $thongBao = $this->thongBaoModel->layTatCaThongBao();
                break;
        }
        
        // Giới hạn 10 thông báo mới nhất
        $thongBao = array_slice($thongBao, 0, 10);
        
        if (empty($thongBao)) {
            echo '<div class="text-center p-3 text-muted">Không có thông báo nào</div>';
            return;
        }
        
        foreach ($thongBao as $tb) {
            $isUnread = $tb['trangThai'] === 'Chưa xem';
            $timeAgo = $this->timeAgo($tb['ngayGui']);
            $detailUrl = "index.php?controller=thongbao&action=chitiet&maThongBao={$tb['maThongBao']}";
            
            echo "
            <div class='notification-item " . ($isUnread ? 'unread' : '') . "' 
                data-id='{$tb['maThongBao']}' 
                data-url='{$detailUrl}'>
                <div class='notification-title'>{$tb['tieuDe']}</div>
                <div class='notification-content'>" . 
                    (strlen($tb['noiDung']) > 100 ? 
                    substr($tb['noiDung'], 0, 100) . '...' : $tb['noiDung']) . 
                "</div>
                <div class='notification-time'>
                    <i class='fas fa-clock'></i> {$timeAgo}
                    " . ($tb['fileDinhKem'] ? ' • <i class="fas fa-paperclip"></i>' : '') . "
                </div>
            </div>";
        }
    }

    // Đánh dấu đã đọc
    public function markAsRead() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maThongBao = $_POST['maThongBao'] ?? '';
            $userRole = $_SESSION['user']['vaiTro'] ?? '';
            
            // Chỉ đánh dấu đã đọc cho học sinh, phụ huynh, giáo viên
            if (in_array($userRole, ['HOCSINH', 'PHUHUYNH', 'GIAOVIEN'])) {
                $this->thongBaoModel->capNhatTrangThai($maThongBao, 'Đã xem');
            }
            
            echo json_encode(['success' => true]);
        }
    }

    // Hàm tính thời gian
    // Sửa từ private thành public
    public function timeAgo($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return 'Vừa xong';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' phút trước';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' giờ trước';
        } elseif ($diff < 2592000) {
            return floor($diff / 86400) . ' ngày trước';
        } else {
            return date('d/m/Y', $time);
        }
    }
}
?>