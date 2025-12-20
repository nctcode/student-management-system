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
        -- Logic 2 bước CHO ĐƠN CHUYỂN TRƯỜNG (ĐÃ SỬA: ĐẾN TRƯỚC -> ĐI SAU):
        (d.loaiDon = 'chuyen_truong' AND (
            -- B1: Trường ĐẾN thấy đơn đầu tiên (Khi cả 2 đều đang chờ)
            (d.maTruongDen = :maTruong AND d.trangThaiTruongDen = 'Chờ duyệt' AND d.trangThaiTruongDi = 'Chờ duyệt')
            
            -- B2: Trường ĐI thấy đơn sau (Khi trường ĐẾN đã duyệt, trường ĐI chưa duyệt)
            OR (d.maTruongHienTai = :maTruong AND d.trangThaiTruongDen = 'Đã duyệt' AND d.trangThaiTruongDi = 'Chờ duyệt')
            
            -- Cả 2 trường đều thấy đơn đã hoàn tất hoặc bị từ chối
            -- Trường Đi thấy lịch sử của mình HOẶC thấy đơn đã bị Trường Đến từ chối
            OR (d.maTruongHienTai = :maTruong AND (d.trangThaiTruongDi IN ('Đã duyệt', 'Từ chối') OR d.trangThaiTruongDen = 'Từ chối'))
            -- Trường Đến thấy lịch sử của mình
            OR (d.maTruongDen = :maTruong AND d.trangThaiTruongDen IN ('Đã duyệt', 'Từ chối'))
        ))
        -- Logic 1 bước CHO ĐƠN CHUYỂN LỚP (Giữ nguyên):
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
            } else { 
    // Chuyển trường (LOGIC 2 BƯỚC: TRƯỜNG ĐẾN DUYỆT TRƯỚC)
    $statusDi = $don['trangThaiTruongDi'] ?? 'Chờ duyệt';
    $statusDen = $don['trangThaiTruongDen'] ?? 'Chờ duyệt';

    if ($statusDen === 'Từ chối' || $statusDi === 'Từ chối') {
        $don['trangThaiTong'] = 'Bị từ chối';
    } elseif ($statusDi === 'Đã duyệt' && $statusDen === 'Đã duyệt') {
        $don['trangThaiTong'] = 'Hoàn tất';
    } else {
        $don['trangThaiTong'] = 'Chờ duyệt';
        
        // B1: Trường ĐẾN duyệt trước (Khi cả 2 đều là Chờ duyệt)
        if ($statusDen === 'Chờ duyệt' && $statusDi === 'Chờ duyệt' && $maTruongDen == $currentSchoolId) {
            $don['canApprove'] = true;
            $don['actionType'] = 'full'; 
        } 
        // B2: Trường ĐI duyệt sau (Khi trường ĐẾN đã duyệt)
        elseif ($statusDen === 'Đã duyệt' && $statusDi === 'Chờ duyệt' && $maTruongHienTai == $currentSchoolId) {
            $don['canApprove'] = true;
            $don['actionType'] = 'full';
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
        
        try {
            $this->conn->beginTransaction();
            
            // 1. Cập nhật trạng thái duyệt
            if ($side === 'truongden') {
                $sql = "UPDATE donchuyenloptruong SET trangThaiTruongDen = 'Đã duyệt', ngayDuyetTruongDen = :now WHERE maDon = :id";
            } elseif ($side === 'truongdi') {
                $sql = "UPDATE donchuyenloptruong SET trangThaiTruongDi = 'Đã duyệt', ngayDuyetTruongDi = :now WHERE maDon = :id";
            } elseif ($side === 'lop') {
                $sql = "UPDATE donchuyenloptruong SET trangThaiLop = 'Đã duyệt', ngayDuyetLop = :now WHERE maDon = :id";
            } else {
                $this->conn->rollBack();
                return false;
            }
            
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([':now' => $now, ':id' => $maDon]);
            
            if (!$result) {
                $this->conn->rollBack();
                error_log("ERROR: Failed to update approval status for maDon=$maDon, side=$side");
                return false;
            }
            
            error_log("DEBUG: Successfully updated approval status for maDon=$maDon, side=$side");
            
            // 2. Cập nhật thông tin học sinh (nếu cần)
            $updateStudentResult = $this->updateStudentInfo($maDon);
            if (!$updateStudentResult) {
                error_log("WARNING: Failed to update student info for maDon=$maDon, but approval was successful");
                // Vẫn commit vì duyệt đơn thành công, chỉ là cập nhật học sinh thất bại
            }
            
            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("ERROR in approve(): " . $e->getMessage());
            return false;
        }
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
        // Nếu là Trường ĐẾN (Bước 1): Không được sửa nếu Trường ĐI (Bước 2) đã có động thái khác "Chờ duyệt"
    if ($side === 'truongden' && ($don['trangThaiTruongDi'] ?? 'Chờ duyệt') !== 'Chờ duyệt') {
        error_log("Cảnh báo: Trường đến cố gắng từ chối đơn #$maDon sau khi Trường đi đã xử lý.");
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
    // Thêm các phương thức sau vào class DonChuyenLopTruongModel

    public function getByParentId($maPhuHuynh) {
        $sql = "SELECT d.maDon, d.lyDoChuyen, d.ngayGui, d.maHocSinh,
                        nd.hoTen AS tenHS,
                        d.maTruongHienTai, d.maTruongDen, d.maLopHienTai, d.maLopDen,
                        d.trangThaiTruongDi, d.trangThaiTruongDen, d.trangThaiLop,
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
                    WHERE h.maPhuHuynh = :maPhuHuynh
                    ORDER BY d.ngayGui DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maPhuHuynh' => $maPhuHuynh]);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Thêm trạng thái tổng
        foreach ($requests as &$don) {
            $don['trangThaiTong'] = $this->determineOverallStatus($don);
        }
        unset($don);
        
        return $requests;
    }

    public function getStudentsByParent($maPhuHuynh) {
        $sql = "SELECT h.maHocSinh, nd.hoTen, h.maLop, l.tenLop, t.maTruong, t.tenTruong
                FROM hocsinh h
                LEFT JOIN nguoidung nd ON h.maNguoiDung = nd.maNguoiDung
                LEFT JOIN lophoc l ON h.maLop = l.maLop
                LEFT JOIN truong t ON l.maTruong = t.maTruong
                WHERE h.maPhuHuynh = :maPhuHuynh 
                AND h.trangThai = 'dang_hoc'";  // CHỈ GIỮ LẠI trangThai CỦA hocsinh
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maPhuHuynh' => $maPhuHuynh]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLopByTruong($maTruong = null) {
        // SỬA: Dùng namHoc thay vì tenNienKhoa
        $sql = "SELECT l.maLop, l.tenLop, k.tenKhoi, nk.namHoc, nk.hocKy
                FROM lophoc l
                LEFT JOIN khoi k ON l.maKhoi = k.maKhoi
                LEFT JOIN nienkhoa nk ON l.maNienKhoa = nk.maNienKhoa
                WHERE 1=1";
        
        if ($maTruong) {
            $sql .= " AND l.maTruong = :maTruong";
        }
        
        $sql .= " ORDER BY l.tenLop ASC";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($maTruong) {
            $stmt->execute([':maTruong' => $maTruong]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createDon($maHocSinh, $loaiDon, $lyDoChuyen, $maTruongDen = null, $maLopDen = null) {
        // Lấy thông tin học sinh hiện tại
        $studentInfo = $this->getStudentInfo($maHocSinh);
        if (!$studentInfo) {
            return false;
        }
        
        $sql = "INSERT INTO donchuyenloptruong 
                (maHocSinh, loaiDon, lyDoChuyen, maTruongHienTai, maLopHienTai, maTruongDen, maLopDen, ngayGui) 
                VALUES 
                (:maHocSinh, :loaiDon, :lyDoChuyen, :maTruongHienTai, :maLopHienTai, :maTruongDen, :maLopDen, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute([
            ':maHocSinh' => $maHocSinh,
            ':loaiDon' => $loaiDon,
            ':lyDoChuyen' => $lyDoChuyen,
            ':maTruongHienTai' => $studentInfo['maTruong'],
            ':maLopHienTai' => $studentInfo['maLop'],
            ':maTruongDen' => $maTruongDen,
            ':maLopDen' => $maLopDen
        ]);
    }

    public function getByIdAndParent($maDon, $maPhuHuynh) {
        $sql = "SELECT d.*, 
                        h.maHocSinh, 
                        nd.hoTen AS tenHS,
                        COALESCE(t1.tenTruong, '') AS truongHienTai, 
                        COALESCE(t2.tenTruong, '') AS truongDen,
                        COALESCE(l1.tenLop, '') AS lopHienTai, 
                        COALESCE(l2.tenLop, '') AS lopDen
                FROM donchuyenloptruong d
                LEFT JOIN hocsinh h ON d.maHocSinh = h.maHocSinh
                LEFT JOIN nguoidung nd ON h.maNguoiDung = nd.maNguoiDung
                LEFT JOIN truong t1 ON d.maTruongHienTai = t1.maTruong
                LEFT JOIN truong t2 ON d.maTruongDen = t2.maTruong
                LEFT JOIN lophoc l1 ON d.maLopHienTai = l1.maLop
                LEFT JOIN lophoc l2 ON d.maLopDen = l2.maLop
                WHERE d.maDon = :maDon AND h.maPhuHuynh = :maPhuHuynh";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maDon' => $maDon, ':maPhuHuynh' => $maPhuHuynh]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStudentInfo($maHocSinh) {
        $sql = "SELECT h.maLop, l.maTruong 
                FROM hocsinh h
                LEFT JOIN lophoc l ON h.maLop = l.maLop
                WHERE h.maHocSinh = :maHocSinh";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maHocSinh' => $maHocSinh]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function determineOverallStatus($don) {
        if ($don['loaiDon'] === 'chuyen_lop') {
            $status = $don['trangThaiLop'] ?? 'Chờ duyệt';
            return match($status) {
                'Từ chối' => 'Bị từ chối',
                'Đã duyệt' => 'Hoàn tất',
                default => 'Chờ duyệt'
            };
        } else {
            $statusDi = $don['trangThaiTruongDi'] ?? 'Chờ duyệt';
            $statusDen = $don['trangThaiTruongDen'] ?? 'Chờ duyệt';
            
            if ($statusDen === 'Từ chối' || $statusDi === 'Từ chối') {
                return 'Bị từ chối';
            } elseif ($statusDi === 'Đã duyệt' && $statusDen === 'Đã duyệt') {
                return 'Hoàn tất';
            } else {
                return 'Chờ duyệt';
            }
        }
    }
    public function getMaPhuHuynhByMaNguoiDung($maNguoiDung) {
        $sql = "SELECT maPhuHuynh FROM phuhuynh WHERE maNguoiDung = :maNguoiDung";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maNguoiDung' => $maNguoiDung]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['maPhuHuynh'] : null;
    }
    // Thêm vào class DonChuyenLopTruongModel
    public function updateStudentInfo($maDon) {
        try {
            // Lấy thông tin đơn
            $sql = "SELECT d.*, h.maLop as currentMaLop, h.maNguoiDung, nd.maTruong as currentMaTruong
                    FROM donchuyenloptruong d
                    JOIN hocsinh h ON d.maHocSinh = h.maHocSinh
                    JOIN nguoidung nd ON h.maNguoiDung = nd.maNguoiDung
                    WHERE d.maDon = :maDon";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':maDon' => $maDon]);
            $don = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$don) {
                error_log("ERROR: Cannot find don with maDon=$maDon");
                return false;
            }
            
            $maHocSinh = $don['maHocSinh'];
            $maNguoiDung = $don['maNguoiDung'];
            $loaiDon = $don['loaiDon'];
            
            error_log("DEBUG updateStudentInfo: maDon=$maDon, loaiDon=$loaiDon, maHocSinh=$maHocSinh, maNguoiDung=$maNguoiDung");
            
            if ($loaiDon === 'chuyen_lop') {
                // Đơn chuyển lớp - chỉ cập nhật lớp
                if ($don['trangThaiLop'] === 'Đã duyệt' && $don['maLopDen']) {
                    $sqlUpdate = "UPDATE hocsinh SET maLop = :maLopDen WHERE maHocSinh = :maHocSinh";
                    $stmtUpdate = $this->conn->prepare($sqlUpdate);
                    $result = $stmtUpdate->execute([
                        ':maLopDen' => $don['maLopDen'],
                        ':maHocSinh' => $maHocSinh
                    ]);
                    error_log("DEBUG: Updated class for student $maHocSinh to class {$don['maLopDen']}, result: " . ($result ? 'SUCCESS' : 'FAILED'));
                    return $result;
                }
            } else {
                // Đơn chuyển trường - chỉ cập nhật khi CẢ HAI đã duyệt
                if ($don['trangThaiTruongDi'] === 'Đã duyệt' && $don['trangThaiTruongDen'] === 'Đã duyệt') {
                    
                    // 1. CẬP NHẬT TRƯỜNG TRONG BẢNG NGUOIDUNG
                    if ($don['maTruongDen'] && $maNguoiDung) {
                        $sqlUpdateNguoiDung = "UPDATE nguoidung SET maTruong = :maTruongDen WHERE maNguoiDung = :maNguoiDung";
                        $stmtUpdateNguoiDung = $this->conn->prepare($sqlUpdateNguoiDung);
                        $resultNguoiDung = $stmtUpdateNguoiDung->execute([
                            ':maTruongDen' => $don['maTruongDen'],
                            ':maNguoiDung' => $maNguoiDung
                        ]);
                        
                        error_log("DEBUG: Updated school in nguoidung for user $maNguoiDung to school {$don['maTruongDen']}, result: " . ($resultNguoiDung ? 'SUCCESS' : 'FAILED'));
                        
                        if (!$resultNguoiDung) {
                            return false; // Nếu cập nhật nguoidung thất bại thì rollback
                        }
                    }
                    
                    // 2. CẬP NHẬT LỚP TRONG BẢNG HOCSINH (nếu có lớp tương ứng)
                    if ($don['maLopHienTai'] && $don['maTruongDen']) {
                        // Tìm lớp tương ứng trong trường mới
                        $sqlFindClass = "SELECT l2.maLop 
                                    FROM lophoc l1 
                                    JOIN lophoc l2 ON l1.tenLop = l2.tenLop 
                                    WHERE l1.maLop = :maLopHienTai 
                                    AND l2.maTruong = :maTruongDen
                                    LIMIT 1";
                        $stmtFind = $this->conn->prepare($sqlFindClass);
                        $stmtFind->execute([
                            ':maLopHienTai' => $don['maLopHienTai'],
                            ':maTruongDen' => $don['maTruongDen']
                        ]);
                        $newClass = $stmtFind->fetch(PDO::FETCH_ASSOC);
                        
                        if ($newClass) {
                            $sqlUpdateHocSinh = "UPDATE hocsinh SET maLop = :maLopMoi WHERE maHocSinh = :maHocSinh";
                            $stmtUpdateHocSinh = $this->conn->prepare($sqlUpdateHocSinh);
                            $resultHocSinh = $stmtUpdateHocSinh->execute([
                                ':maLopMoi' => $newClass['maLop'],
                                ':maHocSinh' => $maHocSinh
                            ]);
                            error_log("DEBUG: Updated class for student $maHocSinh to class {$newClass['maLop']}, result: " . ($resultHocSinh ? 'SUCCESS' : 'FAILED'));
                        } else {
                            error_log("WARNING: No matching class found in new school for student $maHocSinh, keeping current class");
                            // Nếu không tìm thấy lớp tương ứng, có thể set maLop = NULL hoặc giữ nguyên
                            // Tùy thuộc vào logic nghiệp vụ của bạn
                        }
                    }
                    
                    return true; // Cập nhật nguoidung thành công
                    
                } else {
                    error_log("DEBUG: School transfer not completed yet - waiting for both approvals");
                    return true; // Chưa đến lúc cập nhật
                }
            }
            
            return true; // Trường hợp không cần cập nhật vẫn trả về true
            
        } catch (PDOException $e) {
            error_log("ERROR in updateStudentInfo: " . $e->getMessage());
            return false;
        }
    }
}