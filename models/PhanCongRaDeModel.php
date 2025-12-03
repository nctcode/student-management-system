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
                    GROUP_CONCAT(DISTINCT nd.hoTen SEPARATOR ', ') AS tenGiaoVien,
                    MIN(pc.hanNopDe) as hanNopDe,
                    dt.ngayNop, 
                    dt.trangThai
                FROM dethi dt
                LEFT JOIN phancongrade pc ON dt.maDeThi = pc.maDeThi
                LEFT JOIN khoi k ON dt.maKhoi = k.maKhoi
                LEFT JOIN monhoc mh ON dt.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv ON pc.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE dt.trangThai IS NULL OR dt.trangThai != 'HUY'
                GROUP BY dt.maDeThi, dt.tieuDe, k.tenKhoi, mh.tenMonHoc, dt.ngayNop, dt.trangThai
                ORDER BY pc.hanNopDe DESC, dt.maDeThi DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGiaoVien()
    {
        $sql = "SELECT DISTINCT gv.maGiaoVien, nd.hoTen 
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE nd.loaiNguoiDung = 'GIAOVIEN'
                ORDER BY nd.hoTen";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getKhoi()
    {
        $sql = "SELECT maKhoi, tenKhoi FROM khoi ORDER BY tenKhoi";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonHoc()
    {
        $sql = "SELECT maMonHoc, tenMonHoc FROM monhoc ORDER BY tenMonHoc";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPhanCong($data)
    {
        try {
            $this->conn->beginTransaction();

            // Insert vào bảng dethi
            $sqlDeThi = "INSERT INTO dethi (tieuDe, maKhoi, maMonHoc, soLuongDe, noiDung, trangThai, maGiaoVien, maNienKhoa) 
                         VALUES (:tieuDe, :maKhoi, :maMonHoc, :soLuongDe, :noiDung, 'Chờ nộp', :maGiaoVien, :maNienKhoa)";

            $stmtDeThi = $this->conn->prepare($sqlDeThi);
            
            // Lấy năm học hiện tại
            $maNienKhoa = 1; // Mặc định hoặc lấy từ hệ thống
            
            // Sử dụng giáo viên đầu tiên làm người tạo
            $maGiaoVien = !empty($data['maGiaoVien']) ? $data['maGiaoVien'][0] : null;

            $stmtDeThi->execute([
                ':tieuDe' => $data['tieuDe'],
                ':maKhoi' => $data['maKhoi'],
                ':maMonHoc' => $data['maMonHoc'],
                ':soLuongDe' => $data['soLuongDe'],
                ':noiDung' => $data['noiDung'],
                ':maGiaoVien' => $maGiaoVien,
                ':maNienKhoa' => $maNienKhoa
            ]);

            $maDeThi = $this->conn->lastInsertId();

            // Insert vào bảng phancongrade cho từng giáo viên
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
            return $maDeThi;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi tạo phân công: " . $e->getMessage());
            return false;
        }
    }

    public function getGiaoVienByMonHoc($id_monhoc)
    {
        $sql = "SELECT DISTINCT gv.maGiaoVien, nd.hoTen 
                FROM giaovien gv
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                LEFT JOIN monhoc mh ON gv.maMonHoc = mh.maMonHoc
                WHERE mh.maMonHoc = :id_monhoc
                ORDER BY nd.hoTen";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_monhoc' => $id_monhoc]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDeThiById($id) {
        $sql = "SELECT 
                    dt.*, 
                    k.tenKhoi, 
                    mh.tenMonHoc,
                    GROUP_CONCAT(DISTINCT gv2.maGiaoVien) AS dsMaGiaoVien,
                    GROUP_CONCAT(DISTINCT nd2.hoTen SEPARATOR ', ') AS dsTenGiaoVien,
                    pc.hanNopDe,
                    pc.ghiChu
                FROM dethi dt
                LEFT JOIN phancongrade pc ON dt.maDeThi = pc.maDeThi
                LEFT JOIN khoi k ON dt.maKhoi = k.maKhoi
                LEFT JOIN monhoc mh ON dt.maMonHoc = mh.maMonHoc
                LEFT JOIN giaovien gv2 ON pc.maGiaoVien = gv2.maGiaoVien
                LEFT JOIN nguoidung nd2 ON gv2.maNguoiDung = nd2.maNguoiDung
                WHERE dt.maDeThi = :id
                GROUP BY dt.maDeThi";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePhanCong($id, $data) {
        try {
            $this->conn->beginTransaction();

            // Cập nhật bảng dethi
            $sqlDeThi = "UPDATE dethi 
                        SET tieuDe = :tieuDe,
                            maKhoi = :maKhoi,
                            maMonHoc = :maMonHoc,
                            soLuongDe = :soLuongDe,
                            noiDung = :noiDung
                        WHERE maDeThi = :id";
            
            $stmtDeThi = $this->conn->prepare($sqlDeThi);
            $stmtDeThi->execute([
                ':tieuDe' => $data['tieuDe'],
                ':maKhoi' => $data['maKhoi'],
                ':maMonHoc' => $data['maMonHoc'],
                ':soLuongDe' => $data['soLuongDe'],
                ':noiDung' => $data['noiDung'],
                ':id' => $id
            ]);

            // Xóa phân công cũ
            $sqlDelete = "DELETE FROM phancongrade WHERE maDeThi = :id";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->execute([':id' => $id]);

            // Thêm phân công mới
            $sqlPhanCong = "INSERT INTO phancongrade (maDeThi, maGiaoVien, hanNopDe, ghiChu) 
                            VALUES (:maDeThi, :maGiaoVien, :hanNopDe, :ghiChu)";

            $stmtPhanCong = $this->conn->prepare($sqlPhanCong);

            foreach ($data['maGiaoVien'] as $ma_gv) {
                $stmtPhanCong->execute([
                    ':maDeThi' => $id,
                    ':maGiaoVien' => $ma_gv,
                    ':hanNopDe' => $data['hanNopDe'],
                    ':ghiChu' => $data['ghiChu']
                ]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi cập nhật phân công: " . $e->getMessage());
            return false;
        }
    }

    public function deletePhanCong($id)
    {
        try {
            $this->conn->beginTransaction();

            // Xóa các phân công ra đề liên quan
            $sqlDeletePhanCong = "DELETE FROM phancongrade WHERE maDeThi = :id";
            $stmtDeletePhanCong = $this->conn->prepare($sqlDeletePhanCong);
            $stmtDeletePhanCong->execute([':id' => $id]);

            // Xóa đề thi
            $sqlDeleteDeThi = "DELETE FROM dethi WHERE maDeThi = :id";
            $stmtDeleteDeThi = $this->conn->prepare($sqlDeleteDeThi);
            $stmtDeleteDeThi->execute([':id' => $id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi xóa phân công: " . $e->getMessage());
            return false;
        }
    }
    
}