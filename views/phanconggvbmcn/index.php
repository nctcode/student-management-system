<link rel="stylesheet" href="assets/css/phan_cong.css">

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-chalkboard-teacher me-2"></i>Phân Công Giáo Viên
            </h1>
            <p class="text-muted">Quản lý phân công giáo viên chủ nhiệm và bộ môn</p>
        </div>
        <div>
            <a href="index.php?controller=PhanCongGVBMCN&action=viewCurrentAssignments" 
               class="btn btn-outline-primary">
                <i class="fas fa-list-alt me-2"></i>Xem phân công hiện tại
            </a>
        </div>
    </div>

    <?php 
    if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div><?= $_SESSION['success']; ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; 
    
    if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div><?= $_SESSION['error']; ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Phân Công Giáo Viên
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post" action="index.php?controller=PhanCongGVBMCN&action=saveAssignment">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label for="maLop" class="form-label fw-semibold">
                                    <i class="fas fa-school me-2 text-primary"></i>Chọn Lớp
                                </label>
                                <select id="maLop" name="maLop" class="form-select" required>
                                    <option value="">-- Chọn lớp --</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option 
                                            value="<?= $class['maLop'] ?>"
                                            data-gvcn="<?= $class['maGiaoVien'] ?? '' ?>">
                                            <?= htmlspecialchars($class['tenLop']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($classes)): ?>
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>Chưa có lớp nào trong hệ thống
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label for="maGVCN" class="form-label fw-semibold">
                                    <i class="fas fa-user-tie me-2 text-primary"></i>Giáo Viên Chủ Nhiệm
                                </label>
                                <select id="maGVCN" name="maGVCN" class="form-select" required>
                                    <option value="">-- Chọn giáo viên --</option>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?= $teacher['maGiaoVien'] ?>">
                                            <?= htmlspecialchars($teacher['hoTen']) ?>
                                            <?php if (!empty($teacher['chuyenMon'])): ?>
                                                <small class="text-muted">(<?= htmlspecialchars($teacher['chuyenMon']) ?>)</small>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (empty($teachers)): ?>
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>Chưa có giáo viên nào trong hệ thống
                                    </div>
                                <?php endif; ?>
                                <div class="form-text text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Một giáo viên chỉ được chủ nhiệm một lớp
                                </div>
                            </div>

                            <div class="col-md-4 mb-4">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <h6 class="text-primary mb-3">Thống Kê Hệ Thống</h6>
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <h4 class="text-primary mb-1"><?= $totalClasses ?></h4>
                                            <small class="text-muted">Lớp học</small>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <h4 class="text-success mb-1"><?= $totalTeachers ?></h4>
                                            <small class="text-muted">Giáo viên</small>
                                        </div>
                                        <div class="col-12">
                                            <div class="progress mb-1" style="height: 8px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: <?= $totalClasses > 0 ? round(($classesWithGVCN / $totalClasses) * 100) : 0 ?>%">
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <?= $classesWithGVCN ?>/<?= $totalClasses ?> lớp đã có GVCN
                                                (<?= $totalClasses > 0 ? round(($classesWithGVCN / $totalClasses) * 100) : 0 ?>%)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="section-header mb-3">
                                    <h6 class="text-primary mb-0">
                                        <i class="fas fa-book me-2"></i>Phân Công Giáo Viên Bộ Môn
                                    </h6>
                                    <small class="text-muted">Chọn giáo viên theo tổ chuyên môn của môn học</small>
                                </div>
                                
                                <?php if (!empty($subjects)): ?>
                                    <div class="alert alert-info d-flex align-items-center mb-4">
                                        <i class="fas fa-info-circle me-3 fs-5 text-info"></i>
                                        <div>
                                            <strong>Lưu ý:</strong> Hệ thống chỉ hiển thị giáo viên thuộc tổ chuyên môn của môn học
                                            <div class="small mt-1">
                                                <i class="fas fa-check text-success me-1"></i>Giáo viên có chuyên môn phù hợp sẽ được đánh dấu
                                            </div>
                                        </div>
                                    </div>

                                    <div id="gvbm-assignments">
                                        <div class="text-center py-5 text-muted">
                                            <i class="fas fa-school fa-3x mb-3 opacity-25"></i>
                                            <p>Vui lòng chọn lớp để hiển thị danh sách môn học</p>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning text-center py-4">
                                        <i class="fas fa-exclamation-triangle fa-2x mb-3 text-warning"></i>
                                        <h6>Chưa có môn học nào trong hệ thống</h6>
                                        <p class="mb-0">Vui lòng thêm môn học trước khi phân công giáo viên bộ môn</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                                    <a href="index.php?controller=home&action=principal" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4" 
                                            <?= (empty($classes) || empty($teachers) || empty($subjects)) ? 'disabled' : '' ?>>
                                        <i class="fas fa-save me-2"></i>Lưu Phân Công
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.allSubjects = <?= json_encode($subjects) ?>;
</script>
<script src="assets/js/phan_cong.js"></script>