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
        
        // Lấy danh sách học sinh của lớp
        $sqlHS = "SELECT 
                    hs.maHocSinh,
                    nd.hoTen
                  FROM hocsinh hs
                  JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                  WHERE hs.maLop = :maLop
                  ORDER BY nd.hoTen";
        $stmtHS = $conn->prepare($sqlHS);
        $stmtHS->execute(['maLop' => $maLop]);
        $danhSachHocSinh = $stmtHS->fetchAll(PDO::FETCH_ASSOC);

        // Lấy tất cả điểm hiện có của lớp cho môn học và học kỳ này
        $sqlDiem = "SELECT maHocSinh, loaiDiem, diemSo 
                    FROM diem 
                    WHERE maMonHoc = :maMonHoc 
                      AND hocKy = :hocKy 
                      AND namHoc = :namHoc 
                      AND maHocSinh IN (SELECT maHocSinh FROM hocsinh WHERE maLop = :maLop)";
        $stmtDiem = $conn->prepare($sqlDiem);
        $stmtDiem->execute([
            'maMonHoc' => $maMonHoc, 
            'hocKy' => $hocKy, 
            'namHoc' => $namHoc, 
            'maLop' => $maLop
        ]);
        $diemHienTaiRaw = $stmtDiem->fetchAll(PDO::FETCH_ASSOC);

        // Tổ chức lại dữ liệu điểm
        $diemHienTai = [];
        foreach ($diemHienTaiRaw as $diem) {
            $diemHienTai[$diem['maHocSinh']][$diem['loaiDiem']] = $diem['diemSo'];
        }

        // Gộp danh sách học sinh với điểm số
        $ketQua = [];
        foreach ($danhSachHocSinh as $hs) {
            $maHS = $hs['maHocSinh'];
            $ketQua[] = [
                'maHocSinh' => $maHS,
                'hoTen' => $hs['hoTen'],
                'MIENG' => $diemHienTai[$maHS]['MIENG'] ?? null,
                '15_PHUT' => $diemHienTai[$maHS]['15_PHUT'] ?? null,
                '1_TIET' => $diemHienTai[$maHS]['1_TIET'] ?? null,
                'CUOI_KY' => $diemHienTai[$maHS]['CUOI_KY'] ?? null
            ];
        }

        return $ketQua;
    }

    // Lưu điểm 
    public function luuBangDiem($maMonHoc, $maGiaoVien, $hocKy, $namHoc, $danhSachDiem) {
        $conn = $this->db->getConnection();
        
        $conn->beginTransaction();
        try {
            // Lấy ID lớn nhất hiện tại để tạo mã DXXX tuần tự
            $sqlMax = "SELECT MAX(CAST(SUBSTRING(maDiem, 2) AS UNSIGNED)) as maxId 
                       FROM diem 
                       WHERE maDiem LIKE 'D%' FOR UPDATE";
            $stmtMax = $conn->prepare($sqlMax);
            $stmtMax->execute();
            $maxId = $stmtMax->fetch(PDO::FETCH_ASSOC)['maxId'];
            $nextIdCounter = ($maxId ?? 0) + 1; // ID tiếp theo

            // Dùng để kiểm tra xem hàng điểm đã tồn tại chưa
            $sqlSelect = "SELECT maDiem FROM diem 
                          WHERE maHocSinh = :maHocSinh AND maMonHoc = :maMonHoc 
                          AND loaiDiem = :loaiDiem AND hocKy = :hocKy AND namHoc = :namHoc";
            $stmtSelect = $conn->prepare($sqlSelect);

            // Dùng để chèn hàng mới
            $sqlInsert = "INSERT INTO diem (maDiem, maHocSinh, maMonHoc, loaiDiem, hocKy, namHoc, diemSo, maGiaoVien, ngayNhap)
                          VALUES (:maDiem, :maHocSinh, :maMonHoc, :loaiDiem, :hocKy, :namHoc, :diemSo, :maGiaoVien, CURDATE())";
            $stmtInsert = $conn->prepare($sqlInsert);

            // Dùng để cập nhật hàng đã có
            $sqlUpdate = "UPDATE diem SET diemSo = :diemSo, maGiaoVien = :maGiaoVien, ngayNhap = CURDATE()
                          WHERE maHocSinh = :maHocSinh AND maMonHoc = :maMonHoc 
                          AND loaiDiem = :loaiDiem AND hocKy = :hocKy AND namHoc = :namHoc";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            
            // Dùng để xóa hàng nếu ô điểm bị bỏ trống
            $sqlDelete = "DELETE FROM diem 
                          WHERE maHocSinh = :maHocSinh AND maMonHoc = :maMonHoc 
                          AND loaiDiem = :loaiDiem AND hocKy = :hocKy AND namHoc = :namHoc";
            $stmtDelete = $conn->prepare($sqlDelete);

            // Lặp qua dữ liệu và thực thi
            foreach ($danhSachDiem as $maHocSinh => $cacLoaiDiem) {
                foreach ($cacLoaiDiem as $loaiDiem => $diemSo) {
                    
                    if (!in_array($loaiDiem, ['MIENG', '15_PHUT', '1_TIET', 'CUOI_KY'])) {
                        continue; 
                    }
                    
                    // Chuẩn bị tham số chung
                    $params = [
                        'maHocSinh' => $maHocSinh,
                        'maMonHoc' => $maMonHoc,
                        'loaiDiem' => $loaiDiem,
                        'hocKy' => $hocKy,
                        'namHoc' => $namHoc
                    ];

                    if ($diemSo === '' || $diemSo === null) {
                        $stmtDelete->execute($params);
                    
                    } else {
                        
                        // Kiểm tra xem hàng đã tồn tại chưa
                        $stmtSelect->execute($params);
                        $existingRow = $stmtSelect->fetch();
                        
                        if ($existingRow) {
                            // Hàng đã tồn tại -> CẬP NHẬT
                            $params['diemSo'] = $diemSo;
                            $params['maGiaoVien'] = $maGiaoVien;
                            $stmtUpdate->execute($params);
                        } else {
                            // Hàng chưa tồn tại -> THÊM MỚI
                            $params['maDiem'] = 'D' . str_pad($nextIdCounter, 3, '0', STR_PAD_LEFT);
                            $params['diemSo'] = $diemSo;
                            $params['maGiaoVien'] = $maGiaoVien;
                            
                            $stmtInsert->execute($params);
                            
                            // Tăng bộ đếm cho hàng MỚI tiếp theo
                            $nextIdCounter++; 
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
}
?>