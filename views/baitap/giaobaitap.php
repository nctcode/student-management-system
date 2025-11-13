<?php
// Xử lý mảng phancong để lấy danh sách duy nhất
$lopHocList = [];
$monHocList = [];
foreach ($danhSachPhanCong as $pc) {
    $lopHocList[$pc['maLop']] = $pc['tenLop'];
    $monHocList[$pc['maMonHoc']] = $pc['tenMonHoc'];
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Giao bài tập</strong></h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Nội dung bài tập</h5>
        </div>
        <div class="card-body">
            
            <form method="POST" action="index.php?controller=baitap&action=luu" id="formGiaoBaiTap" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="maLop"><strong>Chọn Lớp (*)</strong></label>
                            <select name="maLop" id="maLop" class="form-control" required>
                                <?php foreach ($lopHocList as $maLop => $tenLop): ?>
                                    <option value="<?= $maLop ?>"><?= htmlspecialchars($tenLop) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="maMonHoc"><strong>Chọn Môn học (*)</strong></label>
                            <select name="maMonHoc" id="maMonHoc" class="form-control" required>
                                <?php foreach ($monHocList as $maMonHoc => $tenMonHoc): ?>
                                    <option value="<?= $maMonHoc ?>"><?= htmlspecialchars($tenMonHoc) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hanNop"><strong>Hạn nộp (*)</strong></label>
                            <input type="datetime-local" name="hanNop" id="hanNop" class="form-control" required>
                        </div>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <label for="tenBT"><strong>Tên bài tập (*)</strong></label>
                    <input type="text" name="tenBT" id="tenBT" class="form-control" required 
                           placeholder="Ví dụ: Bài tập tuần 1 - Giới thiệu">
                </div>
                <br>
                <div class="form-group position-relative">
                    <label for="moTa"><strong>Mô tả chi tiết</strong></label>
                    <emoji-picker style="display: none; position: absolute; z-index: 1050; right: 20px; bottom: 150px;"></emoji-picker>
                    <textarea name="moTa" id="moTa" class="form-control" rows="5" 
                              placeholder="Nhập hướng dẫn hoặc yêu cầu cho học sinh..."
                              onkeyup="demKyTu(this)"></textarea>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="form-text text-muted"><span id="soKyTu">0</span>/1000 ký tự</small>
                        <button type="button" id="emojiBtn" class="btn btn-light btn-sm" title="Chèn biểu tượng">😊</button>
                    </div>
                    <script>
                        tinymce.init({
                            selector: 'textarea[name="moTa"]',
                            plugins: 'autolink lists link image charmap preview anchor pagebreak',
                            toolbar: 'undo redo | bold italic underline | ' + 
                                    'alignleft aligncenter alignright | ' +
                                    'bullist numlist outdent indent | link',
                            menubar: false,
                            height: 250
                        });
                    </script>
                </div>
                <br>
                <div class="form-group">
                    <label><strong>Đính kèm file</strong></label>
                    <div id="danhSachFile" class="mb-2">
                        </div>
                    <input type="file" name="fileDinhKem[]" id="fileDinhKem" class="form-control-file" 
                           onchange="hienThiFile()" multiple>
                    <br>
                    <small class="form-text text-muted">
                        • Có thể đính kèm nhiều file (tối đa 20MB).<br>
                        • Định dạng: PDF, DOC, JPG, PNG, MP4, ZIP...
                    </small>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <a href="index.php?controller=home&action=index" class="btn btn-danger btn-lg">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-success btn-lg ms-2">
                        <i class="fas fa-paper-plane"></i> Giao bài
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/baitap.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const picker = document.querySelector('emoji-picker');
    const emojiBtn = document.getElementById('emojiBtn');
    const textarea = document.querySelector('textarea[name="moTa"]');

    if(textarea) {
        if (!window.tinymce || !tinymce.get(textarea.id)) {
            textarea.focus();
        }
    }

    if (picker && emojiBtn && textarea) {
        picker.addEventListener('emoji-click', event => {
            if (window.tinymce && tinymce.get(textarea.id)) {
                tinymce.get(textarea.id).insertContent(event.detail.unicode);
            } else {
                textarea.value += event.detail.unicode;
            }
            picker.style.display = 'none';
        });

        emojiBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = picker.style.display === 'none';
            picker.style.display = isHidden ? 'block' : 'none';
        });

        document.addEventListener('click', (e) => {
            if (picker.style.display === 'block') {
                if (!picker.contains(e.target) && e.target !== emojiBtn) {
                    picker.style.display = 'none';
                }
            }
        });
    }
});
</script>
