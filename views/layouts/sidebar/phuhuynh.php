<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <h5>Menu Phụ Huynh</h5>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="index.php?controller=home&action=parent" class="active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-clipboard-list me-2"></i>
                    <span>Kết quả học tập</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thoikhoabieu&action=xemluoi">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Thời khóa biểu</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=diem&action=xemdiem">
                    <i class="fas fa-chart-bar me-2"></i>
                    <span>Xem điểm</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-calendar-check"></i>
                    <span>Chuyên cần</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=hocphi&action=index">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Học phí</span>
                </a>
            </li>
            <!-- Sửa lại link cho đúng -->
            <li>
                <a href="index.php?controller=donchuyenloptruong&action=guidon">
                    <i class="fas fa-file-alt"></i>
                    <span>Gửi đơn chuyển</span>
                </a>
            </li>
            <!-- Thêm vào sidebar phụ huynh -->
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=donchuyenloptruong&action=danhsachdoncuatoi">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Đơn chuyển lớp/trường</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=tinnhan&action=index">
                    <i class="fas fa-comments"></i>
                    <span>Tin nhắn GV</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-book"></i>
                    <span>Bài tập về nhà</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=thongbao&action=danhsach" class="nav-link">
                    <i class="nav-icon fas fa-bullhorn"></i>
                    <p>
                        Thông báo
                        <?php if ($soThongBaoChuaDoc > 0): ?>
                        <span class="badge bg-danger float-right"><?php echo $soThongBaoChuaDoc; ?></span>
                        <?php endif; ?>
                    </p>
                </a>
            </li>
            <li>
                <a href="index.php?controller=tuyensinh&action=hosocuatoi">
                    <i class="fas fa-user-graduate"></i>
                    <span>Hồ sơ tuyển sinh</span>
                </a>
            </li>
        </ul>
    </div>
</nav>