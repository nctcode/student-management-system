/* assets/js/dang_ky_ban.js */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Tự động đóng thông báo sau 5 giây (Dùng chung)
    const alerts = document.querySelectorAll('.js-autoclose-notification');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (typeof bootstrap !== 'undefined') {
                const bootstrapAlert = bootstrap.Alert.getInstance(alert) || new bootstrap.Alert(alert);
                if(bootstrapAlert) bootstrapAlert.close();
            } else {
                alert.style.display = 'none';
            }
        }, 5000);
    });

    // 2. Logic cho trang Đăng Ký (Index)
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

    // 3. Logic cho trang Thành Công (Success)
    const successIcon = document.querySelector('.fa-check-circle');
    if (successIcon) {
        successIcon.style.transform = 'scale(0)';
        setTimeout(() => {
            successIcon.style.transition = 'transform 0.5s ease-out';
            successIcon.style.transform = 'scale(1)';
        }, 100);
    }
});

// --- Các hàm hỗ trợ ---

function validateForm() {
    const selectedBan = document.querySelector('input[name="ma_ban"]:checked');
    // Lấy dữ liệu từ biến toàn cục window.registrationData (được set trong file PHP)
    const currentBanId = window.registrationData ? window.registrationData.id : null;
    
    if (!selectedBan) {
        showValidationError("Vui lòng chọn ban học!");
        return false;
    }
    
    // Nếu chọn cùng ban cũ
    if (currentBanId && parseInt(selectedBan.value) === parseInt(currentBanId)) {
        alert('Bạn đã chọn ban học này rồi!');
        return false;
    }
    
    return confirmRegistration(selectedBan.value);
}

function confirmRegistration(maBan) {
    const banCard = document.querySelector(`input[value="${maBan}"]`).closest('.card');
    const banName = banCard.querySelector('.card-title').textContent.trim();
    
    const currentBanName = window.registrationData ? window.registrationData.name : null;
    
    const message = currentBanName 
        ? `BẠN CÓ CHẮC CHẮN MUỐN ĐỔI TỪ BAN "${currentBanName}" SANG BAN "${banName.toUpperCase()}"?`
        : `BẠN CÓ CHẮC CHẮN MUỐN ĐĂNG KÝ BAN "${banName.toUpperCase()}"?\n\nLưu ý: Sau khi đăng ký, bạn có thể chọn lại ban khác trong thời hạn.`;
    
    return confirm(message);
}

function showValidationError(message) {
    const validationError = document.getElementById('validationError');
    const errorMessage = document.getElementById('errorMessage');
    if (errorMessage && validationError) {
        errorMessage.textContent = message;
        validationError.classList.remove('d-none');
        validationError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function hideValidationError() {
    const el = document.getElementById('validationError');
    if(el) el.classList.add('d-none');
}

function updateSubmitButtonState() {
    const selectedBan = document.querySelector('input[name="ma_ban"]:checked');
    const submitBtn = document.getElementById('submitBtn');
    
    if (submitBtn) {
        if (selectedBan) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
        } else {
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
        }
    }
}

function scrollToForm() {
    const form = document.getElementById('formDangKy');
    if (form) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}