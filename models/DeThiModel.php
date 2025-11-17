<?php
require_once 'models/Database.php';

class DethiModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Lấy danh sách tất cả đề thi theo khối và học kỳ (tùy chọn lọc)
    public function getExams($maKhoi = null, $maNienKhoa = null, $maNguoiDung = null)
    {
        $conn = $this->db->getConnection();

        $sql = "SELECT d.maDeThi, d.tieuDe, u.hoTen AS tenGiaoVien
            FROM dethi d
            INNER JOIN giaovien g ON d.maGiaoVien = g.maGiaoVien
            INNER JOIN nguoidung u ON g.maNguoiDung = u.maNguoiDung
            INNER JOIN khoi k ON d.maKhoi = k.maKhoi
            INNER JOIN nienkhoa n ON d.maNienKhoa = n.maNienKhoa
            INNER JOIN toTruongChuyenMon t ON d.maMonHoc = t.maMonHoc
            WHERE d.trangThai = 'CHO_DUYET'";

        $params = [];

        // Lọc theo Khối
        if ($maKhoi) {
            $sql .= " AND d.maKhoi = ?";
            $params[] = $maKhoi;
        }

        // Lọc theo Niên khóa
        if ($maNienKhoa) {
            $sql .= " AND d.maNienKhoa = ?";
            $params[] = $maNienKhoa;
        }

        // Lọc theo người dùng (tổ trưởng chuyên môn)
        if ($maNguoiDung) {
            $sql .= " AND t.maNguoiDung = ?";
            $params[] = $maNguoiDung;
        }

        $sql .= " ORDER BY d.maDeThi ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Cập nhật trạng thái duyệt/từ chối nhiều đề thi
    public function updateStatus($maDeThis = [], $status)
    {
        if (empty($maDeThis)) return false;

        $conn = $this->db->getConnection();
        $in = str_repeat('?,', count($maDeThis) - 1) . '?';
        $sql = "UPDATE dethi SET trangThai = ? WHERE maDeThi IN ($in)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_merge([$status], $maDeThis));

        return $stmt->rowCount();
    }


    // Lấy danh sách Khối học (dùng cho combobox)
    public function getKhoiHoc()
    {
        $conn = $this->db->getConnection();
        $sql = "SELECT maKhoi, tenKhoi, maNienKhoa FROM khoi ORDER BY maKhoi ASC";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách Học kỳ (dùng cho combobox)
    public function getNienKhoa()
    {
        $conn = $this->db->getConnection();
        $sql = "SELECT maNienKhoa, hocKy FROM nienkhoa ORDER BY maNienKhoa ASC";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // lấy chi tiết 1 đề thi theo mã đề
    public function getExamDetail($maDeThi)
    {
        $conn = $this->db->getConnection();

        $sql = "SELECT d.*, d.maDeThi, u.hoTen AS tenGiaoVien, k.tenKhoi, n.hocKy, n.namHoc, m.tenMonHoc AS monHoc
        FROM dethi d
        INNER JOIN giaovien g ON d.maGiaoVien = g.maGiaoVien
        INNER JOIN nguoidung u ON g.maNguoiDung = u.maNguoiDung
        INNER JOIN khoi k ON d.maKhoi = k.maKhoi
        INNER JOIN nienkhoa n ON d.maNienKhoa = n.maNienKhoa
        LEFT JOIN monhoc m ON d.maMonHoc = m.maMonHoc
        WHERE d.maDeThi = ?";


        $stmt = $conn->prepare($sql);
        $stmt->execute([$maDeThi]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách câu hỏi và tính tổng số câu, tổng điểm
    public function getQuestions($maDeThi)
    {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM cauhoi WHERE maDeThi = ? ORDER BY maCauHoi ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maDeThi]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Lấy trạng thái đề thi
    public function getLichSuDuyet($maKhoi = null, $maNienKhoa = null, $maNguoiDung = null)
    {
        $conn = $this->db->getConnection();

        $sql = "
        SELECT 
            d.maDeThi,
            d.tieuDe,
            nd.hoTen AS tenGiaoVien,
            d.ngayNop,
            d.trangThai
        FROM 
            dethi d
        JOIN 
            giaovien g ON d.maGiaoVien = g.maGiaoVien
        JOIN 
            nguoidung nd ON g.maNguoiDung = nd.maNguoiDung
        JOIN 
            toTruongChuyenMon t ON t.maMonHoc = d.maMonHoc
        JOIN 
            nguoidung ndTT ON t.maNguoiDung = ndTT.maNguoiDung
        WHERE 
            (d.trangThai = 'DA_DUYET' OR d.trangThai = 'TU_CHOI')
    ";

        $params = [];

        // Lọc khối
        if (!empty($maKhoi)) {
            $sql .= " AND d.maKhoi = :maKhoi";
            $params['maKhoi'] = $maKhoi;
        }

        // Lọc niên khóa
        if (!empty($maNienKhoa)) {
            $sql .= " AND d.maNienKhoa = :maNienKhoa";
            $params['maNienKhoa'] = $maNienKhoa;
        }

        // Lọc theo tổ trưởng chuyên môn (theo người đăng nhập)
        if (!empty($maNguoiDung)) {
            $sql .= " AND t.maNguoiDung = :maNguoiDung";
            $params['maNguoiDung'] = $maNguoiDung;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -----------------------------------------------------------------------------------
    // ----------------------------------------LẬP ĐỀ THI---------------------------------
    // -----------------------------------------------------------------------------------

    public function createExam($maKhoi, $maNienKhoa, $maNguoiDung, $tieuDe, $noiDungDeThi)
    {
        $conn = $this->db->getConnection();

        // Lấy mã giáo viên và mã môn học từ bảng giaovien dựa vào maNguoiDung
        $sqlGV = "SELECT maGiaoVien, maMonHoc FROM giaovien WHERE maNguoiDung = ?";
        $stmtGV = $conn->prepare($sqlGV);
        $stmtGV->execute([$maNguoiDung]);
        $resultGV = $stmtGV->fetch(PDO::FETCH_ASSOC);
        var_dump($resultGV);

        if (!$resultGV) return false; // Không tìm thấy giáo viên

        $maGiaoVien = $resultGV['maGiaoVien']; // Đây mới là mã giáo viên đúng
        $maMonHoc = $resultGV['maMonHoc'];

        // Thêm đề thi vào bảng dethi
        $sqlInsert = "INSERT INTO dethi (tieuDe, noiDung, maKhoi, maNienKhoa, maGiaoVien, maMonHoc, trangThai, ngayNop)
        VALUES (?, ?, ?, ?, ?, ?, 'CHO_DUYET', NOW())";

        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->execute([$tieuDe, $noiDungDeThi, $maKhoi, $maNienKhoa, $maGiaoVien, $maMonHoc]);

        return $conn->lastInsertId(); // Trả về maDeThi vừa tạo
    }

    // Thêm 1 câu hỏi vào cauhoi
    public function addQuestion($maDeThi, $noiDung, $mucDiem)
    {
        $conn = $this->db->getConnection();
        $sql = "INSERT INTO cauhoi (maDeThi, noiDungCauHoi, mucDiem)
            VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$maDeThi, $noiDung, $mucDiem]);
    }

    // Thêm nhiều câu hỏi từ mảng
    public function addQuestionsBatch($maDeThi, $cauHoiArr = [])
    {
        foreach ($cauHoiArr as $cau) {
            $this->addQuestion($maDeThi, $cau['noiDung'], $cau['mucDiem']);
        }
        return true;
    }

    // Lấy tất cả đề thi đã tạo của giáo viên đang đăng nhập, kèm tên môn học và câu hỏi
    public function getDeThiByGiaoVien($maNguoiDung)
    {
        $conn = $this->db->getConnection();

        // Lấy mã giáo viên từ maNguoiDung
        $sqlGV = "SELECT maGiaoVien FROM giaovien WHERE maNguoiDung = ?";
        $stmtGV = $conn->prepare($sqlGV);
        $stmtGV->execute([$maNguoiDung]);
        $gv = $stmtGV->fetch(PDO::FETCH_ASSOC);

        if (!$gv) return []; // Nếu không tìm thấy giáo viên

        $maGiaoVien = $gv['maGiaoVien'];

        // Lấy danh sách đề thi của giáo viên, kèm tên môn học
        $sql = "SELECT d.*, m.tenMonHoc AS monHoc
            FROM dethi d
            LEFT JOIN monhoc m ON d.maMonHoc = m.maMonHoc
            WHERE d.maGiaoVien = ?
            ORDER BY d.ngayNop DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        $deThiList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy câu hỏi cho mỗi đề
        foreach ($deThiList as &$deThi) {
            $stmtCH = $conn->prepare("SELECT noiDungCauHoi, mucDiem FROM cauhoi WHERE maDeThi = ? ORDER BY maCauHoi ASC");
            $stmtCH->execute([$deThi['maDeThi']]);
            $deThi['cauHoi'] = $stmtCH->fetchAll(PDO::FETCH_ASSOC);
        }

        return $deThiList;
    }
}
