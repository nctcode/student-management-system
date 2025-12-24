<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar/totruong.php'; 
?>

<div class="main-content" style="margin-left: 250px; padding: 20px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Chỉnh sửa Phân công Ra đề</h2>
            <a href="index.php?controller=phancongrade" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="index.php?controller=phancongrade&action=update">
                    <input type="hidden" name="id" value="<?php echo $deThi['maDeThi']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tieuDe" class="form-label">Tiêu đề đề thi *</label>
                            <input type="text" class="form-control" id="tieuDe" name="tieuDe" 
                                   value="<?php echo htmlspecialchars($deThi['tieuDe']); ?>" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="maKhoi" class="form-label">Khối *</label>
                            <select class="form-select" id="maKhoi" name="maKhoi" required>
                                <option value="">-- Chọn khối --</option>
                                <?php foreach ($danhSachKhoi as $khoi): ?>
                                    <option value="<?php echo $khoi['maKhoi']; ?>" 
                                        <?php echo $khoi['maKhoi'] == $deThi['maKhoi'] ? 'selected' : ''; ?>>
                                        Khối <?php echo htmlspecialchars($khoi['tenKhoi']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="maMonHoc" class="form-label">Môn học *</label>
                            <select class="form-select" id="maMonHoc" name="maMonHoc" required>
                                <option value="">-- Chọn môn học --</option>
                                <?php foreach ($danhSachMonHoc as $monhoc): ?>
                                    <option value="<?php echo $monhoc['maMonHoc']; ?>" 
                                        <?php echo $monhoc['maMonHoc'] == $deThi['maMonHoc'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($monhoc['tenMonHoc']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="maGiaoVien" class="form-label">Phân công giáo viên *</label>
                            <select class="form-select" id="maGiaoVien" name="maGiaoVien[]" 
                                    multiple required size="5">
                                <!-- JavaScript sẽ load danh sách GV -->
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="hanNopDe" class="form-label">Hạn nộp đề *</label>
                            <input type="datetime-local" class="form-control" id="hanNopDe" 
                                   name="hanNopDe" value="<?php echo date('Y-m-d\TH:i', strtotime($deThi['hanNopDe'])); ?>" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="soLuongDe" class="form-label">Số lượng đề</label>
                            <input type="number" class="form-control" id="soLuongDe" 
                                   name="soLuongDe" value="<?php echo $deThi['soLuongDe']; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="noiDung" class="form-label">Nội dung yêu cầu</label>
                        <textarea class="form-control" id="noiDung" name="noiDung" rows="3"><?php echo htmlspecialchars($deThi['noiDung']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="ghiChu" class="form-label">Ghi chú thêm</label>
                        <textarea class="form-control" id="ghiChu" name="ghiChu" rows="2"><?php echo htmlspecialchars($deThi['ghiChu']); ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php?controller=phancongrade" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const maMonHocSelect = document.getElementById('maMonHoc');
    const maGiaoVienSelect = document.getElementById('maGiaoVien');
    const currentMaMonHoc = <?php echo $deThi['maMonHoc']; ?>;
    const currentGiaoVien = <?php echo json_encode(explode(',', $deThi['dsMaGiaoVien'] ?? '')); ?>;
    
    // Load danh sách giáo viên khi trang load
    loadGiaoVien(currentMaMonHoc);
    
    maMonHocSelect.addEventListener('change', function() {
        loadGiaoVien(this.value);
    });
    
    function loadGiaoVien(maMonHoc) {
        if (!maMonHoc) {
            maGiaoVienSelect.innerHTML = '<option value="">-- Chọn môn học trước --</option>';
            return;
        }
        
        maGiaoVienSelect.innerHTML = '<option value="">Đang tải...</option>';
        
        fetch(`index.php?controller=phancongrade&action=getGiaoVienByMonHoc&id_monhoc=${maMonHoc}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">-- Chọn giáo viên --</option>';
                data.forEach(gv => {
                    const selected = currentGiaoVien.includes(gv.maGiaoVien.toString()) ? 'selected' : '';
                    options += `<option value="${gv.maGiaoVien}" ${selected}>${gv.hoTen}</option>`;
                });
                maGiaoVienSelect.innerHTML = options;
            })
            .catch(error => {
                console.error('Lỗi:', error);
                maGiaoVienSelect.innerHTML = '<option value="">Lỗi tải danh sách</option>';
            });
    }
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php'; 
?>