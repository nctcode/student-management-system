<?php
$title = "Gửi đơn chuyển lớp/trường";
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container">
    <div class="header">
        <h2>GỬI ĐƠN CHUYỂN LỚP / TRƯỜNG</h2>
    </div>

    <div class="card">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="post" action="index.php?controller=donchuyenloptruong&action=xulyguidon">
            <input type="hidden" name="maHocSinh" value="<?= $hocSinhInfo['maHocSinh'] ?? '' ?>">
            <input type="hidden" name="maTruongHienTai" value="<?= $hocSinhInfo['maTruong'] ?? '' ?>">
            <input type="hidden" name="maLopHienTai" value="<?= $hocSinhInfo['maLop'] ?? '' ?>">

            <div class="form-group">
                <label>Học sinh:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinhInfo['tenHocSinh'] ?? '') ?>" readonly>
            </div>

            <div class="form-group">
                <label>Lớp hiện tại:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinhInfo['tenLop'] ?? '') ?>" readonly>
            </div>

            <div class="form-group">
                <label>Trường hiện tại:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinhInfo['tenTruong'] ?? '') ?>" readonly>
            </div>

            <div class="form-group">
                <label>Loại đơn *</label>
                <select name="loaiDon" id="loaiDon" class="form-control" required>
                    <option value="">-- Chọn loại đơn --</option>
                    <option value="chuyen_lop">Chuyển lớp</option>
                    <option value="chuyen_truong">Chuyển trường</option>
                </select>
            </div>

            <!-- Chuyển lớp -->
            <div id="chuyenLopSection" style="display: none;">
                <div class="form-group">
                    <label>Lớp muốn chuyển đến *</label>
                    <select name="maLopDen" class="form-control">
                        <option value="">-- Chọn lớp --</option>
                        <?php foreach ($danhSachLop as $lop): ?>
                            <?php if ($lop['maLop'] != $hocSinhInfo['maLop']): ?>
                                <option value="<?= $lop['maLop'] ?>"><?= htmlspecialchars($lop['tenLop']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Chuyển trường -->
            <div id="chuyenTruongSection" style="display: none;">
                <div class="form-group">
                    <label>Trường muốn chuyển đến *</label>
                    <select name="maTruongDen" class="form-control">
                        <option value="">-- Chọn trường --</option>
                        <?php foreach ($danhSachTruong as $truong): ?>
                            <?php if ($truong['maTruong'] != $hocSinhInfo['maTruong']): ?>
                                <option value="<?= $truong['maTruong'] ?>"><?= htmlspecialchars($truong['tenTruong']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Lý do chuyển *</label>
                <textarea name="lyDoChuyen" class="form-control" rows="4" placeholder="Nhập lý do chuyển lớp/trường..." required></textarea>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">Gửi đơn</button>
                <a href="index.php?controller=home&action=parent" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('loaiDon').addEventListener('change', function() {
    const loaiDon = this.value;
    document.getElementById('chuyenLopSection').style.display = loaiDon === 'chuyen_lop' ? 'block' : 'none';
    document.getElementById('chuyenTruongSection').style.display = loaiDon === 'chuyen_truong' ? 'block' : 'none';
    
    // Reset required fields
    document.querySelector('[name="maLopDen"]').required = loaiDon === 'chuyen_lop';
    document.querySelector('[name="maTruongDen"]').required = loaiDon === 'chuyen_truong';
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>