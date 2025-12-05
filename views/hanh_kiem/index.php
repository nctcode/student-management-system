<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/giaovien.php';
?>

<title>Quản Lý Hạnh Kiểm Lớp Chủ Nhiệm</title>

<style>
    .content {
        margin-left: 250px !important;
        padding: 20px !important;
        
    }
    .editable {
        cursor: pointer;
        position: relative;
        min-height: 40px;
    }
    .editable:hover {
        background-color: rgba(0, 123, 255, 0.05) !important;
    }
    .editable.editing {
        background-color: #fff3cd !important;
    }
    .editable-input {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 10;
        border: 2px solid #007bff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .d-none {
        display: none !important;
    }
    .save-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    /* Tooltip cho nội dung dài */
    [title] {
        cursor: help;
    }
</style>

<div class="content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-2">
                    <i class="fas fa-chalkboard-teacher text-primary"></i> 
                    Quản Lý Hạnh Kiểm Lớp Chủ Nhiệm
                </h1>
                <?php if (isset($lopChuNhiem) && $lopChuNhiem): ?>
                    <p class="text-muted">
                        Lớp: <strong><?= htmlspecialchars($lopChuNhiem['tenLop']) ?></strong> | 
                        Khối: <?= htmlspecialchars($lopChuNhiem['tenKhoi'] ?? 'N/A') ?> | 
                        Năm học: <?= htmlspecialchars($lopChuNhiem['namHoc'] ?? 'N/A') ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
                <!-- Chọn học kỳ -->
                <!-- Trong views/hanh_kiem/index.php -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-calendar-alt"></i> 
                        Học kỳ: <?= htmlspecialchars($_SESSION['hoc_ky_hien_tai'] ?? 'HK1-2024') ?>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($dsHocKy as $hk): ?>
                            <li>
                                <a class="dropdown-item" href="#" onclick="changeHocKy('<?= $hk['hoc_ky'] ?>')">
                                    <?= htmlspecialchars($hk['ten_hoc_ky']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Nút hướng dẫn -->
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#helpModal">
                    <i class="fas fa-question-circle"></i> Hướng dẫn
                </button>
            </div>
        </div>

        <?php if (isset($thongBao)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($thongBao) ?>
            </div>
        <?php endif; ?>

        <!-- Thống kê -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Sĩ số lớp</h6>
                                <h2 class="mb-0"><?= count($dsHanhKiem) ?></h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Đã chấm điểm</h6>
                                <h2 class="mb-0" id="count-scored">0</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Chưa chấm</h6>
                                <h2 class="mb-0" id="count-not-scored">0</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Điểm TB lớp</h6>
                                <h2 class="mb-0" id="average-score">0</h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng danh sách học sinh -->
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list"></i> Danh sách học sinh</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-success" onclick="saveAll()">
                        <i class="fas fa-save"></i> Lưu tất cả
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print"></i> In báo cáo
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($dsHanhKiem)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableHanhKiem">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">STT</th>
                                    <th>Họ tên</th>
                                    <th width="100">Ngày sinh</th>
                                    <th width="80">Giới tính</th>
                                    <th width="100">Điểm số (0-100)</th>
                                    <th width="120">Xếp loại</th>
                                    <th>Nhận xét</th>
                                    <th width="150">Cập nhật</th>
                                    <th width="100">Thao tác</th>
                                </tr>
                            </thead>
                            <!-- Trong phần tbody của bảng -->
<tbody>
    <?php $stt = 1; ?>
    <?php foreach ($dsHanhKiem as $hs): ?>
    <tr data-ma-hs="<?= $hs['maHocSinh'] ?>" data-ma-hk="<?= $hs['maHanhKiem'] ?>">
        <td><?= $stt++ ?></td>
        <td>
            <strong><?= htmlspecialchars($hs['hoTen']) ?></strong><br>
            <small class="text-muted">Mã HS: <?= $hs['maHocSinh'] ?></small>
        </td>
        <td><?= !empty($hs['ngaySinh']) ? date('d/m/Y', strtotime($hs['ngaySinh'])) : '' ?></td>
        <td>
            <?php if ($hs['gioiTinh'] == 'NAM'): ?>
                <span class="badge bg-primary">Nam</span>
            <?php elseif ($hs['gioiTinh'] == 'NU'): ?>
                <span class="badge bg-danger">Nữ</span>
            <?php else: ?>
                <span class="badge bg-secondary">Khác</span>
            <?php endif; ?>
        </td>
        <td class="editable" data-field="diem_so" style="cursor: pointer;">
            <span class="diem-display">
                <?php if (!empty($hs['diem_so']) || $hs['diem_so'] === 0): ?>
                    <?= $hs['diem_so'] ?>
                <?php else: ?>
                    <span class="text-muted">Chưa chấm</span>
                <?php endif; ?>
            </span>
            <input type="number" class="form-control form-control-sm editable-input d-none" 
                   min="0" max="100" 
                   value="<?= !empty($hs['diem_so']) || $hs['diem_so'] === 0 ? $hs['diem_so'] : '' ?>" 
                   data-old-value="<?= !empty($hs['diem_so']) || $hs['diem_so'] === 0 ? $hs['diem_so'] : '' ?>"
                   style="width: 80px;">
        </td>
        <td class="xep-loai-cell">
            <?php if (!empty($hs['diem_so']) || $hs['diem_so'] === 0): ?>
                <?php
                $badgeClass = 'bg-secondary';
                switch($hs['xep_loai']) {
                    case 'Xuất sắc': $badgeClass = 'bg-success'; break;
                    case 'Tốt': $badgeClass = 'bg-primary'; break;
                    case 'Khá': $badgeClass = 'bg-info'; break;
                    case 'Trung bình': $badgeClass = 'bg-warning'; break;
                    case 'Yếu': $badgeClass = 'bg-danger'; break;
                }
                ?>
                <span class="badge <?= $badgeClass ?>"><?= $hs['xep_loai'] ?></span>
            <?php else: ?>
                <span class="badge bg-light text-dark">Chưa xếp loại</span>
            <?php endif; ?>
        </td>
        <td class="editable" data-field="nhan_xet" style="cursor: pointer;">
            <span class="nhan-xet-display">
                <?php if (!empty($hs['nhan_xet'])): ?>
                    <?php 
                    $displayText = htmlspecialchars($hs['nhan_xet']);
                    if (strlen($displayText) > 50) {
                        echo substr($displayText, 0, 50) . '...';
                    } else {
                        echo $displayText;
                    }
                    ?>
                <?php else: ?>
                    <span class="text-muted">Chưa có nhận xét</span>
                <?php endif; ?>
            </span>
            <textarea class="form-control editable-input d-none" rows="2"
                      data-old-value="<?= htmlspecialchars($hs['nhan_xet'] ?? '') ?>"><?= htmlspecialchars($hs['nhan_xet'] ?? '') ?></textarea>
        </td>
        <td>
            <?php if (!empty($hs['created_at'])): ?>
                <?= date('d/m/Y H:i', strtotime($hs['created_at'])) ?>
            <?php else: ?>
                <span class="text-muted">Chưa cập nhật</span>
            <?php endif; ?>
        </td>
        <td class="action-buttons">
            <button class="btn btn-sm btn-success save-btn" onclick="saveRow(this)" title="Lưu">
                <i class="fas fa-save"></i>
            </button>
            <?php if (!empty($hs['maHanhKiem'])): ?>
            <button class="btn btn-sm btn-danger" onclick="deleteHanhKiem(<?= $hs['maHanhKiem'] ?>, <?= $hs['maHocSinh'] ?>)" title="Xóa">
                <i class="fas fa-trash"></i>
            </button>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
                        </table>
                    </div>
                    
                    <!-- Hướng dẫn sử dụng -->
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-lightbulb"></i> Hướng dẫn sử dụng:</h6>
                        <ul class="mb-0">
                            <li>Nhấp vào ô <strong>Điểm số</strong> hoặc <strong>Nhận xét</strong> để chỉnh sửa trực tiếp</li>
                            <li>Nhấn <kbd>Enter</kbd> để lưu, <kbd>ESC</kbd> để hủy khi đang chỉnh sửa</li>
                            <li>Xếp loại sẽ tự động tính dựa trên điểm số</li>
                            <li>Mỗi học sinh chỉ có 1 điểm hạnh kiểm cho mỗi học kỳ</li>
                        </ul>
                    </div>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có học sinh nào trong lớp chủ nhiệm</h5>
                        <p class="text-muted">Hoặc bạn không phải là giáo viên chủ nhiệm của lớp nào</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal hướng dẫn -->
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-question-circle"></i> Hướng dẫn chấm điểm hạnh kiểm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Thang điểm và xếp loại:</h6>
                <ul>
                    <li><span class="badge bg-success">90-100 điểm</span>: Xuất sắc</li>
                    <li><span class="badge bg-primary">80-89 điểm</span>: Tốt</li>
                    <li><span class="badge bg-info">65-79 điểm</span>: Khá</li>
                    <li><span class="badge bg-warning">50-64 điểm</span>: Trung bình</li>
                    <li><span class="badge bg-danger">Dưới 50 điểm</span>: Yếu</li>
                </ul>
                <h6>Tiêu chí đánh giá:</h6>
                <ul>
                    <li>Chấp hành nội quy nhà trường</li>
                    <li>Ý thức học tập và rèn luyện</li>
                    <li>Đạo đức, lối sống</li>
                    <li>Tham gia hoạt động tập thể</li>
                    <li>Quan hệ với bạn bè, thầy cô</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Khởi tạo DataTable
    if ($('#tableHanhKiem').length && $('#tableHanhKiem tbody tr').length > 0) {
        $('#tableHanhKiem').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
            },
            pageLength: 25,
            order: [[1, 'asc']],
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [8] }
            ]
        });
    }
    
    // Tính toán thống kê
    updateStatistics();
    
    // Xử lý click để chỉnh sửa - FIXED
    $(document).on('click', '.editable', function(e) {
        // Ngăn chặn sự kiện click lan truyền
        e.stopPropagation();
        
        var cell = $(this);
        // Nếu đang trong chế độ edit, không làm gì
        if (cell.find('.editable-input').is(':visible')) {
            return;
        }
        
        var displaySpan = cell.find('.diem-display, .nhan-xet-display');
        var input = cell.find('.editable-input');
        
        displaySpan.addClass('d-none');
        input.removeClass('d-none').focus().select();
    });
    
    // Xử lý khi nhấn Enter hoặc ESC trên input
    $(document).on('keydown', '.editable-input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveEditable($(this).closest('.editable'));
        } else if (e.key === 'Escape') {
            cancelEditable($(this).closest('.editable'));
        }
    });
    
    // Xử lý khi mất focus
    $(document).on('blur', '.editable-input', function() {
        saveEditable($(this).closest('.editable'));
    });
});

