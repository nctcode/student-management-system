document.addEventListener('DOMContentLoaded', function() {
    
    const btnXemBangDiem = document.getElementById('btnXemBangDiem');
    const cardBangDiem = document.getElementById('cardBangDiem');
    const tbodyDiem = document.getElementById('tbodyDiem');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
    const formLuuDiem = document.getElementById('formLuuDiem'); 
    
    const selMaLop = document.getElementById('maLop');
    const selMaMonHoc = document.getElementById('maMonHoc');
    const selHocKy = document.getElementById('hocKy');
    const selNamHoc = document.getElementById('namHoc');

    function checkFormValidity() {
        const valid = selMaLop.value && selMaMonHoc.value && selHocKy.value && selNamHoc.value;
        btnXemBangDiem.disabled = !valid; // Kích hoạt/Vô hiệu hóa nút
    }

    selMaLop?.addEventListener('change', checkFormValidity);
    selMaMonHoc?.addEventListener('change', checkFormValidity);
    selHocKy?.addEventListener('change', checkFormValidity);
    selNamHoc?.addEventListener('change', checkFormValidity);

    async function fetchBangDiem() {
        const maLop = selMaLop.value;
        const maMonHoc = selMaMonHoc.value;
        const hocKy = selHocKy.value;
        const namHoc = selNamHoc.value;

        if (!maLop || !maMonHoc || !hocKy || !namHoc) {
            alert('Vui lòng chọn đầy đủ Lớp, Môn học, Học kỳ và Năm học.');
            return;
        }

        btnXemBangDiem.disabled = true;
        btnXemBangDiem.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
        
        cardBangDiem.style.display = 'block';
        cardBangDiem.classList.remove('fade-in'); 
        tbodyDiem.innerHTML = `<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải bảng điểm...</td></tr>`;
        
        try {
            const response = await fetch(`index.php?controller=diem&action=ajaxGetBangDiem&maLop=${maLop}&maMonHoc=${maMonHoc}&hocKy=${hocKy}&namHoc=${namHoc}`, {
                cache: 'no-store' // Luôn lấy dữ liệu mới
            });
            
            if (!response.ok) {
                throw new Error('Lỗi máy chủ khi tải điểm.');
            }

            const danhSachHocSinh = await response.json();
            
            tbodyDiem.innerHTML = ''; 

            if (danhSachHocSinh.error) {
                throw new Error(danhSachHocSinh.error);
            }

            if (danhSachHocSinh.length === 0) {
                 tbodyDiem.innerHTML = `<tr><td colspan="7" class="text-center text-muted">Không tìm thấy học sinh nào trong lớp này.</td></tr>`;
                 return;
            }

            hiddenInputsContainer.innerHTML = `
                <input type="hidden" name="maLop" value="${maLop}">
                <input type="hidden" name="maMonHoc" value="${maMonHoc}">
                <input type="hidden" name="hocKy" value="${hocKy}">
                <input type="hidden" name="namHoc" value="${namHoc}">
            `;

            let stt = 1;
            danhSachHocSinh.forEach(hs => {
                const tr = document.createElement('tr');
                
                tr.innerHTML = `
                    <td>${stt++}</td>
                    <td>${hs.maHocSinh}</td>
                    <td>${hs.hoTen}</td>
                    <td>
                        <input type="text" inputmode="decimal" 
                               class="form-control"
                               name="diem[${hs.maHocSinh}][MIENG]" 
                               value="${hs.MIENG || ''}">
                    </td>
                    <td>
                        <input type="text" inputmode="decimal" 
                               class="form-control"
                               name="diem[${hs.maHocSinh}][15_PHUT]" 
                               value="${hs['15_PHUT'] || ''}">
                    </td>
                    <td>
                        <input type="text" inputmode="decimal" 
                               class="form-control"
                               name="diem[${hs.maHocSinh}][1_TIET]" 
                               value="${hs['1_TIET'] || ''}">
                    </td>
                    <td>
                        <input type="text" inputmode="decimal" 
                               class="form-control"
                               name="diem[${hs.maHocSinh}][CUOI_KY]" 
                               value="${hs['CUOI_KY'] || ''}">
                    </td>
                `;
                tbodyDiem.appendChild(tr);
            });

            cardBangDiem.classList.add('fade-in');

        } catch (error) {
            console.error('Lỗi khi tải bảng điểm:', error);
            tbodyDiem.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${error.message}</td></tr>`;
        } finally {
            btnXemBangDiem.disabled = false;
            btnXemBangDiem.innerHTML = '<i class="fas fa-eye"></i> Xem bảng điểm';
        }
    }

    // Gán sự kiện click cho nút "Xem"
    btnXemBangDiem.addEventListener('click', fetchBangDiem);

    // Cảnh báo điểm trống
    formLuuDiem?.addEventListener('submit', function(event) {
        
        const inputs = tbodyDiem.querySelectorAll('input[type="text"]');
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

    // === HÀM TỰ ĐỘNG TẢI LẠI BẢNG ===
    function autoLoadTable() {
        if (!selMaLop || !selMaMonHoc || !selHocKy || !selNamHoc || !btnXemBangDiem) return;

        const urlParams = new URLSearchParams(window.location.search);
        
        const maLop = urlParams.get('maLop');
        const maMonHoc = urlParams.get('maMonHoc');
        const hocKy = urlParams.get('hocKy');
        const namHoc = urlParams.get('namHoc');
        const autoload = urlParams.get('autoload');

        if (maLop && maMonHoc && hocKy && namHoc && autoload) {
            // 1. Gán giá trị cho các dropdown
            selMaLop.value = maLop;
            selMaMonHoc.value = maMonHoc;
            selHocKy.value = hocKy;
            selNamHoc.value = namHoc;
            
            // 2. Kích hoạt nút "Xem"
            checkFormValidity();
            
            // 3. Tự động nhấp "Xem"
            btnXemBangDiem.click();
            
            // 4. Xóa param khỏi URL để tránh F5 bị lặp lại
            window.history.replaceState({}, document.title, "index.php?controller=diem&action=index");
        } else {
            // Nếu không autoload, vẫn kiểm tra lần đầu khi tải trang
            checkFormValidity();
        }
    }
    
    // Chạy khi trang tải
    autoLoadTable(); 
});