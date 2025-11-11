<?php
require_once 'models/DiemModel.php';
require_once 'models/GiaoVienModel.php'; 
require_once 'models/HocSinhModel.php';

class DiemController {
    private $diemModel;
    private $giaoVienModel;
    private $hocSinhModel;

    public function __construct() {
        $this->diemModel = new DiemModel();
        $this->giaoVienModel = new GiaoVienModel(); 
        $this->hocSinhModel = new HocSinhModel();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    private function checkRole($allowedRoles = []) {
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if (!in_array($userRole, $allowedRoles)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
    }

    // Hiển thị trang Nhập điểm
    public function nhapdiem() {
        $this->checkAuth();
        $this->checkRole(['GIAOVIEN']);
        
        $giaoVienInfo = $this->giaoVienModel->getGiaoVienByMaNguoiDung($_SESSION['user']['maNguoiDung']);

        if (!$giaoVienInfo) {
            $_SESSION['error'] = "Tài khoản của bạn không được liên kết với một hồ sơ giáo viên!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }
        $maGiaoVien = $giaoVienInfo['maGiaoVien'];
        
        $danhSachPhanCong = $this->diemModel->getLopVaMonHocGiaoVien($maGiaoVien);
        
        $title = "Nhập điểm";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar/giaovien.php'; 
        require_once 'views/diem/nhapdiem.php'; 
        require_once 'views/layouts/footer.php';
    }

    // Xử lý AJAX để lấy bảng điểm
    public function ajaxGetBangDiem() {
        $this->checkAuth();
        
        $maLop = $_GET['maLop'] ?? 0;
        $maMonHoc = $_GET['maMonHoc'] ?? 0;
        $hocKy = $_GET['hocKy'] ?? 0;
        $namHoc = $_GET['namHoc'] ?? '';

        if (!$maLop || !$maMonHoc || !$hocKy || empty($namHoc)) {
            echo json_encode(['error' => 'Vui lòng chọn đầy đủ thông tin!']);
            exit;
        }

        $danhSachHocSinh = $this->diemModel->getDanhSachLopVaDiemHienTai($maLop, $maMonHoc, $hocKy, $namHoc);
        
        // Trả về dữ liệu dạng JSON
        header('Content-Type: application/json');
        echo json_encode($danhSachHocSinh);
        exit;
    }

    // Xử lý lưu điểm
    public function luu() {
        $this->checkAuth();
        $this->checkRole(['GIAOVIEN']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maLop = $_POST['maLop'];
            $maMonHoc = $_POST['maMonHoc'];
            $hocKy = $_POST['hocKy'];
            $namHoc = $_POST['namHoc'];
            $danhSachDiem = $_POST['diem']?? []; 
            
            $giaoVienInfo = $this->giaoVienModel->getGiaoVienByMaNguoiDung($_SESSION['user']['maNguoiDung']);
            if (!$giaoVienInfo) {
                $_SESSION['error'] = "Lỗi xác thực thông tin giáo viên!";
                header("Location: index.php?controller=diem&action=nhapdiem"); 
                exit;
            }
            $maGiaoVien = $giaoVienInfo['maGiaoVien'];

            $danhSachDiemDaChuanHoa = [];

            foreach ($danhSachDiem as $maHS => $cacLoaiDiem) {
                foreach ($cacLoaiDiem as $loaiDiem => $mangDiem) {
                    if (is_array($mangDiem)) {
                        foreach ($mangDiem as $key => $diemSo) {
                            $diemSo = str_replace(',', '.', $diemSo);

                            if ($diemSo !== '' && $diemSo !== null) {
                                // Kiểm tra ký tự không hợp lệ hoặc ngoài phạm vi
                                if (!is_numeric($diemSo) || $diemSo < 0 || $diemSo > 10) {
                                    $_SESSION['error'] = "Lỗi điểm không hợp lệ (mã HS: $maHS, loại: $loaiDiem). Vui lòng chỉ nhập số từ 0 đến 10!";
                                    header("Location: index.php?controller=diem&action=nhapdiem"); 
                                    exit;
                                }
                                $danhSachDiemDaChuanHoa[$maHS][$loaiDiem][] = $diemSo;
                            }
                        }
                    }
                }
            }

            if ($this->diemModel->luuBangDiem($maMonHoc, $maGiaoVien, $hocKy, $namHoc, $maLop, $danhSachDiemDaChuanHoa)) {
                $_SESSION['success'] = "Lưu điểm thành công!";
            } else {
                // Lỗi CSDL
                $_SESSION['error'] = "Lỗi không thể lưu điểm. Đã có lỗi xảy ra!";
            }

            $redirectUrl = sprintf(
                "Location: index.php?controller=diem&action=nhapdiem&maLop=%s&maMonHoc=%s&hocKy=%s&namHoc=%s&autoload=true",
                urlencode($maLop),
                urlencode($maMonHoc),
                urlencode($hocKy),
                urlencode($namHoc)
            );
            header($redirectUrl);
            exit;
        }
    }

    // Hiển thị trang xem điểm
    public function xemdiem() {
        $this->checkAuth();
        $this->checkRole(['HOCSINH', 'PHUHUYNH']);

        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        $danhSachCon = []; 
        $maHocSinhChon = null; 
        $hocSinhInfo = null;

        if ($userRole === 'HOCSINH') {
            $hocSinhInfo = $this->hocSinhModel->getHocSinhByNguoiDung($maNguoiDung);
            if ($hocSinhInfo) {
                $maHocSinhChon = $hocSinhInfo['maHocSinh'];
            }
        } elseif ($userRole === 'PHUHUYNH') {
            $danhSachCon = $this->hocSinhModel->getHocSinhByPhuHuynh($maNguoiDung);

            if (empty($danhSachCon)) {
                $_SESSION['error'] = "Tài khoản của bạn không được liên kết với hồ sơ học sinh nào!";
                header('Location: index.php?controller=home&action=index');
                exit;
            }

            $maHocSinhFromGet = $_GET['maHocSinh'] ?? null;
            
            if ($maHocSinhFromGet) {
                $valid = false;
                foreach ($danhSachCon as $con) {
                    if ($con['maHocSinh'] == $maHocSinhFromGet) {
                        $valid = true;
                        $hocSinhInfo = $con;
                        $maHocSinhChon = $con['maHocSinh'];
                        break;
                    }
                }
                if (!$valid) {
                    $_SESSION['error'] = "Lỗi bảo mật: Bạn không có quyền xem thông tin của học sinh này!";
                    header('Location: index.php?controller=home&action=index');
                    exit;
                }
            } else if (count($danhSachCon) === 1) {
                $hocSinhInfo = $danhSachCon[0];
                $maHocSinhChon = $hocSinhInfo['maHocSinh'];
            }
        }

        if (empty($maHocSinhChon) && $userRole === 'HOCSINH') {
             $_SESSION['error'] = "Tài khoản của bạn không được liên kết với một hồ sơ học sinh hợp lệ!";
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $danhSachKyHoc = [];
        $bangDiemData = []; 
        $viewMode = 'none'; // Trạng thái: none, single, all
        
        $namHocChon = $_GET['namHoc'] ?? null;
        $hocKyChon = $_GET['hocKy'] ?? null;

        if ($maHocSinhChon) {
            $danhSachKyHoc = $this->diemModel->getNamHocHocKyCuaHS($maHocSinhChon);

            if ($namHocChon === 'all') {
                $viewMode = 'all';
                foreach ($danhSachKyHoc as $ky) {
                    $result = $this->diemModel->getBangDiemHocSinh($maHocSinhChon, $ky['namHoc'], $ky['hocKy']);
                    $bangDiemData[ $ky['namHoc'] . '|' . $ky['hocKy'] ] = $result;
                }
            } else if ($namHocChon && $hocKyChon) {
                $viewMode = 'single';
                $result = $this->diemModel->getBangDiemHocSinh($maHocSinhChon, $namHocChon, $hocKyChon);
                $bangDiemData['single'] = $result;
            }
        }
        
        $title = "Xem điểm";
        $showSidebar = true; 
        
        require_once 'views/layouts/header.php';
        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        if ($userRole === 'GIAOVIEN') {
            require_once 'views/layouts/sidebar/giaovien.php';
        } elseif ($userRole === 'PHUHUYNH') {
            require_once 'views/layouts/sidebar/phuhuynh.php';
        } else {  
            require_once 'views/layouts/sidebar/hocsinh.php';
        }
        require_once 'views/diem/xemdiem.php'; 
        require_once 'views/layouts/footer.php';
    }


    // Xử lý tải bảng điểm (PDF) bằng mpdf
    public function taibangdiem() {
        require_once 'vendor/autoload.php';
        
        $this->checkAuth();
        $this->checkRole(['HOCSINH', 'PHUHUYNH']);

        $userRole = $_SESSION['user']['vaiTro'] ?? '';
        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        $hocSinhInfo = null;
        $maHocSinhChon = null;

        if ($userRole === 'HOCSINH') {
            $hocSinhInfo = $this->hocSinhModel->getHocSinhByNguoiDung($maNguoiDung);
            if ($hocSinhInfo) {
                $maHocSinhChon = $hocSinhInfo['maHocSinh'];
            }
        } elseif ($userRole === 'PHUHUYNH') {
            $danhSachCon = $this->hocSinhModel->getHocSinhByPhuHuynh($maNguoiDung);
            $maHocSinhFromGet = $_GET['maHocSinh'] ?? null;
            
            if ($maHocSinhFromGet) {
                $valid = false;
                foreach ($danhSachCon as $con) {
                    if ($con['maHocSinh'] == $maHocSinhFromGet) {
                        $valid = true;
                        $hocSinhInfo = $con; 
                        $maHocSinhChon = $con['maHocSinh'];
                        break;
                    }
                }
                if (!$valid) { exit("Lỗi bảo mật!"); }
            } else if (count($danhSachCon) === 1) {
                $hocSinhInfo = $danhSachCon[0];
                $maHocSinhChon = $hocSinhInfo['maHocSinh'];
            }
        }

        if (empty($maHocSinhChon)) {
            exit("Không tìm thấy học sinh!");
        }

        $bangDiemData = []; 
        $viewMode = 'none';
        $namHocChon = $_GET['namHoc'] ?? null;
        $hocKyChon = $_GET['hocKy'] ?? null;

        if ($maHocSinhChon) {
            if ($namHocChon === 'all') {
                $viewMode = 'all';
                $danhSachKyHoc = $this->diemModel->getNamHocHocKyCuaHS($maHocSinhChon);
                foreach ($danhSachKyHoc as $ky) {
                    $result = $this->diemModel->getBangDiemHocSinh($maHocSinhChon, $ky['namHoc'], $ky['hocKy']);
                    $bangDiemData[ $ky['namHoc'] . '|' . $ky['hocKy'] ] = $result;
                }
            } else if ($namHocChon && $hocKyChon) {
                $viewMode = 'single';
                $result = $this->diemModel->getBangDiemHocSinh($maHocSinhChon, $namHocChon, $hocKyChon);
                $bangDiemData['single'] = $result;
            }
        }
        
        $mpdf = new \Mpdf\Mpdf(['default_font' => 'dejavusans']);
        $mpdf->SetTitle("Bang diem - " . $hocSinhInfo['hoTen']);

        ob_start(); 
        
        require 'views/diem/pdf_template.php'; 
        
        $html = ob_get_clean(); 
        
        $mpdf->WriteHTML($html);
    
        $baseNameRaw = '';
        
        if ($namHocChon === 'all') {
            $baseNameRaw = 'BangDiem_' . $hocSinhInfo['hoTen'] . '_All';
        } else {
            $baseNameRaw = 'BangDiem_' . $hocSinhInfo['hoTen'] . '_' . $hocKyChon . '_' . $namHocChon;
        }
        $tenFile = $this->sanitizeVietnameseString($baseNameRaw) . '.pdf'; 

        $mpdf->Output($tenFile, 'D');
        exit;
    }

    // Hàm để làm sạch tên file
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
        
        $extension = '';
        if (substr(strtolower($str), -4) == '.pdf') {
            $extension = '.pdf';
            $str = substr($str, 0, -4);
        }

        $str = preg_replace('/[^A-Za-z0-9_ ]/', '', $str); 
        $str = str_replace(' ', '_', $str);
        $str = preg_replace('/_+/', '_', $str);
        
        return $str . $extension;
    }
}
?>