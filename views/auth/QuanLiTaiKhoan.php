<?PHP
    require_once 'views/layouts/header.php';
    require_once 'views/layouts/sidebar/admin.php';
?>

<!-- Content area với margin-left để tránh sidebar -->
<div class="content-wrapper">

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hiển thị thông báo
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            ' . $_SESSION['success'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            ' . $_SESSION['error'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['error']);
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-users-cog me-2"></i>Quản lý tài khoản
                        </h3>
                        <a href="index.php?controller=QuanLyTaiKhoan&action=create" class="btn btn-light">
                            <i class="fas fa-plus-circle me-1"></i>Thêm tài khoản
                        </a>
                    </div>
                </div>
                
                <!-- Form tìm kiếm -->
                <div class="card-body border-bottom">
                    <form method="GET" action="index.php" class="row g-3">
                        <input type="hidden" name="controller" value="QuanLyTaiKhoan">
                        <input type="hidden" name="action" value="index">
                        
                        <div class="col-md-4">
                            <label for="search_id" class="form-label">
                                <i class="fas fa-id-card me-1"></i>Tìm theo ID
                            </label>
                            <input type="text" class="form-control" id="search_id" name="search_id" 
                                   value="<?php echo htmlspecialchars($_GET['search_id'] ?? ''); ?>" 
                                   placeholder="Nhập ID tài khoản">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="search_username" class="form-label">
                                <i class="fas fa-user me-1"></i>Tìm theo tên đăng nhập
                            </label>
                            <input type="text" class="form-control" id="search_username" name="search_username" 
                                   value="<?php echo htmlspecialchars($_GET['search_username'] ?? ''); ?>" 
                                   placeholder="Nhập tên đăng nhập">
                        </div>
                        
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Tìm kiếm
                            </button>
                            <a href="index.php?controller=QuanLyTaiKhoan&action=index" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Bảng danh sách -->
                <div class="card-body">
                    <?php if (empty($accounts)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không tìm thấy tài khoản nào</h5>
                            <p class="text-muted">Hãy thử tìm kiếm với từ khóa khác hoặc thêm tài khoản mới</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80">ID</th>
                                        <th>Tên đăng nhập</th>
                                        <th>Họ tên</th>
                                        <th>Vai trò</th>
                                        <th width="120">Trạng thái</th>
                                        <th width="200" class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($accounts as $acc): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">#<?= $acc['maTaiKhoan'] ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        <?= strtoupper(substr($acc['tenDangNhap'], 0, 1)) ?>
                                                    </div>
                                                    <span><?= htmlspecialchars($acc['tenDangNhap']) ?></span>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($acc['hoTen'] ?? 'Chưa cập nhật') ?></td>
                                            <td>
                                                <?php
                                                    $roleColors = [
                                                        'QTV' => 'dark',
                                                        'BGH' => 'info',
                                                        'GIAOVIEN' => 'primary',
                                                        'HOCSINH' => 'success',
                                                        'PHUHUYNH' => 'warning',
                                                        'TOTRUONG' => 'danger'
                                                    ];
                                                    $roleLabels = [
                                                        'QTV' => 'Quản trị viên',
                                                        'BGH' => 'Ban giám hiệu',
                                                        'GIAOVIEN' => 'Giáo viên',
                                                        'HOCSINH' => 'Học sinh',
                                                        'PHUHUYNH' => 'Phụ huynh',
                                                        'TOTRUONG' => 'Tổ trưởng'
                                                    ];
                                                    $role = $acc['loaiNguoiDung'] ?? '';
                                                    $color = $roleColors[$role] ?? 'secondary';
                                                    $label = $roleLabels[$role] ?? $role;
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                                            </td>
                                            <td>
                                                <?php if ($acc['trangThai'] === 'HOAT_DONG'): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Hoạt động
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Đã khóa
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="index.php?controller=QuanLyTaiKhoan&action=edit&id=<?= $acc['maTaiKhoan'] ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       data-bs-toggle="tooltip" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="index.php?controller=QuanLyTaiKhoan&action=toggleStatus&id=<?= $acc['maTaiKhoan'] ?>" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       data-bs-toggle="tooltip" 
                                                       title="<?= $acc['trangThai'] === 'HOAT_DONG' ? 'Khóa tài khoản' : 'Mở khóa tài khoản' ?>">
                                                        <i class="fas <?= $acc['trangThai'] === 'HOAT_DONG' ? 'fa-lock' : 'fa-unlock' ?>"></i>
                                                    </a>
                                                    <a href="index.php?controller=QuanLyTaiKhoan&action=delete&id=<?= $acc['maTaiKhoan'] ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       data-bs-toggle="tooltip" title="Xóa"
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Phân trang (nếu có) -->
                <?php if (!empty($accounts) && isset($totalPages) && $totalPages > 1): ?>
                    <div class="card-footer">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                                        <a class="page-link" 
                                           href="index.php?controller=QuanLyTaiKhoan&action=index&page=<?= $i ?>&search_id=<?= $_GET['search_id'] ?? '' ?>&search_username=<?= $_GET['search_username'] ?? '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Nút quay lại -->
            <div class="text-center mt-4">
                <a href="index.php?controller=home&action=index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại trang chính
                </a>
            </div>
        </div>
    </div>
</div>
</div> <!-- Đóng content-wrapper -->

<style>
/* Content wrapper để tránh sidebar */
.content-wrapper {
    margin-left: 250px;
    min-height: 100vh;
    background-color: #f5f7fb;
    transition: margin-left 0.3s;
}

@media (max-width: 768px) {
    .content-wrapper {
        margin-left: 0;
    }
}

/* Card styling */
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    padding: 15px 25px;
}

.card-title {
    font-weight: 600;
}

/* Avatar circle */
.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

/* Table styling */
.table th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

/* Badge styling */
.badge {
    padding: 6px 12px;
    font-weight: 500;
    border-radius: 6px;
}

/* Button group */
.btn-group .btn {
    border-radius: 6px !important;
    margin: 0 2px;
}

.btn-group .btn:first-child {
    margin-left: 0;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Form controls */
.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Alert styling */
.alert {
    border-radius: 8px;
    border: none;
    margin: 20px;
}

/* Pagination */
.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.pagination .page-link {
    color: #495057;
    border-radius: 6px;
    margin: 0 3px;
    border: 1px solid #dee2e6;
}

.pagination .page-link:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

/* Empty state */
.text-center.py-5 {
    padding: 3rem 1rem;
}

.text-center.py-5 i {
    opacity: 0.6;
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow-x: auto;
    }
    
    .btn-group .btn {
        padding: 4px 8px;
        font-size: 12px;
    }
}
</style>

<script>
// Khởi tạo tooltip
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php
    require_once 'views/layouts/footer.php';
?>