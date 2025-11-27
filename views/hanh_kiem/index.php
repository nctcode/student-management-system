<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý Hạnh Kiểm</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn-add { background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-back { display: inline-block; margin-top: 20px; text-decoration: none; font-weight: bold; color: #007bff;}
    </style>
</head>
<body>
    <h2>📋 Bảng Điểm Rèn Luyện</h2>

    <div style="margin-bottom: 20px;">
        <a href="index.php?controller=hanhkiem&action=add" class="btn-add">
            + Chấm Điểm Mới
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên Học Sinh</th>
                <th>Học Kỳ</th>
                <th>Điểm</th>
                <th>Xếp Loại</th>
                <th>Nhận Xét</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($dsHanhKiem && $dsHanhKiem->num_rows > 0): ?>
                <?php while ($row = $dsHanhKiem->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_hanh_kiem'] ?></td>
                        <td><?= $row['hoTen'] ?></td>
                        <td><?= $row['hoc_ky'] ?></td>
                        <td><b><?= $row['diem_so'] ?></b></td>
                        <td><?= $row['xep_loai'] ?></td>
                        <td><?= $row['nhan_xet'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align: center;">Chưa có dữ liệu. Hãy bấm nút Chấm điểm để thêm mới!</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <a href="index.php" class="btn-back">⬅ Về Trang Chủ</a>
</body>
</html>