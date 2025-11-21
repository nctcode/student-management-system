<?php
require_once __DIR__ . '/Database.php';
class PhanCongRaDeModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllPhanCong()
    {
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
                GROUP BY dt.maDeThi
                ORDER BY pc.hanNopDe DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGiaoVien()
    {
        $sql = "SELECT gv.maGiaoVien, nd.hoTen FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE nd.loaiNguoiDung = 'GIAOVIEN'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getKhoi()
    {
        $sql = "SELECT maKhoi, tenKhoi FROM khoi";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonHoc()
    {
        $sql = "SELECT maMonHoc, tenMonHoc FROM monhoc";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPhanCong($data)
    {
        try {
            $this->conn->beginTransaction();

            $sqlDeThi = "INSERT INTO dethi (tieuDe, maKhoi, maMonHoc, soLuongDe, noiDung, trangThai) 
                         VALUES (:tieuDe, :maKhoi, :maMonHoc, :soLuongDe, :noiDung, 'Chờ nộp')";

            $stmtDeThi = $this->conn->prepare($sqlDeThi);
            $stmtDeThi->execute([
                ':tieuDe' => $data['tieuDe'],
                ':maKhoi' => $data['maKhoi'],
                ':maMonHoc' => $data['maMonHoc'],
                ':soLuongDe' => $data['soLuongDe'],
                ':noiDung' => $data['noiDung']
            ]);

            $maDeThi = $this->conn->lastInsertId();

            $sqlPhanCong = "INSERT INTO phancongrade (maDeThi, maGiaoVien, hanNopDe, ghiChu) 
                            VALUES (:maDeThi, :maGiaoVien, :hanNopDe, :ghiChu)";

            $stmtPhanCong = $this->conn->prepare($sqlPhanCong);

            foreach ($data['maGiaoVien'] as $ma_gv) {
                $stmtPhanCong->execute([
                    ':maDeThi' => $maDeThi,
                    ':maGiaoVien' => $ma_gv,
                    ':hanNopDe' => $data['hanNopDe'],
                    ':ghiChu' => $data['ghiChu']
                ]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    public function getGiaoVienByMonHoc($id_monhoc)
    {
        $sql = "SELECT gv.maGiaoVien, nd.hoTen 
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN monhoc mh ON gv.chuyenMon = mh.tenMonHoc
                WHERE mh.maMonHoc = :id_monhoc";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_monhoc' => $id_monhoc]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDeThiById($id) {
        $sql = "SELECT 
                    dt.*, 
                    k.tenKhoi, 
                    mh.tenMonHoc, 
                    nd.hoTen AS tenGiaoVien, 
                    pc.hanNopDe,
                    pc.ghiChu
                FROM dethi dt
                LEFT JOIN phancongrade pc ON dt.maDeThi = pc.maDeThi
                LEFT JOIN khoi k ON dt.maKhoi = k.maKhoi
                LEFT JOIN monhoc mh ON dt.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON dt.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE dt.maDeThi = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTrangThaiDeThi($id, $trangThai) {
        $ngayDuyet = ($trangThai == 'DA_DUYET') ? date('Y-m-d H:i:s') : null;

        $sql = "UPDATE dethi 
                SET trangThai = :trangThai, ngayDuyet = :ngayDuyet 
                WHERE maDeThi = :id";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':trangThai' => $trangThai,
            ':ngayDuyet' => $ngayDuyet,
            ':id' => $id
        ]);
    }
}