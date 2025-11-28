<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar/bangiamhieu.php'; 
?>

<div class="container" style="padding: 20px; margin-left: 250px;">
    <h2>Quản lý Phân công Ra đề</h2>
    
    <a href="index.php?controller=phancongrade&action=create" style="display: inline-block; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-bottom: 20px;">
        + Tạo Phân công Mới
    </a>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Tiêu đề</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Khối</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Môn học</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Giáo viên</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hạn nộp</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Ngày nộp</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Trạng thái</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($danhSachPhanCong) && !empty($danhSachPhanCong)): ?>
                <?php foreach ($danhSachPhanCong as $pc): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($pc['tieuDe']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($pc['tenKhoi']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($pc['tenMonHoc']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($pc['tenGiaoVien']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo date('d-m-Y H:i', strtotime($pc['hanNopDe'])); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $pc['ngayNop'] ? date('d-m-Y H:i', strtotime($pc['ngayNop'])) : '(Chưa nộp)'; ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <span>
                                <?php echo htmlspecialchars($pc['trangThai']); ?>
                            </span>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <a href="index.php?controller=duyetdethi&id=<?php echo $pc['maDeThi']; ?>">Xem / Duyệt</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="padding: 10px; border: 1px solid #ddd; text-align: center;">Chưa có phân công nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php'; 
?>