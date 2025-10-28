document.addEventListener("DOMContentLoaded", () => {
    // === CÁC BIẾN DOM ===
    const lopSelect = document.getElementById("lop");
    const checkHocSinh = document.getElementById("check_hoc_sinh");
    const checkPhuHuynh = document.getElementById("check_phu_huynh");
    const tbody = document.querySelector("#bangNguoiNhan tbody");
    const chonTatCa = document.getElementById("chonTatCa");
    const soLuongChon = document.getElementById("soLuongChon");
    const textarea = document.getElementById("noidung");
    const demKyTu = document.getElementById("demKyTu");
    const timInput = document.getElementById("timNguoiNhan");
    const soKetQua = document.getElementById("soKetQua");
    
    // === BIẾN PHÂN TRANG ===
    const loadMoreBtn = document.getElementById("loadMoreBtn");
    const PAGE_SIZE = 5; // Số hàng tải mỗi lần
    let cachedData = []; // Lưu trữ TOÀN BỘ danh sách từ API
    let currentFilteredData = []; // Lưu trữ danh sách đã lọc (để tìm kiếm)
    let currentVisibleCount = 0; // Số hàng đang hiển thị

    // === HÀM TẢI DỮ LIỆU TỪ API ===
    async function taiDanhSachNguoiNhan() {
        const lop = lopSelect.value;
        const muonLayHS = checkHocSinh.checked;
        const muonLayPH = checkPhuHuynh.checked;

        if (!lop) {
            tbody.innerHTML = "<tr><td colspan='7' class='text-center text-muted'>Chọn lớp để xem danh sách...</td></tr>";
            cachedData = [];
            currentFilteredData = [];
            loadMoreRows(); // Sẽ ẩn nút và hiển thị "0 kết quả"
            return;
        }

        let promises = [];
        if (muonLayHS) promises.push(fetch(`index.php?controller=tinnhan&action=ajaxLayDanhSachNguoiNhan&lop=${lop}&loai=hoc_sinh`).then(r => r.json()));
        if (muonLayPH) promises.push(fetch(`index.php?controller=tinnhan&action=ajaxLayDanhSachNguoiNhan&lop=${lop}&loai=phu_huynh`).then(r => r.json()));

        if (promises.length === 0) {
            cachedData = [];
            currentFilteredData = [];
            loadMoreRows();
            return;
        }

        try {
            const results = await Promise.all(promises);
            let combinedData = [];
            let hs_data = (muonLayHS && muonLayPH) ? results[0] : (muonLayHS ? results[0] : []);
            let ph_data = (muonLayHS && muonLayPH) ? results[1] : (muonLayPH ? results[0] : []);

            (hs_data || []).forEach(d => combinedData.push({ ...d, vaiTro: 'Học sinh', value_id: 'hs_' + d.id }));
            (ph_data || []).forEach(d => combinedData.push({ ...d, vaiTro: 'Phụ huynh', value_id: 'ph_' + d.id }));

            cachedData = combinedData;
            currentFilteredData = cachedData; // Ban đầu, danh sách lọc là danh sách đầy đủ
            
            // Đặt lại mọi thứ để tải trang đầu tiên
            currentVisibleCount = 0;
            tbody.innerHTML = '';
            loadMoreRows();

        } catch (error) {
            console.error("Không thể tải danh sách:", error);
            tbody.innerHTML = "<tr><td colspan='7' class='text-center text-danger'>Có lỗi xảy ra khi tải dữ liệu.</td></tr>";
            loadMoreBtn.style.display = 'none';
        }
    }

    // === HÀM TẢI THÊM HÀNG (CHÍNH) ===
    function loadMoreRows() {
        // Lấy (các) hàng tiếp theo từ danh sách đã lọc
        const dataToRender = currentFilteredData.slice(currentVisibleCount, currentVisibleCount + PAGE_SIZE);

        // Nếu không có gì để hiển thị (và đây là lần tải đầu tiên)
        if (dataToRender.length === 0 && currentVisibleCount === 0) {
            tbody.innerHTML = "<tr><td colspan='7' class='text-center text-muted'>Không có dữ liệu.</td></tr>";
            soKetQua.textContent = "Tìm thấy 0 kết quả";
            loadMoreBtn.style.display = 'none';
            return;
        }

        // Tạo HTML và thêm vào bảng
        const htmlRows = dataToRender.map(d => {
            let id = d.id;
            let value = d.value_id; 
            let hoTen = d.hoTen || '';
            let vaiTro = d.vaiTro || '';
            let thongTin = '';
            let email = '';
            let soDienThoai = '';

            if (vaiTro === 'Học sinh') {
                thongTin = d.tenLop || '';
            } else { // Phụ huynh
                thongTin = `${d.tenHocSinh || ''} (Lớp ${d.tenLop || ''})`;
                email = d.email || '';
                soDienThoai = d.soDienThoai || '';
            }

            return `
                <tr>
                    <td><input type="checkbox" name="nguoinhan[]" value="${value}" class="nguoinhan"></td>
                    <td>${id}</td>
                    <td>${hoTen}</td>
                    <td><span class="badge ${vaiTro === 'Học sinh' ? 'bg-success' : 'bg-primary'}">${vaiTro}</span></td>
                    <td>${thongTin}</td>
                    <td>${email}</td>
                    <td>${soDienThoai}</td>
                </tr>
            `;
        }).join("");

        tbody.insertAdjacentHTML('beforeend', htmlRows);
        
        // Cập nhật số lượng
        currentVisibleCount += dataToRender.length;
        soKetQua.textContent = `Tìm thấy ${currentFilteredData.length} kết quả (hiển thị ${currentVisibleCount})`;

        // Ẩn/hiện nút "Xem thêm"
        if (currentVisibleCount < currentFilteredData.length) {
            loadMoreBtn.style.display = 'block';
        } else {
            loadMoreBtn.style.display = 'none';
        }

        // Cập nhật số lượng đã chọn 
        capNhatSoLuongChon();
    }

    // === HÀM LỌC (TÌM KIẾM) ===
    timInput?.addEventListener("input", () => {
        const keyword = timInput.value.trim().toLowerCase();

        if (!keyword) {
            currentFilteredData = cachedData; // Nếu trống, dùng lại danh sách đầy đủ
        } else {
            // Lọc từ danh sách đầy đủ (cachedData)
            currentFilteredData = cachedData.filter(item => {
                const name = (item.hoTen || "").toLowerCase();
                const child = (item.tenHocSinh || "").toLowerCase();
                const role = (item.vaiTro || "").toLowerCase();
                return name.includes(keyword) || child.includes(keyword) || role.includes(keyword);
            });
        }

        // Đặt lại và tải trang đầu tiên của kết quả lọc
        currentVisibleCount = 0;
        tbody.innerHTML = '';
        loadMoreRows();
    });

    // === CÁC HÀM PHỤ ===
    function capNhatSoLuongChon() {
        const daChon = document.querySelectorAll("input.nguoinhan:checked").length;
        soLuongChon.textContent = `Đã chọn: ${daChon} người`;
    }

    document.addEventListener("change", e => {
        if (e.target.classList.contains("nguoinhan")) capNhatSoLuongChon();
    });

    textarea?.addEventListener("input", () => {
        const len = textarea.value.length;
        demKyTu.textContent = `${len} / 1000 ký tự`;
    });

    // === Nút Chọn tất cả / Bỏ chọn tất cả (Toggle) ===
    chonTatCa?.addEventListener("click", () => {
        // 1. Lấy tất cả checkbox đang hiển thị trong <tbody>
        const visibleCheckboxes = tbody.querySelectorAll("input.nguoinhan");
        
        if (visibleCheckboxes.length === 0) return; // Không có gì để chọn

        // 2. Đếm số lượng đã được chọn
        const checkedCount = tbody.querySelectorAll("input.nguoinhan:checked").length;
        
        // 3. Quyết định hành động:
        // Nếu số lượng đã chọn < tổng số -> chọn tất cả
        // Nếu số lượng đã chọn = tổng số -> bỏ chọn tất cả
        const shouldCheckAll = (checkedCount < visibleCheckboxes.length);

        // 4. Thực hiện hành động
        visibleCheckboxes.forEach(cb => {
            cb.checked = shouldCheckAll;
        });
        
        // 5. Cập nhật lại số lượng
        capNhatSoLuongChon();
    });

    // === GÁN SỰ KIỆN ===
    lopSelect?.addEventListener("change", taiDanhSachNguoiNhan);
    checkHocSinh?.addEventListener("change", taiDanhSachNguoiNhan);
    checkPhuHuynh?.addEventListener("change", taiDanhSachNguoiNhan);
    
    // Sự kiện cho nút mới
    loadMoreBtn?.addEventListener("click", loadMoreRows);
});