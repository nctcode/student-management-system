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
    
    /* Modal nhập nhanh */
    .modal-xl {
        max-width: 90%;
    }
    
    .quick-input-form .form-group {
        margin-bottom: 1rem;
    }
    
    .quick-input-form label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    /* Table styles */
    .editable-cell {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .editable-cell:hover {
        background-color: #f8f9fa;
    }
    
    .editable-cell.editing {
        background-color: #fff3cd !important;
    }
    
    .editable-input {
        width: 100%;
        border: 2px solid #007bff !important;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 14px;
    }
    
    .diem-input {
        width: 80px;
        text-align: center;
    }
    
    .nhan-xet-input {
        width: 100%;
        min-height: 60px;
    }
    
    /* Badge styles */
    .badge-xs {
        font-size: 0.75em;
        padding: 2px 6px;
    }
    
    /* Toolbar */
    .toolbar {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }
    
    /* Action buttons */
    .btn-action {
        padding: 4px 8px;
        font-size: 12px;
        margin: 0 2px;
    }
    
    /* Statistics cards */
    .stat-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    /* Toast notification */
    #toastContainer {
        z-index: 9999;
    }
    
    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .table {
            border-collapse: collapse;
        }
        
        .table th, .table td {
            border: 1px solid #000;
            padding: 5px;
        }
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
            <div class="d-flex gap-2 no-print">
                <!-- Chọn học kỳ -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-calendar-alt"></i> 
                        Học kỳ: <?= htmlspecialchars($_SESSION['hoc_ky_hien_tai'] ?? 'HK1-' . date('Y')) ?>
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
                
                <!-- Nút nhập nhanh -->
                <button class="btn btn-success" onclick="openNhapNhanhModal()">
                    <i class="fas fa-bolt"></i> Nhập nhanh
                </button>
                
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

        <!-- Toolbar -->
        <div class="toolbar no-print">
            <div class="row">
                <div class="col-md-8">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="enableQuickEdit()">
                            <i class="fas fa-edit"></i> Chỉnh sửa nhanh
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="saveAll()">
                            <i class="fas fa-save"></i> Lưu tất cả
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="clearAllScores()">
                            <i class="fas fa-trash"></i> Xóa tất cả điểm
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> In báo cáo
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" 
                               placeholder="Tìm kiếm học sinh..." id="searchInput">
                        <button class="btn btn-sm btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white stat-card">
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
                <div class="card bg-success text-white stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Đã chấm điểm</h6>
                                <h2 class="mb-0" id="count-scored"><?= $daCham ?? 0 ?></h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Chưa chấm</h6>
                                <h2 class="mb-0" id="count-not-scored"><?= $chuaCham ?? 0 ?></h2>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Điểm TB lớp</h6>
                                <h2 class="mb-0" id="average-score"><?= $diemTB ?? 0 ?></h2>
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
                <div class="d-flex gap-2 no-print">
                    <button class="btn btn-sm btn-outline-primary" onclick="exportExcel()">
                        <i class="fas fa-file-excel"></i> Xuất Excel
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="copyTable()">
                        <i class="fas fa-copy"></i> Sao chép
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
                                    <th width="120">Điểm số (0-100)</th>
                                    <th width="120">Xếp loại</th>
                                    <th>Nhận xét</th>
                                    <th width="150">Cập nhật</th>
                                    <th width="120" class="no-print">Thao tác</th>
                                </tr>
                            </thead>
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
                                    <td class="editable-cell" data-field="diem_so">
                                        <div class="diem-display">
                                            <?php if (!empty($hs['diem_so']) || $hs['diem_so'] === 0): ?>
                                                <span class="diem-value"><?= $hs['diem_so'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa nhập</span>
                                            <?php endif; ?>
                                        </div>
                                        <input type="number" class="form-control form-control-sm diem-input editable-input d-none" 
                                               min="0" max="100" step="0.5"
                                               value="<?= !empty($hs['diem_so']) || $hs['diem_so'] === 0 ? $hs['diem_so'] : '' ?>" 
                                               placeholder="Nhập điểm"
                                               data-old-value="<?= !empty($hs['diem_so']) || $hs['diem_so'] === 0 ? $hs['diem_so'] : '' ?>">
                                    </td>
                                    <td class="xep-loai-cell">
                                        <?php if (!empty($hs['xep_loai'])): ?>
                                            <?php
                                            $badgeClass = 'bg-secondary';
                                            switch($hs['xep_loai']) {
                                                case 'Xuất sắc': $badgeClass = 'bg-success'; break;
                                                case 'Tốt': $badgeClass = 'bg-primary'; break;
                                                case 'Khá': $badgeClass = 'bg-info'; break;
                                                case 'Trung bình': $badgeClass = 'bg-warning'; break;
                                                case 'Yếu': $badgeClass = 'bg-danger'; break;
                                                default: $badgeClass = 'bg-light text-dark'; break;
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= $hs['xep_loai'] ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark">Chưa xếp loại</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="editable-cell" data-field="nhan_xet">
                                        <div class="nhan-xet-display">
                                            <?php if (!empty($hs['nhan_xet'])): ?>
                                                <?php 
                                                $displayText = htmlspecialchars($hs['nhan_xet']);
                                                if (strlen($displayText) > 50) {
                                                    echo '<span title="' . htmlspecialchars($hs['nhan_xet']) . '">' 
                                                         . substr($displayText, 0, 50) . '...</span>';
                                                } else {
                                                    echo $displayText;
                                                }
                                                ?>
                                            <?php else: ?>
                                                <span class="text-muted">Chưa có nhận xét</span>
                                            <?php endif; ?>
                                        </div>
                                        <textarea class="form-control form-control-sm nhan-xet-input editable-input d-none" 
                                                  rows="2" placeholder="Nhập nhận xét"
                                                  data-old-value="<?= htmlspecialchars($hs['nhan_xet'] ?? '') ?>"><?= htmlspecialchars($hs['nhan_xet'] ?? '') ?></textarea>
                                    </td>
                                    <td>
                                        <?php if (!empty($hs['created_at'])): ?>
                                            <?= date('d/m/Y H:i', strtotime($hs['created_at'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa cập nhật</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="no-print">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success btn-action" onclick="saveRow(this)" title="Lưu">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            <button class="btn btn-info btn-action" onclick="editRow(this)" title="Sửa nhanh">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if (!empty($hs['maHanhKiem'])): ?>
                                            <button class="btn btn-danger btn-action" onclick="deleteHanhKiem(<?= $hs['maHanhKiem'] ?>, <?= $hs['maHocSinh'] ?>)" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Hướng dẫn sử dụng -->
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-lightbulb"></i> Hướng dẫn sử dụng nhanh:</h6>
                        <ul class="mb-0">
                            <li><strong>Nhấp đúp</strong> vào ô Điểm số hoặc Nhận xét để chỉnh sửa trực tiếp</li>
                            <li>Nhấn <kbd>Enter</kbd> để lưu, <kbd>ESC</kbd> để hủy khi đang chỉnh sửa</li>
                            <li>Dùng nút <strong>"Nhập nhanh"</strong> để nhập điểm cho nhiều học sinh cùng lúc</li>
                            <li>Dùng nút <strong>"Chỉnh sửa nhanh"</strong> để kích hoạt chế độ sửa hàng loạt</li>
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

<!-- Modal nhập nhanh -->
<div class="modal fade" id="nhapNhanhModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-bolt"></i> Nhập điểm hạnh kiểm nhanh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="quick-input-form">
                            <div class="mb-3">
                                <label>Chọn học sinh:</label>
                                <select class="form-control" id="selectHocSinh">
                                    <option value="">-- Chọn học sinh --</option>
                                    <?php foreach ($dsHanhKiem as $hs): ?>
                                    <option value="<?= $hs['maHocSinh'] ?>">
                                        <?= htmlspecialchars($hs['hoTen']) ?> (HS<?= $hs['maHocSinh'] ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label>Điểm số (0-100):</label>
                                <input type="number" class="form-control" id="quickDiemSo" 
                                       min="0" max="100" step="0.5" placeholder="Nhập điểm">
                            </div>
                            
                            <div class="mb-3">
                                <label>Hoặc chọn mức điểm:</label>
                                <div class="btn-group w-100">
                                    <button type="button" class="btn btn-outline-success" onclick="setQuickScore(90)">90</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setQuickScore(80)">80</button>
                                    <button type="button" class="btn btn-outline-info" onclick="setQuickScore(65)">65</button>
                                    <button type="button" class="btn btn-outline-warning" onclick="setQuickScore(50)">50</button>
                                    <button type="button" class="btn btn-outline-danger" onclick="setQuickScore(40)">40</button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label>Nhận xét:</label>
                                <textarea class="form-control" id="quickNhanXet" rows="3" placeholder="Nhập nhận xét"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label>Nhận xét mẫu:</label>
                                <select class="form-control" id="sampleComments" onchange="applySampleComment()">
                                    <option value="">-- Chọn nhận xét mẫu --</option>
                                    <option value="Có ý thức tốt, chấp hành nội quy">Có ý thức tốt, chấp hành nội quy</option>
                                    <option value="Tích cực tham gia hoạt động tập thể">Tích cực tham gia hoạt động tập thể</option>
                                    <option value="Cần cố gắng hơn trong việc chấp hành nội quy">Cần cố gắng hơn trong việc chấp hành nội quy</option>
                                    <option value="Đạo đức tốt, lễ phép với thầy cô">Đạo đức tốt, lễ phép với thầy cô</option>
                                    <option value="Cần rèn luyện tính kỷ luật">Cần rèn luyện tính kỷ luật</option>
                                </select>
                            </div>
                            
                            <button class="btn btn-primary w-100" onclick="saveQuickInput()">
                                <i class="fas fa-save"></i> Lưu điểm
                            </button>
                            <button class="btn btn-success w-100 mt-2" onclick="saveAndNext()">
                                <i class="fas fa-forward"></i> Lưu và tiếp tục
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <h6>Danh sách điểm đã nhập:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Họ tên</th>
                                        <th>Điểm</th>
                                        <th>Xếp loại</th>
                                        <th>Nhận xét</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="quickInputList">
                                    <!-- Danh sách điểm sẽ được thêm ở đây -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <button class="btn btn-success" onclick="saveAllQuickInput()">
                                <i class="fas fa-paper-plane"></i> Lưu tất cả
                            </button>
                            <button class="btn btn-danger" onclick="clearQuickInput()">
                                <i class="fas fa-trash"></i> Xóa tất cả
                            </button>
                        </div>
                    </div>
                </div>
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
                    <li><strong>Chấp hành nội quy</strong> (20 điểm)</li>
                    <li><strong>Ý thức học tập</strong> (20 điểm)</li>
                    <li><strong>Đạo đức, lối sống</strong> (20 điểm)</li>
                    <li><strong>Hoạt động tập thể</strong> (20 điểm)</li>
                    <li><strong>Quan hệ với bạn bè, thầy cô</strong> (20 điểm)</li>
                </ul>
                <div class="alert alert-warning">
                    <small><i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong> Mỗi học sinh chỉ có 1 điểm hạnh kiểm cho mỗi học kỳ. Hệ thống sẽ tự động cập nhật nếu đã có điểm.</small>
                </div>
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
        var table = $('#tableHanhKiem').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
            },
            pageLength: 25,
            order: [[1, 'asc']],
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [8] },
                { searchable: false, targets: [0, 3, 4, 5, 7, 8] }
            ]
        });
        
        // Tìm kiếm
        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });
    }
    
    // Tính toán thống kê ban đầu
    updateStatistics();
    
    // Xử lý nhấp đúp để chỉnh sửa
    $(document).on('dblclick', '.editable-cell', function(e) {
        if ($(e.target).is('input, textarea, select, button')) return;
        
        var cell = $(this);
        if (cell.find('.editable-input').is(':visible')) return;
        
        enterEditMode(cell);
    });
    
    // Xử lý click nút sửa
    $(document).on('click', '.btn-edit-row', function() {
        var row = $(this).closest('tr');
        var field = $(this).data('field');
        var cell = row.find(`[data-field="${field}"]`);
        enterEditMode(cell);
    });
});

