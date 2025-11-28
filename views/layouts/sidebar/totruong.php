<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <h5>Menu Tổ Trưởng Chuyên Môn</h5>
        </div>
        <ul class="sidebar-menu">
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=home&action=leader">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=duyetdethi&action=duyet">
                    <i class="fas fa-file-alt"></i>
                    <span>Duyệt đề thi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=dethi&action=phancong">
                    <i class="fas fa-user-edit"></i>
                    <span>Phân công giáo viên ra đề</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=thongbao&action=danhsach" class="nav-link">
                    <i class="nav-icon fas fa-bullhorn"></i>
                    <span>
                        Thông báo
                        <?php if ($soThongBaoChuaDoc > 0): ?>
                        <span class="badge bg-danger float-right"><?php echo $soThongBaoChuaDoc; ?></span>
                        <?php endif; ?>
                    </span>
                </a>
            </li>
        </ul>
    </div>
</nav>
