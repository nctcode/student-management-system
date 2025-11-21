<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <h5>Menu Ban Giám Hiệu</h5>
        </div>
        <ul class="sidebar-menu">
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=home&action=principal">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <?php if (in_array($_SESSION['user']['vaiTro'], ['GIAOVIEN'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=tinnhan&action=guitinnhan">
                    <i class="fas fa-paper-plane"></i>
                    <span>Gửi tin nhắn</span>
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=donchuyenloptruong&action=index">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Đơn chuyển lớp/trường</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thoikhoabieu&action=xemtkb">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Thời khóa biểu</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thongke&action=index">
                    <i class="fas fa-chart-bar"></i>
                    <span>Thống kê báo cáo</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=giaovien&action=index">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Quản lý giáo viên</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=hocsinh&action=index">
                    <i class="fas fa-users"></i>
                    <span>Quản lý học sinh</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=lophoc&action=index">
                    <i class="fas fa-school"></i>
                    <span>Quản lý lớp học</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=PhanCongGVBMCN&action=index">
                    <i class="fas fa-chalkboard-teacher"></i>Phân công giáo viên
                </a>
            </li>

            <!-- <li class="nav-item">
                <a href="index.php?controller=thongbao&action=dangthongbao" class="nav-link">
                    <i class="nav-icon fas fa-bullhorn"></i>
                    <p>Đăng thông báo</p>
                </a>
            </li> -->
            <li class="nav-item">
                <a href="index.php?controller=thongbao&action=danhsach" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <p>Danh sách thông báo</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=baocao&action=index">
                    <i class="fas fa-file-alt"></i>
                    <span>Báo cáo học tập</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=tuyensinh&action=danhsachhoso">
                    <i class="fas fa-user-graduate"></i>
                    <span>Tuyển sinh</span>
                </a>
            </li>
        </ul>
    </div>
</nav>