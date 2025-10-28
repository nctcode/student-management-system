<?php
require_once __DIR__ . '/Database.php';
class PhanCongRaDeModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection(); 
    }

    public function getAllPhanCong() {
        $sql = "SELECT 
                    dt.maDeThi,
                    dt.tieuDe, 
                    k.tenKhoi, 
                    mh.tenMonHoc, 
                    nd.hoTen AS tenGiaoVien, 
                    pc.hanNopDe, 
                    dt.ngayNop, 
                    dt.trangThai
                FROM dethi dt
                JOIN phancongrade pc ON dt.maDeThi = pc.maDeThi
                LEFT JOIN khoi k ON dt.maKhoi = k.maKhoi
                LEFT JOIN monhoc mh ON dt.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON pc.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                ORDER BY pc.hanNopDe DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGiaoVien() {
        $sql = "SELECT gv.maGiaoVien, nd.hoTen FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE nd.loaiNguoiDung = 'GIAOVIEN'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getKhoi() {
        $sql = "SELECT maKhoi, tenKhoi FROM khoi";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonHoc() {
        $sql = "SELECT maMonHoc, tenMonHoc FROM monhoc";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPhanCong($data) {
        try {
            $this->conn->beginTransaction();

            $sqlDeThi = "INSERT INTO dethi (tieuDe, maKhoi, maMonHoc, soLuongDe, noiDung, trangThai, maGiaoVien) 
                         VALUES (:tieuDe, :maKhoi, :maMonHoc, :soLuongDe, :noiDung, 'Chờ nộp', :maGiaoVien)";
            
            $stmtDeThi = $this->conn->prepare($sqlDeThi);
            $stmtDeThi->execute([
                ':tieuDe' => $data['tieuDe'],
                ':maKhoi' => $data['maKhoi'],
                ':maMonHoc' => $data['maMonHoc'],
                ':soLuongDe' => $data['soLuongDe'],
                ':noiDung' => $data['noiDung'],
                ':maGiaoVien' => $data['maGiaoVien'] 
            ]);

            $maDeThi = $this->conn->lastInsertId();

            $sqlPhanCong = "INSERT INTO phancongrade (maDeThi, maGiaoVien, hanNopDe, ghiChu) 
                            VALUES (:maDeThi, :maGiaoVien, :hanNopDe, :ghiChu)";
            
            $stmtPhanCong = $this->conn->prepare($sqlPhanCong);
            $stmtPhanCong->execute([
                ':maDeThi' => $maDeThi,
                ':maGiaoVien' => $data['maGiaoVien'],
                ':hanNopDe' => $data['hanNopDe'],
                ':ghiChu' => $data['ghiChu']
            ]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
}
?>