<?php
require_once 'models/KetQuaHocTapModel.php';

class KetQuaHocTapController
{
    private $model;

    public function __construct()
    {
        $this->model = new KetQuaHocTapModel();
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    // Trang hiển thị kết quả học tập
    public function thongke()
    {
        // Kiểm tra session người dùng
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];

        // Lấy học kỳ từ GET, mặc định HK1
        $hocKy = $_GET['hocKy'] ?? 'HK1';

        // Lấy dữ liệu điểm trung bình
        $ketQua = $this->model->getDiemTBTheoMon($maNguoiDung, $hocKy);

        $hocSinh = $ketQua['hocSinh'] ?? [];
        $monHoc = $ketQua['monHoc'] ?? [];
        $diemTB_HS = $ketQua['diemTB_HS'] ?? [];
        $diemTB_Lop = $ketQua['diemTB_Lop'] ?? [];

        // Lấy chi tiết điểm cho tất cả học sinh để hiển thị ngay dưới bảng
        $chiTietDiem = [];
        foreach ($hocSinh as $hs) {
            $maHS = $hs['maHocSinh'];
            $chiTietDiem[$maHS] = $this->model->getChiTietDiem($maHS, $hocKy);

            // Thêm luôn điểm trung bình từng môn vào chiTietDiem
            foreach ($monHoc as $m) {
                $maMH = $m['maMonHoc'];
                $chiTietDiem[$maHS][$maMH]['DIEM_TB'] = $diemTB_HS[$maHS][$maMH] ?? 0;
            }
        }


        // Gọi view hiển thị
        require 'views/ketquahoctap/thongke.php';
    }
}
