<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách Học phí</h2>
        <a href="index.php?controller=hocphi&action=donghocphi" class="btn btn-primary">
            <i class="fas fa-plus"></i> Đóng học phí
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Học sinh</th>
                        <th>Tháng</th>
                        <th>Số tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hocPhiList as $item): ?>
                    <tr>
                        <td><?php echo $item['hoc_sinh']; ?></td>
                        <td>Tháng <?php echo $item['thang']; ?></td>
                        <td><?php echo number_format($item['so_tien']); ?> VNĐ</td>
                        <td>
                            <?php if ($item['trang_thai'] == 'DA_NOP'): ?>
                                <span class="badge bg-success">Đã đóng</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Chưa đóng</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>