function saveEditable(cell) {
    var input = cell.find('.editable-input');
    var displaySpan = cell.find('.diem-display, .nhan-xet-display');
    var oldValue = input.data('old-value');
    var newValue = input.val();
    var field = cell.data('field');
    
    // Nếu giá trị không thay đổi
    if (newValue == oldValue) {
        cancelEditable(cell);
        return;
    }
    
    // Validate điểm số
    if (field === 'diem_so') {
        var diem = parseInt(newValue);
        if (isNaN(diem) || diem < 0 || diem > 100) {
            showToast('error', 'Điểm số phải từ 0 đến 100');
            input.focus();
            return;
        }
        newValue = diem; // Đảm bảo là số
    }
    
    // Hiển thị giá trị mới
    if (field === 'diem_so') {
        if (newValue === '' || isNaN(newValue)) {
            displaySpan.html('<span class="text-muted">Chưa chấm</span>');
        } else {
            displaySpan.html(newValue);
            // Cập nhật xếp loại
            updateXepLoai(cell.closest('tr'), newValue);
        }
    } else {
        if (newValue === '') {
            displaySpan.html('<span class="text-muted">Chưa có nhận xét</span>');
        } else {
            // Giới hạn hiển thị nhận xét
            var displayText = newValue.length > 50 ? newValue.substring(0, 50) + '...' : newValue;
            displaySpan.html(displayText);
            displaySpan.attr('title', newValue);
        }
    }
    
    input.addClass('d-none');
    displaySpan.removeClass('d-none');
    
    // Cập nhật thống kê
    updateStatistics();
    
    // Tự động lưu khi thay đổi
    autoSaveRow(cell.closest('tr'));
}

