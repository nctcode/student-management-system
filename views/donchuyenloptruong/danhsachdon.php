<?php
$title = "Danh sách đơn chuyển trường";
$userRole = $_SESSION['user']['vaiTro'] ?? '';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <?php if ($userRole === 'PHUHUYNH'): ?>
        <a href="index.php?controller=donchuyenloptruong&action=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo đơn mới
        </a>
        <?php endif; ?>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <input type="hidden" name="controller" value="donchuyenloptruong">
                <input type="hidden" name="action" value="index">
                
                <div class="col-md-3">
                    <label class="form-label">Trạng thái trường đi</label>
                    <select name="trangThaiTruongDi" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="Chờ duyệt" <?php echo ($_GET['trangThaiTruongDi'] ?? '') === 'Chờ duyệt' ? 'selected' : ''; ?>>Chờ duyệt</option>
                        <option value="Đã duyệt" <?php echo ($_GET['trangThaiTruongDi'] ?? '') === 'Đã duyệt' ? 'selected' : ''; ?>>Đã duyệt</option>
                        <option value="Từ chối" <?php echo ($_GET['trangThaiTruongDi'] ?? '') === 'Từ chối' ? 'selected' : ''; ?>>Từ chối</option>
                        <option value="Đã hủy" <?php echo ($_GET['trangThaiTruongDi'] ?? '') === 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Trạng thái trường đến</label>
                    <select name="trangThaiTruongDen" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="Chờ duyệt" <?php echo ($_GET['trangThaiTruongDen'] ?? '') === 'Chờ duyệt' ? 'selected' : ''; ?>>Chờ duyệt</option>
                        <option value="Đã duyệt" <?php echo ($_GET['trangThaiTruongDen'] ?? '') === 'Đã duyệt' ? 'selected' : ''; ?>>Đã duyệt</option>
                        <option value="Từ chối" <?php echo ($_GET['trangThaiTruongDen'] ?? '') === 'Từ chối' ? 'selected' : ''; ?>>Từ chối</option>
                        <option value="Đã hủy" <?php echo ($_GET['trangThaiTruongDen'] ?? '') === 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="tuNgay" class="form-control" value="<?php echo $_GET['tuNgay'] ?? ''; ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="denNgay" class="form-control" value="<?php echo $_GET['denNgay'] ?? ''; ?>">
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                    <a href="index.php?controller=donchuyenloptruong&action=index" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách đơn -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Học sinh</th>
                            <th>Lớp hiện tại</th>
                            <th>Trường hiện tại</th>
                            <th>Trường chuyển đến</th>
                            <th>Ngày gửi</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($donChuyenTruong)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Không có đơn nào</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($donChuyenTruong as $don): ?>
                        <tr>
                            <td>#<?php echo $don['maDon']; ?></td>
                            <td>
                                <div><?php echo $don['tenHocSinh']; ?></div>
                                <small class="text-muted">PH: <?php echo $don['tenPhuHuynh'] ?? 'N/A'; ?></small>
                            </td>
                            <td><?php echo $don['tenLopHienTai']; ?></td>
                            <td><?php echo $don['tenTruongHienTai']; ?></td>
                            <td><?php echo $don['tenTruongDen'] ?? 'Chưa chọn'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($don['ngayGui'])); ?></td>
                            <td>
                                <div class="mb-1">
                                    <small><strong>Trường đi:</strong></small>
                                    <?php
                                    $badgeClassDi = [
                                        'Chờ duyệt' => 'bg-warning',
                                        'Đã duyệt' => 'bg-success',
                                        'Từ chối' => 'bg-danger',
                                        'Đã hủy' => 'bg-secondary'
                                    ];
                                    ?>
                                    <span class="badge <?php echo $badgeClassDi[$don['trangThaiTruongDi']] ?? 'bg-secondary'; ?>">
                                        <?php echo $don['trangThaiTruongDi']; ?>
                                    </span>
                                </div>
                                <div>
                                    <small><strong>Trường đến:</strong></small>
                                    <?php
                                    $badgeClassDen = [
                                        'Chờ duyệt' => 'bg-warning',
                                        'Đã duyệt' => 'bg-success',
                                        'Từ chối' => 'bg-danger',
                                        'Đã hủy' => 'bg-secondary'
                                    ];
                                    ?>
                                    <span class="badge <?php echo $badgeClassDen[$don['trangThaiTruongDen']] ?? 'bg-secondary'; ?>">
                                        <?php echo $don['trangThaiTruongDen']; ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <a href="index.php?controller=donchuyenloptruong&action=detail&maDon=<?php echo $don['maDon']; ?>" 
                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if (in_array($userRole, ['QTV', 'BGH']) && $don['trangThaiTruongDi'] === 'Chờ duyệt'): ?>
                                <a href="index.php?controller=donchuyenloptruong&action=pheduyetdon&maDon=<?php echo $don['maDon']; ?>" 
                                   class="btn btn-sm btn-success" title="Phê duyệt đơn">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($userRole === 'PHUHUYNH' && $don['trangThaiTruongDi'] === 'Chờ duyệt'): ?>
                                <form method="POST" action="index.php?controller=donchuyenloptruong&action=cancel&maDon=<?php echo $don['maDon']; ?>" 
                                      class="d-inline" onsubmit="return confirm('Bạn có chắc muốn hủy đơn này?')">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hủy đơn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>