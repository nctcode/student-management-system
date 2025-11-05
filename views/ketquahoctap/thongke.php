<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/giaovien.php';

$hocKy = $_GET['hocKy'] ?? '';
$tieuChi = $_GET['tieuChi'] ?? '';
?>

<title>Thống kê kết quả học tập</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="assets/css/thongkeketquahoctap.css">

<div class="content">
  <div class="mb-4">
    <h1>Thống kê kết quả học tập</h1>
  </div>

  <!-- Chọn học kỳ và tiêu chí ngang hàng -->
  <div class="row mb-4">
    <div class="col-md-3">
      <label for="selectHocKy" class="form-label">Chọn học kỳ:</label>
      <select id="selectHocKy" class="form-select" onchange="window.location='?controller=ketquahoctap&action=thongke&hocKy='+this.value+'&tieuChi=<?= $tieuChi ?>';">
        <option value="">-- Chọn học kỳ --</option>
        <option value="HK1" <?= ($hocKy === 'HK1') ? 'selected' : '' ?>>Học kỳ 1</option>
        <option value="HK2" <?= ($hocKy === 'HK2') ? 'selected' : '' ?>>Học kỳ 2</option>
      </select>
    </div>

    <div class="col-md-3">
      <label for="selectTieuChi" class="form-label">Chọn tiêu chí:</label>
      <select id="selectTieuChi" class="form-select" onchange="window.location='?controller=ketquahoctap&action=thongke&hocKy=<?= $hocKy ?>&tieuChi='+this.value;">
        <option value="">-- Chọn tiêu chí --</option>
        <option value="diem" <?= ($tieuChi === 'diem') ? 'selected' : '' ?>>Điểm</option>
        <option value="hocluc" <?= ($tieuChi === 'hocluc') ? 'selected' : '' ?>>Học lực</option>
        <option value="hanhkiem" <?= ($tieuChi === 'hanhkiem') ? 'selected' : '' ?>>Hạnh kiểm</option>
      </select>
    </div>
  </div>

  <!-- Card tổng quan: luôn hiển thị nhưng dữ liệu bên trong rỗng nếu chưa chọn -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Số học sinh</h5>
          <p class="card-text"><?= ($hocKy && $tieuChi) ? count($hocSinh) : '-' ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Điểm TB lớp</h5>
          <p class="card-text">
            <?= ($hocKy && $tieuChi) ? (!empty($diemTB_Lop) ? round(array_sum($diemTB_Lop) / count($diemTB_Lop), 2) : 0) : '-' ?>
          </p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Học lực tốt nhất</h5>
          <p class="card-text"><?= ($hocKy && $tieuChi) ? '-' : '-' ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Hạnh kiểm tốt nhất</h5>
          <p class="card-text"><?= ($hocKy && $tieuChi) ? '-' : '-' ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Bảng dữ liệu: luôn hiển thị nhưng rỗng nếu chưa chọn -->
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
        <?php if ($hocKy && $tieuChi && !empty($hocSinh)): ?>
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
                <button class="btn btn-sm btn-info toggle-detail" data-mahocsinh="<?= $hs['maHocSinh'] ?>" data-tenhs="<?= htmlspecialchars($hs['hoTen']) ?>">
                  Xem chi tiết
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="<?= count($monHoc) + 3 ?>" class="text-center">Chọn học kỳ và tiêu chí để hiển thị dữ liệu</td>
          </tr>
        <?php endif; ?>
      </tbody>

    </table>
  </div>

  <!-- Khu vực hiển thị chi tiết -->
  <div id="chiTietContainer" class="mb-5"></div>

  <?php if ($hocKy && $tieuChi && $tieuChi === 'diem'): ?>
    <script>
      const chiTietDiemData = <?= json_encode($chiTietDiem) ?>;

      document.querySelectorAll('.toggle-detail').forEach(button => {
        button.addEventListener('click', () => {
          const maHS = button.getAttribute('data-mahocsinh');
          const tenHS = button.getAttribute('data-tenhs');
          const chiTiet = chiTietDiemData[maHS];

          if (!chiTiet) {
            document.getElementById('chiTietContainer').innerHTML = `<p class="text-danger">Chưa có điểm chi tiết cho ${tenHS}</p>`;
            return;
          }

          let html = `<h4>Chi tiết điểm của ${tenHS}</h4>`;
          html += `<table class="table table-bordered"><thead>
                 <tr>
                   <th>Môn</th>
                   <th>Miệng</th>
                   <th>15 phút</th>
                   <th>1 tiết</th>
                   <th>Giữa kỳ</th>
                   <th>Cuối kỳ</th>
                   <th>TB</th>
                 </tr>
               </thead><tbody>`;

          for (let maMH in chiTiet) {
            const ct = chiTiet[maMH];
            html += `<tr>
                  <td>${ct.tenMonHoc}</td>
                  <td>${ct.MIENG ?? 0}</td>
                  <td>${ct['15_PHUT'] ?? 0}</td>
                  <td>${ct['1_TIET'] ?? 0}</td>
                  <td>${ct.GIUA_KY ?? 0}</td>
                  <td>${ct.CUOI_KY ?? 0}</td>
                  <td>${ct.DIEM_TB ?? 0}</td>
                 </tr>`;
          }

          html += `</tbody></table>`;
          document.getElementById('chiTietContainer').innerHTML = html;
          window.scrollTo({
            top: document.getElementById('chiTietContainer').offsetTop,
            behavior: 'smooth'
          });
        });
      });
    </script>
  <?php endif; ?>
</div>