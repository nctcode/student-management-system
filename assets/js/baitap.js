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
    
    const maxLength = 1000; // Giới hạn ký tự
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

// Hiển thị file đính kèm VÀ lọc file lỗi (cho Giáo viên)
window.hienThiFile = function() {
    const fileInput = document.getElementById('fileDinhKem');
    const fileList = document.getElementById('danhSachFile');
    if (!fileInput || !fileList) return;

    const files = fileInput.files;
    const dt = new DataTransfer(); 
    
    fileList.innerHTML = '';

    for (let i = 0; i < files.length; i++) {
        const file = files.item(i);
        const fileSize = file.size;
        const fileExt = getFileExtension(file.name);

        let fileItem = document.createElement('div');
        fileItem.className = 'd-flex justify-content-between align-items-center border rounded p-2 mb-2';

        let isValid = true;
        let errorMessage = '';

        if (fileSize === 0) {
            isValid = false;
            errorMessage = 'File rỗng, sẽ bị loại bỏ!';
        } 
        else if (fileSize > MAX_FILE_SIZE) {
            isValid = false;
            const sizeMB = (fileSize / (1024 * 1024)).toFixed(1);
            errorMessage = `File quá 20MB (${sizeMB} MB), sẽ bị loại bỏ!`;
        } 
        else if (!ALLOWED_EXTENSIONS.includes(fileExt)) {
            isValid = false;
            errorMessage = `Định dạng .${fileExt} không hỗ trợ, sẽ bị loại bỏ!`;
        }

        if (isValid) {
            fileItem.classList.add('bg-light');
            let fileSizeText = (fileSize / (1024 * 1024)).toFixed(1) + " MB";
            if (fileSize < (1024 * 1024)) { 
                fileSizeText = (fileSize / 1024).toFixed(0) + " KB";
            }
            fileItem.innerHTML = `
                <div><i class="fas fa-file mr-2"></i> <strong>${file.name}</strong> (${fileSizeText})</div>
                <button type="button" class="btn btn-sm btn-danger" onclick="xoaFile(${i})">×</button>
            `;
            dt.items.add(file);
        } 
        else {
            fileItem.classList.add('bg-danger-light'); 
            fileItem.innerHTML = `
                <div><i class="fas fa-exclamation-triangle text-danger mr-2"></i> 
                     <strong>${file.name}</strong></div>
                <small class="text-danger">${errorMessage}</small>
            `;
        }
        fileList.appendChild(fileItem);
    }
    
    fileInput.files = dt.files;
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
                alert('Hạn nộp phải ở trong tương lai.\nVui lòng chọn lại ngày và giờ!');
                hanNopInput.focus();
                return; 
            }
        }
        
        const submitBtn = formGiaoBaiTap.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang giao...';
        submitBtn.disabled = true;
    });
});