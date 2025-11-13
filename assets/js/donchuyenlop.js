// Gắn sự kiện toggle xem chi tiết đơn
document.addEventListener("DOMContentLoaded", () => {
    const items = document.querySelectorAll(".list-item");
    items.forEach(item => {
        item.addEventListener("click", () => {
            const detail = item.nextElementSibling;
            if (detail && detail.classList.contains("detail-box")) {
                detail.style.display = (detail.style.display === "block") ? "none" : "block";
            }
        });
    });

    // Chuyển chế độ trường đi / trường đến
    const modeButtons = document.querySelectorAll(".mode-switch button");
    modeButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            modeButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            // TODO: gọi API lọc danh sách theo loại trường
        });
    });

    // Nút tìm kiếm
    const searchButton = document.querySelector(".filter-bar button");
    searchButton.addEventListener("click", () => {
        const keyword = document.querySelector(".filter-bar input").value.trim();
        // TODO: gọi API tìm kiếm theo từ khóa
        console.log("Tìm kiếm:", keyword);
    });
});