function cancelEditable(cell) {
    var input = cell.find('.editable-input');
    var displaySpan = cell.find('.diem-display, .nhan-xet-display');
    
    // Khôi phục giá trị cũ
    var oldValue = input.data('old-value');
    if (oldValue === '' || oldValue === null) {
        if (cell.data('field') === 'diem_so') {
            displaySpan.html('<span class="text-muted">Chưa chấm</span>');
        } else {
            displaySpan.html('<span class="text-muted">Chưa có nhận xét</span>');
        }
    } else {
        if (cell.data('field') === 'diem_so') {
            displaySpan.html(oldValue);
        } else {
            var displayText = oldValue.length > 50 ? oldValue.substring(0, 50) + '...' : oldValue;
            displaySpan.html(displayText);
        }
    }
    
    input.addClass('d-none');
    displaySpan.removeClass('d-none');
}

function updateXepLoai(row, diem) {
    if (isNaN(diem)) {
        row.find('.xep-loai-cell').html('<span class="badge bg-light text-dark">Chưa xếp loại</span>');
        return;
    }
    
    var xepLoai = 'Yếu';
    var badgeClass = 'bg-danger';
    
    if (diem >= 90) {
        xepLoai = 'Xuất sắc';
        badgeClass = 'bg-success';
    } else if (diem >= 80) {
        xepLoai = 'Tốt';
        badgeClass = 'bg-primary';
    } else if (diem >= 65) {
        xepLoai = 'Khá';
        badgeClass = 'bg-info';
    } else if (diem >= 50) {
        xepLoai = 'Trung bình';
        badgeClass = 'bg-warning';
    }
    
    var xepLoaiCell = row.find('.xep-loai-cell');
    xepLoaiCell.html('<span class="badge ' + badgeClass + '">' + xepLoai + '</span>');
}

