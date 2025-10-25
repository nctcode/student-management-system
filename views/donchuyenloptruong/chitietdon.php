<?php
$title = "Chi tiết đơn chuyển trường";
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <a href="index.php?controller=donchuyenloptruong&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Thông báo -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Thông tin đơn -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt"></i> Thông tin đơn chuyển trường
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã đơn:</strong> #<?php echo $don['maDon']; ?></p>
                            <p><strong>Học sinh:</strong> <?php echo $don['tenHocSinh']; ?></p>
                            <p><strong>Ngày sinh:</strong> <?php echo date('d/m/Y', strtotime($don['ngaySinh'])); ?></p>
                            <p><strong>Lớp hiện tại:</strong> <?php echo $don['tenLopHienTai']; ?></p>
                            <p><strong>Phụ huynh:</strong> <?php echo $don['tenPhuHuynh'] ?? 'N/A'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày gửi:</strong> <?php echo date('d/m/Y', strtotime($don['ngayGui'])); ?></p>
                            <p><strong>Trường hiện tại:</strong> <?php echo $don['tenTruongHienTai']; ?></p>
                            <p><strong>Trường chuyển đến:</strong> <?php echo $don['tenTruongDen'] ?? 'Chưa chọn'; ?></p>
                            <p><strong>Địa chỉ trường đến:</strong> <?php echo $don['diaChiTruongDen'] ?? 'N/A'; ?></p>
                        </div>
                    </div>

                    <!-- Lý do chuyển -->
                    <div class="mt-3">
                        <strong>Lý do chuyển trường:</strong>
                        <div class="border p-3 mt-2 rounded bg-light">
                            <?php echo nl2br(htmlspecialchars($don['lyDoChuyen'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trạng thái xử lý -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tasks"></i> Trạng thái xử lý
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Trường đi (<?php echo $don['tenTruongHienTai']; ?>)</h6>
                            <?php
                            $badgeClassDi = [
                                'Chờ duyệt' => 'bg-warning',
                                'Đã duyệt' => 'bg-success',
                                'Từ chối' => 'bg-danger',
                                'Đã hủy' => 'bg-secondary'
                            ];
                            ?>
                            <span class="badge <?php echo $badgeClassDi[$don['trangThaiTruongDi']] ?? 'bg-secondary'; ?>">
                                <?php echo $don['trangThaiTruongDi']; ?>
                            </span>
                            
                            <?php if ($don['ngayDuyetTruongDi']): ?>
                            <p class="mt-2 mb-1"><small>Ngày duyệt: <?php echo date('d/m/Y', strtotime($don['ngayDuyetTruongDi'])); ?></small></p>
                            <?php endif; ?>
                            
                            <?php if ($don['lyDoTuChoiTruongDi']): ?>
                            <div class="mt-2">
                                <small><strong>Lý do từ chối:</strong></small>
                                <div class="border p-2 rounded bg-light">
                                    <?php echo nl2br(htmlspecialchars($don['lyDoTuChoiTruongDi'])); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Trường đến (<?php echo $don['tenTruongDen'] ?? 'Chưa chọn'; ?>)</h6>
                            <?php
                            $badgeClassDen = [
                                'Chờ duyệt' => 'bg-warning',
                                'Đã duyệt' => 'bg-success',
                                'Từ chối' => 'bg-danger',
                                'Đã hủy' => 'bg-secondary'
                            ];
                            ?>
                            <span class="badge <?php echo $badgeClassDen[$don['trangThaiTruongDen']] ?? 'bg-secondary'; ?>">
                                <?php echo $don['trangThaiTruongDen']; ?>
                            </span>
                            
                            <?php if ($don['ngayDuyetTruongDen']): ?>
                            <p class="mt-2 mb-1"><small>Ngày duyệt: <?php echo date('d/m/Y', strtotime($don['ngayDuyetTruongDen'])); ?></small></p>
                            <?php endif; ?>
                            
                            <?php if ($don['lyDoTuChoiTruongDen']): ?>
                            <div class="mt-2">
                                <small><strong>Lý do từ chối:</strong></small>
                                <div class="border p-2 rounded bg-light">
                                    <?php echo nl2br(htmlspecialchars($don['lyDoTuChoiTruongDen'])); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Trạng thái tổng thể -->
                    <div class="mt-4 p-3 rounded <?php echo ($don['trangThaiTruongDi'] === 'Đã duyệt' && $don['trangThaiTruongDen'] === 'Đã duyệt') ? 'bg-success text-white' : 'bg-light'; ?>">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle"></i>
                            <?php if ($don['trangThaiTruongDi'] === 'Đã duyệt' && $don['trangThaiTruongDen'] === 'Đã duyệt'): ?>
                                Đơn đã được duyệt hoàn toàn. Học sinh sẽ được chuyển trường.
                            <?php elseif ($don['trangThaiTruongDi'] === 'Từ chối' || $don['trangThaiTruongDen'] === 'Từ chối'): ?>
                                Đơn đã bị từ chối. Vui lòng liên hệ nhà trường để biết thêm chi tiết.
                            <?php elseif ($don['trangThaiTruongDi'] === 'Đã hủy' || $don['trangThaiTruongDen'] === 'Đã hủy'): ?>
                                Đơn đã bị hủy.
                            <?php else: ?>
                                Đơn đang chờ xử lý từ cả hai trường.
                            <?php endif; ?>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thao tác -->
        <div class="col-md-4">
            <?php if (in_array($_SESSION['user']['vaiTro'], ['QTV', 'BGH']) && $don['trangThaiTruongDi'] === 'Chờ duyệt'): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-check-circle"></i> Phê duyệt đơn
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="index.php?controller=donchuyenloptruong&action=pheduyetdon&maDon=<?php echo $don['maDon']; ?>" 
                           class="btn btn-success">
                            <i class="fas fa-check"></i> Xử lý đơn
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($_SESSION['user']['vaiTro'] === 'PHUHUYNH' && $don['trangThaiTruongDi'] === 'Chờ duyệt'): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-times-circle"></i> Thao tác
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?controller=donchuyenloptruong&action=cancel&maDon=<?php echo $don['maDon']; ?>" 
                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn này?')">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times"></i> Hủy đơn
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Thông tin liên hệ -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-phone"></i> Thông tin liên hệ
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Học sinh:</strong> <?php echo $don['tenHocSinh']; ?></p>
                    <p><strong>SĐT học sinh:</strong> <?php echo $don['sdtHocSinh'] ?? 'N/A'; ?></p>
                    <p><strong>Phụ huynh:</strong> <?php echo $don['tenPhuHuynh'] ?? 'N/A'; ?></p>
                    <p><strong>SĐT phụ huynh:</strong> <?php echo $don['sdtPhuHuynh'] ?? 'N/A'; ?></p>
                    
                    <hr>
                    
                    <p><strong>Trường hiện tại:</strong></p>
                    <p class="mb-1"><?php echo $don['tenTruongHienTai']; ?></p>
                    <small class="text-muted"><?php echo $don['diaChiTruongHienTai'] ?? ''; ?></small>
                    
                    <?php if ($don['tenTruongDen']): ?>
                    <p class="mt-3"><strong>Trường chuyển đến:</strong></p>
                    <p class="mb-1"><?php echo $don['tenTruongDen']; ?></p>
                    <small class="text-muted"><?php echo $don['diaChiTruongDen'] ?? ''; ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>