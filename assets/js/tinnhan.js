document.addEventListener('DOMContentLoaded', function() {
    // --- Biến toàn cục ---
    let danhSachDaChon = [];
    let dataHocSinh = [];
    let dataPhuHuynh = [];
    let filteredHocSinh = [];
    let filteredPhuHuynh = [];
    let currentPageHS = 1;
    let currentPagePH = 1;
    const ROWS_PER_PAGE = 5;

    // --- DOM Elements ---
    const selectLop = document.getElementById('selectLop');
    const checkHocSinh = document.getElementById('checkHocSinh');
    const checkPhuHuynh = document.getElementById('checkPhuHuynh');
    const timKiemInput = document.getElementById('timKiem');
    const tbodyHS = document.getElementById('tbodyHocSinh');
    const tbodyPH = document.getElementById('tbodyPhuHuynh');
    const chonTatCaHS = document.getElementById('chonTatCaHS');
    const chonTatCaPH = document.getElementById('chonTatCaPH');
    const containerNguoiNhan = document.getElementById('danhSachNguoiNhan');
    const hiddenInputNguoiNhan = document.getElementById('hiddenNguoiNhan');
    const soLuongChonSpan = document.getElementById('soLuongChon');
    const formGuiTinNhan = document.getElementById('formGuiTinNhan');

    // --- Gán sự kiện ---
    // Đã sửa: Xóa khoảng trắng giữa ? và .
    selectLop?.addEventListener('change', loadData);
    checkHocSinh?.addEventListener('change', toggleViews);
    checkPhuHuynh?.addEventListener('change', toggleViews);
    timKiemInput?.addEventListener('keyup', handleFilter);
    chonTatCaHS?.addEventListener('change', () => chonTatCa('HS'));
    chonTatCaPH?.addEventListener('change', () => chonTatCa('PH'));

    formGuiTinNhan?.addEventListener('submit', function() {
        hiddenInputNguoiNhan.value = danhSachDaChon.map(item => item.maNguoiDung).join(',');

        const loai = [checkHocSinh.checked ? 'HOCSINH' : '', checkPhuHuynh.checked ? 'PHUHUYNH' : '']
            .filter(Boolean).join(',');

        let loaiInput = formGuiTinNhan.querySelector('input[name="loaiNguoiNhan"]');
        if (!loaiInput) {
            loaiInput = document.createElement('input');
            loaiInput.type = 'hidden';
            loaiInput.name = 'loaiNguoiNhan';
            formGuiTinNhan.appendChild(loaiInput);
        }
        loaiInput.value = loai;
    });

    // --- Hàm xử lý chính ---

    async function loadData() {
        const maLop = selectLop.value;
        if (!maLop) {
            clearTables();
            return;
        }

        try {
            if (tbodyHS) tbodyHS.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Đang tải...</td></tr>`;
            if (tbodyPH) tbodyPH.innerHTML = `<tr><td colspan="7" class="text-center text-muted">Đang tải...</td></tr>`;

            const [hsResponse, phResponse] = await Promise.all([
                fetch(`index.php?controller=tinnhan&action=getHocSinhByLop&maLop=${maLop}`),
                fetch(`index.php?controller=tinnhan&action=getPhuHuynhByLop&maLop=${maLop}`)
            ]);

            dataHocSinh = await hsResponse.json();
            dataPhuHuynh = await phResponse.json();

            currentPageHS = 1;
            currentPagePH = 1;

            handleFilter();

        } catch (error) {
            console.error('Lỗi tải danh sách:', error);
            if (tbodyHS) tbodyHS.innerHTML = `<tr><td colspan="4" class="text-danger">Lỗi tải dữ liệu</td></tr>`;
            if (tbodyPH) tbodyPH.innerHTML = `<tr><td colspan="7" class="text-danger">Lỗi tải dữ liệu</td></tr>`;
        }
    }

    function toggleViews() {
        const dsHocSinh = document.getElementById('danhSachHocSinh');
        const dsPhuHuynh = document.getElementById('danhSachPhuHuynh');
        if (dsHocSinh) dsHocSinh.style.display = checkHocSinh.checked ? 'block' : 'none';
        if (dsPhuHuynh) dsPhuHuynh.style.display = checkPhuHuynh.checked ? 'block' : 'none';
        capNhatDanhSachNguoiNhan();
    }

    function handleFilter() {
        const searchTerm = timKiemInput.value.toLowerCase();

        filteredHocSinh = dataHocSinh.filter(item =>
            (item.hoTen && item.hoTen.toLowerCase().includes(searchTerm)) ||
            (item.maHocSinh && item.maHocSinh.toString().toLowerCase().includes(searchTerm))
        );

        filteredPhuHuynh = dataPhuHuynh.filter(item =>
            (item.hoTen && item.hoTen.toLowerCase().includes(searchTerm)) ||
            (item.tenHocSinh && item.tenHocSinh.toLowerCase().includes(searchTerm)) ||
            (item.maPhuHuynh && item.maPhuHuynh.toString().toLowerCase().includes(searchTerm))
        );

        currentPageHS = 1;
        currentPagePH = 1;
        renderAll();
    }

    function renderAll() {
        renderTable('HS', filteredHocSinh, currentPageHS);
        renderTable('PH', filteredPhuHuynh, currentPagePH);
        renderPagination('HS', filteredHocSinh.length, currentPageHS);
        renderPagination('PH', filteredPhuHuynh.length, currentPagePH);
        capNhatDanhSachNguoiNhan();
    }

    function renderTable(loai, data, page) {
        const tbody = (loai === 'HS') ? tbodyHS : tbodyPH;
        if (!tbody) return;

        tbody.innerHTML = '';

        if (data.length === 0) {
            const cols = (loai === 'HS') ? 4 : 7;
            tbody.innerHTML = `<tr><td colspan="${cols}" class="text-center text-muted">Không có dữ liệu</td></tr>`;
            return;
        }

        const start = (page - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = data.slice(start, end);

        pageData.forEach(item => {
            const tr = document.createElement('tr');

            const maNguoiDung = item.maNguoiDung;
            const ten = item.hoTen;
            const isChecked = danhSachDaChon.some(ng => ng.maNguoiDung === maNguoiDung);

            const tdCheck = document.createElement('td');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.checked = isChecked;
            checkbox.addEventListener('change', () => {
                window.chonNguoiNhan(checkbox, ten, maNguoiDung);
            });
            tdCheck.appendChild(checkbox);
            tr.appendChild(tdCheck);

            if (loai === 'HS') {
                tr.appendChild(createTd(item.maHocSinh));
                tr.appendChild(createTd(ten));
                tr.appendChild(createTd(item.tenLop));
            } else {
                tr.appendChild(createTd(item.maPhuHuynh));
                tr.appendChild(createTd(ten));
                tr.appendChild(createTd(item.tenHocSinh));
                tr.appendChild(createTd(item.tenLop));
                tr.appendChild(createTd(item.email || ''));
                tr.appendChild(createTd(item.soDienThoai || ''));
            }

            tbody.appendChild(tr);
        });
    }

    function createTd(text) {
        const td = document.createElement('td');
        td.textContent = text;
        return td;
    }

    function renderPagination(loai, totalItems, currentPage) {
        const container = document.getElementById(`pagination${loai}`);
        if (!container) return;

        container.innerHTML = '';
        const totalPages = Math.ceil(totalItems / ROWS_PER_PAGE);

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'} page-btn`;
            button.textContent = i;
            button.onclick = () => changePage(loai, i);
            container.appendChild(button);
        }
    }

    window.changePage = function(loai, page) {
        if (loai === 'HS') {
            currentPageHS = page;
            renderTable('HS', filteredHocSinh, currentPageHS);
            renderPagination('HS', filteredHocSinh.length, currentPageHS);
        } else {
            currentPagePH = page;
            renderTable('PH', filteredPhuHuynh, currentPagePH);
            renderPagination('PH', filteredPhuHuynh.length, currentPagePH);
        }
    }

    window.chonNguoiNhan = function(checkbox, ten, maNguoiDung) {
        danhSachDaChon = danhSachDaChon.filter(item => item.maNguoiDung !== maNguoiDung);
        if (checkbox.checked) {
            danhSachDaChon.push({ maNguoiDung, ten });
        }
        capNhatDanhSachNguoiNhan();
    }

    function capNhatDanhSachNguoiNhan() {
        if (!containerNguoiNhan) return;

        containerNguoiNhan.innerHTML = '';

        const daChon = danhSachDaChon.length;
        const tongSo = (checkHocSinh.checked ? filteredHocSinh.length : 0) +
            (checkPhuHuynh.checked ? filteredPhuHuynh.length : 0);

        soLuongChonSpan.textContent = `${daChon} / ${tongSo}`;

        danhSachDaChon.forEach(item => {
            const badge = document.createElement('span');
            badge.className = 'badge badge-primary mr-2 mb-2 p-2';

            // Thêm tên
            badge.appendChild(document.createTextNode(item.ten + ' '));

            // Tạo nút 'x'
            const closeButton = document.createElement('span');
            closeButton.innerHTML = '×';
            closeButton.style.cursor = 'pointer';
            closeButton.style.marginLeft = '5px';

            // Gán sự kiện click
            closeButton.addEventListener('click', () => {
                window.xoaNguoiNhan(item.maNguoiDung);
            });

            badge.appendChild(closeButton);
            containerNguoiNhan.appendChild(badge);
        });
    }

    window.xoaNguoiNhan = function(maNguoiDung) {
        danhSachDaChon = danhSachDaChon.filter(item => item.maNguoiDung !== maNguoiDung);
        renderAll(); // Tải lại bảng để bỏ check
    }

    window.chonTatCa = function(loai) {
        const isChecked = (loai === 'HS') ? chonTatCaHS.checked : chonTatCaPH.checked;
        const tbody = (loai === 'HS') ? tbodyHS : tbodyPH;

        tbody.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            if (checkbox.checked !== isChecked) {
                checkbox.checked = isChecked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    }

    // --- Các hàm phụ ---
    window.demKyTu = function(textarea) {
        const soKyTu = document.getElementById('soKyTu');
        if (!soKyTu) return;
        soKyTu.textContent = textarea.value.length;
        soKyTu.className = (textarea.value.length > 1000) ? 'text-danger' : 'text-muted';
    }

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
                fileItem.className = 'd-flex justify-content-between align-items-center border rounded p-2 mb-2';
                fileItem.innerHTML = `
                    <div><strong>${file.name}</strong> (${fileSize}MB)</div>
                    <button type="button" class="btn btn-sm btn-danger" onclick="xoaFile(${i})">×</button>
                `;
                fileList.appendChild(fileItem);
            }
        }
    }

    window.xoaFile = function(index) {
        const fileInput = document.getElementById('fileDinhKem');
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);

        files.splice(index, 1);

        files.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        hienThiFile();
    }

    function clearTables() {
        dataHocSinh = [];
        dataPhuHuynh = [];
        filteredHocSinh = [];
        filteredPhuHuynh = [];
        currentPageHS = 1;
        currentPagePH = 1;
        renderAll();
    }

    if (selectLop) {
        toggleViews();
        loadData();
    }
});