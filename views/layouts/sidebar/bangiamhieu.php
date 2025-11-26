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
                <a class="nav-link" href="index.php?controller=donchuyenloptruong&action=index">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Đơn chuyển lớp/trường</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thongke&action=index">
                    <i class="fas fa-chart-bar"></i>
                    <span>Báo cáo thống kê</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=PhanCongGVBMCN&action=index">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Phân công giáo viên</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=thongbao&action=danhsach" class="nav-link">
                    <i class="nav-icon fas fa-list"></i>
                    <span>Danh sách thông báo</span>
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