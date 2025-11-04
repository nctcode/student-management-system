<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <h5>Menu Học Sinh</h5>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="index.php?controller=home&action=student" class="active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=thoikhoabieu&action=index">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Thời khóa biểu</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Xem điểm</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-book"></i>
                    <span>Bài tập</span>
                </a>
            </li>
            <li>
                <a href="index.php?controller=tinnhan&action=index">
                    <i class="fas fa-comments"></i>
                    <span>Tin nhắn</span>
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
                    <i class="fas fa-chart-line"></i>
                    <span>Kết quả học tập</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-bell"></i>
                    <span>Thông báo</span>
                </a>
            </li>
            
            <!-- THÊM MỤC ĐĂNG KÝ BAN HỌC CHỈ CHO KHỐI 11 -->
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