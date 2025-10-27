<?php
require_once __DIR__ . '/../models/DonChuyenLopTruongModel.php';

class DonChuyenLopTruongController {
    protected $model;

    public function __construct() {
        $this->model = new DonChuyenLopTruongModel();
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public function index() {
        header('Location: index.php?controller=donchuyenloptruong&action=danhsach');
        exit;
    }

    public function danhsach() {
        if (isset($_GET['school']) && is_numeric($_GET['school'])) {
            $_SESSION['maTruong'] = intval($_GET['school']);
        }

        $maTruong = $_SESSION['maTruong'] ?? null;
        $search = $_GET['search'] ?? '';
        $loaiDonUrl = $_GET['loaiDon'] ?? 'tat_ca'; 
        $loaiDon = $loaiDonUrl === 'truong' ? 'chuyen_truong' : ($loaiDonUrl === 'lop' ? 'chuyen_lop' : 'tat_ca');
        
        if (!$maTruong) {
            $requests = [];
        } else {
            $requests = $this->model->getAll($search, $maTruong, $loaiDon);

            // Xử lý trạng thái tổng hợp
            foreach ($requests as &$r) {
                $type = $r['loaiDon'] ?? 'chuyen_truong'; 

                if ($type === 'chuyen_truong') {
                    // Logic cho Chuyển trường (3 bước duyệt)
                    $den = $r['trangThaiTruongDen'] ?? '';
                    $di  = $r['trangThaiTruongDi'] ?? '';

                    if ($den === 'Đã duyệt' && $di === 'Đã duyệt') {
                        $r['trangThaiTong'] = 'Hoàn tất';
                    } elseif ($den === 'Từ chối' || $di === 'Từ chối') {
                        $r['trangThaiTong'] = 'Bị từ chối';
                    } elseif ($den === 'Chờ duyệt' || ($den === 'Đã duyệt' && $di === 'Chờ duyệt')) {
                        $r['trangThaiTong'] = 'Chờ duyệt';
                    } else {
                        $r['trangThaiTong'] = 'Không xác định';
                    }
                } else { // chuyen_lop (1 bước duyệt)
                    $lopStatus = $r['trangThaiLop'] ?? ''; 

                    if ($lopStatus === 'Đã duyệt') {
                        $r['trangThaiTong'] = 'Hoàn tất';
                    } elseif ($lopStatus === 'Từ chối') {
                        $r['trangThaiTong'] = 'Bị từ chối';
                    } elseif ($lopStatus === 'Chờ duyệt') {
                        $r['trangThaiTong'] = 'Chờ duyệt';
                    } else {
                        $r['trangThaiTong'] = 'Không xác định';
                    }
                }
            }
            unset($r); 
        }

        $schools = $this->model->getAllSchools();
        require_once __DIR__ . '/../views/donchuyenloptruong/danhsachdon.php';
    }

    public function approve() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=donchuyenloptruong&action=danhsach'); 
            exit;
        }

        $maDon = intval($_POST['maDon'] ?? 0);
        $side  = $_POST['side'] ?? ''; // truongden, truongdi, lop
        $maTruong = $_SESSION['maTruong'] ?? null;

        if ($maDon <= 0 || !$side) {
            header('Location: ?controller=donchuyenloptruong&action=danhsach'); 
            exit;
        }

        $this->model->approve($maDon, $side);
        
        $qs = $maTruong ? '&school=' . $maTruong : '';
        header("Location: ?controller=donchuyenloptruong&action=danhsach$qs");
        exit;
    }

    public function reject() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=donchuyenloptruong&action=danhsach'); 
            exit;
        }

        $maDon = intval($_POST['maDon'] ?? 0);
        $side = $_POST['side'] ?? ''; // truongden, truongdi, lop
        $reason = trim($_POST['reason'] ?? '');
        $maTruong = $_SESSION['maTruong'] ?? null;

        if ($maDon <= 0 || !$side || $reason === '') {
            header("Location: ?controller=donchuyenloptruong&action=danhsach"); 
            exit;
        }

        $this->model->reject($maDon, $side, $reason);
        
        $qs = $maTruong ? '&school=' . $maTruong : '';
        header("Location: ?controller=donchuyenloptruong&action=danhsach$qs");
        exit;
    }

    public function ajax_chitiet() {
        // Cố gắng bắt lỗi để trả về JSON thay vì HTML
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        
        header('Content-Type: application/json');

        try {
            $id = intval($_GET['id'] ?? 0);
            
            if ($id <= 0) { 
                echo json_encode(['error' => 'ID không hợp lệ']); 
                exit; 
            }
            
            $don = $this->model->getById($id);
            
            if (!$don) { 
                echo json_encode(['error' => 'Không tìm thấy đơn']); 
                exit; 
            }
            
            $don['loaiDon'] = $don['loaiDon'] ?? 'chuyen_truong';
            
            echo json_encode($don);
            exit; 
            
        } catch (\PDOException $e) {
            // Xử lý lỗi CSDL
            echo json_encode(['error' => 'Lỗi CSDL (PDO): ' . $e->getMessage()]);
            exit;
        } catch (\Exception $e) {
            // Xử lý lỗi PHP chung
            echo json_encode(['error' => 'Lỗi máy chủ: ' . $e->getMessage()]);
            exit;
        }
    }
}