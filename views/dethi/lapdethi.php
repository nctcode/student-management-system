<?php
// Lấy message từ session nếu có
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
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lập đề thi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        table input {
            width: 100%;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="content">
        <h1 class="text-center mb-4">Lập đề thi</h1>

        <form id="deThiForm" method="POST" action="index.php?controller=taodethi&action=store">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label for="khoi" class="form-label">Chọn khối:</label>
                    <select id="khoi" name="khoi" class="form-select" required>
                        <option value="">--Chọn khối--</option>
                        <option value="1">Khối 10</option>
                        <option value="2">Khối 11</option>
                        <option value="3">Khối 12</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="hocKy" class="form-label">Chọn học kỳ:</label>
                    <select id="hocKy" name="hocKy" class="form-select" required>
                        <option value="">--Chọn học kỳ--</option>
                        <option value="1">HK1</option>
                        <option value="2">HK2</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="tieuDe" class="form-label">Tiêu đề đề thi:</label>
                    <textarea name="tieuDe" id="tieuDe" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                    <label for="noiDungDeThi" class="form-label">Nội dung tổng quát đề thi:</label>
                    <textarea name="noiDungDeThi" id="noiDungDeThi" class="form-control" rows="2" required></textarea>
                </div>
            </div>

            <div class="row g-3 mb-3 align-items-end">
                <div class="col-md-2">
                    <label for="soLuongCau" class="form-label">Số lượng câu:</label>
                    <input type="number" id="soLuongCau" class="form-control" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary w-100" onclick="taoBang()">Tạo đề thi</button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-info w-100" id="btnXemDeThi">Xem đề thi đã tạo</button>
                </div>
            </div>

            <table class="table table-bordered mt-3" id="bangDeThi">
                <thead class="table-light">
                    <tr>
                        <th>Nội dung câu hỏi</th>
                        <th>Mức điểm</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <button type="submit" class="btn btn-success mt-3">Xác nhận tạo đề thi</button>
        </form>
        <!-- Bảng danh sách đề thi -->
        <div class="row mt-3" id="danhSachDeThiContainer" style="display: none;">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4>Danh sách đề thi đã tạo</h4>
                    <button type="button" class="btn btn-danger btn-sm" id="btnDongDanhSach">Đóng</button>
                </div>
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đề thi</th>
                            <th>Tiêu đề</th>
                            <th>Môn học</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($deThiList)): ?>
                            <?php foreach ($deThiList as $deThi): ?>
                                <tr>
                                    <td><?= htmlspecialchars($deThi['maDeThi']); ?></td>
                                    <td><?= htmlspecialchars($deThi['tieuDe']); ?></td>
                                    <td><?= htmlspecialchars($deThi['monHoc']); ?></td>
                                    <td><?= htmlspecialchars(hienThiTrangThai($deThi['trangThai'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary btnXemChiTiet" data-id="<?= $deThi['maDeThi']; ?>">
                                            Xem chi tiết
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Chưa có đề thi nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Chi tiết đề thi -->
        <div class="row mt-3" id="chiTietDeThiContainer" style="display: none;">
            <div class="col-12">
                <h4>Chi tiết đề thi</h4>
                <div id="chiTietDeThiContent"></div>
            </div>
        </div>
    </div>


    </div>

    <!-- Modal thông báo kết quả -->
    <div class="modal fade" id="thongBaoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header <?php echo ($message && $message['status'] == 'success') ? 'bg-success' : 'bg-danger'; ?> text-white">
                    <h5 class="modal-title">Thông báo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($message) echo htmlspecialchars($message['text']); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Biến từ PHP ra JS -->
    <script>
        window.deThiMessage = <?php echo $message ? json_encode($message): 'null' ?>;
        window.deThiList = <?php echo json_encode($deThiList); ?>;
    </script>

    <!-- File JS  -->
    <script src="assets/js/taodethi.js"></script>

</body>

</html>