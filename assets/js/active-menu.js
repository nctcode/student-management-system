// Thêm active cho menu sidebar dựa trên URL hiện tại
document.addEventListener('DOMContentLoaded', function() {
    function setActiveMenu() {
        // Xóa tất cả class active cũ
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.classList.remove('active');
        });
        
        const params = new URLSearchParams(window.location.search);
        const currentController = params.get('controller') || '';
        const currentAction = params.get('action') || '';
        
        console.log('Current controller:', currentController);
        console.log('Current action:', currentAction);
        
        // Chỉ tìm link khớp CHÍNH XÁC controller và action
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            try {
                // Chỉ xử lý link có href (không phải link collapse)
                if (!link.href || link.href === '#' || link.hasAttribute('data-bs-toggle')) {
                    return;
                }
                
                const linkUrl = new URL(link.href);
                const linkParams = new URLSearchParams(linkUrl.search);
                const linkController = linkParams.get('controller') || '';
                const linkAction = linkParams.get('action') || '';
                
                console.log('Link:', linkController, linkAction);
                
                // So sánh CHÍNH XÁC controller và action
                if (linkController === currentController && linkAction === currentAction) {
                    link.classList.add('active');
                    console.log('Added active to:', link.textContent);
                }
            } catch (e) {
                console.log('Error parsing URL:', link.href);
            }
        });
        
        // Xử lý đặc biệt cho trang thống kê (ketquahoctap/thongke)
        if (currentController === 'ketquahoctap' && currentAction === 'thongke') {
            // Chỉ thêm active cho link có href chính xác
            const exactLink = document.querySelector('.sidebar-menu a[href="index.php?controller=ketquahoctap&action=thongke"]');
            if (exactLink) {
                exactLink.classList.add('active');
            }
        }
    }
    
    // Gọi hàm khi trang tải xong
    setActiveMenu();
});
