<?php
require_once __DIR__ . '/../models/PhanCongRaDeModel.php';

class PhanCongRaDeController
{
    private $model;

    public function __construct()
    {
        $this->model = new PhanCongRaDeModel();
    }

    public function index()
    {
        $danhSachPhanCong = $this->model->getAllPhanCong();
        include __DIR__ . '/../views/phancongrade/index.php';
        exit();
    }

    public function create()
    {
        $danhSachKhoi = $this->model->getKhoi();
        $danhSachMonHoc = $this->model->getMonHoc();

        include __DIR__ . '/../views/phancongrade/create.php';
        exit();
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'tieuDe' => $_POST['tieuDe'] ?? '',
                'maKhoi' => $_POST['maKhoi'] ?? null,
                'maMonHoc' => $_POST['maMonHoc'] ?? null,
                'maGiaoVien' => $_POST['maGiaoVien'] ?? [], 
                'hanNopDe' => $_POST['hanNopDe'] ?? null,
                'soLuongDe' => $_POST['soLuongDe'] ?? 1,
                'noiDung' => $_POST['noiDung'] ?? '',
                'ghiChu' => $_POST['ghiChu'] ?? ''
            ];

            if (empty($data['maGiaoVien'])) {
                header('Location: index.php?controller=phancongrade&action=create&error=novp');
                exit;
            }

            $result = $this->model->createPhanCong($data);

            header('Location: index.php?controller=phancongrade');
            exit;
        }
    }

    public function edit()
    {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=phancongrade');
            exit;
        }

        $id = (int)$_GET['id'];
        $deThi = $this->model->getDeThiById($id);
        $danhSachKhoi = $this->model->getKhoi();
        $danhSachMonHoc = $this->model->getMonHoc();

        include __DIR__ . '/../views/phancongrade/edit.php';
        exit();
    }

    public function view($id = null)
    {
        // Lấy ID từ URL nếu không được truyền trực tiếp
        if ($id === null) {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        }
        
        if ($id <= 0) {
            header('Location: index.php?controller=phancongrade');
            exit;
        }
        
        // Lấy thông tin chi tiết đề thi
        $deThi = $this->model->getDeThiById($id);
        
        if (!$deThi) {
            $_SESSION['error_message'] = 'Không tìm thấy phân công!';
            header('Location: index.php?controller=phancongrade');
            exit;
        }
        
        // Lấy danh sách khoi, monhoc để hiển thị
        $danhSachKhoi = $this->model->getKhoi();
        $danhSachMonHoc = $this->model->getMonHoc();
        
        include __DIR__ . '/../views/phancongrade/view.php';
        exit();
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            if ($id > 0) {
                $result = $this->model->deletePhanCong($id);
                
                if ($result) {
                    $_SESSION['success_message'] = 'Xóa phân công thành công!';
                } else {
                    $_SESSION['error_message'] = 'Xóa phân công thất bại!';
                }
            }
            
            header('Location: index.php?controller=phancongrade');
            exit;
        }
        
        // Nếu không phải POST request, chuyển hướng về trang chủ
        header('Location: index.php?controller=phancongrade');
        exit;
    }

    public function getGiaoVienByMonHoc()
    {
        // Đặt header JSON đầu tiên
        header('Content-Type: application/json');
        
        try {
            if (!isset($_GET['id_monhoc'])) {
                echo json_encode(['error' => 'Thiếu tham số id_monhoc']);
                exit;
            }

            $id_monhoc = (int)$_GET['id_monhoc'];
            
            if ($id_monhoc <= 0) {
                echo json_encode(['error' => 'ID môn học không hợp lệ']);
                exit;
            }
            
            $danhSachGiaoVien = $this->model->getGiaoVienByMonHoc($id_monhoc);
            
            if ($danhSachGiaoVien === false) {
                echo json_encode(['error' => 'Lỗi truy vấn database']);
                exit;
            }
            
            echo json_encode($danhSachGiaoVien);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['error' => 'Lỗi server: ' . $e->getMessage()]);
            exit;
        }
    }
}