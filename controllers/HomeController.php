<?php
require_once 'models/HomeModel.php';
class HomeController
{

    private $homeModel;

    public function __construct()
    {
        $this->homeModel = new HomeModel();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    private function checkRole($allowedRoles)
    {
        $this->checkAuth();
        $userRole = $_SESSION['user']['vaiTro'] ?? '';

        if (!in_array($userRole, $allowedRoles)) {
            header('Location: index.php?controller=home&action=index');
            exit;
        }
    }

    
    // HÀM KIỂM TRA MÃ TRƯỜNG: CHỈ CHẠY ĐỐI VỚI BGH
    private function checkSchoolAccess() {
        $role = $_SESSION['user']['vaiTro'] ?? '';
        
        // Nếu là BGH mà không có maTruong, xóa session và chuyển hướng
        if ($role === 'BGH' && (!isset($_SESSION['user']['maTruong']) || empty($_SESSION['user']['maTruong']))) {
            // Hiển thị lỗi rõ ràng hơn
            $_SESSION['error'] = "Không tìm thấy mã trường trong phiên đăng nhập. Vui lòng đăng nhập lại.";
            unset($_SESSION['user']); 
            header('Location: index.php?controller=auth&action=login&error=missing_school_id');
            exit;
        }
    }

    public function index()
    {

        $this->checkAuth();

        // Chuyển hướng đến trang phù hợp với vai trò
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        switch ($userRole) {
            case 'QTV':
                $this->admin();
                break;
            case 'GIAOVIEN':
                $this->teacher();
                break;
            case 'HOCSINH':
                $this->student();
                break;
            case 'PHUHUYNH':
                $this->parent();
                break;
            case 'BGH':
                $this->principal();
                break;
            case 'TOTRUONG':
                $this->leader();
                break;
            default:
                $this->showDefaultHome();
        }
    }
    

    public function leader() {
    $this->checkRole(['TOTRUONG']); // Kiểm tra vai trò tổ trưởng
    $title = "Tổ trưởng chuyên môn - QLHS";

    // Lấy mã người dùng từ session
    $maNguoiDung = $_SESSION['user']['maNguoiDung'];

    // Lấy mã tổ trưởng từ bảng totruongchuyenmon
    $maToTruong = $this->homeModel->getMaToTruong($maNguoiDung);

    if (!$maToTruong) {
        $_SESSION['error'] = "Không tìm thấy thông tin tổ trưởng!";
        header('Location: index.php?controller=auth&action=login');
        exit;
    }

    // Lấy dữ liệu thống kê đề thi
    $stats = $this->homeModel->getLeaderStats($maToTruong);

    $showSidebar = true;

    require_once 'views/layouts/header.php';
    require_once 'views/layouts/sidebar/totruong.php';
    require_once 'views/home/totruong.php';
    require_once 'views/layouts/footer.php';
}



    public function admin()
    {
        $this->checkRole(['QTV']);
        $title = "Quản trị viên - QLHS";

        // Lấy dữ liệu thống kê
        $stats = $this->homeModel->getAdminStats();
        $systemOverview = $this->homeModel->getSystemOverview();
        $newNotifications = $this->homeModel->getNewNotifications('QTV');

        $showSidebar = true;

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/admin.php';
        require_once 'views/home/admin.php';
        require_once 'views/layouts/footer.php';
    }

    public function teacher()
    {
        $this->checkRole(['GIAOVIEN']);
        $title = "Giáo viên - QLHS";

        // Lấy mã người dùng từ session
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        // Lấy mã giáo viên thực tế
        $maGiaoVien = $this->homeModel->getMaGiaoVien($maNguoiDung);

        if (!$maGiaoVien) {
            $_SESSION['error'] = "Không tìm thấy thông tin giáo viên!";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Lấy dữ liệu thống kê
        $stats = $this->homeModel->getTeacherStats($maGiaoVien);
        $todaySchedule = $this->homeModel->getTodaySchedule($maNguoiDung, 'GIAOVIEN');
        $newNotifications = $this->homeModel->getNewNotifications('GIAOVIEN');
        $teacherClasses = $this->homeModel->getTeacherClasses($maGiaoVien);

        $showSidebar = true;

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php';
        require_once 'views/home/giaovien.php';
        require_once 'views/layouts/footer.php';
    }

    public function student()
    {
        $this->checkRole(['HOCSINH']);
        $title = "Học sinh - QLHS";

        // Lấy mã người dùng từ session
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        // Lấy thông tin học sinh
        $studentInfo = $this->homeModel->getStudentInfo($maNguoiDung);

        if (!$studentInfo) {
            $_SESSION['error'] = "Không tìm thấy thông tin học sinh!";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maHocSinh = $studentInfo['maHocSinh'];

        // Lấy dữ liệu thống kê
        $stats = $this->homeModel->getStudentStats($maHocSinh);
        $todaySchedule = $this->homeModel->getTodaySchedule($maNguoiDung, 'HOCSINH');
        $newNotifications = $this->homeModel->getNewNotifications('HOCSINH');
        $recentScores = $this->homeModel->getRecentScores($maHocSinh);
        $newAssignments = $this->homeModel->getNewAssignments($maHocSinh);

        $showSidebar = true;

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/hocsinh.php';
        require_once 'views/home/hocsinh.php';
        require_once 'views/layouts/footer.php';
    }

    public function parent()
    {
        $this->checkRole(['PHUHUYNH']);
        $title = "Phụ huynh - QLHS";

        // Lấy mã người dùng từ session
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        // Lấy mã phụ huynh thực tế
        $maPhuHuynh = $this->homeModel->getMaPhuHuynh($maNguoiDung);

        if (!$maPhuHuynh) {
            $_SESSION['error'] = "Không tìm thấy thông tin phụ huynh!";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Lấy dữ liệu thống kê
        $stats = $this->homeModel->getParentStats($maPhuHuynh);
        $newNotifications = $this->homeModel->getNewNotifications('PHUHUYNH');
        $parentChildren = $this->homeModel->getParentChildren($maPhuHuynh);

        $showSidebar = true;

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/phuhuynh.php';
        require_once 'views/home/phuhuynh.php';
        require_once 'views/layouts/footer.php';
    }

    public function principal()
    {
        $this->checkRole(['BGH']);
        $this->checkSchoolAccess(); // GỌI HÀM KIỂM TRA MÃ TRƯỜNG
        
        // Lấy mã trường sau khi đã chắc chắn nó tồn tại
        $maTruong = $_SESSION['user']['maTruong'];
        
        $title = "Ban giám hiệu - QLHS";

        $stats = $this->homeModel->getPrincipalStats();
        $systemOverview = $this->homeModel->getSystemOverview();
        $newNotifications = $this->homeModel->getNewNotifications('BGH');

        $showSidebar = true;

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/bangiamhieu.php'; // Sidebar riêng cho BGH
        require_once 'views/home/bangiamhieu.php';
        require_once 'views/layouts/footer.php';
    }

    private function showDefaultHome()
    {
        header('Location: index.php?controller=auth&action=login');
        exit;
    }

    // Thêm hàm xử lý lỗi và trang không tìm thấy
    public function notFound()
    {
        http_response_code(404);
        $title = "Trang không tồn tại - QLHS";

        require_once 'views/layouts/header.php';
        require_once 'views/errors/404.php';
        require_once 'views/layouts/footer.php';
    }

    // Thêm hàm xử lý lỗi server
    public function serverError()
    {
        http_response_code(500);
        $title = "Lỗi máy chủ - QLHS";

        require_once 'views/layouts/header.php';
        require_once 'views/errors/500.php';
        require_once 'views/layouts/footer.php';
    }}