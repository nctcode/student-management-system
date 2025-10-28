<?php
class TinNhanModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // === Lấy maNguoiDung từ maHocSinh ===
    public function getMaNguoiDungTuHocSinh($ds_hocsinh_ids) {
        if (empty($ds_hocsinh_ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ds_hocsinh_ids), '?'));
        $sql = "SELECT maNguoiDung FROM hocsinh WHERE maHocSinh IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($ds_hocsinh_ids);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // === Lấy maNguoiDung từ maPhuHuynh ===
    public function getMaNguoiDungTuPhuHuynh($ds_phuhuynh_ids) {
        if (empty($ds_phuhuynh_ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ds_phuhuynh_ids), '?'));
        $sql = "SELECT maNguoiDung FROM phuhuynh WHERE maPhuHuynh IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($ds_phuhuynh_ids);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // === Lấy danh sách người nhận của hội thoại ===
    public function getNguoiNhanCuaHoiThoai($maHoiThoai) {
        $sql = "SELECT nd.hoTen
                FROM nguoidung_hoithoai nch
                JOIN nguoidung nd ON nch.maNguoiDung = nd.maNguoiDung
                WHERE nch.maHoiThoai = :maHoiThoai";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['maHoiThoai' => $maHoiThoai]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // === Hiển thị danh sách hội thoại ===
    public function getDanhSachCuocHoiThoaiCuaNguoiDung($maNguoiDung) {
        $sql = "SELECT DISTINCT ch.maHoiThoai, ch.tenHoiThoai, ch.loaiHoiThoai, 
                       (SELECT MAX(tn.thoiGianGui) 
                        FROM tinnhan tn 
                        WHERE tn.maHoiThoai = ch.maHoiThoai) AS thoiGianMoiNhat
                FROM nguoidung_hoithoai nch
                JOIN cuochoithoai ch ON nch.maHoiThoai = ch.maHoiThoai
                WHERE nch.maNguoiDung = :maNguoiDung
                GROUP BY ch.maHoiThoai, ch.tenHoiThoai, ch.loaiHoiThoai
                ORDER BY thoiGianMoiNhat DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['maNguoiDung' => $maNguoiDung]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // === Lấy tin nhắn theo hội thoại ===
    public function getTinNhanTheoCuocHoiThoai($maHoiThoai) {
        $sql = "SELECT tn.*, nd.hoTen AS tenNguoiGui
                FROM tinnhan tn
                JOIN nguoidung nd ON tn.maNguoiDung = nd.maNguoiDung
                WHERE tn.maHoiThoai = :maHoiThoai
                ORDER BY tn.thoiGianGui ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['maHoiThoai' => $maHoiThoai]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // === Thêm tin nhắn ===
    public function themTinNhan($maHoiThoai, $tieuDe, $noiDung, $maNguoiGui, $filePath = null) {
        $sql = "INSERT INTO tinnhan (maHoiThoai, tieuDe, noiDung, maNguoiDung, fileDinhKem)
                VALUES (:maHoiThoai, :tieuDe, :noiDung, :maNguoiDung, :fileDinhKem)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'maHoiThoai' => $maHoiThoai,
            'tieuDe' => $tieuDe,
            'noiDung' => $noiDung,
            'maNguoiDung' => $maNguoiGui,
            'fileDinhKem' => $filePath
        ]);
    }

    // === Tạo cuộc hội thoại mới ===
    public function taoCuocHoiThoai($tenHoiThoai, $loaiHoiThoai, $maNguoiDung) {
        $sql = "INSERT INTO cuochoithoai (tenHoiThoai, loaiHoiThoai, maNguoiDung)
                VALUES (:tenHoiThoai, :loaiHoiThoai, :maNguoiDung)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'tenHoiThoai' => $tenHoiThoai,
            'loaiHoiThoai' => $loaiHoiThoai,
            'maNguoiDung' => $maNguoiDung
        ]);
        return $this->conn->lastInsertId();
    }

    // === Lấy học sinh hoặc phụ huynh theo lớp ===
    public function getNguoiNhanTheoLop($loai, $tenLop) {
        
        if ($loai === 'hoc_sinh') {
            $sql = "SELECT hs.maHocSinh AS id, nd.hoTen, l.tenLop
                    FROM hocsinh hs
                    JOIN nguoidung nd ON hs.maNguoiDung = nd.maNguoiDung
                    JOIN lophoc l ON hs.maLop = l.maLop
                    WHERE l.tenLop = :lop";
                        
        } else { // $loai === 'phu_huynh'
            $sql = "SELECT 
                        ph.maPhuHuynh AS id,  nd_ph.hoTen, hs.maHocSinh, nd_hs.hoTen AS tenHocSinh, l.tenLop, nd_ph.email, nd_ph.soDienThoai
                    FROM hocsinh hs
                    JOIN lophoc l ON hs.maLop = l.maLop
                    JOIN phuhuynh ph ON hs.maPhuHuynh = ph.maPhuHuynh
                    JOIN nguoidung nd_ph ON ph.maNguoiDung = nd_ph.maNguoiDung
                    JOIN nguoidung nd_hs ON hs.maNguoiDung = nd_hs.maNguoiDung
                    WHERE l.tenLop = :lop";
        }
        
        // Thực thi truy vấn
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['lop' => $tenLop]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Nếu có lỗi SQL, trả về một mảng rỗng và ghi lại lỗi
            error_log("Lỗi SQL trong getNguoiNhanTheoLop: " . $e->getMessage());
            return [];
        }
    }

    // === Lấy phụ huynh theo lớp ===
    public function getPhuHuynhTheoLop($tenLop) {
        $sql = "SELECT ph.maPhuHuynh, nd_ph.hoTen AS tenPhuHuynh, nd_hs.hoTen AS tenHocSinh, nd_ph.email, nd_ph.soDienThoai
                FROM hocsinh hs
                JOIN lophoc l ON l.maLop = hs.maLop
                JOIN phuhuynh ph ON hs.maPhuHuynh = ph.maPhuHuynh
                JOIN nguoidung nd_ph ON nd_ph.maNguoiDung = ph.maNguoiDung
                JOIN nguoidung nd_hs ON nd_hs.maNguoiDung = hs.maNguoiDung
                WHERE l.tenLop = :lop";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['lop' => $tenLop]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
