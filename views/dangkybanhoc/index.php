<?php
require_once 'views/layouts/header.php';

// Helper functions for this view only
function renderNotifications() {
    // --- Xử lý thông báo LỖI ---
    if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show js-autoclose-notification" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo htmlspecialchars($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; 
    
    // --- Xử lý thông báo THÀNH CÔNG (của Controller) ---
    if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show js-autoclose-notification" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif;
}

function renderRegistrationInfo($thongTinDangKy) { ?>
    <div style="
        background-color: #d1ecf1; /* Nền xanh nhạt alert-info */
        border: 1px solid #bee5eb; /* Viền xanh nhạt alert-info */
        padding: 1rem; 
        border-radius: 0.25rem; 
        margin-bottom: 1.5rem;
    ">
        <h5 class="text-info" style="font-weight: 700;"><i class="fas fa-check-circle me-2"></i>Bạn đã đăng ký ban học thành công!</h5>
        <p class="mb-1"><strong>Ban đã chọn:</strong> <?php echo htmlspecialchars($thongTinDangKy['tenBan']); ?></p>
        <p class="mb-0"><strong>Ngày đăng ký:</strong> <?php echo date('d/m/Y H:i', strtotime($thongTinDangKy['ngayDangKy'])); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?controller=home&action=student" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>Quay về Dashboard
        </a>
    </div>
<?php }

function renderRegistrationForm($danhSachBan) { ?>
    <form id="formDangKy" method="POST" action="index.php?controller=dangkybanhoc&action=store">
        <div class="row">
            <?php foreach ($danhSachBan as $ban): 
                $conChiTieu = $ban['chiTieu'] - $ban['soLuongDaDangKy'];
                // Tránh lỗi chia cho 0 nếu chỉ tiêu bằng 0
                $phanTram = $ban['chiTieu'] > 0 ? ($ban['soLuongDaDangKy'] / $ban['chiTieu']) * 100 : 100;
                $isAlmostFull = $phanTram > 80;
                $isCritical = $conChiTieu <= 3 && $conChiTieu > 0;
            ?>
            <div class="col-md-6 mb-3">
                <div class="card h-100 <?php echo $isAlmostFull ? 'border-warning' : ''; ?> <?php echo $isCritical ? 'border-danger' : ''; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title"><?php echo htmlspecialchars($ban['tenBan']); ?></h5>
                            <div>
                                <?php if ($isCritical): ?>
                                    <span class="badge bg-danger">Sắp hết</span>
                                <?php elseif ($isAlmostFull): ?>
                                    <span class="badge bg-warning">Sắp đầy</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="progress mb-2" style="height: 8px;">
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
                                        name="ma_ban" value="<?php echo $ban['maBan']; ?>" 
                                        id="ban_<?php echo $ban['maBan']; ?>">
                            <label class="form-check-label fw-medium" for="ban_<?php echo $ban['maBan']; ?>">
                                Chọn ban này
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
                background-color: #fff8e1; /* Màu nền nhẹ hơn */
                border-left: 5px solid #ffc107; /* Đường viền dọc màu vàng đậm */
                padding: 1rem; 
                border-radius: 0.3rem; 
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                margin-bottom: 1.5rem;
            ">
                <h6 class="text-warning mb-2" style="font-weight: 700;">
                    <i class="fas fa-exclamation-triangle me-2"></i>Lưu ý quan trọng:
                </h6>
                <ul class="mb-0 small" style="list-style-type: disc; padding-left: 1.5rem;">
                    <li>Mỗi học sinh chỉ được đăng ký **một ban học duy nhất**</li>
                    <li>Sau khi đăng ký, bạn **không thể thay đổi** ban học</li>
                    <li>**Thời hạn đăng ký: 04/11/2025 - 18/11/2025**</li>
                    <li>Vui lòng cân nhắc kỹ trước khi đăng ký</li>
                </ul>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="index.php?controller=home&action=student" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-paper-plane me-2"></i>Xác nhận đăng ký
                </button>
            </div>
        </div>
    </form>
<?php }
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-graduation-cap me-2"></i>Đăng ký Ban học Lớp 12</h4>
                </div>
                <div class="card-body">
                    <?php renderNotifications(); ?>
                    
                    <?php if ($daDangKy && $thongTinDangKy): ?>
                        <?php renderRegistrationInfo($thongTinDangKy); ?>
                    <?php else: ?>
                        <?php if (!empty($danhSachBan)): ?>
                            <?php renderRegistrationForm($danhSachBan); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // TỰ ĐÓNG THÔNG BÁO LỖI/THÀNH CÔNG SAU 5 GIÂY (5000ms)
    const alerts = document.querySelectorAll('.js-autoclose-notification');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bootstrapAlert = typeof bootstrap !== 'undefined' && bootstrap.Alert.getInstance(alert) || new bootstrap.Alert(alert);
            if(bootstrapAlert) {
                bootstrapAlert.close();
            }
        }, 5000); // 5 giây
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
    }
});

function validateForm() {
    const selectedBan = document.querySelector('input[name="ma_ban"]:checked');
    
    if (!selectedBan) {
        showValidationError("Vui lòng chọn ban học!");
        return false;
    }
    
    return confirmRegistration(selectedBan.value);
}

function confirmRegistration(maBan) {
    const banCard = document.querySelector(`input[value="${maBan}"]`).closest('.card');
    const banName = banCard.querySelector('.card-title').textContent.trim();
    
    return confirm(`BẠN CÓ CHẮC CHẮN MUỐN ĐĂNG KÝ BAN "${banName.toUpperCase()}"?\n\nLưu ý: Sau khi đăng ký, bạn KHÔNG THỂ thay đổi ban học.`);
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
</script>

<?php 
require_once 'views/layouts/footer.php'; 
?>