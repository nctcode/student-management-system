<?php
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

function hienThiTrangThai($trangThai)
{
    switch ($trangThai) {
        case 'CHO_DUYET':
            return 'Chờ duyệt';
        case 'DA_DUYET':
            return 'Đã duyệt';
        case 'TU_CHOI':
            return 'Từ chối';
        default:
            return '';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyệt đề thi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 10px;
            }
        }

        .box {
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="content">
        <h1 class="text-center mb-4">Duyệt đề thi</h1>

        <!-- Bộ lọc -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="box">
                    <h6>Chọn Khối học & Học kỳ:</h6>
                    <form method="GET" action="">
                        <input type="hidden" name="controller" value="dethi">
                        <input type="hidden" name="action" value="duyet">

                        <select name="maKhoi" class="form-select mb-3" required>
                            <option value="">-- Khối học --</option>
                            <?php foreach ($khoiHocList as $khoi): ?>
                                <option value="<?= $khoi['maKhoi'] ?>" <?= ($maKhoi == $khoi['maKhoi']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($khoi['tenKhoi']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="maNienKhoa" class="form-select mb-3" required>
                            <option value="">-- Học kỳ --</option>
                            <?php foreach ($nienKhoaList as $nk): ?>
                                <option value="<?= $nk['maNienKhoa'] ?>" <?= ($maNienKhoa == $nk['maNienKhoa']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($nk['hocKy']) ?>
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
                    <?php if (!$maKhoi || !$maNienKhoa): ?>
                        <p class="text-muted">Vui lòng chọn Khối và Học kỳ để hiển thị danh sách đề thi.</p>
                    <?php else: ?>
                        <?php if (!empty($exams)): ?>
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
                                    <?php foreach ($exams as $exam): ?>
                                        <tr>
                                            <td><?= $exam['maDeThi'] ?></td>
                                            <td><?= htmlspecialchars($exam['hoTen']) ?></td>
                                            <td><?= htmlspecialchars($exam['tieuDe']) ?></td>
                                            <td>
                                                <a href="?controller=dethi&action=duyet&maKhoi=<?= $maKhoi ?>&maNienKhoa=<?= $maNienKhoa ?>&maDeThi=<?= $exam['maDeThi'] ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">Không có đề thi nào phù hợp.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chi tiết đề thi -->
        <?php if ($examDetail): ?>
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <h6>Chi tiết đề thi:</h6>
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
                                <td><?= htmlspecialchars($examDetail['hoTen']) ?></td>
                            </tr>
                            <tr>
                                <th>Ngày nộp</th>
                                <td><?= $examDetail['ngayNop'] ?></td>
                            </tr>
                            <tr>
                                <th>Môn học</th>
                                <td><?= htmlspecialchars($examDetail['monHoc']) ?></td>
                            </tr>
                            <tr>
                                <th>Học kỳ</th>
                                <td><?= htmlspecialchars($examDetail['maNienKhoa']) ?></td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td><?= hienThiTrangThai($examDetail['trangThai']) ?></td>
                            </tr>
                            <tr>
                                <th>File đề thi</th>
                                <td>
                                    <?php if (!empty($examDetail['noiDung'])): ?>
                                        <a href="uploads/dethi/<?= htmlspecialchars($examDetail['noiDung']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">Xem/ Tải về</a>
                                        <?php else: ?>Không có file<?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Form duyệt/từ chối -->
                    <form id="formDuyet" method="POST" action="index.php?controller=dethi&action=capNhatTrangThai">
                        <input type="hidden" name="maDeThi" value="<?= $examDetail['maDeThi'] ?>">
                        <input type="hidden" name="maKhoi" value="<?= $maKhoi ?>">
                        <input type="hidden" name="maNienKhoa" value="<?= $maNienKhoa ?>">
                        <input type="hidden" id="hanhDongInput" name="hanhDong" value="">

                        <div class="mt-3" id="divGhiChu" style="display:none;">
                            <label for="ghiChu" class="form-label">Lý do từ chối:</label>
                            <textarea id="ghiChu" name="ghiChu" class="form-control" rows="3" placeholder="Nhập lý do nếu bạn từ chối..."></textarea>
                            <div id="errorMessage" class="text-danger mt-2" style="display:none;">❗ Vui lòng nhập lý do từ chối trước khi gửi!</div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" id="btnDuyet" class="btn btn-success btn-action">Duyệt đề</button>
                            <button type="button" id="btnTuChoi" class="btn btn-danger btn-action">Từ chối</button>
                        </div>
                    </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnTuChoi = document.getElementById('btnTuChoi');
            const btnDuyet = document.getElementById('btnDuyet');
            const divGhiChu = document.getElementById('divGhiChu');
            const ghiChu = document.getElementById('ghiChu');
            const errorMsg = document.getElementById('errorMessage');
            const form = document.getElementById('formDuyet');
            const hanhDongInput = document.getElementById('hanhDongInput');

            function updateDuyetState() {
                // Nếu ô lý do có giá trị thì disable nút duyệt
                btnDuyet.disabled = ghiChu.value.trim() !== '';
            }

            if (btnTuChoi && btnDuyet) {
                // Khi click Từ chối: hiện/ẩn ô ghi chú
                btnTuChoi.addEventListener('click', () => {
                    if (divGhiChu.style.display === 'none') {
                        divGhiChu.style.display = 'block';
                        ghiChu.focus();
                        hanhDongInput.value = 'tuchoi';
                        updateDuyetState();
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

                // Khi click Duyệt
                btnDuyet.addEventListener('click', () => {
                    hanhDongInput.value = 'duyet';
                    form.submit();
                });

                // Kiểm tra trước khi submit
                form.addEventListener('submit', (e) => {
                    if (hanhDongInput.value === 'tuchoi' && ghiChu.value.trim() === '') {
                        e.preventDefault();
                        errorMsg.style.display = 'block';
                        ghiChu.focus();
                    }
                });

                // Bắt sự kiện nhập lý do: cập nhật trạng thái nút Duyệt
                ghiChu.addEventListener('input', updateDuyetState);
            } 

            // Khởi tạo trạng thái nút Duyệt
            updateDuyetState();
        });
    </script>

</body>

</html>