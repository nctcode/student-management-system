<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/totruong.php';

$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

function hienThiTrangThai($t)
{
    return [
        'CHO_DUYET' => 'Chờ duyệt',
        'DA_DUYET'  => 'Đã duyệt',
        'TU_CHOI'   => 'Từ chối'
    ][$t] ?? '';
}

// Phân loại đề thi theo trạng thái
$deDaDuyet = [];
$deTuChoi  = [];
if (!empty($exams)) {
    foreach ($exams as $row) {
        if ($row['trangThai'] === 'DA_DUYET') $deDaDuyet[] = $row;
        elseif ($row['trangThai'] === 'TU_CHOI') $deTuChoi[] = $row;
    }
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/duyetdethi.css">

<div class="container container-main">
    <h2 class="title text-center mb-4">LỊCH SỬ DUYỆT ĐỀ THI</h2>

    <!-- Bộ lọc -->
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-end">
            <form method="GET" action="index.php" class="d-flex gap-3">
                <input type="hidden" name="controller" value="deThi">
                <input type="hidden" name="action" value="lichSuDuyetDeThi">

                <select name="maKhoi" class="form-select" style="width: 160px;">
                    <option value="">-- Khối học --</option>
                    <?php foreach ($khoiHocList as $khoi): ?>
                        <option value="<?= $khoi['maKhoi'] ?>" <?= ($_GET['maKhoi'] ?? '') == $khoi['maKhoi'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($khoi['tenKhoi']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="maNienKhoa" class="form-select" style="width: 160px;">
                    <option value="">-- Học kỳ --</option>
                    <?php foreach ($nienKhoaList as $nk): ?>
                        <option value="<?= $nk['maNienKhoa'] ?>" <?= ($_GET['maNienKhoa'] ?? '') == $nk['maNienKhoa'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nk['hocKy']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button class="btn btn-secondary" type="submit">Lọc</button>
            </form>
        </div>
    </div>

    <!-- Thông báo -->
    <?php
    /*
if (!empty($message)): 
?>
    <div class="alert alert-<?= $type ?> text-center">
        <?= htmlspecialchars($message) ?>
    </div>
<?php 
endif;
*/
    ?>

    <!-- Bảng Đã duyệt / Từ chối -->
    <div class="row">
        <div class="col-md-6">
            <div class="box">
                <h6 class="text-success text-center">ĐỀ THI ĐÃ DUYỆT</h6>
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-success">
                        <tr>
                            <th>Mã đề</th>
                            <th>Tiêu đề</th>
                            <th>Giáo viên</th>
                            <th>Ngày duyệt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($deDaDuyet): ?>
                            <?php foreach ($deDaDuyet as $row): ?>
                                <tr>
                                    <td><?= $row['maDeThi'] ?></td>
                                    <td><?= htmlspecialchars($row['tieuDe']) ?></td>
                                    <td><?= htmlspecialchars($row['hoTen']) ?></td>
                                    <td><?= $row['ngayDuyet'] ?? '-' ?></td>
                                    <td>
                                        <a href="index.php?controller=deThi&action=lichSuDuyetDeThi&maDeThi=<?= $row['maDeThi'] ?>&maKhoi=<?= $_GET['maKhoi'] ?? '' ?>&maNienKhoa=<?= $_GET['maNienKhoa'] ?? '' ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-muted">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box">
                <h6 class="text-danger text-center">ĐỀ THI BỊ TỪ CHỐI</h6>
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-danger">
                        <tr>
                            <th>Mã đề</th>
                            <th>Tiêu đề</th>
                            <th>Giáo viên</th>
                            <th>Ngày duyệt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($deTuChoi): ?>
                            <?php foreach ($deTuChoi as $row): ?>
                                <tr>
                                    <td><?= $row['maDeThi'] ?></td>
                                    <td><?= htmlspecialchars($row['tieuDe']) ?></td>
                                    <td><?= htmlspecialchars($row['hoTen']) ?></td>
                                    <td><?= $row['ngayDuyet'] ?? '-' ?></td>
                                    <td>
                                        <a href="index.php?controller=deThi&action=lichSuDuyetDeThi&maDeThi=<?= $row['maDeThi'] ?>&maKhoi=<?= $_GET['maKhoi'] ?? '' ?>&maNienKhoa=<?= $_GET['maNienKhoa'] ?? '' ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-muted">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chi tiết đề thi -->
    <?php if (!empty($examDetail)): ?>
        <?php $fileUrl = !empty($examDetail['noiDung']) ? 'uploads/dethi/' . $examDetail['noiDung'] : ''; ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="box">
                    <h6>Chi tiết đề thi</h6>
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
                            <th>Giáo viên ra đề</th>
                            <td><?= htmlspecialchars($examDetail['hoTen']) ?></td>
                        </tr>
                        <tr>
                            <th>Ngày nộp</th>
                            <td><?= $examDetail['ngayNop'] ?></td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td><?= hienThiTrangThai($examDetail['trangThai']) ?></td>
                        </tr>
                        <?php if ($examDetail['trangThai'] === 'TU_CHOI'): ?>
                            <tr>
                                <th>Lý do từ chối</th>
                                <td><?= htmlspecialchars($examDetail['ghiChu']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th>File đề thi</th>
                            <td>
                                <?php if ($fileUrl): ?>
                                    <a href="<?= $fileUrl ?>" download class="btn btn-sm btn-success">Xem đề thi</a>
                                <?php else: ?>
                                    <span class="text-muted">Không có file</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- MODAL THÔNG BÁO -->
<?php if ($message): ?>
    <div class="modal fade" id="thongBaoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header <?= ($message['status'] == 'success') ? 'bg-success' : 'bg-danger' ?> text-white">
                    <h5 class="modal-title">Thông báo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"><?= htmlspecialchars($message['text']) ?></div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = new bootstrap.Modal(document.getElementById('thongBaoModal'));
            modal.show();
        });
    </script>
<?php endif; ?>
<?php require_once 'views/layouts/footer.php'; ?>