<?php
require_once __DIR__ . '/../layouts/header.php';

// Xác định sidebar theo vai trò
$roleSidebar = '';
$roleName = '';
if (isset($_SESSION['user']['vaiTro'])) {
    switch ($_SESSION['user']['vaiTro']) {
        case 'BGH':
            $roleSidebar = 'bangiamhieu.php';
            $roleName = 'Ban Giám Hiệu';
            break;
        case 'GIAOVIEN':
            $roleSidebar = 'giaovien.php';
            $roleName = 'Giáo Viên';
            break;
        case 'HOCSINH':
            $roleSidebar = 'hocsinh.php';
            $roleName = 'Học Sinh';
            break;
        case 'PHUHUYNH':
            $roleSidebar = 'phuhuynh.php';
            $roleName = 'Phụ Huynh';
            break;
        case 'QTV':
            $roleSidebar = 'admin.php';
            $roleName = 'Quản Trị Viên';
            break;
        case 'TOTRUONG':
            $roleSidebar = 'totruong.php';
            $roleName = 'Tổ Trưởng Chuyên Môn';
            break;
    }
}
if ($roleSidebar) {
    require_once __DIR__ . '/../layouts/sidebar/' . $roleSidebar;
}
?>

<div class="main-content" style="margin-left: 250px; padding: 20px;">
    <div class="profile-header">
        <h2><i class="fas fa-user-circle"></i> Thông tin cá nhân - <?php echo $roleName; ?></h2>
    </div>

    <?php if (isset($userInfo) && $userInfo): ?>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-avatar-section">
                <div class="avatar-container">
                    <img src="<?php echo isset($userInfo['avatar']) && $userInfo['avatar'] ? $userInfo['avatar'] : '../assets/images/default-avatar.png'; ?>" 
                         alt="Avatar" class="profile-avatar" id="profileAvatar">
                    <div class="avatar-overlay">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <h3 class="profile-name"><?php echo htmlspecialchars($userInfo['hoTen']); ?></h3>
                <p class="profile-role"><?php echo $roleName; ?></p>
                <p class="profile-status">
                    <span class="status-badge <?php echo ($userInfo['trangThaiTaiKhoan'] == 'HOAT_DONG') ? 'active' : 'inactive'; ?>">
                        <?php echo ($userInfo['trangThaiTaiKhoan'] == 'HOAT_DONG') ? 'Đang hoạt động' : 'Ngừng hoạt động'; ?>
                    </span>
                </p>
            </div>

            <div class="profile-info-section">
                <div class="info-tabs">
                    <button class="tab-button active" onclick="openTab('basic')">Thông tin cơ bản</button>
                    <button class="tab-button" onclick="openTab('account')">Tài khoản</button>
                    <?php if ($_SESSION['user']['vaiTro'] == 'HOCSINH' || $_SESSION['user']['vaiTro'] == 'GIAOVIEN' || $_SESSION['user']['vaiTro'] == 'TOTRUONG'): ?>
                    <button class="tab-button" onclick="openTab('academic')">Thông tin học vụ</button>
                    <?php endif; ?>
                </div>

                <div class="tab-content">
                    <!-- Tab Thông tin cơ bản -->
                    <div id="basic" class="tab-pane active">
                        <div class="info-grid">
                            <div class="info-item">
                                <label><i class="fas fa-id-card"></i> Họ và tên:</label>
                                <span><?php echo htmlspecialchars($userInfo['hoTen']); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-birthday-cake"></i> Ngày sinh:</label>
                                <span><?php echo date('d/m/Y', strtotime($userInfo['ngaySinh'])); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-venus-mars"></i> Giới tính:</label>
                                <span>
                                    <?php 
                                    switch($userInfo['gioiTinh']) {
                                        case 'NAM': echo 'Nam'; break;
                                        case 'NU': echo 'Nữ'; break;
                                        default: echo 'Khác';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-envelope"></i> Email:</label>
                                <span><?php echo htmlspecialchars($userInfo['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-phone"></i> Số điện thoại:</label>
                                <span><?php echo htmlspecialchars($userInfo['soDienThoai']); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-map-marker-alt"></i> Địa chỉ:</label>
                                <span><?php echo htmlspecialchars($userInfo['diaChi']); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-id-card"></i> CCCD/CMND:</label>
                                <span><?php echo htmlspecialchars($userInfo['CCCD']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Tài khoản -->
                    <div id="account" class="tab-pane">
                        <div class="info-grid">
                            <div class="info-item">
                                <label><i class="fas fa-user"></i> Tên đăng nhập:</label>
                                <span><?php echo htmlspecialchars($userInfo['tenDangNhap']); ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-shield-alt"></i> Vai trò:</label>
                                <span class="role-badge"><?php echo $roleName; ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-circle"></i> Trạng thái:</label>
                                <span class="status-badge <?php echo ($userInfo['trangThaiTaiKhoan'] == 'HOAT_DONG') ? 'active' : 'inactive'; ?>">
                                    <?php echo ($userInfo['trangThaiTaiKhoan'] == 'HOAT_DONG') ? 'Đang hoạt động' : 'Ngừng hoạt động'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Thông tin học vụ -->
                    <?php if ($_SESSION['user']['vaiTro'] == 'HOCSINH'): ?>
                    <div id="academic" class="tab-pane">
                        <div class="info-grid">
                            <div class="info-item">
                                <label><i class="fas fa-graduation-cap"></i> Mã học sinh:</label>
                                <span><?php echo $userInfo['maHocSinh'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-school"></i> Lớp:</label>
                                <span><?php echo $userInfo['tenLop'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-layer-group"></i> Khối:</label>
                                <span><?php echo $userInfo['tenKhoi'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-calendar-day"></i> Ngày nhập học:</label>
                                <span><?php echo isset($userInfo['ngayNhapHoc']) ? date('d/m/Y', strtotime($userInfo['ngayNhapHoc'])) : 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-users"></i> Phụ huynh:</label>
                                <span><?php echo $userInfo['tenPhuHuynh'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-info-circle"></i> Trạng thái học tập:</label>
                                <span class="status-badge <?php echo ($userInfo['trangThaiHocSinh'] == 'DANG_HOC') ? 'active' : 'inactive'; ?>">
                                    <?php 
                                    switch($userInfo['trangThaiHocSinh']) {
                                        case 'DANG_HOC': echo 'Đang học'; break;
                                        case 'DA_TOT_NGHIEP': echo 'Đã tốt nghiệp'; break;
                                        case 'CHUYEN_TRUONG': echo 'Chuyển trường'; break;
                                        case 'THOI_HOC': echo 'Thôi học'; break;
                                        default: echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($_SESSION['user']['vaiTro'] == 'TOTRUONG'): ?>
                        <div id="academic" class="tab-pane">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label><i class="fas fa-users-cog"></i> Mã tổ trưởng:</label>
                                    <span><?php echo $userInfo['maToTruong'] ?? 'N/A'; ?></span>
                                </div>
                                <div class="info-item">
                                    <label><i class="fas fa-user-tie"></i> Tổ chuyên môn:</label>
                                    <span><?php echo $userInfo['toChuyenMon'] ?? 'N/A'; ?></span>
                                </div>
                                <div class="info-item">
                                    <label><i class="fas fa-book"></i> Môn học phụ trách:</label>
                                    <span><?php echo $userInfo['tenMonHoc'] ?? 'N/A'; ?></span>
                                </div>
                                <div class="info-item">
                                    <label><i class="fas fa-chalkboard-teacher"></i> Số lượng giáo viên:</label>
                                    <span><?php echo $userInfo['soLuongGiaoVien'] ?? '0'; ?> giáo viên</span>
                                </div>
                            </div>

                            <?php if (isset($danhSachGiaoVien) && !empty($danhSachGiaoVien)): ?>
                            <div class="team-section" style="margin-top: 30px;">
                                <h4><i class="fas fa-users"></i> Danh sách giáo viên trong tổ</h4>
                                <div class="team-grid">
                                    <?php foreach ($danhSachGiaoVien as $giaoVien): ?>
                                    <div class="team-member">
                                        <div class="member-avatar">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div class="member-info">
                                            <h5><?php echo htmlspecialchars($giaoVien['hoTen']); ?></h5>
                                            <p class="member-subject"><?php echo $giaoVien['tenMonHoc'] ?? 'N/A'; ?></p>
                                            <p class="member-type">
                                                <?php 
                                                switch($giaoVien['loaiGiaoVien']) {
                                                    case 'GV_CHU_NHIEM': echo 'GV Chủ nhiệm'; break;
                                                    case 'GV_BO_MON': echo 'GV Bộ môn'; break;
                                                    default: echo 'N/A';
                                                }
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                    <?php if ($_SESSION['user']['vaiTro'] == 'GIAOVIEN'): ?>
                    <div id="academic" class="tab-pane">
                        <div class="info-grid">
                            <div class="info-item">
                                <label><i class="fas fa-chalkboard-teacher"></i> Mã giáo viên:</label>
                                <span><?php echo $userInfo['maGiaoVien'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-book"></i> Chuyên môn:</label>
                                <span><?php echo $userInfo['chuyenMon'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-user-tag"></i> Loại giáo viên:</label>
                                <span>
                                    <?php 
                                    switch($userInfo['loaiGiaoVien']) {
                                        case 'GV_CHU_NHIEM': echo 'Giáo viên chủ nhiệm'; break;
                                        case 'GV_BO_MON': echo 'Giáo viên bộ môn'; break;
                                        default: echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-book-open"></i> Môn học:</label>
                                <span><?php echo $userInfo['tenMonHoc'] ?? 'N/A'; ?></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-users-cog"></i> Tổ chuyên môn:</label>
                                <span><?php echo $userInfo['toChuyenMon'] ?? 'N/A'; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> Không thể tải thông tin người dùng.
    </div>
    <?php endif; ?>
</div>

<style>
.profile-container {
    max-width: 1000px;
    margin: 0 auto;
}

.profile-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    overflow: hidden;
}

.profile-avatar-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 20px;
    text-align: center;
    position: relative;
}

.avatar-container {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.3);
    object-fit: cover;
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
    cursor: pointer;
}

.avatar-container:hover .avatar-overlay {
    opacity: 1;
}

.profile-name {
    font-size: 24px;
    font-weight: 600;
    margin: 10px 0 5px 0;
}

.profile-role {
    font-size: 16px;
    opacity: 0.9;
    margin-bottom: 10px;
}

.profile-info-section {
    padding: 30px;
}

.info-tabs {
    display: flex;
    border-bottom: 2px solid #e9ecef;
    margin-bottom: 25px;
}

.tab-button {
    background: none;
    border: none;
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.3s;
}

.tab-button.active {
    color: #667eea;
    border-bottom-color: #667eea;
}

.tab-button:hover {
    color: #667eea;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.info-item label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    font-size: 14px;
}

.info-item label i {
    width: 20px;
    margin-right: 8px;
    color: #667eea;
}

.info-item span {
    color: #212529;
    font-size: 15px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.role-badge {
    background: #e9ecef;
    color: #495057;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin: 20px 0;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.team-section {
    border-top: 2px solid #e9ecef;
    padding-top: 20px;
}

.team-section h4 {
    color: #495057;
    margin-bottom: 20px;
    font-size: 18px;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
}

.team-member {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.team-member:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.member-avatar {
    width: 60px;
    height: 60px;
    background: #667eea;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    color: white;
    font-size: 24px;
}

.member-info h5 {
    margin: 0 0 5px 0;
    color: #495057;
    font-size: 14px;
}

.member-subject {
    color: #667eea;
    font-size: 12px;
    margin: 0 0 5px 0;
    font-weight: 500;
}

.member-type {
    color: #6c757d;
    font-size: 11px;
    margin: 0;
    background: #f8f9fa;
    padding: 2px 8px;
    border-radius: 10px;
    display: inline-block;
}
</style>

<script>
function openTab(tabName) {
    // Ẩn tất cả các tab pane
    var tabPanes = document.getElementsByClassName('tab-pane');
    for (var i = 0; i < tabPanes.length; i++) {
        tabPanes[i].classList.remove('active');
    }
    
    // Xóa active class từ tất cả các tab button
    var tabButtons = document.getElementsByClassName('tab-button');
    for (var i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove('active');
    }
    
    // Hiển thị tab được chọn và thêm active class
    document.getElementById(tabName).classList.add('active');
    event.currentTarget.classList.add('active');
}

// Xử lý upload avatar
document.getElementById('profileAvatar').addEventListener('click', function() {
    // Code xử lý upload avatar sẽ được thêm sau
    alert('Tính năng upload avatar sẽ được cập nhận sau!');
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php'; 
?>