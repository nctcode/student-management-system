<!-- BIỂU ĐỒ THỐNG KÊ -->
<div class="card mt-5 p-4 shadow-sm">
    <h3 class="text-center mb-4">Biểu đồ thống kê kết quả học tập</h3>

    <?php if (!empty($diemTB_HS) && !empty($monHoc)): ?>
        <!-- Biểu đồ điểm trung bình -->
        <div class="mb-5">
            <h5 class="text-center mb-3">Phân bố điểm trung bình theo môn</h5>
            <canvas id="chartDiemTB" style="width:100%; height:350px;"></canvas>
        </div>
    <?php endif; ?>

    <?php if (!empty($tongHocLuc) && !empty($tongHanhKiem)): ?>
        <!-- Biểu đồ học lực và hạnh kiểm -->
        <div class="mt-5">
            <h5 class="text-center mb-3">Tỷ lệ học lực & hạnh kiểm</h5>
            <div class="row">
                <div class="col-md-6">
                    <canvas id="chartHocLuc" style="width:100%; height:300px;"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="chartHanhKiem" style="width:100%; height:300px;"></canvas>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ====== BIỂU ĐỒ ĐIỂM TRUNG BÌNH ======
    // ====== BIỂU ĐỒ PHÂN BỐ ĐIỂM TRUNG BÌNH THEO MÔN ======
    const monHoc = <?= json_encode($monHoc ?? []) ?>;
    const diemTB_HS = <?= json_encode($diemTB_HS ?? []) ?>;

    if (monHoc.length > 0 && Object.keys(diemTB_HS).length > 0) {
        const labelsKhoang = ['<5', '5–6.9', '7–7.9', '8–8.9', '9–10'];
        const phanBoTheoMon = {}; // {tenMon: [soHS_<5, soHS_5_6.9, ...]}

        monHoc.forEach(m => {
            const maMH = m.maMonHoc;
            const tenMH = m.tenMonHoc;
            const counts = [0, 0, 0, 0, 0]; // tương ứng 5 khoảng
            for (let maHS in diemTB_HS) {
                const diem = diemTB_HS[maHS][maMH] ?? 0;
                if (diem < 5) counts[0]++;
                else if (diem < 7) counts[1]++;
                else if (diem < 8) counts[2]++;
                else if (diem < 9) counts[3]++;
                else counts[4]++;
            }
            phanBoTheoMon[tenMH] = counts;
        });

        // Tạo dataset cho từng khoảng điểm
        const datasets = labelsKhoang.map((label, i) => ({
            label,
            data: Object.keys(phanBoTheoMon).map(mon => phanBoTheoMon[mon][i]),
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(255, 159, 64, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(54, 162, 235, 0.6)'
            ][i]
        }));

        new Chart(document.getElementById('chartDiemTB'), {
            type: 'bar',
            data: {
                labels: Object.keys(phanBoTheoMon),
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Phân bố điểm trung bình theo môn học'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số học sinh'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Môn học'
                        }
                    }
                }
            }
        });
    }


    // ====== BIỂU ĐỒ HỌC LỰC ======
    const tongHocLuc = <?= json_encode($tongHocLuc ?? []) ?>;
    const tongHanhKiem = <?= json_encode($tongHanhKiem ?? []) ?>;

    if (Object.keys(tongHocLuc).length > 0) {
        new Chart(document.getElementById('chartHocLuc'), {
            type: 'pie',
            data: {
                labels: Object.keys(tongHocLuc),
                datasets: [{
                    data: Object.values(tongHocLuc),
                    backgroundColor: ['#4CAF50', '#FFCE56', '#36A2EB', '#FF6384', '#9966FF']
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Phân bố học lực'
                    }
                }
            }
        });
    }

    // ====== BIỂU ĐỒ HẠNH KIỂM ======
    if (Object.keys(tongHanhKiem).length > 0) {
        new Chart(document.getElementById('chartHanhKiem'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(tongHanhKiem),
                datasets: [{
                    data: Object.values(tongHanhKiem),
                    backgroundColor: ['#FF9F40', '#36A2EB', '#FF6384', '#4BC0C0']
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Phân bố hạnh kiểm'
                    }
                }
            }
        });
    }
</script>