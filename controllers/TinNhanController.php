<?php
require_once "models/TinNhanModel.php";
require_once "models/LopModel.php";

class TinNhanController {
    private $model;
    private $db;

    public function __construct() {
        require_once "models/Database.php";
        $this->db = (new Database())->getConnection();
        $this->model = new TinNhanModel($this->db);
    }

    // === Trang danh sách tin nhắn / cuộc hội thoại ===
    public function index() {
        if (empty($_SESSION['user']['maNguoiDung'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $dscuoc = $this->model->getDanhSachCuocHoiThoaiCuaNguoiDung($maNguoiDung);

        require_once 'views/layouts/header.php';
        require_once 'views/tinnhan/danhsachtinnhan.php';
        require_once 'views/layouts/footer.php';
    }

    // === Trang soạn tin nhắn ===
    public function gui() {
        if (empty($_SESSION['user']['maNguoiDung'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $lopModel = new LopModel($this->db);
        $dsLop = $lopModel->getTatCaLop();

        require_once 'views/layouts/header.php';
        require_once 'views/tinnhan/guitinnhan.php';
        require_once 'views/layouts/footer.php';
    }

    // === Xử lý gửi tin nhắn ===
    public function guitin() {
        if (empty($_SESSION['user']['maNguoiDung'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $maNguoiGui = $_SESSION['user']['maNguoiDung'];
        $nguoinhan_values = $_POST['nguoinhan'] ?? []; // (Vd: ['hs_1', 'ph_1'])
        $tieuDe = trim($_POST['tieuDe'] ?? '');
        $noiDung = trim($_POST['noidung'] ?? '');
        $filePath = null; 
        $tenLop = $_POST['lop'] ?? '';

        // xử lý file đính kèm
        if (!empty($_FILES['dinhkem']['name'])) {
            $file = $_FILES['dinhkem'];
            $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed) || $file['size'] > 10 * 1024 * 1024) {
                $_SESSION['flash_error'] = "File không hợp lệ hoặc vượt quá 10MB.";
                header("Location: index.php?controller=tinnhan&action=gui");
                exit;
            }
            $uploadDir = "uploads/tinnhan/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($file['name']));
            $dest = $uploadDir . $filename;
            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $_SESSION['flash_error'] = "Không thể lưu file đính kèm.";
                header("Location: index.php?controller=tinnhan&action=gui");
                exit;
            }
            $filePath = $dest;
        }

        // ==== Kiểm tra người nhận ====
        if (empty($nguoinhan_values)) {
            $_SESSION['flash_error'] = "Vui lòng chọn ít nhất một người nhận.";
            header("Location: index.php?controller=tinnhan&action=gui");
            exit;
        }

        // ==== PHÂN TÁCH ID HỌC SINH VÀ PHỤ HUYNH ====
        $ds_hocsinh_ids = [];
        $ds_phuhuynh_ids = [];
        foreach ($nguoinhan_values as $value) {
            if (strpos($value, 'hs_') === 0) $ds_hocsinh_ids[] = (int)substr($value, 3);
            elseif (strpos($value, 'ph_') === 0) $ds_phuhuynh_ids[] = (int)substr($value, 3);
        }

        // === GỬI 1 TIN NHẮN NHÓM ===
        $tongSoNguoiNhan = count($ds_hocsinh_ids) + count($ds_phuhuynh_ids);
        $loaiHoiThoai = ($tongSoNguoiNhan > 1) ? 'NHOM' : 'DOI_TUONG';
        $tenHoiThoai = "";

        // 1. Xác định tên hội thoại
        if ($loaiHoiThoai === 'NHOM') {
            $tenHoiThoai = "Nhóm " . $tenLop;
        } else {
            // Nếu chỉ có 1 người, xác định là HS hay PH
            $tenVaiTro = !empty($ds_hocsinh_ids) ? "Học sinh" : "Phụ huynh";
            $tenHoiThoai = "Trao đổi Giáo viên - " . $tenVaiTro;
        }

        // 2. Tạo MỘT cuộc hội thoại duy nhất
        $maHoiThoai = $this->model->taoCuocHoiThoai($tenHoiThoai, $loaiHoiThoai, $maNguoiGui);

        // 3. Gửi MỘT tin nhắn duy nhất vào hội thoại đó
        $this->model->themTinNhan($maHoiThoai, $tieuDe, $noiDung, $maNguoiGui, $filePath);

        // 4. Thêm TẤT CẢ người dùng vào bảng trung gian (NGUOI DUNG HOI THOAI)
        
        // Chuẩn bị câu lệnh SQL
        $stmt_add_user = $this->db->prepare("INSERT INTO nguoidung_hoithoai (maHoiThoai, maNguoiDung) VALUES (:maHoiThoai, :maNguoiDung)");

        // Lấy maNguoiDung của học sinh
        $dsMaNguoiDung = $this->model->getMaNguoiDungTuHocSinh($ds_hocsinh_ids);
        // Lấy maNguoiDung của phụ huynh
        $dsMaNguoiDung = array_merge($dsMaNguoiDung, $this->model->getMaNguoiDungTuPhuHuynh($ds_phuhuynh_ids));
        
        // Thêm chính người gửi vào hội thoại
        $dsMaNguoiDung[] = $maNguoiGui; 
        
        // Xóa trùng lặp và thêm vào CSDL
        foreach (array_unique($dsMaNguoiDung) as $maND) {
            if ($maND) {
                $stmt_add_user->execute(['maHoiThoai' => $maHoiThoai, 'maNguoiDung' => $maND]);
            }
        }

        // Thông báo và chuyển hướng
        $_SESSION['flash_success'] = "Gửi tin nhắn thành công!";
        header("Location: index.php?controller=tinnhan&action=index");
        exit;
    }

    // === AJAX: Lấy danh sách học sinh / phụ huynh theo lớp ===
    public function ajaxLayDanhSachNguoiNhan() {
        header('Content-Type: application/json; charset=utf-8');
        $loai = $_GET['loai'] ?? 'hoc_sinh';
        $lop = $_GET['lop'] ?? '';
        $data = $this->model->getNguoiNhanTheoLop($loai, $lop);
        echo json_encode($data);
        exit;
    }

    // === AJAX: Lấy danh sách phụ huynh theo lớp học sinh ===
    public function ajaxLayPhuHuynhTheoLop() {
        header('Content-Type: application/json; charset=utf-8');
        $lop = $_GET['lop'] ?? '';
        $data = $this->model->getPhuHuynhTheoLop($lop);
        echo json_encode($data);
        exit;
    }

    // === AJAX fallback: Lấy danh sách lớp ===
    public function ajaxLayDanhSachLop() {
        header('Content-Type: application/json; charset=utf-8');
        $stmt = $this->db->query("SELECT maLop, tenLop FROM lophoc ORDER BY tenLop");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rows);
        exit;
    }

    // === Trang chi tiết cuộc hội thoại ===
    public function chitiet() {
        $maHoiThoai = intval($_GET['maHoiThoai'] ?? 0);
        if ($maHoiThoai <= 0) {
            header("Location: index.php?controller=tinnhan&action=index");
            exit;
        }

        $tinnhans = $this->model->getTinNhanTheoCuocHoiThoai($maHoiThoai);
        
        // === Lấy danh sách người nhận ===
        $dsNguoiNhan = $this->model->getNguoiNhanCuaHoiThoai($maHoiThoai);

        require_once 'views/layouts/header.php';
        require_once 'views/tinnhan/chitiettinnhan.php';
        require_once 'views/layouts/footer.php';
    }
}
?>
