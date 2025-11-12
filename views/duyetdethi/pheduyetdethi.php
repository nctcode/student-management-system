<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/totruong.php';

// Lấy message từ session (nếu có) để hiển thị popup
$message = $_SESSION['message'] ?? '';
$type = $_SESSION['type'] ?? '';
unset($_SESSION['message'], $_SESSION['type']);


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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/duyetdethi.css">

<div class="container container-main">
  <h2 class="title">DUYỆT ĐỀ THI</h2>

  <?php if (!empty($message) && $type === 'error'): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <div class="row mb-4">
    <!-- Bộ lọc -->
    <div class="col-md-4">
      <div class="box">
        <h6>Chọn Khối học & Học kỳ:</h6>
        <form method="GET" action="">
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

          <button class="btn btn-success w-100" type="submit">Xác nhận</button>
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
              <th>Giáo viên</th>
              <th>Tiêu đề</th>
              <th>Chọn</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($exams)): ?>
              <?php foreach ($exams as $exam): ?>
                <tr>
                  <td><?= $exam['maDeThi'] ?></td>
                  <td><?= htmlspecialchars($exam['tenGiaoVien']) ?></td>
                  <td><?= htmlspecialchars($exam['tieuDe']) ?></td>
                  <td>
                    <a href="?controller=duyetdethi&action=duyet&maKhoi=<?= $maKhoi ?>&maNienKhoa=<?= $maNienKhoa ?>&maDeThi=<?= $exam['maDeThi'] ?>"
                      class="btn btn-sm btn-outline-primary">Xem</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center text-muted">
                  <?= ($maKhoi || $maNienKhoa) ? 'Không có đề thi nào phù hợp' : 'Vui lòng chọn Khối và Học kỳ' ?>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Chi tiết đề thi -->
  <div class="row mt-4">
    <div class="col-md-4">
      <div class="box">
        <h6>Chi tiết đề thi:</h6>
        <?php if ($examDetail): ?>
          <table class="table table-bordered">
            <tr>
              <th>Mã đề</th>
              <td><?= $examDetail['maDeThi'] ?></td>
            </tr>
            <tr>
              <th>Tiêu đề</th>
              <td><?= htmlspecialchars($examDetail['tieuDe']) ?></td>
            </tr>
            <tr>
              <th>Giáo viên</th>
              <td><?= htmlspecialchars($examDetail['tenGiaoVien']) ?></td>
            </tr>
            <tr>
              <th>Ngày nộp</th>
              <td><?= $examDetail['ngayNop'] ?></td>
            </tr>
            <tr>
              <th>Môn</th>
              <td><?= htmlspecialchars($examDetail['monHoc']) ?></td>
            </tr>
            <tr>
              <th>Học kỳ</th>
              <td><?= htmlspecialchars($examDetail['hocKy']) ?></td>
            </tr>
            <tr>
              <th>Năm học</th>
              <td><?= htmlspecialchars($examDetail['namHoc']) ?></td>
            </tr>
            <tr>
              <th>Trạng thái</th>
              <td><?= htmlspecialchars(hienThiTrangThai($examDetail['trangThai']) )?></td>
            </tr>

            <tr>
              <th>Tổng số câu</th>
              <td><?= count($questions) ?></td>
            </tr>
            <tr>
              <th>Tổng điểm</th>
              <td>10</td>
            </tr>
          </table>
        <?php else: ?>
          <p class="text-center text-muted mb-0">Chưa chọn đề thi nào.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Danh sách câu hỏi -->
    <div class="col-md-8">
      <div class="box">
        <h6>Danh sách câu hỏi:</h6>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Câu hỏi</th>
              <th style="width: 90px;">Mức điểm</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($questions)): ?>
              <?php
              $total = count($questions);
              $rounded = $total > 0 ? round((10 / $total) * 4) / 4 : 0;
              foreach ($questions as $i => $q): ?>
                <tr>
                  <td><strong><?= $i + 1 ?>:</strong> <?= htmlspecialchars($q['noiDungCauHoi']) ?></td>
                  <td><?= number_format($rounded, 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="2" class="text-center text-muted">Chưa có câu hỏi.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Form duyệt/từ chối -->
  <?php if ($examDetail): ?>
    <form id="formDuyet" method="POST" action="index.php?controller=duyetdethi&action=capNhatTrangThai">
      <input type="hidden" name="maDeThi" value="<?= $examDetail['maDeThi'] ?>">
      <input type="hidden" name="maKhoi" value="<?= $maKhoi ?>">
      <input type="hidden" name="maNienKhoa" value="<?= $maNienKhoa ?>">
      <input type="hidden" id="hanhDongInput" name="hanhDong" value="">

      <!-- Khung ghi chú ẩn -->
      <div class="mt-3" id="divGhiChu" style="display:none;">
        <label for="ghiChu" class="form-label">Lý do từ chối:</label>
        <textarea id="ghiChu" name="ghiChu" class="form-control" rows="3" placeholder="Nhập lý do nếu bạn từ chối..."></textarea>
        <div id="errorMessage" class="text-danger mt-2" style="display:none;">
          ❗ Vui lòng nhập lý do từ chối trước khi gửi!
        </div>
      </div>

      <!-- Nút hành động -->
      <div class="text-center mt-3">
        <button type="submit" id="btnDuyet" class="btn btn-success btn-action">Duyệt đề</button>
        <button type="button" id="btnTuChoi" class="btn btn-danger btn-action">Từ chối</button>
      </div>
    </form>
  <?php endif; ?>

  <!-- Popup thông báo -->
  <div class="modal fade" id="popupThongBao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-3">
        <h5 id="popupMessage"></h5>
        <div class="mt-3">
          <button class="btn btn-primary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const btnTuChoi = document.getElementById('btnTuChoi');
      const btnDuyet = document.getElementById('btnDuyet');
      const divGhiChu = document.getElementById('divGhiChu');
      const ghiChu = document.getElementById('ghiChu');
      const errorMsg = document.getElementById('errorMessage');
      const form = document.getElementById('formDuyet');
      const hanhDongInput = document.getElementById('hanhDongInput');

      if (btnTuChoi && btnDuyet && form && divGhiChu && ghiChu && errorMsg && hanhDongInput) {
        // Nút Từ chối
        btnTuChoi.addEventListener('click', () => {
          if (divGhiChu.style.display === 'none') {
            divGhiChu.style.display = 'block';
            ghiChu.focus();
            hanhDongInput.value = 'tuchoi';
          } else {
            if (ghiChu.value.trim() === '') {
              errorMsg.style.display = 'block';
              ghiChu.focus();
            } else {
              errorMsg.style.display = 'none';
              hanhDongInput.value = 'tuchoi';
              form.submit();
            }
          }
        });

        // Nút Duyệt
        btnDuyet.addEventListener('click', () => {
          hanhDongInput.value = 'duyet';
          form.submit();
        });

        // Kiểm tra trước khi submit (chỉ cho Từ chối)
        form.addEventListener('submit', (e) => {
          if (hanhDongInput.value === 'tuchoi' && ghiChu.value.trim() === '') {
            e.preventDefault();
            errorMsg.style.display = 'block';
            ghiChu.focus();
            return false;
          }
          errorMsg.style.display = 'none';
        });

        // Vô hiệu hóa nút Duyệt khi textarea có nội dung
        ghiChu.addEventListener('input', () => {
          if (ghiChu.value.trim() !== '') {
            btnDuyet.disabled = true; // đã nhập lý do → disable Duyệt
          } else {
            btnDuyet.disabled = false; // xóa hết lý do → enable Duyệt
          }
        });
      }

      // Hiển thị popup nếu có message
      <?php if (!empty($message) && $type !== 'error'): ?>
        const modalEl = document.getElementById('popupThongBao');
        if (modalEl) {
          const modal = new bootstrap.Modal(modalEl);
          document.getElementById('popupMessage').textContent = <?php echo json_encode($message); ?>;
          modal.show();
        }
      <?php endif; ?>
    });
  </script>


  <?php require_once 'views/layouts/footer.php'; ?>