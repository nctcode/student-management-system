<div class="container-fluid">
    <!-- Header -->
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
    // Hiển thị thông báo
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
                <div><?= nl2br(htmlspecialchars($_SESSION['error'])); ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Form phân công -->
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
                            <!-- Chọn lớp -->
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

                            <!-- Phân công GVCN -->
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

                            <!-- Thống kê nhanh -->
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

                        <!-- Phân công GVBM -->
                        <!-- Thay thế phần "Phân Công Giáo Viên Bộ Môn" trong view bằng code sau: -->
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

                        <!-- Nút hành động -->
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

<!-- Giữ nguyên CSS và JavaScript từ phiên bản trước -->
<style>
    .section-header {
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }

    .card {
        border-radius: 12px;
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }

    .form-select, .form-control {
        border-radius: 8px;
        transition: border-color 0.15s ease-in-out;
    }

    .assignment-row {
        transition: all 0.2s ease;
        border-radius: 8px;
    }

    .assignment-row:hover {
        background-color: #f8f9fa;
    }

    .specialization-match {
        border-left: 4px solid #28a745 !important;
        background-color: rgba(40, 167, 69, 0.05);
    }

    .specialization-mismatch {
        border-left: 4px solid #ffc107 !important;
        background-color: rgba(255, 193, 7, 0.05);
    }

    .teacher-option {
        padding: 8px 12px;
    }

    .progress {
        border-radius: 4px;
    }

    .icon-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .current-assignment-info {
        background-color: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        border-left: 3px solid #0d6efd;
        font-size: 0.85rem;
    }

    /* Thêm hiệu ứng khi có thay đổi */
    .form-select.changed {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Style cho option đang được chọn */
    .teacher-option[selected] {
        background-color: #e7f1ff;
        color: #0d6efd;
        font-weight: 500;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const maLopSelect = document.getElementById('maLop');
    const maGVCNSelect = document.getElementById('maGVCN');
    const gvbmAssignmentsDiv = document.getElementById('gvbm-assignments');
    const allSubjects = <?= json_encode($subjects) ?>;
    
    // Biến toàn cục lưu trữ giáo viên theo môn
    const teachersBySubject = {};
    // Biến lưu phân công hiện tại
    let currentAssignments = {};

    function renderSubjectAssignments() {
        const maLop = maLopSelect.value;
        if (!maLop) {
            gvbmAssignmentsDiv.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-school fa-3x mb-3 opacity-25"></i>
                    <p>Vui lòng chọn lớp để hiển thị danh sách môn học</p>
                </div>
            `;
            return;
        }

        // Reset current assignments khi chọn lớp mới
        currentAssignments = {};
        
        // Tải phân công hiện tại và sau đó render giao diện
        loadCurrentAssignments(maLop).then((assignments) => {
            // Lưu phân công hiện tại vào biến toàn cục
            currentAssignments = assignments || {};
            
            // Render giao diện
            let html = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="30%" class="ps-4">Môn Học</th>
                                <th width="70%">Giáo Viên Phụ Trách</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            allSubjects.forEach(subject => {
                // Kiểm tra xem môn này đã có phân công chưa
                const currentAssignment = currentAssignments[subject.maMonHoc];
                
                html += `
                    <tr class="assignment-row">
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-sm bg-primary bg-opacity-10 text-primary rounded-circle me-3">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <strong class="d-block">${subject.tenMonHoc}</strong>
                                    
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="position-relative">
                                <select name="assignments[${subject.maMonHoc}]" 
                                        class="form-select teacher-select" 
                                        data-subject-id="${subject.maMonHoc}"
                                        onchange="highlightSpecialization(this, ${subject.maMonHoc})">
                                    <option value="">-- Chọn giáo viên --</option>
                                    <option value="loading" disabled>Đang tải giáo viên...</option>
                                </select>
                                <div class="specialization-feedback mt-1 small" style="display: none;"></div>
                                ${currentAssignment ? `
                                <div id="current-info-${subject.maMonHoc}" class="current-assignment-info mt-1 small text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Đã phân công: ${currentAssignment.tenGiaoVien || 'Chưa có'}
                                </div>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
            gvbmAssignmentsDiv.innerHTML = html;
            
            // Tải giáo viên cho từng môn học và tự động chọn giáo viên đã phân công
            allSubjects.forEach(subject => {
                loadTeachersForSubject(subject.maMonHoc);
            });
        }).catch(error => {
            console.error('Lỗi khi tải phân công:', error);
            gvbmAssignmentsDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Lỗi khi tải phân công. Vui lòng thử lại.
                </div>
            `;
        });
    }

    async function loadCurrentAssignments(maLop) {
        try {
            const response = await fetch(`index.php?controller=PhanCongGVBMCN&action=ajaxGetAssignments&maLop=${maLop}`);
            const data = await response.json();
            
            if (data.success) {
                // Cập nhật GVCN
                if (data.gvcnAssignment && data.gvcnAssignment.maGiaoVien) {
                    maGVCNSelect.value = data.gvcnAssignment.maGiaoVien;
                }
                
                // Trả về assignments
                return data.assignments || {};
            }
            return {};
        } catch (error) {
            console.error('Lỗi tải phân công hiện tại:', error);
            return {};
        }
    }

    function loadTeachersForSubject(maMonHoc) {
        fetch(`index.php?controller=PhanCongGVBMCN&action=ajaxGetTeachersBySubject&maMonHoc=${maMonHoc}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Lưu giáo viên vào biến toàn cục
                    teachersBySubject[maMonHoc] = data.teachers;
                    
                    // Cập nhật dropdown
                    const select = document.querySelector(`.teacher-select[data-subject-id="${maMonHoc}"]`);
                    if (select) {
                        // Xóa option "Đang tải..."
                        select.innerHTML = '<option value="">-- Chọn giáo viên --</option>';
                        
                        if (data.teachers.length === 0) {
                            select.innerHTML += '<option value="" disabled>Không có giáo viên trong tổ</option>';
                        } else {
                            // Lấy giáo viên đã phân công hiện tại cho môn này
                            const currentAssignment = currentAssignments[maMonHoc];
                            const currentTeacherId = currentAssignment ? currentAssignment.maGiaoVien : null;
                            
                            // Thêm các option giáo viên
                            data.teachers.forEach(teacher => {
                                const chuyenMonText = teacher.chuyenMon ? ` (${teacher.chuyenMon})` : '';
                                const selected = currentTeacherId == teacher.maGiaoVien ? 'selected' : '';
                                
                                select.innerHTML += `
                                    <option value="${teacher.maGiaoVien}" ${selected}>
                                        ${teacher.hoTen}${chuyenMonText}
                                    </option>
                                `;
                            });
                        }
                        
                        // Kiểm tra và highlight chuyên môn nếu đã chọn giáo viên
                        const currentAssignment = currentAssignments[maMonHoc];
                        if (currentAssignment && currentAssignment.maGiaoVien) {
                            setTimeout(() => {
                                highlightSpecialization(select, maMonHoc);
                            }, 100);
                        }
                    }
                } else {
                    // Nếu không thành công, hiển thị thông báo
                    const select = document.querySelector(`.teacher-select[data-subject-id="${maMonHoc}"]`);
                    if (select) {
                        select.innerHTML = '<option value="">-- Lỗi tải giáo viên --</option>';
                    }
                }
            })
            .catch(error => {
                console.error('Lỗi tải giáo viên:', error);
                const select = document.querySelector(`.teacher-select[data-subject-id="${maMonHoc}"]`);
                if (select) {
                    select.innerHTML = '<option value="">-- Lỗi kết nối --</option>';
                }
            });
    }

    // Hàm highlight chuyên môn phù hợp
    window.highlightSpecialization = function(selectElement, maMonHoc) {
        const teacherId = selectElement.value;
        const feedbackElement = selectElement.parentNode.querySelector('.specialization-feedback');
        const currentInfoElement = document.getElementById(`current-info-${maMonHoc}`);
        const row = selectElement.closest('tr');
        
        // Ẩn thông tin phân công cũ khi đổi giáo viên
        if (currentInfoElement && teacherId) {
            currentInfoElement.style.display = 'none';
        }
        
        if (!teacherId || teacherId === '') {
            feedbackElement.style.display = 'none';
            selectElement.classList.remove('is-valid', 'is-invalid', 'border-success', 'border-warning');
            row.classList.remove('specialization-match', 'specialization-mismatch');
            return;
        }
        
        const teachers = teachersBySubject[maMonHoc];
        const subject = allSubjects.find(s => s.maMonHoc == maMonHoc);
        
        if (teachers && subject) {
            const teacher = teachers.find(t => t.maGiaoVien == teacherId);
            
            if (teacher) {
                const chuyenMon = teacher.chuyenMon ? teacher.chuyenMon.toLowerCase() : '';
                const tenMonHoc = subject.tenMonHoc.toLowerCase();
                
                // Kiểm tra nếu chuyên môn chứa tên môn học
                const isMatch = chuyenMon.includes(tenMonHoc) || chuyenMon.includes(subject.tenMonHoc.toLowerCase());
                
                if (isMatch) {
                    feedbackElement.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>Chuyên môn phù hợp</span>';
                    feedbackElement.style.display = 'block';
                    selectElement.classList.add('is-valid', 'border-success');
                    selectElement.classList.remove('is-invalid', 'border-warning');
                    row.classList.add('specialization-match');
                    row.classList.remove('specialization-mismatch');
                } else {
                    feedbackElement.innerHTML = `<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Chuyên môn không khớp hoàn toàn</span>`;
                    feedbackElement.style.display = 'block';
                    selectElement.classList.add('is-invalid', 'border-warning');
                    selectElement.classList.remove('is-valid', 'border-success');
                    row.classList.add('specialization-mismatch');
                    row.classList.remove('specialization-match');
                }
            }
        }
    };

    maLopSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const maGVCNHienTai = selectedOption.dataset.gvcn;
        
        // Tự động chọn GVCN hiện tại (nếu có)
        maGVCNSelect.value = maGVCNHienTai || '';
        
        // Render và tải phân công bộ môn
        renderSubjectAssignments();
    });

    // Gọi lần đầu để hiển thị giao diện trống
    renderSubjectAssignments();
    
    // Thêm hàm reload để gọi từ bên ngoài nếu cần
    window.reloadClassAssignments = function() {
        renderSubjectAssignments();
    };
});
</script>