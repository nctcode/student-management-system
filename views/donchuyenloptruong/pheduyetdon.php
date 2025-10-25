<?php
$title = "Phê duyệt đơn chuyển trường";
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><?php echo $title; ?></h1>
        <a href="index.php?controller=donchuyenloptruong&action=detail&maDon=<?php echo $don['maDon']; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Form phê duyệt -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-check-circle"></i> Phê duyệt đơn #<?php echo $don['maDon']; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Thông tin đơn -->
                    <div class="alert alert-info">
                        <h6>Thông tin đơn chuyển trường:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Học sinh:</strong> <?php echo $don['tenHocSinh']; ?></p>
                                <p><strong>Lớp hiện tại:</strong> <?php echo $don['tenLopHienTai']; ?></p>
                                <p><strong>Trường hiện tại:</strong> <?php echo $don['tenTruongHienTai']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Trường chuyển đến:</strong> <?php echo $don['tenTruongDen'] ?? 'Chưa chọn'; ?></p>
                                <p><strong>Ngày gửi:</strong> <?php echo date('d/m/Y', strtotime($don['ngayGui'])); ?></p>
                                <p><strong>Phụ huynh:</strong> <?php echo $don['tenPhuHuynh'] ?? 'N/A'; ?></p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>Lý do chuyển trường:</strong>
                            <div class="border p-2 mt-1 rounded bg-white">
                                <?php echo nl2br(htmlspecialchars($don['lyDoChuyen'])); ?>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="index.php?controller=donchuyenloptruong&action=pheduyetdon&maDon=<?php echo $don['maDon']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Quyết định <span class="text-danger">*</span></label>
                            <select name="trangThai" class="form-select" required onchange="toggleLyDoTuChoi(this.value)">
                                <option value="">-- Chọn quyết định --</option>
                                <option value="Đã duyệt">Duyệt đơn</option>
                                <option value="Từ chối">Từ chối đơn</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="lyDoTuChoiGroup" style="display: none;">
                            <label class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                            <textarea name="lyDoTuChoi" class="form-control" rows="4" 
                                      placeholder="Nhập lý do từ chối đơn này..."></textarea>
                            <small class="text-muted">Lý do từ chối sẽ được gửi đến phụ huynh học sinh.</small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Lưu ý quan trọng:</strong>
                            <ul class="mb-0 mt-1">
                                <li>Khi duyệt đơn từ trường đi, học sinh vẫn chưa được chuyển trường ngay</li>
                                <li>Học sinh chỉ được chuyển trường khi cả TRƯỜNG ĐI và TRƯỜNG ĐẾN đều duyệt đơn</li>
                                <li>Nếu từ chối, vui lòng cung cấp lý do rõ ràng để phụ huynh nắm được thông tin</li>
                            </ul>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?controller=donchuyenloptruong&action=detail&maDon=<?php echo $don['maDon']; ?>" 
                               class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Xác nhận phê duyệt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleLyDoTuChoi(trangThai) {
    const lyDoGroup = document.getElementById('lyDoTuChoiGroup');
    const lyDoTextarea = document.querySelector('textarea[name="lyDoTuChoi"]');
    
    if (trangThai === 'Từ chối') {
        lyDoGroup.style.display = 'block';
        lyDoTextarea.required = true;
    } else {
        lyDoGroup.style.display = 'none';
        lyDoTextarea.required = false;
        lyDoTextarea.value = '';
    }
}
</script>