// Biến lưu trữ dữ liệu nhập nhanh
var quickInputData = [];

function enterEditMode(cell) {
    var input = cell.find('.editable-input');
    var display = cell.find('.diem-display, .nhan-xet-display');
    
    display.addClass('d-none');
    input.removeClass('d-none').focus().select();
    cell.addClass('editing');
}

function exitEditMode(cell, save = true) {
    var input = cell.find('.editable-input');
    var display = cell.find('.diem-display, .nhan-xet-display');
    var field = cell.data('field');
    var oldValue = input.data('old-value');
    var newValue = input.val().trim();
    
    if (save && newValue != oldValue) {
        if (field === 'diem_so' && newValue !== '') {
            var diem = parseFloat(newValue);
            if (isNaN(diem) || diem < 0 || diem > 100) {
                showToast('error', 'Điểm số phải từ 0 đến 100');
                input.focus();
                return;
            }
            newValue = diem;
        }
        
        // Cập nhật hiển thị
        updateDisplay(cell, field, newValue);
        
        // Cập nhật old value
        input.data('old-value', newValue);
        
        // Tự động lưu
        autoSaveRow(cell.closest('tr'));
    } else {
        // Khôi phục giá trị cũ
        if (field === 'diem_so') {
            display.find('.diem-value').text(oldValue || '');
            display.find('.text-muted').toggle(!oldValue && oldValue !== 0);
        } else {
            var displayText = oldValue ? (oldValue.length > 50 ? oldValue.substring(0, 50) + '...' : oldValue) : '';
            display.html(oldValue ? `<span title="${oldValue}">${displayText}</span>` : '<span class="text-muted">Chưa có nhận xét</span>');
        }
    }
    
    input.addClass('d-none');
    display.removeClass('d-none');
    cell.removeClass('editing');
}

