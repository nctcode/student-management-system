<?php
require_once 'models/Database.php';

class DiemModel {
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

    // Lấy danh sách học sinh và các điểm ĐÃ CÓ của họ cho môn học và học kỳ cụ thể.
    public function getDanhSachLopVaDiemHienTai($maLop, $maMonHoc, $hocKy, $namHoc) {
        $conn = $this->db->getConnection();

        $sqlHS = "SELECT hs.maHocSinh, nd.hoTen
                  FROM hocsinh hs
                  JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                  WHERE hs.maLop = :maLop
                  ORDER BY nd.hoTen";
        $stmtHS = $conn->prepare($sqlHS);
        $stmtHS->execute(['maLop' => $maLop]);
        $danhSachHocSinh = $stmtHS->fetchAll(PDO::FETCH_ASSOC);

        $sqlDiem = "SELECT maHocSinh, loaiDiem, diemSo 
                    FROM diem 
                    WHERE maMonHoc = :maMonHoc 
                      AND hocKy = :hocKy 
                      AND namHoc = :namHoc 
                      AND maHocSinh IN (SELECT maHocSinh FROM hocsinh WHERE maLop = :maLop)
                    ORDER BY maDiem ASC"; 
        $stmtDiem = $conn->prepare($sqlDiem);
        $stmtDiem->execute([
            'maMonHoc' => $maMonHoc, 
            'hocKy' => $hocKy, 
            'namHoc' => $namHoc, 
            'maLop' => $maLop
        ]);
        $diemHienTaiRaw = $stmtDiem->fetchAll(PDO::FETCH_ASSOC);

        $diemHienTai = [];
        foreach ($diemHienTaiRaw as $diem) {
            $diemHienTai[$diem['maHocSinh']][$diem['loaiDiem']][] = $diem['diemSo'];
        }

        $ketQua = [];
        foreach ($danhSachHocSinh as $hs) {
            $maHS = $hs['maHocSinh'];
            $ketQua[] = [
                'maHocSinh' => $maHS,
                'hoTen' => $hs['hoTen'],
                'MIENG' => $diemHienTai[$maHS]['MIENG'] ?? [],
                '15_PHUT' => $diemHienTai[$maHS]['15_PHUT'] ?? [],
                '1_TIET' => $diemHienTai[$maHS]['1_TIET'] ?? [],
                'CUOI_KY' => $diemHienTai[$maHS]['CUOI_KY'] ?? []
            ];
        }

        return $ketQua;
    }

    // Lưu điểm 
    public function luuBangDiem($maMonHoc, $maGiaoVien, $hocKy, $namHoc, $maLop, $danhSachDiem) {
        $conn = $this->db->getConnection();
        
        $conn->beginTransaction();
        try {
            $sqlGetHS = "SELECT maHocSinh FROM hocsinh WHERE maLop = ?";
            $stmtGetHS = $conn->prepare($sqlGetHS);
            $stmtGetHS->execute([$maLop]);
            $dsMaHocSinh = $stmtGetHS->fetchAll(PDO::FETCH_COLUMN);

            if (empty($dsMaHocSinh)) {
                $conn->commit();
                return true;
            }
            $placeholders = implode(',', array_fill(0, count($dsMaHocSinh), '?'));

            $sqlDelete = "DELETE FROM diem 
                          WHERE maMonHoc = ? AND hocKy = ? AND namHoc = ? 
                          AND maHocSinh IN ($placeholders)";
            
            $paramsDelete = array_merge([$maMonHoc, $hocKy, $namHoc], $dsMaHocSinh);
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->execute($paramsDelete);

            $sqlInsert = "INSERT INTO diem (maHocSinh, maMonHoc, loaiDiem, hocKy, namHoc, diemSo, maGiaoVien, ngayNhap)
                          VALUES (:maHocSinh, :maMonHoc, :loaiDiem, :hocKy, :namHoc, :diemSo, :maGiaoVien, CURDATE())";
            $stmtInsert = $conn->prepare($sqlInsert);

            foreach ($danhSachDiem as $maHocSinh => $cacLoaiDiem) {
                if (in_array($maHocSinh, $dsMaHocSinh)) {
                    foreach ($cacLoaiDiem as $loaiDiem => $mangDiem) {
                        if (is_array($mangDiem)) {
                            foreach ($mangDiem as $diemSo) {
                                $stmtInsert->execute([
                                    'maHocSinh' => $maHocSinh,
                                    'maMonHoc' => $maMonHoc,
                                    'loaiDiem' => $loaiDiem,
                                    'hocKy' => $hocKy,
                                    'namHoc' => $namHoc,
                                    'diemSo' => $diemSo,
                                    'maGiaoVien' => $maGiaoVien
                                ]);
                            }
                        }
                    }
                }
            }
            
            $conn->commit();
            return true; 

        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Lỗi lưu điểm: " . $e->getMessage());
            return false; 
        }
    }

