<?php
require_once 'models/Database.php';

class ChuyenCanModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Lấy danh sách các tiết học (buổi học) mà giáo viên được phân công.
     * Dựa trên bảng tiethoc và buoihoc.
     */
    public function getTietHocGiaoVien($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    t.maTietHoc, 
                    b.maLop, 
                    l.tenLop, 
                    m.tenMonHoc, 
                    b.tietHoc,
                    t.ngayHocTrongTuan 
                FROM tiethoc t
                JOIN buoihoc b ON t.maBuoiHoc = b.maBuoiHoc
                JOIN lophoc l ON b.maLop = l.maLop
                JOIN monhoc m ON t.maMonHoc = m.maMonHoc
                WHERE t.maGiaoVien = ?
                ORDER BY l.tenLop, t.ngayHocTrongTuan, b.tietHoc";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    // Lấy thông tin chi tiết của 1 tiết học (để hiển thị)
    public function getThongTinTietHoc($maTietHoc) {
        $conn = $this->db->getConnection();
        $sql = "SELECT 
                    l.tenLop, 
                    m.tenMonHoc, 
                    b.tietHoc,
                    t.ngayHocTrongTuan
                FROM tiethoc t
                JOIN buoihoc b ON t.maBuoiHoc = b.maBuoiHoc
                JOIN lophoc l ON b.maLop = l.maLop
                JOIN monhoc m ON t.maMonHoc = m.maMonHoc
                WHERE t.maTietHoc = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maTietHoc]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách học sinh và trạng thái chuyên cần (nếu có)
     * Dựa trên maLop, maTietHoc, và ngayHoc
     */
    public function getDanhSachLopDeDiemDanh($maLop, $maTietHoc, $ngayHoc) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    hs.maHocSinh,
                    nd.hoTen,
                    cc.trangThai,
                    cc.ghiChu
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                LEFT JOIN chuyencan cc ON hs.maHocSinh = cc.maHocSinh 
                                      AND cc.maTietHoc = :maTietHoc
                                      AND cc.ngayHoc = :ngayHoc
                WHERE hs.maLop = :maLop
                ORDER BY nd.hoTen";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'maTietHoc' => $maTietHoc,
            'ngayHoc' => $ngayHoc,
            'maLop' => $maLop
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lưu (hoặc cập nhật) dữ liệu chuyên cần
    public function luuChuyenCan($maTietHoc, $ngayHoc, $danhSachTrangThai, $danhSachGhiChu) {
        $conn = $this->db->getConnection();
        
        $conn->beginTransaction();
        try {
            $sql = "INSERT INTO chuyencan (maHocSinh, ngayHoc, maTietHoc, trangThai, ghiChu)
                    VALUES (:maHocSinh, :ngayHoc, :maTietHoc, :trangThai, :ghiChu)
                    ON DUPLICATE KEY UPDATE
                        trangThai = VALUES(trangThai),
                        ghiChu = VALUES(ghiChu)";
            
            $stmt = $conn->prepare($sql);

            foreach ($danhSachTrangThai as $maHocSinh => $trangThai) {
                // Chỉ lưu nếu GV đã chọn 1 trạng thái
                if (!empty($trangThai)) {
                    $ghiChu = $danhSachGhiChu[$maHocSinh] ?? null; // Lấy ghi chú
                    
                    $stmt->execute([
                        'maHocSinh' => $maHocSinh,
                        'ngayHoc' => $ngayHoc,
                        'maTietHoc' => $maTietHoc,
                        'trangThai' => $trangThai,
                        'ghiChu' => $ghiChu
                    ]);
                }
            }
            
            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Lỗi lưu chuyên cần: " . $e->getMessage());
            return false;
        }
    }
}
?>