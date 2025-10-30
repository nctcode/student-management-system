<?php
$title = "Đăng ký tuyển sinh";
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
                            <input type="text" name="quocTich" class="form-control" value="<?php echo $_POST['quocTich'] ?? 'Việt Nam'; ?>">
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

                        <div class="col-12">
                            <label class="form-label">Địa chỉ thường trú <span class="text-danger">*</span></label>
                            <input type="text" name="diaChiThuongTru" class="form-control" value="<?php echo $_POST['diaChiThuongTru'] ?? ''; ?>" required>
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

                        <div class="col-md-2">
                            <label class="form-label">Năm sinh</label>
                            <input type="number" name="namSinhCha" class="form-control" min="1950" max="2010" value="<?php echo $_POST['namSinhCha'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Nghề nghiệp</label>
                            <input type="text" name="ngheNghiepCha" class="form-control" value="<?php echo $_POST['ngheNghiepCha'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">SĐT cha</label>
                            <input type="tel" name="dienThoaiCha" class="form-control" value="<?php echo $_POST['dienThoaiCha'] ?? ''; ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Nơi công tác</label>
                            <input type="text" name="noiCongTacCha" class="form-control" value="<?php echo $_POST['noiCongTacCha'] ?? ''; ?>">
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

                        <div class="col-md-2">
                            <label class="form-label">Năm sinh</label>
                            <input type="number" name="namSinhMe" class="form-control" min="1950" max="2010" value="<?php echo $_POST['namSinhMe'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Nghề nghiệp</label>
                            <input type="text" name="ngheNghiepMe" class="form-control" value="<?php echo $_POST['ngheNghiepMe'] ?? ''; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">SĐT mẹ</label>
                            <input type="tel" name="dienThoaiMe" class="form-control" value="<?php echo $_POST['dienThoaiMe'] ?? ''; ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Nơi công tác</label>
                            <input type="text" name="noiCongTacMe" class="form-control" value="<?php echo $_POST['noiCongTacMe'] ?? ''; ?>">
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
                            <label class="form-label">Trường THCS</label>
                            <input type="text" name="truongTHCS" class="form-control" value="<?php echo $_POST['truongTHCS'] ?? ''; ?>">
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
                            <label class="form-label">Ban học</label>
                            <select name="maBan" class="form-select">
                                <option value="">-- Chọn ban học --</option>
                                <?php foreach ($banHoc as $ban): ?>
                                <?php if ($ban['soLuongDaDangKy'] < $ban['chiTieu']): ?>
                                <option value="<?php echo $ban['maBan']; ?>" 
                                        <?php echo ($_POST['maBan'] ?? '') == $ban['maBan'] ? 'selected' : ''; ?>>
                                    <?php echo $ban['tenBan'] . ' (Còn ' . ($ban['chiTieu'] - $ban['soLuongDaDangKy']) . ' chỉ tiêu)'; ?>
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Ngành học</label>
                            <input type="text" name="nganhHoc" class="form-control" value="<?php echo $_POST['nganhHoc'] ?? ''; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Hình thức tuyển sinh</label>
                            <select name="hinhThucTuyenSinh" class="form-select">
                                <option value="XET_TUYEN" <?php echo ($_POST['hinhThucTuyenSinh'] ?? 'XET_TUYEN') === 'XET_TUYEN' ? 'selected' : ''; ?>>Xét tuyển</option>
                                <option value="THI_TUYEN" <?php echo ($_POST['hinhThucTuyenSinh'] ?? '') === 'THI_TUYEN' ? 'selected' : ''; ?>>Thi tuyển</option>
                                <option value="KET_HOP" <?php echo ($_POST['hinhThucTuyenSinh'] ?? '') === 'KET_HOP' ? 'selected' : ''; ?>>Kết hợp</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 1</label>
                            <input type="text" name="nguyenVong1" class="form-control" value="<?php echo $_POST['nguyenVong1'] ?? ''; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 2</label>
                            <input type="text" name="nguyenVong2" class="form-control" value="<?php echo $_POST['nguyenVong2'] ?? ''; ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nguyện vọng 3</label>
                            <input type="text" name="nguyenVong3" class="form-control" value="<?php echo $_POST['nguyenVong3'] ?? ''; ?>">
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