<!--  -->
<?php
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

function hienThiTrangThai($trangThai)
{
    $statusClass = [
        'CHO_DUYET' => 'badge bg-warning',
        'DA_DUYET' => 'badge bg-success',
        'TU_CHOI' => 'badge bg-danger',
        'Chờ nộp' => 'badge bg-info',
        'Đã nộp' => 'badge bg-primary'
    ];

    $statusText = [
        'CHO_DUYET' => 'Chờ duyệt',
        'DA_DUYET' => 'Đã duyệt',
        'TU_CHOI' => 'Từ chối',
        'Chờ nộp' => 'Chờ nộp',
        'Đã nộp' => 'Đã nộp'
    ];

    $class = $statusClass[$trangThai] ?? 'badge bg-secondary';
    $text = $statusText[$trangThai] ?? $trangThai;

    return '<span class="' . $class . '">' . $text . '</span>';
}

// Lấy danh sách môn học (cần thêm vào controller)
$monHocList = $monHocList ?? [];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyệt đề thi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
        }

        .card-filter {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .card-exam {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }

        .card-exam:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .exam-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .btn-action {
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 500;
        }

        .file-preview {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            border: 1px dashed #dee2e6;
        }

        .count-badge {
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <?php
    require_once 'views/layouts/header.php';
    require_once 'views/layouts/sidebar/totruong.php';
    ?>

    <div class="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Duyệt đề thi</h2>
                    <small class="text-muted">Phê duyệt đề thi từ giáo viên trong tổ chuyên môn</small>
                </div>
                <div class="btn-group">
                    <button class="btn btn-info" onclick="window.print()">
                        <i class="fas fa-print"></i> In danh sách
                    </button>
                    <a href="index.php?controller=dethi&action=lichSuDuyetDeThi" class="btn btn-secondary">
                        <i class="fas fa-history"></i> Lịch sử duyệt
                    </a>
                </div>
            </div>

            <!-- Thông báo -->
            <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo $message['status'] == 'success' ? 'success' : 'danger'; ?> 
            alert-dismissible fade show" role="alert">
                    <i class="fas <?php echo $message['status'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?>"></i>
                    <?php echo htmlspecialchars($message['text']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Bộ lọc -->
            <div class="card card-filter mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Bộ lọc đề thi</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <input type="hidden" name="controller" value="dethi">
                        <input type="hidden" name="action" value="duyet">

                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-layer-group"></i> Khối học</label>
                            <select name="maKhoi" class="form-select">
                                <option value="">-- Tất cả khối --</option>
                                <?php foreach ($khoiHocList as $khoi):
                                    $khoiMap = [5 => '10', 6 => '11', 7 => '12', 1 => '6', 2 => '7', 3 => '8', 4 => '9'];
                                    $tenKhoiHienThi = isset($khoiMap[$khoi['maKhoi']]) ? 'Khối ' . $khoiMap[$khoi['maKhoi']] : $khoi['tenKhoi'];
                                ?>
                                    <option value="<?= $khoi['maKhoi'] ?>" <?= ($maKhoi == $khoi['maKhoi']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tenKhoiHienThi) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-calendar-alt"></i> Học kỳ</label>
                            <select name="maNienKhoa" class="form-select">
                                <option value="">-- Tất cả học kỳ --</option>
                                <?php foreach ($nienKhoaList as $nk): 
                                    // Loại bỏ học kỳ "Cả năm"
                                    if ($nk['hocKy'] === 'CA_NAM') continue;
                                ?>
                                    <option value="<?= $nk['maNienKhoa'] ?>" <?= ($maNienKhoa == $nk['maNienKhoa']) ? 'selected' : '' ?>>
                                        <?php 
                                        // Hiển thị học kỳ thân thiện
                                        $hocKyText = '';
                                        switch($nk['hocKy']) {
                                            case 'HK1': $hocKyText = 'Học kỳ 1'; break;
                                            case 'HK2': $hocKyText = 'Học kỳ 2'; break;
                                            // Đã bỏ case 'CA_NAM'
                                            default: $hocKyText = $nk['hocKy'];
                                        }
                                        echo htmlspecialchars($hocKyText) . ' - ' . htmlspecialchars($nk['namHoc'] ?? '');
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <div class="w-100">
                                <button class="btn btn-success w-100" type="submit">
                                    <i class="fas fa-search"></i> Lọc đề thi
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php if (isset($_GET['maKhoi']) || isset($_GET['maNienKhoa'])): ?>
                        <?php if ($maKhoi === '' || $maNienKhoa === ''): ?>
                            <div class="alert alert-danger mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                Vui lòng chọn đầy đủ <strong>Khối học</strong> và <strong>Học kỳ</strong>!
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>


                    <!-- Thống kê -->
                    <?php if ($maKhoi || $maNienKhoa || isset($maMonHoc)): ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="p-3 alert-light border">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <small class="text-muted">Đã chọn:</small>
                                            <div>
                                                <?php if ($maKhoi):
                                                    $selectedKhoi = [];
                                                    foreach ($khoiHocList as $khoiItem) {
                                                        if ($khoiItem['maKhoi'] == $maKhoi) {
                                                            $selectedKhoi = $khoiItem;
                                                            break;
                                                        }
                                                    }
                                                    if (!empty($selectedKhoi)): ?>
                                                        <span class="badge bg-primary"><?= htmlspecialchars($selectedKhoi['tenKhoi']) ?></span>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if ($maNienKhoa):
                                                    $selectedNK = [];
                                                    foreach ($nienKhoaList as $nienKhoaItem) {
                                                        if ($nienKhoaItem['maNienKhoa'] == $maNienKhoa) {
                                                            $selectedNK = $nienKhoaItem;
                                                            break;
                                                        }
                                                    }
                                                    if (!empty($selectedNK)): ?>
                                                        <span class="badge bg-info"><?= htmlspecialchars($selectedNK['hocKy']) ?></span>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if (isset($maMonHoc) && $maMonHoc && !empty($monHocList)):
                                                    $selectedMon = [];
                                                    foreach ($monHocList as $monItem) {
                                                        if ($monItem['maMonHoc'] == $maMonHoc) {
                                                            $selectedMon = $monItem;
                                                            break;
                                                        }
                                                    }
                                                    if (!empty($selectedMon)): ?>
                                                        <span class="badge bg-success"><?= htmlspecialchars($selectedMon['tenMonHoc']) ?></span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">Tổng đề thi:</small>
                                            <h5 class="mb-0"><?= isset($exams) ? count($exams) : 0 ?> đề</h5>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <a href="?controller=dethi&action=duyet" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-times"></i> Xóa bộ lọc
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Danh sách đề thi -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0">
                                    <i class="fas fa-list-check"></i> Danh sách đề thi ĐÃ NỘP chờ duyệt
                                    <?php if (!empty($exams)): ?>
                                        <span class="count-badge bg-warning text-dark ms-2">
                                            <?= count($exams) ?> đề
                                        </span>
                                    <?php endif; ?>
                                </h5>
                                <small class="text-muted">Chỉ hiển thị các đề thi đã được giáo viên nộp lên</small>
                            </div>
                            <div class="card-body">
                                <?php if (!$maKhoi && !$maNienKhoa && !isset($maMonHoc)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-filter fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Vui lòng chọn bộ lọc để hiển thị danh sách đề thi</h5>
                                        <p class="text-muted">Sử dụng bộ lọc bên trên để tìm đề thi đã nộp cần duyệt</p>
                                    </div>
                                <?php elseif (empty($exams)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Không có đề thi đã nộp nào phù hợp</h5>
                                        <p class="text-muted">Không tìm thấy đề thi đã nộp chờ duyệt với bộ lọc đã chọn</p>
                                        <a href="?controller=dethi&action=duyet" class="btn btn-outline-primary">
                                            <i class="fas fa-redo"></i> Thử bộ lọc khác
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover exam-table">
                                            <thead>
                                                <tr>
                                                    <th width="80">Mã đề</th>
                                                    <th width="150">Giáo viên</th>
                                                    <th>Tiêu đề</th>
                                                    <th width="120">Môn học</th>
                                                    <th width="100">Ngày nộp</th>
                                                    <th width="120">Trạng thái</th>
                                                    <th width="100">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($exams as $exam):
                                                    // Kiểm tra dữ liệu có tồn tại không
                                                    $hoTen = isset($exam['hoTen']) ? htmlspecialchars($exam['hoTen']) : 'N/A';
                                                    $monHoc = isset($exam['tenMonHoc']) ? htmlspecialchars($exam['tenMonHoc']) : (isset($exam['monHoc']) ? htmlspecialchars($exam['monHoc']) : 'N/A');
                                                ?>
                                                    <tr class="<?= ($exam['maDeThi'] == ($examDetail['maDeThi'] ?? 0)) ? 'table-active' : '' ?>">
                                                        <td>
                                                            <strong>#<?= $exam['maDeThi'] ?></strong>
                                                            <?php if (!empty($exam['ngayNop'])): ?>
                                                                <br><small class="text-success"><i class="fas fa-paper-plane"></i> Đã nộp</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                                <span><?= $hoTen ?></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="fw-medium"><?= isset($exam['tieuDe']) ? htmlspecialchars($exam['tieuDe']) : 'N/A' ?></div>
                                                            <?php if (!empty($monHoc)): ?>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-book text-secondary"></i>
                                                                    <?= $monHoc ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?= $monHoc ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($exam['ngayNop'])):
                                                                $ngayNop = new DateTime($exam['ngayNop']);
                                                                $now = new DateTime();
                                                                $interval = $now->diff($ngayNop);
                                                            ?>
                                                                <div><?= $ngayNop->format('d/m/Y') ?></div>
                                                                <small class="text-muted">
                                                                    <?= $ngayNop->format('H:i') ?>
                                                                    <?php if ($interval->days == 0): ?>
                                                                        <span class="badge bg-success">Hôm nay</span>
                                                                    <?php elseif ($interval->days <= 3): ?>
                                                                        <span class="badge bg-warning"><?= $interval->days ?> ngày</span>
                                                                    <?php endif; ?>
                                                                </small>
                                                            <?php else: ?>
                                                                <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Chưa nộp</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?= isset($exam['trangThai']) ? hienThiTrangThai($exam['trangThai']) : hienThiTrangThai('') ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="?controller=dethi&action=duyet&maKhoi=<?= $maKhoi ?>&maNienKhoa=<?= $maNienKhoa ?>&maMonHoc=<?= $maMonHoc ?? '' ?>&maDeThi=<?= $exam['maDeThi'] ?>"
                                                                    class="btn btn-outline-primary"
                                                                    title="Xem chi tiết">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <?php if (($exam['trangThai'] ?? '') == 'CHO_DUYET' && !empty($exam['ngayNop'])): ?>
                                                                    <button type="button"
                                                                        class="btn btn-outline-success"
                                                                        onclick="duyetDeThi(<?= $exam['maDeThi'] ?>)"
                                                                        title="Duyệt nhanh">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                <?php endif; ?>
                                                            </div>
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
                </div>

                <!-- Chi tiết đề thi (bên phải hoặc modal) -->
                <?php if ($examDetail):
                    // Đảm bảo dữ liệu tồn tại
                    $examDetail['hoTen'] = $examDetail['hoTen'] ?? ($examDetail['tenGiaoVien'] ?? 'N/A');
                    $examDetail['monHoc'] = $examDetail['monHoc'] ?? ($examDetail['tenMonHoc'] ?? 'N/A');
                    $examDetail['tieuDe'] = $examDetail['tieuDe'] ?? 'N/A';
                    $examDetail['noiDung'] = $examDetail['noiDung'] ?? ($examDetail['fileDeThi'] ?? '');
                    $examDetail['ngayNop'] = $examDetail['ngayNop'] ?? null;
                ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-exam">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-alt"></i> Chi tiết đề thi
                                        <span class="badge bg-light text-dark ms-2">#<?= $examDetail['maDeThi'] ?? 'N/A' ?></span>
                                    </h5>
                                    <div>
                                        <?= hienThiTrangThai($examDetail['trangThai'] ?? '') ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Thông tin cơ bản -->
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-user-tie text-primary"></i> Giáo viên</h6>
                                                    <p class="fw-medium"><?= htmlspecialchars($examDetail['hoTen']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-book text-success"></i> Môn học</h6>
                                                    <p class="fw-medium"><?= htmlspecialchars($examDetail['monHoc']) ?></p>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-calendar-day text-warning"></i> Ngày nộp</h6>
                                                    <p>
                                                        <?= !empty($examDetail['ngayNop']) ? date('d/m/Y H:i', strtotime($examDetail['ngayNop'])) : 'Chưa nộp' ?>
                                                        <?php if (!empty($examDetail['ngayNop'])):
                                                            $submitted = new DateTime($examDetail['ngayNop']);
                                                            $now = new DateTime();
                                                            $diff = $now->diff($submitted);
                                                            if ($diff->days <= 1): ?>
                                                                <span class="badge bg-success ms-2">Mới</span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-layer-group text-info"></i> Khối</h6>
                                                    <p>
                                                        <?php
                                                        $maKhoiDetail = $examDetail['maKhoi'] ?? 0;
                                                        $khoiMap = [5 => '10', 6 => '11', 7 => '12', 1 => '6', 2 => '7', 3 => '8', 4 => '9'];
                                                        echo 'Khối ' . ($khoiMap[$maKhoiDetail] ?? $maKhoiDetail);
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <h6><i class="fas fa-heading text-secondary"></i> Tiêu đề đề thi</h6>
                                                <div>
                                                    <p class="mb-0"><?= htmlspecialchars($examDetail['tieuDe']) ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- File và hành động -->
                                        <div class="col-md-6">
                                            <div class="file-preview mb-4">
                                                <h6><i class="fas fa-file-upload text-primary"></i> File đề thi</h6>
                                                <?php if (!empty($examDetail['noiDung'])):
                                                    $filePath = 'uploads/dethi/' . $examDetail['noiDung'];
                                                    $fileExists = file_exists($filePath);
                                                    $fileSize = $fileExists ? filesize($filePath) : 0;
                                                    $fileExt = !empty($examDetail['noiDung']) ? strtolower(pathinfo($examDetail['noiDung'], PATHINFO_EXTENSION)) : '';

                                                    // Icon file
                                                    $fileIcons = [
                                                        'pdf' => 'fa-file-pdf text-danger',
                                                        'doc' => 'fa-file-word text-primary',
                                                        'docx' => 'fa-file-word text-primary',
                                                        'jpg' => 'fa-file-image text-success',
                                                        'jpeg' => 'fa-file-image text-success',
                                                        'png' => 'fa-file-image text-success',
                                                        'txt' => 'fa-file-alt text-secondary',
                                                        'zip' => 'fa-file-archive text-warning',
                                                        'rar' => 'fa-file-archive text-warning'
                                                    ];
                                                    $fileIcon = isset($fileIcons[$fileExt]) ? $fileIcons[$fileExt] : 'fa-file text-secondary';

                                                    // Format tên file
                                                    $fileName = htmlspecialchars($examDetail['noiDung']);
                                                    $fileNameShort = strlen($fileName) > 30 ? substr($fileName, 0, 27) . '...' : $fileName;
                                                ?>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="me-3">
                                                            <i class="fas <?= $fileIcon ?> fa-3x"></i>
                                                        </div>
                                                        <div class="flex-grow-1" style="min-width: 0;"> <!-- Quan trọng: cho phép text overflow -->
                                                            <h6 class="mb-1 text-truncate" title="<?= $fileName ?>">
                                                                <?= $fileNameShort ?>
                                                            </h6>
                                                            <small class="text-muted d-block">
                                                                <span class="badge bg-light text-dark border">
                                                                    <?= strtoupper($fileExt) ?>
                                                                </span>
                                                                <?php if ($fileExists): ?>
                                                                    <span class="ms-2">
                                                                        <i class="fas fa-hdd"></i>
                                                                        <?= round($fileSize / 1024, 2) ?> KB
                                                                    </span>
                                                                    <span class="ms-2">
                                                                        <i class="far fa-calendar"></i>
                                                                        <?= date('d/m/Y', filemtime($filePath)) ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-danger ms-2">
                                                                        <i class="fas fa-exclamation-circle"></i> File không tồn tại
                                                                    </span>
                                                                <?php endif; ?>
                                                            </small>
                                                        </div>
                                                    </div>

                                                    <div class="d-grid gap-2">
                                                        <?php if ($fileExists): ?>
                                                            <a href="<?= $filePath ?>"
                                                                class="btn btn-outline-primary"
                                                                target="_blank">
                                                                <i class="fas fa-eye"></i> Xem trước
                                                            </a>
                                                            <a href="<?= $filePath ?>"
                                                                class="btn btn-outline-success"
                                                                download>
                                                                <i class="fas fa-download"></i> Tải xuống
                                                            </a>
                                                        <?php else: ?>
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                File không tồn tại trên máy chủ
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-center py-3">
                                                        <i class="fas fa-file-excel fa-3x text-muted mb-2"></i>
                                                        <p class="text-muted">Không có file đính kèm</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Form duyệt/từ chối -->
                                            <div class="border rounded p-3 bg-light">
                                                <h6><i class="fas fa-check-circle text-success"></i> Phê duyệt đề thi</h6>

                                                <form id="formDuyet" method="POST" action="index.php?controller=dethi&action=capNhatTrangThai">
                                                    <input type="hidden" name="maDeThi" value="<?= $examDetail['maDeThi'] ?>">
                                                    <input type="hidden" name="maKhoi" value="<?= $maKhoi ?>">
                                                    <input type="hidden" name="maNienKhoa" value="<?= $maNienKhoa ?>">
                                                    <input type="hidden" name="maMonHoc" value="<?= $maMonHoc ?? '' ?>">
                                                    <input type="hidden" id="hanhDongInput" name="hanhDong" value="">

                                                    <div class="mb-3" id="divGhiChu" style="display:none;">
                                                        <label for="ghiChu" class="form-label">
                                                            <i class="fas fa-comment-dots"></i> Lý do từ chối
                                                        </label>
                                                        <textarea id="ghiChu" name="ghiChu" class="form-control"
                                                            rows="3" placeholder="Nhập lý do từ chối chi tiết..."></textarea>
                                                        <div id="errorMessage" class="text-danger mt-2" style="display:none;">
                                                            <i class="fas fa-exclamation-circle"></i> Vui lòng nhập lý do từ chối!
                                                        </div>
                                                    </div>

                                                    <div class="d-grid gap-2">
                                                        <?php if (!empty($examDetail['ngayNop'])): ?>
                                                            <button type="button" id="btnDuyet" class="btn btn-success btn-action">
                                                                <i class="fas fa-check"></i> Phê duyệt đề thi
                                                            </button>
                                                            <button type="button" id="btnTuChoi" class="btn btn-danger btn-action">
                                                                <i class="fas fa-times"></i> Từ chối đề thi
                                                            </button>
                                                        <?php else: ?>
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle"></i> Không thể duyệt đề thi chưa được nộp
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>


        <!-- JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const btnTuChoi = document.getElementById('btnTuChoi');
                const btnDuyet = document.getElementById('btnDuyet');
                const divGhiChu = document.getElementById('divGhiChu');
                const ghiChu = document.getElementById('ghiChu');
                const errorMsg = document.getElementById('errorMessage');
                const form = document.getElementById('formDuyet');
                const hanhDongInput = document.getElementById('hanhDongInput');

                // Duyệt nhanh (từ danh sách) - CHỈ DUYỆT ĐỀ ĐÃ NỘP
                window.duyetDeThi = function(maDeThi) {
                    // Kiểm tra xem đề thi đã nộp chưa
                    const row = document.querySelector(`tr[data-id="${maDeThi}"]`);
                    if (!row) {
                        alert('Không tìm thấy đề thi #' + maDeThi);
                        return;
                    }

                    const ngayNop = row.querySelector('.ngay-nop').innerText;
                    if (ngayNop.includes('Chưa nộp')) {
                        alert('Không thể duyệt đề thi chưa được nộp!');
                        return;
                    }

                    if (confirm('Bạn có chắc chắn muốn duyệt đề thi #' + maDeThi + ' không?')) {
                        window.location.href = `index.php?controller=dethi&action=capNhatTrangThaiNhanh&maDeThi=${maDeThi}&hanhDong=duyet`;
                    }
                };

                if (btnTuChoi && btnDuyet) {
                    // Xử lý nút Từ chối
                    btnTuChoi.addEventListener('click', function() {
                        if (divGhiChu.style.display === 'none') {
                            // Hiển thị ô lý do
                            divGhiChu.style.display = 'block';
                            ghiChu.focus();
                            hanhDongInput.value = 'tuchoi';

                            // Disable nút duyệt
                            btnDuyet.disabled = true;
                            btnDuyet.classList.add('disabled');

                            // Thay đổi text nút từ chối
                            btnTuChoi.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi từ chối';
                            btnTuChoi.classList.remove('btn-danger');
                            btnTuChoi.classList.add('btn-warning');
                        } else {
                            // Kiểm tra và submit
                            if (ghiChu.value.trim() === '') {
                                errorMsg.style.display = 'block';
                                ghiChu.focus();
                                ghiChu.classList.add('is-invalid');
                            } else {
                                errorMsg.style.display = 'none';
                                ghiChu.classList.remove('is-invalid');

                                // Hiển thị xác nhận
                                if (confirm('Xác nhận từ chối đề thi này?')) {
                                    hanhDongInput.value = 'tuchoi';
                                    form.submit();
                                }
                            }
                        }
                    });

                    // Xử lý nút Duyệt
                    btnDuyet.addEventListener('click', function() {
                        if (confirm('Bạn có chắc chắn muốn phê duyệt đề thi này không?')) {
                            hanhDongInput.value = 'duyet';

                            // Hiển thị loading
                            const originalText = btnDuyet.innerHTML;
                            btnDuyet.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                            btnDuyet.disabled = true;

                            form.submit();

                            // Restore sau 3s nếu có lỗi
                            setTimeout(() => {
                                btnDuyet.innerHTML = originalText;
                                btnDuyet.disabled = false;
                            }, 3000);
                        }
                    });

                    // Validate form
                    form.addEventListener('submit', function(e) {
                        if (hanhDongInput.value === 'tuchoi' && ghiChu.value.trim() === '') {
                            e.preventDefault();
                            errorMsg.style.display = 'block';
                            ghiChu.focus();
                            ghiChu.classList.add('is-invalid');
                            return false;
                        }

                        // Hiển thị loading
                        const submitBtn = hanhDongInput.value === 'duyet' ? btnDuyet : btnTuChoi;
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
                        submitBtn.disabled = true;

                        setTimeout(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }, 5000);

                        return true;
                    });

                    // Xử lý khi nhập lý do
                    ghiChu.addEventListener('input', function() {
                        if (this.value.trim() !== '') {
                            errorMsg.style.display = 'none';
                            this.classList.remove('is-invalid');
                        }
                    });
                }

                // Auto-focus khi có thông báo lỗi
                <?php if (isset($message) && $message['status'] == 'danger'): ?>
                    setTimeout(() => {
                        const modal = new bootstrap.Modal(document.getElementById('thongBaoModal'));
                        modal.show();
                    }, 500);
                <?php endif; ?>

                // Print functionality
                document.querySelector('[onclick="window.print()"]').addEventListener('click', function() {
                    window.print();
                });
            });

            console.log('Current font for body:', window.getComputedStyle(document.body).fontFamily);
            console.log('Current font for .main-content:', window.getComputedStyle(document.querySelector('.main-content')).fontFamily);
        </script>
        <script src="assets/js/active-menu.js"></script>
        <?php require_once 'views/layouts/footer.php'; ?>
</body>

</html>