function updateDisplay(cell, field, value) {
    var display = cell.find('.diem-display, .nhan-xet-display');
    
    if (field === 'diem_so') {
        if (value === '' || value === null) {
            display.html('<span class="text-muted">Chưa nhập</span>');
            cell.closest('tr').find('.xep-loai-cell').html('<span class="badge bg-light text-dark">Chưa xếp loại</span>');
        } else {
            display.html(`<span class="diem-value">${value}</span>`);
            updateXepLoai(cell.closest('tr'), value);
        }
    } else {
        if (value === '') {
            display.html('<span class="text-muted">Chưa có nhận xét</span>');
        } else {
            var displayText = value.length > 50 ? value.substring(0, 50) + '...' : value;
            display.html(`<span title="${value}">${displayText}</span>`);
        }
    }
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
    
    row.find('.xep-loai-cell').html(`<span class="badge ${badgeClass}">${xepLoai}</span>`);
}

// Tự động lưu khi thay đổi
function autoSaveRow(row) {
    var maHocSinh = row.data('ma-hs');
    var diemInput = row.find('[data-field="diem_so"] .editable-input');
    var nhanXetInput = row.find('[data-field="nhan_xet"] .editable-input');
    
    var diemSo = diemInput.val();
    var nhanXet = nhanXetInput.val();
    var hocKy = '<?= $_SESSION["hoc_ky_hien_tai"] ?? "HK1-" . date("Y") ?>';
    
    // Chỉ lưu nếu có thay đổi
    if (diemInput.data('old-value') != diemSo || nhanXetInput.data('old-value') != nhanXet) {
        if (diemSo !== '' && (isNaN(diemSo) || diemSo < 0 || diemSo > 100)) {
            return;
        }
        
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
                    // Cập nhật ngày cập nhật
                    var now = new Date();
                    var dateStr = now.getDate().toString().padStart(2, '0') + '/' + 
                                 (now.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                                 now.getFullYear() + ' ' + 
                                 now.getHours().toString().padStart(2, '0') + ':' + 
                                 now.getMinutes().toString().padStart(2, '0');
                    
                    row.find('td:eq(7)').html(dateStr);
                }
                updateStatistics();
            },
            error: function() {
                showToast('error', 'Lỗi kết nối máy chủ');
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
    var hocKy = '<?= $_SESSION["hoc_ky_hien_tai"] ?? "HK1-" . date("Y") ?>';
    
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
            updateStatistics();
        },
        error: function() {
            showToast('error', 'Lỗi kết nối máy chủ');
            $(button).prop('disabled', false).html('<i class="fas fa-save"></i>');
        }
    });
}

