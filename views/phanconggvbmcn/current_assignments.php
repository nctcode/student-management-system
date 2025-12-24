<?php
// Thêm dòng này ở đầu file để đảm bảo biến được định nghĩa
$gvcnAssignments = $gvcnAssignments ?? [];
$classes = $classes ?? [];
?>

<link rel="stylesheet" href="assets/css/phan_cong.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-list-alt me-2"></i>Phân Công Hiện Tại
            </h1>
            <p class="text-muted">Xem danh sách phân công giáo viên hiện tại</p>
        </div>
        <div>
            <a href="index.php?controller=PhanCongGVBMCN&action=index" 
               class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại phân công
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="card-title text-primary mb-1"><?= count($gvcnAssignments) ?></h3>
                            <p class="card-text text-muted mb-0">Tổng số lớp</p>
                        </div>
                        <div class="icon-circle bg-primary bg-opacity-25 text-primary">
                            <i class="fas fa-school fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="card-title text-success mb-1"><?= count(array_filter($gvcnAssignments, function($item) { return !empty($item['tenGV']); })) ?></h3>
                            <p class="card-text text-muted mb-0">Lớp có GVCN</p>
                        </div>
                        <div class="icon-circle bg-success bg-opacity-25 text-success">
                            <i class="fas fa-user-tie fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="card-title text-warning mb-1"><?= count(array_filter($gvcnAssignments, function($item) { return empty($item['tenGV']); })) ?></h3>
                            <p class="card-text text-muted mb-0">Lớp chưa có GVCN</p>
                        </div>
                        <div class="icon-circle bg-warning bg-opacity-25 text-warning">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="card-title text-info mb-1"><?= count($classes) ?></h3>
                            <p class="card-text text-muted mb-0">Lớp hệ thống</p>
                        </div>
                        <div class="icon-circle bg-info bg-opacity-25 text-info">
                            <i class="fas fa-layer-group fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0 text-primary">
                <i class="fas fa-user-tie me-2"></i>Giáo Viên Chủ Nhiệm
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($gvcnAssignments)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                    <p>Chưa có phân công giáo viên chủ nhiệm</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40%" class="ps-4">Lớp Học</th>
                                <th width="40%">Giáo Viên Chủ Nhiệm</th>
                                <th width="20%" class="text-center">Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gvcnAssignments as $assignment): ?>
                                <tr class="assignment-row">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-sm bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                                                <i class="fas fa-school"></i>
                                            </div>
                                            <div>
                                                <strong><?= htmlspecialchars($assignment['tenLop'] ?? 'N/A') ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($assignment['tenGV'])): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="icon-sm bg-success bg-opacity-10 text-success rounded-circle me-3">
                                                    <i class="fas fa-user-tie"></i>
                                                </div>
                                                <div>
                                                    <strong><?= htmlspecialchars($assignment['tenGV']) ?></strong>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa phân công</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($assignment['tenGV'])): ?>
                                            <span class="badge bg-success rounded-pill py-2 px-3">
                                                <i class="fas fa-check me-1"></i>Đã phân công
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning rounded-pill py-2 px-3">
                                                <i class="fas fa-clock me-1"></i>Chờ phân công
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>