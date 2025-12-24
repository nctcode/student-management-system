<?php
require_once 'models/Database.php';
class HomeModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    //Thống kê cho Tổ trưởng chuyên môn
    public function getLeaderStats($maToTruong = null)
    {
        $conn = $this->db->getConnection();

        $stats = [
            'pending_exams' => 0,
            'approved_exams' => 0,
            'rejected_exams' => 0
        ];

        if (!$maToTruong) return $stats;

        // 1. Lấy môn học phụ trách dựa trên mã tổ trưởng (đúng logic)
        $sql = "SELECT maMonHoc FROM totruongchuyenmon WHERE maToTruong = :maToTruong LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':maToTruong' => $maToTruong]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$info) return $stats;

        $maMonHoc = $info['maMonHoc'];

        // 2. Thống kê theo trạng thái
        $sql = "SELECT 
                CASE 
                    WHEN trangThai IS NULL OR trangThai = 'CHO_DUYET' THEN 'pending_exams'
                    WHEN trangThai = 'DA_DUYET' THEN 'approved_exams'
                    WHEN trangThai = 'TU_CHOI' THEN 'rejected_exams'
                END AS status_group,
                COUNT(*) as total
            FROM dethi
            WHERE maMonHoc = :maMonHoc
            GROUP BY status_group";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':maMonHoc' => $maMonHoc]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats[$row['status_group']] = $row['total'];
        }

        return $stats;
    }




    // Thống kê cho Admin
    public function getAdminStats()
    {
        $conn = $this->db->getConnection();

        $stats = [];

        // Tổng học sinh
        $sql = "SELECT COUNT(*) as total FROM hocsinh WHERE trangThai = 'DANG_HOC'";
        $stmt = $conn->query($sql);
        $stats['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Tổng giáo viên
        $sql = "SELECT COUNT(*) as total FROM giaovien g 
                JOIN nguoidung nd ON g.maNguoiDung = nd.maNguoiDung 
                WHERE nd.loaiNguoiDung = 'GIAOVIEN'";
        $stmt = $conn->query($sql);
        $stats['total_teachers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Tổng lớp học
        $sql = "SELECT COUNT(*) as total FROM lophoc";
        $stmt = $conn->query($sql);
        $stats['total_classes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Đơn chờ duyệt
        $sql = "SELECT COUNT(*) as total FROM donchuyenloptruong 
                WHERE trangThaiTruongDi = 'Chờ duyệt' OR trangThaiTruongDen = 'Chờ duyệt'";
        $stmt = $conn->query($sql);
        $stats['pending_requests'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Hồ sơ tuyển sinh chờ duyệt
        $sql = "SELECT COUNT(*) as total FROM hosotuyensinh 
                WHERE trangThai = 'CHO_XET_DUYET'";
        $stmt = $conn->query($sql);
        $stats['pending_admissions'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Học phí chưa đóng
        $sql = "SELECT COUNT(*) as total FROM hocphi 
                WHERE trangThai = 'CHUA_NOP' AND hanNop < CURDATE()";
        $stmt = $conn->query($sql);
        $stats['overdue_tuitions'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }

    // Thống kê cho Giáo viên
    public function getTeacherStats($maGiaoVien)
    {
        $conn = $this->db->getConnection();

        $stats = [];

        // Học sinh trong lớp chủ nhiệm
        $sql = "SELECT COUNT(*) as total FROM hocsinh hs 
                JOIN lophoc l ON hs.maLop = l.maLop 
                WHERE l.maGiaoVien = ? AND hs.trangThai = 'DANG_HOC'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        $stats['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Bài tập chưa chấm
        $sql = "SELECT COUNT(DISTINCT bt.maBaiTap) as total 
                FROM baitap bt 
                LEFT JOIN bainop bn ON bt.maBaiTap = bn.maBaiTap 
                WHERE bt.maGV = ? AND bn.maBaiNop IS NULL";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        $stats['pending_assignments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // // Tiết dạy hôm nay
        // $sql = "SELECT COUNT(*) as total FROM thoikhoabieu 
        //         WHERE maGiaoVien = ? AND ngayApDung = CURDATE()";
        // $stmt = $conn->prepare($sql);
        // $stmt->execute([$maGiaoVien]);
        // $stats['today_lessons'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Lớp chủ nhiệm
        $sql = "SELECT COUNT(*) as total FROM lophoc 
                WHERE maGiaoVien = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien]);
        $stats['homeroom_classes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }

    // Thống kê cho Học sinh
    public function getStudentStats($maHocSinh)
    {
        $conn = $this->db->getConnection();

        $stats = [];

        // Điểm trung bình
        $sql = "SELECT AVG(d.diemSo) as average FROM diem d
                JOIN hocsinh hs ON d.maHocSinh = hs.maHocSinh
                WHERE hs.maHocSinh = ? AND d.hocKy = 'HK1' AND d.namHoc = '2024-2025'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['average_score'] = $result['average'] ? round($result['average'], 1) : 0;

        // Tỷ lệ chuyên cần
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN cc.trangThai = 'Co_mat' THEN 1 ELSE 0 END) as present 
                FROM chuyencan cc
                JOIN BUOIHOC bh ON cc.maBuoiHoc = bh.maBuoiHoc
                WHERE maHocSinh = ? AND ngayHoc >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['attendance_rate'] = $result['total'] > 0 ?
            round(($result['present'] / $result['total']) * 100) : 100;

        // Bài tập mới
        $sql = "SELECT COUNT(*) as total FROM baitap 
                WHERE maLop = (SELECT maLop FROM hocsinh WHERE maHocSinh = ?) 
                AND hanNop >= CURDATE()";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        $stats['new_assignments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Thông báo mới
        $sql = "SELECT COUNT(*) as total FROM thongbao 
                WHERE (nguoiNhan = 'TAT_CA' OR nguoiNhan = 'HOC_SINH') 
                AND trangThai = 'Chưa xem'";
        $stmt = $conn->query($sql);
        $stats['new_notifications'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Số môn học
        $sql = "SELECT COUNT(DISTINCT maMonHoc) as total FROM diem 
                WHERE maHocSinh = ? AND hocKy = 'HK1' AND namHoc = '2024-2025'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        $stats['total_subjects'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }

    // Thống kê cho Phụ huynh
    public function getParentStats($maPhuHuynh)
    {
        $conn = $this->db->getConnection();

        $stats = [];

        // Lấy mã học sinh của con
        $sql = "SELECT maHocSinh FROM hocsinh WHERE maPhuHuynh = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maPhuHuynh]);
        $maHocSinh = $stmt->fetch(PDO::FETCH_ASSOC)['maHocSinh'];

        if ($maHocSinh) {
            // Điểm trung bình của con
            $sql = "SELECT AVG(d.diemSo) as average FROM diem d
                    WHERE d.maHocSinh = ? AND d.hocKy = 'HK1' AND d.namHoc = '2024-2025'";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maHocSinh]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['child_average'] = $result['average'] ? round($result['average'], 1) : 0;

            // Chuyên cần của con
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN cc.trangThai = 'CO_MAT' THEN 1 ELSE 0 END) as present 
                    FROM chuyencan cc
                    JOIN BUOIHOC bh ON cc.maBuoiHoc = bh.maBuoiHoc
                    WHERE maHocSinh = ? AND ngayHoc >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maHocSinh]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['child_attendance'] = $result['total'] > 0 ?
                round(($result['present'] / $result['total']) * 100) : 100;

            // Số con đang học
            $sql = "SELECT COUNT(*) as total FROM hocsinh 
                    WHERE maPhuHuynh = ? AND trangThai = 'DANG_HOC'";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maPhuHuynh]);
            $stats['total_children'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } else {
            $stats['child_average'] = 0;
            $stats['child_attendance'] = 0;
            $stats['total_children'] = 0;
        }

        // Học phí tháng
        $sql = "SELECT SUM(soTien) as total FROM hocphi 
                WHERE maHocSinh IN (SELECT maHocSinh FROM hocsinh WHERE maPhuHuynh = ?) 
                AND thang = MONTH(CURDATE()) AND trangThai = 'CHUA_NOP'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maPhuHuynh]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['monthly_tuition'] = $result['total'] ?? 0;

        // Thông báo mới
        $sql = "SELECT COUNT(*) as total FROM thongbao 
                WHERE (nguoiNhan = 'TAT_CA' OR nguoiNhan = 'PHU_HUYNH') 
                AND trangThai = 'Chưa xem'";
        $stmt = $conn->query($sql);
        $stats['new_notifications'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }

    // Thống kê cho Ban giám hiệu
    public function getPrincipalStats()
    {
        $conn = $this->db->getConnection();

        $stats = [];

        // Tổng học sinh toàn trường
        $sql = "SELECT COUNT(*) as total FROM hocsinh WHERE trangThai = 'DANG_HOC'";
        $stmt = $conn->query($sql);
        $stats['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Tổng giáo viên
        $sql = "SELECT COUNT(*) as total FROM giaovien g 
                JOIN nguoidung nd ON g.maNguoiDung = nd.maNguoiDung 
                WHERE nd.loaiNguoiDung = 'GIAOVIEN'";
        $stmt = $conn->query($sql);
        $stats['total_teachers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Tổng lớp học
        $sql = "SELECT COUNT(*) as total FROM lophoc";
        $stmt = $conn->query($sql);
        $stats['total_classes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Tỷ lệ học sinh giỏi
        $sql = "SELECT COUNT(*) as total FROM ketquahoctap 
                WHERE hocLuc = 'GIOI' AND hocKy = 'HK1' AND namHoc = '2024-2025'";
        $stmt = $conn->query($sql);
        $excellentStudents = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $stats['excellent_rate'] = $stats['total_students'] > 0 ?
            round(($excellentStudents / $stats['total_students']) * 100, 1) : 0;

        // Doanh thu học phí tháng
        $sql = "SELECT SUM(soTien) as total FROM hocphi 
                WHERE thang = MONTH(CURDATE()) AND trangThai = 'DA_NOP'";
        $stmt = $conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['monthly_revenue'] = $result['total'] ?? 0;

        return $stats;
    }

    // Lấy lịch học/dạy hôm nay
    public function getTodaySchedule($maNguoiDung, $role)
    {
        $conn = $this->db->getConnection();
        $today = date('Y-m-d'); // Lấy ngày hiện tại

        if ($role === 'GIAOVIEN') {
            // Lấy mã giáo viên từ mã người dùng
            $maGiaoVien = $this->getMaGiaoVien($maNguoiDung);
            if (!$maGiaoVien) return [];

            $sql = "SELECT bh.*, mh.tenMonHoc, l.tenLop, l.maLop
                    FROM buoihoc bh
                    JOIN monhoc mh ON bh.maMonHoc = mh.maMonHoc
                    JOIN lophoc l ON bh.maLop = l.maLop
                    WHERE bh.maGiaoVien = ? AND bh.ngayHoc = ?
                    ORDER BY bh.tietBatDau";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$maGiaoVien, $today]);
        } else if ($role === 'HOCSINH') {
            // Lấy mã học sinh và lớp từ mã người dùng
            $studentInfo = $this->getStudentInfo($maNguoiDung);
            if (!$studentInfo || empty($studentInfo['maLop'])) return [];

            $sql = "SELECT bh.*, mh.tenMonHoc, gv.maGiaoVien, nd.hoTen as tenGiaoVien, l.tenLop
                    FROM buoihoc bh
                    JOIN monhoc mh ON bh.maMonHoc = mh.maMonHoc
                    JOIN giaovien gv ON bh.maGiaoVien = gv.maGiaoVien
                    JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                    JOIN lophoc l ON bh.maLop = l.maLop
                    WHERE bh.maLop = ? AND bh.ngayHoc = ?
                    ORDER BY bh.tietBatDau";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$studentInfo['maLop'], $today]);
        } else {
            return [];
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông báo mới
    public function getNewNotifications($role)
    {
        $conn = $this->db->getConnection();

        $recipient = '';
        switch ($role) {
            case 'HOCSINH':
                $recipient = "HOC_SINH";
                break;
            case 'PHUHUYNH':
                $recipient = "PHU_HUYNH";
                break;
            case 'GIAOVIEN':
                $recipient = "GIAO_VIEN";
                break;
            default:
                $recipient = "TAT_CA";
        }

        $sql = "SELECT * FROM thongbao 
                WHERE (nguoiNhan = 'TAT_CA' OR nguoiNhan = ?) 
                AND trangThai = 'Chưa xem'
                ORDER BY ngayGui DESC 
                LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$recipient]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy điểm mới nhất của học sinh
    public function getRecentScores($maHocSinh, $limit = 5)
    {
        $conn = $this->db->getConnection();

        // Sửa LIMIT - nối trực tiếp vào câu SQL sau khi đã validate
        $limit = intval($limit);

        $sql = "SELECT d.*, mh.tenMonHoc 
                FROM diem d
                JOIN monhoc mh ON d.maMonHoc = mh.maMonHoc
                WHERE d.maHocSinh = ?
                ORDER BY d.ngayNhap DESC 
                LIMIT " . $limit;
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy bài tập mới
    public function getNewAssignments($maHocSinh, $limit = 5)
    {
        $conn = $this->db->getConnection();

        $limit = intval($limit);

        $sql = "SELECT bt.*, mh.tenMonHoc, nd.hoTen as tenGiaoVien
                FROM baitap bt
                JOIN monhoc mh ON bt.maMonHoc = mh.maMonHoc
                JOIN giaovien gv ON bt.maGV = gv.maGiaoVien
                JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
                WHERE bt.maLop = (SELECT maLop FROM hocsinh WHERE maHocSinh = ?)
                AND bt.hanNop >= CURDATE()
                ORDER BY bt.hanNop ASC 
                LIMIT " . $limit;
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin học sinh từ mã người dùng
    public function getStudentInfo($maNguoiDung)
    {
        $conn = $this->db->getConnection();

        $sql = "SELECT hs.maHocSinh, hs.maLop, l.tenLop 
                FROM hocsinh hs
                JOIN lophoc l ON hs.maLop = l.maLop
                WHERE hs.maNguoiDung = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy mã giáo viên từ mã người dùng
    public function getMaGiaoVien($maNguoiDung)
    {
        $conn = $this->db->getConnection();

        $sql = "SELECT maGiaoVien FROM giaovien WHERE maNguoiDung = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['maGiaoVien'] : null;
    }

    // Lấy mã phụ huynh từ mã người dùng
    public function getMaPhuHuynh($maNguoiDung)
    {
        $conn = $this->db->getConnection();

        $sql = "SELECT maPhuHuynh FROM phuhuynh WHERE maNguoiDung = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['maPhuHuynh'] : null;
    }

    //Lẫy mã tổ trưởng từ mã người dùng
    public function getMaToTruong($maNguoiDung)
    {
        $conn = $this->db->getConnection();

        $sql = "SELECT maToTruong FROM totruongchuyenmon WHERE maNguoiDung = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['maToTruong'] : null;
    }


    // Lấy thông tin lớp học cho giáo viên
    public function getTeacherClasses($maGiaoVien)
    {
        $conn = $this->db->getConnection();

        $sql = "SELECT l.*, k.tenKhoi 
                FROM lophoc l
                JOIN khoi k ON l.maKhoi = k.maKhoi
                WHERE l.maGiaoVien = ? OR l.maLop IN (
                    SELECT DISTINCT maLop FROM phanconggiangday 
                    WHERE maGiaoVien = ? AND trangThai = 'Hoạt động'
                )";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maGiaoVien, $maGiaoVien]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy học sinh của phụ huynh
    public function getParentChildren($maPhuHuynh)
    {
        $conn = $this->db->getConnection();

        $sql = "SELECT hs.*, nd.hoTen, l.tenLop 
                FROM hocsinh hs
                JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                JOIN lophoc l ON hs.maLop = l.maLop
                WHERE hs.maPhuHuynh = ? AND hs.trangThai = 'DANG_HOC'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maPhuHuynh]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================KIET================


    // Lấy tổng quan hệ thống
    public function getSystemOverview()
    {
        $conn = $this->db->getConnection();

        $overview = [];

        // Tổng người dùng
        $sql = "SELECT COUNT(*) as total FROM nguoidung";
        $stmt = $conn->query($sql);
        $overview['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Tổng tài khoản
        $sql = "SELECT COUNT(*) as total FROM taikhoan WHERE trangThai = 'HOAT_DONG'";
        $stmt = $conn->query($sql);
        $overview['active_accounts'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Tổng môn học
        $sql = "SELECT COUNT(*) as total FROM monhoc";
        $stmt = $conn->query($sql);
        $overview['total_subjects'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Tổng khối
        $sql = "SELECT COUNT(*) as total FROM khoi";
        $stmt = $conn->query($sql);
        $overview['total_grades'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $overview;
    }
}
