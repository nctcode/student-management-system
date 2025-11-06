<?php
require_once 'models/GiaoVienModel.php'; 

class PhanCongGVBMCNController { 
    protected $model;

    public function __construct() {
        $this->model = new GiaoVienModel(); 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->checkPermission(['QTV', 'BGH']);
    }

    private function checkPermission($allowedRoles) {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['vaiTro'], $allowedRoles)) {
            header('Location: index.php?controller=home&action=index');
            exit;
        }
    }

    public function index() {
        $title = "Phân công Giáo viên BM/CN";
        
        // Lấy dữ liệu không cần maTruong
        $classes = $this->model->getAllClasses(); 
        $teachers = $this->model->getAllTeachers();
        $subjects = $this->model->getAllSubjects();
        
        // Lấy dữ liệu thống kê thực tế
        $totalClasses = $this->model->getTotalClasses();
        $totalTeachers = $this->model->getTotalTeachers();
        $classesWithGVCN = $this->model->getClassesWithGVCN();
        
        $showSidebar = true;
        $roleName = strtolower($_SESSION['user']['vaiTro']);
        $sidebarPath = 'views/layouts/sidebar/' . ($roleName === 'bgh' ? 'bangiamhieu' : $roleName) . '.php';

        require_once 'views/layouts/header.php';
        require_once $sidebarPath; 
        require_once 'views/phanconggvbmcn/index.php';
        require_once 'views/layouts/footer.php';
    }

    public function saveAssignment() {
        $this->checkPermission(['QTV', 'BGH']);
        
        $maLop = intval($_POST['maLop'] ?? 0);
        $maGVCN = intval($_POST['maGVCN'] ?? 0);
        $assignments = $_POST['assignments'] ?? [];

        if ($maLop === 0 || $maGVCN === 0) {
            $_SESSION['error'] = "Vui lòng chọn Lớp và Giáo viên Chủ nhiệm.";
            header('Location: index.php?controller=PhanCongGVBMCN&action=index'); 
            exit;
        }
        
        $assignmentList = [];
        foreach ($assignments as $maMonHoc => $maGiaoVien) {
            if (!empty($maGiaoVien)) {
                $assignmentList[] = [
                    'maMonHoc' => intval($maMonHoc),
                    'maGiaoVien' => intval($maGiaoVien)
                ];
            }
        }

        // Gọi hàm processAssignment không cần maTruong
        $result = $this->model->processAssignment($maLop, $maGVCN, $assignmentList);

        if ($result === true) {
            $_SESSION['success'] = "Phân công giáo viên cho lớp thành công!";
        } elseif (is_array($result) && isset($result['error'])) {
            if ($result['error'] === 'GVCN_DUPLICATE') {
                $_SESSION['error'] = "Lỗi: Giáo viên đã được gán chủ nhiệm lớp khác ({$result['lop']}).";
            } elseif ($result['error'] === 'GVBM_INVALID_CONDITION') {
                $errorDetails = "";
                foreach ($result['details'] as $detail) {
                    $errorDetails .= "• {$detail['giaoVien']} (Chuyên môn: {$detail['chuyenMon']}) không phù hợp với môn {$detail['monHoc']}\n";
                }
                $_SESSION['error'] = "Lỗi: Phân công không phù hợp chuyên môn:\n" . $errorDetails;
            } else {
                $_SESSION['error'] = "Không thể lưu dữ liệu, vui lòng thử lại.";
            }
        } else {
            $_SESSION['error'] = "Không thể lưu dữ liệu, vui lòng thử lại.";
        }

        header('Location: index.php?controller=PhanCongGVBMCN&action=index'); 
        exit;
    }
    
    // AJAX: Lấy phân công GVBM hiện tại
    public function ajaxGetAssignments() {
        header('Content-Type: application/json');
        $maLop = intval($_GET['maLop'] ?? 0);
        
        if ($maLop > 0) {
            $assignments = $this->model->getSubjectAssignmentsByClass($maLop);
            echo json_encode(['success' => true, 'assignments' => $assignments]);
        } else {
            echo json_encode(['success' => false, 'assignments' => []]);
        }
        exit;
    }

    /**
     * Xem phân công hiện tại
     */
    public function viewCurrentAssignments() {
        $this->checkPermission(['QTV', 'BGH']);
        
        $title = "Xem phân công hiện tại";

        $gvcnAssignments = $this->model->getCurrentGVCNAssignments();
        $classes = $this->model->getAllClasses();
        
        // Lấy dữ liệu thống kê thực tế
        $totalClasses = $this->model->getTotalClasses();
        $totalTeachers = $this->model->getTotalTeachers();
        $classesWithGVCN = $this->model->getClassesWithGVCN();
        $classesWithoutGVCN = $totalClasses - $classesWithGVCN;
        
        $showSidebar = true;
        $roleName = strtolower($_SESSION['user']['vaiTro']);
        $sidebarPath = 'views/layouts/sidebar/' . ($roleName === 'bgh' ? 'bangiamhieu' : $roleName) . '.php';

        require_once 'views/layouts/header.php';
        require_once $sidebarPath;
        require_once 'views/phanconggvbmcn/current_assignments.php';
        require_once 'views/layouts/footer.php';
    }
}
?>