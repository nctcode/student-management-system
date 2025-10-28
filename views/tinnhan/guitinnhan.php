<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gửi tin nhắn</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Cột trái: Chọn đối tượng nhận tin nhắn -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">CHỌN ĐỐI TƯỢNG NHẬN TIN NHẮN</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="loaiNguoiNhan" id="hocSinh" value="HOCSINH" checked>
                            <label class="form-check-label" for="hocSinh">Học sinh</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="loaiNguoiNhan" id="phuHuynh" value="PHUHUYNH">
                            <label class="form-check-label" for="phuHuynh">Phụ huynh</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><strong>Lớp:</strong></label>
                        <select class="form-control" id="selectLop" onchange="loadDanhSach()">
                            <option value="">-- Chọn lớp --</option>
                            <?php foreach ($danhSachLop as $lop): ?>
                                <option value="<?= $lop['maLop'] ?>"><?= $lop['tenLop'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" id="timKiem" placeholder="Tìm kiếm..." onkeyup="timKiemDanhSach()">
                    </div>

                    <!-- Danh sách học sinh -->
                    <div id="danhSachHocSinh" class="danh-sach-container">
                        <h6 class="font-weight-bold">DANH SÁCH HỌC SINH</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="tableHocSinh">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="chonTatCaHS" onchange="chonTatCa('HS')"></th>
                                        <th>Mã HS</th>
                                        <th>Học sinh</th>
                                        <th>Lớp</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyHocSinh">
                                    <!-- Danh sách học sinh sẽ được load bằng AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Danh sách phụ huynh -->
                    <div id="danhSachPhuHuynh" class="danh-sach-container" style="display: none;">
                        <h6 class="font-weight-bold">DANH SÁCH PHỤ HUYNH</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="tablePhuHuynh">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="chonTatCaPH" onchange="chonTatCa('PH')"></th>
                                        <th>Mã PH</th>
                                        <th>Phụ huynh</th>
                                        <th>Học sinh</th>
                                        <th>Lớp</th>
                                        <th>Email</th>
                                        <th>SĐT</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyPhuHuynh">
                                    <!-- Danh sách phụ huynh sẽ được load bằng AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <strong>Đã chọn: <span id="soLuongChon">0</span></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Form gửi tin nhắn -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">GỬI TIN NHẮN</h6>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="formGuiTinNhan">
                        <div class="form-group">
                            <label><strong>Người nhận</strong></label>
                            <div class="border rounded p-2 bg-light" id="danhSachNguoiNhan" style="min-height: 40px;">
                                <!-- Danh sách người nhận sẽ hiển thị ở đây -->
                            </div>
                            <input type="hidden" name="nguoiNhan[]" id="hiddenNguoiNhan">
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
                                <!-- Danh sách file sẽ hiển thị ở đây -->
                            </div>
                            <input type="file" name="fileDinhKem" id="fileDinhKem" class="form-control-file" 
                                   onchange="hienThiFile()" multiple>
                            <small class="form-text text-muted">
                                • File đính kèm tối đa 10MB<br>
                                • Định dạng hỗ trợ: PDF, DOC, JPG, PNG, XLSX<br>
                                • Không gửi nội dung không phù hợp
                            </small>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="index.php?controller=tinnhan&action=index" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let danhSachDaChon = [];

// Chuyển đổi giữa học sinh và phụ huynh
document.querySelectorAll('input[name="loaiNguoiNhan"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === 'HOCSINH') {
            document.getElementById('danhSachHocSinh').style.display = 'block';
            document.getElementById('danhSachPhuHuynh').style.display = 'none';
        } else {
            document.getElementById('danhSachHocSinh').style.display = 'none';
            document.getElementById('danhSachPhuHuynh').style.display = 'block';
        }
        danhSachDaChon = [];
        capNhatDanhSachNguoiNhan();
        loadDanhSach();
    });
});

// Load danh sách theo lớp
function loadDanhSach() {
    const maLop = document.getElementById('selectLop').value;
    const loaiNguoiNhan = document.querySelector('input[name="loaiNguoiNhan"]:checked').value;
    
    if (!maLop) return;

    if (loaiNguoiNhan === 'HOCSINH') {
        loadHocSinh(maLop);
    } else {
        loadPhuHuynh(maLop);
    }
}

// Load danh sách học sinh
function loadHocSinh(maLop) {
    fetch(`index.php?controller=tinnhan&action=getHocSinhByLop&maLop=${maLop}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tbodyHocSinh');
            tbody.innerHTML = '';
            
            data.forEach(hs => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="checkbox" value="${hs.maNguoiDung}" onchange="chonNguoiNhan(this, '${hs.hoTen}', '${hs.maNguoiDung}')"></td>
                    <td>${hs.maHocSinh}</td>
                    <td>${hs.hoTen}</td>
                    <td>${hs.tenLop}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Error loading students:', error);
            document.getElementById('tbodyHocSinh').innerHTML = '<tr><td colspan="4" class="text-center text-muted">Lỗi tải dữ liệu</td></tr>';
        });
}

// Load danh sách phụ huynh
function loadPhuHuynh(maLop) {
    fetch(`index.php?controller=tinnhan&action=getPhuHuynhByLop&maLop=${maLop}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tbodyPhuHuynh');
            tbody.innerHTML = '';
            
            data.forEach(ph => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="checkbox" value="${ph.maNguoiDung}" onchange="chonNguoiNhan(this, '${ph.hoTen}', '${ph.maNguoiDung}')"></td>
                    <td>${ph.maPhuHuynh}</td>
                    <td>${ph.hoTen}</td>
                    <td>${ph.tenHocSinh}</td>
                    <td>${ph.tenLop}</td>
                    <td>${ph.email}</td>
                    <td>${ph.soDienThoai}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Error loading parents:', error);
            document.getElementById('tbodyPhuHuynh').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Lỗi tải dữ liệu</td></tr>';
        });
}

