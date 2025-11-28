<?php
// views/thongbao/danhsach.php
$title = $data['title'] ?? 'Danh Sách Thông Báo';
$thongBao = $data['thongBao'] ?? [];
$userRole = $data['userRole'] ?? '';
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">📢 Danh sách thông báo</h2>
            <p class="text-muted mb-0">Quản lý và xem tất cả thông báo trong hệ thống</p>
        </div>
        <?php if (in_array($userRole, ['QTV', 'BGH'])): ?>
        <a href="index.php?controller=thongbao&action=dangthongbao" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Đăng thông báo
        </a>
        <?php endif; ?>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="filterLoai" class="form-label">Lọc theo loại</label>
                    <select class="form-select" id="filterLoai">
                        <option value="">Tất cả loại</option>
                        <option value="CHUNG">Thông báo chung</option>
                        <option value="LOP">Thông báo lớp học</option>
                        <option value="MON_HOC">Thông báo môn học</option>
                        <option value="KHOA_HOC">Thông báo khóa học</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterUuTien" class="form-label">Lọc theo ưu tiên</label>
                    <select class="form-select" id="filterUuTien">
                        <option value="">Tất cả mức độ</option>
                        <option value="KHAN_CAP">Khẩn cấp</option>
                        <option value="CAO">Cao</option>
                        <option value="TRUNG_BINH">Trung bình</option>
                        <option value="THAP">Thấp</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchThongBao" class="form-label">Tìm kiếm</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchThongBao" placeholder="Tìm theo tiêu đề...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                Danh sách thông báo
                <span class="badge bg-primary ms-2"><?php echo count($thongBao); ?></span>
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($thongBao)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có thông báo nào</h5>
                    <p class="text-muted">Hiện tại không có thông báo nào để hiển thị.</p>
                    <?php if (in_array($userRole, ['QTV', 'BGH'])): ?>
                    <a href="index.php?controller=thongbao&action=dangthongbao" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-2"></i>Đăng thông báo đầu tiên
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($thongBao as $tb): ?>
                        <?php
                        // Xác định badge ưu tiên
                        $priorityBadge = '';
                        switch ($tb['uuTien']) {
                            case 'KHAN_CAP':
                                $priorityBadge = '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Khẩn cấp</span>';
                                break;
                            case 'CAO':
                                $priorityBadge = '<span class="badge bg-warning"><i class="fas fa-exclamation-circle me-1"></i>Cao</span>';
                                break;
                            case 'TRUNG_BINH':
                                $priorityBadge = '<span class="badge bg-info"><i class="fas fa-info-circle me-1"></i>Trung bình</span>';
                                break;
                            case 'THAP':
                                $priorityBadge = '<span class="badge bg-secondary"><i class="fas fa-arrow-down me-1"></i>Thấp</span>';
                                break;
                        }

                        // Xác định badge loại thông báo
                        $typeBadge = '';
                        switch ($tb['loaiThongBao']) {
                            case 'CHUNG':
                                $typeBadge = '<span class="badge bg-primary">Thông báo chung</span>';
                                break;
                            case 'LOP':
                                $typeBadge = '<span class="badge bg-success">Thông báo lớp</span>';
                                break;
                            case 'MON_HOC':
                                $typeBadge = '<span class="badge bg-info">Thông báo môn học</span>';
                                break;
                            case 'KHOA_HOC':
                                $typeBadge = '<span class="badge bg-warning">Thông báo khóa học</span>';
                                break;
                        }

                        // Xác định badge người nhận
                        $receiverBadge = '';
                        switch ($tb['nguoiNhan']) {
                            case 'TAT_CA':
                                $receiverBadge = '<span class="badge bg-dark">Tất cả mọi người</span>';
                                break;
                            case 'HOC_SINH':
                                $receiverBadge = '<span class="badge bg-success">Học sinh</span>';
                                break;
                            case 'PHU_HUYNH':
                                $receiverBadge = '<span class="badge bg-primary">Phụ huynh</span>';
                                break;
                            case 'GIAO_VIEN':
                                $receiverBadge = '<span class="badge bg-info">Giáo viên</span>';
                                break;
                        }

                        // Xác định trạng thái
                        $statusBadge = '';
                        if ($tb['trangThai'] === 'Đã xem') {
                            $statusBadge = '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Đã xem</span>';
                        } else {
                            $statusBadge = '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Chưa xem</span>';
                        }

                        // Format thời gian
                        $timeAgo = $this->timeAgo($tb['ngayGui']);
                        $fullTime = date('H:i d/m/Y', strtotime($tb['ngayGui']));
                        ?>
                        
                        <div class="list-group-item list-group-item-action p-4 notification-item" 
                             data-ma-thong-bao="<?php echo (int)$tb['maThongBao']; ?>"
                            data-loai="<?php echo htmlspecialchars($tb['loaiThongBao']); ?>"
                            data-uu-tien="<?php echo htmlspecialchars($tb['uuTien']); ?>"
                            style="cursor: pointer;">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start mb-2">
                                        <h6 class="mb-0 me-2 flex-grow-1">
                                            <a href="index.php?controller=thongbao&action=chitiet&maThongBao=<?php echo $tb['maThongBao']; ?>" 
                                               class="text-decoration-none text-dark fw-bold">
                                                <?php echo htmlspecialchars($tb['tieuDe']); ?>
                                            </a>
                                        </h6>
                                        <?php if (!empty($tb['fileDinhKem'])): ?>
                                            <i class="fas fa-paperclip text-muted ms-2" title="Có file đính kèm"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <p class="text-muted mb-2">
                                        <?php 
                                        $noiDung = strip_tags($tb['noiDung']);
                                        echo strlen($noiDung) > 150 ? substr($noiDung, 0, 150) . '...' : $noiDung;
                                        ?>
                                    </p>
                                    
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <?php echo $priorityBadge; ?>
                                        <?php echo $typeBadge; ?>
                                        <?php echo $receiverBadge; ?>
                                        <?php echo $statusBadge; ?>
                                    </div>
                                    
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="fas fa-user me-1"></i>
                                        <span class="me-3"><?php echo htmlspecialchars($tb['tenNguoiGui'] ?? 'Hệ thống'); ?></span>
                                        <i class="fas fa-clock me-1"></i>
                                        <span title="<?php echo $fullTime; ?>"><?php echo $timeAgo; ?></span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 text-end">
                                    <div class="d-flex justify-content-end gap-2 align-items-center">
                                        <a href="index.php?controller=thongbao&action=chitiet&maThongBao=<?php echo $tb['maThongBao']; ?>" 
                                        class="btn btn-outline-primary btn-sm view-detail-btn">
                                            <i class="fas fa-eye me-1"></i>Xem chi tiết
                                        </a>
                                        
                                        <?php if (in_array($userRole, ['QTV', 'BGH'])): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" 
                                                       href="index.php?controller=thongbao&action=chitiet&maThongBao=<?php echo $tb['maThongBao']; ?>">
                                                        <i class="fas fa-eye me-2"></i>Xem chi tiết
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger delete-notification" 
                                                       href="index.php?controller=thongbao&action=xoa&maThongBao=<?php echo $tb['maThongBao']; ?>" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này?')">
                                                        <i class="fas fa-trash me-2"></i>Xóa thông báo
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.notification-item {
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fa;
    border-left-color: #007bff;
}

