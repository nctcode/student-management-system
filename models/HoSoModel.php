<?php
require_once 'models/Database.php'; 

class HoSoModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection(); 
    }

    // Hàm lấy thông tin hồ sơ theo mã hồ sơ (đầy đủ thông tin)
    public function getHoSoByMa($maHoSo)
    {
        $sql = "SELECT 
                    maHoSo,
                    hoTen,
                    gioiTinh,
                    ngaySinh,
                    noiSinh,
                    danToc,
                    tonGiao,
                    quocTich,
                    diaChiThuongTru,
                    noiOHienNay,
                    soDienThoaiHocSinh,
                    soDienThoaiPhuHuynh,
                    email,
                    hoTenCha,
                    namSinhCha,
                    ngheNghiepCha,
                    dienThoaiCha,
                    noiCongTacCha,
                    hoTenMe,
                    namSinhMe,
                    ngheNghiepMe,
                    dienThoaiMe,
                    noiCongTacMe,
                    hoTenNguoiGiamHo,
                    namSinhNguoiGiamHo,
                    ngheNghiepNguoiGiamHo,
                    dienThoaiNguoiGiamHo,
                    noiCongTacNguoiGiamHo,
                    truongTHCS,
                    diaChiTruongTHCS,
                    namTotNghiep,
                    xepLoaiHocLuc,
                    xepLoaiHanhKiem,
                    diemTB_Lop9,
                    diemThiTuyenSinh,
                    nguyenVong1,
                    nguyenVong2,
                    nguyenVong3,
                    nganhHoc,
                    hinhThucTuyenSinh,
                    banSaoGiayKhaiSinh,
                    banSaoHoKhau,
                    hocBaTHCS,
                    giayChungNhanTotNghiep,
                    anh34,
                    giayXacNhanUuTien,
                    maBan,
                    trangThai,
                    ngayDangKy,
                    ketQua,
                    ghiChu
                FROM hosotuyensinh
                WHERE maHoSo = :maHoSo";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['maHoSo' => $maHoSo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>