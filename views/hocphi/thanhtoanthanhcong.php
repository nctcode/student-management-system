<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card text-center">
                <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                <h1 class="text-success">Thanh toán thành công!</h1>
                <p class="lead">Học phí đã được thanh toán thành công.</p>
                <p class="text-muted">Mã giao dịch: <strong><?= $maGiaoDich ?></strong></p>
                
                <div class="row justify-content-center mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5>Thông tin giao dịch</h5>
                                <p>Biên lai điện tử đã được tạo và gửi về email của bạn.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="index.php?controller=hocphi&action=index" class="btn btn-primary me-2">
                        <i class="fas fa-list me-1"></i>Về danh sách học phí
                    </a>
                    <a href="index.php?controller=hocphi&action=inbienlai&maGiaoDich=<?= $maGiaoDich ?>" class="btn btn-success" target="_blank">
                        <i class="fas fa-print me-1"></i>In biên lai
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>