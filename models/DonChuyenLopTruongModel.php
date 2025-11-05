<?php
require_once __DIR__ . '/Database.php';

class DonChuyenLopTruongModel {
    protected $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        $this->conn->exec("SET NAMES 'utf8'");
    }

    public function getAllSchools() {
        $sql = "SELECT maTruong, tenTruong FROM truong ORDER BY tenTruong ASC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả đơn theo logic lọc 3 bước cho chuyển trường và 1 bước cho chuyển lớp
     */
    public function getAll($search = '', $maTruong = null, $loaiDon = 'tat_ca') {
        $sql = "SELECT d.maDon, d.lyDoChuyen, d.ngayGui, d.maHocSinh,
                        nd.hoTen AS tenHS,
                        d.maTruongHienTai, d.maTruongDen, d.maLopHienTai, d.maLopDen,
                        d.trangThaiTruongDi, d.trangThaiTruongDen, d.lyDoTuChoiTruongDi, d.lyDoTuChoiTruongDen,
                        d.trangThaiLop, d.lyDoTuChoiLop,
                        d.loaiDon,
                        COALESCE(t1.tenTruong, '') AS truongHienTai, COALESCE(t2.tenTruong, '') AS truongDen,
                        COALESCE(l1.tenLop, '') AS lopHienTai, COALESCE(l2.tenLop, '') AS lopDen
                    FROM donchuyenloptruong d
                    LEFT JOIN hocsinh h ON d.maHocSinh = h.maHocSinh
                    LEFT JOIN nguoidung nd ON h.maNguoiDung = nd.maNguoiDung
                    LEFT JOIN truong t1 ON d.maTruongHienTai = t1.maTruong
                    LEFT JOIN truong t2 ON d.maTruongDen = t2.maTruong
                    LEFT JOIN lophoc l1 ON d.maLopHienTai = l1.maLop
                    LEFT JOIN lophoc l2 ON d.maLopDen = l2.maLop
                    WHERE 1=1";

        $params = [];

        // 1. Lọc theo loại đơn
        if ($loaiDon !== 'tat_ca') {
            $sql .= " AND d.loaiDon = :loaiDon";
            $params[':loaiDon'] = $loaiDon;
        }

        // 2. Lọc theo Trường đang chọn và Logic duyệt
        if ($maTruong) {
            $sql .= " AND (
                -- Logic 3 bước CHO ĐƠN CHUYỂN TRƯỜNG:
                (d.loaiDon = 'chuyen_truong' AND (
                    -- B1: Trường đến thấy (Đang chờ duyệt ở Trường đến)
                    (d.maTruongDen = :maTruong AND d.trangThaiTruongDen = 'Chờ duyệt')
                    -- B2: Trường đi thấy (Đã duyệt ở Trường đến, Chờ duyệt ở Trường đi)
                    OR (d.maTruongHienTai = :maTruong AND d.trangThaiTruongDen = 'Đã duyệt' AND d.trangThaiTruongDi = 'Chờ duyệt')
                    -- B3: Trường đến HOẶC Trường đi thấy các đơn ĐÃ HOÀN TẤT/BỊ TỪ CHỐI
                    OR (d.maTruongDen = :maTruong AND d.trangThaiTruongDi IN ('Đã duyệt', 'Từ chối'))
                    OR (d.maTruongHienTai = :maTruong AND d.trangThaiTruongDi IN ('Đã duyệt', 'Từ chối'))
                    OR (d.maTruongHienTai = :maTruong AND d.trangThaiTruongDen = 'Từ chối')
                    OR (d.maTruongDen = :maTruong AND d.trangThaiTruongDen IN ('Đã duyệt', 'Từ chối') AND d.trangThaiTruongDi IN ('Đã duyệt', 'Từ chối'))
                ))
                -- Logic 1 bước CHO ĐƠN CHUYỂN LỚP:
                OR (d.loaiDon = 'chuyen_lop' AND d.maTruongHienTai = :maTruong_2)
            )";
            $params[':maTruong'] = $maTruong;
            $params[':maTruong_2'] = $maTruong;
        }

        // 3. Tìm kiếm
        if (!empty($search)) {
            $sql .= " AND (nd.hoTen LIKE :kw OR d.maDon LIKE :kw2)";
            $params[':kw'] = "%$search%";
            $params[':kw2'] = "%$search%";
        }

        $sql .= " ORDER BY d.ngayGui DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ******************************************************
        // LOGIC XÁC ĐỊNH TRẠNG THÁI TỔNG VÀ QUYỀN DUYỆT (CAN APPROVE & actionType)
        // ******************************************************
        foreach ($requests as &$don) {
            $maTruongHienTai = $don['maTruongHienTai'];
            $maTruongDen = $don['maTruongDen'];
            $currentSchoolId = $maTruong; 
            $don['canApprove'] = false;
            $don['actionType'] = 'full'; // Mặc định: Duyệt và Từ chối

            if ($don['loaiDon'] === 'chuyen_lop') {
                $status = $don['trangThaiLop'] ?? 'Chờ duyệt';
                
                if ($status === 'Từ chối') {
                    $don['trangThaiTong'] = 'Bị từ chối';
                } elseif ($status === 'Đã duyệt') {
                    $don['trangThaiTong'] = 'Hoàn tất';
                } else {
                    $don['trangThaiTong'] = 'Chờ duyệt';
                    if ($maTruongHienTai == $currentSchoolId) {
                        $don['canApprove'] = true;
                    }
                }
            } else { // Chuyển trường (LOGIC 3 BƯỚC)
                $statusDi = $don['trangThaiTruongDi'] ?? 'Chờ duyệt';
                $statusDen = $don['trangThaiTruongDen'] ?? 'Chờ duyệt';

                if ($statusDen === 'Từ chối' || $statusDi === 'Từ chối') {
                    $don['trangThaiTong'] = 'Bị từ chối';
                } elseif ($statusDi === 'Đã duyệt' && $statusDen === 'Đã duyệt') {
                    $don['trangThaiTong'] = 'Hoàn tất';
                } else {
                    $don['trangThaiTong'] = 'Chờ duyệt';
                    
                    // B1: Trường đến duyệt (Duyệt/Từ chối)
                    if ($statusDen === 'Chờ duyệt' && $maTruongDen == $currentSchoolId) {
                        $don['canApprove'] = true;
                        $don['actionType'] = 'full'; 
                    } 
                    // B2: Trường đi duyệt (CHỈ DUYỆT - Không có quyền từ chối)
                    elseif ($statusDen === 'Đã duyệt' && $statusDi === 'Chờ duyệt' && $maTruongHienTai == $currentSchoolId) {
                        $don['canApprove'] = true;
                        $don['actionType'] = 'approve_only'; // KHÔNG CÓ NÚT TỪ CHỐI
                    }
                }
            }
        }
        unset($don); 
        
        return $requests;
    }

    public function getById($maDon) {
        $sql = "SELECT 
                        d.*, 
                        h.maHocSinh, 
                        nd.hoTen AS tenHS,
                        
                        COALESCE(t1.tenTruong, '') AS truongHienTai, 
                        COALESCE(t2.tenTruong, '') AS truongDen,
                        COALESCE(l1.tenLop, '') AS lopHienTai, 
                        COALESCE(l2.tenLop, '') AS lopDen,

                        -- Thông tin LỚP VÀ GVCN
                        l1.maGiaoVien AS maGVCN_of_lop,    
                        nd_gv.hoTen AS tenGVCN,
                        nd_gv.soDienThoai AS sdtGVCN, 
                        nd_gv.email AS emailGVCN,

                        -- Thông tin PHỤ HUYNH
                        nd_ph.hoTen AS tenPhuHuynh,      
                        nd_ph.soDienThoai AS sdtPhuHuynh, 
                        nd_ph.email AS emailPhuHuynh,
                        ph.ngheNghiep,             
                        ph.moiQuanHe              
                    FROM donchuyenloptruong d
                    LEFT JOIN hocsinh h ON d.maHocSinh = h.maHocSinh
                    LEFT JOIN nguoidung nd ON h.maNguoiDung = nd.maNguoiDung
                    LEFT JOIN truong t1 ON d.maTruongHienTai = t1.maTruong
                    LEFT JOIN truong t2 ON d.maTruongDen = t2.maTruong
                    LEFT JOIN lophoc l1 ON d.maLopHienTai = l1.maLop
                    LEFT JOIN lophoc l2 ON d.maLopDen = l2.maLop
                    
                    -- JOIN lấy thông tin GVCN
                    LEFT JOIN giaovien gv ON l1.maGiaoVien = gv.maGiaoVien 
                    LEFT JOIN nguoidung nd_gv ON gv.maNguoiDung = nd_gv.maNguoiDung

                    -- JOIN lấy thông tin Phụ huynh
                    LEFT JOIN phuhuynh ph ON h.maPhuHuynh = ph.maPhuHuynh 
                    LEFT JOIN nguoidung nd_ph ON ph.maNguoiDung = nd_ph.maNguoiDung 
                    
                    WHERE d.maDon = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $maDon]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function approve($maDon, $side) {
        $now = date('Y-m-d H:i:s');
        
        if ($side === 'truongden') {
            $sql = "UPDATE donchuyenloptruong SET trangThaiTruongDen = 'Đã duyệt', ngayDuyetTruongDen = :now WHERE maDon = :id";
        } elseif ($side === 'truongdi') {
            $sql = "UPDATE donchuyenloptruong SET trangThaiTruongDi = 'Đã duyệt', ngayDuyetTruongDi = :now WHERE maDon = :id";
        } elseif ($side === 'lop') { // Xử lý đơn chuyển lớp (duyệt 1 lần)
            $sql = "UPDATE donchuyenloptruong 
                     SET trangThaiLop = 'Đã duyệt', ngayDuyetLop = :now 
                     WHERE maDon = :id";
        } else {
            return false;
        }
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':now' => $now, ':id' => $maDon]);
    }

    public function reject($maDon, $side, $reason) {
        $now = date('Y-m-d H:i:s');
        
        // 1. Lấy thông tin đơn để kiểm tra trạng thái
        // Lưu ý: Đảm bảo class Database và getById() đã được require/define
        $don = $this->getById($maDon);
        if (!$don) {
            return false;
        }
        
        // **********************************************
        // LOGIC CHẶN TỪ CHỐI: TRƯỜNG ĐI KHÔNG CÓ QUYỀN TỪ CHỐI NẾU TRƯỜNG ĐẾN ĐÃ DUYỆT
        // **********************************************
        if ($side === 'truongdi' && ($don['trangThaiTruongDen'] ?? 'Chờ duyệt') === 'Đã duyệt') {
            error_log("Cảnh báo: Trường đi cố gắng từ chối đơn #$maDon sau khi Trường đến đã duyệt. Thao tác bị chặn.");
            // Giả lập lỗi để Controller hiển thị thông báo "Lỗi khi từ chối đơn"
            return false; 
        }

        if ($side === 'truongdi') {
            $sql = "UPDATE donchuyenloptruong 
                     SET trangThaiTruongDi = 'Từ chối', lyDoTuChoiTruongDi = :reason, ngayDuyetTruongDi = :now 
                     WHERE maDon = :id";
        } elseif ($side === 'truongden') {
            $sql = "UPDATE donchuyenloptruong 
                     SET trangThaiTruongDen = 'Từ chối', lyDoTuChoiTruongDen = :reason, ngayDuyetTruongDen = :now 
                     WHERE maDon = :id";
        } elseif ($side === 'lop') { // Xử lý đơn chuyển lớp (từ chối 1 lần)
            $sql = "UPDATE donchuyenloptruong 
                     SET trangThaiLop = 'Từ chối', lyDoTuChoiLop = :reason, ngayDuyetLop = :now 
                     WHERE maDon = :id";
        } else {
            return false;
        }
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':reason' => $reason, ':now' => $now, ':id' => $maDon]);
    }
}