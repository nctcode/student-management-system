<?PHP
    require_once 'views/layouts/header.php';
    require_once 'views/layouts/sidebar/admin.php';
    require_once 'views/layouts/footer.php';
?>
<style>
/* Vùng chứa chính */
.taikhoan-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    padding: 30px;
    max-width: 1000px;
    margin: 40px auto;
    font-family: "Segoe UI", sans-serif;
}

/* Tiêu đề */
.taikhoan-container h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 25px;
}

/* Nút thêm mới */
.taikhoan-container .btn-primary {
    display: inline-block;
    background-color: #007bff;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    margin-bottom: 15px;
    transition: 0.2s;
}
.taikhoan-container .btn-primary:hover {
    background-color: #0056b3;
}

/* Form tìm kiếm */
.search-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

.search-form .form-group {
    margin-bottom: 15px;
}

.search-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
    color: #495057;
}

.search-form input[type="text"] {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
}

.search-form button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.search-form button:hover {
    background-color: #218838;
}

.search-form .btn-reset {
    background-color: #6c757d;
    margin-left: 10px;
}

.search-form .btn-reset:hover {
    background-color: #545b62;
}

/* Bảng */
.taikhoan-container table {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
}

.taikhoan-container th {
    background-color: #007bff;
    color: white;
    text-align: left;
    padding: 10px;
}

.taikhoan-container td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.taikhoan-container tr:hover {
    background-color: #f2f6ff;
}

/* Cột hành động */
.taikhoan-container td a {
    text-decoration: none;
    margin-right: 8px;
    font-weight: 500;
}

.taikhoan-container a.edit {
    color: #28a745;
}
.taikhoan-container a.delete {
    color: #dc3545;
}
.taikhoan-container a.toggle {
    color: #ffc107;
}
.taikhoan-container a.edit:hover {
    text-decoration: underline;
    color: #1f7a33;
}
.taikhoan-container a.delete:hover {
    text-decoration: underline;
    color: #b02a37;
}
.taikhoan-container a.toggle:hover {
    text-decoration: underline;
    color: #c69500;
}

/* Trạng thái */
.status-active {
    color: #28a745;
    font-weight: 600;
}
.status-locked {
    color: #dc3545;
    font-weight: 600;
}

/* Không có kết quả */
.no-results {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-style: italic;
}
</style>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hiển thị thông báo
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #c3e6cb;">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #f5c6cb;">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="taikhoan-container">
    <h2>Quản lý tài khoản</h2>
    
    <!-- Form tìm kiếm -->
    <div class="search-form">
        <form method="GET" action="index.php">
            <input type="hidden" name="controller" value="QuanLyTaiKhoan">
            <input type="hidden" name="action" value="index">
            
            <div class="form-group">
                <label for="search_id">Tìm theo ID:</label>
                <input type="text" id="search_id" name="search_id" 
                       value="<?php echo htmlspecialchars($_GET['search_id'] ?? ''); ?>" 
                       placeholder="Nhập ID tài khoản">
            </div>
            
            <div class="form-group">
                <label for="search_username">Tìm theo tên đăng nhập:</label>
                <input type="text" id="search_username" name="search_username" 
                       value="<?php echo htmlspecialchars($_GET['search_username'] ?? ''); ?>" 
                       placeholder="Nhập tên đăng nhập">
            </div>
            
            <div>
                <button type="submit">Tìm kiếm</button>
                <a href="index.php?controller=QuanLyTaiKhoan&action=index" class="btn-reset" style="color: white; text-decoration: none; padding: 8px 16px; background: #6c757d; border-radius: 4px; display: inline-block;">Reset</a>
            </div>
        </form>
    </div>

    <a href="index.php?controller=QuanLyTaiKhoan&action=create" class="btn-primary">+ Thêm tài khoản</a>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Tên đăng nhập</th>
            <th>Họ tên</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        <?php if (empty($accounts)): ?>
            <tr>
                <td colspan="6" class="no-results">Không tìm thấy tài khoản nào</td>
            </tr>
        <?php else: ?>
            <?php foreach ($accounts as $acc): ?>
                <tr>
                    <td><?= $acc['maTaiKhoan'] ?></td>
                    <td><?= htmlspecialchars($acc['tenDangNhap']) ?></td>
                    <td><?= htmlspecialchars($acc['hoTen'] ?? '') ?></td>
                    <td><?= htmlspecialchars($acc['loaiNguoiDung']) ?></td>
                    <td class="<?= $acc['trangThai'] === 'HOAT_DONG' ? 'status-active' : 'status-locked' ?>">
                     <?= $acc['trangThai'] === 'HOAT_DONG' ? 'Hoạt động' : 'Đã khóa' ?>
                    </td>
                    <td>
                        <a href="index.php?controller=QuanLyTaiKhoan&action=edit&id=<?= $acc['maTaiKhoan'] ?>" class="edit">Sửa</a>
                        <a href="index.php?controller=QuanLyTaiKhoan&action=delete&id=<?= $acc['maTaiKhoan'] ?>" class="delete" onclick="return confirm('Xóa tài khoản này?')">Xóa</a>
                        <a href="index.php?controller=QuanLyTaiKhoan&action=toggleStatus&id=<?= $acc['maTaiKhoan'] ?>" class="toggle">
                            <?= $acc['trangThai'] === 'HOAT_DONG' ? 'Khóa' : 'Mở khóa' ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
     <!-- THÊM NÚT QUAY LẠI TRANG CHÍNH -->
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php" class="btn-reset" style="color: white; text-decoration: none; padding: 8px 16px; background: #6c757d; border-radius: 4px; display: inline-block;">← Quay lại trang chính</a>
    </div>
</div>