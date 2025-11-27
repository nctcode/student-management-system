<?php
require_once 'models/KetQuaHocTapModel.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


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


    // Hiển thị dễ đọc khi xuất excel
    function hienThiExcel($text)
    {
        switch ($text) {
            case 'KHA':
                return 'Khá';
            case 'GIOI':
                return 'Giỏi';
            case 'TRUNG_BINH':
                return 'Trung bình';
            case 'TOT':
                return 'Tốt';
            case 'HOAN_THANH':
                return 'Hoàn thành';
            case 'CHUA_HOAN_THANH':
                return 'Chưa hoàn thành';
            default:
                return '';
        }
    }


    // xuất Excel
    public function xuatExcel()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $maNguoiDung = $_SESSION['user']['maNguoiDung'];
        $hocKy = $_GET['hocKy'] ?? '';
        if (empty($hocKy)) die('Vui lòng chọn học kỳ.');

        $hocSinh = $this->model->getHocSinhByGiaoVien($maNguoiDung);
        $monHoc = $this->model->getMonHocByGiaoVien($maNguoiDung);

        $ketQua = $this->model->getDiemTBTheoMon($maNguoiDung, $hocKy);
        $diemTB_HS = $ketQua['diemTB_HS'] ?? [];

        $chiTietDiem = [];
        foreach ($hocSinh as $hs) {
            $maHS = $hs['maHocSinh'];
            $chiTietDiem[$maHS] = $this->model->getChiTietDiem($maHS, $hocKy);
            foreach ($monHoc as $m) {
                $maMH = $m['maMonHoc'];
                $chiTietDiem[$maHS][$maMH]['DIEM_TB'] = $diemTB_HS[$maHS][$maMH] ?? 0;
            }
        }

        $dataHK_HK = $this->model->getThongKeTheoHocLucHanhKiem($maNguoiDung, $hocKy, 'hocluchanhkiem')['data'] ?? [];
        $hkMap = [];
        foreach ($dataHK_HK as $hk) {
            if (isset($hk['maHocSinh'])) $hkMap[$hk['maHocSinh']] = $hk;
        }

        $spreadsheet = new Spreadsheet();

        foreach ($hocSinh as $index => $hs) {
            $sheet = ($index === 0) ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle(substr($hs['hoTen'], 0, 31));

            $row = 1;
            $sheet->setCellValue("A$row", "Học sinh: {$hs['hoTen']}")
                ->setCellValue("B$row", "Lớp: {$hs['tenLop']}");
            $row += 2;

            // Header
            $headers = ['Môn', 'Miệng', '15 phút', '1 tiết', 'Giữa kỳ', 'Cuối kỳ', 'Trung Bình'];
            $sheet->fromArray($headers, NULL, "A$row");

            // Bôi đậm header
            $sheet->getStyle("A$row:G$row")->getFont()->setBold(true);
            $sheet->getStyle("A$row:G$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;

            foreach ($monHoc as $m) {
                $maMH = $m['maMonHoc'];
                $ct = $chiTietDiem[$hs['maHocSinh']][$maMH] ?? [];
                $sheet->fromArray([
                    $m['tenMonHoc'],
                    $ct['MIENG'] ?? 0,
                    $ct['15_PHUT'] ?? 0,
                    $ct['1_TIET'] ?? 0,
                    $ct['GIUA_KY'] ?? 0,
                    $ct['CUOI_KY'] ?? 0,
                    $ct['DIEM_TB'] ?? 0
                ], NULL, "A$row");
                $row++;
            }

            // Trung bình học kỳ
            $diemTB_mon = array_map(fn($m) => $chiTietDiem[$hs['maHocSinh']][$m['maMonHoc']]['DIEM_TB'] ?? 0, $monHoc);
            $tbMon = count($diemTB_mon) ? round(array_sum($diemTB_mon) / count($diemTB_mon), 2) : 0;
            $sheet->fromArray(["Trung bình học kỳ", $tbMon], NULL, "A$row");
            $row++;

            // Học lực/hạnh kiểm
            // Học lực/hạnh kiểm
            $hkData = $hkMap[$hs['maHocSinh']] ?? ['hocLuc' => '', 'hanhKiem' => '', 'xepLoai' => ''];
            $sheet->fromArray(["Học lực", $this->hienThiExcel($hkData['hocLuc'])], NULL, "A$row");
            $row++;
            $sheet->fromArray(["Hạnh kiểm", $this->hienThiExcel($hkData['hanhKiem'])], NULL, "A$row");
            $row++;
            $sheet->fromArray(["Loại",$this->hienThiExcel($hkData['xepLoai'])], NULL, "A$row");
            $row++;


            // Tự động điều chỉnh width cột
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "ThongKe_HocKy_{$hocKy}.xlsx";

        // Xóa buffer trước khi gửi file
        if (ob_get_contents()) ob_end_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
