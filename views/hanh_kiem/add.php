<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Chấm Điểm</title>
    <style>
        form { max-width: 500px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        input, select, textarea { width: 100%; padding: 8px; margin: 10px 0; box-sizing: border-box; }
        button { background: #007bff; color: white; padding: 10px; border: none; width: 100%; cursor: pointer; }
    </style>
</head>
<body>
    <form action="index.php?controller=hanhkiem&action=store" method="POST">
        <h3>✍ Chấm Điểm Rèn Luyện</h3>
        
        <label>Chọn Học Sinh:</label>
        <select name="sinh_vien_id" required>
            <?php while ($hs = $dsHocSinh->fetch_assoc()): ?>
                <option value="<?= $hs['maNguoiDung'] ?>">
                    <?= $hs['maNguoiDung'] ?> - <?= $hs['hoTen'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Học Kỳ:</label>
        <select name="hoc_ky">
            <option value="HK1-2024">Học kỳ 1 - 2024</option>
            <option value="HK2-2024">Học kỳ 2 - 2024</option>
        </select>

        <label>Điểm Số (0-100):</label>
        <input type="number" name="diem_so" min="0" max="100" required placeholder="Nhập điểm...">

        <label>Nhận Xét:</label>
        <textarea name="nhan_xet" placeholder="Ghi chú về học sinh..."></textarea>

        <button type="submit">Lưu Kết Quả</button>
        <br><br>
        <a href="index.php?controller=hanhkiem&action=index">Hủy bỏ</a>
    </form>
</body>
</html>