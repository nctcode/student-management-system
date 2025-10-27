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
                    -- B3: Trường đến thấy lại (Hoàn tất hoặc Bị từ chối ở Trường đi/Đến)
                    OR (d.maTruongDen = :maTruong AND d.trangThaiTruongDi IN ('Đã duyệt', 'Từ chối'))
                    -- Trường đi thấy các đơn bị từ chối từ trường đến
                    OR (d.maTruongHienTai = :maTruong AND d.trangThaiTruongDen = 'Từ chối')
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                
                -- JOIN lấy thông tin GVCN (ĐÃ LOẠI BỎ LỌC LOẠI GIAO VIEN)
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