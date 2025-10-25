<?php
$title = "Tạo đơn chuyển trường";
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <a href="index.php?controller=donchuyenloptruong&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-import"></i> Đơn xin chuyển trường
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?controller=donchuyenloptruong&action=create">
                        <div class="mb-3">
                            <label class="form-label">Chọn học sinh <span class="text-danger">*</span></label>
                            <select name="maHocSinh" class="form-select" required>
                                <option value="">-- Chọn học sinh --</option>
                                <?php foreach ($hocSinh as $hs): ?>
                                <option value="<?php echo $hs['maHocSinh']; ?>" 
                                        <?php echo ($_POST['maHocSinh'] ?? '') == $hs['maHocSinh'] ? 'selected' : ''; ?>>
                                    <?php echo $hs['hoTen'] . ' - Lớp: ' . $hs['tenLop'] . ' - Khối: ' . $hs['tenKhoi']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Chỉ hiển thị học sinh đang học tại trường</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trường chuyển đến <span class="text-danger">*</span></label>
                            <select name="maTruongDen" class="form-select" required>
                                <option value="">-- Chọn trường --</option>
                                <?php foreach ($truong as $t): ?>
                                <?php if ($t['maTruong'] != 1): // Loại trừ trường hiện tại ?>
                                <option value="<?php echo $t['maTruong']; ?>" 
                                        <?php echo ($_POST['maTruongDen'] ?? '') == $t['maTruong'] ? 'selected' : ''; ?>>
                                    <?php echo $t['tenTruong'] . ' - ' . ($t['diaChi'] ?? ''); ?>
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lý do chuyển trường <span class="text-danger">*</span></label>
                            <textarea name="lyDoChuyen" class="form-control" rows="6" 
                                      placeholder="Trình bày lý do chuyển trường (gia đình chuyển chỗ ở, điều kiện học tập, lý do cá nhân...)" 
                                      required><?php echo $_POST['lyDoChuyen'] ?? ''; ?></textarea>
                            <small class="text-muted">Vui lòng trình bày rõ ràng và chi tiết lý do chuyển trường.</small>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Thông tin quan trọng:</h6>
                            <ul class="mb-0">
                                <li>Đơn chuyển trường cần được sự đồng ý của cả TRƯỜNG ĐI và TRƯỜNG ĐẾN</li>
                                <li>Thời gian xử lý đơn: 3-5 ngày làm việc</li>
                                <li>Bạn có thể theo dõi trạng thái đơn trong mục "Danh sách đơn"</li>
                                <li>Có thể hủy đơn nếu đơn chưa được duyệt</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?controller=donchuyenloptruong&action=index" 
                               class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Gửi đơn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>