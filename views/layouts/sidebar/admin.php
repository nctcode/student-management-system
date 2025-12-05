<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <h5>Menu Quản Trị</h5>
        </div>
        <ul class="sidebar-menu">
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=home&action=admin">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <?php if (in_array($_SESSION['user']['vaiTro'], ['QTV', 'BGH', 'GIAOVIEN'])): ?>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" 
                   data-bs-toggle="collapse" 
                   data-bs-target="#collapseTinNhan"
                   aria-expanded="false" 
                   aria-controls="collapseTinNhan">
                    <i class="fas fa-fw fa-comments"></i>
                    <span>Tin nhắn</span>
                    <i class="fas fa-fw fa-angle-right sidebar-arrow"></i>
                </a>
                <div id="collapseTinNhan" class="collapse" aria-labelledby="headingTinNhan" data-bs-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="index.php?controller=tinnhan&action=guitinnhan">Gửi tin nhắn mới</a>
                        <a class="collapse-item" href="index.php?controller=tinnhan&action=index">Danh sách tin nhắn</a>
                    </div>
                </div>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=quanlytaikhoan&action=index">
                    <i class="fas fa-users"></i>
                    <span>Quản lý tài khoản</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thoikhoabieu&action=taotkb">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Quản lý thời khóa biểu</span>
                </a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thoikhoabieu&action=quanlytkb">
                    <i class="fas fa-list-alt"></i>
                    <span>Quản lý TKB</span>
                </a>
            </li> -->
            <!-- <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-school"></i>
                    <span>Quản lý lớp học</span>
                </a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=tuyensinh&action=danhsachhoso">
                    <i class="fas fa-user-graduate"></i>
                    <span>Quản lý tuyển sinh</span>
                </a>
            </li>
        </ul>
    </div>
</nav>