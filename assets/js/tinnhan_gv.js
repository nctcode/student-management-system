document.addEventListener('DOMContentLoaded', function() {
    // --- Biến toàn cục ---
    let danhSachGiaoVienDaChon = []; 
    let dataGiaoVien = []; 
    let filteredGiaoVien = []; 
    let currentPageGV = 1; 
    const ROWS_PER_PAGE = 5; 

    // --- DOM Elements ---
    const selectLop = document.getElementById('selectLop');
    const timKiemGVInput = document.getElementById('timKiemGV');
    const tbodyGV = document.getElementById('tbodyGiaoVien');
    const chonTatCaGV = document.getElementById('chonTatCaGV');
    const containerGiaoVienNhan = document.getElementById('danhSachGiaoVienNhan');
    const hiddenInputGiaoVienNhan = document.getElementById('hiddenGiaoVienNhan');
    const soLuongChonGVSpan = document.getElementById('soLuongChonGV');
    const formGuiTinNhanGV = document.getElementById('formGuiTinNhanGV');

    // --- Gán sự kiện ---
    selectLop?.addEventListener('change', loadGiaoVienTheoLop);
    timKiemGVInput?.addEventListener('keyup', handleFilterGV);
    chonTatCaGV?.addEventListener('change', () => chonTatCaGVHandler());
    
    formGuiTinNhanGV?.addEventListener('submit', function(e) {
        if (danhSachGiaoVienDaChon.length === 0) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một giáo viên!');
            return;
        }
        
        hiddenInputGiaoVienNhan.value = danhSachGiaoVienDaChon.map(item => item.maGiaoVien).join(',');
        
        // Hiển thị loading
        const submitBtn = formGuiTinNhanGV.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        submitBtn.disabled = true;
    });

    // --- Hàm xử lý chính ---

    async function loadGiaoVienTheoLop() {
        const maLop = selectLop?.value;
        
        try {
            if (tbodyGV) tbodyGV.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Đang tải...</td></tr>`;

            let url = 'index.php?controller=tinnhan&action=getAllGiaoVien';
            if (maLop && maLop !== '') {
                url = `index.php?controller=tinnhan&action=getGiaoVienByLop&maLop=${maLop}`;
            }

            const response = await fetch(url);
            dataGiaoVien = await response.json();
            
            currentPageGV = 1;
            handleFilterGV();
            
        } catch (error) {
            console.error('Lỗi tải danh sách giáo viên:', error);
            if (tbodyGV) tbodyGV.innerHTML = `<tr><td colspan="4" class="text-danger">Lỗi tải dữ liệu</td></tr>`;
        }
    }

    function handleFilterGV() {
        const searchTerm = timKiemGVInput.value.toLowerCase();
        
        filteredGiaoVien = dataGiaoVien.filter(item => 
            (item.hoTen && item.hoTen.toLowerCase().includes(searchTerm)) || 
            (item.maGiaoVien && item.maGiaoVien.toString().toLowerCase().includes(searchTerm)) ||
            (item.loaiGiaoVien && item.loaiGiaoVien.toLowerCase().includes(searchTerm)) ||
            (item.toChuyenMon && item.toChuyenMon.toLowerCase().includes(searchTerm))
        );

        currentPageGV = 1;
        renderTableGV();
        renderPaginationGV();
        capNhatDanhSachGiaoVienNhan();
    }

    function renderTableGV() {
        if (!tbodyGV) return;
        
        tbodyGV.innerHTML = '';
        
        if (filteredGiaoVien.length === 0) {
            tbodyGV.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Không có dữ liệu</td></tr>`;
            return;
        }

        const start = (currentPageGV - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = filteredGiaoVien.slice(start, end);

        pageData.forEach(item => {
            const tr = document.createElement('tr');
            const maGiaoVien = item.maGiaoVien;
            const ten = item.hoTen;
            const loaiGV = item.loaiGiaoVien || item.toChuyenMon || 'Giáo viên';
            const isChecked = danhSachGiaoVienDaChon.some(gv => gv.maGiaoVien === maGiaoVien);

            // Ô checkbox
            const tdCheck = document.createElement('td');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'chon-giao-vien';
            checkbox.checked = isChecked;
            checkbox.value = maGiaoVien;
            checkbox.addEventListener('change', () => {
                chonGiaoVien(checkbox, ten, maGiaoVien, loaiGV);
            });
            tdCheck.appendChild(checkbox);
            tr.appendChild(tdCheck);

            // Các ô thông tin
            tr.appendChild(createTd(maGiaoVien));
            tr.appendChild(createTd(ten));
            tr.appendChild(createTd(loaiGV));
            
            tbodyGV.appendChild(tr);
        });

        // Cập nhật trạng thái "Chọn tất cả"
        updateChonTatCaGV();
    }
    
    function createTd(text) {
        const td = document.createElement('td');
        td.textContent = text;
        return td;
    }

    function renderPaginationGV() {
        const container = document.getElementById('paginationGV');
        if (!container) return;
        
        container.innerHTML = '';
        const totalPages = Math.ceil(filteredGiaoVien.length / ROWS_PER_PAGE);
        
        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `btn btn-sm ${i === currentPageGV ? 'btn-primary' : 'btn-outline-primary'} page-btn`;
            button.textContent = i;
            button.onclick = () => changePageGV(i);
            container.appendChild(button);
        }
    }

    function changePageGV(page) {
        currentPageGV = page;
        renderTableGV();
        renderPaginationGV();
    }

    function chonGiaoVien(checkbox, ten, maGiaoVien, loaiGV) {
        // Xóa khỏi danh sách nếu đã tồn tại
        danhSachGiaoVienDaChon = danhSachGiaoVienDaChon.filter(item => item.maGiaoVien !== maGiaoVien);
        
        // Thêm vào danh sách nếu được chọn
        if (checkbox.checked) {
            danhSachGiaoVienDaChon.push({ 
                maGiaoVien, 
                ten, 
                loaiGV 
            });
        }
        
        capNhatDanhSachGiaoVienNhan();
        updateChonTatCaGV();
    }

    // Sửa hàm capNhatDanhSachGiaoVienNhan trong tinnhan_gv.js

    function capNhatDanhSachGiaoVienNhan() {
        if (!containerGiaoVienNhan) {
            console.error('Không tìm thấy containerGiaoVienNhan');
            return;
        }
        
        console.log('Cập nhật danh sách giáo viên nhận:', danhSachGiaoVienDaChon);
        
        // Xóa nội dung cũ
        containerGiaoVienNhan.innerHTML = '';
        
        // Cập nhật số lượng
        soLuongChonGVSpan.textContent = danhSachGiaoVienDaChon.length;
        
        // Nếu không có giáo viên nào được chọn
        if (danhSachGiaoVienDaChon.length === 0) {
            const placeholder = document.createElement('small');
            placeholder.className = 'text-muted';
            placeholder.textContent = 'Chọn giáo viên từ danh sách bên trái';
            containerGiaoVienNhan.appendChild(placeholder);
            return;
        }
        
        // Tạo badge cho từng giáo viên đã chọn
        danhSachGiaoVienDaChon.forEach((item, index) => {
            const badge = document.createElement('span');
            badge.className = 'badge-gv-selected';
            badge.id = `badge-gv-${item.maGiaoVien}`;
            
            // Tên giáo viên
            const nameSpan = document.createElement('span');
            nameSpan.textContent = `${item.ten} (${item.loaiGV})`;
            badge.appendChild(nameSpan);
            
            // Nút xóa
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'btn-close-gv';
            closeBtn.innerHTML = '×';
            closeBtn.title = 'Xóa';
            
            // Sự kiện xóa
            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('Xóa giáo viên:', item.maGiaoVien);
                xoaGiaoVien(item.maGiaoVien);
            });
            
            badge.appendChild(closeBtn);
            containerGiaoVienNhan.appendChild(badge);
        });
        
        console.log('Đã cập nhật danh sách hiển thị');
    }

    function xoaGiaoVien(maGiaoVien) {
        // Xóa khỏi danh sách đã chọn
        danhSachGiaoVienDaChon = danhSachGiaoVienDaChon.filter(item => item.maGiaoVien !== maGiaoVien);
        
        // Bỏ check trong bảng
        const checkbox = tbodyGV.querySelector(`input[value="${maGiaoVien}"]`);
        if (checkbox) {
            checkbox.checked = false;
        }
        
        capNhatDanhSachGiaoVienNhan();
        updateChonTatCaGV();
    }

    function chonTatCaGVHandler() {
        const isChecked = chonTatCaGV.checked;
        const checkboxes = tbodyGV.querySelectorAll('.chon-giao-vien');
        
        checkboxes.forEach(checkbox => {
            if (checkbox.checked !== isChecked) {
                checkbox.checked = isChecked;
                
                // Lấy thông tin từ hàng
                const row = checkbox.closest('tr');
                const maGiaoVien = checkbox.value;
                const ten = row.cells[2].textContent;
                const loaiGV = row.cells[3].textContent;
                
                // Gọi hàm chọn giáo viên
                chonGiaoVien(checkbox, ten, maGiaoVien, loaiGV);
            }
        });
    }

    function updateChonTatCaGV() {
        if (!chonTatCaGV || !tbodyGV) return;
        
        const checkboxes = tbodyGV.querySelectorAll('.chon-giao-vien');
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const totalCount = checkboxes.length;
        
        if (checkedCount === 0) {
            chonTatCaGV.checked = false;
            chonTatCaGV.indeterminate = false;
        } else if (checkedCount === totalCount) {
            chonTatCaGV.checked = true;
            chonTatCaGV.indeterminate = false;
        } else {
            chonTatCaGV.checked = false;
            chonTatCaGV.indeterminate = true;
        }
    }

    // --- Các hàm phụ cho form ---
    window.demKyTuGV = function(textarea) {
        const soKyTu = document.getElementById('soKyTuGV');
        if (!soKyTu) return;
        
        const content = textarea.value || '';
        soKyTu.textContent = content.length;
        soKyTu.className = (content.length > 1000) ? 'text-danger' : 'text-muted';
    }

    window.hienThiFileGV = function() {
        const fileInput = document.getElementById('fileDinhKemGV');
        const fileList = document.getElementById('danhSachFileGV');
        if (!fileInput || !fileList) return;
        
        fileList.innerHTML = '';
        for (let i = 0; i < fileInput.files.length; i++) {
            const file = fileInput.files.item(i);
            if (file) {
                const fileSize = (file.size / (1024 * 1024)).toFixed(1);
                
                const fileItem = document.createElement('div');
                fileItem.className = 'd-flex justify-content-between align-items-center border rounded p-2 mb-2 bg-light';
                fileItem.innerHTML = `
                    <div>
                        <i class="fas fa-file mr-2"></i>
                        <strong>${file.name}</strong> (${fileSize}MB)
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" onclick="xoaFileGV(${i})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                fileList.appendChild(fileItem);
            }
        }
    }

    window.xoaFileGV = function(index) {
        const fileInput = document.getElementById('fileDinhKemGV');
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);
        
        files.splice(index, 1); 
        
        files.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        window.hienThiFileGV();
    }

    // --- Khởi tạo ---
    function init() {
        // Tải danh sách giáo viên khi trang được load
        loadGiaoVienTheoLop();
        
        // Khởi tạo TinyMCE nếu có
        if (typeof tinymce !== 'undefined' && tinymce.get('noiDungTinNhanGV')) {
            const editor = tinymce.get('noiDungTinNhanGV');
            editor.on('keyup', function() {
                const content = editor.getContent({ format: 'text' });
                const fakeTextarea = { value: content };
                window.demKyTuGV(fakeTextarea);
            });
            
            editor.on('Change', function() {
                const content = editor.getContent({ format: 'text' });
                const fakeTextarea = { value: content };
                window.demKyTuGV(fakeTextarea);
            });
        }
    }

    // Chạy khởi tạo
    init();
});
