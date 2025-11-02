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
                    <h5 class="m-0 font-weight-bold text-primary">CHỌN ĐỐI TƯỢNG NHẬN TIN NHẮN</h5>
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

                    <div class="mt-3">
                        <strong>Đã chọn: <span id="soLuongChon">0</span></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h5 class="m-0 font-weight-bold text-primary">GỬI TIN NHẮN</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="formGuiTinNhan">
                        <div class="form-group">
                            <label><strong>Người nhận</strong></label>
                            <div class="border rounded p-2 bg-light" id="danhSachNguoiNhan" style="min-height: 40px;">
                                </div>
                            <input type="hidden" name="nguoiNhan" id="hiddenNguoiNhan">
                        </div>

                        <div class="form-group">
                            <label><strong>Tiêu đề</strong></label>
                            <input type="text" name="tieuDe" class="form-control" required placeholder="Nhập tiêu đề tin nhắn">
                        </div>

                        <div class="form-group">
                            <label><strong>Nội dung tin nhắn</strong></label>
                            <textarea name="noiDung" class="form-control" rows="6" required 
                                      placeholder="Nhập nội dung tin nhắn..." 
                                      onkeyup="demKyTu(this)"></textarea>
                            <small class="form-text text-muted">
                                <span id="soKyTu">0</span>/1000 ký tự
                            </small>
                        </div>

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
                                • Không gửi nội dung không phù hợp
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

<link rel="stylesheet" href="assets/css/tinnhan.css">
<script src="assets/js/tinnhan.js"></script>