    // Lấy các năm học và học kỳ mà HS có điểm
    public function getNamHocHocKyCuaHS($maHocSinh) {
        $conn = $this->db->getConnection();
        $sql = "SELECT DISTINCT namHoc, hocKy 
                FROM diem 
                WHERE maHocSinh = ? 
                ORDER BY namHoc DESC, hocKy DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$maHocSinh]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy TBM Lớp cho các môn
    private function getTbmLop($maLop, $namHoc, $hocKy, $cacMonHoc) {
        $conn = $this->db->getConnection();
        $TBM_Lop_Final = [];

        try {
            $sqlHSCuaLop = "SELECT maHocSinh FROM hocsinh WHERE maLop = ?";
            $stmtHSCuaLop = $conn->prepare($sqlHSCuaLop);
            $stmtHSCuaLop->execute([$maLop]);
            $dsHocSinhLop = $stmtHSCuaLop->fetchAll(PDO::FETCH_COLUMN);

            if (empty($dsHocSinhLop)) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($dsHocSinhLop), '?'));
            $sqlAllDiem = "SELECT maHocSinh, maMonHoc, loaiDiem, diemSo
                           FROM diem
                           WHERE maHocSinh IN ($placeholders)
                             AND namHoc = ? AND hocKy = ?";
            $params = array_merge($dsHocSinhLop, [$namHoc, $hocKy]);
            $stmtAllDiem = $conn->prepare($sqlAllDiem);
            $stmtAllDiem->execute($params);
            $allDiemRaw = $stmtAllDiem->fetchAll(PDO::FETCH_ASSOC);

            $allDiemMap = [];
            foreach ($allDiemRaw as $diem) {
                $allDiemMap[$diem['maHocSinh']][$diem['maMonHoc']][$diem['loaiDiem']][] = $diem['diemSo'];
            }

            $TBMs_Lop_TheoMon = []; 
            foreach ($allDiemMap as $maHS => $monHocDiem) {
                foreach ($monHocDiem as $maMon => $loaiDiemDiem) {
                    
                    if (!isset($cacMonHoc[$maMon])) continue;

                    $diem_MIENG = $loaiDiemDiem['MIENG'] ?? [];
                    $diem_15_PHUT = $loaiDiemDiem['15_PHUT'] ?? [];
                    $diem_1_TIET = $loaiDiemDiem['1_TIET'] ?? [];
                    $diem_CUOI_KY = $loaiDiemDiem['CUOI_KY'] ?? [];

                    if (!empty($diem_MIENG) && !empty($diem_15_PHUT) && !empty($diem_1_TIET) && !empty($diem_CUOI_KY)) {
                        $tongDiem = 0; $tongHeSo = 0;
                        foreach ($diem_MIENG as $d) { $tongDiem += $d * 1; $tongHeSo += 1; }
                        foreach ($diem_15_PHUT as $d) { $tongDiem += $d * 1; $tongHeSo += 1; }
                        foreach ($diem_1_TIET as $d) { $tongDiem += $d * 2; $tongHeSo += 2; }
                        foreach ($diem_CUOI_KY as $d) { $tongDiem += $d * 3; $tongHeSo += 3; }

                        if ($tongHeSo > 0) {
                            $TBMs_Lop_TheoMon[$maMon][] = $tongDiem / $tongHeSo;
                        }
                    }
                }
            }

