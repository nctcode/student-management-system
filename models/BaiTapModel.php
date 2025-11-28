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
    
    // Lấy danh sách bài tập cho học sinh và trạng thái nộp bài của học sinh đó.
    public function getDanhSachBaiTapChoHocSinh($maLop, $maHocSinh) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    bt.*, 
                    m.tenMonHoc,
                    gv_nd.hoTen AS tenGiaoVien,
                    bn.trangThai AS trangThaiNop,
                    bn.ngayNop
                FROM baitap bt
                JOIN monhoc m ON bt.maMonHoc = m.maMonHoc
                JOIN giaovien gv ON bt.maGV = gv.maGiaoVien
                JOIN nguoidung gv_nd ON gv.maNguoiDung = gv_nd.maNguoiDung
                LEFT JOIN bainop bn ON bt.maBaiTap = bn.maBaiTap AND bn.maHocSinh = :maHocSinh
                WHERE bt.maLop = :maLop
                ORDER BY bt.ngayGiao DESC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['maLop' => $maLop, 'maHocSinh' => $maHocSinh]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Lỗi CSDL khi lấy ds bài tập cho HS: " . $e->getMessage());
            return [];
        }
    }

    // Lấy chi tiết bài tập VÀ thông tin bài đã nộp (nếu có)
    public function getBaiTapChiTietChoHocSinh($maBaiTap, $maHocSinh) {
        $conn = $this->db->getConnection();
        
        $baiTap = $this->getBaiTapChiTiet($maBaiTap);
        if (!$baiTap) {
            return false;
        }

        $sql_nop = "SELECT * FROM bainop WHERE maBaiTap = :maBaiTap AND maHocSinh = :maHocSinh";
        $stmt_nop = $conn->prepare($sql_nop);
        $stmt_nop->execute(['maBaiTap' => $maBaiTap, 'maHocSinh' => $maHocSinh]);
        $baiNop = $stmt_nop->fetch(PDO::FETCH_ASSOC);
        
        $baiTap['baiNopCuaToi'] = $baiNop;

        return $baiTap;
    }

    // Thực hiện nộp bài
    public function nopBai($maBaiTap, $maHocSinh, $fileDinhKemJSON, $trangThai) {
        $conn = $this->db->getConnection();

        $sql = "INSERT INTO bainop (maBaiTap, maHocSinh, fileDinhKem, trangThai, ngayNop)
                VALUES (:maBaiTap, :maHocSinh, :fileDinhKem, :trangThai, NOW())
                ON DUPLICATE KEY UPDATE
                    fileDinhKem = :fileDinhKem,
                    trangThai = :trangThai,
                    ngayNop = NOW()";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'maBaiTap' => $maBaiTap,
                'maHocSinh' => $maHocSinh,
                'fileDinhKem' => $fileDinhKemJSON,
                'trangThai' => $trangThai
            ]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Lỗi CSDL khi nộp bài: " . $e->getMessage());
            return false;
        }
    }
    
    // Lấy danh sách học sinh đã nộp của một bài tập
    public function getDanhSachNopBai($maBaiTap) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    bn.*,
                    nd.hoTen AS tenHocSinh
                FROM bainop bn
                JOIN hocsinh hs ON bn.maHocSinh = hs.maHocSinh
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                WHERE bn.maBaiTap = :maBaiTap
                ORDER BY bn.ngayNop ASC";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['maBaiTap' => $maBaiTap]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Lỗi CSDL khi lấy ds nộp bài: " . $e->getMessage());
            return [];
        }
    }

    // Lấy thống kê nộp bài (Đã nộp, Nộp trễ)
    public function getThongKeNopBai($maBaiTap) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT trangThai, COUNT(*) as soLuong 
                FROM bainop 
                WHERE maBaiTap = ? 
                GROUP BY trangThai";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maBaiTap]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $thongKe = [
                'DaNop' => 0,
                'NopTre' => 0
            ];
            
            foreach ($results as $row) {
                if ($row['trangThai'] == 'Đã nộp') {
                    $thongKe['DaNop'] = (int)$row['soLuong'];
                } elseif ($row['trangThai'] == 'Nộp trễ') {
                    $thongKe['NopTre'] = (int)$row['soLuong'];
                }
            }
            return $thongKe;
            
        } catch (Exception $e) {
            error_log("Lỗi CSDL khi lấy thống kê nộp bài: " . $e->getMessage());
            return ['DaNop' => 0, 'NopTre' => 0];
        }
    }

    // Lấy thông tin bài nộp (nếu có)
    public function getBaiNop($maBaiTap, $maHocSinh) {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM bainop WHERE maBaiTap = :maBaiTap AND maHocSinh = :maHocSinh";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['maBaiTap' => $maBaiTap, 'maHocSinh' => $maHocSinh]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Lỗi CSDL khi lấy bài nộp: " . $e->getMessage());
            return false;
        }
    }
    
    // Cập nhật lại cột fileDinhKem cho một bài nộp (sau khi xóa file)
    public function updateFileDinhKemBaiNop($maBaiNop, $fileDinhKemJSON) {
        $conn = $this->db->getConnection();
        $sql = "UPDATE bainop SET fileDinhKem = :files WHERE maBaiNop = :maBaiNop";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'files' => $fileDinhKemJSON,
                'maBaiNop' => $maBaiNop
            ]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Lỗi CSDL khi cập nhật file nộp: " . $e->getMessage());
            return false;
        }
    }
}
?>