<?php
require_once 'Database.php';

class ThongKeModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // *******************************************************
    // 1. Thống kê Phân công (CẬP NHẬT: THÊM maTruong)
    // *******************************************************
    public function getThongKePhanCong($maKhoi = null, $maLop = null, $maTruong = null) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    l.maLop, 
                    l.tenLop, 
                    k.tenKhoi, 
                    (SELECT COUNT(maMonHoc) FROM monhoc) AS tongSoMon,
                    nd_gvcn.hoTen AS gvcnHoTen,
                    (SELECT COUNT(DISTINCT maMonHoc) 
                     FROM phanconggiangday pc 
                     WHERE pc.maLop = l.maLop) AS soMonDaPhanCong,
                    (SELECT COUNT(*) 
                     FROM hocsinh hs 
                     WHERE hs.maLop = l.maLop) AS tongSoHocSinh
                FROM lophoc l
                LEFT JOIN khoi k ON l.maKhoi = k.maKhoi
                LEFT JOIN giaovien gv_cn ON l.maGiaoVien = gv_cn.maGiaoVien
                LEFT JOIN nguoidung nd_gvcn ON gv_cn.maNguoiDung = nd_gvcn.maNguoiDung
                WHERE 1=1";
        
        $params = [];
        if ($maKhoi && $maKhoi !== 'all') { 
            $sql .= " AND l.maKhoi = :maKhoi"; 
            $params[':maKhoi'] = $maKhoi; 
        }
        if ($maLop && $maLop !== 'all') { 
            $sql .= " AND l.maLop = :maLop"; 
            $params[':maLop'] = $maLop; 
        }
        if ($maTruong) { 
            $sql .= " AND l.maTruong = :maTruong"; 
            $params[':maTruong'] = $maTruong; 
        }
        
        $sql .= " ORDER BY k.tenKhoi, l.tenLop";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as &$row) {
                $tongSoMon = $row['tongSoMon'] ?? 0;
                
                if ($tongSoMon > 0) {
                    $row['tyLePC'] = round(($row['soMonDaPhanCong'] / $tongSoMon) * 100, 0);
                    $row['trangThaiPC'] = $row['tyLePC'] == 100 ? 'Hoàn thành' : 'Chưa hoàn thành';
                } else {
                    $row['tyLePC'] = 0;
                    $row['trangThaiPC'] = 'Chưa thiết lập môn học trong hệ thống';
                }
            }
            return $results;
        } catch (PDOException $e) {
            error_log("Lỗi PDO (Phân công): " . $e->getMessage());
            return [];
        }
    }

    // *******************************************************
    // 2. Thống kê Học lực (CẬP NHẬT: THÊM maTruong)
    // *******************************************************
    public function getThongKeHocLuc($maKhoi = null, $maLop = null, $hocKy = 1, $maTruong = null) {
        $conn = $this->db->getConnection();
        
        $hocKyString = "HK" . $hocKy;

        $sql = "SELECT 
                    k.tenKhoi, l.tenLop, 
                    SUM(CASE WHEN kq.hocLuc = 'GIOI' THEN 1 ELSE 0 END) AS slGIOI,
                    SUM(CASE WHEN kq.hocLuc = 'KHA' THEN 1 ELSE 0 END) AS slKHA,
                    SUM(CASE WHEN kq.hocLuc = 'TRUNG_BINH' THEN 1 ELSE 0 END) AS slTB,
                    SUM(CASE WHEN kq.hocLuc = 'YEU' THEN 1 ELSE 0 END) AS slYEU,
                    COUNT(kq.maHocSinh) AS tongSoHocSinh
                FROM ketquahoctap kq
                JOIN hocsinh hs ON kq.maHocSinh = hs.maHocSinh
                JOIN lophoc l ON hs.maLop = l.maLop
                LEFT JOIN khoi k ON l.maKhoi = k.maKhoi
                WHERE kq.hocKy = :hocKyString";
        
        $params = [':hocKyString' => $hocKyString];
        if ($maKhoi && $maKhoi !== 'all') { 
            $sql .= " AND l.maKhoi = :maKhoi"; 
            $params[':maKhoi'] = $maKhoi; 
        }
        if ($maLop && $maLop !== 'all') { 
            $sql .= " AND l.maLop = :maLop"; 
            $params[':maLop'] = $maLop; 
        }
        if ($maTruong) { 
            $sql .= " AND l.maTruong = :maTruong"; 
            $params[':maTruong'] = $maTruong; 
        }
        
        $sql .= " GROUP BY l.maLop, l.tenLop, k.tenKhoi ORDER BY k.tenKhoi, l.tenLop";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as &$row) {
                $total = $row['tongSoHocSinh'];
                if ($total > 0) {
                    $row['tyLeGIOI'] = round(($row['slGIOI'] / $total) * 100, 1);
                    $row['tyLeKHA'] = round(($row['slKHA'] / $total) * 100, 1);
                    $row['tyLeTB'] = round(($row['slTB'] / $total) * 100, 1);
                    $row['tyLeYEU'] = round(($row['slYEU'] / $total) * 100, 1);
                } else {
                    $row['tyLeGIOI'] = $row['tyLeKHA'] = $row['tyLeTB'] = $row['tyLeYEU'] = 0;
                }
            }
            return $results;
        } catch (PDOException $e) {
            error_log("Lỗi lấy báo cáo học lực: " . $e->getMessage());
            return [];
        }
    }

    // *******************************************************
    // 3. Thống kê Chuyên cần (CẬP NHẬT: THÊM maTruong)
    // *******************************************************
    public function getThongKeChuyenCan($maKhoi = null, $maLop = null, $hocKy = 1, $maTruong = null) {
        $conn = $this->db->getConnection();
        
        $sql = "SELECT 
                    k.tenKhoi, l.tenLop, 
                    COUNT(DISTINCT hs.maHocSinh) AS tongSoHocSinh,
                    SUM(CASE WHEN cc.trangThai = 'Vang' THEN 1 ELSE 0 END) AS tongSoLanVang
                FROM hocsinh hs
                JOIN lophoc l ON hs.maLop = l.maLop
                LEFT JOIN khoi k ON l.maKhoi = k.maKhoi 
                LEFT JOIN chuyencan cc ON hs.maHocSinh = cc.maHocSinh
                WHERE 1=1"; 
                
        $params = [];
        if ($maKhoi && $maKhoi !== 'all') { 
            $sql .= " AND l.maKhoi = :maKhoi"; 
            $params[':maKhoi'] = $maKhoi; 
        }
        if ($maLop && $maLop !== 'all') { 
            $sql .= " AND l.maLop = :maLop"; 
            $params[':maLop'] = $maLop; 
        }
        if ($maTruong) { 
            $sql .= " AND l.maTruong = :maTruong"; 
            $params[':maTruong'] = $maTruong; 
        }
        
        $sql .= " GROUP BY l.maLop, l.tenLop, k.tenKhoi ORDER BY k.tenKhoi, l.tenLop";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as &$row) {
                if ($row['tongSoHocSinh'] > 0) {
                    $row['TBSoLanVang'] = round($row['tongSoLanVang'] / $row['tongSoHocSinh'], 2); 
                } else {
                    $row['TBSoLanVang'] = 0;
                }
            }
            return $results;
        } catch (PDOException $e) {
            error_log("Lỗi lấy báo cáo chuyên cần: " . $e->getMessage());
            return [];
        }
    }
}
?>