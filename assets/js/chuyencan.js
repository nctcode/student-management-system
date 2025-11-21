document.addEventListener('DOMContentLoaded', function() {
    
    // === DOM Elements ===
    const selMaLop = document.getElementById('maLop');
    const selMaTietHoc = document.getElementById('maTietHoc');
    const selNgayDiemDanh = document.getElementById('ngayDiemDanh');
    const btnXem = document.getElementById('btnXemDiemDanh');
    
    const cardDiemDanh = document.getElementById('cardDiemDanh');
    const cardSubTitleDiemDanh = document.getElementById('cardSubTitleDiemDanh');
    const tbodyDiemDanh = document.getElementById('tbodyDiemDanh');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');

    const btnDiemDanhNhanh = document.getElementById('btnDiemDanhNhanh');
    const btnApDungNhom = document.getElementById('btnApDungNhom');
    const btnHuy = document.getElementById('btnHuy');
    const checkAllNhom = document.getElementById('checkAllNhom');

    // === XỬ LÝ DROPDOWN PHỤ THUỘC ===
    
    function checkFormValidity() {
        const valid = selMaLop.value && selMaTietHoc.value && selNgayDiemDanh.value;
        btnXem.disabled = !valid; 
    }

    selMaLop?.addEventListener('change', function() {
        const maLopChon = this.value;
        selMaTietHoc.innerHTML = '<option value="">Vui lòng chọn buổi học</option>';
        
        if (maLopChon && tietHocData[maLopChon]) {
            const tietHocCuaLop = tietHocData[maLopChon].tietHoc;
            
            tietHocCuaLop.forEach(tiet => {
                const optionText = `${tiet.tenMonHoc} (${tiet.ngayHocTrongTuan}, Tiết ${tiet.tietHoc})`;
                const option = new Option(optionText, tiet.maTietHoc);
                selMaTietHoc.add(option);
            });
            selMaTietHoc.disabled = false;
        } else {
            selMaTietHoc.disabled = true;
        }
        checkFormValidity();
    });
    
    selMaTietHoc?.addEventListener('change', checkFormValidity);
    selNgayDiemDanh?.addEventListener('change', checkFormValidity);

    // === XỬ LÝ AJAX TẢI BẢNG ĐIỂM DANH ===
    
    btnXem?.addEventListener('click', async function() {
        const maLop = selMaLop.value;
        const maTietHoc = selMaTietHoc.value;
        const ngayDiemDanh = selNgayDiemDanh.value;

        if (!maLop || !maTietHoc || !ngayDiemDanh) {
            alert('Vui lòng chọn đầy đủ thông tin.');
            return;
        }

        btnXem.disabled = true;
        btnXem.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
        cardDiemDanh.style.display = 'block';
        tbodyDiemDanh.innerHTML = `<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>`;
        cardSubTitleDiemDanh.innerHTML = '';

        try {
            const response = await fetch(`index.php?controller=chuyencan&action=ajaxGetBangDiemDanh&maLop=${maLop}&maTietHoc=${maTietHoc}&ngayDiemDanh=${ngayDiemDanh}`, {
                cache: 'no-store' 
            });
            if (!response.ok) throw new Error('Lỗi mạng khi tải dữ liệu.');

            const result = await response.json();
            if (result.error) throw new Error(result.error);

            const { danhSachHocSinh, thongTinTietHoc } = result;

            const ngayFormatted = new Date(ngayDiemDanh + 'T00:00:00').toLocaleDateString('vi-VN');
            cardSubTitleDiemDanh.innerHTML = `
                Lớp: ${thongTinTietHoc.tenLop} | Môn: ${thongTinTietHoc.tenMonHoc} | Ngày: <strong>${ngayFormatted}</strong>
            `;

            hiddenInputsContainer.innerHTML = `
                <input type="hidden" name="maLop" value="${maLop}">
                <input type="hidden" name="maTietHoc" value="${maTietHoc}">
                <input type="hidden" name="ngayDiemDanh" value="${ngayDiemDanh}">
            `;

            tbodyDiemDanh.innerHTML = ''; 
            if (danhSachHocSinh.length === 0) {
                tbodyDiemDanh.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Không tìm thấy học sinh.</td></tr>`;
                return;
            }

            let stt = 1;
            danhSachHocSinh.forEach(hs => {
                const maHS = hs.maHocSinh;
                const trangThai = hs.trangThai || 'CO_MAT'; 
                const ghiChu = hs.ghiChu || '';
                
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="checkbox" class="hs-checkbox" data-mahs="${maHS}"></td>
                    <td>${stt++}</td>
                    <td>${hs.hoTen}</td>
                    <td>
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-success btn-sm ${trangThai == 'CO_MAT' ? 'active' : ''}">
                                <input type="radio" name="trangthai[${maHS}]" value="CO_MAT" ${trangThai == 'CO_MAT' ? 'checked' : ''}> Có mặt
                            </label>
                            <label class="btn btn-outline-warning btn-sm ${trangThai == 'DI_MUON' ? 'active' : ''}">
                                <input type="radio" name="trangthai[${maHS}]" value="DI_MUON" ${trangThai == 'DI_MUON' ? 'checked' : ''}> Đi muộn
                            </label>
                            <label class="btn btn-outline-info btn-sm ${trangThai == 'VANG_CP' ? 'active' : ''}">
                                <input type="radio" name="trangthai[${maHS}]" value="VANG_CP" ${trangThai == 'VANG_CP' ? 'checked' : ''}> Vắng (CP)
                            </label>
                            <label class="btn btn-outline-danger btn-sm ${trangThai == 'VANG_KP' ? 'active' : ''}">
                                <input type="radio" name="trangthai[${maHS}]" value="VANG_KP" ${trangThai == 'VANG_KP' ? 'checked' : ''}> Vắng (KP)
                            </label>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="ghichu[${maHS}]" class="form-control form-control-sm" value="${ghiChu}">
                    </td>
                `;
                tbodyDiemDanh.appendChild(tr);
            });

        } catch (error) {
            console.error('Lỗi khi tải bảng điểm danh:', error);
            tbodyDiemDanh.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${error.message}</td></tr>`;
        } finally {
            btnXem.disabled = false;
            btnXem.innerHTML = '<i class="fas fa-list-check"></i> Xem danh sách';
        }
    });


    // === LOGIC CÁC NÚT ĐIỂM DANH ===
    
    const tableBody = tbodyDiemDanh; 

    btnDiemDanhNhanh?.addEventListener('click', function() {
        if (!tableBody) return;
        if (confirm('Bạn có chắc muốn đánh dấu "Có mặt" cho tất cả học sinh?')) {
            const radios = tableBody.querySelectorAll('input[type="radio"][value="CO_MAT"]');
            radios.forEach(radio => {
                radio.checked = true;
                radio.closest('.btn-group').querySelectorAll('label').forEach(label => label.classList.remove('active'));
                radio.closest('label').classList.add('active');
            });
        }
    });

    btnApDungNhom?.addEventListener('click', function() {
        if (!tableBody) return;
        const selectedCheckboxes = tableBody.querySelectorAll('.hs-checkbox:checked');
        if (selectedCheckboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một học sinh để áp dụng.');
            return;
        }

        const statusPrompt = prompt('Nhập trạng thái muốn áp dụng cho nhóm:\n1 = Có mặt\n2 = Đi muộn\n3 = Vắng (Có phép)\n4 = Vắng (Không phép)');
        let valueToSet = '';
        
        switch (statusPrompt) {
            case '1': valueToSet = 'CO_MAT'; break;
            case '2': valueToSet = 'DI_MUON'; break;
            case '3': valueToSet = 'VANG_CP'; break;
            case '4': valueToSet = 'VANG_KP'; break;
            default:
                alert('Lựa chọn không hợp lệ.');
                return;
        }

        selectedCheckboxes.forEach(checkbox => {
            const tr = checkbox.closest('tr');
            const radioToSelect = tr.querySelector(`input[type="radio"][value="${valueToSet}"]`);
            if (radioToSelect) {
                radioToSelect.checked = true;
                radioToSelect.closest('.btn-group').querySelectorAll('label').forEach(label => label.classList.remove('active'));
                radioToSelect.closest('label').classList.add('active');
            }
        });
    });

    checkAllNhom?.addEventListener('change', function() {
        if (!tableBody) return;
        tableBody.querySelectorAll('.hs-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    btnHuy?.addEventListener('click', function() {
        if (confirm('Bạn có chắc chắn muốn hủy? Bảng điểm danh sẽ bị đóng.')) {
            cardDiemDanh.style.display = 'none';
            tbodyDiemDanh.innerHTML = '';
            // Reset các ô chọn
            selMaLop.value = '';
            selMaTietHoc.innerHTML = '<option value="">Vui lòng chọn lớp trước</option>';
            selMaTietHoc.disabled = true;
            btnXem.disabled = true;
        }
    });

    // === TỰ ĐỘNG TẢI BẢNG ===
    function autoLoadTable() {
        if (!selMaLop || !selMaTietHoc || !selNgayDiemDanh || !btnXem) return;

        const urlParams = new URLSearchParams(window.location.search);
        
        const maLop = urlParams.get('maLop');
        const maTietHoc = urlParams.get('maTietHoc');
        const ngayDiemDanh = urlParams.get('ngayDiemDanh');
        const autoload = urlParams.get('autoload');

        if (maLop && maTietHoc && ngayDiemDanh && autoload) {
            // 1. Gán giá trị cho các dropdown
            selMaLop.value = maLop;
            
            // 2. Kích hoạt dropdown tiết học (dispatch event 'change')
            selMaLop.dispatchEvent(new Event('change'));
            
            // 3. Gán giá trị cho tiết học VÀ ngày
            // (Phải chờ một chút để dropdown tiết học được nạp xong)
            setTimeout(() => {
                selMaTietHoc.value = maTietHoc;
                selNgayDiemDanh.value = ngayDiemDanh;
                
                // 4. Kích hoạt nút "Xem"
                checkFormValidity();
                
                // 5. Tự động nhấp "Xem"
                btnXem.click();
                
                // 6. Xóa param khỏi URL để tránh F5 bị lặp lại
                window.history.replaceState({}, document.title, "index.php?controller=chuyencan&action=index");
            }, 100); // Chờ 100ms
        }
    }
    
    autoLoadTable(); // Chạy khi trang tải
});