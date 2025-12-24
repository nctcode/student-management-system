<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/totruong.php';

// Lấy message từ session (nếu có)
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $type = $_SESSION['type'] ?? 'success';
    unset($_SESSION['message'], $_SESSION['type']);
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/duyetdethi.css">

<div class="container container-main">
    <h2 class="title text-center mb-4">DANH SÁCH ĐỀ THI ĐÃ DUYỆT / TỪ CHỐI</h2>

    <!-- Thanh lọc dữ liệu -->
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-end">
            <form method="GET" action="index.php" class="d-flex gap-3">
                <input type="hidden" name="controller" value="duyetdethi">
                <input type="hidden" name="action" value="lichSuDuyetDeThi">

                <select name="maKhoi" class="form-select" style="width: 160px;">
                    <option value="">-- Khối học --</option>
                    <?php foreach ($khoiHocList as $khoi): ?>
                        <option value="<?= htmlspecialchars($khoi['maKhoi']) ?>"
                            <?= isset($_GET['maKhoi']) && $_GET['maKhoi'] == $khoi['maKhoi'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($khoi['tenKhoi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="maNienKhoa" class="form-select" style="width: 160px;">
                    <option value="">-- Học kỳ --</option>
                    <?php foreach ($nienKhoaList as $nk): 
                        // Loại bỏ học kỳ "Cả năm"
                        if ($nk['hocKy'] === 'CA_NAM') continue;
                    ?>
                        <option value="<?= htmlspecialchars($nk['maNienKhoa']) ?>"
                            <?= isset($_GET['maNienKhoa']) && $_GET['maNienKhoa'] == $nk['maNienKhoa'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nk['hocKy']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button class="btn btn-secondary" type="submit">Lọc</button>
            </form>
        </div>
    </div>

    <!-- Hiển thị thông báo lỗi trực tiếp -->
    <?php if (!empty($message) && $type === 'error'): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    
    <!-- Hai bảng ngang -->
    <div class="row">
        <!-- Đã duyệt -->
        <div class="col-md-6">
            <div class="box">
                <h6 class="text-success text-center">ĐỀ THI ĐÃ DUYỆT</h6>
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-success">
                        <tr>
                            <th>Mã đề</th>
                            <th>Nội dung</th>
                            <th>Giáo viên ra đề</th>
                            <th>Ngày duyệt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($deDaDuyet)): ?>
                            <?php foreach ($deDaDuyet as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['maDeThi']) ?></td>
                                    <td><?= htmlspecialchars($row['tieuDe']) ?></td>
                                    <td><?= htmlspecialchars($row['tenGiaoVien']) ?></td>
                                    <td><?= htmlspecialchars($row['ngayNop'] ?? '-') ?></td>
                                    <td>
                                        <a href="index.php?controller=duyetdethi&action=lichSuDuyetDeThi&maDeThi=<?= $row['maDeThi'] ?>&maKhoi=<?= $_GET['maKhoi'] ?? '' ?>&maNienKhoa=<?= $_GET['maNienKhoa'] ?? '' ?>"
                                           class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-muted">Chưa có dữ liệu</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Từ chối -->
        <div class="col-md-6">
            <div class="box">
                <h6 class="text-danger text-center">ĐỀ THI BỊ TỪ CHỐI</h6>
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-danger">
                        <tr>
                            <th>Mã đề</th>
                            <th>Nội dung</th>
                            <th>Giáo viên ra đề</th>
                            <th>Ngày duyệt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($deTuChoi)): ?>
                            <?php foreach ($deTuChoi as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['maDeThi']) ?></td>
                                    <td><?= htmlspecialchars($row['tieuDe']) ?></td>
                                    <td><?= htmlspecialchars($row['tenGiaoVien']) ?></td>
                                    <td><?= htmlspecialchars($row['ngayNop'] ?? '-') ?></td>
                                    <td>
                                        <a href="index.php?controller=duyetdethi&action=lichSuDuyetDeThi&maDeThi=<?= $row['maDeThi'] ?>&maKhoi=<?= $_GET['maKhoi'] ?? '' ?>&maNienKhoa=<?= $_GET['maNienKhoa'] ?? '' ?>"
                                           class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-muted">Chưa có dữ liệu</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chi tiết đề & câu hỏi -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="box">
                <h6>Chi tiết đề thi:</h6>
                <?php if ($examDetail): ?>
                    <table class="table table-bordered">
                        <tr><th>Mã đề</th><td><?= $examDetail['maDeThi'] ?></td></tr>
                        <tr><th>Tiêu đề</th><td><?= htmlspecialchars($examDetail['tieuDe']) ?></td></tr>
                        <tr><th>Giáo viên ra đề</th><td><?= htmlspecialchars($examDetail['tenGiaoVien']) ?></td></tr>
                        <tr><th>Ngày nộp</th><td><?= $examDetail['ngayNop'] ?></td></tr>
                        <tr><th>Môn</th><td><?= htmlspecialchars($examDetail['monHoc']) ?></td></tr>
                        <tr><th>Học kỳ</th><td>
                            <?php 
                            // Chỉ hiển thị HK1/HK2, không hiển thị "Cả năm"
                            $hocKy = $examDetail['hocKy'] ?? '';
                            if ($hocKy === 'CA_NAM') {
                                // Nếu là "Cả năm", có thể hiển thị thông báo khác hoặc để trống
                                echo '<span class="text-muted">Không xác định</span>';
                            } else {
                                echo htmlspecialchars($hocKy);
                            }
                            ?>
                        </td></tr>
                        <tr><th>Năm học</th><td><?= htmlspecialchars($examDetail['namHoc']) ?></td></tr>
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
                            <?php $pointPerQuestion = 10 / count($questions); ?>
                            <?php foreach ($questions as $i => $q): ?>
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
</div>

<?php require_once 'views/layouts/footer.php'; ?>