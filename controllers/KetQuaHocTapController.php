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

    // Trang hiển thị thống kê kết quả học tập
    public function thongke()
    {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $hocKy = $_GET['hocKy'] ?? '';
        $tieuChi = $_GET['tieuChi'] ?? '';

        // --- KHỞI TẠO BIẾN MẶC ĐỊNH ---
        $data = [];
        $hocSinh = [];
        $monHoc = [];
        $diemTB_HS = [];
        $diemTB_Lop = [];
        $chiTietDiem = [];
        $tongHocLuc = [];
        $tongHanhKiem = [];

        // --- NẾU ĐÃ CHỌN HỌC KỲ ---
        if (!empty($hocKy)) {

            // 🔹 1. THỐNG KÊ HỌC LỰC & HẠNH KIỂM
            if ($tieuChi === 'hocluchanhkiem' || $tieuChi === 'tatca') {
                $thongKe = $this->model->getThongKeTheoHocLucHanhKiem($maNguoiDung, $hocKy, 'hocluchanhkiem');
                $data = $thongKe['data'];
                $tongHocLuc = $thongKe['thongKeHocLuc'];
                $tongHanhKiem = $thongKe['thongKeHanhKiem'];
            }

            // 🔹 2. THỐNG KÊ THEO ĐIỂM
            if ($tieuChi === 'diem' || $tieuChi === 'tatca') {
                $ketQua = $this->model->getDiemTBTheoMon($maNguoiDung, $hocKy);

                $hocSinh = $ketQua['hocSinh'] ?? [];
                $monHoc = $ketQua['monHoc'] ?? [];
                $diemTB_HS = $ketQua['diemTB_HS'] ?? [];
                $diemTB_Lop = $ketQua['diemTB_Lop'] ?? [];

                // Lấy chi tiết điểm cho từng học sinh
                foreach ($hocSinh as $hs) {
                    $maHS = $hs['maHocSinh'];
                    $chiTietDiem[$maHS] = $this->model->getChiTietDiem($maHS, $hocKy);

                    // Bổ sung điểm TB vào chi tiết
                    foreach ($monHoc as $m) {
                        $maMH = $m['maMonHoc'];
                        $chiTietDiem[$maHS][$maMH]['DIEM_TB'] = $diemTB_HS[$maHS][$maMH] ?? 0;
                    }
                }
            }
        }

        // --- GỌI VIEW ---
        require 'views/ketquahoctap/thongke.php';
    }
    public function xuatCSV()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $hocKy = $_GET['hocKy'] ?? '';
        $tieuChi = $_GET['tieuChi'] ?? '';

        if (empty($hocKy) || empty($tieuChi)) {
            die('Vui lòng chọn học kỳ và tiêu chí.');
        }

        require_once 'models/KetQuaHocTapModel.php';
        $model = new KetQuaHocTapModel();

        // --- Lấy danh sách học sinh có lớp ---
        $hocSinh = $model->getHocSinhByGiaoVien($maNguoiDung);

        // --- Lấy danh sách môn học ---
        $monHoc = $model->getMonHocByGiaoVien($maNguoiDung);

        // --- Lấy điểm trung bình và chi tiết điểm ---
        $ketQua = $model->getDiemTBTheoMon($maNguoiDung, $hocKy);
        $diemTB_HS = $ketQua['diemTB_HS'] ?? [];

        $chiTietDiem = [];
        foreach ($hocSinh as $hs) {
            $maHS = $hs['maHocSinh'];
            $chiTietDiem[$maHS] = $model->getChiTietDiem($maHS, $hocKy);
            // Thêm điểm trung bình từng môn
            foreach ($monHoc as $m) {
                $maMH = $m['maMonHoc'];
                $chiTietDiem[$maHS][$maMH]['DIEM_TB'] = $diemTB_HS[$maHS][$maMH] ?? 0;
            }
        }

        // --- Lấy học lực & hạnh kiểm ---
        $dataHK_HK = $model->getThongKeTheoHocLucHanhKiem($maNguoiDung, $hocKy, 'hocluchanhkiem')['data'] ?? [];
        $hkMap = [];
        foreach ($dataHK_HK as $hk) {
            if (isset($hk['maHocSinh'])) {
                $hkMap[$hk['maHocSinh']] = $hk;
            }
        }

        // --- Tên file ---
        $filename = "ThongKe_HocKy_{$hocKy}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        foreach ($hocSinh as $hs) {
            $maHS = $hs['maHocSinh'];
            $tenHS = $hs['hoTen'];
            $lop = $hs['tenLop'] ?? '';

            // Tiêu đề học sinh
            fputcsv($output, ["Học sinh: $tenHS", "Lớp: $lop"]);
            fputcsv($output, []); // dòng trống

            // Header bảng điểm
            $header = ["Môn", "Miệng", "15 phút", "1 tiết", "Giữa kỳ", "Cuối kỳ", "Trung Binh"];
            fputcsv($output, $header);

            // Dữ liệu từng môn
            foreach ($monHoc as $m) {
                $maMH = $m['maMonHoc'];
                $ct = $chiTietDiem[$maHS][$maMH] ?? [];
                fputcsv($output, [
                    $m['tenMonHoc'],
                    $ct['MIENG'] ?? 0,
                    $ct['15_PHUT'] ?? 0,
                    $ct['1_TIET'] ?? 0,
                    $ct['GIUA_KY'] ?? 0,
                    $ct['CUOI_KY'] ?? 0,
                    $ct['DIEM_TB'] ?? 0
                ]);
            }

            // Trung bình học kỳ
            $diemTB_mon = array_map(function ($m) use ($chiTietDiem, $maHS) {
                return $chiTietDiem[$maHS][$m['maMonHoc']]['DIEM_TB'] ?? 0;
            }, $monHoc);
            $tbMon = count($diemTB_mon) ? round(array_sum($diemTB_mon) / count($diemTB_mon), 2) : 0;
            fputcsv($output, ["Trung bình học kỳ (TB tổng môn)", $tbMon]);

            // Học lực & hạnh kiểm & loại
            $hkData = $hkMap[$maHS] ?? ['hocLuc' => '', 'hanhKiem' => '', 'xepLoai' => ''];
            fputcsv($output, ["Học lực (chú thích: điểm tổng theo môn)", $hkData['hocLuc']]);
            fputcsv($output, ["Hạnh kiểm (chú thích: thái độ, nề nếp)", $hkData['hanhKiem']]);
            fputcsv($output, ["Loại (chú thích: xếp loại cuối kỳ)", $hkData['xepLoai']]);

            // Dòng trống tách học sinh
            for ($i = 0; $i < 5; $i++) {
                fputcsv($output, []);
            }
        }

        fclose($output);
        exit;
    }
}
