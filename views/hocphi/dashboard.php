<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-money-bill-wave me-2"></i>Quản lý Học phí</h1>
                    <div class="text-end">
                        <p class="mb-0 text-muted">Xin chào, <strong><?= $_SESSION['user_name'] ?? 'Học sinh' ?></strong></p>
                        <small class="text-muted"><?= date('d/m/Y') ?></small>
                    </div>
                </div>

                <!-- Cards chức năng chính -->
                <div class="row justify-content-center">
                    <!-- Đóng học phí -->
                    <div class="col-md-5 mb-4">
                        <div class="feature-card h-100">
                            <div class="card border-primary shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="feature-icon bg-primary mb-3">
                                        <i class="fas fa-credit-card fa-2x"></i>
                                    </div>
                                    <h4 class="card-title text-primary">Đóng học phí</h4>
                                    <p class="card-text text-muted mb-4">Thanh toán học phí trực tuyến hoặc tại trường</p>
                                    <a href="index.php?controller=hocphi&action=donghocphi" class="btn btn-primary btn-lg px-4">
                                        <i class="fas fa-arrow-right me-2"></i>Truy cập
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lịch sử thanh toán -->
                    <div class="col-md-5 mb-4">
                        <div class="feature-card h-100">
                            <div class="card border-success shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="feature-icon bg-success mb-3">
                                        <i class="fas fa-history fa-2x"></i>
                                    </div>
                                    <h4 class="card-title text-success">Lịch sử thanh toán</h4>
                                    <p class="card-text text-muted mb-4">Xem lịch sử các giao dịch đã thanh toán</p>
                                    <a href="index.php?controller=hocphi&action=lichsu" class="btn btn-success btn-lg px-4">
                                        <i class="fas fa-arrow-right me-2"></i>Truy cập
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông báo nhanh -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Thông báo</h5>
                                    <p class="mb-0">Vui lòng thanh toán học phí đúng hạn để tránh bị tính phí trễ hạn.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.feature-card .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
}

.feature-card .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.feature-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
}

.btn-lg {
    border-radius: 10px;
    font-weight: 600;
}

.alert {
    border-radius: 10px;
    border: none;
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
}
</style>