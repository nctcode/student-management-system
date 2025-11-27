<?PHP
    require_once 'views/layouts/header.php';
    require_once 'views/layouts/sidebar/admin.php';
?>

<!-- THÊM THẺ MAIN BAO QUANH NỘI DUNG -->
<main class="content-area">

<style>
/* RESET TRIỆT ĐỂ */
body, html {
    margin: 0 !important;
    padding: 0 !important;
}

/* Content area */
.content-area {
    margin-left: 280px;
    padding: 20px;
    background: #f4f6fb;
    min-height: 100vh;
}

/* Vùng chứa chính - ĐÃ SỬA: bỏ margin-left */
.taikhoan-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 25px;
    margin: 0;
    font-family: "Segoe UI", sans-serif;
}

/* Alert messages - ĐÃ SỬA: bỏ margin-left */
.alert {
    margin: 0 0 20px 0;
    border-radius: 8px;
    padding: 15px 20px;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .content-area {
        margin-left: 0;
        padding: 10px;
    }
}

/* PHẦN CSS CÒN LẠI GIỮ NGUYÊN */
.taikhoan-container h2 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: 600;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.taikhoan-container .btn-primary {
    display: inline-block;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    margin-bottom: 20px;
    border: none;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
}
.taikhoan-container .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,123,255,0.4);
}

.search-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    border: 1px solid #e9ecef;
}

.search-form .form-group {
    margin-bottom: 15px;
}

.search-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #495057;
    font-size: 14px;
}

.search-form input[type="text"] {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.search-form input[type="text"]:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.search-form button {
    background: linear-gradient(135deg, #28a745, #218838);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-right: 10px;
}

.search-form button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(40,167,69,0.3);
}

.search-form .btn-reset {
    background: linear-gradient(135deg, #6c757d, #545b62);
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s ease;
}

.search-form .btn-reset:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(108,117,125,0.3);
    color: white;
}

.taikhoan-container table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.taikhoan-container th {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    text-align: left;
    padding: 12px 15px;
    font-weight: 600;
    font-size: 14px;
}

.taikhoan-container td {
    padding: 12px 15px;
    border-bottom: 1px solid #e9ecef;
    color: #495057;
}

.taikhoan-container tr:hover {
    background-color: #f8f9fa;
}

.taikhoan-container tr:last-child td {
    border-bottom: none;
}

.taikhoan-container td a {
    text-decoration: none;
    margin-right: 12px;
    font-weight: 500;
    font-size: 13px;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.taikhoan-container a.edit {
    color: #28a745;
    background: rgba(40,167,69,0.1);
}
.taikhoan-container a.delete {
    color: #dc3545;
    background: rgba(220,53,69,0.1);
}
.taikhoan-container a.toggle {
    color: #ffc107;
    background: rgba(255,193,7,0.1);
}

.taikhoan-container a.edit:hover {
    background: #28a745;
    color: white;
}
.taikhoan-container a.delete:hover {
    background: #dc3545;
    color: white;
}
.taikhoan-container a.toggle:hover {
    background: #ffc107;
    color: #212529;
}

.status-active {
    color: #28a745;
    font-weight: 600;
    background: rgba(40,167,69,0.1);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
}
.status-locked {
    color: #dc3545;
    font-weight: 600;
    background: rgba(220,53,69,0.1);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
}

.no-results {
    text-align: center;
    padding: 40px;
    color: #6c757d;
    font-style: italic;
    background: #f8f9fa;
    border-radius: 8px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.taikhoan-container .btn-back {
    background: linear-gradient(135deg, #6c757d, #545b62);
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    margin-top: 20px;
    transition: all 0.3s ease;
    border: none;
    font-weight: 500;
}

.taikhoan-container .btn-back:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(108,117,125,0.3);
    color: white;
}
</style>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hiển thị thông báo
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="taikhoan-container">
    <h2>📊 Quản lý tài khoản</h2>
    
    <!-- Form tìm kiếm -->
    <div class="search-form">
        <form method="GET" action="index.php">
            <input type="hidden" name="controller" value="QuanLyTaiKhoan">
            <input type="hidden" name="action" value="index">
            
            <div class="form-group">
                <label for="search_id">🔍 Tìm theo ID:</label>
                <input type="text" id="search_id" name="search_id" 
                       value="<?php echo htmlspecialchars($_GET['search_id'] ?? ''); ?>" 
                       placeholder="Nhập ID tài khoản">
            </div>
            
            <div class="form-group">
                <label for="search_username">👤 Tìm theo tên đăng nhập:</label>
                <input type="text" id="search_username" name="search_username" 
                       value="<?php echo htmlspecialchars($_GET['search_username'] ?? ''); ?>" 
                       placeholder="Nhập tên đăng nhập">
            </div>
            
            <div>
                <button type="submit">🔍 Tìm kiếm</button>
                <a href="index.php?controller=QuanLyTaiKhoan&action=index" class="btn-reset">🔄 Reset</a>
            </div>
        </form>
    </div>

    <a href="index.php?controller=QuanLyTaiKhoan&action=create" class="btn-primary">➕ Thêm tài khoản</a>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Họ tên</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($accounts)): ?>
                <tr>
                    <td colspan="6" class="no-results">📭 Không tìm thấy tài khoản nào</td>
                </tr>
            <?php else: ?>
                <?php foreach ($accounts as $acc): ?>
                    <tr>
                        <td><?= $acc['maTaiKhoan'] ?></td>
                        <td><?= htmlspecialchars($acc['tenDangNhap']) ?></td>
                        <td><?= htmlspecialchars($acc['hoTen'] ?? '') ?></td>
                        <td><?= htmlspecialchars($acc['loaiNguoiDung']) ?></td>
                        <td>
                            <span class="<?= $acc['trangThai'] === 'HOAT_DONG' ? 'status-active' : 'status-locked' ?>">
                                <?= $acc['trangThai'] === 'HOAT_DONG' ? '✅ Hoạt động' : '❌ Đã khóa' ?>
                            </span>
                        </td>
                        <td>
                            <a href="index.php?controller=QuanLyTaiKhoan&action=edit&id=<?= $acc['maTaiKhoan'] ?>" class="edit">✏️ Sửa</a>
                            <a href="index.php?controller=QuanLyTaiKhoan&action=delete&id=<?= $acc['maTaiKhoan'] ?>" class="delete" onclick="return confirm('Bạn có chắc muốn xóa tài khoản này?')">🗑️ Xóa</a>
                            <a href="index.php?controller=QuanLyTaiKhoan&action=toggleStatus&id=<?= $acc['maTaiKhoan'] ?>" class="toggle">
                                <?= $acc['trangThai'] === 'HOAT_DONG' ? '🔒 Khóa' : '🔓 Mở khóa' ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Nút quay lại -->
    <div style="text-align: center; margin-top: 25px;">
        <a href="index.php" class="btn-back">← Quay lại trang chính</a>
    </div>
</div>

</main> <!-- ĐÓNG THẺ MAIN -->

<?php
    require_once 'views/layouts/footer.php';
?>