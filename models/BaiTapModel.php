<?php
require_once 'models/Database.php';

class BaiTapModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy danh sách các lớp và môn học mà giáo viên được phân công.
    public function getLopVaMonHocGiaoVien($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT DISTINCT 
                    l.maLop, l.tenLop, 
                    mh.maMonHoc, mh.tenMonHoc
                FROM phanconggiangday pc
                JOIN lophoc l ON pc.maLop = l.maLop
                JOIN monhoc mh ON pc.maMonHoc = mh.maMonHoc
                WHERE pc.maGiaoVien = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Giao bài tập mới
    public function giaoBaiTap($maGV, $maLop, $maMonHoc, $tenBT, $moTa, $hanNop, $fileDinhKemJSON) {
        $conn = $this->db->getConnection();
        
        $sql = "INSERT INTO baitap (tenBT, moTa, ngayGiao, hanNop, fileDinhKem, maLop, maGV, maMonHoc)
                VALUES (:tenBT, :moTa, NOW(), :hanNop, :fileDinhKem, :maLop, :maGV, :maMonHoc)";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'tenBT' => $tenBT,
                'moTa' => $moTa,
                'hanNop' => $hanNop,
                'fileDinhKem' => $fileDinhKemJSON,
                'maLop' => $maLop,
                'maGV' => $maGV,
                'maMonHoc' => $maMonHoc
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Lỗi CSDL khi giao bài tập: " . $e->getMessage());
            return false;
        }
    }
    
    // Lấy danh sách bài tập đã giao
    public function getDanhSachBaiTapDaGiao($maGiaoVien) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    bt.*, 
                    l.tenLop, 
                    m.tenMonHoc
                FROM baitap bt
                JOIN lophoc l ON bt.maLop = l.maLop
                JOIN monhoc m ON bt.maMonHoc = m.maMonHoc
                WHERE bt.maGV = :maGV
                ORDER BY bt.ngayGiao DESC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['maGV' => $maGiaoVien]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Lỗi CSDL khi lấy ds bài tập: " . $e->getMessage());
            return [];
        }
    }

    // Lấy chi tiết một bài tập
    public function getBaiTapChiTiet($maBaiTap) {
        $conn = $this->db->getConnection();
        
        // Lấy chi tiết bài tập, join thêm tên giáo viên
        $sql = "SELECT 
                    bt.*, 
                    l.tenLop, 
                    m.tenMonHoc,
                    nd.hoTen as tenGiaoVien
                FROM baitap bt
                JOIN lophoc l ON bt.maLop = l.maLop
                JOIN monhoc m ON bt.maMonHoc = m.maMonHoc
                JOIN giaovien gv ON bt.maGV = gv.maGiaoVien
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE bt.maBaiTap = :maBaiTap";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['maBaiTap' => $maBaiTap]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Lỗi CSDL khi lấy chi tiết bài tập: " . $e->getMessage());
            return false;
        }
    }

}
?>