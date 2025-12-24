<?php
require_once 'models/ThongKeModel.php';

class ThongKeController {
    private $model;

    public function __construct() {
        $this->model = new ThongKeModel();
    }

    public function index() {
        // KIỂM TRA SESSION ĐỂ LẤY MÃ TRƯỜNG
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Nếu không có mã trường (chưa đăng nhập hoặc lỗi), gán mặc định hoặc xử lý lỗi
        $maTruong = isset($_SESSION['user']['maTruong']) ? $_SESSION['user']['maTruong'] : 0;

        $hocKy = isset($_GET['hk']) ? $_GET['hk'] : '1';
        $maKhoi = isset($_GET['maKhoi']) ? $_GET['maKhoi'] : 'all';
        $maLop = isset($_GET['maLop']) ? $_GET['maLop'] : 'all';
        $tab = isset($_GET['loaiBaoCao']) ? $_GET['loaiBaoCao'] : 'hocLuc';
        
        $mapTab = ['hocLuc'=>'hoctap', 'hanhKiem'=>'hanhkiem', 'nhanSu'=>'nhansu', 'quyMo'=>'quymo', 'taiChinh'=>'taichinh'];
        $activeTab = $mapTab[$tab] ?? 'hoctap';

        // TRUYỀN $maTruong VÀO CÁC HÀM MODEL
        $phoDiem = $this->model->getPhoDiem($hocKy, $maKhoi, $maLop, $maTruong);
        $jsonPhoDiem = json_encode(array_values($phoDiem));
        
        $duBaoTN = $this->model->getDuBaoTotNghiep($hocKy, $maTruong);
        
        $hanhKiem = $this->model->getThongKeHanhKiem($hocKy, $maKhoi, $maLop, $maTruong);
        $jsonHanhKiem = json_encode(array_values($hanhKiem));
        
        $taiChinhKPI = $this->model->getTaiChinhOverview($hocKy, $maKhoi, $maLop, $maTruong);
        
        $duLieuSoSanh = $this->model->getSoSanhHocLuc('2024-2025', $maKhoi, $maLop, $maTruong);
        $dataSS_HK1 = []; $dataSS_HK2 = []; $tempMap = []; 
        foreach($duLieuSoSanh as $r) $tempMap[$r['hocLuc']] = $r;
        foreach(['KEM', 'YEU', 'TRUNG_BINH', 'KHA', 'GIOI'] as $k) {
            $dataSS_HK1[] = $tempMap[$k]['SL_HK1'] ?? 0;
            $dataSS_HK2[] = $tempMap[$k]['SL_HK2'] ?? 0;
        }
        $jsonSS_HK1 = json_encode($dataSS_HK1);
        $jsonSS_HK2 = json_encode($dataSS_HK2);

        $gvTaiCongViec = $this->model->getTaiCongViecGiaoVien($maTruong);
        $siSoKhoi = $this->model->getSiSoTrungBinh($maTruong);
        $doanhThuChart = $this->model->getDoanhThuTheoThang($maTruong);
        $topLopNo = $this->model->getTopLopNoHocPhi($hocKy, $maTruong);
        
        $labelsDT = []; $dataDT = [];
        foreach($doanhThuChart as $d) { $labelsDT[] = $d['thang']; $dataDT[] = $d['doanhThu']; }
        $jsonLabelsDT = json_encode($labelsDT);
        $jsonDataDT = json_encode($dataDT);

        // Lấy danh sách lớp thuộc trường này
        $danhSachKhoi = $this->model->getAllKhoi(); // Khối thường dùng chung
        $danhSachLop = $this->model->getAllLopWithKhoi($maTruong);

        // Lấy KPI tổng quan theo trường
        $kpiData = $this->model->getKPIs($maTruong); // Giả sử bạn muốn dùng biến này ở View

        require_once 'views/thongke/index.php';
    }

    public function export() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $maTruong = isset($_SESSION['user']['maTruong']) ? $_SESSION['user']['maTruong'] : 0;

        $type = $_GET['loaiBaoCao'] ?? 'hocLuc';
        $hocKy = $_GET['hk'] ?? '1';
        $maKhoi = $_GET['maKhoi'] ?? 'all';
        $maLop = $_GET['maLop'] ?? 'all';

        // Truyền $maTruong vào hàm export
        $data = $this->model->getDataExport($type, 'HK'.$hocKy, $maKhoi, $maLop, $maTruong);
        
        $filename = "BaoCao_" . ucfirst($type) . "_" . date('Ymd_His') . ".xls";
        
        if (ob_get_level()) ob_end_clean();
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo '<meta http-equiv="content-type" content="text/plain; charset=UTF-8"/>';
        echo '<table border="1"><tr><th>STT</th>';
        if(!empty($data)) {
            foreach(array_keys($data[0]) as $k) echo '<th>'.mb_strtoupper($k, 'UTF-8').'</th>';
            echo '</tr>'; $i=1;
            foreach($data as $r) { echo '<tr><td>'.$i++.'</td>'; foreach($r as $c) echo '<td>'.$c.'</td>'; echo '</tr>'; }
        } else { echo '<td>Không có dữ liệu</td></tr>'; }
        echo '</table>';
        exit;
    }
}
?>