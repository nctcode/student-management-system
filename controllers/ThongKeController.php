<?php
require_once 'models/ThongKeModel.php';

class ThongKeController {
    private $model;

    public function __construct() {
        $this->model = new ThongKeModel();
    }

    public function index() {
        $hocKy = isset($_GET['hk']) ? $_GET['hk'] : '1';
        $maKhoi = isset($_GET['maKhoi']) ? $_GET['maKhoi'] : 'all';
        $maLop = isset($_GET['maLop']) ? $_GET['maLop'] : 'all';
        $tab = isset($_GET['loaiBaoCao']) ? $_GET['loaiBaoCao'] : 'hocLuc';
        
        $mapTab = ['hocLuc'=>'hoctap', 'hanhKiem'=>'hanhkiem', 'nhanSu'=>'nhansu', 'quyMo'=>'quymo', 'taiChinh'=>'taichinh'];
        $activeTab = $mapTab[$tab] ?? 'hoctap';

        // Lấy dữ liệu từ Model
        $phoDiem = $this->model->getPhoDiem($hocKy, $maKhoi, $maLop);
        $jsonPhoDiem = json_encode(array_values($phoDiem));
        $duBaoTN = $this->model->getDuBaoTotNghiep($hocKy);
        $hanhKiem = $this->model->getThongKeHanhKiem($hocKy, $maKhoi, $maLop);
        $jsonHanhKiem = json_encode(array_values($hanhKiem));
        $taiChinhKPI = $this->model->getTaiChinhOverview($hocKy, $maKhoi, $maLop);
        
        $duLieuSoSanh = $this->model->getSoSanhHocLuc('2024-2025', $maKhoi, $maLop);
        $dataSS_HK1 = []; $dataSS_HK2 = []; $tempMap = []; 
        foreach($duLieuSoSanh as $r) $tempMap[$r['hocLuc']] = $r;
        foreach(['KEM', 'YEU', 'TRUNG_BINH', 'KHA', 'GIOI'] as $k) {
            $dataSS_HK1[] = $tempMap[$k]['SL_HK1'] ?? 0;
            $dataSS_HK2[] = $tempMap[$k]['SL_HK2'] ?? 0;
        }
        $jsonSS_HK1 = json_encode($dataSS_HK1);
        $jsonSS_HK2 = json_encode($dataSS_HK2);

        $gvTaiCongViec = $this->model->getTaiCongViecGiaoVien();
        $siSoKhoi = $this->model->getSiSoTrungBinh();
        $doanhThuChart = $this->model->getDoanhThuTheoThang();
        $topLopNo = $this->model->getTopLopNoHocPhi($hocKy);
        $labelsDT = []; $dataDT = [];
        foreach($doanhThuChart as $d) { $labelsDT[] = $d['thang']; $dataDT[] = $d['doanhThu']; }
        $jsonLabelsDT = json_encode($labelsDT);
        $jsonDataDT = json_encode($dataDT);

        $danhSachKhoi = $this->model->getAllKhoi();
        $danhSachLop = $this->model->getAllLopWithKhoi();

        require_once 'views/thongke/index.php';
    }

    public function export() {
        $type = $_GET['loaiBaoCao'] ?? 'hocLuc';
        $hocKy = $_GET['hk'] ?? '1';
        $maKhoi = $_GET['maKhoi'] ?? 'all';
        $maLop = $_GET['maLop'] ?? 'all';

        $data = $this->model->getDataExport($type, 'HK'.$hocKy, $maKhoi, $maLop);
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