function editRow(button) {
    var row = $(button).closest('tr');
    var diemCell = row.find('[data-field="diem_so"]');
    enterEditMode(diemCell);
}

function saveAll() {
    if (!confirm('Bạn có chắc muốn lưu tất cả điểm đã chỉnh sửa?')) return;
    
    var rows = $('#tableHanhKiem tbody tr');
    var savePromises = [];
    
    rows.each(function() {
        var row = $(this);
        var maHocSinh = row.data('ma-hs');
        var diemInput = row.find('[data-field="diem_so"] .editable-input');
        var nhanXetInput = row.find('[data-field="nhan_xet"] .editable-input');
        
        var diemSo = diemInput.val();
        var nhanXet = nhanXetInput.val();
        var hocKy = '<?= $_SESSION["hoc_ky_hien_tai"] ?? "HK1-" . date("Y") ?>';
        
        if (diemInput.data('old-value') != diemSo || nhanXetInput.data('old-value') != nhanXet) {
            savePromises.push(
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
                    dataType: 'json'
                })
            );
        }
    });
    
    if (savePromises.length === 0) {
        showToast('info', 'Không có thay đổi nào để lưu');
        return;
    }
    
    $('.save-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    
    Promise.all(savePromises).then(function(responses) {
        var successCount = responses.filter(r => r.success).length;
        showToast('success', `Đã lưu thành công ${successCount}/${savePromises.length} học sinh`);
        
        // Cập nhật ngày cập nhật
        var now = new Date();
        var dateStr = now.getDate().toString().padStart(2, '0') + '/' + 
                     (now.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                     now.getFullYear() + ' ' + 
                     now.getHours().toString().padStart(2, '0') + ':' + 
                     now.getMinutes().toString().padStart(2, '0');
        
        $('#tableHanhKiem tbody tr').each(function() {
            $(this).find('td:eq(7)').html(dateStr);
        });
        
    }).catch(function(error) {
        showToast('error', 'Lỗi khi lưu dữ liệu');
    }).finally(function() {
        $('.save-btn').prop('disabled', false).html('<i class="fas fa-save"></i>');
        updateStatistics();
    });
}

function clearAllScores() {
    if (!confirm('Bạn có chắc muốn xóa tất cả điểm hạnh kiểm? Hành động này không thể hoàn tác!')) return;
    
    $.ajax({
        url: 'index.php?controller=hanhkiem&action=clearAll',
        type: 'POST',
        data: {
            hoc_ky: '<?= $_SESSION["hoc_ky_hien_tai"] ?? "HK1-" . date("Y") ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('success', response.message);
                location.reload();
            } else {
                showToast('error', response.message);
            }
        },
        error: function() {
            showToast('error', 'Lỗi kết nối máy chủ');
        }
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
                // Xóa hàng khỏi bảng
                $(`tr[data-ma-hk="${maHanhKiem}"]`).fadeOut(300, function() {
                    $(this).remove();
                    updateStatistics();
                });
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
            totalScore += parseFloat(diem);
        }
    });
    
    var avgScore = totalScored > 0 ? (totalScore / totalScored).toFixed(1) : 0;
    
    $('#count-scored').text(totalScored);
    $('#count-not-scored').text(totalStudents - totalScored);
    $('#average-score').text(avgScore);
}

