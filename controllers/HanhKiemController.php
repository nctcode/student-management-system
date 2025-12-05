<?php
require_once 'models/HanhKiemModel.php';
require_once 'models/Database.php';

class HanhKiemController {
    private $model;
    private $db;

    public function __construct() {
        // Bắt đầu session nếu chưa có
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new HanhKiemModel($this->db);
    }

    private function checkAuth() {
        // Kiểm tra session theo cấu trúc của AuthController
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['vaiTro'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        // Chỉ cho phép giáo viên, BGH, QTV, TOTRUONG
        $allowedRoles = ['GIAOVIEN', 'BGH', 'QTV', 'TOTRUONG'];
        if (!in_array($_SESSION['user']['vaiTro'], $allowedRoles)) {
            echo "<script>alert('Bạn không có quyền truy cập!'); window.history.back();</script>";
            exit;
        }
    }

    private function getMaGiaoVien() {
        // Lấy mã giáo viên từ session
        if (isset($_SESSION['user']['maGiaoVien'])) {
            return $_SESSION['user']['maGiaoVien'];
        }
        
        // Nếu chưa có, lấy từ database dựa trên maNguoiDung
        $maNguoiDung = $_SESSION['user']['maNguoiDung'] ?? null;
        if ($maNguoiDung) {
            $query = "SELECT maGiaoVien FROM giaovien WHERE maNguoiDung = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$maNguoiDung]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $_SESSION['user']['maGiaoVien'] = $result['maGiaoVien'];
                return $result['maGiaoVien'];
            }
        }
        
        return null;
    }

    private function getCurrentHocKy() {
        // Lấy học kỳ hiện tại từ session hoặc mặc định
        if (isset($_SESSION['hoc_ky_hien_tai'])) {
            return $_SESSION['hoc_ky_hien_tai'];
        }
        
        // Lấy học kỳ hiện tại từ bảng nienkhoa
        $query = "SELECT CONCAT(hocKy, '-', YEAR(ngayBatDau)) AS hoc_ky 
                  FROM nienkhoa 
                  WHERE CURDATE() BETWEEN ngayBatDau AND ngayKetThuc 
                  ORDER BY maNienKhoa DESC 
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $_SESSION['hoc_ky_hien_tai'] = $result['hoc_ky'];
            return $result['hoc_ky'];
        }
        
        // Mặc định nếu không tìm thấy
        $defaultHocKy = 'HK1-2024';
        $_SESSION['hoc_ky_hien_tai'] = $defaultHocKy;
        return $defaultHocKy;
    }

    public function index() {
        $this->checkAuth();
        
        // Lấy thông tin giáo viên
        $maGiaoVien = $this->getMaGiaoVien();
        $hocKyHienTai = $this->getCurrentHocKy();
        
        // Lấy danh sách học kỳ từ database
        $query = "SELECT DISTINCT CONCAT(hocKy, '-', YEAR(ngayBatDau)) AS hoc_ky, 
                         CONCAT('Học kỳ ', 
                                CASE hocKy 
                                    WHEN 'HK1' THEN '1' 
                                    WHEN 'HK2' THEN '2' 
                                    ELSE 'Cả năm' 
                                END,
                                ' - Năm học ', 
                                YEAR(ngayBatDau), '-', YEAR(ngayKetThuc)
                         ) AS ten_hoc_ky
                  FROM nienkhoa 
                  ORDER BY ngayBatDau DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $dsHocKy = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Lấy thông tin lớp chủ nhiệm
        $lopChuNhiem = $this->model->getLopChuNhiem($maGiaoVien);
        
        // Kiểm tra xem có phải là giáo viên không
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        
        if (!$lopChuNhiem && $userRole === 'GIAOVIEN') {
            // Giáo viên không có lớp chủ nhiệm
            $dsHanhKiem = [];
            $thongBao = "Bạn không phải là giáo viên chủ nhiệm của lớp nào.";
        } else {
            // Lấy danh sách học sinh của lớp chủ nhiệm
            $dsHanhKiem = $this->model->getHocSinhByLopChuNhiem($maGiaoVien, $hocKyHienTai);
            $thongBao = null;
        }
        
        require 'views/hanh_kiem/index.php';
    }

    public function save() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maHocSinh = $_POST['maHocSinh'] ?? '';
            $hocKy = $_POST['hoc_ky'] ?? '';
            $diemSo = intval($_POST['diem_so'] ?? 0);
            $nhanXet = $_POST['nhan_xet'] ?? '';
            $action = $_POST['action'] ?? 'save';
            
            // Validate
            if (empty($maHocSinh) || empty($hocKy)) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
                exit();
            }
            
            // Kiểm tra quyền (nếu là giáo viên)
            $userRole = $_SESSION['user']['vaiTro'] ?? '';
            if ($userRole === 'GIAOVIEN') {
                $maGiaoVien = $this->getMaGiaoVien();
                if (!$this->model->isGiaoVienChuNhiem($maGiaoVien, $maHocSinh)) {
                    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền sửa điểm học sinh này']);
                    exit();
                }
            }
            
            if ($action === 'delete') {
                $id = $_POST['id'] ?? '';
                if (!empty($id) && $this->model->delete($id)) {
                    echo json_encode(['success' => true, 'message' => 'Xóa điểm thành công']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa điểm']);
                }
                exit();
            }
            
            // Validate điểm
            if ($diemSo < 0 || $diemSo > 100) {
                echo json_encode(['success' => false, 'message' => 'Điểm phải từ 0 đến 100']);
                exit();
            }
            
            // Logic tự động xếp loại
            $xepLoai = 'Yếu';
            if ($diemSo >= 90) $xepLoai = 'Xuất sắc';
            elseif ($diemSo >= 80) $xepLoai = 'Tốt';
            elseif ($diemSo >= 65) $xepLoai = 'Khá';
            elseif ($diemSo >= 50) $xepLoai = 'Trung bình';
            
            // Lưu điểm
            if ($this->model->saveHanhKiem($maHocSinh, $hocKy, $diemSo, $xepLoai, $nhanXet)) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Lưu điểm thành công',
                    'xep_loai' => $xepLoai
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu điểm']);
            }
            exit();
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    public function changeHocKy() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $hocKy = $_POST['hoc_ky'] ?? '';
            if (!empty($hocKy)) {
                $_SESSION['hoc_ky_hien_tai'] = $hocKy;
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            exit();
        }
    }
}
?>