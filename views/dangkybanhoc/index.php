<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/hocsinh.php';

// Helper functions
function renderNotifications() {
    // Xử lý thông báo LỖI
    if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show js-autoclose-notification" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo htmlspecialchars($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; 
    
    // Xử lý thông báo THÀNH CÔNG
    if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show js-autoclose-notification" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif;
    
    // Xử lý thông báo THÔNG TIN
    if (isset($_SESSION['info'])): ?>
    <div class="alert alert-info alert-dismissible fade show js-autoclose-notification" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <?php echo htmlspecialchars($_SESSION['info']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['info']); ?>
    <?php endif;
}

function renderRegistrationInfo($thongTinDangKy, $daDangKy) { ?>
    <div style="
        background-color: #d1ecf1;
        border: 1px solid #bee5eb;
        padding: 1.5rem; 
        border-radius: 0.5rem; 
        margin-bottom: 1.5rem;
    ">
        <h5 class="text-primary" style="font-weight: 700;">
            <i class="fas fa-check-circle me-2"></i>Bạn đã đăng ký ban học
        </h5>
        <div class="row mt-3">
            <div class="col-md-6">
                <p class="mb-1"><strong>Ban đã chọn:</strong> <?php echo htmlspecialchars($thongTinDangKy['tenBan']); ?></p>
                <p class="mb-1"><strong>Ngày đăng ký:</strong> <?php echo date('d/m/Y H:i', strtotime($thongTinDangKy['ngayDangKy'])); ?></p>
                <p class="mb-0"><strong>Trạng thái:</strong> <span class="badge bg-success">Đã đăng ký</span></p>
            </div>
            <div class="col-md-6">
                <p class="mb-1"><strong>Chỉ tiêu:</strong> <?php echo $thongTinDangKy['chiTieu']; ?> học sinh</p>
                <p class="mb-1"><strong>Đã đăng ký:</strong> <?php echo $thongTinDangKy['soLuongDaDangKy']; ?> học sinh</p>
                <p class="mb-0"><strong>Còn lại:</strong> <?php echo $thongTinDangKy['chiTieu'] - $thongTinDangKy['soLuongDaDangKy']; ?> chỉ tiêu</p>
            </div>
        </div>
        
        <div class="mt-4 pt-3 border-top">
            <p class="text-muted small mb-3">
                <i class="fas fa-exclamation-circle me-1"></i>
                Bạn có thể chọn lại ban học khác trước khi hết thời hạn đăng ký.
            </p>
            
            <div class="d-flex gap-2 flex-wrap">
                <a href="index.php?controller=home&action=student" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay về Dashboard
                </a>
                
                <button type="button" class="btn btn-outline-primary" onclick="scrollToForm()">
                    <i class="fas fa-edit me-2"></i>Chọn lại ban khác
                </button>
            </div>
        </div>
    </div>
<?php }

