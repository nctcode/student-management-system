<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <h5>Menu Học Sinh</h5>
        </div>
        <ul class="sidebar-menu">
            <li class="nav-item" class="nav-item">
                <a class="nav-link" href="index.php?controller=home&action=student" class="active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thoikhoabieu&action=xemluoi">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Xem thời khóa biểu</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Xem điểm</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-book"></i>
                    <span>Bài tập</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=tinnhan&action=index">
                    <i class="fas fa-comments"></i>
                    <span>Tin nhắn</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=hocphi&action=index">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Học phí</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-line"></i>
                    <span>Kết quả học tập</span>
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
            
            <!-- THÊM MỤC ĐĂNG KÝ BAN HỌC CHỈ CHO KHỐI 11 -->
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=donchuyenloptruong&action=index">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Đơn chuyển trường</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=tuyensinh&action=hosocuatoi">
                    <i class="fas fa-user-graduate"></i>
                    <span>Hồ sơ tuyển sinh</span>
                </a>
            </li>
            <?php 
            // Kiểm tra nếu là học sinh và có thông tin khối
            if (isset($_SESSION['user']) && 
                $_SESSION['user']['vaiTro'] == 'HOCSINH' && 
                isset($_SESSION['user']['khoi']) && 
                $_SESSION['user']['khoi'] == 11): 
            ?>
            <li>
                <a href="index.php?controller=dangkybanhoc&action=index">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Đăng ký ban học</span>
                    <span class="badge bg-warning ms-2">Mới</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>