<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/totruong.php';
?>
<!-- CSS riêng cho trang duyệt đề thi -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/duyetdethi.css">

<div class="container container-main">
  <h2 class="title">DUYỆT ĐỀ THI</h2>

  <!-- Hiển thị alert khi có thông báo -->
  <?php if (isset($_GET['msg'])): ?>
    <script>
      alert("<?= htmlspecialchars($_GET['msg']) ?>");
      window.location.href = "index.php?controller=duyetdethi&action=duyet";
    </script>
  <?php endif; ?>

  <div class="row mb-4">
    <!-- Lọc Khối & Học kỳ -->
    <div class="col-md-4">
      <div class="box">
        <h6>Chọn Khối học & Học kỳ:</h6>
        <form method="GET" action="index.php">
          <input type="hidden" name="controller" value="duyetdethi">
          <input type="hidden" name="action" value="duyet">

          <select name="maKhoi" class="form-select mb-3">
            <option value="">-- Khối học --</option>
            <?php foreach ($khoiHocList as $khoi): ?>
              <option value="<?= $khoi['maKhoi'] ?>" <?= ($maKhoi == $khoi['maKhoi']) ? 'selected' : '' ?>>
                <?= $khoi['tenKhoi'] ?>
              </option>
            <?php endforeach; ?>
          </select>

          <select name="maNienKhoa" class="form-select mb-3">
            <option value="">-- Học kỳ --</option>
            <?php foreach ($nienKhoaList as $nk): ?>
              <option value="<?= $nk['maNienKhoa'] ?>" <?= ($maNienKhoa == $nk['maNienKhoa']) ? 'selected' : '' ?>>
                <?= $nk['hocKy'] ?>
              </option>
            <?php endforeach; ?>
          </select>

          <button class="btn btn-secondary w-100" type="submit">Xác nhận</button>
        </form>
      </div>
    </div>

    <!-- Danh sách đề thi -->
    <div class="col-md-8">
      <div class="box">
        <h6>Danh sách đề thi:</h6>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Mã đề</th>
              <th>Tên giáo viên ra đề</th>
              <th>Tiêu đề</th>
              <th>Chọn</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($exams)): ?>
              <?php foreach ($exams as $exam): ?>
                <tr>
                  <td><?= $exam['maDeThi'] ?></td>
                  <td><?= $exam['tenGiaoVien'] ?></td>
                  <td><?= $exam['tieuDe'] ?></td>
                  <td>
                    <a href="index.php?controller=duyetdethi&action=duyet&maKhoi=<?= $maKhoi ?>&maNienKhoa=<?= $maNienKhoa ?>&maDeThi=<?= $exam['maDeThi'] ?>" class="btn btn-sm btn-outline-primary">
                      Xem chi tiết
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center">
                  <?php if ($maKhoi || $maNienKhoa): ?>
                    Không có đề thi nào phù hợp
                  <?php else: ?>
                    Chưa có dữ liệu, vui lòng chọn Khối và Học kỳ
                  <?php endif; ?>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Luôn hiển thị khung Chi tiết & Danh sách câu hỏi -->
  <div class="row mt-4">
    <div class="col-md-4">
      <div class="box">
        <h6>Chi tiết đề thi:</h6>
        <?php if ($examDetail): ?>
          <table class="table table-bordered">
            <tr><th>Mã đề</th><td><?= $examDetail['maDeThi'] ?></td></tr>
            <tr><th>Tiêu đề</th><td><?= $examDetail['tieuDe'] ?></td></tr>
            <tr><th>Giáo viên ra đề</th><td><?= $examDetail['tenGiaoVien'] ?></td></tr>
            <tr><th>Ngày nộp</th><td><?= $examDetail['ngayNop'] ?></td></tr>
            <tr><th>Môn</th><td><?= $examDetail['monHoc'] ?></td></tr>
            <tr><th>Học kỳ</th><td><?= $examDetail['hocKy'] ?></td></tr>
            <tr><th>Năm học</th><td><?= $examDetail['namHoc'] ?></td></tr>
            <tr><th>Trạng thái</th><td><?= $examDetail['trangThai'] ?></td></tr>
            <tr><th>Tổng số câu</th><td><?= count($questions) ?></td></tr>
            <tr><th>Tổng điểm</th><td>10</td></tr>
          </table>
        <?php else: ?>
          <p class="text-center text-muted mb-0">Chưa chọn đề thi nào.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-md-8">
      <div class="box">
        <h6>Danh sách câu hỏi:</h6>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Câu hỏi</th>
              <th style="width:80px;">Mức điểm</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($questions)): ?>
              <?php
              $totalQuestions = count($questions);
              $pointPerQuestion = $totalQuestions > 0 ? 10 / $totalQuestions : 0;
              foreach ($questions as $i => $q): ?>
                <tr>
                  <td><strong><?= $i + 1 ?>:</strong> <?= htmlspecialchars($q['noiDungCauHoi'] ?? '') ?></td>
                  <td><?= number_format($pointPerQuestion, 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="2" class="text-center text-muted">Chưa có dữ liệu câu hỏi.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Form duyệt / từ chối -->
  <form method="POST" action="index.php?controller=duyetdethi&action=capNhatTrangThai">
    <input type="hidden" name="maDeThi" value="<?= $examDetail['maDeThi'] ?? '' ?>">
    <div class="text-center mt-4">
      <button type="submit" name="hanhDong" value="duyet" class="btn btn-dark btn-action" <?= !$examDetail ? 'disabled' : '' ?>>Duyệt đề</button>
      <button type="submit" name="hanhDong" value="tuchoi" class="btn btn-secondary btn-action" <?= !$examDetail ? 'disabled' : '' ?>>Từ chối</button>
    </div>
  </form>

</div>

<?php
require_once 'views/layouts/footer.php';
?>
