document.addEventListener('DOMContentLoaded', function() {
    
    // === DOM ELEMENTS ===
    const btnXemBangDiem = document.getElementById('btnXemBangDiem');
    const cardBangDiem = document.getElementById('cardBangDiem');
    const tableHead = document.getElementById('diemTableHead'); 
    const tableBody = document.getElementById('tbodyDiem');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
    const formLuuDiem = document.getElementById('formLuuDiem'); 
    
    const selMaLop = document.getElementById('maLop');
    const selMaMonHoc = document.getElementById('maMonHoc');
    const selHocKy = document.getElementById('hocKy');
    const selNamHoc = document.getElementById('namHoc');

    const checkMieng = document.getElementById('checkMieng');
    const check15Phut = document.getElementById('check15Phut');
    const check1Tiet = document.getElementById('check1Tiet');
    const checkCuoiKy = document.getElementById('checkCuoiKy');

    // === GLOBAL STATE ===
    let currentData = []; 
    let columnCounts = { 
        'MIENG': 3,
        '15_PHUT': 3,
        '1_TIET': 1,
        'CUOI_KY': 1
    };
    const gradeTypes = {
        'MIENG': 'Điểm Miệng',
        '15_PHUT': 'Điểm 15 Phút',
        '1_TIET': 'Điểm 1 Tiết',
        'CUOI_KY': 'Điểm Cuối Kỳ'
    };

    // --- CÁC HÀM CHECK ---

    function areAllFiltersChecked() {
        return checkMieng.checked && 
               check15Phut.checked && 
               check1Tiet.checked && 
               checkCuoiKy.checked;
    }

    function checkFormValidity() {
        const valid = selMaLop.value && selMaMonHoc.value && selHocKy.value && selNamHoc.value;
        btnXemBangDiem.disabled = !valid; 
    }

    selMaLop?.addEventListener('change', checkFormValidity);
    selMaMonHoc?.addEventListener('change', checkFormValidity);
    selHocKy?.addEventListener('change', checkFormValidity);
    selNamHoc?.addEventListener('change', checkFormValidity);
    
    // Gán sự kiện cho Checkbox (Bộ lọc)
    checkMieng?.addEventListener('change', function() {
        updateCurrentDataFromInputs();
        redrawTable();
    });
    
    check15Phut?.addEventListener('change', function() {
        updateCurrentDataFromInputs();
        redrawTable();
    });
    
    check1Tiet?.addEventListener('change', function() {
        updateCurrentDataFromInputs();
        redrawTable();
    });
    
    checkCuoiKy?.addEventListener('change', function() {
        updateCurrentDataFromInputs();
        redrawTable();
    });

    // --- HÀM CHÍNH: TẢI BẢNG ĐIỂM ---
    async function fetchBangDiem() {
        const maLop = selMaLop.value;
        const maMonHoc = selMaMonHoc.value;
        const hocKy = selHocKy.value;
        const namHoc = selNamHoc.value;
        if (!maLop || !maMonHoc || !hocKy || !namHoc) return;
        if (!maLop || !maMonHoc || !hocKy || !namHoc) {
            alert('Vui lòng chọn đầy đủ Lớp, Môn học, Học kỳ và Năm học.');
            return;
        }

        btnXemBangDiem.disabled = true;
        btnXemBangDiem.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
        
        cardBangDiem.style.display = 'block';
        tableHead.innerHTML = ''; 
        tableBody.innerHTML = `<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>`;
        
        try {
            const response = await fetch(`index.php?controller=diem&action=ajaxGetBangDiem&maLop=${maLop}&maMonHoc=${maMonHoc}&hocKy=${hocKy}&namHoc=${namHoc}`, {
                cache: 'no-store' 
            });
            if (!response.ok) throw new Error('Lỗi máy chủ khi tải điểm!');

            currentData = await response.json(); 
            
            if (currentData.error) throw new Error(currentData.error);

            hiddenInputsContainer.innerHTML = `
                <input type="hidden" name="maLop" value="${maLop}">
                <input type="hidden" name="maMonHoc" value="${maMonHoc}">
                <input type="hidden" name="hocKy" value="${hocKy}">
                <input type="hidden" name="namHoc" value="${namHoc}">
            `;

            if (currentData.length === 0) {
                 tableHead.innerHTML = '';
                 tableBody.innerHTML = `<tr><td colspan="3" class="text-center text-muted">Không tìm thấy học sinh!</td></tr>`;
                 return;
            }

            redrawTable();
            cardBangDiem.classList.add('fade-in');

        } catch (error) {
            console.error('Lỗi khi tải bảng điểm:', error);
            tableBody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${error.message}</td></tr>`;
        } finally {
            btnXemBangDiem.disabled = false;
            btnXemBangDiem.innerHTML = '<i class="fas fa-eye"></i> Xem bảng điểm';
        }
    }
    btnXemBangDiem.addEventListener('click', fetchBangDiem);

    // === HÀM ĐỌC DỮ LIỆU TỪ BẢNG ===
    function updateCurrentDataFromInputs() {
        if (!tableBody || !tableBody.rows || currentData.length === 0) return;

        const filters = {
            'MIENG': checkMieng.checked,
            '15_PHUT': check15Phut.checked,
            '1_TIET': check1Tiet.checked,
            'CUOI_KY': checkCuoiKy.checked
        };

        Array.from(tableBody.rows).forEach((tr, rowIndex) => {
            const hs = currentData[rowIndex];
            if (!hs) return; 

            const maHocSinh = hs.maHocSinh;

            for (const type in gradeTypes) {
                if (filters[type]) { 
                    
                    const inputsForType = tr.querySelectorAll(`input[name="diem[${maHocSinh}][${type}][]"]`);

                    if (inputsForType.length > 0) {
                        hs[type] = []; 
                        
                        inputsForType.forEach(input => {
                            hs[type].push(input.value); 
                        });
                    }
                }
            }
        });
    }

    // === Hàm redrawTable ===
    function redrawTable() {
        drawTableHeader();
        drawTableBody();
    }

    // Vẽ Header động
    function drawTableHeader() {
        tableHead.innerHTML = '';
        const tr = document.createElement('tr');
        
        tr.innerHTML = '<th>STT</th><th>Mã HS</th><th>Họ tên</th>';
        
        const filters = {
            'MIENG': checkMieng.checked,
            '15_PHUT': check15Phut.checked,
            '1_TIET': check1Tiet.checked,
            'CUOI_KY': checkCuoiKy.checked
        };

        for (const type in gradeTypes) {
            if (filters[type]) { 
                const count = columnCounts[type]; 
                for (let i = 1; i <= count; i++) {
                    const th = document.createElement('th');
                    let title = gradeTypes[type];
                    if (count > 1) {
                        title += ` (Lần ${i})`;
                    }
                    th.textContent = title;
                    tr.appendChild(th);
                }
            }
        }
        if (areAllFiltersChecked()) {
            const thTBM = document.createElement('th');
            thTBM.textContent = 'TBM';
            tr.appendChild(thTBM);
        }
        tableHead.appendChild(tr);
    }

    // Vẽ Body động
    function drawTableBody() {
        tableBody.innerHTML = '';
        let stt = 1;

        const filters = {
            'MIENG': checkMieng.checked,
            '15_PHUT': check15Phut.checked,
            '1_TIET': check1Tiet.checked,
            'CUOI_KY': checkCuoiKy.checked
        };

        currentData.forEach(hs => {
        const tr = document.createElement('tr');

        const tdSTT = document.createElement('td');
        tdSTT.textContent = stt++;
        tr.appendChild(tdSTT);

        const tdMaHS = document.createElement('td');
        tdMaHS.textContent = hs.maHocSinh;
        tr.appendChild(tdMaHS);

        const tdHoTen = document.createElement('td');
        tdHoTen.textContent = hs.hoTen;
        tr.appendChild(tdHoTen);

        for (const type in gradeTypes) {
            if (filters[type]) { 
                const count = columnCounts[type];
                const scores = hs[type] || [];
                
                for (let i = 0; i < count; i++) {
                    const td = document.createElement('td');
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.inputMode = 'decimal';
                    input.className = 'form-control';
                    input.name = `diem[${hs.maHocSinh}][${type}][]`;
                    input.value = scores[i] || ''; 

                    input.addEventListener('input', function() {
                            tinhTBMHang(tr);
                        });
                    
                    td.appendChild(input);
                    tr.appendChild(td);
                }
            }
        }
        if (areAllFiltersChecked()) {
                const tdTBM = document.createElement('td');
                tdTBM.className = 'tbm-cell font-weight-bold text-danger';
                tr.appendChild(tdTBM);
                
                tinhTBMHang(tr);
            }
        tableBody.appendChild(tr);
    });
    }

    // --- CÁC HÀM CÒN LẠI ---

    // Tính TBM cho một hàng (tr) và cập nhật ô TBM
    function tinhTBMHang(tr) {
        if (!tr) return;
        if (!areAllFiltersChecked()) {
            return;
        }
        const inputs = tr.querySelectorAll('input[type="text"]');
        const tbmCell = tr.querySelector('.tbm-cell');
        if (!inputs.length || !tbmCell) return;

        let tongDiem = 0;
        let tongHeSo = 0;
        let coDiemMieng = false;
        let coDiem15Phut = false;
        let coDiem1Tiet = false;
        let coDiemCuoiKy = false;

        const heSo = {
            'MIENG': 1,
            '15_PHUT': 1,
            '1_TIET': 2,
            'CUOI_KY': 3
        };

        inputs.forEach(input => {
            const name = input.name;
            const diemSo = parseFloat(input.value.replace(',', '.'));

            if (!isNaN(diemSo) && diemSo >= 0 && diemSo <= 10) {
                let loaiDiem = '';
                if (name.includes('MIENG')) loaiDiem = 'MIENG';
                else if (name.includes('15_PHUT')) loaiDiem = '15_PHUT';
                else if (name.includes('1_TIET')) loaiDiem = '1_TIET';
                else if (name.includes('CUOI_KY')) loaiDiem = 'CUOI_KY';

                if (heSo[loaiDiem]) {
                tongDiem += diemSo * heSo[loaiDiem];
                tongHeSo += heSo[loaiDiem];
                
                if (loaiDiem === 'MIENG') coDiemMieng = true;
                if (loaiDiem === '15_PHUT') coDiem15Phut = true;
                if (loaiDiem === '1_TIET') coDiem1Tiet = true;
                if (loaiDiem === 'CUOI_KY') coDiemCuoiKy = true;
            }
            }
        });

        if (coDiemMieng && coDiem15Phut && coDiem1Tiet && coDiemCuoiKy && tongHeSo > 0) {
        const tbm = tongDiem / tongHeSo;
        tbmCell.textContent = tbm.toFixed(2);
        } else {
            tbmCell.textContent = '';
        }
    }

    // Cảnh báo điểm trống
    formLuuDiem?.addEventListener('submit', function(event) {
        updateCurrentDataFromInputs(); 

        const inputs = tableBody.querySelectorAll('input[type="text"]');

        if (inputs.length === 0) return; 

        let emptyFields = 0;
        inputs.forEach(input => {
            if (input.value.trim() === '') emptyFields++;
        });

        if (emptyFields > 0) {
            event.preventDefault(); 
            const message = `Cảnh báo:\n\nPhát hiện có ${emptyFields} ô điểm bị bỏ trống.\n\n- Nhấn "OK" để tiếp tục lưu.\n- Nhấn "Hủy" để ở lại trang.`;
            
            if (confirm(message)) {
                formLuuDiem.submit();
            }
        }
    });

    // Tự động tải lại bảng
    function autoLoadTable() {
        if (!selMaLop || !selMaMonHoc || !selHocKy || !selNamHoc || !btnXemBangDiem) return;
        const urlParams = new URLSearchParams(window.location.search);
        
        const maLop = urlParams.get('maLop');
        const maMonHoc = urlParams.get('maMonHoc');
        const hocKy = urlParams.get('hocKy');
        const namHoc = urlParams.get('namHoc');
        const autoload = urlParams.get('autoload');

        if (maLop && maMonHoc && hocKy && namHoc && autoload) {
            selMaLop.value = maLop;
            selMaMonHoc.value = maMonHoc;
            selHocKy.value = hocKy;
            selNamHoc.value = namHoc;

            checkFormValidity();
            btnXemBangDiem.click();
            
            window.history.replaceState({}, document.title, "index.php?controller=diem&action=nhapdiem");
        } else {
            checkFormValidity();
        }
    }

    checkFormValidity();
    autoLoadTable(); 
});