<?php
$title = "Tạo đơn chuyển lớp/trường";
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.form-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.form-card .card-header {
    background: rgba(255,255,255,0.95);
    border-radius: 20px 20px 0 0 !important;
    border-bottom: 2px solid rgba(102, 126, 234, 0.1);
    padding: 2rem;
}

.form-card .card-body {
    background: rgba(255,255,255,0.98);
    border-radius: 0 0 20px 20px;
    padding: 2rem;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.form-select, .form-control {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.form-select:focus, .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.form-section {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 16px;
    padding: 2rem;
    margin: 1.5rem 0;
    color: white;
}

.form-section h6 {
    color: white;
    font-weight: 700;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-outline-secondary {
    border: 2px solid #cbd5e0;
    border-radius: 12px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    border-color: #667eea;
    color: #667eea;
    transform: translateY(-2px);
}

.alert-custom {
    border: none;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.form-group {
    margin-bottom: 1.5rem;
}

.select-loading {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card form-card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-gradient text-primary mb-1">TẠO ĐƠN CHUYỂN LỚP / TRƯỜNG</h4>
                            <p class="mb-0 text-muted">Tạo đơn chuyển lớp hoặc chuyển trường cho học sinh</p>
                        </div>
                        <a href="index.php?controller=donchuyenloptruong&action=danhsachdoncuatoi" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-3 text-danger"></i>
                                <div class="flex-grow-1">
                                    <strong class="text-danger">Lỗi!</strong> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?controller=donchuyenloptruong&action=store">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="maHocSinh" class="form-label">Chọn học sinh <span class="text-danger">*</span></label>
                                    <select name="maHocSinh" id="maHocSinh" class="form-select shadow-sm" required>
                                        <option value="">-- Chọn học sinh --</option>
                                        <?php foreach ($hocSinhList as $hs): ?>
                                            <option value="<?= $hs['maHocSinh'] ?>">
                                                <?= htmlspecialchars($hs['hoTen']) ?> - <?= htmlspecialchars($hs['tenLop'] ?? 'Chưa có lớp') ?> - <?= htmlspecialchars($hs['tenTruong'] ?? 'Chưa có trường') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="loaiDon" class="form-label">Loại đơn <span class="text-danger">*</span></label>
                                    <select name="loaiDon" id="loaiDon" class="form-select shadow-sm" required onchange="toggleDonType()">
                                        <option value="">-- Chọn loại đơn --</option>
                                        <option value="chuyen_lop">Chuyển lớp</option>
                                        <option value="chuyen_truong">Chuyển trường</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Phần chọn lớp (chỉ hiện khi chọn chuyển lớp) -->
                        <div class="row mt-3" id="chuyenLopSection" style="display: none;">
                            <div class="col-12">
                                <div class="form-section">
                                    <h6 class="mb-3"><i class="fas fa-chalkboard-teacher me-2"></i>Thông tin chuyển lớp</h6>
                                    <div class="form-group">
                                        <label for="maLopDen" class="form-label text-white">Chọn lớp chuyển đến <span class="text-warning">*</span></label>
                                        <select name="maLopDen" id="maLopDen" class="form-select shadow-sm">
                                            <option value="">-- Chọn lớp --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Phần chọn trường (chỉ hiện khi chọn chuyển trường) -->
                        <div class="row mt-3" id="chuyenTruongSection" style="display: none;">
                            <div class="col-12">
                                <div class="form-section" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <h6 class="mb-3"><i class="fas fa-school me-2"></i>Thông tin chuyển trường</h6>
                                    <div class="form-group">
                                        <label for="maTruongDen" class="form-label text-white">Chọn trường chuyển đến <span class="text-warning">*</span></label>
                                        <select name="maTruongDen" id="maTruongDen" class="form-select shadow-sm">
                                            <option value="">-- Chọn trường --</option>
                                            <?php foreach ($truongList as $truong): ?>
                                                <option value="<?= $truong['maTruong'] ?>"><?= htmlspecialchars($truong['tenTruong']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="lyDoChuyen" class="form-label">Lý do chuyển <span class="text-danger">*</span></label>
                                    <textarea name="lyDoChuyen" id="lyDoChuyen" class="form-control shadow-sm" rows="5" 
                                              placeholder="Nhập lý do chuyển lớp/trường chi tiết..." required></textarea>
                                    <div class="form-text text-muted">Vui lòng mô tả rõ lý do chuyển để được xét duyệt nhanh chóng.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-paper-plane me-2"></i> Gửi đơn
                                    </button>
                                    <a href="index.php?controller=donchuyenloptruong&action=danhsachdoncuatoi" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-times me-2"></i> Hủy bỏ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript code remains the same as previous version
function toggleDonType() {
    const loaiDon = document.getElementById('loaiDon').value;
    const chuyenLopSection = document.getElementById('chuyenLopSection');
    const chuyenTruongSection = document.getElementById('chuyenTruongSection');
    
    chuyenLopSection.style.display = 'none';
    chuyenTruongSection.style.display = 'none';
    
    document.getElementById('maLopDen').required = false;
    document.getElementById('maTruongDen').required = false;
    document.getElementById('maLopDen').value = '';
    document.getElementById('maTruongDen').value = '';
    
    if (loaiDon === 'chuyen_lop') {
        chuyenLopSection.style.display = 'block';
        document.getElementById('maLopDen').required = true;
        loadLopHoc();
    } else if (loaiDon === 'chuyen_truong') {
        chuyenTruongSection.style.display = 'block';
        document.getElementById('maTruongDen').required = true;
    }
}

function loadLopHoc() {
    const maHocSinh = document.getElementById('maHocSinh').value;
    
    if (!maHocSinh) {
        showAlert('Vui lòng chọn học sinh trước', 'warning');
        document.getElementById('loaiDon').value = '';
        toggleDonType();
        return;
    }
    
    const select = document.getElementById('maLopDen');
    select.innerHTML = '<option value="">Đang tải danh sách lớp...</option>';
    select.disabled = true;
    select.classList.add('select-loading');
    
    fetch(`index.php?controller=donchuyenloptruong&action=ajaxGetLop&maHocSinh=${maHocSinh}`)
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Server trả về định dạng không phải JSON: ${contentType}`);
            }
            return response.json();
        })
        .then(data => {
            const select = document.getElementById('maLopDen');
            select.disabled = false;
            select.classList.remove('select-loading');
            
            if (data.success && data.lopList && data.lopList.length > 0) {
                select.innerHTML = '<option value="">-- Chọn lớp --</option>';
                
                data.lopList.forEach(lop => {
                    const option = document.createElement('option');
                    option.value = lop.maLop;
                    option.textContent = lop.tenLop;
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = '<option value="">Không có lớp nào</option>';
                showAlert('Không có lớp nào để chọn hoặc dữ liệu không hợp lệ', 'warning');
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            const select = document.getElementById('maLopDen');
            select.innerHTML = '<option value="">Lỗi tải danh sách lớp</option>';
            select.disabled = false;
            select.classList.remove('select-loading');
            showAlert('Lỗi khi tải danh sách lớp: ' + error.message, 'error');
        });
}

function showAlert(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-custom alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} me-3"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.querySelector('.card-body').prepend(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 5000);
}

document.getElementById('maHocSinh').addEventListener('change', function() {
    if (document.getElementById('loaiDon').value === 'chuyen_lop') {
        loadLopHoc();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>