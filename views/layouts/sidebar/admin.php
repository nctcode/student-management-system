<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <h5>Menu Quản Trị</h5>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="index.php?controller=home&action=admin" class="active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <?php if (in_array($_SESSION['user']['vaiTro'], ['QTV', 'BGH', 'GIAOVIEN'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=tinnhan&action=guitinnhan">
                    <i class="fas fa-paper-plane"></i>
                    <span>Gửi tin nhắn</span>
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=tinnhan&action=index">
                    <i class="fas fa-paper-plane"></i>
                    <span>Danh sách tin nhắn</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=quanlynguoidung&action=index">
                    <i class="fas fa-users"></i>
                    <span>Quản lý người dùng</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thoikhoabieu&action=taotkb">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Tạo thời khóa biểu</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thoikhoabieu&action=quanlytkb">
                    <i class="fas fa-list-alt"></i>
                    <span>Quản lý TKB</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-school"></i>
                    <span>Quản lý lớp học</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=hocphi&action=index">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Học phí</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-book"></i>
                    <span>Môn học</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=donchuyenloptruong&action=index">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Đơn chuyển lớp</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-cog"></i>
                    <span>Cài đặt hệ thống</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-chart-bar"></i>
                    <span>Báo cáo thống kê</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=tuyensinh&action=danhsachhoso">
                    <i class="fas fa-user-graduate"></i>
                    <span>Tuyển sinh</span>
                </a>
            </li>

            <!-- Đăng thông báo -->
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thongbao&action=dangthongbao">
                    <i class="fas fa-bullhorn"></i>
                    <span>Đăng thông báo</span>
                </a>
            </li>

            <!-- Danh sách thông báo -->
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thongbao&action=danhsach">
                    <i class="fas fa-list"></i>
                    <span>Danh sách thông báo</span>
                </a>
            </li>
        </ul>
    </div>
</nav>