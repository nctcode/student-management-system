<?php
require_once 'models/BaiTapModel.php';
require_once 'models/GiaoVienModel.php'; 
require_once 'models/HocSinhModel.php';


class BaiTapController {
    private $baiTapModel;
    private $giaoVienModel;
    private $hocSinhModel;
    private $maGiaoVien;
    private $maHocSinh; 
    private $maLop;

    public function __construct() {
        $this->baiTapModel = new BaiTapModel();
        $this->giaoVienModel = new GiaoVienModel(); 
        $this->hocSinhModel = new HocSinhModel();
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
            $_SESSION['error'] = "Tài khoản của bạn không được liên kết với một hồ sơ giáo viên!";
            header('Location: index.php?controller=home&action=index'); 
            exit;
        }
        $this->maGiaoVien = $giaoVienInfo['maGiaoVien'];
    }

    // Kiểm tra quyền (HS) và lấy maHocSinh, maLop
    private function checkAuthAndGetMaHS() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['vaiTro'] !== 'HOCSINH') {
            $_SESSION['error'] = "Bạn không có quyền truy cập!";
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        
        $hocSinhInfo = $this->hocSinhModel->getHocSinhByNguoiDung($_SESSION['user']['maNguoiDung']);
        if (!$hocSinhInfo || empty($hocSinhInfo['maLop'])) {
            $_SESSION['error'] = "Tài khoản của bạn chưa được xếp lớp hoặc không liên kết với hồ sơ học sinh!";
            header('Location: index.php?controller=home&action=index'); 
            exit;
        }
        $this->maHocSinh = $hocSinhInfo['maHocSinh'];
        $this->maLop = $hocSinhInfo['maLop'];
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
            $filesInfo = $this->xuLyUploadFile();
            
            if ($filesInfo !== null) {
                $fileDinhKemJSON = json_encode($filesInfo);
            }

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
            return null;
        }

        $files = $_FILES['fileDinhKem'];
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx', 'xls', 'mp4', 'mov', 'avi', 'mp3', 'zip', 'rar', 'txt', 'ppt', 'pptx'];
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
            
            if ($fileSize == 0) {
                $_SESSION['error'] = "File '" . htmlspecialchars($name) . "' bị rỗng (0 byte) và sẽ không được tải lên!";
                return null;
            }
            
            if ($fileSize > $maxSize) {
                $_SESSION['error'] = "File '" . htmlspecialchars($name) . "' vượt quá 20MB!";
                return null; 
            }

            $fileExtension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedTypes)) {
                $_SESSION['error'] = "File '" . htmlspecialchars($name) . "' có định dạng (." . $fileExtension . ") không hỗ trợ!";
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
            $_SESSION['error'] = "Không tìm thấy bài tập này!";
            header("Location: index.php?controller=baitap&action=danhsach");
            exit;
        }

        $danhSachNopBai = $this->baiTapModel->getDanhSachNopBai($maBaiTap);

        $maLop = $baiTap['maLop'];
        $siSo = $this->hocSinhModel->getSoLuongHocSinhByLop($maLop);
        $statsNopBai = $this->baiTapModel->getThongKeNopBai($maBaiTap);
        $daNop = $statsNopBai['DaNop'];
        $nopTre = $statsNopBai['NopTre'];
        $tongDaNop = $daNop + $nopTre;
        $chuaNop = $siSo - $tongDaNop;
        
        $thongKe = [
            'siSo' => $siSo,
            'daNop' => $daNop,
            'nopTre' => $nopTre,
            'tongDaNop' => $tongDaNop,
            'chuaNop' => ($chuaNop < 0) ? 0 : $chuaNop 
        ];
        
        $title = "Chi tiết bài tập";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php'; 
        require_once 'views/baitap/chitiet.php';
        require_once 'views/layouts/footer.php';
    }

    // Hiển thị danh sách bài tập của học sinh
    public function danhsach_hs() {
        $this->checkAuthAndGetMaHS();
        
        $danhSachBaiTap = $this->baiTapModel->getDanhSachBaiTapChoHocSinh($this->maLop, $this->maHocSinh);
        
        $title = "Bài tập của tôi";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/hocsinh.php'; 
        require_once 'views/baitap/danhsach_hs.php'; 
        require_once 'views/layouts/footer.php';
    }

    // Hiển thị chi tiết bài tập VÀ form nộp bài
    public function chitiet_hs($maBaiTap) {
        $this->checkAuthAndGetMaHS();
        
        $baiTap = $this->baiTapModel->getBaiTapChiTietChoHocSinh($maBaiTap, $this->maHocSinh);
        
        if (!$baiTap || $baiTap['maLop'] != $this->maLop) {
            $_SESSION['error'] = "Không tìm thấy bài tập này hoặc bài tập không thuộc lớp của bạn!";
            header("Location: index.php?controller=baitap&action=danhsach_hs");
            exit;
        }
        
        $title = "Chi tiết bài tập";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/hocsinh.php'; 
        require_once 'views/baitap/chitiet_hs.php';
        require_once 'views/layouts/footer.php';
    }

    // Xử lý nộp bài
    public function nopbai() {
        $this->checkAuthAndGetMaHS();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maBaiTap = $_POST['maBaiTap'] ?? 0;

            $baiTap = $this->baiTapModel->getBaiTapChiTiet($maBaiTap);
            if (!$baiTap || $baiTap['maLop'] != $this->maLop) {
                $_SESSION['error'] = "Lỗi: Bài tập không hợp lệ!";
                header("Location: index.php?controller=baitap&action=danhsach_hs");
                exit;
            }

            $hanNopDate = new DateTime($baiTap['hanNop']);
            $now = new DateTime();
            $hetHan = $now > $hanNopDate;

            $newFilesInfo = $this->xuLyUploadFile();
            
            if ($newFilesInfo === null && !isset($_SESSION['error'])) {
                 $_SESSION['error'] = "Bạn chưa đính kèm file nào để thêm!";
                 header("Location: index.php?controller=baitap&action=chitiet_hs&maBaiTap=" . $maBaiTap);
                 exit;
            }
            if (isset($_SESSION['error'])) { 
                 header("Location: index.php?controller=baitap&action=chitiet_hs&maBaiTap=" . $maBaiTap);
                 exit;
            }

            $baiNop = $this->baiTapModel->getBaiNop($maBaiTap, $this->maHocSinh);
            $existingFiles = [];
            if ($baiNop && !empty($baiNop['fileDinhKem'])) {
                $existingFiles = json_decode($baiNop['fileDinhKem'], true);
                if (!is_array($existingFiles)) { $existingFiles = []; }
            }
        
            $mergedFiles = array_merge($existingFiles, $newFilesInfo);
            $fileDinhKemJSON = json_encode($mergedFiles);

            $trangThai = $hetHan ? 'Nộp trễ' : 'Đã nộp';

            $result = $this->baiTapModel->nopBai(
                $maBaiTap,
                $this->maHocSinh,
                $fileDinhKemJSON,
                $trangThai
            );

            if ($result) {
                $_SESSION['success'] = "Thêm file thành công!";
            } else {
                $_SESSION['error'] = "Đã có lỗi CSDL xảy ra, không thể thêm file!";
            }

            header("Location: index.php?controller=baitap&action=chitiet_hs&maBaiTap=" . $maBaiTap);
            exit;
        }
    }

    // Tải tất cả bài nộp của học sinh dưới dạng file ZIP
    public function taiTatCaBaiNop($maBaiTap) {
        $this->checkAuthAndGetMaGV();

        $baiTap = $this->baiTapModel->getBaiTapChiTiet($maBaiTap);
        if (!$baiTap || $baiTap['maGV'] != $this->maGiaoVien) {
            $_SESSION['error'] = "Không tìm thấy bài tập hoặc bạn không có quyền!";
            header("Location: index.php?controller=baitap&action=danhsach");
            exit;
        }

        $danhSachNopBai = $this->baiTapModel->getDanhSachNopBai($maBaiTap);

        if (empty($danhSachNopBai)) {
            $_SESSION['error'] = "Chưa có học sinh nào nộp bài!";
            header("Location: index.php?controller=baitap&action=chitiet&maBaiTap=" . $maBaiTap);
            exit;
        }

        $zip = new ZipArchive();
        $zipFileName = tempnam(sys_get_temp_dir(), 'baitap_') . '.zip'; 

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            $_SESSION['error'] = "Không thể tạo file nén (Lỗi ZipArchive)!";
            header("Location: index.php?controller=baitap&action=chitiet&maBaiTap=" . $maBaiTap);
            exit;
        }

        foreach ($danhSachNopBai as $baiNop) {
            $tenHocSinh = $baiNop['tenHocSinh'];
            
            $studentDir = $baiNop['maHocSinh'] . '_' . $this->sanitizeVietnameseString($tenHocSinh);

            $filesHS = json_decode($baiNop['fileDinhKem'], true);
            if (isset($filesHS['duongDan'])) { $filesHS = [$filesHS]; }

            if (is_array($filesHS)) {
                foreach ($filesHS as $fileInfo) {
                    if (isset($fileInfo['duongDan']) && file_exists($fileInfo['duongDan'])) {
                        $originalFileName = $fileInfo['tenFile'];
                        $filePathOnServer = $fileInfo['duongDan'];
                        
                        $zip->addFile($filePathOnServer, $studentDir . '/' . $originalFileName);
                    }
                }
            }
        }
        $zip->close();

        $sanitizedLop = $this->sanitizeVietnameseString($baiTap['tenLop']);
        $sanitizedMon = $this->sanitizeVietnameseString($baiTap['tenMonHoc']);
        $sanitizedTenBT = $this->sanitizeVietnameseString($baiTap['tenBT']);

        $downloadFileName = $sanitizedLop . '_' . $sanitizedMon . '_' . $sanitizedTenBT . '.zip';

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $downloadFileName . '"');
        header('Content-Length: ' . filesize($zipFileName));
        header('Pragma: no-cache'); 
        header('Expires: 0');
        
        readfile($zipFileName);
        
        unlink($zipFileName);
        exit;
    }

    // Chuẩn hóa chuỗi tiếng Việt sang không dấu và an toàn cho tên file
    private function sanitizeVietnameseString($str) {
        $str = trim($str);
        
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
  
        $str = preg_replace('/[^A-Za-z0-9 ]/', '', $str); 
        
        $str = str_replace(' ', '_', $str);
      
        $str = preg_replace('/_+/', '_', $str);
        
        return $str;
    }

    // Xóa file đã nộp
    public function xoaFileNop() {
        $this->checkAuthAndGetMaHS();
        
        $maBaiTap = $_GET['maBaiTap'] ?? 0;
        $fileKey = $_GET['key'] ?? null; 

        if ($maBaiTap == 0 || $fileKey === null) {
            $_SESSION['error'] = "Yêu cầu không hợp lệ!";
            header("Location: index.php?controller=baitap&action=danhsach_hs");
            exit;
        }

        $baiTap = $this->baiTapModel->getBaiTapChiTiet($maBaiTap);
        if (!$baiTap) {
             $_SESSION['error'] = "Không tìm thấy bài tập!";
            header("Location: index.php?controller=baitap&action=danhsach_hs");
            exit;
        }
        
        $hanNopDate = new DateTime($baiTap['hanNop']);
        $now = new DateTime();
        
        $baiNop = $this->baiTapModel->getBaiNop($maBaiTap, $this->maHocSinh);
        
        if (!$baiNop || empty($baiNop['fileDinhKem'])) {
            $_SESSION['error'] = "Không tìm thấy bài nộp để xóa!";
            header("Location: index.php?controller=baitap&action=chitiet_hs&maBaiTap=" . $maBaiTap);
            exit;
        }

        $files = json_decode($baiNop['fileDinhKem'], true);
        if (!is_array($files) || !isset($files[$fileKey])) {
            $_SESSION['error'] = "Không tìm thấy file để xóa!";
            header("Location: index.php?controller=baitap&action=chitiet_hs&maBaiTap=" . $maBaiTap);
            exit;
        }
        
        $fileToRemove = $files[$fileKey];
        $filePath = $fileToRemove['duongDan'];
       
        unset($files[$fileKey]);
        
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $newFileDinhKemJSON = json_encode(array_values($files));
        
        $result = $this->baiTapModel->updateFileDinhKemBaiNop($baiNop['maBaiNop'], $newFileDinhKemJSON);

        if ($result) {
            $_SESSION['success'] = "Xóa file thành công!";
        } else {
            $_SESSION['error'] = "Lỗi CSDL khi xóa file!";
        }
        
        header("Location: index.php?controller=baitap&action=chitiet_hs&maBaiTap=" . $maBaiTap);
        exit;
    }
}
?>