function renderRegistrationForm($danhSachBan, $thongTinDangKy) { 
    $currentBan = $thongTinDangKy['maBan'] ?? null;
    ?>
    <form id="formDangKy" method="POST" action="index.php?controller=dangkybanhoc&action=store">
        <div class="row">
            <?php foreach ($danhSachBan as $ban): 
                $conChiTieu = $ban['chiTieu'] - $ban['soLuongDaDangKy'];
                $phanTram = $ban['chiTieu'] > 0 ? ($ban['soLuongDaDangKy'] / $ban['chiTieu']) * 100 : 100;
                $isAlmostFull = $phanTram > 80;
                $isCritical = $conChiTieu <= 3 && $conChiTieu > 0;
                $isSelected = ($currentBan == $ban['maBan']);
            ?>
            <div class="col-md-6 mb-3">
                <div class="card h-100 <?php echo $isSelected ? 'border-primary border-2' : ''; ?> 
                                      <?php echo $isAlmostFull ? 'border-warning' : ''; ?> 
                                      <?php echo $isCritical ? 'border-danger' : ''; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($ban['tenBan']); ?>
                                <?php if ($isSelected): ?>
                                    <span class="badge bg-primary ms-2">Đã chọn</span>
                                <?php endif; ?>
                            </h5>
                            <div>
                                <?php if ($isCritical): ?>
                                    <span class="badge bg-danger">Sắp hết</span>
                                <?php elseif ($isAlmostFull): ?>
                                    <span class="badge bg-warning">Sắp đầy</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar 
                                <?php echo $isCritical ? 'bg-danger' : ($isAlmostFull ? 'bg-warning' : 'bg-success'); ?>" 
                                style="width: <?php echo min(100, $phanTram); ?>%"
                                title="<?php echo number_format($phanTram, 1); ?>% đã đăng ký">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between text-sm mb-3">
                            <span class="text-muted">
                                <i class="fas fa-users me-1"></i>
                                <?php echo $ban['soLuongDaDangKy']; ?>/<?php echo $ban['chiTieu']; ?>
                            </span>
                            <span class="<?php echo $isCritical ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                <i class="fas fa-ticket-alt me-1"></i>
                                Còn <?php echo $conChiTieu; ?> chỉ tiêu
                            </span>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" 
                                   name="ma_ban" 
                                   value="<?php echo $ban['maBan']; ?>" 
                                   id="ban_<?php echo $ban['maBan']; ?>"
                                   <?php echo $isSelected ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-medium" for="ban_<?php echo $ban['maBan']; ?>">
                                <?php echo $isSelected ? 'Đã chọn ban này' : 'Chọn ban này'; ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div id="validationError" class="alert alert-danger d-none">
            <i class="fas fa-exclamation-circle me-2"></i>
            <span id="errorMessage">Vui lòng chọn ban học!</span>
        </div>
        
        <div class="mt-4">
            <div style="
                background-color: #fff8e1;
                border-left: 5px solid #ffc107;
                padding: 1rem; 
                border-radius: 0.3rem; 
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                margin-bottom: 1.5rem;
            ">
                <h6 class="text-warning mb-2" style="font-weight: 700;">
                    <i class="fas fa-exclamation-triangle me-2"></i>Lưu ý quan trọng:
                </h6>
                <ul class="mb-0 small" style="list-style-type: disc; padding-left: 1.5rem;">
                    <li>Học sinh có thể <strong>chọn lại ban học</strong> trước khi hết thời hạn</li>
                    <li>**Thời hạn đăng ký: 15/12/2025 - 31/12/2025**</li>
                </ul>
            </div>
            
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <a href="index.php?controller=home&action=student" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-paper-plane me-2"></i>
                        <?php echo $thongTinDangKy ? 'Cập nhật đăng ký' : 'Xác nhận đăng ký'; ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
    
    <?php if ($thongTinDangKy): ?>
    <!-- Form ẩn để hủy đăng ký -->
    <form id="formHuy" method="POST" action="index.php?controller=dangkybanhoc&action=huyDangKy" style="display: none;"></form>
    <?php endif; ?>
<?php }
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-graduation-cap me-2"></i>Đăng ký Ban học Lớp 12</h4>
                    <p class="mb-0 small opacity-75">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Thời hạn đăng ký: 15/12/2025 - 31/12/2025
                    </p>
                </div>
                <div class="card-body">
                    <?php renderNotifications(); ?>
                    
                    <?php if ($daDangKy && $thongTinDangKy): ?>
                        <?php renderRegistrationInfo($thongTinDangKy, $daDangKy); ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($danhSachBan)): ?>
                        <?php renderRegistrationForm($danhSachBan, $thongTinDangKy ?? []); ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Hiện tại không có ban học nào còn chỉ tiêu để đăng ký.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tự đóng thông báo sau 5 giây
    const alerts = document.querySelectorAll('.js-autoclose-notification');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bootstrapAlert = typeof bootstrap !== 'undefined' && bootstrap.Alert.getInstance(alert) || new bootstrap.Alert(alert);
            if(bootstrapAlert) {
                bootstrapAlert.close();
            }
        }, 5000);
    });
    
    // Logic Validation Form
    const form = document.getElementById('formDangKy');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
        
        const radioButtons = form.querySelectorAll('input[name="ma_ban"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                hideValidationError();
                updateSubmitButtonState();
            });
        });
        
        updateSubmitButtonState();
    }
});

function validateForm() {
    const selectedBan = document.querySelector('input[name="ma_ban"]:checked');
    const currentBan = <?php echo json_encode($thongTinDangKy['maBan'] ?? null); ?>;
    
    if (!selectedBan) {
        showValidationError("Vui lòng chọn ban học!");
        return false;
    }
    
    // Nếu chọn cùng ban cũ
    if (currentBan && parseInt(selectedBan.value) === parseInt(currentBan)) {
        alert('Bạn đã chọn ban học này rồi!');
        return false;
    }
    
    return confirmRegistration(selectedBan.value);
}

function confirmRegistration(maBan) {
    const banCard = document.querySelector(`input[value="${maBan}"]`).closest('.card');
    const banName = banCard.querySelector('.card-title').textContent.trim();
    
    const currentBan = <?php echo json_encode($thongTinDangKy['tenBan'] ?? null); ?>;
    const message = currentBan 
        ? `BẠN CÓ CHẮC CHẮN MUỐN ĐỔI TỪ BAN "${currentBan}" SANG BAN "${banName.toUpperCase()}"?`
        : `BẠN CÓ CHẮC CHẮN MUỐN ĐĂNG KÝ BAN "${banName.toUpperCase()}"?\n\nLưu ý: Sau khi đăng ký, bạn có thể chọn lại ban khác trong thời hạn.`;
    
    return confirm(message);
}

function showValidationError(message) {
    const validationError = document.getElementById('validationError');
    const errorMessage = document.getElementById('errorMessage');
    
    errorMessage.textContent = message;
    validationError.classList.remove('d-none');
    validationError.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function hideValidationError() {
    document.getElementById('validationError').classList.add('d-none');
}

function updateSubmitButtonState() {
    const selectedBan = document.querySelector('input[name="ma_ban"]:checked');
    const submitBtn = document.getElementById('submitBtn');
    
    if (selectedBan) {
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-primary');
    } else {
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-secondary');
    }
}

function scrollToForm() {
    const form = document.getElementById('formDangKy');
    if (form) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>

<?php 
require_once 'views/layouts/footer.php'; 
?>