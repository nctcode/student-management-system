<?php
require_once 'models/BaiTapModel.php';
require_once 'models/GiaoVienModel.php'; 

class BaiTapController {
    private $baiTapModel;
    private $giaoVienModel;
    private $maGiaoVien;

    public function __construct() {
        $this->baiTapModel = new BaiTapModel();
        $this->giaoVienModel = new GiaoVienModel(); 
    }

    // Kiểm tra quyền (GV) và lấy maGiaoVien
    private function checkAuthAndGetMaGV() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['vaiTro'] !== 'GIAOVIEN') {
            $_SESSION['error'] = "Bạn không có quyền truy cập!";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        $giaoVienInfo = $this->giaoVienModel->getGiaoVienByMaNguoiDung($_SESSION['user']['maNguoiDung']);
        if (!$giaoVienInfo) {
            $_SESSION['error'] = "Tài khoản của bạn không được liên kết với một hồ sơ giáo viên.";
            header('Location: index.php?controller=home&action=index'); 
            exit;
        }
        $this->maGiaoVien = $giaoVienInfo['maGiaoVien'];
    }

    // Hiển thị trang Giao bài tập
    public function index() {
        $this->checkAuthAndGetMaGV();
        
        // Lấy danh sách lớp và môn GV này dạy
        $danhSachPhanCong = $this->baiTapModel->getLopVaMonHocGiaoVien($this->maGiaoVien);
        
        $title = "Giao bài tập";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php'; 
        require_once 'views/baitap/giaobaitap.php';
        require_once 'views/layouts/footer.php';
    }

    public function luu() {
        $this->checkAuthAndGetMaGV();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu
            $maLop = $_POST['maLop'] ?? 0;
            $maMonHoc = $_POST['maMonHoc'] ?? 0;
            $tenBT = trim($_POST['tenBT'] ?? '');
            $moTa = trim($_POST['moTa'] ?? '');
            $hanNop = $_POST['hanNop'] ?? '';

            if (empty($maLop) || empty($maMonHoc) || empty($tenBT) || empty($hanNop)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ Tiêu đề, Hạn nộp, Lớp và Môn học!";
                header("Location: index.php?controller=baitap&action=index"); 
                exit;
            }
            
            try {
                $hanNopDate = new DateTime($hanNop);
                $now = new DateTime();
                
                // Hạn nộp phải lớn hơn hiện tại
                if ($hanNopDate <= $now) {
                     $_SESSION['error'] = "Hạn nộp phải ở trong tương lai!";
                     header("Location: index.php?controller=baitap&action=index"); 
                     exit;
                }
            } catch (Exception $e) {
                 $_SESSION['error'] = "Định dạng ngày Hạn nộp không hợp lệ!";
                 header("Location: index.php?controller=baitap&action=index"); 
                 exit;
            }

            // Xử lý upload file
            $fileDinhKemJSON = null;
            $filesInfo = $this->xuLyUploadFile(); // Trả về mảng file hoặc null
            
            if ($filesInfo !== null) {
                $fileDinhKemJSON = json_encode($filesInfo);
            }
            // (Nếu xuLyUploadFile() thất bại, nó sẽ tự đặt $_SESSION['error'] và trả về null)

            // Nếu không có lỗi file, tiếp tục lưu
            if (!isset($_SESSION['error'])) {
                // Lưu vào CSDL
                try {
                    $result = $this->baiTapModel->giaoBaiTap(
                        $this->maGiaoVien,
                        $maLop,
                        $maMonHoc,
                        $tenBT,
                        $moTa,
                        $hanNop,
                        $fileDinhKemJSON
                    );
                    
                    if ($result) {
                        $_SESSION['success'] = "Giao bài tập thành công!";
                    } else {
                        // Lỗi CSDL
                        $_SESSION['error'] = "Không thể lưu bài tập. Đã có lỗi CSDL!";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Lỗi: " . $e->getMessage();
                }
            }

            header("Location: index.php?controller=baitap&action=danhsach");
            exit;
        }
    }

    private function xuLyUploadFile() {
        if (!isset($_FILES['fileDinhKem']) || empty($_FILES['fileDinhKem']['name'][0])) {
            return null; // Không có file nào
        }

        $files = $_FILES['fileDinhKem'];
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx', 'xls', 'mp4', 'mov', 'avi', 'mp3', 'zip', 'rar'];
        $maxSize = 20 * 1024 * 1024; 
        $uploadDir = 'uploads/baitap/';
        
        $uploadedFilesInfo = []; 

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                continue; 
            }

            $fileSize = $files['size'][$key];
            $fileTmpName = $files['tmp_name'][$key];
            
            if ($fileSize > $maxSize) {
                $_SESSION['error'] = "File '" . htmlspecialchars($name) . "' vượt quá 20MB!";
                return null; 
            }

            $fileExtension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedTypes)) {
                $_SESSION['error'] = "File '" . htmlspecialchars($name) . "' có định dạng không hỗ trợ!";
                return null;
            }

            $fileName = uniqid() . '_' . time() . '_' . $key . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpName, $filePath)) {
                $uploadedFilesInfo[] = [
                    'tenFile' => $name,
                    'duongDan' => $filePath,
                    'kichThuoc' => $fileSize
                ];
            } else {
                $_SESSION['error'] = "Có lỗi khi lưu file '" . htmlspecialchars($name) . "'!";
                return null;
            }
        }

        return $uploadedFilesInfo;
    }

    // Hiển thị trang Danh sách bài tập đã giao
    public function danhsach() {
        $this->checkAuthAndGetMaGV();
        
        // Lấy danh sách bài tập từ Model
        $danhSachBaiTap = $this->baiTapModel->getDanhSachBaiTapDaGiao($this->maGiaoVien);
        
        $title = "Bài tập đã giao";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php'; 
        require_once 'views/baitap/danhsach.php';
        require_once 'views/layouts/footer.php';
    }

    // Hiển thị trang Chi tiết bài tập
    public function chitiet($maBaiTap) { 
        $this->checkAuthAndGetMaGV(); 
        
        $baiTap = $this->baiTapModel->getBaiTapChiTiet($maBaiTap);
        
        if (!$baiTap) {
            $_SESSION['error'] = "Không tìm thấy bài tập này.";
            header("Location: index.php?controller=baitap&action=danhsach");
            exit;
        }
        
        $title = "Chi tiết bài tập";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php'; 
        require_once 'views/baitap/chitiet.php';
        require_once 'views/layouts/footer.php';
    }
}
?>