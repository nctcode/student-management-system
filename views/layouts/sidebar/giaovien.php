<nav class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <h5>Menu Giáo Viên</h5>
        </div>
        <ul class="sidebar-menu">
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=home&action=teacher">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=ketquahoctap&action=thongke">
                    <i class="fas fa-history"></i>
                    <span>Thống kê kết quả học tập</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=thoikhoabieu&action=xemluoi">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Thời khóa biểu</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=diem&action=nhapdiem">
                    <i class="fas fa-fw fa-pen-to-square"></i>
                    <span>Nhập điểm</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=chuyencan&action=index">
                    <i class="fas fa-user-check"></i>
                    <span>Chuyên cần</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=hanhkiem&action=index">
                    <i class="fas fa-star"></i>
                    <span>Hạnh Kiểm</span>
                </a>
            </li>
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

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" 
                   data-bs-toggle="collapse" 
                   data-bs-target="#collapseBaiTap"
                   aria-expanded="false" 
                   aria-controls="collapseBaiTap">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Bài tập</span>
                    <i class="fas fa-fw fa-angle-right sidebar-arrow"></i>
                </a>
                <div id="collapseBaiTap" class="collapse" aria-labelledby="headingBaiTap" data-bs-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="index.php?controller=baitap&action=index">Giao bài tập</a>
                        <a class="collapse-item" href="index.php?controller=baitap&action=danhsach">Danh sách đã giao</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=danhsachlop&action=index">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Danh sách lớp</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=dethi&action=index">
                    <i class="fas fa-file-alt"></i> 
                    <span>Tạo đề thi</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?controller=thongbao&action=danhsach" class="nav-link">
                    <i class="nav-icon fas fa-bullhorn"></i>
                    <span>
                        Thông báo
                        <?php if (isset($soThongBaoChuaDoc) && $soThongBaoChuaDoc > 0): ?>
                        <span class="badge bg-danger float-right"><?php echo $soThongBaoChuaDoc; ?></span>
                        <?php endif; ?>
                    </span>
                </a>
            </li>
        </ul>
    </div>
</nav>