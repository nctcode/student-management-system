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


    function checkFormValidity() {
        const valid = selMaLop.value && selMaBuoiHoc.value && selNgayDiemDanh.value;
        btnXem.disabled = !valid;
    }

    // Hàm cập nhật danh sách buổi học dựa trên Lớp và Ngày đã chọn
    function capNhatDropdownBuoiHoc() {
        return new Promise((resolve) => {
            const maLopChon = selMaLop.value;
            const ngayChon = selNgayDiemDanh.value;
            selMaBuoiHoc.innerHTML = '<option value="">Chọn buổi học</option>';
            selMaBuoiHoc.disabled = true;
            cardDiemDanh.style.display = 'none';

            if (maLopChon && ngayChon) {
                const buoiHocCuaLop = buoiHocData[maLopChon] ? buoiHocData[maLopChon].buoiHoc : [];
                const buoiHocTheoNgay = buoiHocCuaLop.filter(buoi => buoi.ngayHoc === ngayChon);

                if (buoiHocTheoNgay.length > 0) {
                    buoiHocTheoNgay.forEach(buoi => {
                        const optionText = `${buoi.tenMonHoc} (Tiết ${buoi.tietBatDau}-${buoi.tietKetThuc})`;
                        const option = new Option(optionText, buoi.maBuoiHoc);
                        selMaBuoiHoc.add(option);
                    });
                    selMaBuoiHoc.disabled = false;
                    selMaBuoiHoc.selectedIndex = 1; 
                    taiBangDiemDanh();
                } else {
                    selMaBuoiHoc.innerHTML = '';
                    const option = new Option("Không có buổi học trong ngày đó", "");
                    selMaBuoiHoc.add(option);
                    selMaBuoiHoc.disabled = true;
                    selMaBuoiHoc.selectedIndex = 0;
                }

                selMaBuoiHoc?.addEventListener('change', function() {
                    checkFormValidity();
                    if (this.value) {
                        taiBangDiemDanh();
                    }
                });

                btnXem?.addEventListener('click', taiBangDiemDanh);
            }
            checkFormValidity();
            resolve();
        });
    }

    selMaLop?.addEventListener('change', capNhatDropdownBuoiHoc);
    selNgayDiemDanh?.addEventListener('change', capNhatDropdownBuoiHoc);

    // === XỬ LÝ AJAX TẢI BẢNG ĐIỂM DANH ===
    async function taiBangDiemDanh() {
        const maLop = selMaLop.value;
        const maBuoiHoc = selMaBuoiHoc.value;

        if (!maLop || !maBuoiHoc) return;

        btnXem.disabled = true;
        btnXem.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
        cardDiemDanh.style.display = 'block';
        tbodyDiemDanh.innerHTML = `<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải danh sách...</td></tr>`;

        try {
            const response = await fetch(`index.php?controller=chuyencan&action=ajaxGetBangDiemDanh&maLop=${maLop}&maBuoiHoc=${maBuoiHoc}`);
            const result = await response.json();
            
            if (result.error) throw new Error(result.error);

            const { danhSachHocSinh, thongTinBuoiHoc } = result;
            const ngayFormatted = new Date(thongTinBuoiHoc.ngayHoc + 'T00:00:00').toLocaleDateString('vi-VN');
            
            cardSubTitleDiemDanh.innerHTML = `
                Lớp: <strong>${thongTinBuoiHoc.tenLop}</strong> | Môn: ${thongTinBuoiHoc.tenMonHoc} | 
                Ngày: ${ngayFormatted} | Tiết: ${thongTinBuoiHoc.tietBatDau}-${thongTinBuoiHoc.tietKetThuc}
            `;

            hiddenInputsContainer.innerHTML = `
                <input type="hidden" name="maLop" value="${maLop}">
                <input type="hidden" name="maBuoiHoc" value="${maBuoiHoc}">
            `;

            tbodyDiemDanh.innerHTML = ''; 
            danhSachHocSinh.forEach((hs, index) => {
                const trangThai = hs.trangThai || 'CO_MAT';
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="checkbox" class="hs-checkbox" data-mahs="${hs.maHocSinh}"></td>
                    <td>${index + 1}</td>
                    <td>${hs.hoTen}</td>
                    <td>
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-success btn-sm ${trangThai == 'CO_MAT' ? 'active' : ''}">
                                <input type="radio" name="trangthai[${hs.maHocSinh}]" value="CO_MAT" ${trangThai == 'CO_MAT' ? 'checked' : ''}> Có mặt
                            </label>
                            <label class="btn btn-outline-warning btn-sm ${trangThai == 'DI_MUON' ? 'active' : ''}">
                                <input type="radio" name="trangthai[${hs.maHocSinh}]" value="DI_MUON" ${trangThai == 'DI_MUON' ? 'checked' : ''}> Muộn
                            </label>
                            <label class="btn btn-outline-info btn-sm ${trangThai == 'VANG_CO_PHEP' ? 'active' : ''}">
                                <input type="radio" name="trangthai[${hs.maHocSinh}]" value="VANG_CO_PHEP" ${trangThai == 'VANG_CO_PHEP' ? 'checked' : ''}> Vắng (P)
                            </label>
                            <label class="btn btn-outline-danger btn-sm ${trangThai == 'VANG_KHONG_PHEP' ? 'active' : ''}">
                                <input type="radio" name="trangthai[${hs.maHocSinh}]" value="VANG_KHONG_PHEP" ${trangThai == 'VANG_KHONG_PHEP' ? 'checked' : ''}> Vắng (K)
                            </label>
                        </div>
                    </td>
                    <td><input type="text" name="ghichu[${hs.maHocSinh}]" class="form-control form-control-sm" value="${hs.ghiChu || ''}"></td>
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
        updateHiddenInputs(maLop, maBuoiHoc);
    }

    // === LOGIC CÁC NÚT ĐIỂM DANH ===

    // Có mặt tất cả
    btnDiemDanhNhanh?.addEventListener('click', function() {
        const modalElement = document.getElementById('modalConfirmTatCa');
        const modalInstance = new bootstrap.Modal(modalElement);
        modalInstance.show();
    });

    document.getElementById('btnXacNhanDiemDanhTatCa')?.addEventListener('click', function() {
        tbodyDiemDanh.querySelectorAll('input[type="radio"][value="CO_MAT"]').forEach(r => {
            r.checked = true;
            const btnGroup = r.closest('.btn-group');
            if (btnGroup) {
                btnGroup.querySelectorAll('label').forEach(l => l.classList.remove('active'));
                r.closest('label').classList.add('active');
            }
        });

        const modalElement = document.getElementById('modalConfirmTatCa');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        modalInstance.hide();
    });

    // Áp dụng cho nhóm
    btnApDungNhom?.addEventListener('click', function() {
        const selectedCheckboxes = tbodyDiemDanh.querySelectorAll('.hs-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            showBootstrapAlert('Vui lòng chọn ít nhất một học sinh để áp dụng.', 'warning');
            return;
        }
        
        new bootstrap.Modal(document.getElementById('modalApDungNhom')).show()
    });

    document.getElementById('btnXacNhanApDung')?.addEventListener('click', function() {
        const selectedRadio = document.querySelector('input[name="groupStatus"]:checked');
        
        if (!selectedRadio) return;
        
        const valueToSet = selectedRadio.value;
        const selectedCheckboxes = tbodyDiemDanh.querySelectorAll('.hs-checkbox:checked');

        selectedCheckboxes.forEach(checkbox => {
            const tr = checkbox.closest('tr');
            const radioToSelect = tr.querySelector(`input[type="radio"][value="${valueToSet}"]`);
            
            if (radioToSelect) {
                radioToSelect.checked = true;
                const btnGroup = radioToSelect.closest('.btn-group');
                btnGroup.querySelectorAll('label').forEach(label => label.classList.remove('active'));
                radioToSelect.closest('label').classList.add('active');
            }
        });

        new bootstrap.Modal(document.getElementById('modalApDungNhom')).hide();
        checkAllNhom.checked = false;
        selectedCheckboxes.forEach(cb => cb.checked = false);

        selectedCheckboxes.forEach(checkbox => {
            const tr = checkbox.closest('tr');
            const radioToSelect = tr.querySelector(`input[type="radio"][value="${valueToSet}"]`);
            if (radioToSelect) {
                radioToSelect.checked = true;
                const btnGroup = radioToSelect.closest('.btn-group');
                btnGroup.querySelectorAll('label').forEach(l => l.classList.remove('active'));
                radioToSelect.closest('label').classList.add('active');
            }
        });
    });

    checkAllNhom?.addEventListener('change', function() {
        tbodyDiemDanh.querySelectorAll('.hs-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // Nhấn nút "Hủy"
    btnHuy?.addEventListener('click', function() {
        const modalElement = document.getElementById('modalConfirmHuy');
        const modalInstance = new bootstrap.Modal(modalElement);
        modalInstance.show();
    });

    document.getElementById('btnXacNhanHuyThaoTac')?.addEventListener('click', function() {
        cardDiemDanh.style.display = 'none';
        selMaBuoiHoc.innerHTML = '<option value="">Chọn buổi học</option>';
        selMaBuoiHoc.disabled = true;
        selMaLop.value = '';
        
        checkFormValidity();

        const modalElement = document.getElementById('modalConfirmHuy');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        modalInstance.hide();

        showBootstrapAlert('Đã hủy thao tác điểm danh.', 'info');
    });

    if (selMaLop && selMaLop.options.length === 2) {
        selMaLop.selectedIndex = 1;
        selMaLop.dispatchEvent(new Event('change'));
    }

    // Hàm hiển thị thông báo
    function showBootstrapAlert(message, type = 'danger') {
        const container = document.getElementById('js-alert-container');
        
        if (!container) return;

        container.innerHTML = `
            <div class="alert alert-${type} fade show" role="alert">
                ${message}
            </div>
        `;

        window.scrollTo({ top: 0, behavior: 'smooth' });

        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) alert.remove();
        }, 3000);
    }

    function updateHiddenInputs(maLop, maBuoiHoc) {
        const ngayDiemDanh = selNgayDiemDanh.value;
        hiddenInputsContainer.innerHTML = `
            <input type="hidden" name="maLop" value="${maLop}">
            <input type="hidden" name="maBuoiHoc" value="${maBuoiHoc}">
            <input type="hidden" name="ngayDiemDanh" value="${ngayDiemDanh}">
        `;
    }

    // === TỰ ĐỘNG TẢI BẢNG ===
    async function autoLoadTable() {
        const urlParams = new URLSearchParams(window.location.search);
        const maLop = urlParams.get('maLop');
        const maBuoiHoc = urlParams.get('maBuoiHoc');
        const ngayDiemDanh = urlParams.get('ngayDiemDanh');
        const autoload = urlParams.get('autoload');

        if (maLop && maBuoiHoc && ngayDiemDanh && autoload) {
            selMaLop.value = maLop;
            selNgayDiemDanh.value = ngayDiemDanh;
            await capNhatDropdownBuoiHoc();
            selMaBuoiHoc.value = maBuoiHoc;
            checkFormValidity();
            btnXem.click();
            window.history.replaceState({}, document.title, "index.php?controller=chuyencan&action=index");
        }
    }
    autoLoadTable();
});