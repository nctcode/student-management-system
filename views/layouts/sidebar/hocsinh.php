<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <h5>Menu Học Sinh</h5>
        </div>
        <ul class="sidebar-menu">
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=home&action=student" class="active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thoikhoabieu&action=xemluoi">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Thời khóa biểu</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=diem&action=xemdiem">
                    <i class="fas fa-chart-line"></i>
                    <span>Kết quả học tập</span>
                </a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="index.php?controller=diem&action=xemdiem">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Xem điểm</span>
                </a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=baitap&action=danhsach_hs">
                    <i class="fas fa-book"></i>
                    <span>Bài tập</span>
                </a>
            </li>
            <?php if (in_array($_SESSION['user']['vaiTro'], ['QTV', 'BGH', 'GIAOVIEN', 'HOCSINH', 'PHUHUYNH'])): ?>
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
                        <a class="collapse-item" href="index.php?controller=tinnhan&action=guitinnhangiaovien">Gửi tin nhắn mới</a>
                        <a class="collapse-item" href="index.php?controller=tinnhan&action=index">Danh sách tin nhắn</a>
                    </div>
                </div>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=hocphi&action=index">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Học phí</span>
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
            <!-- <li class="nav-item">
                <a class="nav-link" href="index.php?controller=donchuyenloptruong&action=index">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Đơn chuyển trường</span>
                </a>
            </li> -->
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
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=dangkybanhoc&action=index">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Đăng ký ban học</span>
                </a>
            </li>
            <?php endif; ?>
            
        </ul>
    </div>
</nav>