<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/hocsinh.php';

if (!isset($_SESSION['success']) || $_SESSION['success'] !== "Đăng ký ban học thành công!") {
    header('Location: index.php?controller=home&action=student');
    exit;
}

require_once 'models/Database.php';
$db = new Database();
$conn = $db->getConnection();
$maHocSinh = $_SESSION['user']['maHocSinh'];
$sql = "SELECT dk.*, bh.tenBan FROM dangkybanhoc dk JOIN banhoc bh ON dk.maBan = bh.maBan WHERE dk.maHocSinh = ? ORDER BY dk.ngayDangKy DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([$maHocSinh]);
$thongTinDangKy = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="assets/css/dang_ky_ban.css">

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4><i class="fas fa-check-circle me-2"></i>Đăng ký Thành công</h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4"><i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i></div>
                    <h3 class="text-success mb-3">Đăng ký Ban học Thành công!</h3>
                    <p class="lead mb-4">Bạn đã đăng ký ban học thành công. Thông tin đăng ký đã được lưu vào hệ thống.</p>
                    
                    <div class="alert alert-success text-start mx-auto" style="max-width: 500px;">
                        <h6><i class="fas fa-info-circle me-2"></i>Thông tin đăng ký:</h6>
                        <p class="mb-1"><strong>Học sinh:</strong> <?php echo htmlspecialchars($_SESSION['user']['hoTen']); ?></p>
                        <p class="mb-1"><strong>Lớp:</strong> <?php echo htmlspecialchars($_SESSION['user']['tenLop'] ?? 'N/A'); ?></p>
                        <p class="mb-1"><strong>Khối:</strong> <?php echo htmlspecialchars($_SESSION['user']['khoi'] ?? 'N/A'); ?></p>
                        <?php if ($thongTinDangKy): ?>
                        <p class="mb-1"><strong>Ban học đã đăng ký:</strong> <?php echo htmlspecialchars($thongTinDangKy['tenBan']); ?></p>
                        <p class="mb-1"><strong>Mã đăng ký:</strong> #<?php echo htmlspecialchars($thongTinDangKy['id']); ?></p>
                        <p class="mb-0"><strong>Thời gian đăng ký:</strong> <?php echo date('d/m/Y H:i', strtotime($thongTinDangKy['ngayDangKy'])); ?></p>
                        <?php else: ?>
                        <p class="mb-0 text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Đang tải thông tin đăng ký...</p>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4">
                        <a href="index.php?controller=home&action=student" class="btn btn-primary me-2 mb-2"><i class="fas fa-home me-2"></i>Về Trang chủ</a>
                        <a href="index.php?controller=dangkybanhoc&action=index" class="btn btn-outline-primary me-2 mb-2"><i class="fas fa-eye me-2"></i>Xem Chi tiết</a>
                        <button type="button" class="btn btn-outline-success mb-2" onclick="window.print()"><i class="fas fa-print me-2"></i>In Xác nhận</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/dang_ky_ban.js"></script>

<?php 
unset($_SESSION['success']);
require_once 'views/layouts/footer.php'; 
?>