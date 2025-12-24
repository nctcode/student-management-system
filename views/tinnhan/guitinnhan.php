<?php
// Thêm vào đầu file guitinnhan.php (trước phần HTML)
$maTruong = $_SESSION['user']['maTruong'] ?? '';
?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Gửi tin nhắn</strong></h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="m-0 font-weight-bold text-primary">Chọn đối tượng nhận tin nhắn</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="checkHocSinh" value="HOCSINH" checked>
                            <label class="form-check-label" for="checkHocSinh">Học sinh</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="checkPhuHuynh" value="PHUHUYNH">
                            <label class="form-check-label" for="checkPhuHuynh">Phụ huynh</label>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <select class="form-control" id="selectLop">
                                    <option value="">Chọn lớp</option>
                                    <?php foreach ($danhSachLop as $lop): ?>
                                        <option value="<?= $lop['maLop'] ?>"><?= $lop['tenLop'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" id="timKiem" placeholder="Tìm kiếm...">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div id="danhSachHocSinh" style="display: none;">
                        <div class="danh-sach-scroll">
                            <h6 class="font-weight-bold text-center"id="titleHocSinh">DANH SÁCH HỌC SINH</h6>
                            <table class="table table-bordered table-sm" id="tableHocSinh">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="chonTatCaHS"></th>
                                        <th>Mã HS</th>
                                        <th>Học sinh</th>
                                        <th>Lớp</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyHocSinh">
                                    </tbody>
                            </table>
                        </div>
                        <div id="paginationHS" class="pagination-container mt-2"></div>
                    </div>
                    <br>
                    <div id="danhSachPhuHuynh" style="display: none;">
                        <div class="danh-sach-scroll">
                            <h6 class="font-weight-bold text-center">DANH SÁCH PHỤ HUYNH</h6>
                            <table class="table table-bordered table-sm" id="tablePhuHuynh">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="chonTatCaPH"></th>
                                        <th>Mã PH</th>
                                        <th>Phụ huynh</th>
                                        <th>Học sinh</th>
                                        <th>Lớp</th>
                                        <th>Email</th>
                                        <th>SĐT</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyPhuHuynh">
                                    </tbody>
                            </table>
                        </div>
                        <div id="paginationPH" class="pagination-container mt-2"></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group text-end">
                            <button type="button" class="btn btn-sm btn-info mr-2" id="btnChonTatCaLop">
                                <i class="fas fa-check-double"></i> Chọn tất cả (HS & PH)
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary" id="btnBoChonTatCa">
                                <i class="fas fa-undo"></i> Bỏ chọn tất cả
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <strong>Đã chọn: <span id="soLuongChon">0</span></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="m-0 font-weight-bold text-primary">Gửi tin nhắn</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="formGuiTinNhan">
                        <div class="form-group">
                            <label><strong>Người nhận (*)</strong></label>
                            <div class="border rounded p-2 bg-light" id="danhSachNguoiNhan" style="min-height: 40px;">
                                </div>
                            <input type="hidden" name="nguoiNhan" id="hiddenNguoiNhan">
                        </div>
                        <br>

                        <?php 
                        $old = $_SESSION['old_tinnhan'] ?? null;
                        unset($_SESSION['old_tinnhan']); 
                        ?>
                        <div class="form-group">
                            <label><strong>Tiêu đề (*)</strong></label>
                            <input type="text" name="tieuDe" class="form-control"
                            value="<?= htmlspecialchars($old['tieuDe'] ?? '') ?>" placeholder="Nhập tiêu đề tin nhắn">
                        </div>
                        <br>
                        <div class="form-group position-relative">
                            <label><strong>Nội dung tin nhắn (*)</strong></label>
                            <emoji-picker style="display: none; position: absolute; z-index: 1050; right: 20px; bottom: 150px;"></emoji-picker>
                            <textarea name="noiDung"  id="noiDungTinNhan" class="form-control" rows="6" placeholder="Nhập nội dung tin nhắn..."><?= $old['noiDung'] ?? '' ?></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="form-text text-muted">
                                    <span id="soKyTu">0</span>/1000 ký tự
                                </small>
                                <button type="button" id="emojiBtn" class="btn btn-light btn-sm" title="Chèn biểu tượng">😊</button>
                            </div>
                            <script>
                                tinymce.init({
                                    selector: 'textarea[name="noiDung"]',
                                    plugins: 'autolink lists link image charmap preview anchor pagebreak',
                                    toolbar: 'undo redo | bold italic underline | ' + 
                                            'alignleft aligncenter alignright | ' +
                                            'bullist numlist outdent indent | link',
                                    menubar: false,
                                    height: 250,
                                    setup: function(editor) {
                                        editor.on('keyup Change SetContent', function(e) {
                                            var content = editor.getContent({ format: 'text' });
                                            var fakeTextarea = { value: content };
                                            if (window.demKyTu) { 
                                                window.demKyTu(fakeTextarea);
                                            }
                                        });
                                    }
                                });
                            </script>
                        </div>
                        <script>
                            window.oldNguoiNhan = "<?= $old['nguoiNhan'] ?? '' ?>";
                        </script>
                        <br>
                        <div class="form-group">
                            <label><strong>Đính kèm file</strong></label>
                            <div id="danhSachFile" class="mb-2">
                                </div>
                            <input type="file" name="fileDinhKem[]" id="fileDinhKem" class="form-control-file" 
                                onchange="hienThiFile()" multiple>
                            <br>
                            <small class="form-text text-muted">
                                • File đính kèm tối đa 10MB<br>
                                • Định dạng hỗ trợ: PDF, DOC, JPG, PNG, XLSX<br>
                            </small>
                        </div>
                        <br>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-danger btn-lg" onclick="window.location.href='index.php'">
                                <i class="fas fa-times"></i> Hủy
                            </button>
                            <button type="submit" class="btn btn-success btn-lg ms-2">
                                <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAlertTN" data-backdrop="static" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Cảnh báo lỗi
                </h5>
                <button type="button" class="btn-close-custom" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <i id="iconModalTN" class="fas fa-file-excel fa-3x text-danger mb-3"></i>
                <p id="msgModalTN" class="mb-0 text-secondary font-weight-bold"></p>
            </div>
            <div class="modal-footer border-0 py-3 justify-content-center">
                <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fas fa-check-circle mr-1"></i> Đã hiểu
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Trong phần script của guitinnhan.php - thêm biến toàn cục
    const maTruong = '<?= $maTruong ?>';
    document.addEventListener('DOMContentLoaded', function() {
    const picker = document.querySelector('emoji-picker');
    const emojiBtn = document.getElementById('emojiBtn');
    const textarea = document.querySelector('textarea[name="noiDung"]');

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

<link rel="stylesheet" href="assets/css/tinnhan.css">
<script src="assets/js/tinnhan.js"></script>