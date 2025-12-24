<?php
require_once 'models/Database.php';

class ChuyenCanModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Lấy danh sách các buổi học mà giáo viên được phân công.
     */
    public function getBuoiHocGiaoVien($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    bh.maBuoiHoc, 
                    bh.maLop, 
                    l.tenLop, 
                    mh.tenMonHoc, 
                    bh.tietBatDau,
                    bh.tietKetThuc,
                    bh.ngayHoc,
                    bh.phongHoc,
                    bh.trangThai
                FROM buoihoc bh
                JOIN lophoc l ON bh.maLop = l.maLop
                JOIN monhoc mh ON bh.maMonHoc = mh.maMonHoc
                WHERE bh.maGiaoVien = ? 
                AND bh.trangThai IN ('DU_KIEN', 'DA_HOC')
                ORDER BY bh.ngayHoc DESC, l.tenLop, bh.tietBatDau";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // DEBUG
        error_log("Query buổi học cho GV $maGiaoVien: " . print_r($result, true));
        
        return $result;
    }

    // Lấy thông tin chi tiết của 1 buổi học
    public function getThongTinBuoiHoc($maBuoiHoc) {
        $conn = $this->db->getConnection();
        $sql = "SELECT 
                    l.tenLop, 
                    mh.tenMonHoc, 
                    bh.tietBatDau,
                    bh.tietKetThuc,
                    bh.ngayHoc,
                    bh.phongHoc,
                    nd.hoTen as tenGiaoVien
                FROM buoihoc bh
                JOIN lophoc l ON bh.maLop = l.maLop
                JOIN monhoc mh ON bh.maMonHoc = mh.maMonHoc
                JOIN giaovien gv ON bh.maGiaoVien = gv.maGiaoVien
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE bh.maBuoiHoc = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maBuoiHoc]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách học sinh và trạng thái chuyên cần (nếu có)
     * Dựa trên maLop, maBuoiHoc
     */
    public function getDanhSachLopDeDiemDanh($maLop, $maBuoiHoc) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    hs.maHocSinh,
                    nd.hoTen,
                    cc.trangThai,
                    cc.ghiChu
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                LEFT JOIN chuyencan cc ON hs.maHocSinh = cc.maHocSinh 
                                      AND cc.maBuoiHoc = :maBuoiHoc
                WHERE hs.maLop = :maLop AND hs.trangThai = 'DANG_HOC'
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'maBuoiHoc' => $maBuoiHoc,
            'maLop' => $maLop
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lưu (hoặc cập nhật) dữ liệu chuyên cần
    public function luuChuyenCan($maBuoiHoc, $danhSachTrangThai, $danhSachGhiChu) {
        $conn = $this->db->getConnection();
        
        $conn->beginTransaction();
        try {
            $sql = "INSERT INTO chuyencan (maHocSinh, maBuoiHoc, trangThai, ghiChu)
                    VALUES (:maHocSinh, :maBuoiHoc, :trangThai, :ghiChu)
                    ON DUPLICATE KEY UPDATE
                        trangThai = VALUES(trangThai),
                        ghiChu = VALUES(ghiChu),
                        updated_at = CURRENT_TIMESTAMP";
            
            $stmt = $conn->prepare($sql);

            foreach ($danhSachTrangThai as $maHocSinh => $trangThai) {
                // Chỉ lưu nếu GV đã chọn 1 trạng thái
                if (!empty($trangThai)) {
                    $ghiChu = $danhSachGhiChu[$maHocSinh] ?? null;
                    
                    $stmt->execute([
                        'maHocSinh' => $maHocSinh,
                        'maBuoiHoc' => $maBuoiHoc,
                        'trangThai' => $trangThai,
                        'ghiChu' => $ghiChu
                    ]);
                }
            }
            
            // Cập nhật trạng thái buổi học thành "DA_HOC"
            $sqlUpdateBuoiHoc = "UPDATE buoihoc SET trangThai = 'DA_HOC' WHERE maBuoiHoc = ?";
            $stmtUpdate = $conn->prepare($sqlUpdateBuoiHoc);
            $stmtUpdate->execute([$maBuoiHoc]);
            
            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Lỗi lưu chuyên cần: " . $e->getMessage());
            return false;
        }
    }

    public function kiemTraQuyenBuoiHoc($maBuoiHoc, $maGiaoVien) {
        $conn = $this->db->getConnection();
        $sql = "SELECT COUNT(*) FROM buoihoc WHERE maBuoiHoc = ? AND maGiaoVien = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maBuoiHoc, $maGiaoVien]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Tạo buổi học tự động từ thời khóa biểu
     */
    public function taoBuoiHocTuTKB($maNienKhoa, $ngayBatDau, $ngayKetThuc) {
        $conn = $this->db->getConnection();
        
        $conn->beginTransaction();
        try {
            // Map từ THU_X sang số thứ tự
            $thuMap = [
                'THU_2' => 1, 'THU_3' => 2, 'THU_4' => 3, 
                'THU_5' => 4, 'THU_6' => 5, 'THU_7' => 6, 'THU_8' => 0
            ];
            
            $currentDate = new DateTime($ngayBatDau);
            $endDate = new DateTime($ngayKetThuc);
            
            $sqlInsert = "INSERT INTO buoihoc (maThoiKhoaBieu, maLop, maMonHoc, maGiaoVien, maNienKhoa, ngayHoc, tietBatDau, tietKetThuc, phongHoc)
                         SELECT tkb.maThoiKhoaBieu, tkb.maLop, tkb.maMonHoc, tkb.maGiaoVien, tkb.maNienKhoa, ?, tkb.tietBatDau, tkb.tietKetThuc, tkb.phongHoc
                         FROM thoikhoabieu tkb
                         WHERE tkb.loaiLich = ? AND tkb.maNienKhoa = ?";
            
            $stmtInsert = $conn->prepare($sqlInsert);
            
            while ($currentDate <= $endDate) {
                $ngayHoc = $currentDate->format('Y-m-d');
                $thu = $currentDate->format('N'); // 1=Thứ 2, 7=Thứ 7, 0=Chủ nhật
                
                $loaiLich = 'THU_' . ($thu === '0' ? '8' : $thu + 1);
                
                $stmtInsert->execute([$ngayHoc, $loaiLich, $maNienKhoa]);
                
                $currentDate->modify('+1 day');
            }
            
            $conn->commit();
            return true;
            
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Lỗi tạo buổi học từ TKB: " . $e->getMessage());
            return false;
        }
    }
}
?>