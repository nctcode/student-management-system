/* assets/js/thongke.js */

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================================
    // 1. FIX LỖI ACTIVE MENU (QUAN TRỌNG)
    // ========================================================
    const sidebarLink = document.querySelector('.sidebar-menu a[href*="controller=thongke"]');
    if (sidebarLink) {
        sidebarLink.classList.add('active');
        const parentLi = sidebarLink.closest('li');
        if (parentLi) {
            parentLi.classList.add('active');
        }
        // Ép style bằng JS để đảm bảo hiển thị
        sidebarLink.style.backgroundColor = "#4e73df";
        sidebarLink.style.color = "#ffffff";
        sidebarLink.style.fontWeight = "bold";
        const icon = sidebarLink.querySelector('i');
        if(icon) icon.style.color = "#ffffff";
    }

    // ========================================================
    // 2. LỌC LỚP THEO KHỐI
    // ========================================================
    const selectKhoi = document.getElementById('selectKhoi');
    const selectLop = document.getElementById('selectLop');
    
    if(selectKhoi && selectLop){
        const allLopOptions = Array.from(selectLop.options).map(opt => opt.cloneNode(true));

        function filterLop() {
            const khoiId = selectKhoi.value.toString();
            const currentLop = selectLop.value;
            selectLop.innerHTML = '';
            
            let hasCurrent = false;
            allLopOptions.forEach(opt => {
                const dataKhoi = opt.getAttribute('data-khoi');
                if (khoiId === 'all' || opt.value === 'all' || (dataKhoi && dataKhoi.toString() === khoiId)) {
                    const newOpt = opt.cloneNode(true);
                    selectLop.appendChild(newOpt);
                    if (newOpt.value === currentLop) hasCurrent = true;
                }
            });
            if (hasCurrent) selectLop.value = currentLop; else selectLop.value = 'all';
        }
        selectKhoi.addEventListener('change', filterLop);
        filterLop();
    }

    // ========================================================
    // 3. VẼ BIỂU ĐỒ (Sử dụng dữ liệu từ biến toàn cục ThongKeData)
    // ========================================================
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Nunito', sans-serif";
        Chart.defaults.color = '#858796';

        // --- Biểu đồ Phổ Điểm ---
        var ctxPhoDiem = document.getElementById('chartPhoDiem');
        if (ctxPhoDiem && ThongKeData.phoDiem) {
            new Chart(ctxPhoDiem, {
                type: 'bar',
                data: { 
                    labels: ['Kém', 'Yếu', 'Trung Bình', 'Khá', 'Giỏi'], 
                    datasets: [{ 
                        label: 'Số lượng', 
                        data: ThongKeData.phoDiem, 
                        backgroundColor: ['rgba(231, 74, 59, 0.7)', 'rgba(246, 194, 62, 0.7)', 'rgba(133, 135, 150, 0.7)', 'rgba(54, 185, 204, 0.7)', 'rgba(28, 200, 138, 0.7)'],
                        borderColor: ['#e74a3b', '#f6c23e', '#858796', '#36b9cc', '#1cc88a'],
                        borderWidth: 1, borderRadius: 5, barPercentage: 0.6
                    }] 
                },
                options: { 
                    responsive: true, maintainAspectRatio: false, 
                    scales: { 
                        y: { beginAtZero: true, ticks: { precision: 0, maxTicksLimit: 8, padding: 10 }, grid: { borderDash: [2, 2], drawBorder: false } }, 
                        x: { grid: { display: false } } 
                    }, 
                    plugins: { legend: { display: false } } 
                }
            });
        }

        // --- Biểu đồ So Sánh ---
        var ctxSoSanh = document.getElementById('chartSoSanh');
        if (ctxSoSanh && ThongKeData.soSanhHK1 && ThongKeData.soSanhHK2) {
            new Chart(ctxSoSanh, {
                type: 'bar',
                data: { 
                    labels: ['Kém','Yếu','TB','Khá','Giỏi'], 
                    datasets: [
                        { label: 'HK1', data: ThongKeData.soSanhHK1, backgroundColor: '#4e73df', borderRadius: 3 }, 
                        { label: 'HK2', data: ThongKeData.soSanhHK2, backgroundColor: '#1cc88a', borderRadius: 3 }
                    ] 
                },
                options: { 
                    maintainAspectRatio: false, 
                    scales: { 
                        y: { beginAtZero: true, ticks: { precision: 0, maxTicksLimit: 8 }, grid: { borderDash: [2, 2] } }, 
                        x: { grid: { display: false } } 
                    }, 
                    plugins: { legend: { position: 'bottom' } } 
                }
            });
        }

        // --- Biểu đồ Hạnh Kiểm ---
        var ctxHanhKiem = document.getElementById('chartHanhKiem');
        if(ctxHanhKiem && ThongKeData.hanhKiem) {
            new Chart(ctxHanhKiem, { 
                type: 'doughnut', 
                data: { 
                    labels: ['Tốt','Khá','TB','Yếu'], 
                    datasets: [{ data: ThongKeData.hanhKiem, backgroundColor: ['#1cc88a','#36b9cc','#f6c23e','#e74a3b'], hoverOffset: 4 }] 
                }, 
                options: { maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } } } 
            });
        }

        // --- Biểu đồ Tài Chính ---
        var ctxDoanhThu = document.getElementById('chartDoanhThu');
        if(ctxDoanhThu && ThongKeData.doanhThuData) {
            new Chart(ctxDoanhThu, { 
                type: 'line', 
                data: { 
                    labels: ThongKeData.doanhThuLabels, 
                    datasets: [{ 
                        label: 'Doanh Thu (VNĐ)', 
                        data: ThongKeData.doanhThuData, 
                        borderColor: '#4e73df', 
                        backgroundColor: 'rgba(78, 115, 223, 0.05)', 
                        pointRadius: 3, pointBackgroundColor: '#4e73df', pointBorderColor: '#4e73df', fill: true, tension: 0.3 
                    }] 
                }, 
                options: { 
                    maintainAspectRatio: false, 
                    scales: { 
                        y: { beginAtZero: true, grid: { borderDash: [2, 2] }, ticks: { maxTicksLimit: 8, callback: function(value) { return value.toLocaleString('vi-VN', {style: 'currency', currency: 'VND', maximumSignificantDigits: 3}); } } }, 
                        x: { grid: { display: false } } 
                    }, 
                    plugins: { legend: { display: false } } 
                } 
            });
        }
    }
});