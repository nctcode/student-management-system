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

// Hiển thị file đính kèm
window.hienThiFile = function() {
    const fileInput = document.getElementById('fileDinhKem');
    const fileList = document.getElementById('danhSachFile');
    if (!fileInput || !fileList) return;
    
    fileList.innerHTML = '';
    for (let i = 0; i < fileInput.files.length; i++) {
        const file = fileInput.files.item(i);
        if (file) {
            const fileSize = (file.size / (1024 * 1024)).toFixed(1);
            
            const fileItem = document.createElement('div');
            fileItem.className = 'd-flex justify-content-between align-items-center border rounded p-2 mb-2 bg-light';
            fileItem.innerHTML = `
                <div><i class="fas fa-file mr-2"></i> <strong>${file.name}</strong> (${fileSize}MB)</div>
                <button type="button" class="btn btn-sm btn-danger" onclick="xoaFile(${i})">×</button>
            `;
            fileList.appendChild(fileItem);
        }
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

document.addEventListener('DOMContentLoaded', function() {
    const formGiaoBaiTap = document.getElementById('formGiaoBaiTap');
    const hanNopInput = document.getElementById('hanNop');

    formGiaoBaiTap?.addEventListener('submit', function(event) {
        
        // Kiểm tra Hạn nộp
        if (hanNopInput.value) {
            const hanNopDate = new Date(hanNopInput.value);
            const now = new Date();
            
            if (hanNopDate <= now) {
                event.preventDefault();
                alert('Hạn nộp phải ở trong tương lai.\nVui lòng chọn lại ngày và giờ.');
                hanNopInput.focus();
            }
        }

        // Hiển thị loading (nếu hợp lệ)
        const submitBtn = formGiaoBaiTap.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang giao...';
        submitBtn.disabled = true;
    });
});