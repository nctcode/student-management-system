<?php
// Danh sách trường THCS (có thể lấy từ database sau)
$truongTHCS = [
    "THCS Trần Phú", "THCS Nguyễn Du", "THCS Lê Quý Đôn",
    "THCS Lương Thế Vinh", "THCS Chu Văn An", "THCS Nguyễn Trãi",
    "THCS Nguyễn Thị Minh Khai", "THCS Colette", "THCS Lê Văn Tám",
    "THCS Nguyễn Gia Thiều", "THCS Bàn Cờ", "THCS Trần Văn Ơn"
];

// Danh sách trường THPT (nguyện vọng)
$truongTHPT = [
    "THPT Chuyên Lê Hồng Phong", "THPT Trần Đại Nghĩa", "THPT Nguyễn Thị Minh Khai",
    "THPT Marie Curie", "THPT Gia Định", "THPT Bùi Thị Xuân",
    "THPT Nguyễn Hữu Huân", "THPT Trưng Vương", "THPT Lê Quý Đôn",
    "THPT Nguyễn Thượng Hiền", "THPT Tenloman", "THPT Phú Nhuận"
];
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-graduate"></i> ĐĂNG KÝ TUYỂN SINH
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success']) && isset($_SESSION['new_maHoSo'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; ?>
                        <div class="mt-2">
                            <a href="index.php?controller=tuyensinh&action=xemhoso&maHoSo=<?php echo $_SESSION['new_maHoSo']; ?>" 
                            class="btn btn-sm btn-outline-success">
                                <i class="fas fa-eye"></i> Xem hồ sơ đã đăng ký
                            </a>
                            <a href="index.php?controller=tuyensinh&action=hosocuatoi" 
                            class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-list"></i> Danh sách hồ sơ của tôi
                            </a>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php 
                    unset($_SESSION['success']);
                    unset($_SESSION['new_maHoSo']);
                    endif; 
                    ?>

                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" class="row g-3" id="formTuyenSinh" enctype="multipart/form-data">
                        <!-- THÔNG TIN CÁ NHÂN HỌC SINH -->
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-user"></i> THÔNG TIN CÁ NHÂN HỌC SINH
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="hoTen" class="form-control" value="<?php echo $_POST['hoTen'] ?? ''; ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                            <select name="gioiTinh" class="form-select" required>
                                <option value="NAM" <?php echo ($_POST['gioiTinh'] ?? 'NAM') === 'NAM' ? 'selected' : ''; ?>>Nam</option>
                                <option value="NU" <?php echo ($_POST['gioiTinh'] ?? '') === 'NU' ? 'selected' : ''; ?>>Nữ</option>
                                <option value="KHAC" <?php echo ($_POST['gioiTinh'] ?? '') === 'KHAC' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Ngày sinh <span class="text-danger">*</span></label>
                            <input type="date" name="ngaySinh" class="form-control" value="<?php echo $_POST['ngaySinh'] ?? ''; ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nơi sinh</label>
                            <input type="text" name="noiSinh" class="form-control" value="<?php echo $_POST['noiSinh'] ?? ''; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Dân tộc</label>
                            <input type="text" name="danToc" class="form-control" value="<?php echo $_POST['danToc'] ?? 'Kinh'; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tôn giáo</label>
                            <input type="text" name="tonGiao" class="form-control" value="<?php echo $_POST['tonGiao'] ?? 'Không'; ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Quốc tịch</label>
                            <select name="quocTich" class="form-select">
                                <option value="Việt Nam" <?php echo ($_POST['quocTich'] ?? 'Việt Nam') === 'Việt Nam' ? 'selected' : ''; ?>>Việt Nam</option>
                                <option value="Khác" <?php echo ($_POST['quocTich'] ?? '') === 'Khác' ? 'selected' : ''; ?>>Khác</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">SĐT học sinh <span class="text-danger">*</span></label>
                            <input type="tel" name="soDienThoaiHocSinh" class="form-control" value="<?php echo $_POST['soDienThoaiHocSinh'] ?? ''; ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $_POST['email'] ?? ''; ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">SĐT phụ huynh <span class="text-danger">*</span></label>
                            <input type="tel" name="soDienThoaiPhuHuynh" class="form-control" value="<?php echo $_POST['soDienThoaiPhuHuynh'] ?? ''; ?>" required>
                        </div>

                        <!-- Địa chỉ thường trú -->
                        <div class="col-12">
                            <label class="form-label">Địa chỉ thường trú <span class="text-danger">*</span></label>
                            
                            <!-- Tỉnh/Thành phố -->
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <label class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                    <select name="tinhThanh" id="tinhThanh" class="form-select select2-address" required>
                                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                    </select>
                                </div>
                                
                                <!-- Quận/Huyện -->
                                <div class="col-md-4">
                                    <label class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                    <select name="quanHuyen" id="quanHuyen" class="form-select select2-address" disabled required>
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                    </select>
                                </div>
                                
                                <!-- Xã/Phường/Thị trấn -->
                                <div class="col-md-4">
                                    <label class="form-label">Xã/Phường/Thị trấn <span class="text-danger">*</span></label>
                                    <select name="xaPhuong" id="xaPhuong" class="form-select select2-address" disabled required>
                                        <option value="">-- Chọn Xã/Phường --</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Địa chỉ chi tiết -->
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label">Địa chỉ chi tiết (Số nhà, tên đường...)</label>
                                    <input type="text" name="diaChiChiTiet" id="diaChiChiTiet" class="form-control" value="<?php echo $_POST['diaChiChiTiet'] ?? ''; ?>">
                                </div>
                            </div>
                            
                            <!-- Input ẩn để lưu địa chỉ đầy đủ -->
                            <input type="hidden" name="diaChiThuongTru" id="diaChiThuongTru" value="<?php echo $_POST['diaChiThuongTru'] ?? ''; ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Nơi ở hiện nay (nếu khác)</label>
                            <input type="text" name="noiOHienNay" class="form-control" value="<?php echo $_POST['noiOHienNay'] ?? ''; ?>">
                        </div>

                        <!-- THÔNG TIN CHA -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-male"></i> THÔNG TIN CHA
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Họ tên cha</label>
                            <input type="text" name="hoTenCha" class="form-control" value="<?php echo $_POST['hoTenCha'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Nghề nghiệp</label>
                            <input type="text" name="ngheNghiepCha" class="form-control" value="<?php echo $_POST['ngheNghiepCha'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">SĐT cha</label>
                            <input type="tel" name="dienThoaiCha" class="form-control" value="<?php echo $_POST['dienThoaiCha'] ?? ''; ?>">
                        </div>

                        <!-- THÔNG TIN MẸ -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-female"></i> THÔNG TIN MẸ
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Họ tên mẹ</label>
                            <input type="text" name="hoTenMe" class="form-control" value="<?php echo $_POST['hoTenMe'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Nghề nghiệp</label>
                            <input type="text" name="ngheNghiepMe" class="form-control" value="<?php echo $_POST['ngheNghiepMe'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">SĐT mẹ</label>
                            <input type="tel" name="dienThoaiMe" class="form-control" value="<?php echo $_POST['dienThoaiMe'] ?? ''; ?>">
                        </div>

                        <!-- THÔNG TIN NGƯỜI GIÁM HỘ (Nếu có) -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-user-shield"></i> THÔNG TIN NGƯỜI GIÁM HỘ (Nếu khác cha/mẹ)
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Họ tên người giám hộ</label>
                            <input type="text" name="hoTenNguoiGiamHo" class="form-control" value="<?php echo $_POST['hoTenNguoiGiamHo'] ?? ''; ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Năm sinh</label>
                            <input type="number" name="namSinhNguoiGiamHo" class="form-control" min="1950" max="2010" value="<?php echo $_POST['namSinhNguoiGiamHo'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Nghề nghiệp</label>
                            <input type="text" name="ngheNghiepNguoiGiamHo" class="form-control" value="<?php echo $_POST['ngheNghiepNguoiGiamHo'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">SĐT người giám hộ</label>
                            <input type="tel" name="dienThoaiNguoiGiamHo" class="form-control" value="<?php echo $_POST['dienThoaiNguoiGiamHo'] ?? ''; ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Nơi công tác</label>
                            <input type="text" name="noiCongTacNguoiGiamHo" class="form-control" value="<?php echo $_POST['noiCongTacNguoiGiamHo'] ?? ''; ?>">
                        </div>

                        <!-- THÔNG TIN HỌC TẬP -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-graduation-cap"></i> THÔNG TIN HỌC TẬP
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Trường THCS <span class="text-danger">*</span></label>
                            <select name="truongTHCS" class="form-select" required>
                                <option value="">-- Chọn trường THCS --</option>
                                <?php foreach ($truongTHCS as $t): ?>
                                    <option value="<?= $t ?>" <?php echo ($_POST['truongTHCS'] ?? '') === $t ? 'selected' : ''; ?>>
                                        <?= $t ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="Khác">Khác (nhập tên trường)</option>
                            </select>
                            <input type="text" name="truongTHCS_khac" id="truongTHCS_khac" class="form-control mt-2 d-none" 
                                   placeholder="Nhập tên trường THCS" value="<?php echo $_POST['truongTHCS_khac'] ?? ''; ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Địa chỉ trường THCS</label>
                            <input type="text" name="diaChiTruongTHCS" class="form-control" value="<?php echo $_POST['diaChiTruongTHCS'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Năm tốt nghiệp</label>
                            <input type="number" name="namTotNghiep" class="form-control" min="2020" max="2025" value="<?php echo $_POST['namTotNghiep'] ?? '2024'; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Xếp loại học lực</label>
                            <select name="xepLoaiHocLuc" class="form-select">
                                <option value="">-- Chọn --</option>
                                <option value="GIOI" <?php echo ($_POST['xepLoaiHocLuc'] ?? '') === 'GIOI' ? 'selected' : ''; ?>>Giỏi</option>
                                <option value="KHA" <?php echo ($_POST['xepLoaiHocLuc'] ?? '') === 'KHA' ? 'selected' : ''; ?>>Khá</option>
                                <option value="TRUNG_BINH" <?php echo ($_POST['xepLoaiHocLuc'] ?? '') === 'TRUNG_BINH' ? 'selected' : ''; ?>>Trung bình</option>
                                <option value="YEU" <?php echo ($_POST['xepLoaiHocLuc'] ?? '') === 'YEU' ? 'selected' : ''; ?>>Yếu</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Xếp loại hạnh kiểm</label>
                            <select name="xepLoaiHanhKiem" class="form-select">
                                <option value="">-- Chọn --</option>
                                <option value="TOT" <?php echo ($_POST['xepLoaiHanhKiem'] ?? '') === 'TOT' ? 'selected' : ''; ?>>Tốt</option>
                                <option value="KHA" <?php echo ($_POST['xepLoaiHanhKiem'] ?? '') === 'KHA' ? 'selected' : ''; ?>>Khá</option>
                                <option value="TRUNG_BINH" <?php echo ($_POST['xepLoaiHanhKiem'] ?? '') === 'TRUNG_BINH' ? 'selected' : ''; ?>>Trung bình</option>
                                <option value="YEU" <?php echo ($_POST['xepLoaiHanhKiem'] ?? '') === 'YEU' ? 'selected' : ''; ?>>Yếu</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Điểm TB lớp 9</label>
                            <input type="number" name="diemTB_Lop9" class="form-control" min="0" max="10" step="0.1" value="<?php echo $_POST['diemTB_Lop9'] ?? ''; ?>">
                        </div>

                        <!-- NGUYỆN VỌNG TUYỂN SINH -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-list-alt"></i> NGUYỆN VỌNG TUYỂN SINH
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 1 <span class="text-danger">*</span></label>
                            <select name="nguyenVong1" class="form-select" required>
                                <option value="">-- Chọn trường --</option>
                                <?php foreach ($truongTHPT as $t): ?>
                                    <option value="<?= $t ?>" <?php echo ($_POST['nguyenVong1'] ?? '') === $t ? 'selected' : ''; ?>>
                                        <?= $t ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 2</label>
                            <select name="nguyenVong2" class="form-select">
                                <option value="">-- Chọn trường --</option>
                                <?php foreach ($truongTHPT as $t): ?>
                                    <option value="<?= $t ?>" <?php echo ($_POST['nguyenVong2'] ?? '') === $t ? 'selected' : ''; ?>>
                                        <?= $t ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 3</label>
                            <select name="nguyenVong3" class="form-select">
                                <option value="">-- Chọn trường --</option>
                                <?php foreach ($truongTHPT as $t): ?>
                                    <option value="<?= $t ?>" <?php echo ($_POST['nguyenVong3'] ?? '') === $t ? 'selected' : ''; ?>>
                                        <?= $t ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- THÔNG TIN HỒ SƠ ĐÍNH KÈM -->
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 text-primary">
                                <i class="fas fa-file-upload"></i> HỒ SƠ ĐÍNH KÈM
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bản sao giấy khai sinh</label>
                            <input type="file" name="banSaoGiayKhaiSinh" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file PDF, JPG, PNG (tối đa 5MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bản sao hộ khẩu</label>
                            <input type="file" name="banSaoHoKhau" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file PDF, JPG, PNG (tối đa 5MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Học bạ THCS</label>
                            <input type="file" name="hocBaTHCS" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file PDF, JPG, PNG (tối đa 5MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Giấy chứng nhận tốt nghiệp</label>
                            <input type="file" name="giayChungNhanTotNghiep" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file PDF, JPG, PNG (tối đa 5MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ảnh 3x4</label>
                            <input type="file" name="anh34" class="form-control" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file JPG, PNG (tối đa 2MB)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Giấy xác nhận ưu tiên (nếu có)</label>
                            <input type="file" name="giayXacNhanUuTien" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Chấp nhận file PDF, JPG, PNG (tối đa 5MB)</small>
                        </div>

                        <!-- THÔNG BÁO QUAN TRỌNG -->
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Thông tin quan trọng:</h6>
                                <ul class="mb-0">
                                    <li>Các trường có dấu <span class="text-danger">*</span> là bắt buộc</li>
                                    <li>Hồ sơ sẽ được xét duyệt trong vòng 3-5 ngày làm việc</li>
                                    <li>Thí sinh cần chuẩn bị đầy đủ hồ sơ gốc khi đến nhập học</li>
                                    <li>Mọi thông tin cần được điền chính xác theo giấy tờ tùy thân</li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane"></i> Gửi đăng ký
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS đã có trong file gốc, giữ nguyên -->

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Xử lý chọn trường THCS
    const truongTHCSSelect = document.querySelector('select[name="truongTHCS"]');
    const truongTHCSKhacInput = document.getElementById('truongTHCS_khac');
    
    if (truongTHCSSelect && truongTHCSKhacInput) {
        truongTHCSSelect.addEventListener('change', function() {
            if (this.value === 'Khác') {
                truongTHCSKhacInput.classList.remove('d-none');
                truongTHCSKhacInput.setAttribute('required', 'required');
            } else {
                truongTHCSKhacInput.classList.add('d-none');
                truongTHCSKhacInput.removeAttribute('required');
                truongTHCSKhacInput.value = '';
            }
        });
        
        // Kiểm tra nếu đã chọn "Khác" từ trước
        if (truongTHCSSelect.value === 'Khác') {
            truongTHCSKhacInput.classList.remove('d-none');
            truongTHCSKhacInput.setAttribute('required', 'required');
        }
    }

    // Gọi API lấy toàn bộ dữ liệu tỉnh/quan/phuong
    fetch("https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json")
        .then(res => res.json())
        .then(data => {
            let tinh = document.getElementById("tinhThanh");
            let quan = document.getElementById("quanHuyen");
            let xa   = document.getElementById("xaPhuong");

            // Load TỈNH / THÀNH PHỐ
            data.forEach(item => {
                tinh.innerHTML += `<option value="${item.Id}">${item.Name}</option>`;
            });

            // Khi chọn TỈNH → load QUẬN
            tinh.addEventListener("change", function () {
                let idTinh = this.value;
                quan.innerHTML = `<option value="">-- Chọn Quận/Huyện --</option>`;
                xa.innerHTML = `<option value="">-- Chọn Xã/Phường --</option>`;
                quan.disabled = true;
                xa.disabled = true;

                if (!idTinh) return;

                let dsQuan = data.find(t => t.Id == idTinh)?.Districts || [];

                dsQuan.forEach(q => {
                    quan.innerHTML += `<option value="${q.Id}">${q.Name}</option>`;
                });

                quan.disabled = false;
            });

            // Khi chọn QUẬN → load XÃ
            quan.addEventListener("change", function () {
                let idTinh  = tinh.value;
                let idQuan  = this.value;

                xa.innerHTML = `<option value="">-- Chọn Xã/Phường --</option>`;
                xa.disabled = true;

                if (!idQuan) return;

                let dsXa = data.find(t => t.Id == idTinh)?.Districts
                    .find(d => d.Id == idQuan)?.Wards || [];

                dsXa.forEach(x => {
                    xa.innerHTML += `<option value="${x.Id}">${x.Name}</option>`;
                });

                xa.disabled = false;
            });
        });

    // Xử lý cập nhật địa chỉ đầy đủ khi submit form
    const form = document.getElementById('formTuyenSinh');
    if (form) {
        form.addEventListener('submit', function(e) {
            updateFullAddress();
            
            // Kiểm tra xem đã có địa chỉ chưa
            if (!document.getElementById('diaChiThuongTru').value) {
                alert('Vui lòng chọn đầy đủ thông tin địa chỉ!');
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    }

    // Hàm cập nhật địa chỉ đầy đủ
    function updateFullAddress() {
        const tinhId = document.getElementById('tinhThanh').value;
        const quanId = document.getElementById('quanHuyen').value;
        const xaId = document.getElementById('xaPhuong').value;
        const chiTiet = document.getElementById('diaChiChiTiet').value;
        
        // Lấy tên từ select (option text)
        const tinhSelect = document.getElementById('tinhThanh');
        const quanSelect = document.getElementById('quanHuyen');
        const xaSelect = document.getElementById('xaPhuong');
        
        const tinhText = tinhSelect.options[tinhSelect.selectedIndex].text;
        const quanText = quanSelect.options[quanSelect.selectedIndex].text;
        const xaText = xaSelect.options[xaSelect.selectedIndex].text;
        
        let diaChiArray = [];
        if (chiTiet) diaChiArray.push(chiTiet);
        if (xaText && xaText !== '-- Chọn Xã/Phường --') diaChiArray.push(xaText);
        if (quanText && quanText !== '-- Chọn Quận/Huyện --') diaChiArray.push(quanText);
        if (tinhText && tinhText !== '-- Chọn Tỉnh/Thành phố --') diaChiArray.push(tinhText);
        
        const fullAddress = diaChiArray.join(', ');
        document.getElementById('diaChiThuongTru').value = fullAddress;
    }
});
</script>