            foreach ($TBMs_Lop_TheoMon as $maMon => $tbmArray) {
                if (count($tbmArray) > 0) {
                    $TBM_Lop_Final[$maMon] = round(array_sum($tbmArray) / count($tbmArray), 2);
                }
            }
        
        } catch (Exception $e) {
            error_log("Lỗi tính TBM Lớp: " . $e->getMessage());
        }
        
        return $TBM_Lop_Final;
    }

    // Lấy bảng điểm chi tiết của MỘT học sinh
    public function getBangDiemHocSinh($maHocSinh, $maLop, $namHoc, $hocKy) {
        $conn = $this->db->getConnection();
        
        $sqlMonHoc = "SELECT DISTINCT mh.maMonHoc, mh.tenMonHoc
                     FROM diem d
                     JOIN monhoc mh ON d.maMonHoc = mh.maMonHoc
                     WHERE d.maHocSinh = ? AND d.namHoc = ? AND d.hocKy = ?";
        $stmtMonHoc = $conn->prepare($sqlMonHoc);
        $stmtMonHoc->execute([$maHocSinh, $namHoc, $hocKy]);
        $cacMonHocRaw = $stmtMonHoc->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cacMonHocRaw)) return ['bangDiem' => [], 'TBM_HocKy' => null];

        $cacMonHocMap = [];
        foreach ($cacMonHocRaw as $mon) {
            $cacMonHocMap[$mon['maMonHoc']] = $mon['tenMonHoc'];
        }

        $TBM_Lop_Final = [];
        if ($maLop) {
            $TBM_Lop_Final = $this->getTbmLop($maLop, $namHoc, $hocKy, $cacMonHocMap);
        }

        $sqlDiem = "SELECT maMonHoc, loaiDiem, diemSo
                    FROM diem
                    WHERE maHocSinh = ? AND namHoc = ? AND hocKy = ?";
        $stmtDiem = $conn->prepare($sqlDiem);
        $stmtDiem->execute([$maHocSinh, $namHoc, $hocKy]);
        $diemRaw = $stmtDiem->fetchAll(PDO::FETCH_ASSOC);

        $diemMap = [];
        foreach ($diemRaw as $diem) {
            $diemMap[$diem['maMonHoc']][$diem['loaiDiem']][] = $diem['diemSo'];
        }

        $ketQua = [];
        $tongTBM_CacMon = 0;
        $soMonTinhTBM = 0;
        foreach ($cacMonHocRaw as $mon) { 
            $maMon = $mon['maMonHoc'];
            $diemMonNay = $diemMap[$maMon] ?? [];

            $diem_MIENG = $diemMonNay['MIENG'] ?? [];
            $diem_15_PHUT = $diemMonNay['15_PHUT'] ?? [];
            $diem_1_TIET = $diemMonNay['1_TIET'] ?? [];
            $diem_CUOI_KY = $diemMonNay['CUOI_KY'] ?? [];

            $TBM = null; 

            if (!empty($diem_MIENG) && !empty($diem_15_PHUT) && !empty($diem_1_TIET) && !empty($diem_CUOI_KY)) {
                
                $tongDiem = 0;
                $tongHeSo = 0;

                foreach ($diem_MIENG as $d) { $tongDiem += $d * 1; $tongHeSo += 1; }
                foreach ($diem_15_PHUT as $d) { $tongDiem += $d * 1; $tongHeSo += 1; }
                foreach ($diem_1_TIET as $d) { $tongDiem += $d * 2; $tongHeSo += 2; }
                foreach ($diem_CUOI_KY as $d) { $tongDiem += $d * 3; $tongHeSo += 3; }

                if ($tongHeSo > 0) {
                    $TBM = round($tongDiem / $tongHeSo, 2);
                }
            }

            if ($TBM !== null) {
                $tongTBM_CacMon += $TBM;
                $soMonTinhTBM++;
            }

            $TBM_Lop = $TBM_Lop_Final[$maMon] ?? null;

            $ketQua[] = [
                'tenMonHoc' => $mon['tenMonHoc'],
                'MIENG' => $diem_MIENG,
                '15_PHUT' => $diem_15_PHUT,
                '1_TIET' => $diem_1_TIET,
                'CUOI_KY' => $diem_CUOI_KY,
                'TBM' => $TBM,
                'TBM_Lop' => $TBM_Lop 
            ];

            $TBM_HocKy = null;
            if ($soMonTinhTBM > 0) {
                $TBM_HocKy = round($tongTBM_CacMon / $soMonTinhTBM, 2);
            }
        }
        return [
            'bangDiem' => $ketQua,
            'TBM_HocKy' => $TBM_HocKy
        ];
    }
}
?>