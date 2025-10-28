<?php
// Giả sử file này được include vào layout chính (header/sidebar) của bạn
?>

<div class="container" style="padding: 20px;">
    <h2>Tạo Phân công Ra đề mới</h2>

    <form action="index.php?controller=phancongrade&action=store" method="POST" style="max-width: 800px; margin: auto;">
        
        <div style="margin-bottom: 15px;">
            <label for="tieuDe" style="display: block; margin-bottom: 5px; font-weight: bold;">Tiêu đề đề thi:</label>
            <input type="text" id="tieuDe" name="tieuDe" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="display: flex; justify-content: space-between; gap: 20px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label for="maKhoi" style="display: block; margin-bottom: 5px; font-weight: bold;">Chọn Khối:</label>
                <select id="maKhoi" name="maKhoi" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">-- Chọn Khối --</option>
                    <?php if (isset($danhSachKhoi)): ?>
                        <?php foreach ($danhSachKhoi as $khoi): ?>
                            <option value="<?php echo $khoi['maKhoi']; ?>"><?php echo htmlspecialchars($khoi['tenKhoi']); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="flex: 1;">
                <label for="maMonHoc" style="display: block; margin-bottom: 5px; font-weight: bold;">Chọn Môn học:</label>
                <select id="maMonHoc" name="maMonHoc" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">-- Chọn Môn học --</option>
                    <?php if (isset($danhSachMonHoc)): ?>
                        <?php foreach ($danhSachMonHoc as $mon): ?>
                            <option value="<?php echo $mon['maMonHoc']; ?>"><?php echo htmlspecialchars($mon['tenMonHoc']); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; gap: 20px; margin-bottom: 15px;">
            <div style="flex: 2;">
                <label for="maGiaoVien" style="display: block; margin-bottom: 5px; font-weight: bold;">Giáo viên phụ trách:</label>
                <select id="maGiaoVien" name="maGiaoVien" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="">-- Chọn Giáo viên --</option>
                    <?php if (isset($danhSachGiaoVien)): ?>
                        <?php foreach ($danhSachGiaoVien as $gv): ?>
                            <option value="<?php echo $gv['maGiaoVien']; ?>"><?php echo htmlspecialchars($gv['hoTen']); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="flex: 1.5;">
                <label for="hanNopDe" style="display: block; margin-bottom: 5px; font-weight: bold;">Hạn nộp đề:</label>
                <input type="datetime-local" id="hanNopDe" name="hanNopDe" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <label for="soLuongDe" style="display: block; margin-bottom: 5px; font-weight: bold;">Số lượng đề:</label>
                <input type="number" id="soLuongDe" name="soLuongDe" value="2" min="1" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="noiDung" style="display: block; margin-bottom: 5px; font-weight: bold;">Nội dung / Yêu cầu:</label>
            <textarea id="noiDung" name="noiDung" rows="4" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="ghiChu" style="display: block; margin-bottom: 5px; font-weight: bold;">Ghi chú (phân công):</label>
            <textarea id="ghiChu" name="ghiChu" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
        </div>

        <div>
            <button type="submit" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Lưu Phân công</button>
            <a href="index.php?controller=phancongrade" style="padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">Hủy</a>
        </div>
    </form>
</div>