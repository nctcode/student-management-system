<div class="container mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>✉️ Soạn tin nhắn</h4>
        <a href="index.php?controller=tinnhan&action=index" class="btn btn-secondary">← Quay lại danh sách</a>
    </div>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="index.php?controller=tinnhan&action=guitin">
        
        <div class="row g-4"> <div class="col-lg-7">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title mb-3">CHỌN ĐỐI TƯỢNG NHẬN</h5>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <label class="form-label small">Đối tượng:</label>
                            <div>
                                <label class="form-check-label"><input type="checkbox" id="check_hoc_sinh" value="hoc_sinh" checked> Học sinh</label>
                                <label class="form-check-label ms-2"><input type="checkbox" id="check_phu_huynh" value="phu_huynh"> Phụ huynh</label>
                            </div>
                        </div>
                        <div>
                            <label for="lop" class="form-label small">Lớp:</label>
                            <select id="lop" name="lop" class="form-select form-select-sm" style="width: 150px;" required>
                                <option value="">-- Chọn lớp --</option>
                                <?php foreach ($dsLop as $lop): ?>
                                    <option value="<?= htmlspecialchars($lop['tenLop']) ?>"><?= htmlspecialchars($lop['tenLop']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">🔍</span>
                        <input type="text" id="timNguoiNhan" class="form-control" placeholder="Tìm kiếm theo tên, vai trò...">
                    </div>

                    <small id="soKetQua" class="text-muted ms-2">Tìm thấy 0 kết quả</small>
                    <br>
                    <div class="table-responsive flex-grow-1" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-sm table-striped align-middle" id="bangNguoiNhan">
                            <thead class="table-light" style="position: sticky; top: 0;">
                                <tr>
                                    <th style="width:40px"></th>
                                    <th style="width:80px">Mã</th>
                                    <th>Họ tên</th>
                                    <th style="width:120px">Vai trò</th>
                                    <th>Thông tin</th>
                                    <th>Email</th>
                                    <th>SĐT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="7" class="text-center text-muted">Chọn lớp để xem danh sách...</td></tr>
                            </tbody>
                        </table>
                        <button type="button" id="loadMoreBtn" class="btn btn-outline-primary btn-sm ms-2" style="display: none;">Xem thêm</button>

                    </div>
                                
                    <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-3">
                        <div>
                            <button type="button" id="chonTatCa" class="btn btn-outline-secondary btn-sm">Chọn tất cả</button>
                        </div>
                        <div id="soLuongChon" class="text-muted fw-bold">Đã chọn: 0 người</div>
                    </div>

                </div> 
            </div> 
            </div> 
            <div class="col-lg-5">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">SOẠN TIN NHẮN</h5>

                        <h6 class="text-muted small">NHẬP TIN NHẮN</h6>
                        
                        <div class="mb-3">
                            <label for="tieuDe" class="form-label">Tiêu đề:</label>
                            <input type="text" id="tieuDe" name="tieuDe" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="noidung" class="form-label">Nội dung:</label>
                            <textarea name="noidung" id="noidung" class="form-control" rows="8" maxlength="1000" required></textarea>
                            <small id="demKyTu" class="form-text text-muted">0 / 1000 ký tự</small>
                        </div>

                        <div class="mb-3">
                            <label for="dinhkem" class="form-label">Đính kèm file:</label>
                            <input type="file" name="dinhkem" id="dinhkem" class="form-control">
                        </div>

                        <div class="small mt-4">
                            <strong>Lưu ý:</strong>
                            <ul class="mb-0" style="padding-left: 1.2rem;">
                                <li>Tin nhắn tối đa 1000 ký tự.</li>
                                <li>File đính kèm tối đa 10MB.</li>
                                <li>Định dạng hỗ trợ: PDF, DOC, DOCX, JPG, PNG.</li>
                            </ul>
                        </div>

                        <div class="text-end mt-4">
                            <a href="index.php?controller=tinnhan&action=index" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary ms-2">Gửi tin nhắn</button>
                        </div>

                    </div> 
                </div> 
            </div> 
        </div> 
    </form> 
</div> 

<script src="assets/js/tinnhan.js"></script>