.notification-item[data-uu-tien="KHAN_CAP"] {
    border-left-color: #dc3545;
    background-color: #fff5f5;
}

.notification-item[data-uu-tien="CAO"] {
    border-left-color: #ffc107;
    background-color: #fffdf5;
}

.badge {
    font-size: 0.75em;
}
.notification-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.notification-item:hover {
    background-color: #f8f9fa;
    border-left-color: #007bff;
}

/* Ẩn tạm thời cho đến khi JavaScript khởi tạo */
.notification-item:not(.js-initialized) {
    opacity: 0.9;
}
</style>
<!-- THÊM PHẦN NÀY VÀO CUỐI FILE danhsach.php, TRƯỚC </body> -->
<script>
// Sử dụng IIFE để tránh xung đột
(function() {
    'use strict';
    
    let initialized = false;
    
    function initNotifications() {
        if (initialized) return;
        initialized = true;
        
        console.log('Initializing notifications...');
        
        // Filter functionality
        const filterLoai = document.getElementById('filterLoai');
        const filterUuTien = document.getElementById('filterUuTien');
        const searchThongBao = document.getElementById('searchThongBao');
        
        if (filterLoai) filterLoai.addEventListener('change', filterNotifications);
        if (filterUuTien) filterUuTien.addEventListener('change', filterNotifications);
        if (searchThongBao) searchThongBao.addEventListener('input', filterNotifications);

        // Xử lý xóa thông báo
        document.querySelectorAll('.delete-notification').forEach(function(button) {
            button.addEventListener('click', function(e) {
                if (!confirm('Bạn có chắc chắn muốn xóa thông báo này?')) {
                    e.preventDefault();
                }
            });
        });

        // Click vào item để xem chi tiết - FIX TRIỆT ĐỂ
        document.querySelectorAll('.notification-item').forEach(function(item) {
            // Xóa event listener cũ nếu có
            item.removeEventListener('click', handleItemClick);
            // Thêm event listener mới
            item.addEventListener('click', handleItemClick);
        });

        console.log('Notifications initialized successfully');
    }

    function handleItemClick(e) {
        // QUAN TRỌNG: Kiểm tra nếu click vào button "Xem chi tiết" hoặc các link khác
        const clickedElement = e.target;
        const isViewDetailBtn = clickedElement.closest('.view-detail-btn');
        const isLink = clickedElement.closest('a');
        const isButton = clickedElement.closest('button');
        
        // Nếu click vào button "Xem chi tiết" hoặc các link/button khác, KHÔNG xử lý
        if (isViewDetailBtn || isLink || isButton) {
            console.log('Clicked on button/link, allowing default behavior');
            return; // Cho phép link/button hoạt động bình thường
        }
        
        // Ngăn chặn xử lý nhiều lần
        if (e.defaultPrevented) return;
        
        const maThongBao = this.getAttribute('data-ma-thong-bao');
        console.log('Clicked notification area:', maThongBao);
        
        if (maThongBao && maThongBao !== 'undefined') {
            // Ngăn sự kiện mặc định
            e.preventDefault();
            e.stopPropagation();
            
            // Chuyển hướng trực tiếp
            const url = `index.php?controller=thongbao&action=chitiet&maThongBao=${maThongBao}`;
            console.log('Redirecting to:', url);
            window.location.href = url;
            return false;
        } else {
            console.error('Invalid maThongBao:', maThongBao);
        }
    }

    function filterNotifications() {
        const loaiFilter = document.getElementById('filterLoai')?.value || '';
        const uuTienFilter = document.getElementById('filterUuTien')?.value || '';
        const searchText = document.getElementById('searchThongBao')?.value.toLowerCase() || '';
        
        document.querySelectorAll('.notification-item').forEach(function(item) {
            const loai = item.getAttribute('data-loai');
            const uuTien = item.getAttribute('data-uu-tien');
            const title = item.querySelector('h6')?.textContent.toLowerCase() || '';
            const content = item.querySelector('p')?.textContent.toLowerCase() || '';
            
            const loaiMatch = !loaiFilter || loai === loaiFilter;
            const uuTienMatch = !uuTienFilter || uuTien === uuTienFilter;
            const searchMatch = !searchText || title.includes(searchText) || content.includes(searchText);
            
            item.style.display = (loaiMatch && uuTienMatch && searchMatch) ? 'block' : 'none';
        });
    }

    // Khởi tạo khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifications);
    } else {
        initNotifications();
    }

    // Khởi tạo lại khi quay lại trang (cho trình duyệt cache)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            console.log('Page loaded from cache, reinitializing...');
            initialized = false;
            setTimeout(initNotifications, 100);
        }
    });

})();
</script>