// Tự động lưu khi thay đổi
function autoSaveRow(row) {
    var maHocSinh = row.data('ma-hs');
    var maHanhKiem = row.data('ma-hk');
    var diemInput = row.find('[data-field="diem_so"] .editable-input');
    var nhanXetInput = row.find('[data-field="nhan_xet"] .editable-input');
    
    var diemSo = diemInput.val();
    var nhanXet = nhanXetInput.val();
    var hocKy = '<?= $_SESSION["hoc_ky_hien_tai"] ?? "HK1-2024" ?>';
    
    // Chỉ lưu nếu có thay đổi thực sự
    if (diemInput.data('old-value') != diemSo || nhanXetInput.data('old-value') != nhanXet) {
        // Validate
        if (diemSo !== '' && (isNaN(diemSo) || diemSo < 0 || diemSo > 100)) {
            return;
        }
        
        var saveBtn = row.find('.save-btn');
        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: 'index.php?controller=hanhkiem&action=save',
            type: 'POST',
            data: {
                maHocSinh: maHocSinh,
                hoc_ky: hocKy,
                diem_so: diemSo,
                nhan_xet: nhanXet,
                action: 'save'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Cập nhật old value
                    diemInput.data('old-value', diemSo);
                    nhanXetInput.data('old-value', nhanXet);
                    
                    // Cập nhật ngày cập nhật
                    var now = new Date();
                    var dateStr = now.getDate().toString().padStart(2, '0') + '/' + 
                                 (now.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                                 now.getFullYear() + ' ' + 
                                 now.getHours().toString().padStart(2, '0') + ':' + 
                                 now.getMinutes().toString().padStart(2, '0');
                    
                    row.find('td:eq(7)').html(dateStr);
                    
                    // Hiển thị thông báo nhỏ
                    showToast('success', 'Đã tự động lưu');
                } else {
                    showToast('error', response.message);
                }
                saveBtn.prop('disabled', false).html('<i class="fas fa-save"></i>');
            },
            error: function() {
                showToast('error', 'Lỗi kết nối máy chủ');
                saveBtn.prop('disabled', false).html('<i class="fas fa-save"></i>');
            }
        });
    }
}

