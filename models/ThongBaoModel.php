<?php
// models/ThongBaoModel.php

class ThongBaoModel {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=127.0.0.1;dbname=qlhs;charset=utf8mb4", "root", "");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Kết nối database thất bại: " . $e->getMessage());
        }
    }

    // Thêm thông báo mới - ĐÃ SỬA HOÀN TOÀN
    public function themThongBao($tieuDe, $noiDung, $maNguoiGui, $nguoiNhan = 'TAT_CA', $fileDinhKem = null, 
                                $thoiGianKetThuc = null, $maLop = null, $maKhoi = null, $maMonHoc = null,
                                $loaiThongBao = 'CHUNG', $uuTien = 'TRUNG_BINH') {
        error_log("=== MODEL themThongBao (UPDATED) ===");
        
        // Kiểm tra bảng tồn tại
        $checkTable = "SHOW TABLES LIKE 'thongbao'";
        $stmt = $this->pdo->query($checkTable);
        if (!$stmt->fetch()) {
            error_log("Table thongbao does not exist!");
            return false;
        }
        
        $sql = "INSERT INTO thongbao (tieuDe, noiDung, maNguoiGui, nguoiNhan, fileDinhKem, 
                                    thoiGianKetThuc, maLop, maKhoi, maMonHoc, loaiThongBao, uuTien) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            
            // Xử lý thời gian kết thúc
            if ($thoiGianKetThuc) {
                $thoiGianKetThuc = date('Y-m-d H:i:s', strtotime($thoiGianKetThuc));
            }
            
            $params = [
                $tieuDe, 
                $noiDung, 
                $maNguoiGui, 
                $nguoiNhan, 
                $fileDinhKem,
                $thoiGianKetThuc,
                $maLop,
                $maKhoi,
                $maMonHoc,
                $loaiThongBao,
                $uuTien
            ];
            
            error_log("SQL: " . $sql);
            error_log("Params: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if ($result) {
                $lastId = $this->pdo->lastInsertId();
                error_log("Insert successful, last ID: " . $lastId);
                
                // Verify the record was inserted
                $verifySql = "SELECT * FROM thongbao WHERE maThongBao = ?";
                $verifyStmt = $this->pdo->prepare($verifySql);
                $verifyStmt->execute([$lastId]);
                $record = $verifyStmt->fetch(PDO::FETCH_ASSOC);
                
                error_log("Verified record: " . ($record ? 'FOUND' : 'NOT FOUND'));
                if ($record) {
                    error_log("Record details: " . print_r($record, true));
                }
                
                return $lastId; // TRẢ VỀ ID THAY VÌ TRUE/FALSE
            } else {
                error_log("Execute failed");
                $errorInfo = $stmt->errorInfo();
                error_log("PDO Error: " . print_r($errorInfo, true));
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDO Exception in themThongBao: " . $e->getMessage());
            error_log("Error Code: " . $e->getCode());
            return false;
        }
    }

    // Lấy tất cả thông báo (cho QTV và BGH)
    public function layTatCaThongBao() {
        $sql = "SELECT tb.*, nd.hoTen as tenNguoiGui
                FROM thongbao tb 
                LEFT JOIN nguoidung nd ON tb.maNguoiGui = nd.maNguoiDung 
                ORDER BY 
                    CASE tb.uuTien 
                        WHEN 'KHAN_CAP' THEN 1
                        WHEN 'CAO' THEN 2
                        WHEN 'TRUNG_BINH' THEN 3
                        WHEN 'THAP' THEN 4
                    END,
                    tb.ngayGui DESC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông báo: " . $e->getMessage());
            return [];
        }
    }

    // Lấy thông báo theo người nhận
    public function layThongBaoTheoNguoiNhan($nguoiNhan) {
        $sql = "SELECT tb.*, nd.hoTen as tenNguoiGui
                FROM thongbao tb 
                LEFT JOIN nguoidung nd ON tb.maNguoiGui = nd.maNguoiDung 
                WHERE (tb.nguoiNhan = ? OR tb.nguoiNhan = 'TAT_CA')
                AND (tb.thoiGianKetThuc IS NULL OR tb.thoiGianKetThuc >= NOW())
                ORDER BY 
                    CASE tb.uuTien 
                        WHEN 'KHAN_CAP' THEN 1
                        WHEN 'CAO' THEN 2
                        WHEN 'TRUNG_BINH' THEN 3
                        WHEN 'THAP' THEN 4
                    END,
                    tb.ngayGui DESC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nguoiNhan]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông báo theo người nhận: " . $e->getMessage());
            return [];
        }
    }

    // Lấy thông báo theo mã - SỬA LẠI
    public function layThongBaoTheoMa($maThongBao) {
        error_log("=== LAY THONG BAO THEO MA ===");
        error_log("maThongBao: " . $maThongBao);
        
        $sql = "SELECT tb.*, nd.hoTen as tenNguoiGui
                FROM thongbao tb 
                LEFT JOIN nguoidung nd ON tb.maNguoiGui = nd.maNguoiDung 
                WHERE tb.maThongBao = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$maThongBao]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("Query result: " . ($result ? 'FOUND' : 'NOT FOUND'));
            if ($result) {
                error_log("Thong bao data: " . print_r($result, true));
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông báo theo mã: " . $e->getMessage());
            return null;
        }
    }

    // Cập nhật trạng thái thông báo
    public function capNhatTrangThai($maThongBao, $trangThai) {
        $sql = "UPDATE thongbao SET trangThai = ? WHERE maThongBao = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$trangThai, $maThongBao]);
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật trạng thái: " . $e->getMessage());
            return false;
        }
    }

    // Xóa thông báo
    public function xoaThongBao($maThongBao) {
        $sql = "DELETE FROM thongbao WHERE maThongBao = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$maThongBao]);
        } catch (PDOException $e) {
            error_log("Lỗi xóa thông báo: " . $e->getMessage());
            return false;
        }
    }

    // Đếm số thông báo chưa đọc
    public function demThongBaoChuaDoc($nguoiNhan) {
        $sql = "SELECT COUNT(*) as soLuong 
                FROM thongbao 
                WHERE (nguoiNhan = ? OR nguoiNhan = 'TAT_CA') 
                AND trangThai = 'Chưa xem'
                AND (thoiGianKetThuc IS NULL OR thoiGianKetThuc >= NOW())";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nguoiNhan]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['soLuong'] ?? 0;
        } catch (PDOException $e) {
            error_log("Lỗi đếm thông báo chưa đọc: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy danh sách lớp học
    public function layDanhSachLop() {
        $sql = "SELECT * FROM lophoc ORDER BY tenLop";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách lớp: " . $e->getMessage());
            return [];
        }
    }

    // Lấy danh sách khối
    public function layDanhSachKhoi() {
        $sql = "SELECT * FROM khoi ORDER BY tenKhoi";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách khối: " . $e->getMessage());
            return [];
        }
    }

    // Lấy danh sách môn học
    public function layDanhSachMonHoc() {
        $sql = "SELECT * FROM monhoc ORDER BY tenMonHoc";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách môn học: " . $e->getMessage());
            return [];
        }
    }
}
?>