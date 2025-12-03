<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar/totruong.php'; 
?>

<div class="main-content" style="margin-left: 250px; padding: 20px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Quản lý Phân công Ra đề</h2>
            <a href="index.php?controller=phancongrade&action=create" 
               class="btn btn-primary">
                <i class="fas fa-plus"></i> Tạo Phân công Mới
            </a>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'novp'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Cảnh báo!</strong> Vui lòng chọn ít nhất một giáo viên.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Khối</th>
                                <th>Môn học</th>
                                <th>Giáo viên</th>
                                <th>Hạn nộp</th>
                                <th>Ngày nộp</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($danhSachPhanCong) && !empty($danhSachPhanCong)): ?>
                                <?php foreach ($danhSachPhanCong as $pc): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($pc['maDeThi']); ?></td>
                                        <td><?php echo htmlspecialchars($pc['tieuDe']); ?></td>
                                        <td><?php echo htmlspecialchars($pc['tenKhoi'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($pc['tenMonHoc'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php 
                                            $giaoVienList = explode(', ', $pc['tenGiaoVien'] ?? '');
                                            echo implode('<br>', array_map('htmlspecialchars', $giaoVienList)); 
                                            ?>
                                        </td>
                                        <td><?php echo $pc['hanNopDe'] ? date('d/m/Y H:i', strtotime($pc['hanNopDe'])) : 'N/A'; ?></td>
                                        <td>
                                            <?php if ($pc['ngayNop']): ?>
                                                <span class="text-success">
                                                    <?php echo date('d/m/Y H:i', strtotime($pc['ngayNop'])); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-warning">Chưa nộp</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'Chờ nộp' => 'badge bg-warning',
                                                'Đã nộp' => 'badge bg-info',
                                                'CHO_DUYET' => 'badge bg-primary',
                                                'DA_DUYET' => 'badge bg-success',
                                                'TU_CHOI' => 'badge bg-danger'
                                            ];
                                            $statusText = [
                                                'Chờ nộp' => 'Chờ nộp',
                                                'Đã nộp' => 'Đã nộp',
                                                'CHO_DUYET' => 'Chờ duyệt',
                                                'DA_DUYET' => 'Đã duyệt',
                                                'TU_CHOI' => 'Từ chối'
                                            ];
                                            $status = $pc['trangThai'] ?? 'Chờ nộp';
                                            $class = $statusClass[$status] ?? 'badge bg-secondary';
                                            $text = $statusText[$status] ?? $status;
                                            ?>
                                            <span class="<?php echo $class; ?>"><?php echo $text; ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="index.php?controller=phancongrade&action=view&id=<?php echo $pc['maDeThi']; ?>" 
                                                class="btn btn-sm btn-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($status == 'Chờ nộp'): ?>
                                                    <a href="index.php?controller=phancongrade&action=edit&id=<?php echo $pc['maDeThi']; ?>" 
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Nút xóa với modal xác nhận -->
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Xóa"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal<?php echo $pc['maDeThi']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                    <!-- Modal xác nhận xóa -->
                                                    <div class="modal fade" id="deleteModal<?php echo $pc['maDeThi']; ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Xác nhận xóa</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Bạn có chắc chắn muốn xóa phân công "<strong><?php echo htmlspecialchars($pc['tieuDe']); ?></strong>" không?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                    <form action="index.php?controller=phancongrade&action=delete" method="POST" style="display: inline;">
                                                                        <input type="hidden" name="id" value="<?php echo $pc['maDeThi']; ?>">
                                                                        <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                                                        <button type="submit" class="btn btn-danger">Xóa</button>
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
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-3"></i><br>
                                            Chưa có phân công nào.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php'; 
?>