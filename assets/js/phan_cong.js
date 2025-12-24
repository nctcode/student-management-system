/* assets/js/phan_cong.js */

document.addEventListener('DOMContentLoaded', function() {
    // Chỉ chạy logic nếu đang ở trang Phân Công (có element maLop)
    const maLopSelect = document.getElementById('maLop');
    const maGVCNSelect = document.getElementById('maGVCN');
    const gvbmAssignmentsDiv = document.getElementById('gvbm-assignments');
    
    // Biến toàn cục từ PHP truyền sang
    const allSubjects = window.allSubjects || [];
    
    // Biến lưu trữ cục bộ
    const teachersBySubject = {};
    let currentAssignments = {};

    if (!maLopSelect) return; // Thoát nếu không phải trang phân công

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

        currentAssignments = {};
        
        loadCurrentAssignments(maLop).then((assignments) => {
            currentAssignments = assignments || {};
            
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
                if (data.gvcnAssignment && data.gvcnAssignment.maGiaoVien) {
                    maGVCNSelect.value = data.gvcnAssignment.maGiaoVien;
                }
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
                    teachersBySubject[maMonHoc] = data.teachers;
                    
                    const select = document.querySelector(`.teacher-select[data-subject-id="${maMonHoc}"]`);
                    if (select) {
                        select.innerHTML = '<option value="">-- Chọn giáo viên --</option>';
                        
                        if (data.teachers.length === 0) {
                            select.innerHTML += '<option value="" disabled>Không có giáo viên trong tổ</option>';
                        } else {
                            const currentAssignment = currentAssignments[maMonHoc];
                            const currentTeacherId = currentAssignment ? currentAssignment.maGiaoVien : null;
                            
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
                        
                        const currentAssignment = currentAssignments[maMonHoc];
                        if (currentAssignment && currentAssignment.maGiaoVien) {
                            setTimeout(() => {
                                highlightSpecialization(select, maMonHoc);
                            }, 100);
                        }
                    }
                } else {
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

    window.highlightSpecialization = function(selectElement, maMonHoc) {
        const teacherId = selectElement.value;
        const feedbackElement = selectElement.parentNode.querySelector('.specialization-feedback');
        const currentInfoElement = document.getElementById(`current-info-${maMonHoc}`);
        const row = selectElement.closest('tr');
        
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
        maGVCNSelect.value = maGVCNHienTai || '';
        renderSubjectAssignments();
    });

    renderSubjectAssignments();
    
    window.reloadClassAssignments = function() {
        renderSubjectAssignments();
    };
});