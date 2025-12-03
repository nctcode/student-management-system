<?php
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

function hienThiTrangThai($trangThai)
{
    switch ($trangThai) {
        case 'CHO_DUYET': return '<span class="badge bg-warning">Chờ duyệt</span>';
        case 'DA_DUYET': return '<span class="badge bg-success">Đã duyệt</span>';
        case 'TU_CHOI': return '<span class="badge bg-danger">Từ chối</span>';
        case 'Chờ nộp': return '<span class="badge bg-info">Chờ nộp</span>';
        default: return '<span class="badge bg-secondary">' . $trangThai . '</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lập đề thi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .main-content { margin-left: 250px; padding: 20px; }
        @media (max-width: 768px) { .main-content { margin-left:0; padding:10px; } }
        
        .card-phancong {
            border-left: 5px solid #28a745;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .card-phancong h4 {
            color: #28a745;
        }
        
        .countdown {
            font-size: 2rem;
            font-weight: bold;
            color: #dc3545;
        }
        
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php require_once 'views/layouts/header.php'; ?>
<?php require_once 'views/layouts/sidebar/giaovien.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">Lập đề thi</h2>
                <small class="text-muted">Tạo và quản lý đề thi của bạn</small>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-info" id="btnXemDeThi">
                    <i class="fas fa-list"></i> Xem đề thi đã tạo
                </button>
                <?php if (isset($phanCong) && $phanCong): ?>
                <a href="#formLapDe" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tạo đề thi ngay
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thông báo phân công -->
        <?php if (isset($phanCong) && $phanCong): ?>
        <div class="card card-phancong mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="text-success mb-3">
                            <i class="fas fa-bullhorn"></i> Bạn đã được phân công ra đề thi!
                        </h4>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Tiêu đề:</strong> <?php echo htmlspecialchars($phanCong['tieuDe']); ?></p>
                            </div>
                            <div class="col-md-2">
                                <p><strong>Khối:</strong> <?php echo htmlspecialchars($phanCong['tenKhoi']); ?></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>Môn học:</strong> <?php echo htmlspecialchars($phanCong['tenMonHoc']); ?></p>
                            </div>
                            <div class="col-md-2">
                                <p><strong>Số đề:</strong> <?php echo htmlspecialchars($phanCong['soLuongDe'] ?? 1); ?></p>
                            </div>
                            <div class="col-md-2">
                                <p><strong>Học kỳ:</strong> 
                                    <?php 
                                    // Hiển thị học kỳ đúng
                                    $maNienKhoa = $phanCong['maNienKhoa'] ?? 0;
                                    $hocKy = $phanCong['hocKy'] ?? '';
                                    $namHoc = $phanCong['namHoc'] ?? '';
                                    
                                    // Mapping học kỳ
                                    $hocKyText = '';
                                    if ($maNienKhoa == 2) {
                                        $hocKyText = 'Học kỳ 1';
                                    } elseif ($maNienKhoa == 3) {
                                        $hocKyText = 'Học kỳ 2';
                                    } elseif (!empty($hocKy)) {
                                        switch($hocKy) {
                                            case 'HK1': $hocKyText = 'Học kỳ 1'; break;
                                            case 'HK2': $hocKyText = 'Học kỳ 2'; break;
                                            case 'CA_NAM': $hocKyText = 'Cả năm'; break;
                                            default: $hocKyText = $hocKy;
                                        }
                                    }
                                    
                                    echo '<span class="badge bg-info">' . $hocKyText . '</span>';
                                    if (!empty($namHoc)) {
                                        echo '<br><small>' . $namHoc . '</small>';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        
                        <?php if (!empty($phanCong['ghiChu'])): ?>
                        <div class="alert alert-info mt-2">
                            <strong><i class="fas fa-sticky-note"></i> Ghi chú:</strong>
                            <?php echo nl2br(htmlspecialchars($phanCong['ghiChu'])); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-4 text-center">
                        <?php if (!empty($phanCong['hanNopDe'])): 
                            $deadline = new DateTime($phanCong['hanNopDe']);
                            $now = new DateTime();
                            $interval = $now->diff($deadline);
                            $daysLeft = $interval->days;
                            $hoursLeft = $interval->h;
                        ?>
                            <div class="countdown mb-2">
                                <?php if ($daysLeft > 0): ?>
                                    <?php echo $daysLeft; ?> ngày
                                <?php else: ?>
                                    <?php echo $hoursLeft; ?> giờ
                                <?php endif; ?>
                            </div>
                            <p class="mb-1"><strong>Hạn nộp:</strong></p>
                            <p class="text-danger mb-0">
                                <i class="far fa-clock"></i>
                                <?php echo date('d/m/Y H:i', strtotime($phanCong['hanNopDe'])); ?>
                            </p>
                            <?php if ($now > $deadline): ?>
                                <span class="badge bg-danger">Quá hạn</span>
                            <?php elseif ($daysLeft <= 2): ?>
                                <span class="badge bg-warning">Sắp hạn</span>
                            <?php else: ?>
                                <span class="badge bg-success">Còn thời gian</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">Không có hạn nộp cụ thể</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-warning mb-4">
            <h5><i class="fas fa-exclamation-triangle"></i> Chưa có phân công ra đề</h5>
            <p class="mb-0">Hiện tại bạn chưa được phân công tạo đề thi nào. Vui lòng chờ tổ trưởng phân công hoặc liên hệ với tổ trưởng chuyên môn.</p>
        </div>
        <?php endif; ?>

        <!-- FORM TẠO ĐỀ THI -->
        <div class="form-section" id="formLapDe">
            <h4 class="mb-4">
                <i class="fas fa-file-alt"></i> 
                <?php echo isset($phanCong) && $phanCong ? 'Tạo đề thi theo phân công' : 'Tạo đề thi tự do'; ?>
            </h4>
            
            <form method="POST" action="index.php?controller=deThi&action=store"
                  enctype="multipart/form-data" id="formDeThi">
                
                <div class="row g-3 mb-3">
                    <?php if (isset($phanCong) && $phanCong): ?>
                        <!-- Hiển thị thông tin đã có từ phân công -->
                        <input type="hidden" name="khoi" value="<?php echo $phanCong['maKhoi']; ?>">
                        <input type="hidden" name="hocKy" value="<?php echo isset($_SESSION['hocKyHienTai']) ? $_SESSION['hocKyHienTai'] : 1; ?>">
                        <input type="hidden" name="tieuDeDuocPhanCong" value="<?php echo htmlspecialchars($phanCong['tieuDe']); ?>">
                        
                        <div class="col-md-4">
                            <label class="form-label">Khối:</label>
                            <div class="form-control bg-light">
                                <i class="fas fa-check text-success"></i>
                                <?php echo htmlspecialchars($phanCong['tenKhoi']); ?>
                            </div>
                            <small class="text-muted">(Đã được phân công)</small>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Môn học:</label>
                            <div class="form-control bg-light">
                                <i class="fas fa-check text-success"></i>
                                <?php echo htmlspecialchars($phanCong['tenMonHoc']); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Học kỳ:</label>
                            <div class="form-control bg-light">
                                <i class="fas fa-check text-success"></i>
                                <?php 
                                // Hiển thị học kỳ đúng từ phân công
                                $maNienKhoa = $phanCong['maNienKhoa'] ?? 0;
                                $hocKy = $phanCong['hocKy'] ?? '';
                                
                                if ($maNienKhoa == 2) {
                                    echo 'Học kỳ 1';
                                    echo '<input type="hidden" name="hocKy" value="1">'; // Giữ giá trị cũ cho form
                                    echo '<input type="hidden" name="maNienKhoa" value="2">'; // THÊM: Lưu maNienKhoa
                                } elseif ($maNienKhoa == 3) {
                                    echo 'Học kỳ 2';
                                    echo '<input type="hidden" name="hocKy" value="2">';
                                    echo '<input type="hidden" name="maNienKhoa" value="3">'; // THÊM: Lưu maNienKhoa
                                } elseif (!empty($hocKy)) {
                                    switch($hocKy) {
                                        case 'HK1': 
                                            echo 'Học kỳ 1';
                                            echo '<input type="hidden" name="hocKy" value="1">';
                                            break;
                                        case 'HK2': 
                                            echo 'Học kỳ 2';
                                            echo '<input type="hidden" name="hocKy" value="2">';
                                            break;
                                        case 'CA_NAM': 
                                            echo 'Cả năm';
                                            echo '<input type="hidden" name="hocKy" value="3">';
                                            break;
                                        default: 
                                            echo $hocKy;
                                    }
                                } else {
                                    echo '<span class="text-warning">Chưa xác định</span>';
                                }
                                
                                // Hiển thị năm học nếu có
                                if (!empty($phanCong['namHoc'])) {
                                    echo '<br><small class="text-muted">' . htmlspecialchars($phanCong['namHoc']) . '</small>';
                                }
                                ?>
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <!-- Form tự do khi không có phân công -->
                        <div class="col-md-3">
                            <label class="form-label">Chọn khối:</label>
                            <select name="khoi" class="form-select" required>
                                <option value="">--Chọn khối--</option>
                                <?php
                                $khoiMap = [1 => '6', 2 => '7', 3 => '8', 4 => '9', 5 => '10', 6 => '11', 7 => '12'];
                                foreach ($khoiMap as $maKhoi => $tenKhoi):
                                ?>
                                    <option value="<?php echo $maKhoi; ?>">Khối <?php echo $tenKhoi; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Chọn học kỳ:</label>
                            <select name="hocKy" class="form-select" required>
                                <option value="">--Chọn học kỳ--</option>
                                <option value="1">HK1</option>
                                <option value="2">HK2</option>
                                <option value="3">Cả năm</option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tiêu đề đề thi:</label>
                        <input type="text" name="tieuDe" class="form-control" 
                               placeholder="Ví dụ: Đề kiểm tra 1 tiết chương 1" 
                               value="<?php echo isset($phanCong) && $phanCong ? htmlspecialchars($phanCong['tieuDe']) : ''; ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tải lên file đề thi:</label>
                        <div class="input-group">
                            <input type="file" name="fileDeThi" class="form-control" 
                                   accept=".pdf,.doc,.docx,.jpg,.png" required>
                            <span class="input-group-text">
                                <i class="fas fa-upload"></i>
                            </span>
                        </div>
                        <small class="text-muted">Chấp nhận: PDF, Word, JPG, PNG (tối đa 10MB)</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung/ghi chú bổ sung:</label>
                    <textarea name="noiDungBoSung" class="form-control" rows="3" 
                              placeholder="Nhập nội dung yêu cầu chi tiết hoặc ghi chú bổ sung (nếu có)"></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <button type="submit" class="btn btn-success me-2">
                            <i class="fas fa-save"></i> 
                            <?php echo isset($phanCong) && $phanCong ? 'Nộp đề thi' : 'Tạo đề thi'; ?>
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Làm mới
                        </button>
                    </div>
                    
                    <?php if (isset($phanCong) && $phanCong && !empty($phanCong['hanNopDe'])): ?>
                    <div class="text-end">
                        <small class="text-muted">
                            <i class="far fa-clock"></i>
                            Hạn nộp: <?php echo date('d/m/Y H:i', strtotime($phanCong['hanNopDe'])); ?>
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- DANH SÁCH ĐỀ THI ĐÃ TẠO -->
        <div class="card mt-4" id="danhSachDeThiContainer" style="display:none;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list-check"></i> Đề thi đã tạo</h5>
                <button type="button" class="btn btn-light btn-sm" id="btnDongDanhSach">
                    <i class="fas fa-times"></i> Đóng
                </button>
            </div>
            <div class="card-body">
                <?php if (!empty($deThiList)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đề</th>
                                    <th>Tiêu đề</th>
                                    <th>Môn học</th>
                                    <th>Học kỳ</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày nộp</th>
                                    <th>File</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deThiList as $deThi): ?>
                                    <tr>
                                        <td>#<?= $deThi['maDeThi'] ?></td>
                                        <td><?= htmlspecialchars($deThi['tieuDe']) ?></td>
                                        <td><?= htmlspecialchars($deThi['monHoc']) ?></td>
                                        <td>
                                            <?php 
                                            // Hiển thị học kỳ
                                            $maNienKhoa = $deThi['maNienKhoa'] ?? 0;
                                            $hocKy = $deThi['hocKy'] ?? '';
                                            
                                            if ($maNienKhoa == 2) {
                                                echo '<span class="badge bg-primary">HK1</span>';
                                            } elseif ($maNienKhoa == 3) {
                                                echo '<span class="badge bg-success">HK2</span>';
                                            } elseif (!empty($hocKy)) {
                                                echo '<span class="badge bg-info">' . $hocKy . '</span>';
                                            }
                                            
                                            if (!empty($deThi['namHoc'])) {
                                                echo '<br><small>' . $deThi['namHoc'] . '</small>';
                                            }
                                            ?>
                                        </td>
                                        <td><?= hienThiTrangThai($deThi['trangThai']) ?></td>
                                        <td>
                                            <?php 
                                            $ngayNop = !empty($deThi['ngayNop']) ? 
                                                date('d/m/Y', strtotime($deThi['ngayNop'])) : 
                                                'Chưa nộp';
                                            echo $ngayNop;
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($deThi['fileDeThi'])): ?>
                                                <a href="uploads/dethi/<?= htmlspecialchars($deThi['fileDeThi']) ?>" 
                                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Tải
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-info" onclick="xemChiTiet(<?= $deThi['maDeThi'] ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if ($deThi['trangThai'] == 'CHO_DUYET' || $deThi['trangThai'] == 'TU_CHOI'): ?>
                                                    <a href="index.php?controller=deThi&action=edit&id=<?= $deThi['maDeThi'] ?>" 
                                                    class="btn btn-warning" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Nút xóa với modal -->
                                                    <button type="button" class="btn btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal<?= $deThi['maDeThi'] ?>"
                                                            title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                    <!-- Modal xác nhận xóa -->
                                                    <div class="modal fade" id="deleteModal<?= $deThi['maDeThi'] ?>" tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title">
                                                                        <i class="fas fa-exclamation-triangle"></i> Xác nhận xóa
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Bạn có chắc chắn muốn xóa đề thi <strong>"<?= htmlspecialchars($deThi['tieuDe']) ?>"</strong> không?</p>
                                                                    <div class="alert alert-warning">
                                                                        <small>
                                                                            <i class="fas fa-info-circle"></i>
                                                                            Chỉ có thể xóa đề thi đang ở trạng thái "Chờ duyệt" hoặc "Từ chối"
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                    <form action="index.php?controller=deThi&action=delete" method="POST" style="display: inline;">
                                                                        <input type="hidden" name="id" value="<?= $deThi['maDeThi'] ?>">
                                                                        <input type="hidden" name="_token" value="<?php 
                                                                            if (session_status() === PHP_SESSION_NONE) session_start();
                                                                            echo $_SESSION['csrf_token'] ?? ''; 
                                                                        ?>">
                                                                        <button type="submit" class="btn btn-danger">
                                                                            <i class="fas fa-check"></i> Xác nhận xóa
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có đề thi nào được tạo</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- MODAL THÔNG BÁO -->
        <?php if ($message): ?>
        <div class="modal fade" id="thongBaoModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header <?= ($message['status']=='success')?'bg-success':'bg-danger' ?> text-white">
                        <h5 class="modal-title">
                            <i class="fas <?= ($message['status']=='success')?'fa-check-circle':'fa-exclamation-triangle' ?>"></i>
                            Thông báo
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <?= htmlspecialchars($message['text']) ?>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Hiển thị/ẩn danh sách đề thi
document.getElementById('btnXemDeThi').addEventListener('click', function() {
    const container = document.getElementById('danhSachDeThiContainer');
    if (container.style.display === 'none') {
        container.style.display = 'block';
        window.scrollTo({ top: container.offsetTop, behavior: 'smooth' });
    } else {
        container.style.display = 'none';
    }
});

document.getElementById('btnDongDanhSach').addEventListener('click', function() {
    document.getElementById('danhSachDeThiContainer').style.display = 'none';
});

// Kiểm tra file upload
document.getElementById('formDeThi').addEventListener('submit', function(e) {
    const fileInput = document.querySelector('input[name="fileDeThi"]');
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        
        // Kiểm tra kích thước
        if (file.size > maxSize) {
            e.preventDefault();
            alert('File không được vượt quá 10MB');
            return false;
        }
        
        // Kiểm tra định dạng
        const allowedExt = ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png'];
        const fileExt = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!allowedExt.includes(fileExt)) {
            e.preventDefault();
            alert('Chỉ chấp nhận file PDF, Word hoặc hình ảnh');
            return false;
        }
    }
    
    return true;
});

// Xem chi tiết đề thi
function xemChiTiet(maDeThi) {
    window.open('index.php?controller=deThi&action=view&id=' + maDeThi, '_blank');
}

// Hiển thị modal thông báo nếu có
<?php if ($message): ?>
    var modal = new bootstrap.Modal(document.getElementById('thongBaoModal'));
    modal.show();
<?php endif; ?>

// Auto-scroll tới form nếu có phân công
<?php if (isset($phanCong) && $phanCong): ?>
    window.addEventListener('load', function() {
        const formSection = document.getElementById('formLapDe');
        if (formSection) {
            setTimeout(function() {
                formSection.scrollIntoView({ behavior: 'smooth' });
            }, 500);
        }
    });
<?php endif; ?>
</script>
</body>
</html>