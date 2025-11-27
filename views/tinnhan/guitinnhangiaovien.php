<?php
// Thêm vào đầu file views/tinnhan/guitinnhangiaovien.php, trước phần HTML
?>
<link rel="stylesheet" href="assets/css/tinnhan.css">
<style>
.badge-gv-selected {
    background-color: #4e73df !important;
    color: white !important;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.35rem;
    margin: 0.25rem;
    display: inline-flex;
    align-items: center;
    border: 1px solid #2e59d9;
}

.badge-gv-selected .btn-close-gv {
    background: none;
    border: none;
    color: white;
    margin-left: 8px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: bold;
    padding: 0;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.badge-gv-selected .btn-close-gv:hover {
    color: #ff6b6b;
}

#danhSachGiaoVienNhan {
    min-height: 40px;
    padding: 8px;
    background-color: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

#danhSachGiaoVienNhan:empty::before {
    content: "Chọn giáo viên từ danh sách bên trái";
    color: #6c757d;
    font-style: italic;
}
</style>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Gửi tin nhắn cho giáo viên</strong></h1>
    
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
                    <h5 class="m-0 font-weight-bold text-primary">Chọn giáo viên</h5>
                </div>
                <div class="card-body">
                    <?php if ($_SESSION['user']['vaiTro'] === 'PHUHUYNH' && !empty($thongTinNguoiGui['hocSinh'])): ?>
                    <div class="form-group">
                        <label><strong>Chọn lớp</strong></label>
                        <select class="form-control" id="selectLop">
                            <option value="">Tất cả các lớp</option>
                            <?php 
                            $cacLop = [];
                            foreach ($thongTinNguoiGui['hocSinh'] as $hs) {
                                if (!in_array($hs['maLop'], array_column($cacLop, 'maLop'))) {
                                    $cacLop[] = ['maLop' => $hs['maLop'], 'tenLop' => $hs['tenLop']];
                                }
                            }
                            foreach ($cacLop as $lop): 
                            ?>
                            <option value="<?= $lop['maLop'] ?>"><?= $lop['tenLop'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <input type="text" class="form-control" id="timKiemGV" placeholder="Tìm kiếm giáo viên...">
                    </div>

                    <div id="danhSachGiaoVien">
                        <div class="danh-sach-scroll">
                            <h6 class="font-weight-bold text-center">DANH SÁCH GIÁO VIÊN</h6>
                            <table class="table table-bordered table-sm" id="tableGiaoVien">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="chonTatCaGV"></th>
                                        <th>Mã GV</th>
                                        <th>Giáo viên</th>
                                        <th>Vai trò</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyGiaoVien">
                                    <!-- Dữ liệu sẽ được tải bằng JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div id="paginationGV" class="pagination-container mt-2"></div>
                    </div>

                    <div class="mt-3">
                        <strong>Đã chọn: <span id="soLuongChonGV">0</span> giáo viên</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="m-0 font-weight-bold text-primary">Soạn tin nhắn</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="formGuiTinNhanGV">
                        <div class="form-group">
                            <label><strong>Giáo viên nhận (*)</strong></label>
                            <div class="border rounded p-2 bg-light" id="danhSachGiaoVienNhan" style="min-height: 40px;">
                                <small class="text-muted">Chọn giáo viên từ danh sách bên trái</small>
                            </div>
                            <input type="hidden" name="giaoVienNhan" id="hiddenGiaoVienNhan">
                        </div>

                        <div class="form-group">
                            <label><strong>Tiêu đề (*)</strong></label>
                            <input type="text" name="tieuDe" class="form-control" required 
                                   placeholder="Nhập tiêu đề tin nhắn" 
                                   value="<?= isset($thongTinNguoiGui['hocSinh']) ? 'Tin nhắn từ phụ huynh' : 'Tin nhắn từ học sinh' ?>">
                        </div>

                        <div class="form-group position-relative">
                            <label><strong>Nội dung tin nhắn (*)</strong></label>
                            <textarea name="noiDung" id="noiDungTinNhanGV" class="form-control" rows="6" required 
                                      placeholder="Nhập nội dung tin nhắn..." 
                                      onkeyup="demKyTuGV(this)"></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small class="form-text text-muted">
                                    <span id="soKyTuGV">0</span>/1000 ký tự
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><strong>Đính kèm file</strong></label>
                            <div id="danhSachFileGV" class="mb-2"></div>
                            <input type="file" name="fileDinhKem[]" id="fileDinhKemGV" class="form-control-file" 
                                onchange="hienThiFileGV()" multiple>
                            <small class="form-text text-muted">
                                • File đính kèm tối đa 10MB<br>
                                • Định dạng hỗ trợ: PDF, DOC, JPG, PNG, XLSX
                            </small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-danger btn-lg" onclick="history.back()">
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

<script src="assets/js/tinnhan_gv.js"></script>