function saveRow(button) {
    var row = $(button).closest('tr');
    var maHocSinh = row.data('ma-hs');
    var diemInput = row.find('[data-field="diem_so"] .editable-input');
    var nhanXetInput = row.find('[data-field="nhan_xet"] .editable-input');
    
    var diemSo = diemInput.val();
    var nhanXet = nhanXetInput.val();
    var hocKy = '<?= $_SESSION["hoc_ky_hien_tai"] ?? "HK1-2024" ?>';
    
    // Validate
    if (diemSo !== '' && (isNaN(diemSo) || diemSo < 0 || diemSo > 100)) {
        showToast('error', 'Vui lòng nhập điểm số hợp lệ (0-100)');
        return;
    }
    
    $(button).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: 'index.php?controller=hanhkiem&action=save',
        type: 'POST',
        data: {
            maHocSinh: maHocSinh,
            hoc_ky: hocKy,
            diem_so: diemSo,
            nhan_xet: nhanXet,
            action: 'save'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('success', response.message);
                // Cập nhật old value
                diemInput.data('old-value', diemSo);
                nhanXetInput.data('old-value', nhanXet);
                
                // Cập nhật ngày cập nhật
                var now = new Date();
                var dateStr = now.getDate().toString().padStart(2, '0') + '/' + 
                             (now.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                             now.getFullYear() + ' ' + 
                             now.getHours().toString().padStart(2, '0') + ':' + 
                             now.getMinutes().toString().padStart(2, '0');
                
                row.find('td:eq(7)').html(dateStr);
            } else {
                showToast('error', response.message);
            }
            $(button).prop('disabled', false).html('<i class="fas fa-save"></i>');
        },
        error: function() {
            showToast('error', 'Lỗi kết nối máy chủ');
            $(button).prop('disabled', false).html('<i class="fas fa-save"></i>');
        }
    });
}


function saveAll() {
    if (!confirm('Bạn có chắc muốn lưu tất cả điểm đã chỉnh sửa?')) return;
    
    $('.save-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    var savedCount = 0;
    var totalRows = $('#tableHanhKiem tbody tr').length;
    
    $('#tableHanhKiem tbody tr').each(function(index) {
        var row = $(this);
        setTimeout(function() {
            saveRow(row.find('.save-btn')[0]);
            savedCount++;
            
            if (savedCount === totalRows) {
                showToast('success', 'Đã lưu tất cả điểm thành công!');
            }
        }, index * 100); // Delay để tránh quá tải server
    });
}

function deleteHanhKiem(maHanhKiem, maHocSinh) {
    if (!confirm('Bạn có chắc muốn xóa điểm hạnh kiểm này?')) return;
    
    $.ajax({
        url: 'index.php?controller=hanhkiem&action=save',
        type: 'POST',
        data: {
            id: maHanhKiem,
            maHocSinh: maHocSinh,
            action: 'delete'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('success', response.message);
                // Reload page
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showToast('error', response.message);
            }
        },
        error: function() {
            showToast('error', 'Lỗi kết nối máy chủ');
        }
    });
}

function updateStatistics() {
    var totalScored = 0;
    var totalScore = 0;
    var totalStudents = $('#tableHanhKiem tbody tr').length;
    
    $('#tableHanhKiem tbody tr').each(function() {
        var diem = $(this).find('[data-field="diem_so"] .editable-input').val();
        if (diem !== '' && !isNaN(diem) && diem >= 0) {
            totalScored++;
            totalScore += parseInt(diem);
        }
    });
    
    var avgScore = totalScored > 0 ? (totalScore / totalScored).toFixed(1) : 0;
    
    $('#count-scored').text(totalScored);
    $('#count-not-scored').text(totalStudents - totalScored);
    $('#average-score').text(avgScore);
}

function changeHocKy(hocKy) {
    if (confirm('Thay đổi học kỳ sẽ làm mới dữ liệu. Bạn có muốn tiếp tục?')) {
        // Lưu học kỳ vào session (cần tạo endpoint để lưu)
        $.post('index.php?controller=hanhkiem&action=changeHocKy', { hoc_ky: hocKy }, function() {
            location.reload();
        });
    }
}

function showToast(type, message) {
    var toastClass = type === 'success' ? 'bg-success' : 'bg-danger';
    
    var toast = $('<div class="toast align-items-center text-white ' + toastClass + ' border-0" role="alert">' +
                  '<div class="d-flex">' +
                  '<div class="toast-body">' + message + '</div>' +
                  '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
                  '</div>' +
                  '</div>');
    
    $('#toastContainer').append(toast);
    var bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
    
    // Tự động xóa sau 3 giây
    setTimeout(function() {
        toast.remove();
    }, 3000);
}

// Tạo container cho toast
if (!$('#toastContainer').length) {
    $('body').append('<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055"></div>');
}

// Tính thống kê ban đầu
updateStatistics();
</script>

<?php require_once 'views/layouts/footer.php'; ?>