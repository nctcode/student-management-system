<?php
require_once __DIR__ . '/Database.php';

class ThongKeModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // --- HÀM HỖ TRỢ (Đã thêm maTruong) ---
    private function buildWhere(&$sql, &$params, $maKhoi, $maLop, $maTruong) {
        // Luôn lọc theo trường
        $sql .= " AND l.maTruong = :maTruong";
        $params[':maTruong'] = $maTruong;

        if ($maKhoi != 'all') { 
            $sql .= " AND l.maKhoi = :maKhoi"; 
            $params[':maKhoi'] = $maKhoi; 
        }
        if ($maLop != 'all') { 
            $sql .= " AND l.maLop = :maLop"; 
            $params[':maLop'] = $maLop; 
        }
    }

    public function getAllKhoi() { 
        try { 
            return $this->db->getConnection()->query("SELECT maKhoi, tenKhoi FROM khoi ORDER BY tenKhoi ASC")->fetchAll(PDO::FETCH_ASSOC); 
        } catch(Exception $e) { return []; } 
    }

    public function getAllLopWithKhoi($maTruong) { 
        try { 
            $stmt = $this->db->getConnection()->prepare("SELECT maLop, tenLop, maKhoi FROM lophoc WHERE maTruong = ? ORDER BY tenLop ASC");
            $stmt->execute([$maTruong]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch(Exception $e) { return []; } 
    }

    // 1. KPI (Đã lọc theo maTruong)
    public function getKPIs($maTruong) {
        $conn = $this->db->getConnection();
        $data = ['tongSoLop'=>0, 'lopCoGVCN'=>0, 'tongSoGV'=>0, 'canhBaoCount'=>0];
        try {
            // Tổng số lớp
            $stmt = $conn->prepare("SELECT COUNT(*) FROM lophoc WHERE maTruong = ?");
            $stmt->execute([$maTruong]);
            $data['tongSoLop'] = $stmt->fetchColumn();

            // Lớp có GVCN
            $stmt = $conn->prepare("SELECT COUNT(*) FROM lophoc WHERE maTruong = ? AND maGiaoVien > 0");
            $stmt->execute([$maTruong]);
            $data['lopCoGVCN'] = $stmt->fetchColumn();

            // Tổng số GV (JOIN qua nguoidung để lấy maTruong)
            $stmt = $conn->prepare("SELECT COUNT(*) FROM giaovien g JOIN nguoidung n ON g.maNguoiDung = n.maNguoiDung WHERE n.maTruong = ?");
            $stmt->execute([$maTruong]);
            $data['tongSoGV'] = $stmt->fetchColumn();

            // Cảnh báo (Học lực yếu/kém HOẶC Học phí quá hạn) - Lọc theo trường
            $sqlCanhBao = "
                SELECT COUNT(*) FROM ketquahoctap kq 
                JOIN hocsinh hs ON kq.maHocSinh = hs.maHocSinh 
                JOIN lophoc l ON hs.maLop = l.maLop 
                WHERE l.maTruong = ? AND kq.hocLuc IN ('YEU', 'KEM')
                UNION ALL 
                SELECT COUNT(*) FROM hocphi hp 
                JOIN hocsinh hs ON hp.maHocSinh = hs.maHocSinh 
                JOIN lophoc l ON hs.maLop = l.maLop 
                WHERE l.maTruong = ? AND hp.trangThai = 'QUA_HAN'
            ";
            $stmt = $conn->prepare($sqlCanhBao);
            $stmt->execute([$maTruong, $maTruong]);
            $data['canhBaoCount'] = array_sum($stmt->fetchAll(PDO::FETCH_COLUMN));

        } catch (Exception $e) {}
        return $data;
    }

    // 2. PHỔ ĐIỂM
    public function getPhoDiem($hocKy, $maKhoi, $maLop, $maTruong) {
        $conn = $this->db->getConnection();
        $params = [':hk' => "%$hocKy%"];
        $sql = "SELECT SUM(CASE WHEN diemTrungBinh < 3.5 THEN 1 ELSE 0 END) as Kem, 
                       SUM(CASE WHEN diemTrungBinh >= 3.5 AND diemTrungBinh < 5.0 THEN 1 ELSE 0 END) as Yeu, 
                       SUM(CASE WHEN diemTrungBinh >= 5.0 AND diemTrungBinh < 6.5 THEN 1 ELSE 0 END) as TB, 
                       SUM(CASE WHEN diemTrungBinh >= 6.5 AND diemTrungBinh < 8.0 THEN 1 ELSE 0 END) as Kha, 
                       SUM(CASE WHEN diemTrungBinh >= 8.0 THEN 1 ELSE 0 END) as Gioi 
                FROM ketquahoctap kq 
                JOIN hocsinh hs ON kq.maHocSinh=hs.maHocSinh 
                JOIN lophoc l ON hs.maLop=l.maLop 
                WHERE kq.hocKy LIKE :hk";
        
        $this->buildWhere($sql, $params, $maKhoi, $maLop, $maTruong);
        
        try { 
            $stmt = $conn->prepare($sql); 
            $stmt->execute($params); 
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['Kem'=>0,'Yeu'=>0,'TB'=>0,'Kha'=>0,'Gioi'=>0]; 
        } catch (Exception $e) { return ['Kem'=>0,'Yeu'=>0,'TB'=>0,'Kha'=>0,'Gioi'=>0]; }
    }

    // 3. SO SÁNH
    public function getSoSanhHocLuc($namHoc, $maKhoi, $maLop, $maTruong) {
        $conn = $this->db->getConnection(); 
        $params = [':namHoc' => $namHoc];
        $sql = "SELECT kq.hocLuc, 
                       SUM(CASE WHEN kq.hocKy='HK1' THEN 1 ELSE 0 END) as SL_HK1, 
                       SUM(CASE WHEN kq.hocKy='HK2' THEN 1 ELSE 0 END) as SL_HK2 
                FROM ketquahoctap kq 
                JOIN hocsinh hs ON kq.maHocSinh=hs.maHocSinh 
                JOIN lophoc l ON hs.maLop=l.maLop 
                WHERE kq.namHoc=:namHoc AND kq.hocLuc IS NOT NULL";
        
        $this->buildWhere($sql, $params, $maKhoi, $maLop, $maTruong);
        
        $sql .= " GROUP BY kq.hocLuc ORDER BY FIELD(kq.hocLuc, 'KEM', 'YEU', 'TRUNG_BINH', 'KHA', 'GIOI')";
        try { $stmt=$conn->prepare($sql); $stmt->execute($params); return $stmt->fetchAll(PDO::FETCH_ASSOC); } catch(Exception $e){return [];}
    }

    // 4. DỰ BÁO (Lọc theo maTruong)
    public function getDuBaoTotNghiep($hocKy, $maTruong) {
        try {
            $sql = "SELECT COUNT(*) as TongSo, 
                           SUM(CASE WHEN kq.diemTrungBinh >= 5.0 AND kq.hanhKiem != 'YEU' THEN 1 ELSE 0 END) as DuKienDau, 
                           SUM(CASE WHEN kq.diemTrungBinh < 5.0 OR kq.hanhKiem = 'YEU' THEN 1 ELSE 0 END) as NguyCoRot 
                    FROM ketquahoctap kq 
                    JOIN hocsinh hs ON kq.maHocSinh=hs.maHocSinh 
                    JOIN lophoc l ON hs.maLop=l.maLop 
                    JOIN khoi k ON l.maKhoi=k.maKhoi 
                    WHERE k.tenKhoi=12 AND kq.hocKy LIKE ? AND l.maTruong = ?";
            
            $stmt = $this->db->getConnection()->prepare($sql); 
            $stmt->execute(["%$hocKy%", $maTruong]);
            
            $r = $stmt->fetch(PDO::FETCH_ASSOC); 
            $r['TyLeDau'] = ($r['TongSo']>0)?round(($r['DuKienDau']/$r['TongSo'])*100,1):0; 
            return $r;
        } catch(Exception $e){return ['TongSo'=>0,'DuKienDau'=>0,'NguyCoRot'=>0,'TyLeDau'=>0];}
    }

    // 5. TÀI CHÍNH
    public function getTaiChinhOverview($hocKy, $maKhoi, $maLop, $maTruong) {
        $conn = $this->db->getConnection(); 
        $params = [':hk' => "%$hocKy%"];
        $join = " JOIN hocsinh hs ON hp.maHocSinh = hs.maHocSinh JOIN lophoc l ON hs.maLop = l.maLop ";
        
        $sql1 = "SELECT COALESCE(SUM(hp.soTien), 0) FROM hocphi hp $join WHERE hp.kyHoc LIKE :hk";
        $p1 = $params; 
        $this->buildWhere($sql1, $p1, $maKhoi, $maLop, $maTruong);
        
        $sql2 = "SELECT COALESCE(SUM(tt.soTien), 0) FROM thanhtoan tt JOIN hocphi hp ON tt.maHocPhi = hp.maHocPhi $join WHERE hp.kyHoc LIKE :hk AND tt.trangThai = 'THANH_CONG'";
        $p2 = $params; 
        $this->buildWhere($sql2, $p2, $maKhoi, $maLop, $maTruong);
        
        try {
            $stmt = $conn->prepare($sql1); $stmt->execute($p1); $phaiThu = $stmt->fetchColumn();
            $stmt = $conn->prepare($sql2); $stmt->execute($p2); $thucThu = $stmt->fetchColumn();
            return ['phaiThu' => $phaiThu, 'thucThu' => $thucThu, 'congNo' => max(0, $phaiThu - $thucThu), 'tyLe' => $phaiThu > 0 ? round(($thucThu/$phaiThu)*100, 1) : 0];
        } catch (Exception $e) { return ['phaiThu'=>0, 'thucThu'=>0, 'congNo'=>0, 'tyLe'=>0]; }
    }

    // 6. TẢI CÔNG VIỆC GV (Lọc theo maTruong của lớp học)
    public function getTaiCongViecGiaoVien($maTruong) {
        try { 
            $sql = "SELECT nd.hoTen, 
                           COUNT(pc.maLop) as soLopCN,
                           GROUP_CONCAT(l.tenLop SEPARATOR ', ') as danhSachLop
                    FROM phanconggiangday pc 
                    JOIN giaovien gv ON pc.maGiaoVien = gv.maGiaoVien 
                    JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                    JOIN lophoc l ON pc.maLop = l.maLop
                    WHERE pc.loaiPhanCong = 'GVCN' AND l.maTruong = ?
                    GROUP BY pc.maGiaoVien, nd.hoTen 
                    ORDER BY soLopCN DESC 
                    LIMIT 5";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$maTruong]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch(Exception $e){ return []; }
    }

    // 7. CÁC HÀM KHÁC
    public function getThongKeHanhKiem($hocKy, $maKhoi, $maLop, $maTruong) {
        $conn = $this->db->getConnection(); 
        $params = [':hk' => "%$hocKy%"];
        $sql = "SELECT SUM(CASE WHEN hanhKiem='TOT' THEN 1 ELSE 0 END) as Tot, 
                       SUM(CASE WHEN hanhKiem='KHA' THEN 1 ELSE 0 END) as Kha, 
                       SUM(CASE WHEN hanhKiem IN ('TB','TRUNGBINH') THEN 1 ELSE 0 END) as TB, 
                       SUM(CASE WHEN hanhKiem='YEU' THEN 1 ELSE 0 END) as Yeu 
                FROM ketquahoctap kq 
                JOIN hocsinh hs ON kq.maHocSinh=hs.maHocSinh 
                JOIN lophoc l ON hs.maLop=l.maLop 
                WHERE kq.hocKy LIKE :hk";
        
        $this->buildWhere($sql, $params, $maKhoi, $maLop, $maTruong);
        
        try { $stmt=$conn->prepare($sql); $stmt->execute($params); return $stmt->fetch(PDO::FETCH_ASSOC)?:['Tot'=>0,'Kha'=>0,'TB'=>0,'Yeu'=>0]; } catch(Exception $e){return[];}
    }

    public function getDoanhThuTheoThang($maTruong) { 
        try { 
            // Cần JOIN để lọc theo maTruong (thanhtoan -> hocphi -> hocsinh -> lophoc)
            $sql = "SELECT DATE_FORMAT(tt.ngayGiaoDich, '%m/%Y') as thang, SUM(tt.soTien) as doanhThu 
                    FROM thanhtoan tt
                    JOIN hocphi hp ON tt.maHocPhi = hp.maHocPhi
                    JOIN hocsinh hs ON hp.maHocSinh = hs.maHocSinh
                    JOIN lophoc l ON hs.maLop = l.maLop
                    WHERE tt.trangThai='THANH_CONG' AND l.maTruong = ?
                    GROUP BY DATE_FORMAT(tt.ngayGiaoDich, '%Y-%m') 
                    ORDER BY DATE_FORMAT(tt.ngayGiaoDich, '%Y-%m') ASC LIMIT 12";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$maTruong]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch(Exception $e){ return []; } 
    }

    public function getTopLopNoHocPhi($hk, $maTruong) { 
        try { 
            $sql = "SELECT l.tenLop, COUNT(hp.maHocPhi) as soHocSinhNo, SUM(hp.soTien) as tongNo 
                    FROM lophoc l 
                    JOIN hocsinh hs ON l.maLop=hs.maLop 
                    JOIN hocphi hp ON hs.maHocSinh=hp.maHocSinh 
                    WHERE hp.trangThai IN('CHUA_NOP','QUA_HAN') AND hp.kyHoc LIKE ? AND l.maTruong = ?
                    GROUP BY l.tenLop 
                    ORDER BY tongNo DESC LIMIT 5";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute(["%$hk%", $maTruong]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch(Exception $e){ return []; } 
    }

    public function getSiSoTrungBinh($maTruong) { 
        try { 
            $sql = "SELECT k.tenKhoi, COUNT(DISTINCT l.maLop) as tong_lop, COUNT(hs.maHocSinh) as tong_hs 
                    FROM khoi k 
                    LEFT JOIN lophoc l ON k.maKhoi=l.maKhoi AND l.maTruong = ?
                    LEFT JOIN hocsinh hs ON l.maLop=hs.maLop 
                    GROUP BY k.tenKhoi 
                    ORDER BY LENGTH(k.tenKhoi), k.tenKhoi";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$maTruong]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch(Exception $e){ return []; } 
    }

    public function getDataExport($type, $hocKy, $maKhoi, $maLop, $maTruong) {
        $conn = $this->db->getConnection(); 
        $params = [':hk' => "%$hocKy%"]; 
        $where = " WHERE 1=1 ";
        
        // Thêm lọc maTruong vào WHERE
        $where .= " AND l.maTruong = :maTruong ";
        $params[':maTruong'] = $maTruong;

        if ($maKhoi!='all') { $where.=" AND l.maKhoi=:mk"; $params[':mk']=$maKhoi; }
        if ($maLop!='all')  { $where.=" AND l.maLop=:ml"; $params[':ml']=$maLop; }
        
        if ($type == 'taiChinh') 
            $sql = "SELECT hs.maHocSinh, nd.hoTen, l.tenLop, hp.soTien, hp.trangThai FROM hocphi hp JOIN hocsinh hs ON hp.maHocSinh=hs.maHocSinh JOIN nguoidung nd ON hs.maNguoiDung=nd.maNguoiDung JOIN lophoc l ON hs.maLop=l.maLop $where AND hp.kyHoc LIKE :hk";
        else 
            $sql = "SELECT hs.maHocSinh, nd.hoTen, l.tenLop, kq.diemTrungBinh, kq.hocLuc, kq.hanhKiem FROM ketquahoctap kq JOIN hocsinh hs ON kq.maHocSinh=hs.maHocSinh JOIN nguoidung nd ON hs.maNguoiDung=nd.maNguoiDung JOIN lophoc l ON hs.maLop=l.maLop $where AND kq.hocKy LIKE :hk";
        
        $stmt = $conn->prepare($sql); 
        $stmt->execute($params); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}