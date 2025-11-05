<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/giaovien.php';

$hocKy = $_GET['hocKy'] ?? '';
$tieuChi = $_GET['tieuChi'] ?? '';
?>

<title>Thống kê kết quả học tập</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="assets/css/thongkeketquahoctap.css">

<div class="content">
  <div class="mb-4">
    <h1>Thống kê kết quả học tập</h1>
  </div>

  <!-- Chọn học kỳ, tiêu chí và nút xuất Excel -->
  <div class="row align-items-end mb-4">
    <!-- Cột chọn học kỳ -->
    <div class="col-md-3">
      <label for="selectHocKy" class="form-label">Chọn học kỳ:</label>
      <select id="selectHocKy" class="form-select"
        onchange="window.location='?controller=ketquahoctap&action=thongke&hocKy='+this.value+'&tieuChi=<?= $tieuChi ?>';">
        <option value="">-- Chọn học kỳ --</option>
        <option value="HK1" <?= ($hocKy === 'HK1') ? 'selected' : '' ?>>Học kỳ 1</option>
        <option value="HK2" <?= ($hocKy === 'HK2') ? 'selected' : '' ?>>Học kỳ 2</option>
      </select>
    </div>

    <!-- Cột chọn tiêu chí -->
    <div class="col-md-3">
      <label for="selectTieuChi" class="form-label">Chọn tiêu chí:</label>
      <select id="selectTieuChi" class="form-select"
        onchange="window.location='?controller=ketquahoctap&action=thongke&hocKy=<?= $hocKy ?>&tieuChi='+this.value;">
        <option value="">-- Chọn tiêu chí --</option>
        <option value="diem" <?= ($tieuChi === 'diem') ? 'selected' : '' ?>>Điểm</option>
        <option value="hocluchanhkiem" <?= ($tieuChi === 'hocluchanhkiem') ? 'selected' : '' ?>>Học lực/Hạnh kiểm</option>
        <option value="tatca" <?= ($tieuChi === 'tatca') ? 'selected' : '' ?>>Tất cả</option>
      </select>
    </div>

    <!-- Cột nút xuất Excel -->
    <div class="col-md-3">
      <?php if ($hocKy && $tieuChi): ?>
        <a href="?controller=ketquahoctap&action=xuatCSV&hocKy=<?= $hocKy ?>&tieuChi=<?= $tieuChi ?>" class="btn btn-success">
          <i class="bi bi-file-earmark-excel"></i> Xuất CSV
        </a>
      <?php endif; ?>
    </div>
  </div>


  <!-- Card thống kê -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Số học sinh</h5>
          <p class="card-text">
            <?= ($hocKy && $tieuChi) ? count(!empty($hocSinh) ? $hocSinh : $data) : '-' ?>
          </p>

        </div>
      </div>
    </div>

    <!-- Card điểm TB lớp (chỉ hiển thị khi tiêu chí là điểm) -->
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Điểm TB lớp</h5>
          <p class="card-text">
            <?= ($hocKy && ($tieuChi === 'diem' || $tieuChi === 'tatca')) ? (!empty($diemTB_Lop) ? round(array_sum($diemTB_Lop) / count($diemTB_Lop), 2) : 0) : '-' ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Card học lực -->
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Thống kê học lực</h5>
          <p class="card-text">
            <?php if ($hocKy && ($tieuChi === 'hocluchanhkiem' || $tieuChi === 'tatca')): ?>
              <?php foreach ($tongHocLuc as $hl => $soHS): ?>
                <?= htmlspecialchars($hl) ?>: <?= $soHS ?> HS<br>
              <?php endforeach; ?>
            <?php else: ?> - <?php endif; ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Card hạnh kiểm -->
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Thống kê hạnh kiểm</h5>
          <p class="card-text">
            <?php if ($hocKy && ($tieuChi === 'hocluchanhkiem' || $tieuChi === 'tatca')): ?>
              <?php foreach ($tongHanhKiem as $hk => $soHS): ?>
                <?= htmlspecialchars($hk) ?>: <?= $soHS ?> HS<br>
              <?php endforeach; ?>
            <?php else: ?> - <?php endif; ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- BẢNG DỮ LIỆU -->
  <?php if ($hocKy && ($tieuChi === 'diem' || $tieuChi === 'tatca')): ?>
    <!-- Bảng điểm trung bình -->
    <div id="sectionDiem" class="mb-5">
      <h3>Bảng kết quả học tập</h3>
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>STT</th>
            <th>Họ tên</th>
            <?php foreach ($monHoc as $m): ?>
              <th><?= htmlspecialchars($m['tenMonHoc']) ?> (TB)</th>
            <?php endforeach; ?>
            <th>Chi tiết</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($hocSinh)): ?>
            <?php foreach ($hocSinh as $i => $hs): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($hs['hoTen']) ?></td>
                <?php foreach ($monHoc as $m):
                  $diem = $diemTB_HS[$hs['maHocSinh']][$m['maMonHoc']] ?? 0;
                ?>
                  <td><?= $diem ?></td>
                <?php endforeach; ?>
                <td>
                  <button type="button" class="btn btn-sm btn-info toggle-detail" data-mahocsinh="<?= $hs['maHocSinh'] ?>" data-tenhs="<?= htmlspecialchars($hs['hoTen']) ?>">
                    Xem chi tiết
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="<?= count($monHoc) + 3 ?>" class="text-center">Không có dữ liệu</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Khu vực chi tiết -->
    <div id="chiTietContainer" class="mb-5"></div>

    <script>
      const chiTietDiemData = <?= json_encode($chiTietDiem) ?>;

      document.querySelectorAll('.toggle-detail').forEach(button => {
        button.addEventListener('click', () => {
          const maHS = button.getAttribute('data-mahocsinh');
          const tenHS = button.getAttribute('data-tenhs');
          const chiTiet = chiTietDiemData[maHS];

          if (!chiTiet) {
            document.getElementById('chiTietContainer').innerHTML =
              `<p class="text-danger">Chưa có điểm chi tiết cho ${tenHS}</p>`;
            return;
          }

          //Thêm nút Đóng
          let html = `
        <div class="card p-3">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Chi tiết điểm của ${tenHS}</h4>
            <button class="btn btn-sm btn-danger" id="closeDetail">Đóng</button>
          </div>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Môn</th>
                <th>Miệng</th>
                <th>15 phút</th>
                <th>1 tiết</th>
                <th>Giữa kỳ</th>
                <th>Cuối kỳ</th>
                <th>TB</th>
              </tr>
            </thead>
            <tbody>`;

          for (let maMH in chiTiet) {
            const ct = chiTiet[maMH];
            html += `
          <tr>
            <td>${ct.tenMonHoc}</td>
            <td>${ct.MIENG ?? 0}</td>
            <td>${ct['15_PHUT'] ?? 0}</td>
            <td>${ct['1_TIET'] ?? 0}</td>
            <td>${ct.GIUA_KY ?? 0}</td>
            <td>${ct.CUOI_KY ?? 0}</td>
            <td>${ct.DIEM_TB ?? 0}</td>
          </tr>`;
          }

          html += `
            </tbody>
          </table>
        </div>`;

          // Gán vào container
          const container = document.getElementById('chiTietContainer');
          container.innerHTML = html;

          //Xử lý nút Đóng
          document.getElementById('closeDetail').addEventListener('click', () => {
            container.innerHTML = '';
          });

        });
      });
    </script>
  <?php endif; ?>


  <?php if ($hocKy && ($tieuChi === 'hocluchanhkiem' || $tieuChi === 'tatca')): ?>
    <!-- Bảng học lực / hạnh kiểm -->
    <div class="mb-5">
      <h3>Bảng học lực & hạnh kiểm</h3>
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>STT</th>
            <th>Họ tên</th>
            <th>Học lực</th>
            <th>Hạnh kiểm</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($data)): ?>
            <?php foreach ($data as $i => $hs): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($hs['hoTen']) ?></td>
                <td><?= htmlspecialchars($hs['hocLuc']) ?></td>
                <td><?= htmlspecialchars($hs['hanhKiem']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center">Không có dữ liệu</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  <?php elseif (!$hocKy || !$tieuChi): ?>
    <div class="alert alert-info text-center">Vui lòng chọn học kỳ và tiêu chí để hiển thị thống kê.</div>
  <?php endif; ?>
  <?php if ($hocKy && $tieuChi): ?>
    <?php include 'views/ketquahoctap/bieudothongke.php'; ?>
  <?php endif; ?>