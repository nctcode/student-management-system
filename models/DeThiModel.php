<?php
require_once 'models/Database.php';

class DethiModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy thông tin giáo viên theo maNguoiDung
    public function getGiaoVienByMaNguoiDung($maNguoiDung)
    {
        $stmt = $this->conn->prepare("SELECT * FROM giaovien WHERE maNguoiDung = :maNguoiDung LIMIT 1");
        $stmt->execute(['maNguoiDung' => $maNguoiDung]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    ///////////////////////////////////////////////////////////////////////
    ////////////////////////////LẬP ĐỀ THI///////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    // Tạo hoặc cập nhật đề thi
    public function createDeThi($data)
    {
        // Kiểm tra xem đề thi đã tồn tại chưa
        if (isset($data['maDeThi'])) {
            $checkSql = "SELECT COUNT(*) FROM dethi WHERE maDeThi = :maDeThi";
            $checkStmt = $this->conn->prepare($checkSql);
            $checkStmt->execute(['maDeThi' => $data['maDeThi']]);
            $exists = $checkStmt->fetchColumn();
            
            if ($exists) {
                // Cập nhật đề thi đã tồn tại
                $sql = "UPDATE dethi SET 
                        maGiaoVien = :maGiaoVien,
                        maMonHoc = :maMonHoc,
                        maKhoi = :maKhoi,
                        maNienKhoa = :maNienKhoa,
                        tieuDe = :tieuDe,
                        noiDung = :noiDung,
                        ngayNop = :ngayNop,
                        trangThai = :trangThai,
                        ngayDuyet = NULL
                        WHERE maDeThi = :maDeThi";
                        
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute($data);
            }
        }
        
        // Tạo mới nếu chưa tồn tại
        $sql = "INSERT INTO dethi (maGiaoVien, maMonHoc, maKhoi, maNienKhoa, tieuDe, noiDung, trangThai, ngayNop) 
                VALUES (:maGiaoVien, :maMonHoc, :maKhoi, :maNienKhoa, :tieuDe, :noiDung, :trangThai, :ngayNop)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Lấy danh sách đề thi của giáo viên
    public function getDeThiByGiaoVien($maNguoiDung)
    {
        $giaoVien = $this->getGiaoVienByMaNguoiDung($maNguoiDung);
        if (!$giaoVien) return [];

        $sql = "SELECT d.maDeThi, d.tieuDe, m.tenMonHoc as monHoc, 
                    d.trangThai, d.noiDung as fileDeThi, d.ngayNop,
                    nk.hocKy, nk.namHoc, d.maNienKhoa 
                FROM dethi d
                JOIN monhoc m ON d.maMonHoc = m.maMonHoc
                LEFT JOIN nienkhoa nk ON d.maNienKhoa = nk.maNienKhoa  
                WHERE d.maGiaoVien = :maGiaoVien
                ORDER BY d.maDeThi DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['maGiaoVien' => $giaoVien['maGiaoVien']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function giaoVienDuocPhanCong($maGiaoVien)
    {
        $sql = "SELECT 1 FROM phancongrade WHERE maGiaoVien = :maGiaoVien LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['maGiaoVien' => $maGiaoVien]);
        return $stmt->fetchColumn() ? true : false;
    }


    ///////////////////////////////////////////////////////////////////////
    ////////////////////////////DUYỆT ĐỀ THI///////////////////////////////
    ///////////////////////////////////////////////////////////////////////

    // Lấy thông tin tổ trưởng theo maNguoiDung
    public function getToTruongByMaNguoiDung($maNguoiDung)
    {
        $stmt = $this->conn->prepare("SELECT * FROM totruongchuyenmon WHERE maNguoiDung = :maNguoiDung LIMIT 1");
        $stmt->execute(['maNguoiDung' => $maNguoiDung]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách đề thi theo môn học, khối và học kỳ - CHỈ LẤY ĐÃ NỘP
    public function getDeThi($maMonHoc, $maKhoi = null, $maNienKhoa = null)
    {
        $sql = "SELECT d.maDeThi, d.tieuDe, d.trangThai, d.noiDung as fileDeThi, 
                    n.hoTen, m.tenMonHoc, d.maMonHoc,
                    d.ngayNop, d.maKhoi, d.maNienKhoa
                FROM dethi d
                JOIN giaovien g ON d.maGiaoVien = g.maGiaoVien
                JOIN nguoidung n ON g.maNguoiDung = n.maNguoiDung
                JOIN monhoc m ON d.maMonHoc = m.maMonHoc
                WHERE d.maMonHoc = :maMonHoc 
                AND d.trangThai = 'CHO_DUYET' 
                AND d.ngayNop IS NOT NULL";  // CHỈ LẤY ĐỀ ĐÃ NỘP

        $params = ['maMonHoc' => $maMonHoc];

        if ($maKhoi) {
            $sql .= " AND d.maKhoi = :maKhoi";
            $params['maKhoi'] = $maKhoi;
        }
        if ($maNienKhoa) {
            $sql .= " AND d.maNienKhoa = :maNienKhoa";
            $params['maNienKhoa'] = $maNienKhoa;
        }

        $sql .= " ORDER BY d.ngayNop DESC, d.maDeThi DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả khối học
    public function getAllKhoiHoc()
    {
        $stmt = $this->conn->prepare("SELECT maKhoi, tenKhoi FROM khoi ORDER BY maKhoi");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Lấy tất cả niên khóa
    public function getAllNienKhoa()
    {
        $stmt = $this->conn->prepare("SELECT maNienKhoa, hocKy FROM nienkhoa ORDER BY maNienKhoa, hocKy");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 đề thi
    public function getDeThiById($id) {
        $sql = "SELECT 
                    dt.*, 
                    k.tenKhoi, 
                    mh.tenMonHoc,
                    nk.hocKy,
                    nk.namHoc,
                    -- Lấy thông tin giáo viên từ bảng giaovien và nguoidung
                    nd.hoTen AS tenGiaoVien,
                    gv.maGiaoVien,
                    -- Lấy thông tin phân công (nếu có)
                    GROUP_CONCAT(DISTINCT gv2.maGiaoVien) AS dsMaGiaoVien,
                    GROUP_CONCAT(DISTINCT nd2.hoTen SEPARATOR ', ') AS dsTenGiaoVien,
                    pc.hanNopDe,
                    pc.ghiChu
                FROM dethi dt
                -- JOIN để lấy thông tin môn học
                LEFT JOIN monhoc mh ON dt.maMonHoc = mh.maMonHoc
                -- JOIN để lấy thông tin khối
                LEFT JOIN khoi k ON dt.maKhoi = k.maKhoi
                -- JOIN để lấy thông tin học kỳ
                LEFT JOIN nienkhoa nk ON dt.maNienKhoa = nk.maNienKhoa
                -- JOIN để lấy thông tin giáo viên tạo đề thi
                LEFT JOIN giaovien gv ON dt.maGiaoVien = gv.maGiaoVien
                LEFT JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                -- JOIN để lấy thông tin phân công (nếu có)
                LEFT JOIN phancongrade pc ON dt.maDeThi = pc.maDeThi
                LEFT JOIN giaovien gv2 ON pc.maGiaoVien = gv2.maGiaoVien
                LEFT JOIN nguoidung nd2 ON gv2.maNguoiDung = nd2.maNguoiDung
                WHERE dt.maDeThi = :id
                GROUP BY dt.maDeThi";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Cập nhật trạng thái đề thi
    public function capNhatTrangThai($maDeThi, $trangThai, $ghiChu = null)
    {
        // Kiểm tra đề thi đã nộp chưa
        $sqlCheck = "SELECT COUNT(*) FROM dethi WHERE maDeThi = :maDeThi AND ngayNop IS NOT NULL";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute(['maDeThi' => $maDeThi]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            return false; // Đề thi chưa nộp
        }
        
        $sql = "UPDATE dethi SET trangThai = :trangThai, ghiChu = :ghiChu, ngayDuyet = NOW() WHERE maDeThi = :maDeThi";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'trangThai' => $trangThai,
            'ghiChu' => $ghiChu,
            'maDeThi' => $maDeThi
        ]);
    }


    ///////////////////////////////////////////////////////////////////////
    ////////////////////////////LỊCH SỬ DUYỆT ĐỀ THI///////////////////////
    ///////////////////////////////////////////////////////////////////////


    // Lấy lịch sử duyệt đề thi theo chuyên môn tổ trưởng, khối, niên khóa, môn học
    public function getLichSuDuyetDeThi($maNguoiDung, $maKhoi = null, $maNienKhoa = null, $maMonHoc = null)
    {
        // Lấy thông tin tổ trưởng
        $toTruong = $this->getToTruongByMaNguoiDung($maNguoiDung);
        if (!$toTruong) return [];

        // Nếu không có mã môn từ filter, dùng mã môn của tổ trưởng
        $maMonHocFilter = $maMonHoc ?? $toTruong['maMonHoc'];

        // Query chính
        $sql = "SELECT d.maDeThi, d.tieuDe, d.trangThai, d.noiDung AS fileDeThi,
                n.hoTen, m.tenMonHoc, d.maMonHoc,
                d.ngayNop, d.ngayDuyet, d.maKhoi, d.maNienKhoa
            FROM dethi d
            JOIN giaovien g ON d.maGiaoVien = g.maGiaoVien
            JOIN nguoidung n ON g.maNguoiDung = n.maNguoiDung
            JOIN monhoc m ON d.maMonHoc = m.maMonHoc
            WHERE (d.trangThai = 'DA_DUYET' OR d.trangThai = 'TU_CHOI')";

        $params = [];

        // Lọc môn học
        if ($maMonHocFilter) {
            $sql .= " AND d.maMonHoc = :maMonHoc";
            $params['maMonHoc'] = $maMonHocFilter;
        }

        // Lọc khối
        if ($maKhoi) {
            $sql .= " AND d.maKhoi = :maKhoi";
            $params['maKhoi'] = $maKhoi;
        }

        // Lọc niên khóa
        if ($maNienKhoa) {
            $sql .= " AND d.maNienKhoa = :maNienKhoa";
            $params['maNienKhoa'] = $maNienKhoa;
        }

        $sql .= " ORDER BY d.ngayDuyet DESC, d.maDeThi DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPhanCongGiaoVien($maGiaoVien)
    {
        $sql = "SELECT pc.*, dt.tieuDe, dt.maKhoi, dt.maMonHoc, dt.maNienKhoa,
                    k.tenKhoi, mh.tenMonHoc, dt.soLuongDe,
                    nk.hocKy, nk.namHoc, 
                    pc.hanNopDe, pc.ghiChu
                FROM phancongrade pc
                JOIN dethi dt ON pc.maDeThi = dt.maDeThi
                JOIN khoi k ON dt.maKhoi = k.maKhoi
                JOIN monhoc mh ON dt.maMonHoc = mh.maMonHoc
                LEFT JOIN nienkhoa nk ON dt.maNienKhoa = nk.maNienKhoa
                WHERE pc.maGiaoVien = :maGiaoVien
                AND dt.trangThai = 'Chờ nộp'
                AND (pc.hanNopDe IS NULL OR pc.hanNopDe >= CURDATE())
                ORDER BY pc.hanNopDe ASC
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maGiaoVien' => $maGiaoVien]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm phương thức lấy thông tin học kỳ theo maNienKhoa
    public function getNienKhoaInfo($maNienKhoa)
    {
        $sql = "SELECT hocKy, namHoc FROM nienkhoa WHERE maNienKhoa = :maNienKhoa";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maNienKhoa' => $maNienKhoa]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Thêm phương thức này vào DeThiModel.php
    public function getMonHocList()
    {
        $sql = "SELECT maMonHoc, tenMonHoc FROM monhoc ORDER BY tenMonHoc";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}