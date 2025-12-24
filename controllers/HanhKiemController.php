<?php
require_once 'models/HanhKiemModel.php';
require_once 'models/Database.php';

class HanhKiemController {
    private $model;
    private $db;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $database = new Database();
        $this->db = $database->getConnection();
        $this->model = new HanhKiemModel($this->db);
    }

    private function checkAuth() {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['vaiTro'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        $allowedRoles = ['GIAOVIEN', 'BGH', 'QTV', 'TOTRUONG'];
        if (!in_array($_SESSION['user']['vaiTro'], $allowedRoles)) {
            echo "<script>alert('Bạn không có quyền truy cập!'); window.history.back();</script>";
            exit;
        }
    }

    private function getMaGiaoVien() {
        if (isset($_SESSION['user']['maGiaoVien'])) {
            return $_SESSION['user']['maGiaoVien'];
        }
        
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
        if (isset($_SESSION['hoc_ky_hien_tai'])) {
            return $_SESSION['hoc_ky_hien_tai'];
        }
        
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
        
        $defaultHocKy = 'HK1-' . date('Y');
        $_SESSION['hoc_ky_hien_tai'] = $defaultHocKy;
        return $defaultHocKy;
    }

    public function index() {
        $this->checkAuth();
        
        $maGiaoVien = $this->getMaGiaoVien();
        $hocKyHienTai = $this->getCurrentHocKy();
        
        // Lấy danh sách học kỳ
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
        
        $lopChuNhiem = $this->model->getLopChuNhiem($maGiaoVien);
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        
        if (!$lopChuNhiem && $userRole === 'GIAOVIEN') {
            $dsHanhKiem = [];
            $thongBao = "Bạn không phải là giáo viên chủ nhiệm của lớp nào.";
        } else {
            $dsHanhKiem = $this->model->getHocSinhByLopChuNhiem($maGiaoVien, $hocKyHienTai);
            
            // Tính thống kê
            $tongHS = count($dsHanhKiem);
            $daCham = 0;
            $tongDiem = 0;
            
            foreach ($dsHanhKiem as $hs) {
                if (!empty($hs['diem_so']) || $hs['diem_so'] === 0) {
                    $daCham++;
                    $tongDiem += $hs['diem_so'];
                }
            }
            
            $diemTB = $daCham > 0 ? round($tongDiem / $daCham, 1) : 0;
            $chuaCham = $tongHS - $daCham;
            
            $thongBao = null;
        }
        
        require 'views/hanh_kiem/index.php';
    }

    // CHỈNH SỬA: Thêm action để lấy form nhập nhanh
    public function nhapNhanh() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maHocSinh = $_POST['maHocSinh'] ?? '';
            $hocKy = $_POST['hoc_ky'] ?? '';
            
            if (empty($maHocSinh) || empty($hocKy)) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
                exit();
            }
            
            // Kiểm tra xem đã có điểm chưa
            $query = "SELECT hk.*, nd.hoTen 
                      FROM hanh_kiem hk
                      JOIN hocsinh hs ON hk.sinh_vien_id = hs.maHocSinh
                      JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                      WHERE hk.sinh_vien_id = :maHocSinh AND hk.hoc_ky = :hocKy";
            
            try {
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':maHocSinh', $maHocSinh, PDO::PARAM_INT);
                $stmt->bindParam(':hocKy', $hocKy, PDO::PARAM_STR);
                $stmt->execute();
                $hanhKiem = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $hanhKiem
                ]);
                
            } catch(PDOException $e) {
                error_log("Lỗi truy vấn nhapNhanh: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn']);
            }
            exit();
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    // CHỈNH SỬA: Cải tiến hàm save để xử lý tốt hơn
    public function save() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maHocSinh = $_POST['maHocSinh'] ?? '';
            $hocKy = $_POST['hoc_ky'] ?? '';
            $diemSo = isset($_POST['diem_so']) && $_POST['diem_so'] !== '' ? intval($_POST['diem_so']) : null;
            $nhanXet = $_POST['nhan_xet'] ?? '';
            $action = $_POST['action'] ?? 'save';
            
            // Validate
            if (empty($maHocSinh) || empty($hocKy)) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
                exit();
            }
            
            // Kiểm tra quyền
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
            if ($diemSo !== null && ($diemSo < 0 || $diemSo > 100)) {
                echo json_encode(['success' => false, 'message' => 'Điểm phải từ 0 đến 100']);
                exit();
            }
            
            // Logic tự động xếp loại
            $xepLoai = 'Chưa xếp loại';
            if ($diemSo !== null) {
                if ($diemSo >= 90) $xepLoai = 'Xuất sắc';
                elseif ($diemSo >= 80) $xepLoai = 'Tốt';
                elseif ($diemSo >= 65) $xepLoai = 'Khá';
                elseif ($diemSo >= 50) $xepLoai = 'Trung bình';
                elseif ($diemSo >= 0) $xepLoai = 'Yếu';
            }
            
            // Lưu điểm (cho phép lưu cả khi điểm là null)
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

    // CHỈNH SỬA: Thêm chức năng nhập hàng loạt
    public function nhapHangLoat() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $hocKy = $data['hoc_ky'] ?? '';
            $dsDiem = $data['ds_diem'] ?? [];
            $maGiaoVien = $this->getMaGiaoVien();
            
            if (empty($hocKy) || empty($dsDiem)) {
                echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
                exit();
            }
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($dsDiem as $item) {
                $maHocSinh = $item['maHocSinh'] ?? '';
                $diemSo = isset($item['diem_so']) && $item['diem_so'] !== '' ? intval($item['diem_so']) : null;
                $nhanXet = $item['nhan_xet'] ?? '';
                
                if (empty($maHocSinh)) {
                    $errorCount++;
                    continue;
                }
                
                // Kiểm tra quyền (nếu là giáo viên)
                $userRole = $_SESSION['user']['vaiTro'] ?? '';
                if ($userRole === 'GIAOVIEN') {
                    if (!$this->model->isGiaoVienChuNhiem($maGiaoVien, $maHocSinh)) {
                        $errorCount++;
                        $errors[] = "Không có quyền nhập điểm cho HS $maHocSinh";
                        continue;
                    }
                }
                
                // Validate điểm
                if ($diemSo !== null && ($diemSo < 0 || $diemSo > 100)) {
                    $errorCount++;
                    $errors[] = "Điểm HS $maHocSinh không hợp lệ";
                    continue;
                }
                
                // Logic xếp loại
                $xepLoai = 'Chưa xếp loại';
                if ($diemSo !== null) {
                    if ($diemSo >= 90) $xepLoai = 'Xuất sắc';
                    elseif ($diemSo >= 80) $xepLoai = 'Tốt';
                    elseif ($diemSo >= 65) $xepLoai = 'Khá';
                    elseif ($diemSo >= 50) $xepLoai = 'Trung bình';
                    elseif ($diemSo >= 0) $xepLoai = 'Yếu';
                }
                
                // Lưu điểm
                if ($this->model->saveHanhKiem($maHocSinh, $hocKy, $diemSo, $xepLoai, $nhanXet)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Lỗi lưu điểm HS $maHocSinh";
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => "Đã nhập thành công $successCount học sinh. Lỗi: $errorCount",
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors
            ]);
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