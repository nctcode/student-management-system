document.addEventListener('DOMContentLoaded', function() {
    
    // === DOM Elements ===
    const selMaLop = document.getElementById('maLop');
    const selMaBuoiHoc = document.getElementById('maBuoiHoc');
    const btnXem = document.getElementById('btnXemDiemDanh');
    const selNgayDiemDanh = document.getElementById('ngayDiemDanh');
    const cardDiemDanh = document.getElementById('cardDiemDanh');
    const cardSubTitleDiemDanh = document.getElementById('cardSubTitleDiemDanh');
    const tbodyDiemDanh = document.getElementById('tbodyDiemDanh');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');

    const btnDiemDanhNhanh = document.getElementById('btnDiemDanhNhanh');
    const btnApDungNhom = document.getElementById('btnApDungNhom');
    const btnHuy = document.getElementById('btnHuy');
    const checkAllNhom = document.getElementById('checkAllNhom');

    // === XỬ LÝ DROPDOWN PHỤ THUỘC ===
    
    // === XỬ LÝ LỌC BUỔI HỌC THEO NGÀY (GỢI Ý) ===

    function checkFormValidity() {
        const valid = selMaLop.value && selMaBuoiHoc.value;
        // KHÔNG kiểm tra ngày trong validation
        btnXem.disabled = !valid; 
        console.log("🔍 Validation - Lớp:", !!selMaLop.value, "Buổi:", !!selMaBuoiHoc.value);
    }

    // Khi chọn lớp - HIỂN THỊ TẤT CẢ BUỔI HỌC
    selMaLop?.addEventListener('change', function() {
        const maLopChon = this.value;
        console.log("🎯 Chọn lớp:", maLopChon);
        
        // Reset dropdown buổi học
        selMaBuoiHoc.innerHTML = '<option value="">Chọn buổi học</option>';
        
        if (maLopChon && buoiHocData[maLopChon]) {
            const buoiHocCuaLop = buoiHocData[maLopChon].buoiHoc;
            console.log("📚 Tất cả buổi học:", buoiHocCuaLop);
            
            // THÊM TẤT CẢ buổi học vào dropdown
            buoiHocCuaLop.forEach(buoi => {
                const ngayFormatted = new Date(buoi.ngayHoc + 'T00:00:00').toLocaleDateString('vi-VN');
                const optionText = `${buoi.tenMonHoc} (${ngayFormatted}, Tiết ${buoi.tietBatDau}-${buoi.tietKetThuc})`;
                const option = new Option(optionText, buoi.maBuoiHoc);
                selMaBuoiHoc.add(option);
            });
            selMaBuoiHoc.disabled = false;
            console.log(`✅ Đã thêm ${buoiHocCuaLop.length} buổi học`);
            
            // GỢI Ý: Tự động chọn buổi học đầu tiên của ngày đã chọn (nếu có)
            tuDongChonBuoiHocTheoNgay(maLopChon);
        } else {
            console.log("⚠️ Không có buổi học cho lớp này");
            selMaBuoiHoc.disabled = true;
        }
        
        checkFormValidity();
    });

    // Khi thay đổi ngày - CHỈ GỢI ý chọn buổi học
    selNgayDiemDanh?.addEventListener('change', function() {
        const ngayChon = this.value;
        const maLopChon = selMaLop.value;
        
        console.log("📅 Chọn ngày:", ngayChon);
        
        if (maLopChon && ngayChon) {
            // GỢI Ý: Tự động chọn buổi học của ngày này (nếu có)
            tuDongChonBuoiHocTheoNgay(maLopChon);
        }
    });

    // Hàm tự động chọn buổi học theo ngày (GỢI Ý)
    function tuDongChonBuoiHocTheoNgay(maLop) {
        const ngayChon = selNgayDiemDanh.value;
        
        if (!ngayChon || !buoiHocData[maLop]) return;
        
        const buoiHocCuaLop = buoiHocData[maLop].buoiHoc;
        const buoiHocTheoNgay = buoiHocCuaLop.filter(buoi => buoi.ngayHoc === ngayChon);
        
        console.log("🎯 Gợi ý buổi học ngày", ngayChon, ":", buoiHocTheoNgay);
        
        if (buoiHocTheoNgay.length > 0) {
            // Tự động chọn buổi học đầu tiên của ngày này
            selMaBuoiHoc.value = buoiHocTheoNgay[0].maBuoiHoc;
            console.log(`✅ Đã gợi ý chọn buổi học: ${selMaBuoiHoc.value}`);
            
            // Hiển thị thông báo gợi ý
            const thongBao = document.createElement('div');
            thongBao.className = 'alert alert-info alert-dismissible fade show mt-2';
            thongBao.innerHTML = `
                <i class="fas fa-info-circle"></i> 
                Đã tự động chọn buổi học <strong>${buoiHocTheoNgay[0].tenMonHoc}</strong> 
                cho ngày <strong>${formatNgayVietNam(ngayChon)}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Thêm thông báo vào trước nút Xem
            const cardBody = document.querySelector('.card-body');
            const existingAlert = cardBody.querySelector('.alert');
            if (existingAlert) existingAlert.remove();
            
            cardBody.insertBefore(thongBao, cardBody.querySelector('.mt-3'));
            
        } else {
            console.log("ℹ️ Không có buổi học nào vào ngày này, nhưng vẫn hiển thị tất cả buổi học");
            
            // Thông báo không có lịch ngày này (nhưng vẫn cho chọn buổi học khác)
            const thongBao = document.createElement('div');
            thongBao.className = 'alert alert-warning alert-dismissible fade show mt-2';
            thongBao.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i> 
                Không có lịch dạy vào ngày <strong>${formatNgayVietNam(ngayChon)}</strong>. 
                Vui lòng chọn buổi học từ các ngày khác.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const cardBody = document.querySelector('.card-body');
            const existingAlert = cardBody.querySelector('.alert');
            if (existingAlert) existingAlert.remove();
            
            cardBody.insertBefore(thongBao, cardBody.querySelector('.mt-3'));
        }
        
        checkFormValidity();
    }

    // Hàm format ngày Việt Nam
    function formatNgayVietNam(ngayISO) {
        const ngay = new Date(ngayISO + 'T00:00:00');
        return ngay.toLocaleDateString('vi-VN', {
            weekday: 'long',
            year: 'numeric',
            month: 'numeric',
            day: 'numeric'
        });
    }

    // Khi chọn buổi học
    selMaBuoiHoc?.addEventListener('change', function() {
        console.log("🎯 Chọn buổi học:", this.value);
        
        // Cập nhật ngày theo buổi học được chọn (gợi ý)
        if (this.value && buoiHocData[selMaLop.value]) {
            const buoiHocCuaLop = buoiHocData[selMaLop.value].buoiHoc;
            const buoiHocDuocChon = buoiHocCuaLop.find(buoi => buoi.maBuoiHoc == this.value);
            
            if (buoiHocDuocChon) {
                selNgayDiemDanh.value = buoiHocDuocChon.ngayHoc;
                console.log(`📅 Đã cập nhật ngày theo buổi học: ${buoiHocDuocChon.ngayHoc}`);
            }
        }
        
        checkFormValidity();
    });

    if (selMaLop && selMaLop.options.length === 2) { // 1 option mặc định + 1 lớp
        selMaLop.selectedIndex = 1;
        selMaLop.dispatchEvent(new Event('change'));
    }
    // === XỬ LÝ AJAX TẢI BẢNG ĐIỂM DANH ===
    
    btnXem?.addEventListener('click', async function() {
        const maLop = selMaLop.value;
        const maBuoiHoc = selMaBuoiHoc.value;
        const ngayDiemDanh = selNgayDiemDanh.value;

        if (!maLop || !maBuoiHoc || !ngayDiemDanh) {
            alert('Vui lòng chọn đầy đủ thông tin.');
            return;
        }

        btnXem.disabled = true;
        btnXem.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
        cardDiemDanh.style.display = 'block';
        tbodyDiemDanh.innerHTML = `<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>`;
        cardSubTitleDiemDanh.innerHTML = '';

        try {
            const response = await fetch(`index.php?controller=chuyencan&action=ajaxGetBangDiemDanh&maLop=${maLop}&maBuoiHoc=${maBuoiHoc}`, {
                cache: 'no-store' 
            });
            if (!response.ok) throw new Error('Lỗi mạng khi tải dữ liệu.');

            const result = await response.json();
            if (result.error) throw new Error(result.error);

            const { danhSachHocSinh, thongTinBuoiHoc } = result;

            const ngayFormatted = new Date(thongTinBuoiHoc.ngayHoc + 'T00:00:00').toLocaleDateString('vi-VN');
            cardSubTitleDiemDanh.innerHTML = `
                Lớp: ${thongTinBuoiHoc.tenLop} | Môn: ${thongTinBuoiHoc.tenMonHoc} | 
                Ngày: <strong>${ngayFormatted}</strong> | Tiết: ${thongTinBuoiHoc.tietBatDau}-${thongTinBuoiHoc.tietKetThuc} |
                Giáo viên: ${thongTinBuoiHoc.tenGiaoVien}
            `;

            hiddenInputsContainer.innerHTML = `
                <input type="hidden" name="maLop" value="${maLop}">
                <input type="hidden" name="maBuoiHoc" value="${maBuoiHoc}">
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
                            <label class="btn btn-outline-info btn-sm ${trangThai == 'VANG_CO_PHEP' ? 'active' : ''}">
                                <input type="radio" name="trangthai[${maHS}]" value="VANG_CO_PHEP" ${trangThai == 'VANG_CO_PHEP' ? 'checked' : ''}> Vắng (CP)
                            </label>
                            <label class="btn btn-outline-danger btn-sm ${trangThai == 'VANG_KHONG_PHEP' ? 'active' : ''}">
                                <input type="radio" name="trangthai[${maHS}]" value="VANG_KHONG_PHEP" ${trangThai == 'VANG_KHONG_PHEP' ? 'checked' : ''}> Vắng (KP)
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
        const urlParams = new URLSearchParams(window.location.search);
        
        const maLop = urlParams.get('maLop');
        const maBuoiHoc = urlParams.get('maBuoiHoc');
        const autoload = urlParams.get('autoload');

        if (maLop && maBuoiHoc && autoload) {
            selMaLop.value = maLop;
            selMaLop.dispatchEvent(new Event('change'));
            
            setTimeout(() => {
                selMaBuoiHoc.value = maBuoiHoc;
                checkFormValidity();
                btnXem.click();
                
                window.history.replaceState({}, document.title, "index.php?controller=chuyencan&action=index");
            }, 100);
        }
    }
    
    autoLoadTable();
});