<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar/bangiamhieu.php'; 
?>

<div class="container" style="padding: 20px; margin-left: 250px;">
    <h2>Tạo Phân công Ra đề mới</h2>

    <form action="index.php?controller=phancongrade&action=store" method="POST" style="max-width: 800px; margin: auto;">
        
        <div style="margin-bottom: 15px;">
            <label for="tieuDe" style="display: block; margin-bottom: 5px; font-weight: bold;">Chọn loại đề thi:</label>
            <select id="tieuDe" name="tieuDe" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <option value="">-- Chọn loại đề thi --</option>
                <option value="Kiểm tra 1 tiết (Giữa kỳ)">Kiểm tra 1 tiết (Giữa kỳ)</option>
                <option value="Kiểm tra cuối kỳ">Kiểm tra cuối kỳ</option>
                <option value="Đề thi Học sinh giỏi">Đề thi Học sinh giỏi</option>
                </select>
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

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Chọn Giáo viên phụ trách:</label>
            <div id="teacher-list-container" style="border: 1px solid #ccc; border-radius: 4px; padding: 15px; min-height: 100px; background-color: #f9f9f9;">
                <p style="color: #777;">Vui lòng chọn môn học để thấy danh sách giáo viên...</p>
            </div>
        </div>
        
        <div style="display: flex; justify-content: space-between; gap: 20px; margin-bottom: 15px;">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monHocSelect = document.getElementById('maMonHoc');
    const teacherContainer = document.getElementById('teacher-list-container');

    monHocSelect.addEventListener('change', function() {
        const monHocId = this.value;
        
        if (monHocId) {
            teacherContainer.innerHTML = '<p>Đang tải giáo viên...</p>';
            
            fetch(`index.php?controller=phancongrade&action=getGiaoVienByMonHoc&id_monhoc=${monHocId}`)
                .then(response => response.json())
                .then(teachers => {
                    if (teachers.length > 0) {
                        let html = '<table style="width: 100%;"><thead><tr>';
                        html += '<th style="text-align: left; padding: 5px;"><input type="checkbox" id="check-all-teachers"></th>';
                        html += '<th style="text-align: left; padding: 5px;">Mã GV</th>';
                        html += '<th style="text-align: left; padding: 5px;">Tên Giáo Viên</th>';
                        html += '</tr></thead><tbody>';

                        teachers.forEach(teacher => {
                            html += `<tr>`;
                            html += `<td style="padding: 5px;"><input type="checkbox" name="maGiaoVien[]" class="teacher-checkbox" value="${teacher.maGiaoVien}"></td>`;
                            html += `<td style="padding: 5px;">${teacher.maGiaoVien}</td>`;
                            html += `<td style="padding: 5px;">${teacher.hoTen}</td>`;
                            html += `</tr>`;
                        });
                        html += '</tbody></table>';
                        teacherContainer.innerHTML = html;

                        document.getElementById('check-all-teachers').addEventListener('change', function() {
                            document.querySelectorAll('.teacher-checkbox').forEach(checkbox => {
                                checkbox.checked = this.checked;
                            });
                        });

                    } else {
                        teacherContainer.innerHTML = '<p style="color: red;">Không tìm thấy giáo viên nào cho môn học này.</p>';
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải giáo viên:', error);
                    teacherContainer.innerHTML = '<p style="color: red;">Đã xảy ra lỗi khi tải danh sách.</p>';
                });
        } else {
            teacherContainer.innerHTML = '<p style="color: #777;">Vui lòng chọn môn học để thấy danh sách giáo viên...</p>';
        }
    });
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php'; 
?>