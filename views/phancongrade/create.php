<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar/totruong.php'; 
?>

<div class="main-content" style="margin-left: 250px; padding: 20px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Tạo Phân công Ra đề Mới</h2>
            <a href="index.php?controller=phancongrade" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="index.php?controller=phancongrade&action=store">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tieuDe" class="form-label">Tiêu đề đề thi *</label>
                            <input type="text" class="form-control" id="tieuDe" name="tieuDe" 
                                   required placeholder="Ví dụ: Đề thi học kỳ 1 Toán 10">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="maKhoi" class="form-label">Khối *</label>
                            <select class="form-select" id="maKhoi" name="maKhoi" required>
                                <option value="">-- Chọn khối --</option>
                                <?php foreach ($danhSachKhoi as $khoi): ?>
                                    <option value="<?php echo $khoi['maKhoi']; ?>">
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
                                    <option value="<?php echo $monhoc['maMonHoc']; ?>">
                                        <?php echo htmlspecialchars($monhoc['tenMonHoc']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Học kỳ *</label>
                            <select name="maNienKhoa" class="form-select" required id="selectNienKhoa">
                                <option value="">-- Chọn học kỳ --</option>
                                <?php if (!empty($danhSachNienKhoa)): ?>
                                    <?php foreach ($danhSachNienKhoa as $nk): 
                                        // Loại bỏ học kỳ "Cả năm"
                                        if ($nk['hocKy'] === 'CA_NAM') continue;
                                        
                                        // Map học kỳ theo yêu cầu: 2=HK1, 3=HK2
                                        $hocKyText = '';
                                        switch($nk['hocKy']) {
                                            case 'HK1':
                                                $hocKyText = 'Học kỳ 1';
                                                break;
                                            case 'HK2':
                                                $hocKyText = 'Học kỳ 2';
                                                break;
                                            default:
                                                // Bỏ qua nếu không phải HK1/HK2
                                                continue 2;
                                        }
                                        
                                        $selected = '';
                                        if (isset($nienKhoaHienTai) && $nienKhoaHienTai['maNienKhoa'] == $nk['maNienKhoa']) {
                                            $selected = 'selected';
                                        }
                                    ?>
                                        <option value="<?= $nk['maNienKhoa'] ?>" <?= $selected ?>>
                                            <?= $hocKyText ?> - <?= $nk['namHoc'] ?>
                                            <?php if ($nk['maNienKhoa'] == 2): ?>
                                                (HK1)
                                            <?php elseif ($nk['maNienKhoa'] == 3): ?>
                                                (HK2)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="maGiaoVien" class="form-label">Phân công giáo viên *</label>
                            <select class="form-select" id="maGiaoVien" name="maGiaoVien[]" 
                                    multiple required size="5">
                                <option value="">-- Chọn môn học trước --</option>
                            </select>
                            <div class="form-text">
                                Giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều giáo viên
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="hanNopDe" class="form-label">Hạn nộp đề *</label>
                            <input type="datetime-local" class="form-control" id="hanNopDe" 
                                   name="hanNopDe" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="soLuongDe" class="form-label">Số lượng đề</label>
                            <input type="number" class="form-control" id="soLuongDe" 
                                   name="soLuongDe" min="1" max="10" value="1">
                            <div class="form-text">Số đề cần ra (mặc định: 1)</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="noiDung" class="form-label">Nội dung yêu cầu</label>
                        <textarea class="form-control" id="noiDung" name="noiDung" 
                                  rows="3" placeholder="Mô tả yêu cầu về đề thi..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="ghiChu" class="form-label">Ghi chú thêm</label>
                        <textarea class="form-control" id="ghiChu" name="ghiChu" 
                                  rows="2" placeholder="Ghi chú cho giáo viên..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-secondary">Nhập lại</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu phân công
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
    
    maMonHocSelect.addEventListener('change', function() {
        const maMonHoc = this.value;
        
        if (!maMonHoc) {
            maGiaoVienSelect.innerHTML = '<option value="">-- Chọn môn học trước --</option>';
            return;
        }
        
        // Hiển thị loading
        maGiaoVienSelect.innerHTML = '<option value="">Đang tải danh sách giáo viên...</option>';
        
        // Debug URL
        const url = `index.php?controller=phancongrade&action=getGiaoVienByMonHoc&id_monhoc=${maMonHoc}`;
        console.log('URL:', url);
        
        // Gọi AJAX để lấy danh sách giáo viên
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers.get('Content-Type'));
                
                // Kiểm tra nếu response là HTML thay vì JSON
                const contentType = response.headers.get('Content-Type');
                if (contentType && contentType.includes('text/html')) {
                    throw new Error('Server trả về HTML thay vì JSON');
                }
                
                return response.text(); // Dùng text() trước để xem nội dung
            })
            .then(text => {
                console.log('Raw response text:', text.substring(0, 500)); // Chỉ hiển thị 500 ký tự đầu
                
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed JSON:', data);
                    
                    if (Array.isArray(data)) {
                        if (data.length > 0) {
                            let options = '<option value="">-- Chọn giáo viên --</option>';
                            data.forEach(gv => {
                                options += `<option value="${gv.maGiaoVien}">${gv.hoTen}</option>`;
                            });
                            maGiaoVienSelect.innerHTML = options;
                        } else {
                            maGiaoVienSelect.innerHTML = '<option value="">Không có giáo viên nào cho môn học này</option>';
                        }
                    } else if (data.error) {
                        maGiaoVienSelect.innerHTML = `<option value="">Lỗi: ${data.error}</option>`;
                    }
                } catch (e) {
                    console.error('Lỗi parse JSON:', e);
                    maGiaoVienSelect.innerHTML = '<option value="">Lỗi định dạng dữ liệu</option>';
                }
            })
            .catch(error => {
                console.error('Lỗi fetch:', error);
                maGiaoVienSelect.innerHTML = '<option value="">Lỗi kết nối server</option>';
            });
    });
    
    // Đặt hạn nộp mặc định là ngày mai
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('hanNopDe').value = tomorrow.toISOString().slice(0, 16);
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php'; 
?>