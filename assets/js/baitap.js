// === CÁC BIẾN CẤU HÌNH VALIDATION ===
const MAX_FILE_SIZE = 20 * 1024 * 1024;
const ALLOWED_EXTENSIONS = [
    'pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx', 'xls', 
    'mp4', 'mov', 'avi', 'mp3', 'zip', 'rar', 'txt', 'ppt', 'pptx'
];

// === HÀM CHUNG ===

// Đếm ký tự cho Mô tả
window.demKyTu = function(textarea) {
    const soKyTu = document.getElementById('soKyTu');
    if (!soKyTu) return;
    
    const maxLength = 1000;
    soKyTu.textContent = textarea.value.length;
    
    if (textarea.value.length > maxLength) {
        soKyTu.className = 'text-danger';
    } else {
        soKyTu.className = 'text-muted';
    }
}

// Lấy phần mở rộng (extension) của file
function getFileExtension(filename) {
    return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2).toLowerCase();
}

function showBTAlert(message, iconClass = 'fa-exclamation-triangle') {
    const modalElement = document.getElementById('modalAlertBT');
    const msgElement = document.getElementById('msgAlertBT');
    const iconContainer = modalElement.querySelector('.modal-body i');

    if (modalElement && msgElement) {
        msgElement.innerHTML = message;
        
        if (iconContainer) {
            iconContainer.className = `fas ${iconClass} fa-3x text-danger mb-3`;
        }
        
        const modalInstance = new bootstrap.Modal(modalElement);
        modalInstance.show();
    }
}

window.hienThiFile = function() {
    const fileInput = document.getElementById('fileDinhKem');
    const fileList = document.getElementById('danhSachFile');
    
    if (!fileInput || !fileList) return;

    const dt = new DataTransfer();
    fileList.innerHTML = '';
    let errorList = [];

    Array.from(fileInput.files).forEach(file => {
        const ext = getFileExtension(file.name);
        let msg = "";

        if (file.size === 0) msg = "file bị rỗng";
        else if (file.size > MAX_FILE_SIZE) msg = "vượt quá 20MB";
        else if (!ALLOWED_EXTENSIONS.includes(ext)) msg = `định dạng .${ext} không hỗ trợ`;

        if (msg !== "") {
            errorList.push(`<li>File <b>${file.name}</b> (${msg})</li>`);
        } else {
            dt.items.add(file);
            const fileSizeText = (file.size / (1024 * 1024)).toFixed(1) + " MB";
            const fileItem = document.createElement('div');
            fileItem.className = 'd-flex justify-content-between align-items-center border rounded p-2 mb-2 bg-light';
            fileItem.innerHTML = `
                <div><i class="fas fa-file mr-2 text-primary"></i><strong>${file.name}</strong> (${fileSizeText})</div>
                <button type="button" class="btn btn-sm btn-danger" onclick="xoaFile(${dt.items.length - 1})">×</button>
            `;
            fileList.appendChild(fileItem);
        }
    });

    fileInput.files = dt.files;

    if (errorList.length > 0) {
        const finalMsg = `Phát hiện ${errorList.length} file lỗi đã bị loại bỏ:<ul class='text-left mt-2'>${errorList.join('')}</ul>`;
        showBTAlert(finalMsg, 'fa-file-excel');
    }
}

// Xóa file khỏi danh sách
window.xoaFile = function(index) {
    const fileInput = document.getElementById('fileDinhKem');
    const dt = new DataTransfer();
    const files = Array.from(fileInput.files);
    files.splice(index, 1); 
    files.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
    hienThiFile(); 
}

// === XỬ LÝ FORM ===
document.addEventListener('DOMContentLoaded', function() {
    const formGiaoBaiTap = document.getElementById('formGiaoBaiTap');
    const hanNopInput = document.getElementById('hanNop');
    formGiaoBaiTap?.addEventListener('submit', function(event) {
        
        // Kiểm tra Hạn nộp
        if (hanNopInput.value) {
            const hanNopDate = new Date(hanNopInput.value);
            const now = new Date();
            now.setSeconds(0, 0); 
            
            if (hanNopDate < now) {
                event.preventDefault(); 
                showBTAlert('Hạn nộp phải ở trong tương lai.<br>Vui lòng chọn lại ngày và giờ!', 'fa-calendar-times');
                return;
            }
        }
        
        const submitBtn = formGiaoBaiTap.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang giao...';
        submitBtn.disabled = true;
    });
});