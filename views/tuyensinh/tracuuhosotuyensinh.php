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

// Hàm chuyển đổi giá trị ENUM sang tiếng Việt
function convertToVietnamese($value, $type) {
    switch ($type) {
        case 'gioiTinh':
            $mapping = [
                'NAM' => 'Nam',
                'NU' => 'Nữ',
                'KHAC' => 'Khác'
            ];
            return $mapping[$value] ?? $value;
            
        case 'trangThai':
            $mapping = [
                'CHO_XET_DUYET' => 'Chờ xét duyệt',
                'DA_DUYET' => 'Đã duyệt',
                'TU_CHOI' => 'Từ chối'
            ];
            return $mapping[$value] ?? $value;
            
        case 'ketQua':
            $mapping = [
                'TRUNG_TUYEN' => 'Trúng tuyển',
                'KHONG_TRUNG_TUYEN' => 'Không trúng tuyển'
            ];
            return $mapping[$value] ?? $value;
            
        case 'hocLuc':
            $mapping = [
                'GIOI' => 'Giỏi',
                'KHA' => 'Khá',
                'TRUNG_BINH' => 'Trung bình',
                'YEU' => 'Yếu'
            ];
            return $mapping[$value] ?? $value;
            
        case 'hanhKiem':
            $mapping = [
                'TOT' => 'Tốt',
                'KHA' => 'Khá',
                'TRUNG_BINH' => 'Trung bình',
                'YEU' => 'Yếu'
            ];
            return $mapping[$value] ?? $value;
            
        case 'hinhThucTuyenSinh':
            $mapping = [
                'THI_TUYEN' => 'Thi tuyển',
                'XET_TUYEN' => 'Xét tuyển',
                'KET_HOP' => 'Kết hợp'
            ];
            return $mapping[$value] ?? $value;
            
        default:
            return $value;
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

    .section-title {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      margin: 20px 0 15px 0;
      font-weight: bold;
    }

    .info-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 5px;
    }

    .info-value {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 5px;
      padding: 8px 12px;
      min-height: 42px;
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
        <?php if ($hoSo): ?>
        <div class="table-responsive">
          <table class="table table-bordered text-center align-middle">
            <thead>
              <tr>
                <th>Mã hồ sơ</th>
                <th>Họ tên thí sinh</th>
                <th>Ngày nộp</th>
                <th>Nguyện vọng 1</th>
                <th>Trạng thái</th>
                <th>Kết quả</th>
                <th>Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?= htmlspecialchars($hoSo['maHoSo']) ?></td>
                <td><?= htmlspecialchars($hoSo['hoTen']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($hoSo['ngayDangKy'])) ?></td>
                <td><?= htmlspecialchars($hoSo['nguyenVong1'] ?? '') ?></td>
                <td>
                  <span class="badge 
                    <?= $hoSo['trangThai'] == 'DA_DUYET' ? 'bg-success' : 
                       ($hoSo['trangThai'] == 'TU_CHOI' ? 'bg-danger' : 'bg-warning') ?>">
                    <?= convertToVietnamese($hoSo['trangThai'], 'trangThai') ?>
                  </span>
                </td>
                <td>
                  <?php if ($hoSo['ketQua']): ?>
                  <span class="badge 
                    <?= $hoSo['ketQua'] == 'TRUNG_TUYEN' ? 'bg-success' : 'bg-danger' ?>">
                    <?= convertToVietnamese($hoSo['ketQua'], 'ketQua') ?>
                  </span>
                  <?php else: ?>
                    <span class="text-muted">Chưa có</span>
                  <?php endif; ?>
                </td>
                <td>
                  <form method="post" style="display:inline;">
                    <input type="hidden" name="maHoSo" value="<?= htmlspecialchars($hoSo['maHoSo']) ?>">
                    <input type="hidden" name="action" value="xemchitiet">
                    <button class="btn btn-sm btn-info" type="submit">
                      <i class="fas fa-eye me-1"></i>Xem chi tiết
                    </button>
                  </form>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <?php else: ?>
          <div class="text-center text-muted py-4">
            <i class="fas fa-search fa-2x mb-3"></i>
            <p>Dữ liệu hồ sơ sẽ hiển thị tại đây...</p>
          </div>
        <?php endif; ?>

        <!-- Chi tiết hồ sơ -->
        <?php if ($hoSo && $showDetail): ?>
          <div id="chitiet" class="card detail-card p-4">
            <h5 class="text-primary mb-3">
              <i class="fas fa-file-alt me-2"></i>Chi tiết hồ sơ tuyển sinh
            </h5>
            
            <!-- Thông tin cá nhân -->
            <div class="section-title">Thông tin cá nhân</div>
            <div class="row g-3">
              <div class="col-md-3">
                <div class="info-label">Mã hồ sơ</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['maHoSo']) ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">Họ tên thí sinh</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['hoTen']) ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">Ngày sinh</div>
                <div class="info-value"><?= date('d/m/Y', strtotime($hoSo['ngaySinh'])) ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">Giới tính</div>
                <div class="info-value"><?= convertToVietnamese($hoSo['gioiTinh'], 'gioiTinh') ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">Dân tộc</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['danToc'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">Tôn giáo</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['tonGiao'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">Quốc tịch</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['quocTich'] ?? 'Việt Nam') ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">SĐT học sinh</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['soDienThoaiHocSinh'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-6">
                <div class="info-label">Địa chỉ thường trú</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['diaChiThuongTru'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-6">
                <div class="info-label">Nơi ở hiện nay</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['noiOHienNay'] ?? 'Chưa cập nhật') ?></div>
              </div>
            </div>

            <!-- Thông tin phụ huynh -->
            <div class="section-title">Thông tin phụ huynh</div>
            <div class="row g-3">
              <div class="col-md-4">
                <div class="info-label">Họ tên cha</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['hoTenCha'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Nghề nghiệp cha</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['ngheNghiepCha'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Điện thoại cha</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['dienThoaiCha'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Họ tên mẹ</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['hoTenMe'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Nghề nghiệp mẹ</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['ngheNghiepMe'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Điện thoại mẹ</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['dienThoaiMe'] ?? 'Chưa cập nhật') ?></div>
              </div>
            </div>

            <!-- Thông tin học vấn -->
            <div class="section-title">Thông tin học vấn</div>
            <div class="row g-3">
              <div class="col-md-4">
                <div class="info-label">Trường THCS</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['truongTHCS'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Năm tốt nghiệp</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['namTotNghiep'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Điểm TB lớp 9</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['diemTB_Lop9'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">Xếp loại học lực</div>
                <div class="info-value"><?= convertToVietnamese($hoSo['xepLoaiHocLuc'] ?? '', 'hocLuc') ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">Xếp loại hạnh kiểm</div>
                <div class="info-value"><?= convertToVietnamese($hoSo['xepLoaiHanhKiem'] ?? '', 'hanhKiem') ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">Hình thức tuyển sinh</div>
                <div class="info-value"><?= convertToVietnamese($hoSo['hinhThucTuyenSinh'] ?? '', 'hinhThucTuyenSinh') ?></div>
              </div>
              <div class="col-md-3">
                <div class="info-label">Điểm thi tuyển sinh</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['diemThiTuyenSinh'] ?? 'Chưa có') ?></div>
              </div>
            </div>

            <!-- Nguyện vọng -->
            <div class="section-title">Nguyện vọng đăng ký</div>
            <div class="row g-3">
              <div class="col-md-4">
                <div class="info-label">Nguyện vọng 1</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['nguyenVong1'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Nguyện vọng 2</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['nguyenVong2'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Nguyện vọng 3</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['nguyenVong3'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-6">
                <div class="info-label">Ngành học</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['nganhHoc'] ?? 'Chưa cập nhật') ?></div>
              </div>
              <div class="col-md-6">
                <div class="info-label">Ban học</div>
                <div class="info-value"><?= htmlspecialchars($hoSo['tenBan'] ?? 'Chưa cập nhật') ?></div>
              </div>
            </div>

            <!-- Kết quả -->
            <div class="section-title">Kết quả xét tuyển</div>
            <div class="row g-3">
              <div class="col-md-4">
                <div class="info-label">Trạng thái</div>
                <div class="info-value">
                  <span class="badge 
                    <?= $hoSo['trangThai'] == 'DA_DUYET' ? 'bg-success' : 
                       ($hoSo['trangThai'] == 'TU_CHOI' ? 'bg-danger' : 'bg-warning') ?>">
                    <?= convertToVietnamese($hoSo['trangThai'], 'trangThai') ?>
                  </span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Kết quả</div>
                <div class="info-value">
                  <?php if ($hoSo['ketQua']): ?>
                  <span class="badge 
                    <?= $hoSo['ketQua'] == 'TRUNG_TUYEN' ? 'bg-success' : 'bg-danger' ?>">
                    <?= convertToVietnamese($hoSo['ketQua'], 'ketQua') ?>
                  </span>
                  <?php else: ?>
                    <span class="text-muted">Chưa có kết quả</span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="col-md-4">
                <div class="info-label">Ngày đăng ký</div>
                <div class="info-value"><?= date('d/m/Y H:i', strtotime($hoSo['ngayDangKy'])) ?></div>
              </div>
              <?php if (!empty($hoSo['ghiChu'])): ?>
              <div class="col-12">
                <div class="info-label">Ghi chú</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($hoSo['ghiChu'])) ?></div>
              </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>

</html>