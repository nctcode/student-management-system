<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar/bangiamhieu.php'; 
?>

<div class="container" style="padding: 20px; max-width: 900px; margin-left: 250px;">
    <h2>Chi tiết Đề thi: <?php echo htmlspecialchars($dethi['tieuDe']); ?></h2>

    <div style="margin-bottom: 15px;">
        <strong>Trạng thái:</strong> 
        <span style="font-weight: bold; color: <?php echo $dethi['trangThai'] == 'DA_DUYET' ? 'green' : 'orange'; ?>;">
            <?php echo htmlspecialchars($dethi['trangThai']); ?>
        </span>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; width: 150px;"><strong>Giáo viên ra đề:</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($dethi['tenGiaoVien']); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Môn học:</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($dethi['tenMonHoc']); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Khối:</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($dethi['tenKhoi']); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Hạn nộp:</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo date('d-m-Y H:i', strtotime($dethi['hanNopDe'])); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Ngày nộp:</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $dethi['ngayNop'] ? date('d-m-Y H:i', strtotime($dethi['ngayNop'])) : '(Chưa nộp)'; ?></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Nội dung yêu cầu:</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo nl2br(htmlspecialchars($dethi['noiDung'])); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Ghi chú phân công:</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo nl2br(htmlspecialchars($dethi['ghiChu'])); ?></td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>File đề nộp lên:</strong></td>
            <td style="padding: 10px; border: 1px solid #ddd;">
                <?php if (!empty($dethi['fileDinhKem'])): ?>
                    <a href="<?php echo htmlspecialchars($dethi['fileDinhKem']); ?>" target="_blank">Xem File</a>
                <?php else: ?>
                    (Giáo viên chưa nộp file)
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <?php if ($dethi['trangThai'] != 'DA_DUYET'): ?>
        <div style="padding: 15px; background-color: #f0f8ff; border: 1px solid #b0e0e6; border-radius: 5px;">
            <h4>Hành động</h4>
            <a href="index.php?controller=duyetdethi&action=approve&id=<?php echo $dethi['maDeThi']; ?>" 
               style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; cursor: pointer;">
                Duyệt đề thi
            </a>
            <a href="index.php?controller=duyetdethi&action=reject&id=<?php echo $dethi['maDeThi']; ?>" 
               style="padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px; cursor: pointer;">
                Từ chối
            </a>
        </div>
    <?php endif; ?>

    <a href="index.php?controller=phancongrade" style="display: inline-block; margin-top: 20px;">Quay lại danh sách</a>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php'; 
?>