// Chọn người nhận
function chonNguoiNhan(checkbox, ten, maNguoiDung) {
    if (checkbox.checked) {
        danhSachDaChon.push({ maNguoiDung, ten });
    } else {
        danhSachDaChon = danhSachDaChon.filter(item => item.maNguoiDung !== maNguoiDung);
    }
    capNhatDanhSachNguoiNhan();
}

// Cập nhật danh sách người nhận hiển thị
function capNhatDanhSachNguoiNhan() {
    const container = document.getElementById('danhSachNguoiNhan');
    const hiddenInput = document.getElementById('hiddenNguoiNhan');
    const soLuongChon = document.getElementById('soLuongChon');
    
    container.innerHTML = '';
    hiddenInput.value = danhSachDaChon.map(item => item.maNguoiDung).join(',');
    soLuongChon.textContent = danhSachDaChon.length;
    
    danhSachDaChon.forEach(item => {
        const badge = document.createElement('span');
        badge.className = 'badge badge-primary mr-2 mb-2 p-2';
        badge.innerHTML = `${item.ten} <span onclick="xoaNguoiNhan('${item.maNguoiDung}')" style="cursor: pointer; margin-left: 5px;">×</span>`;
        container.appendChild(badge);
    });
}

// Xóa người nhận
function xoaNguoiNhan(maNguoiDung) {
    danhSachDaChon = danhSachDaChon.filter(item => item.maNguoiDung !== maNguoiDung);
    capNhatDanhSachNguoiNhan();
    
    // Bỏ chọn checkbox tương ứng
    document.querySelectorAll(`input[value="${maNguoiDung}"]`).forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Chọn tất cả
function chonTatCa(loai) {
    const checkboxes = document.querySelectorAll(`#table${loai === 'HS' ? 'HocSinh' : 'PhuHuynh'} input[type="checkbox"]:not(#chonTatCa${loai})`);
    const isChecked = document.getElementById(`chonTatCa${loai}`).checked;
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
        if (isChecked) {
            const row = checkbox.closest('tr');
            const cells = row.getElementsByTagName('td');
            const ten = cells[2].textContent;
            const maNguoiDung = checkbox.value;
            
            if (!danhSachDaChon.find(item => item.maNguoiDung === maNguoiDung)) {
                danhSachDaChon.push({ maNguoiDung, ten });
            }
        } else {
            danhSachDaChon = danhSachDaChon.filter(item => item.maNguoiDung !== checkbox.value);
        }
    });
    
    capNhatDanhSachNguoiNhan();
}

// Đếm ký tự
function demKyTu(textarea) {
    const soKyTu = document.getElementById('soKyTu');
    soKyTu.textContent = textarea.value.length;
    
    if (textarea.value.length > 1000) {
        soKyTu.className = 'text-danger';
    } else {
        soKyTu.className = 'text-muted';
    }
}

// Hiển thị file đính kèm
function hienThiFile() {
    const fileInput = document.getElementById('fileDinhKem');
    const fileList = document.getElementById('danhSachFile');
    fileList.innerHTML = '';
    
    for (let i = 0; i < fileInput.files.length; i++) {
        const file = fileInput.files[i];
        const fileSize = (file.size / (1024 * 1024)).toFixed(1);
        
        const fileItem = document.createElement('div');
        fileItem.className = 'd-flex justify-content-between align-items-center border rounded p-2 mb-2';
        fileItem.innerHTML = `
            <div>
                <strong>${file.name}</strong> (${fileSize}MB)
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="xoaFile(${i})">×</button>
        `;
        fileList.appendChild(fileItem);
    }
}

// Xóa file
function xoaFile(index) {
    const fileInput = document.getElementById('fileDinhKem');
    const files = Array.from(fileInput.files);
    files.splice(index, 1);
    
    // Tạo new FileList (không thể trực tiếp, nên tạo DataTransfer)
    const dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
    
    hienThiFile();
}

// Tìm kiếm
function timKiemDanhSach() {
    const searchTerm = document.getElementById('timKiem').value.toLowerCase();
    const loaiNguoiNhan = document.querySelector('input[name="loaiNguoiNhan"]:checked').value;
    const tableId = loaiNguoiNhan === 'HOCSINH' ? 'tableHocSinh' : 'tablePhuHuynh';
    
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Khởi tạo
document.addEventListener('DOMContentLoaded', function() {
    loadDanhSach();
});
</script>

<style>
.danh-sach-container {
    max-height: 400px;
    overflow-y: auto;
}

.table th {
    position: sticky;
    top: 0;
    background: white;
    z-index: 10;
}
</style>