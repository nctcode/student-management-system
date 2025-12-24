<?php
require_once 'views/layouts/header.php';
require_once 'views/layouts/sidebar/totruong.php';

$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

function hienThiTrangThai($t)
{
    return [
        'CHO_DUYET' => 'Chờ duyệt',
        'DA_DUYET'  => 'Đã duyệt',
        'TU_CHOI'   => 'Từ chối'
    ][$t] ?? $t;
}

// Phân loại đề thi theo trạng thái
$deDaDuyet = [];
$deTuChoi  = [];
$deChoDuyet = [];
if (!empty($exams)) {
    foreach ($exams as $row) {
        if ($row['trangThai'] === 'DA_DUYET') $deDaDuyet[] = $row;
        elseif ($row['trangThai'] === 'TU_CHOI') $deTuChoi[] = $row;
        elseif ($row['trangThai'] === 'CHO_DUYET') $deChoDuyet[] = $row;
    }
}

// Lấy danh sách môn học từ model hoặc database
$monHocList = $monHocList ?? [];

// Lấy các tham số lọc
$maKhoi = $_GET['maKhoi'] ?? '';
$maNienKhoa = $_GET['maNienKhoa'] ?? '';
$maMonHoc = $_GET['maMonHoc'] ?? '';
$maDeThi = $_GET['maDeThi'] ?? '';
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">Lịch sử duyệt đề thi</h2>
                <small class="text-muted">Xem lại các đề thi đã được duyệt hoặc từ chối</small>
            </div>
            <div class="btn-group">
                <a href="index.php?controller=dethi&action=duyet" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Quay lại duyệt đề
                </a>
                <button class="btn btn-info" onclick="window.print()">
                    <i class="fas fa-print"></i> In báo cáo
                </button>
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
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-filter"></i> Bộ lọc lịch sử</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="index.php" class="row g-3">
                    <input type="hidden" name="controller" value="deThi">
                    <input type="hidden" name="action" value="lichSuDuyetDeThi">

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
                        <div class="d-flex gap-2 w-100">
                            <button class="btn btn-success w-75" type="submit">
                                <i class="fas fa-search"></i> Lọc dữ liệu
                            </button>
                            <a href="index.php?controller=deThi&action=lichSuDuyetDeThi" 
                               class="btn btn-outline-secondary w-25" title="Xóa bộ lọc">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </form>
                
                <!-- Thống kê -->
                <?php if ($maKhoi || $maNienKhoa || $maMonHoc): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="p-3 alert-light border">
                            <div class="row">
                                <div class="col-md-8">
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
                                            if (!empty($selectedKhoi)): 
                                                $khoiMap = [5 => '10', 6 => '11', 7 => '12', 1 => '6', 2 => '7', 3 => '8', 4 => '9'];
                                                $tenKhoiHienThi = isset($khoiMap[$selectedKhoi['maKhoi']]) ? 'Khối ' . $khoiMap[$selectedKhoi['maKhoi']] : $selectedKhoi['tenKhoi'];
                                        ?>
                                            <span class="badge bg-primary"><?= htmlspecialchars($tenKhoiHienThi) ?></span>
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
                                        
                                        <?php if ($maMonHoc && !empty($monHocList)): 
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
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-success">Đã duyệt: <?= count($deDaDuyet) ?></span>
                                    <span class="badge bg-danger ms-2">Từ chối: <?= count($deTuChoi) ?></span>
                                    <span class="badge bg-warning ms-2">Tổng: <?= count($exams) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tổng quan -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0"><?= count($deDaDuyet) ?></h5>
                                <small>Đề thi đã duyệt</small>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0"><?= count($deTuChoi) ?></h5>
                                <small>Đề thi từ chối</small>
                            </div>
                            <i class="fas fa-times-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0"><?= count($exams) ?></h5>
                                <small>Tổng số đề thi</small>
                            </div>
                            <i class="fas fa-file-alt fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab điều hướng -->
        <ul class="nav nav-tabs mb-3" id="historyTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                    <i class="fas fa-list"></i> Tất cả (<?= count($exams) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button">
                    <i class="fas fa-check-circle"></i> Đã duyệt (<?= count($deDaDuyet) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button">
                    <i class="fas fa-times-circle"></i> Từ chối (<?= count($deTuChoi) ?>)
                </button>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="historyTabContent">
            <!-- Tab tất cả -->
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-list-alt"></i> Tất cả đề thi</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($exams)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Không có dữ liệu lịch sử</h5>
                                <p class="text-muted">Chưa có đề thi nào được duyệt hoặc từ chối với bộ lọc này</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="80">Mã đề</th>
                                            <th width="150">Giáo viên</th>
                                            <th>Tiêu đề</th>
                                            <th width="120">Môn học</th>
                                            <th width="100">Ngày nộp</th>
                                            <th width="100">Ngày duyệt</th>
                                            <th width="100">Trạng thái</th>
                                            <th width="80">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($exams as $exam): ?>
                                            <tr class="<?= ($exam['maDeThi'] == ($examDetail['maDeThi'] ?? 0)) ? 'table-active' : '' ?>">
                                                <td>
                                                    <strong>#<?= $exam['maDeThi'] ?></strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user-circle text-primary me-2"></i>
                                                        <span><?= htmlspecialchars($exam['hoTen'] ?? 'N/A') ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-medium"><?= htmlspecialchars($exam['tieuDe']) ?></div>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($exam['tenMonHoc'] ?? ($exam['monHoc'] ?? 'N/A')) ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($exam['ngayNop'])): 
                                                        $ngayNop = new DateTime($exam['ngayNop']);
                                                    ?>
                                                        <div><?= $ngayNop->format('d/m/Y') ?></div>
                                                        <small class="text-muted"><?= $ngayNop->format('H:i') ?></small>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($exam['ngayDuyet'])): 
                                                        $ngayDuyet = new DateTime($exam['ngayDuyet']);
                                                    ?>
                                                        <div><?= $ngayDuyet->format('d/m/Y') ?></div>
                                                        <small class="text-muted"><?= $ngayDuyet->format('H:i') ?></small>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($exam['trangThai'] == 'DA_DUYET'): ?>
                                                        <span class="badge bg-success"><i class="fas fa-check"></i> Đã duyệt</span>
                                                    <?php elseif ($exam['trangThai'] == 'TU_CHOI'): ?>
                                                        <span class="badge bg-danger"><i class="fas fa-times"></i> Từ chối</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning"><?= hienThiTrangThai($exam['trangThai']) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="index.php?controller=deThi&action=lichSuDuyetDeThi&maDeThi=<?= $exam['maDeThi'] ?>&maKhoi=<?= $maKhoi ?>&maNienKhoa=<?= $maNienKhoa ?>&maMonHoc=<?= $maMonHoc ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
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

            <!-- Tab đã duyệt -->
            <div class="tab-pane fade" id="approved" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-check-circle text-success"></i> Đề thi đã duyệt</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($deDaDuyet)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Không có đề thi đã duyệt</h5>
                                <p class="text-muted">Chưa có đề thi nào được duyệt với bộ lọc này</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Mã đề</th>
                                            <th>Tiêu đề</th>
                                            <th>Giáo viên</th>
                                            <th>Ngày duyệt</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($deDaDuyet as $row): ?>
                                            <tr>
                                                <td><strong>#<?= $row['maDeThi'] ?></strong></td>
                                                <td><?= htmlspecialchars($row['tieuDe']) ?></td>
                                                <td><?= htmlspecialchars($row['hoTen']) ?></td>
                                                <td><?= !empty($row['ngayDuyet']) ? date('d/m/Y H:i', strtotime($row['ngayDuyet'])) : '-' ?></td>
                                                <td>
                                                    <a href="index.php?controller=deThi&action=lichSuDuyetDeThi&maDeThi=<?= $row['maDeThi'] ?>&maKhoi=<?= $maKhoi ?>&maNienKhoa=<?= $maNienKhoa ?>&maMonHoc=<?= $maMonHoc ?>" 
                                                       class="btn btn-sm btn-outline-primary">Xem</a>
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

            <!-- Tab từ chối -->
            <div class="tab-pane fade" id="rejected" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-times-circle text-danger"></i> Đề thi bị từ chối</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($deTuChoi)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Không có đề thi bị từ chối</h5>
                                <p class="text-muted">Chưa có đề thi nào bị từ chối với bộ lọc này</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-danger">
                                        <tr>
                                            <th>Mã đề</th>
                                            <th>Tiêu đề</th>
                                            <th>Giáo viên</th>
                                            <th>Ngày từ chối</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($deTuChoi as $row): ?>
                                            <tr>
                                                <td><strong>#<?= $row['maDeThi'] ?></strong></td>
                                                <td><?= htmlspecialchars($row['tieuDe']) ?></td>
                                                <td><?= htmlspecialchars($row['hoTen']) ?></td>
                                                <td><?= !empty($row['ngayDuyet']) ? date('d/m/Y H:i', strtotime($row['ngayDuyet'])) : '-' ?></td>
                                                <td>
                                                    <a href="index.php?controller=deThi&action=lichSuDuyetDeThi&maDeThi=<?= $row['maDeThi'] ?>&maKhoi=<?= $maKhoi ?>&maNienKhoa=<?= $maNienKhoa ?>&maMonHoc=<?= $maMonHoc ?>" 
                                                       class="btn btn-sm btn-outline-primary">Xem</a>
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

        <!-- Chi tiết đề thi -->
        <?php if (!empty($examDetail)): 
            $fileUrl = !empty($examDetail['noiDung']) ? 'uploads/dethi/' . $examDetail['noiDung'] : '';
            $fileExists = !empty($examDetail['noiDung']) && file_exists($fileUrl);
        ?>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt"></i> Chi tiết đề thi
                                <span class="badge bg-light text-dark ms-2">#<?= $examDetail['maDeThi'] ?></span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Tiêu đề:</th>
                                            <td><?= htmlspecialchars($examDetail['tieuDe']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Giáo viên ra đề:</th>
                                            <td><?= htmlspecialchars($examDetail['tenGiaoVien'] ?? $examDetail['hoTen'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Môn học:</th>
                                            <td><?= htmlspecialchars($examDetail['tenMonHoc'] ?? $examDetail['monHoc'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Khối:</th>
                                            <td>
                                                <?php
                                                $maKhoiDetail = $examDetail['maKhoi'] ?? 0;
                                                $khoiMap = [5 => '10', 6 => '11', 7 => '12', 1 => '6', 2 => '7', 3 => '8', 4 => '9'];
                                                echo 'Khối ' . ($khoiMap[$maKhoiDetail] ?? $maKhoiDetail);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Ngày nộp:</th>
                                            <td><?= !empty($examDetail['ngayNop']) ? date('d/m/Y H:i', strtotime($examDetail['ngayNop'])) : 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Ngày duyệt:</th>
                                            <td><?= !empty($examDetail['ngayDuyet']) ? date('d/m/Y H:i', strtotime($examDetail['ngayDuyet'])) : 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Trạng thái:</th>
                                            <td>
                                                <?php if ($examDetail['trangThai'] == 'DA_DUYET'): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check"></i> Đã duyệt</span>
                                                <?php elseif ($examDetail['trangThai'] == 'TU_CHOI'): ?>
                                                    <span class="badge bg-danger"><i class="fas fa-times"></i> Từ chối</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning"><?= hienThiTrangThai($examDetail['trangThai']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php if ($examDetail['trangThai'] === 'TU_CHOI' && !empty($examDetail['ghiChu'])): ?>
                                            <tr>
                                                <th>Lý do từ chối:</th>
                                                <td>
                                                    <div class="alert alert-danger mb-0">
                                                        <i class="fas fa-comment-dots"></i>
                                                        <?= nl2br(htmlspecialchars($examDetail['ghiChu'])) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-file-download"></i> File đề thi</h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <?php if ($fileExists): 
                                                $fileSize = filesize($fileUrl);
                                                $fileSizeFormatted = round($fileSize / 1024, 2) . ' KB';
                                            ?>
                                                <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                                <h6><?= htmlspecialchars($examDetail['noiDung']) ?></h6>
                                                <p class="text-muted"><?= $fileSizeFormatted ?></p>
                                                <div class="d-grid gap-2">
                                                    <a href="<?= $fileUrl ?>" 
                                                       class="btn btn-success" 
                                                       target="_blank">
                                                        <i class="fas fa-eye"></i> Xem trước
                                                    </a>
                                                    <a href="<?= $fileUrl ?>" 
                                                       class="btn btn-primary" 
                                                       download="<?= $examDetail['noiDung'] ?>">
                                                        <i class="fas fa-download"></i> Tải xuống
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-center py-4">
                                                    <i class="fas fa-file-excel fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">Không có file đính kèm</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
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
    // Xử lý tab
    const triggerTabList = [].slice.call(document.querySelectorAll('#historyTab button'));
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
    
    // Tự động mở modal thông báo nếu có
    <?php if (isset($message)): ?>
        setTimeout(() => {
            const toast = new bootstrap.Toast(document.getElementById('liveToast'));
            toast.show();
        }, 500);
    <?php endif; ?>
    
    // In báo cáo
    document.querySelector('[onclick="window.print()"]').addEventListener('click', function() {
        window.print();
    });
    
    // Lọc dữ liệu khi thay đổi select
    document.querySelectorAll('select[name="maKhoi"], select[name="maNienKhoa"], select[name="maMonHoc"]').forEach(select => {
        select.addEventListener('change', function() {
            if (this.value) {
                // Nếu chọn giá trị, submit form
                this.form.submit();
            }
        });
    });
});

// Toast thông báo
const toastTrigger = document.getElementById('liveToastBtn');
const toastLiveExample = document.getElementById('liveToast');
if (toastTrigger) {
    toastTrigger.addEventListener('click', () => {
        const toast = new bootstrap.Toast(toastLiveExample);
        toast.show();
    });
}
</script>

<style>
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

.card {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    border: 1px solid #dee2e6;
    margin-bottom: 20px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    font-weight: 600;
}

.nav-tabs .nav-link {
    border-radius: 8px 8px 0 0;
    font-weight: 500;
    color: #495057;
}

.nav-tabs .nav-link.active {
    font-weight: 600;
    border-bottom: 3px solid #0d6efd;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
}

.btn {
    border-radius: 8px;
    padding: 8px 20px;
    font-weight: 500;
}

.form-select {
    border-radius: 8px;
    padding: 8px 12px;
}

@media print {
    .d-print-none, .nav-tabs, .btn, .card-header, .form-select, .filter-section {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    
    .table {
        border-collapse: collapse;
    }
    
    .table th, .table td {
        border: 1px solid #000 !important;
    }
}
</style>

<?php require_once 'views/layouts/footer.php'; ?>