function changeHocKy(hocKy) {
    if (confirm('Thay đổi học kỳ sẽ làm mới dữ liệu. Bạn có muốn tiếp tục?')) {
        $.post('index.php?controller=hanhkiem&action=changeHocKy', { hoc_ky: hocKy }, function() {
            location.reload();
        });
    }
}

// Chức năng nhập nhanh
function openNhapNhanhModal() {
    $('#nhapNhanhModal').modal('show');
    loadQuickInputList();
}

function setQuickScore(score) {
    $('#quickDiemSo').val(score);
}

function applySampleComment() {
    var comment = $('#sampleComments').val();
    if (comment) {
        $('#quickNhanXet').val(comment);
    }
}

function saveQuickInput() {
    var maHocSinh = $('#selectHocSinh').val();
    var diemSo = $('#quickDiemSo').val();
    var nhanXet = $('#quickNhanXet').val();
    
    if (!maHocSinh) {
        showToast('error', 'Vui lòng chọn học sinh');
        return;
    }
    
    // Lấy thông tin học sinh
    var studentName = $('#selectHocSinh option:selected').text();
    
    // Tính xếp loại
    var xepLoai = 'Chưa xếp loại';
    if (diemSo !== '') {
        var diem = parseFloat(diemSo);
        if (diem >= 90) xepLoai = 'Xuất sắc';
        else if (diem >= 80) xepLoai = 'Tốt';
        else if (diem >= 65) xepLoai = 'Khá';
        else if (diem >= 50) xepLoai = 'Trung bình';
        else if (diem >= 0) xepLoai = 'Yếu';
    }
    
    // Thêm vào danh sách
    quickInputData.push({
        maHocSinh: maHocSinh,
        hoTen: studentName,
        diem_so: diemSo,
        xep_loai: xepLoai,
        nhan_xet: nhanXet
    });
    
    loadQuickInputList();
    showToast('success', 'Đã thêm vào danh sách nhập');
    
    // Reset form
    $('#quickDiemSo').val('');
    $('#quickNhanXet').val('');
    $('#sampleComments').val('');
}

function saveAndNext() {
    saveQuickInput();
    
    // Chọn học sinh tiếp theo
    var currentIndex = $('#selectHocSinh option:selected').index();
    var nextIndex = currentIndex + 1;
    var totalOptions = $('#selectHocSinh option').length;
    
    if (nextIndex < totalOptions) {
        $('#selectHocSinh').prop('selectedIndex', nextIndex);
    }
    
    $('#quickDiemSo').focus();
}

