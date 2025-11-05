<?php
require_once 'models/Database.php';

class KetQuaHocTapModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection(); // PDO connection
    }

    // Lấy danh sách học sinh của lớp do giáo viên chủ nhiệm
    public function getHocSinhByGiaoVien($maNguoiDung)
    {
        $sql = "
        SELECT hs.maHocSinh, nd.hoTen, l.tenLop
        FROM hocsinh hs
        INNER JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
        INNER JOIN lophoc l ON hs.maLop = l.maLop
        INNER JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
        INNER JOIN nguoidung ndgv ON gv.maNguoiDung = ndgv.maNguoiDung
        WHERE ndgv.maNguoiDung = ?
        ORDER BY nd.hoTen ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy danh sách môn học xuất hiện trong bảng diem của học sinh lớp đó
    public function getMonHocByGiaoVien($maNguoiDung)
    {
        $sql = "
            SELECT DISTINCT m.maMonHoc, m.tenMonHoc
            FROM monhoc m
            INNER JOIN diem d ON m.maMonHoc = d.maMonHoc
            INNER JOIN hocsinh hs ON d.maHocSinh = hs.maHocSinh
            INNER JOIN lophoc l ON hs.maLop = l.maLop
            INNER JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
            INNER JOIN nguoidung nd ON gv.maNguoiDung = nd.maNguoiDung
            WHERE nd.maNguoiDung = ?
            ORDER BY m.maMonHoc ASC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maNguoiDung]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tính điểm trung bình theo môn với trọng số
    public function getDiemTBTheoMon($maNguoiDung, $hocKy)
    {
        $hocSinh = $this->getHocSinhByGiaoVien($maNguoiDung);
        $monHoc = $this->getMonHocByGiaoVien($maNguoiDung);

        $diemTB_HS = []; // điểm trung bình từng môn của từng học sinh
        $diemTB_Lop = []; // điểm trung bình từng môn cả lớp

        // Trọng số cho từng loại điểm
        $trongSo = [
            'MIENG' => 1,
            '15_PHUT' => 1,
            '1_TIET' => 2,
            'GIUA_KY' => 3,
            'CUOI_KY' => 4
        ];

        foreach ($monHoc as $m) {
            $diemMon = [];
            foreach ($hocSinh as $hs) {
                // Lấy điểm tất cả loại của học sinh trong môn và học kỳ
                $stmt = $this->conn->prepare("
                    SELECT loaiDiem, diemSo
                    FROM diem
                    WHERE maHocSinh = :maHocSinh
                      AND maMonHoc = :maMonHoc
                      AND hocKy = :hocKy
                ");
                $stmt->execute([
                    'maHocSinh' => $hs['maHocSinh'],
                    'maMonHoc' => $m['maMonHoc'],
                    'hocKy' => $hocKy
                ]);
                $diemList = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Tính điểm trung bình có trọng số
                $tongDiem = 0;
                $tongTrongSo = 0;
                foreach ($diemList as $d) {
                    $loai = $d['loaiDiem'];
                    if (isset($trongSo[$loai])) {
                        $tongDiem += $d['diemSo'] * $trongSo[$loai];
                        $tongTrongSo += $trongSo[$loai];
                    }
                }
                $tbHS = $tongTrongSo ? round($tongDiem / $tongTrongSo, 2) : 0;
                $diemTB_HS[$hs['maHocSinh']][$m['maMonHoc']] = $tbHS;

                if ($tbHS > 0) $diemMon[] = $tbHS;
            }

            $diemTB_Lop[$m['maMonHoc']] = count($diemMon) ? round(array_sum($diemMon) / count($diemMon), 2) : 0;
        }

        return [
            'hocSinh' => $hocSinh,
            'monHoc' => $monHoc,
            'diemTB_HS' => $diemTB_HS,
            'diemTB_Lop' => $diemTB_Lop
        ];
    }

    // Lấy chi tiết điểm tất cả loại điểm của học sinh theo học kỳ
    public function getChiTietDiem($maHocSinh, $hocKy)
    {
        $sql = "
            SELECT d.maMonHoc, m.tenMonHoc, d.loaiDiem, d.diemSo
            FROM diem d
            INNER JOIN monhoc m ON d.maMonHoc = m.maMonHoc
            WHERE d.maHocSinh = :maHocSinh AND d.hocKy = :hocKy
            ORDER BY m.maMonHoc, FIELD(d.loaiDiem,'MIENG','15_PHUT','1_TIET','GIUA_KY','CUOI_KY')
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'maHocSinh' => $maHocSinh,
            'hocKy' => $hocKy
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $chiTiet = [];
        foreach ($rows as $r) {
            $chiTiet[$r['maMonHoc']]['tenMonHoc'] = $r['tenMonHoc'];
            $chiTiet[$r['maMonHoc']][$r['loaiDiem']] = $r['diemSo'];
        }
        return $chiTiet;
    }

    //thống kê học lực, hạnh kiểm
    public function getThongKeTheoHocLucHanhKiem($maNguoiDung, $hocKy, $tieuChi)
    {
        $sql = "
    SELECT hs.maHocSinh, nd.hoTen, l.tenLop, kq.hocLuc, kq.hanhKiem, kq.xepLoai, kq.diemTrungBinh
    FROM ketquahoctap kq
    INNER JOIN hocsinh hs ON kq.maHocSinh = hs.maHocSinh
    INNER JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
    INNER JOIN lophoc l ON hs.maLop = l.maLop
    INNER JOIN giaovien gv ON l.maGiaoVien = gv.maGiaoVien
    INNER JOIN nguoidung ndgv ON gv.maNguoiDung = ndgv.maNguoiDung
    WHERE ndgv.maNguoiDung = :maNguoiDung
      AND kq.hocKy = :hocKy
    ORDER BY nd.hoTen ASC
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'maNguoiDung' => $maNguoiDung,
            'hocKy' => $hocKy
        ]);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đếm tổng theo học lực và hạnh kiểm
        $thongKeHocLuc = [];
        $thongKeHanhKiem = [];

        foreach ($data as $row) {
            $hl = $row['hocLuc'];
            $hk = $row['hanhKiem'];

            if (!isset($thongKeHocLuc[$hl])) $thongKeHocLuc[$hl] = 0;
            if (!isset($thongKeHanhKiem[$hk])) $thongKeHanhKiem[$hk] = 0;

            $thongKeHocLuc[$hl]++;
            $thongKeHanhKiem[$hk]++;
        }

        return [
            'data' => $data, // danh sách học sinh
            'thongKeHocLuc' => $thongKeHocLuc,
            'thongKeHanhKiem' => $thongKeHanhKiem
        ];
    }
}
