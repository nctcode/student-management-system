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
    /* Bọc toàn bộ nội dung bên phải sidebar */
    .main-content {
      margin-left: 260px; /* Dành chỗ cho sidebar */
      padding: 20px;
    }
    .search-card {
      max-width: 100%;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .table th {
      background-color: #0d6efd;
      color: white;
    }
    .detail-card {
      margin-top: 30px;
      border-radius: 12px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="card search-card">
    <div class="card-body">
      <h4 class="card-title text-center mb-4 text-primary fw-bold">Tra cứu kết quả tuyển sinh</h4>

      <form class="d-flex mb-4" method="post">
        <input class="form-control me-2" type="search" name="maHoSo" placeholder="Nhập mã hồ sơ..." aria-label="Search">
        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
      </form>

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
            <tr>
              <td colspan="6" class="text-muted fst-italic">Dữ liệu hồ sơ sẽ hiển thị tại đây...</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Khu vực chi tiết hồ sơ -->
      <div class="card detail-card p-4">
        <h5 class="text-primary mb-3">Chi tiết hồ sơ tuyển sinh</h5>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Mã hồ sơ</label>
            <input type="text" class="form-control" placeholder="...">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Họ tên thí sinh</label>
            <input type="text" class="form-control" placeholder="...">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Ngày nộp</label>
            <input type="text" class="form-control" placeholder="...">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Nguyện vọng</label>
            <input type="text" class="form-control" placeholder="...">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Trạng thái</label>
            <input type="text" class="form-control" placeholder="...">
          </div>
          <div class="col-12 text-end mt-3">
            <button class="btn btn-success">Xác nhận</button>
            <button class="btn btn-secondary">Quay lại</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
