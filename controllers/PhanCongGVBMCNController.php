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

    // Thêm vào phương thức index
    public function index() {
        $title = "Phân Công Giáo viên BM/CN";
        
        // Lấy maTruong từ session
        $maTruong = $_SESSION['user']['maTruong'] ?? null;
        
        // Lấy dữ liệu CÓ maTruong
        $classes = $this->model->getAllClasses($maTruong); 
        $teachers = $this->model->getAllTeachers($maTruong);
        $subjects = $this->model->getAllSubjects();
        
        // Lấy thông tin tổ chuyên môn cho mỗi môn học
        $subjectGroups = [];
        foreach ($subjects as $subject) {
            $groupInfo = $this->model->getToChuyenMonByMonHoc($subject['maMonHoc']);
            $subjectGroups[$subject['maMonHoc']] = $groupInfo;
        }
        
        // Lấy dữ liệu thống kê thực tế CÓ maTruong
        $totalClasses = $this->model->getTotalClasses($maTruong);
        $totalTeachers = $this->model->getTotalTeachers($maTruong);
        $classesWithGVCN = $this->model->getClassesWithGVCN($maTruong);
        
        $showSidebar = true;
        $roleName = strtolower($_SESSION['user']['vaiTro']);
        $sidebarPath = 'views/layouts/sidebar/' . ($roleName === 'bgh' ? 'bangiamhieu' : $roleName) . '.php';

        require_once 'views/layouts/header.php';
        require_once $sidebarPath; 
        require_once 'views/phanconggvbmcn/index.php';
        require_once 'views/layouts/footer.php';
        exit();
    }

    // Thêm phương thức này vào controller
    public function ajaxGetTeachersBySubject() {
        header('Content-Type: application/json');
        
        $maMonHoc = intval($_GET['maMonHoc'] ?? 0);
        $maTruong = $_SESSION['user']['maTruong'] ?? null;
        
        if ($maMonHoc > 0) {
            $teachers = $this->model->getTeachersBySubject($maMonHoc, $maTruong);
            echo json_encode(['success' => true, 'teachers' => $teachers]);
        } else {
            echo json_encode(['success' => false, 'teachers' => []]);
        }
        exit;
    }

    // Trong PhanCongGVBMCNController.php

    public function saveAssignment() {
        $this->checkPermission(['QTV', 'BGH']);
        
        $maLop = intval($_POST['maLop'] ?? 0);
        $maGVCN = intval($_POST['maGVCN'] ?? 0);
        $assignments = $_POST['assignments'] ?? [];
        
        // Lấy maTruong từ session
        $maTruong = $_SESSION['user']['maTruong'] ?? null;

        if ($maLop === 0 || $maGVCN === 0) {
            $_SESSION['error'] = "Vui lòng chọn Lớp và Giáo viên Chủ nhiệm.";
            header('Location: index.php?controller=PhanCongGVBMCN&action=index'); 
            exit;
        }

        // --- BẮT ĐẦU SỬA: Kiểm tra trùng GVCN ---
        $tenLopTrung = $this->model->checkGVCNExisted($maGVCN, $maLop, $maTruong);
        
        if ($tenLopTrung) {
            // Lấy tên giáo viên để thông báo rõ hơn
            $teachers = $this->model->getAllTeachers($maTruong);
            $tenGiaoVien = '';
            foreach ($teachers as $t) {
                if ($t['maGiaoVien'] == $maGVCN) {
                    $tenGiaoVien = $t['hoTen'];
                    break;
                }
            }

            $_SESSION['error'] = "Giáo viên <strong>$tenGiaoVien</strong> đang chủ nhiệm lớp <strong>$tenLopTrung</strong>.<br>Một giáo viên chỉ được chủ nhiệm một lớp.";
            header('Location: index.php?controller=PhanCongGVBMCN&action=index'); 
            exit;
        }
        // --- KẾT THÚC SỬA ---
        
        // Lấy tất cả môn học
        $allSubjects = $this->model->getAllSubjects();
        $assignmentList = [];
        
        // Tạo danh sách phân công cho tất cả môn học
        foreach ($allSubjects as $subject) {
            $maMonHoc = $subject['maMonHoc'];
            $maGiaoVien = isset($assignments[$maMonHoc]) ? intval($assignments[$maMonHoc]) : 0;
            
            if ($maGiaoVien > 0) {
                $assignmentList[] = [
                    'maMonHoc' => $maMonHoc,
                    'maGiaoVien' => $maGiaoVien
                ];
            }
        }

        // Gọi hàm processAssignment
        $result = $this->model->processAssignment($maLop, $maGVCN, $assignmentList, $maTruong);

        if ($result === true) {
            $_SESSION['success'] = "Phân công giáo viên cho lớp thành công!";
        } elseif (is_array($result) && isset($result['error'])) {
            $_SESSION['error'] = $result['error']; // Hiển thị lỗi cụ thể từ model nếu có
        } else {
            $_SESSION['error'] = "Không thể lưu dữ liệu, vui lòng thử lại.";
        }

        header('Location: index.php?controller=PhanCongGVBMCN&action=index'); 
        exit;
    }
    
    // AJAX: Lấy phân công GVBM hiện tại
    // AJAX: Lấy phân công hiện tại
    public function ajaxGetAssignments() {
        header('Content-Type: application/json');
        $maLop = intval($_GET['maLop'] ?? 0);
        
        if ($maLop > 0) {
            // Lấy maTruong từ session
            $maTruong = $_SESSION['user']['maTruong'] ?? null;
            $assignments = $this->model->getSubjectAssignmentsByClass($maLop, $maTruong);
            
            // Lấy thêm thông tin phân công GVCN
            $gvcnAssignment = $this->model->getGVCNAssignmentByClass($maLop);
            
            echo json_encode([
                'success' => true, 
                'assignments' => $assignments,
                'gvcnAssignment' => $gvcnAssignment
            ]);
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

        // Lấy maTruong từ session
        $maTruong = $_SESSION['user']['maTruong'] ?? null;
        
        $gvcnAssignments = $this->model->getCurrentGVCNAssignments($maTruong);
        $classes = $this->model->getAllClasses($maTruong);
        
        // Lấy dữ liệu thống kê thực tế CÓ maTruong
        $totalClasses = $this->model->getTotalClasses($maTruong);
        $totalTeachers = $this->model->getTotalTeachers($maTruong);
        $classesWithGVCN = $this->model->getClassesWithGVCN($maTruong);
        $classesWithoutGVCN = $totalClasses - $classesWithGVCN;
        
        $showSidebar = true;
        $roleName = strtolower($_SESSION['user']['vaiTro']);
        $sidebarPath = 'views/layouts/sidebar/' . ($roleName === 'bgh' ? 'bangiamhieu' : $roleName) . '.php';

        require_once 'views/layouts/header.php';
        require_once $sidebarPath;
        require_once 'views/phanconggvbmcn/current_assignments.php';
        require_once 'views/layouts/footer.php';
        exit();
    }
}
?>