function loadQuickInputList() {
    var html = '';
    quickInputData.forEach(function(item, index) {
        var badgeClass = 'bg-light text-dark';
        if (item.xep_loai === 'Xuất sắc') badgeClass = 'bg-success';
        else if (item.xep_loai === 'Tốt') badgeClass = 'bg-primary';
        else if (item.xep_loai === 'Khá') badgeClass = 'bg-info';
        else if (item.xep_loai === 'Trung bình') badgeClass = 'bg-warning';
        else if (item.xep_loai === 'Yếu') badgeClass = 'bg-danger';
        
        html += `
        <tr>
            <td>${item.hoTen}</td>
            <td>${item.diem_so || 'Chưa nhập'}</td>
            <td><span class="badge ${badgeClass}">${item.xep_loai}</span></td>
            <td>${item.nhan_xet || ''}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="removeQuickInput(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
        `;
    });
    
    $('#quickInputList').html(html || '<tr><td colspan="5" class="text-center">Chưa có dữ liệu</td></tr>');
}

function removeQuickInput(index) {
    quickInputData.splice(index, 1);
    loadQuickInputList();
}

function clearQuickInput() {
    if (quickInputData.length === 0) return;
    if (confirm('Bạn có chắc muốn xóa tất cả dữ liệu nhập nhanh?')) {
        quickInputData = [];
        loadQuickInputList();
    }
}

function saveAllQuickInput() {
    if (quickInputData.length === 0) {
        showToast('info', 'Không có dữ liệu để lưu');
        return;
    }
    
    $.ajax({
        url: 'index.php?controller=hanhkiem&action=nhapHangLoat',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            hoc_ky: '<?= $_SESSION["hoc_ky_hien_tai"] ?? "HK1-" . date("Y") ?>',
            ds_diem: quickInputData
        }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast('success', response.message);
                quickInputData = [];
                loadQuickInputList();
                $('#nhapNhanhModal').modal('hide');
                location.reload();
            } else {
                showToast('error', response.message);
            }
        },
        error: function() {
            showToast('error', 'Lỗi kết nối máy chủ');
        }
    });
}

function enableQuickEdit() {
    $('.editable-cell').addClass('quick-edit-mode');
    showToast('info', 'Đã bật chế độ chỉnh sửa nhanh. Nhấp đúp vào ô để chỉnh sửa.');
}

function exportExcel() {
    // Tạo bảng tạm để xuất Excel
    var table = $('#tableHanhKiem').clone();
    table.find('.no-print').remove();
    table.find('.btn-action').remove();
    
    var html = `
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #000; padding: 5px; }
            th { background-color: #f2f2f2; }
        </style>
    </head>
    <body>
        <h3>Bảng điểm hạnh kiểm - Học kỳ <?= $_SESSION['hoc_ky_hien_tai'] ?? 'HK1-' . date('Y') ?></h3>
        ${table[0].outerHTML}
    </body>
    </html>
    `;
    
    var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'HanhKiem_<?= date("Y-m-d") ?>.xls';
    a.click();
}

function copyTable() {
    var table = $('#tableHanhKiem').clone();
    table.find('.no-print').remove();
    table.find('.btn-action').remove();
    
    var $temp = $("<textarea>");
    $("body").append($temp);
    $temp.val(table[0].outerHTML).select();
    document.execCommand("copy");
    $temp.remove();
    
    showToast('success', 'Đã sao chép bảng vào clipboard');
}

function showToast(type, message) {
    var toastClass = type === 'success' ? 'bg-success' : 
                     type === 'error' ? 'bg-danger' : 
                     type === 'warning' ? 'bg-warning' : 'bg-info';
    
    var toast = $(`
        <div class="toast align-items-center text-white ${toastClass} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    $('#toastContainer').append(toast);
    var bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
    
    setTimeout(function() {
        toast.remove();
    }, 3000);
}

// Tạo container cho toast nếu chưa có
if (!$('#toastContainer').length) {
    $('body').append('<div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055"></div>');
}

// Xử lý phím tắt
$(document).keydown(function(e) {
    // ESC để thoát chế độ edit
    if (e.key === 'Escape') {
        $('.editable-cell.editing').each(function() {
            exitEditMode($(this), false);
        });
    }
    
    // Ctrl+S để lưu tất cả
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        saveAll();
    }
    
    // Ctrl+F để tìm kiếm
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        $('#searchInput').focus();
    }
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>