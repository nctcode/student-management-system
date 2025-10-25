// Biến toàn cục
let currentHocPhi = null;
let currentSoTien = null;
let currentKyHoc = null;

// Hàm chọn học phí
function chonHocPhi(maHocPhi, soTien, kyHoc) {
    console.log('🚀 Chọn học phí:', maHocPhi, soTien, kyHoc);

    currentHocPhi = maHocPhi;
    currentSoTien = soTien;
    currentKyHoc = kyHoc;

    // Hiển thị thông tin trong modal
    document.getElementById('displayMaHocPhi').value = '#' + maHocPhi;
    document.getElementById('displaySoTien').value = soTien.toLocaleString() + ' đ';
    document.getElementById('displayKyHoc').value = getTenKyHoc(kyHoc);

    // Hiển thị modal phương thức
    const modal = new bootstrap.Modal(document.getElementById('modalPhuongThuc'));
    modal.show();
}

// Hàm chuyển đổi tên kỳ học
function getTenKyHoc(kyHoc) {
    const kyHocMap = {
        'HK1': 'Học kỳ 1',
        'HK2': 'Học kỳ 2',
        'CA_NAM': 'Cả năm'
    };
    return kyHocMap[kyHoc] || kyHoc;
}

// Hàm chọn thanh toán online
function chonOnline() {
    console.log('💰 Chọn thanh toán online');

    // Đóng modal phương thức
    const modalPhuongThuc = bootstrap.Modal.getInstance(document.getElementById('modalPhuongThuc'));
    if (modalPhuongThuc) modalPhuongThuc.hide();

    // Reset và hiển thị modal QR
    document.getElementById('qrLoading').style.display = 'block';
    document.getElementById('qrContent').style.display = 'none';

    const modalQR = new bootstrap.Modal(document.getElementById('modalQR'));
    modalQR.show();

    // Tạo QR code sau 1 giây
    setTimeout(() => {
        document.getElementById('qrLoading').style.display = 'none';
        document.getElementById('qrContent').style.display = 'block';

        // Tạo QR code
        document.getElementById('qrImage').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=HP${currentHocPhi}_${currentSoTien}`;

        console.log('✅ QR code đã được tạo');

    }, 1000);
}

// Hàm chọn thanh toán tại trường
function chonTruong() {
    if (confirm('In phiếu thu và thanh toán tại trường?')) {
        window.open('index.php?controller=hocphi&action=inphieu&maHocPhi=' + currentHocPhi, '_blank');
        bootstrap.Modal.getInstance(document.getElementById('modalPhuongThuc')).hide();
    }
}

// 🎯 HÀM TEST THÀNH CÔNG - GỌI API THẬT
function testThanhCong() {
    console.log('🎉 TEST THÀNH CÔNG - Bắt đầu...');

    // 1. Hiển thị loading
    const paymentStatus = document.getElementById('paymentStatus');
    if (paymentStatus) {
        paymentStatus.innerHTML =
            '<div class="text-center"><div class="spinner-border spinner-border-sm me-2"></div>Đang xử lý thanh toán thật...</div>';
    }

    // 2. Gửi request thanh toán THẬT đến server
    const formData = new FormData();
    formData.append('maHocPhi', currentHocPhi);
    formData.append('phuongThuc', 'VI_DIEN_TU'); // Phương thức test

    console.log('📤 Gửi request thanh toán thật:', {
        maHocPhi: currentHocPhi,
        soTien: currentSoTien,
        phuongThuc: 'VI_DIEN_TU'
    });

    fetch('index.php?controller=hocphi&action=thanhtoan', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('📥 Nhận response:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('✅ Dữ liệu trả về:', data);

            // Đóng modal QR
            const modalQR = bootstrap.Modal.getInstance(document.getElementById('modalQR'));
            if (modalQR) modalQR.hide();

            if (data.success) {
                // Hiển thị modal thành công với mã giao dịch THẬT
                document.getElementById('successMaGiaoDich').textContent = data.maGiaoDich;
                const modalSuccess = new bootstrap.Modal(document.getElementById('modalSuccess'));
                modalSuccess.show();

                console.log('🎊 Thanh toán THẬT thành công! Mã GD:', data.maGiaoDich);
            } else {
                // Hiển thị lỗi nếu thanh toán thất bại
                alert('❌ Thanh toán thất bại: ' + (data.message || 'Lỗi không xác định'));
                console.error('💥 Thanh toán thất bại:', data);
            }
        })
        .catch(error => {
            console.error('💥 Lỗi fetch:', error);
            alert('❌ Lỗi kết nối đến server');
        });
}

// 🎯 HÀM TEST THẤT BẠI - ĐƠN GIẢN KHÔNG DÙNG paymentStatus
function testThatBai() {
    console.log('💥 TEST THẤT BẠI - Bắt đầu...');

    // ĐÓNG MODAL QR VÀ HIỂN THỊ MODAL THẤT BẠI NGAY LẬP TỨC
    try {
        // 1. Đóng modal QR
        const modalQR = document.getElementById('modalQR');
        if (modalQR) {
            const modalQRInstance = bootstrap.Modal.getInstance(modalQR);
            if (modalQRInstance) {
                modalQRInstance.hide();
                console.log('✅ Đã đóng modal QR');
            }
        }

        // 2. Hiển thị modal thất bại
        const modalFail = document.getElementById('modalFail');
        if (modalFail) {
            const modalFailInstance = new bootstrap.Modal(modalFail);
            modalFailInstance.show();
            console.log('💥 Đã hiển thị modal thất bại');
        } else {
            console.error('❌ Không tìm thấy modal thất bại');
            alert('❌ Thanh toán thất bại!');
        }
    } catch (error) {
        console.error('💥 Lỗi trong testThatBai:', error);
        alert('❌ Thanh toán thất bại!');
    }
}

// Các hàm phụ trợ
function huyThanhToan() {
    bootstrap.Modal.getInstance(document.getElementById('modalQR')).hide();
}

function xemBienLai() {
    const maGiaoDich = document.getElementById('successMaGiaoDich').textContent;
    window.open('index.php?controller=hocphi&action=bienlai&maGiaoDich=' + maGiaoDich, '_blank');
    dongModal();
}

function dongModal() {
    bootstrap.Modal.getInstance(document.getElementById('modalSuccess')).hide();
    // Reload trang sau 0.5 giây
    setTimeout(() => {
        location.reload();
    }, 500);
}

function thuLai() {
    bootstrap.Modal.getInstance(document.getElementById('modalFail')).hide();
    chonOnline();
}

function chonPhuongThucKhac() {
    bootstrap.Modal.getInstance(document.getElementById('modalFail')).hide();
    chonHocPhi(currentHocPhi, currentSoTien, currentKyHoc);
}

// 🛠 KIỂM TRA KHI TRANG LOAD
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== KIỂM TRA HỆ THỐNG ===');
    console.log('Tất cả modal đã sẵn sàng!');
    console.log('Bootstrap:', typeof bootstrap !== 'undefined');
});