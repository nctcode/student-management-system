<?php
$title = "Đơn chuyển lớp/trường của tôi";
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    /* CSS fix cho trạng thái không bị xuống dòng */
.status-badge,
.badge-completed,
.badge-rejected,
.badge-pending,
.bg-gradient-success,
.bg-gradient-danger,
.bg-gradient-warning,
.bg-gradient-info {
    white-space: nowrap !important;
    word-break: keep-all !important;
    display: inline-block !important;
    min-width: max-content !important;
    text-align: center !important;
}

/* Đảm bảo text trong các badge không wrap */
.badge {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

/* Fix cho các thẻ trạng thái trong danh sách */
.request-item .status {
    white-space: nowrap !important;
    min-width: 100px !important;
    text-align: center !important;
}

/* Fix cho các badge trong chi tiết đơn */
.transfer-card .badge {
    white-space: nowrap !important;
    min-width: 90px !important;
}

/* Đảm bảo text trong các ô trạng thái của table */
.table td .badge {
    white-space: nowrap !important;
    min-width: 100px !important;
}

/* Fix cụ thể cho các trạng thái */
.status-approved,
.status-rejected,
.status-pending {
    white-space: nowrap !important;
    min-width: 100px !important;
    display: inline-block !important;
    text-align: center !important;
}
.table-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.table-card .card-header {
    background: rgba(255,255,255,0.95);
    border-radius: 20px 20px 0 0 !important;
    border-bottom: 2px solid rgba(102, 126, 234, 0.1);
    padding: 2rem;
}

.table-card .card-body {
    background: rgba(255,255,255,0.98);
    border-radius: 0 0 20px 20px;
}

.table th {
    border-bottom: 2px solid #e2e8f0;
    font-weight: 700;
    color: #2d3748;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1.2rem 0.75rem;
}

.table td {
    padding: 1.2rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.badge-completed {
    background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
    color: white;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 8px;
}

.badge-rejected {
    background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
    color: white;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 8px;
}

.badge-pending {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 8px;
}

.student-avatar {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.1rem;
}

.request-item:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    transform: translateY(-2px);
    transition: all 0.3s ease;
    border-radius: 12px;
}

.btn-detail {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-detail:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    color: white;
}

.empty-state {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 20px;
    padding: 4rem 2rem;
}

.empty-state-icon {
    font-size: 4rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1.5rem;
}

.alert-custom {
    border: none;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin: 1rem 2rem;
}

.transfer-info {
    font-size: 0.85rem;
    color: #64748b;
}

.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-gradient text-primary mb-1">ĐƠN CHUYỂN LỚP / TRƯỜNG CỦA TÔI</h4>
                            <p class="mb-0 text-muted">Quản lý các đơn chuyển lớp, chuyển trường của bạn</p>
                        </div>
                        <a href="index.php?controller=donchuyenloptruong&action=create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Tạo đơn mới
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-3 text-success"></i>
                                <div class="flex-grow-1">
                                    <strong class="text-success">Thành công!</strong> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-3 text-danger"></i>
                                <div class="flex-grow-1">
                                    <strong class="text-danger">Lỗi!</strong> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive p-0">
                        <?php if (empty($requests)): ?>
                            <div class="empty-state text-center">
                                <div class="empty-state-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <h5 class="text-secondary mb-2">Chưa có đơn chuyển lớp/trường nào</h5>
                                <p class="text-muted mb-4">Hãy tạo đơn đầu tiên để bắt đầu quá trình chuyển lớp/trường</p>
                                <a href="index.php?controller=donchuyenloptruong&action=create" class="btn btn-primary px-4">
                                    <i class="fas fa-plus me-2"></i> Tạo đơn đầu tiên
                                </a>
                            </div>
                        <?php else: ?>
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Thông tin đơn</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Loại đơn</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ngày gửi</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                                        <th class="text-secondary opacity-7"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $don): ?>
                                        <?php
                                        $type = $don['loaiDon'] ?? 'chuyen_truong';
                                        $status = $don['trangThaiTong'] ?? 'Không xác định';
                                        $statusClass = match($status) {
                                            'Hoàn tất' => 'badge-completed',
                                            'Bị từ chối' => 'badge-rejected',
                                            default => 'badge-pending',
                                        };
                                        $studentInitial = substr($don['tenHS'] ?? 'N', 0, 1);
                                        ?>
                                        <tr class="request-item">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="student-avatar me-3">
                                                        <?= $studentInitial ?>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm"><?= htmlspecialchars($don['tenHS'] ?? 'N/A') ?></h6>
                                                        <p class="text-xs text-secondary mb-0 transfer-info">
                                                            #<?= str_pad($don['maDon'], 3, '0', STR_PAD_LEFT) ?> • 
                                                            <?= $type === 'chuyen_lop' 
                                                                ? htmlspecialchars($don['lopHienTai'] ?? 'N/A') . ' → ' . htmlspecialchars($don['lopDen'] ?? 'N/A')
                                                                : htmlspecialchars($don['truongHienTai'] ?? 'N/A') . ' → ' . htmlspecialchars($don['truongDen'] ?? 'N/A') ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold badge 
                                                    <?= $type === 'chuyen_lop' ? 'bg-info' : 'bg-warning' ?>">
                                                    <?= $type === 'chuyen_lop' ? 'Chuyển lớp' : 'Chuyển trường' ?>
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">
                                                    <?= date('d/m/Y H:i', strtotime($don['ngayGui'])) ?>
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="<?= $statusClass ?>"><?= $status ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <a href="index.php?controller=donchuyenloptruong&action=chitietdoncuatoi&id=<?= $don['maDon'] ?>" 
                                                class="btn btn-detail btn-sm" style="white-space: nowrap; min-width: 100px;">
                                                    <i class="fas fa-eye me-1"></i>Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>