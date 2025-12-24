<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><strong>Bài tập của tôi</strong></h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Danh sách bài tập</h5>
        </div>
        <div class="card-body">
            <?php if (empty($danhSachBaiTap)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-book-reader fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Hiện chưa có bài tập nào!</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Môn học</th>
                                <th>Tên bài tập</th>
                                <th>Người giao</th>
                                <th>Ngày giao</th>
                                <th>Hạn nộp</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($danhSachBaiTap as $bt): ?>
                            <tr>
                                <td><?= htmlspecialchars($bt['tenMonHoc']) ?></td>
                                <td>
                                    <a href="index.php?controller=baitap&action=chitiet_hs&maBaiTap=<?= $bt['maBaiTap'] ?>">
                                        <?= htmlspecialchars($bt['tenBT']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($bt['tenGiaoVien']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($bt['ngayGiao'])) ?></td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($bt['hanNop'])) ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    // Lấy trạng thái nộp bài từ CSDL
                                    $trangThai = $bt['trangThaiNop']; 
                                    
                                    if ($trangThai == 'Đã nộp') {
                                        echo '<span class="badge badge-success" style="font-size: 0.9rem; color: green">Đã nộp</span>';
                                    } 
                                    elseif ($trangThai == 'Nộp trễ') {
                                        echo '<span class="badge badge-warning" style="font-size: 0.9rem; color: #e77f0f">Nộp trễ</span>';
                                    } 
                                    else {
                                        $hanNopDate = new DateTime($bt['hanNop']);
                                        $now = new DateTime();
                                        
                                        if ($now > $hanNopDate) {
                                            echo '<span class="badge badge-danger" style="font-size: 0.9rem; color: #fc260a">Đã trễ hạn</span>';
                                        } else {
                                            echo '<span class="badge badge-secondary" style="font-size: 0.9rem; color: #0e7be1">Chưa nộp</span>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?controller=baitap&action=chitiet_hs&maBaiTap=<?= $bt['maBaiTap'] ?>" 
                                    class="btn btn-info btn-sm rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-file-signature"></i> Xem & Nộp bài
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

<link rel="stylesheet" href="assets/css/baitap.css">