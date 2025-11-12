<?php
require_once 'models/HoSoModel.php';

$model = new HoSoModel();
$hoSo = null;
$message = '';
$showDetail = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['maHoSo'])) {
  $maHoSo = trim($_POST['maHoSo']);
  $hoSo = $model->getHoSoByMa($maHoSo);

  if (!$hoSo) {
    $message = "❌ Không tìm thấy hồ sơ với mã: $maHoSo";
  } else {
    if (isset($_POST['action']) && $_POST['action'] === 'xemchitiet') {
      $showDetail = true;
    }
  }
}


function hienThiTrangThai($trangThai)
{
  switch ($trangThai) {
    case 'CHO_DUYET':
      return 'Chờ duyệt';
    case 'DA_DUYET':
      return 'Đã duyệt';
    case 'TU_CHOI':
      return 'Từ chối';
  }
}

?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tra cứu kết quả tuyển sinh</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
    }

    .main-content {
      margin-left: 260px;
      padding: 20px;
    }

    .search-card {
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .table th {
      background-color: #0d6efd;
      color: white;
    }

    .detail-card {
      margin-top: 30px;
      border-radius: 12px;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    /* Ô kết quả nổi bật */
    .highlight-result {
      font-weight: bold;
      text-transform: uppercase;
      font-size: 1.1rem;
      text-align: center;
      border: 2px solid;
      transition: 0.3s;
    }

    .highlight-result.passed {
      border-color: #198754;
      background-color: #e8f5e9;
      color: #0f5132;
    }

    .highlight-result.failed {
      border-color: #dc3545;
      background-color: #fdecea;
      color: #842029;
    }
  </style>
</head>

<body>

  <div class="main-content">
    <div class="card search-card">
      <div class="card-body">
        <h4 class="card-title text-center mb-4 text-primary fw-bold">Tra cứu kết quả tuyển sinh</h4>

        <!-- Ô tìm kiếm -->
        <form class="d-flex mb-4" method="post">
          <input class="form-control me-2" type="search" name="maHoSo"
            placeholder="Nhập mã hồ sơ..."
            value="<?= htmlspecialchars($_POST['maHoSo'] ?? '') ?>" required>
          <button class="btn btn-primary" type="submit">Tìm kiếm</button>
        </form>

        <?php if ($message): ?>
          <div class="alert alert-warning text-center"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Bảng danh sách hồ sơ -->
        <div class="table-responsive">
          <table class="table table-bordered text-center align-middle">
            <thead>
              <tr>
                <th>Mã hồ sơ</th>
                <th>Họ tên thí sinh</th>
                <th>Ngày nộp</th>
                <th>Nguyện vọng</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($hoSo): ?>
                <tr>
                  <td><?= htmlspecialchars($hoSo['maHoSo']) ?></td>
                  <td><?= htmlspecialchars($hoSo['hoTen']) ?></td>
                  <td><?= htmlspecialchars($hoSo['ngayDangKy']) ?></td>
                  <td><?= htmlspecialchars($hoSo['nguyenVong']) ?></td>
                  <td><?= htmlspecialchars(hienThiTrangThai($hoSo['trangThai'])) ?></td>
                  <td>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="maHoSo" value="<?= htmlspecialchars($hoSo['maHoSo']) ?>">
                      <input type="hidden" name="action" value="xemchitiet">
                      <button class="btn btn-sm btn-info" type="submit">Xem chi tiết</button>
                    </form>
                  </td>
                </tr>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-muted fst-italic">Dữ liệu hồ sơ sẽ hiển thị tại đây...</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Chi tiết hồ sơ -->
        <?php if ($hoSo && $showDetail): ?>
          <div id="chitiet" class="card detail-card p-4">
            <h5 class="text-primary mb-3">Chi tiết hồ sơ tuyển sinh</h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Mã hồ sơ</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hoSo['maHoSo']) ?>" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Họ tên thí sinh</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hoSo['hoTen']) ?>" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Ngày sinh</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hoSo['ngaySinh']) ?>" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Giới tính</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hoSo['gioiTinh']) ?>" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Số điện thoại</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hoSo['soDienThoai']) ?>" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Địa chỉ</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hoSo['diaChi']) ?>" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Trường THCS</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hoSo['truongTHCS']) ?>" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Phụ huynh</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hoSo['hoTenPhuHuynh']) ?>" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">SĐT Phụ huynh</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hoSo['soDTPhuHuynh']) ?>" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Ngày đăng ký</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hoSo['ngayDangKy']) ?>" readonly>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Trạng thái</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars(hienThiTrangThai($hoSo['trangThai']))  ?>" readonly>
              </div>

              <!-- Ô kết quả nổi bật -->
              <div class="col-md-6">
                <?php
                $ketQua = strtoupper(trim($hoSo['ketQua']));
                $classKetQua = ($ketQua === 'TRUNG_TUYEN') ? 'passed' : 'failed';
                ?>
                <label class="form-label fw-semibold">Kết quả</label>
                <input type="text"
                  class="form-control highlight-result <?= $classKetQua ?>"
                  value="<?= htmlspecialchars($hoSo['ketQua']) ?>"
                  readonly>
              </div>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>