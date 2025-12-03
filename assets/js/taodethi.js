
// Tạo bảng câu hỏi
function taoBang() {
    const soLuong = parseInt(document.getElementById('soLuongCau').value);
    const tbody = document.querySelector('#bangDeThi tbody');
    tbody.innerHTML = '';

    for (let i = 0; i < soLuong; i++) {
        const row = document.createElement('tr');

        const cellNoiDung = document.createElement('td');
        const inputNoiDung = document.createElement('input');
        inputNoiDung.type = 'text';
        inputNoiDung.placeholder = `Nhập nội dung câu ${i + 1}`;
        inputNoiDung.className = 'form-control';
        inputNoiDung.name = 'noiDung[]';
        cellNoiDung.appendChild(inputNoiDung);

        const cellMucDiem = document.createElement('td');
        const inputMucDiem = document.createElement('input');
        inputMucDiem.type = 'number';
        inputMucDiem.min = 0;
        inputMucDiem.className = 'form-control';
        inputMucDiem.name = 'mucDiem[]';
        cellMucDiem.appendChild(inputMucDiem);

        row.appendChild(cellNoiDung);
        row.appendChild(cellMucDiem);
        tbody.appendChild(row);
    }
}

// Hiển thị modal
function showModal(message, isSuccess = false, onClose = null) {
    const modalEl = document.getElementById('thongBaoModal');
    const modalHeader = modalEl.querySelector('.modal-header');
    const modalBody = modalEl.querySelector('.modal-body');

    modalHeader.classList.remove('bg-success', 'bg-danger');
    modalHeader.classList.add(isSuccess ? 'bg-success' : 'bg-danger');
    modalBody.textContent = message;

    const modal = new bootstrap.Modal(modalEl);

    if (onClose) {
        modalEl.addEventListener('hidden.bs.modal', function handler() {
            modalEl.removeEventListener('hidden.bs.modal', handler);
            onClose();
        });
    }

    modal.show();
}

// Validate trước khi submit form
document.getElementById('deThiForm').addEventListener('submit', function (e) {
    const soLuong = parseInt(document.getElementById('soLuongCau').value);
    const noiDungInputs = document.querySelectorAll('input[name="noiDung[]"]');
    const mucDiemInputs = document.querySelectorAll('input[name="mucDiem[]"]');

    // Kiểm tra số lượng câu hỏi
    for (let i = 0; i < soLuong; i++) {
        const noiDung = noiDungInputs[i].value.trim();
        const mucDiem = mucDiemInputs[i].value;

        if (!noiDung) {
            e.preventDefault();
            showModal(`Câu hỏi ${i + 1} chưa nhập nội dung!`);
            return;
        }

        if (mucDiem === '' || isNaN(mucDiem) || Number(mucDiem) < 0) {
            e.preventDefault();
            showModal(`Câu hỏi ${i + 1} điểm chưa hợp lệ!`);
            return;
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    if (window.deThiMessage) {
        showModal(
            window.deThiMessage.text,
            window.deThiMessage.status === 'success',
            function () {
                if (window.deThiMessage.status === 'success') {
                    location.reload(); // reload khi đóng modal nếu thành công
                }
            }
        );
    }
});



// Hiển thị danh sách đề thi
document.getElementById('btnXemDeThi').addEventListener('click', function () {
    const container = document.getElementById('danhSachDeThiContainer');
    container.style.display = container.style.display === 'none' ? 'block' : 'none';
    document.getElementById('chiTietDeThiContainer').style.display = 'none';
});

// Xem chi tiết đề thi
document.addEventListener('DOMContentLoaded', function () {
    const deThiListJS = window.deThiList || [];

    document.querySelectorAll('.btnXemChiTiet').forEach(btn => {
        btn.addEventListener('click', function () {
            const maDeThi = this.dataset.id;
            const deThi = deThiListJS.find(d => d.maDeThi == maDeThi);
            if (!deThi) return;

            let html = `
                <table class="table table-bordered">
                    <tr><th>Lớp</th><td>${deThi.maKhoi}</td></tr>
                    <tr><th>Học kỳ</th><td>${deThi.maNienKhoa}</td></tr>
                    <tr><th>Môn học</th><td>${deThi.monHoc}</td></tr>
                    <tr><th>Tiêu đề</th><td>${deThi.tieuDe}</td></tr>
                    <tr><th>Nội dung tổng quát</th><td>${deThi.noiDung}</td></tr>
                    <tr><th>Câu hỏi</th><td>
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr><th>#</th><th>Nội dung</th><th>Điểm</th></tr>
                            </thead>
                            <tbody>
            `;

            deThi.cauHoi.forEach((c, i) => {
                html += `<tr><td>${i + 1}</td><td>${c.noiDungCauHoi || c.noiDung}</td><td>${c.mucDiem}</td></tr>`;
            });

            html += `</tbody></table></td></tr></table>`;

            document.getElementById('chiTietDeThiContent').innerHTML = html;
            document.getElementById('chiTietDeThiContainer').style.display = 'block';
            window.scrollTo({
                top: document.getElementById('chiTietDeThiContainer').offsetTop,
                behavior: 'smooth'
            });
        });
    });
});

// Nút đóng danh sách và chi tiết đề thi
document.getElementById('btnDongDanhSach').addEventListener('click', function () {
    document.getElementById('danhSachDeThiContainer').style.display = 'none';
    document.getElementById('chiTietDeThiContainer').style.display = 'none';
});

