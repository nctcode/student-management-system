<?php
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

function hienThiTrangThai($trangThai)
{
    switch ($trangThai) {
        case 'CHO_DUYET': return 'Chờ duyệt';
        case 'DA_DUYET': return 'Đã duyệt';
        case 'TU_CHOI': return 'Từ chối';
        default: return '';
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
        body { background-color: #f9f9f9; }
        .content { margin-left: 250px; padding: 20px; }
        @media (max-width: 768px) { .content { margin-left:0; padding:10px; } }
    </style>
</head>
<body>
<div class="content">
    <h1 class="text-center mb-4">Lập đề thi</h1>

    <!-- FORM TẠO ĐỀ THI -->
    <form method="POST" action="index.php?controller=deThi&action=store"
          enctype="multipart/form-data" class="p-3 border rounded bg-white shadow-sm">

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Chọn khối:</label>
                <select name="khoi" class="form-select" required>
                    <option value="">--Chọn khối--</option>
                    <option value="1">Khối 10</option>
                    <option value="2">Khối 11</option>
                    <option value="3">Khối 12</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Chọn học kỳ:</label>
                <select name="hocKy" class="form-select" required>
                    <option value="">--Chọn học kỳ--</option>
                    <option value="1">HK1</option>
                    <option value="2">HK2</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Tiêu đề đề thi:</label>
                <textarea name="tieuDe" class="form-control" rows="2" required></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label">Tải lên file đề thi (PDF/Word):</label>
                <input type="file" name="fileDeThi" class="form-control" accept=".pdf,.doc,.docx" required>
            </div>
        </div>

        <button type="submit" class="btn btn-success me-2">Xác nhận tạo đề thi</button>
        <button type="button" class="btn btn-info" id="btnXemDeThi">Xem danh sách đề thi</button>
    </form>

    <!-- DANH SÁCH ĐỀ THI -->
    <div class="row mt-4" id="danhSachDeThiContainer" style="display:none;">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4>Danh sách đề thi đã tạo</h4>
                <button type="button" class="btn btn-danger btn-sm" id="btnDongDanhSach">Đóng</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đề thi</th>
                            <th>Tiêu đề</th>
                            <th>Môn học</th>
                            <th>Trạng thái</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($deThiList)): ?>
                        <?php foreach ($deThiList as $deThi): ?>
                            <tr>
                                <td><?= $deThi['maDeThi'] ?></td>
                                <td><?= htmlspecialchars($deThi['tieuDe']) ?></td>
                                <td><?= htmlspecialchars($deThi['monHoc']) ?></td>
                                <td><?= hienThiTrangThai($deThi['trangThai']) ?></td>
                                <td>
                                    <?php if (!empty($deThi['fileDeThi'])): ?>
                                        <a href="uploads/dethi/<?= htmlspecialchars($deThi['fileDeThi']) ?>" target="_blank">Tải xuống</a>
                                    <?php else: ?>-<?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Chưa có đề thi nào</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL THÔNG BÁO -->
    <?php if ($message): ?>
    <div class="modal fade" id="thongBaoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header <?= ($message['status']=='success')?'bg-success':'bg-danger' ?> text-white">
                    <h5 class="modal-title">Thông báo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"><?= htmlspecialchars($message['text']) ?></div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="btnOkModal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const btnXem = document.getElementById('btnXemDeThi');
const btnDong = document.getElementById('btnDongDanhSach');
const danhSach = document.getElementById('danhSachDeThiContainer');

btnXem.addEventListener('click', ()=>{ danhSach.style.display='block'; window.scrollTo(0,danhSach.offsetTop); });
btnDong.addEventListener('click', ()=>{ danhSach.style.display='none'; });

<?php if ($message): ?>
    var modal = new bootstrap.Modal(document.getElementById('thongBaoModal'));
    modal.show();

    // Nếu message là not_assigned, redirect khi nhấn OK
    document.getElementById('btnOkModal').addEventListener('click', function() {
        modal.hide();
        <?php if ($message['status'] === 'not_assigned'): ?>
            window.location.href = "index.php?controller=home&action=teacher";
        <?php endif; ?>
    });
<?php endif; ?>
</script>
</body>
</html>
