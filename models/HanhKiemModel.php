<?php
class HanhKiemModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        // [ĐÃ SỬA]: 
        // 1. JOIN bằng cột 'maNguoiDung'
        // 2. Lấy cột 'hoTen'
        $query = "SELECT hk.*, hk.id AS id_hanh_kiem, nd.maNguoiDung, nd.hoTen 
                  FROM hanh_kiem hk 
                  JOIN nguoidung nd ON hk.sinh_vien_id = nd.maNguoiDung 
                  ORDER BY hk.id DESC";
        $result = $this->conn->query($query);
        return $result;
    }

    public function getDanhSachHocSinh() {
        // [ĐÃ SỬA]: 
        // 1. Tìm theo cột 'loaiNguoiDung'
        // 2. Giá trị là 'HOCSINH' (theo enum trong ảnh của bạn)
        $query = "SELECT maNguoiDung, hoTen 
                  FROM nguoidung 
                  WHERE loaiNguoiDung = 'HOCSINH'"; 
        return $this->conn->query($query);
    }

    public function create($sv_id, $hoc_ky, $diem, $xep_loai, $nhan_xet) {
        $query = "INSERT INTO hanh_kiem (sinh_vien_id, hoc_ky, diem_so, xep_loai, nhan_xet) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isiss", $sv_id, $hoc_ky, $diem, $xep_loai, $nhan_xet);
        return $stmt->execute();
    }
}
?>