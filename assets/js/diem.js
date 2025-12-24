document.addEventListener('DOMContentLoaded', function() {
    
    // === DOM ELEMENTS ===
    const btnXemBangDiem = document.getElementById('btnXemBangDiem');
    const cardBangDiem = document.getElementById('cardBangDiem');
    const tableHead = document.getElementById('diemTableHead'); 
    const tableBody = document.getElementById('tbodyDiem');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
    const formLuuDiem = document.getElementById('formLuuDiem'); 
    const btnHuy = document.getElementById('btnHuyNhapDiem');
    
    const selMaLop = document.getElementById('maLop');
    const selMaMonHoc = document.getElementById('maMonHoc');
    const selHocKy = document.getElementById('hocKy');
    const selNamHoc = document.getElementById('namHoc');

    const checkMieng = document.getElementById('checkMieng');
    const check15Phut = document.getElementById('check15Phut');
    const check1Tiet = document.getElementById('check1Tiet');
    const checkCuoiKy = document.getElementById('checkCuoiKy');

    const modalCanhBao = document.getElementById('modalCanhBaoDiemTrong');
    const msgCanhBao = document.getElementById('msgCanhBaoDiemTrong');
    const btnXacNhanLuu = document.getElementById('btnXacNhanLuuDiem');

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

    selMaLop?.addEventListener('change', handleAutoLoad);
    selMaMonHoc?.addEventListener('change', handleAutoLoad);
    selHocKy?.addEventListener('change', handleAutoLoad);
    selNamHoc?.addEventListener('change', handleAutoLoad);
    
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
        if (!tableBody || currentData.length === 0) return;

        Array.from(tableBody.rows).forEach((tr, rowIndex) => {
            const hs = currentData[rowIndex];
            
            if (!hs) return;

            for (const type in gradeTypes) {
                const inputs = tr.querySelectorAll(`input[name="diem[${hs.maHocSinh}][${type}][]"]`);
                if (inputs.length > 0) {
                    hs[type] = Array.from(inputs).map(input => input.value);
                }
            }
        });
    }

    // === Hàm redrawTable ===
    function redrawTable() {
        drawTableHeader();
        drawTableBody();
    }

    const checkboxMapping = {
        'MIENG': 'checkMieng',
        '15_PHUT': 'check15Phut',
        '1_TIET': 'check1Tiet',
        'CUOI_KY': 'checkCuoiKy'
    };

    // Vẽ Header động
    function drawTableHeader() {
        tableHead.innerHTML = '';
        const tr = document.createElement('tr');
        tr.innerHTML = '<th>STT</th><th>Mã HS</th><th>Họ tên</th>';

        for (const type in gradeTypes) {
            const count = columnCounts[type];
            const checkboxId = checkboxMapping[type];
            const checkbox = document.getElementById(checkboxId);
            const isHidden = checkbox ? !checkbox.checked : false;

            for (let i = 1; i <= count; i++) {
                const th = document.createElement('th');
                
                if (isHidden) th.classList.add('column-hidden');
                
                let title = gradeTypes[type];
                th.textContent = count > 1 ? `${title} (Lần ${i})` : title;
                tr.appendChild(th);
            }
        }
        
        const thTBM = document.createElement('th');
        thTBM.textContent = 'TBM';
    
        if (!areAllFiltersChecked()) thTBM.classList.add('column-hidden');
        
        tr.appendChild(thTBM);
        tableHead.appendChild(tr);
    }

    // Vẽ Body động
    function drawTableBody() {
        tableBody.innerHTML = '';
        let stt = 1;

        currentData.forEach(hs => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${stt++}</td><td>${hs.maHocSinh}</td><td>${hs.hoTen}</td>`;

            for (const type in gradeTypes) {
                const count = columnCounts[type];
                const scores = hs[type] || [];
                const checkbox = document.getElementById(checkboxMapping[type]);
                const isVisible = checkbox ? checkbox.checked : true;

                for (let i = 0; i < count; i++) {
                    const td = document.createElement('td');
                    
                    if (!isVisible) td.classList.add('column-hidden');

                    const input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-control text-center';
                    input.name = `diem[${hs.maHocSinh}][${type}][]`;
                    input.value = scores[i] || '';
                    
                    input.addEventListener('input', function() {
                        let val = this.value.trim().replace(',', '.');
                        if (val !== '' && (isNaN(val) || parseFloat(val) < 0 || parseFloat(val) > 10)) {
                            this.classList.add('is-invalid');
                        } else {
                            this.classList.remove('is-invalid');
                        }
                        tinhTBMHang(tr);
                    });
                    td.appendChild(input);
                    tr.appendChild(td);
                }
            }
            
            const tdTBM = document.createElement('td');
            tdTBM.className = 'tbm-cell font-weight-bold text-danger';
            
            if (!areAllFiltersChecked()) tdTBM.classList.add('column-hidden');
            
            tr.appendChild(tdTBM);
            tinhTBMHang(tr);
            tableBody.appendChild(tr);
        });
    }

    // --- CÁC HÀM CÒN LẠI ---

    function handleAutoLoad() {
        checkFormValidity();
        
        if (selMaLop.value && selMaMonHoc.value && selHocKy.value && selNamHoc.value) {
            fetchBangDiem();
        }
    }

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
        const invalidInputs = tableBody.querySelectorAll('.is-invalid');
    
        if (invalidInputs.length > 0) {
            event.preventDefault(); 
            document.getElementById('modalAlertMessage').innerHTML = 
                `Phát hiện <b>${invalidInputs.length}</b> ô điểm không hợp lệ (phải là số từ 0 đến 10).<br>Vui lòng sửa lại trước khi lưu!`;
            
            const modalElement = document.getElementById('modalAlertDiem');
            const modalInstance = new bootstrap.Modal(modalElement);
            modalInstance.show();
            return;
        }

        if (this.dataset.confirmed === "true") return;

        updateCurrentDataFromInputs(); 
        const inputs = tableBody.querySelectorAll('input[type="text"]');

        if (inputs.length === 0) return; 

        let emptyFields = 0;
        inputs.forEach(input => {
            if (input.value.trim() === '') emptyFields++;
        });

        if (emptyFields > 0) {
            event.preventDefault(); 
            msgCanhBao.innerHTML = `Phát hiện có <strong>${emptyFields}</strong> ô điểm bị bỏ trống. Bạn có chắc chắn muốn lưu không?`;
            const modalInstance = new bootstrap.Modal(modalCanhBao);
            modalInstance.show();
        }
    });

    btnHuy?.addEventListener('click', function() {
        const modalElement = document.getElementById('modalConfirmHuyDiem');
        const modalInstance = new bootstrap.Modal(modalElement);
        modalInstance.show();
    });

    document.getElementById('btnXacNhanHuyDiem')?.addEventListener('click', function() {
        window.location.href = 'index.php';
    });

    btnXacNhanLuu?.addEventListener('click', function() {
        formLuuDiem.dataset.confirmed = "true";
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
        formLuuDiem.submit();
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