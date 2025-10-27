<?php
$title = "Phê duyệt chuyển lớp/trường";
require_once __DIR__ . '/../layouts/header.php';

$selectedSchool = $_SESSION['maTruong'] ?? ($_GET['school'] ?? null);
$activeTab = $_GET['tab'] ?? 'cho_duyet'; // tab mặc định

// Lấy giá trị từ URL: 'truong', 'lop', hoặc 'tat_ca'
$loaiDonUrl = $_GET['loaiDon'] ?? 'tat_ca'; 
$loaiDon = $loaiDonUrl === 'truong' ? 'chuyen_truong' : ($loaiDonUrl === 'lop' ? 'chuyen_lop' : 'tat_ca');
?>
<link rel="stylesheet" href="assets/css/donchuyenlop_truong.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
/* ---------------------------------------------------------------------- */
/* CSS ĐẶC BIỆT CHO DANH SÁCH CUỘN VÀ NÚT ĐÓNG */
/* ---------------------------------------------------------------------- */
.request-list {
    /* Giới hạn chiều cao để chỉ hiển thị 3-4 đơn */
    max-height: 350px; 
    overflow-y: auto; /* Cho phép cuộn dọc */
    border: 1px solid #e0e0e0; 
    border-radius: 8px;
    padding: 10px;
    background-color: #f7f9fc;
    margin-bottom: 20px; 
}
.request-item {
    margin-bottom: 8px; 
}
.action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
/* Style cho nút Đóng khi có nút Duyệt/Từ chối */
.btn-cancel {
    background-color: #6c757d; /* Màu xám */
    color: white;
}
.btn-cancel:hover {
    background-color: #5a6268;
}
/* Style cho nút Đóng khi là đơn đã xử lý */
.action-buttons-single {
    margin-top: 20px;
    text-align: right; 
}
/* Thêm style cho phần thông tin liên hệ */
.contact-info-section {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 20px;
}
/* ---------------------------------------------------------------------- */
</style>

<div class="container">
    <div class="header">
        <h2>PHÊ DUYỆT CHUYỂN LỚP / TRƯỜNG</h2>
    </div>

    <div class="card">
        <h3 class="section-title"><i class="fas fa-list"></i> QUẢN LÝ ĐƠN CHUYỂN</h3>

        <form id="typeForm" method="get" class="mb-3">
            <input type="hidden" name="controller" value="donchuyenloptruong">
            <input type="hidden" name="action" value="danhsach">
            <input type="hidden" name="school" value="<?= htmlspecialchars($selectedSchool) ?>">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($activeTab) ?>">
            <select name="loaiDon" onchange="this.form.submit()" style="padding:8px;border-radius:6px;">
                <option value="tat_ca" <?= $loaiDonUrl=='tat_ca'?'selected':'' ?>>Tất cả đơn</option>
                <option value="truong" <?= $loaiDonUrl=='truong'?'selected':'' ?>>Đơn chuyển trường</option>
                <option value="lop" <?= $loaiDonUrl=='lop'?'selected':'' ?>>Đơn chuyển lớp</option>
            </select>
        </form>

        <form id="schoolForm" method="get" class="mb-3">
            <input type="hidden" name="controller" value="donchuyenloptruong">
            <input type="hidden" name="action" value="danhsach">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($activeTab) ?>">
            <input type="hidden" name="loaiDon" value="<?= htmlspecialchars($loaiDonUrl) ?>">

            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                <label style="min-width:110px;font-weight:600;">Chọn trường:</label>
                <select name="school" id="schoolSelect" style="min-width:320px;padding:8px;border-radius:6px;border:1px solid #cfd8e3;">
                    <option value="">-- Chọn trường --</option>
                    <?php
                    $schools = $schools ?? [];
                    foreach ($schools as $s) {
                        $sel = ($selectedSchool == $s['maTruong']) ? 'selected' : '';
                        echo "<option value='{$s['maTruong']}' $sel>{$s['tenTruong']}</option>";
                    }
                    ?>
                </select>
            </div>
        </form>

        <div class="tabs">
            <?php
            $tabs = [
                'cho_duyet' => 'Đơn chờ duyệt',
                'da_duyet' => 'Đơn đã duyệt',
                'tu_choi' => 'Đơn bị từ chối'
            ];
            foreach ($tabs as $key => $label) {
                $active = ($key === $activeTab) ? 'active' : '';
                $url = "?controller=donchuyenloptruong&action=danhsach&school={$selectedSchool}&tab={$key}&loaiDon={$loaiDonUrl}";
                echo "<a class='tab $active' href='$url'>$label</a>";
            }
            ?>
        </div>

        <div class="filter-group">
            <form id="searchForm" method="get" class="search-form">
                <input type="hidden" name="controller" value="donchuyenloptruong">
                <input type="hidden" name="action" value="danhsach">
                <input type="hidden" name="school" value="<?= htmlspecialchars($selectedSchool) ?>">
                <input type="hidden" name="tab" value="<?= htmlspecialchars($activeTab) ?>">
                <input type="hidden" name="loaiDon" value="<?= htmlspecialchars($loaiDonUrl) ?>">
                <div class="search-bar">
                    <input type="text" id="search" name="search"
                        placeholder="Nhập tên học sinh hoặc mã đơn..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button id="searchBtn" type="submit">Tìm</button>
                </div>
            </form>
        </div>

        <div class="request-list" id="requestList">
            <?php
            $requests = $requests ?? [];

            // Lọc theo tab trạng thái
            $filteredRequests = array_filter($requests, function($don) use ($activeTab) {
                $trangThaiTong = $don['trangThaiTong'] ?? 'Không xác định';
                switch ($activeTab) {
                    case 'cho_duyet': return $trangThaiTong === 'Chờ duyệt';
                    case 'da_duyet':  return $trangThaiTong === 'Hoàn tất';
                    case 'tu_choi':   return $trangThaiTong === 'Bị từ chối';
                    default: return true;
                }
            });
            ?>

            <?php if (empty($filteredRequests)): ?>
                <div class="p-3 text-center text-muted">Không có đơn nào trong mục này.</div>
            <?php else: ?>
                <?php foreach ($filteredRequests as $don): ?>
                    <?php
                        $type = $don['loaiDon'] ?? 'chuyen_truong'; 
                        $status = $don['trangThaiTong'] ?? 'Không xác định';
                        $cls = match($status) {
                            'Hoàn tất' => 'status-approved',
                            'Bị từ chối' => 'status-rejected',
                            default => 'status-pending',
                        };

                        $role = '';
                        $roleDisplay = '';

                        if ($type === 'chuyen_truong') {
                            // Logic chuyển trường (dùng maTruong)
                            $role = ($don['maTruongDen'] == $selectedSchool) ? 'truongden' : 'truongdi';
                            $roleDisplay = ($role === 'truongden') ? 'Trường đến' : 'Trường đi';
                        } elseif ($type === 'chuyen_lop') {
                            // Logic chuyển lớp: Chỉ cần là 'lop'
                            $role = 'lop'; 
                            $roleDisplay = 'Ban Giám Hiệu'; 
                        }
                    ?>
                    <div class="request-item" data-id="<?= $don['maDon'] ?>" data-role="<?= $role ?>" data-type="<?= $type ?>">
                        <div class="request-info">
                            <div class="request-code">#<?= str_pad($don['maDon'], 3, '0', STR_PAD_LEFT) ?></div>
                            <div class="student-name"><?= htmlspecialchars($don['tenHS'] ?? 'N/A') ?></div>
                            <div class="transfer-info">
                                <?= $type==='chuyen_lop' ? htmlspecialchars($don['lopHienTai'] ?? 'N/A') : htmlspecialchars($don['truongHienTai'] ?? 'N/A') ?> → 
                                <?= $type==='chuyen_lop' ? htmlspecialchars($don['lopDen'] ?? 'N/A') : htmlspecialchars($don['truongDen'] ?? 'N/A') ?>
                            </div>
                            <div class="transfer-info">
                                Loại đơn: <strong><?= $type === 'chuyen_lop' ? 'Chuyển lớp' : 'Chuyển trường' ?></strong>
                                <?php if ($type === 'chuyen_truong'): ?>
                                    | Vai trò: <strong><?= $roleDisplay ?></strong>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="status <?= $cls ?>"><?= $status ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="detailContainer" style="display:none;margin-top:18px;">
            <h3 class="section-title"><i class="fas fa-info-circle"></i> THÔNG TIN CHI TIẾT</h3>
            <div id="detailContent" class="request-details"></div>
        </div>
    </div>
</div>

<script>
document.getElementById('schoolSelect').addEventListener('change', () => {
    document.getElementById('schoolForm').submit();
});

document.querySelectorAll('.request-item').forEach(el => {
    el.addEventListener('click', async function() {
        document.querySelectorAll('.request-item').forEach(i => i.classList.remove('selected'));
        this.classList.add('selected');
        const id = this.dataset.id;
        const role = this.dataset.role; // truongden, truongdi, lop
        const type = this.dataset.type; // chuyen_truong, chuyen_lop

        const box = document.getElementById('detailContainer');
        const content = document.getElementById('detailContent');
        box.style.display = 'block';
        content.innerHTML = '<i>Đang tải...</i>';
        
        try {
            const res = await fetch(`index.php?controller=donchuyenloptruong&action=ajax_chitiet&id=${id}`);
            const d = await res.json();

            // *** QUAN TRỌNG: BẮT LỖI JSON TỪ SERVER ***
            if (d.error) {
                // Hiển thị lỗi chi tiết từ server để debug
                return content.innerHTML = `<p style='color:red; font-weight:bold;'>Lỗi tải chi tiết: ${d.error}</p>`;
            }
            // *****************************************

            const isTruong = type === 'chuyen_truong';
            const unitType = isTruong ? 'Trường' : 'Lớp';
            const fromUnit = isTruong ? (d.truongHienTai || 'N/A') : (d.lopHienTai || 'N/A');
            const toUnit = isTruong ? (d.truongDen || 'N/A') : (d.lopDen || 'N/A');
            
            let statusDi, statusDen, lyDoTuChoiDi, lyDoTuChoiDen, actionRole, currentStatus, destinationStatus;
            
            if (isTruong) {
                statusDi = d.trangThaiTruongDi || 'Chờ duyệt';
                statusDen = d.trangThaiTruongDen || 'Chờ duyệt';
                lyDoTuChoiDi = d.lyDoTuChoiTruongDi;
                lyDoTuChoiDen = d.lyDoTuChoiTruongDen;
                currentStatus = statusDi;
                destinationStatus = statusDen;
                actionRole = role; 
            } else {
                const lopStatus = d.trangThaiLop || 'Chờ duyệt';
                statusDi = lopStatus; 
                statusDen = lopStatus; 
                lyDoTuChoiDi = d.lyDoTuChoiLop;
                lyDoTuChoiDen = d.lyDoTuChoiLop; 
                currentStatus = lopStatus;
                destinationStatus = lopStatus;
                actionRole = 'lop'; 
            }
            
            // LOGIC XÁC ĐỊNH HIỂN THỊ NÚT DUYỆT
            let showActions = false;

            if (isTruong) {
                if (role === 'truongden' && statusDen === 'Chờ duyệt') {
                    showActions = true;
                }
                if (role === 'truongdi' && statusDi === 'Chờ duyệt' && statusDen === 'Đã duyệt') {
                    showActions = true;
                }
                if (statusDi === 'Từ chối' || statusDen === 'Từ chối') {
                    showActions = false;
                }
            } else {
                if (actionRole === 'lop' && statusDen === 'Chờ duyệt') {
                    showActions = true;
                }
            }

            // Hiển thị vai trò cho Action và Detail
            let displayRoleText = isTruong ? (role === 'truongden' ? 'Trường đến' : 'Trường đi') : 'Ban Giám Hiệu';

            const actionButtonsHTML = showActions ? `
                <div class="notes-section"><label>Ghi chú xử lý (bắt buộc khi từ chối):</label>
                    <textarea id="note${d.maDon}"></textarea>
                </div>
                <div class="action-buttons">
                    <form method="post" action="?controller=donchuyenloptruong&action=approve" onsubmit="return confirmApprove();">
                        <input type="hidden" name="maDon" value="${d.maDon}">
                        <input type="hidden" name="side" value="${actionRole}">
                        <button class="btn btn-approve" type="submit"><i class="fas fa-check"></i> Duyệt</button>
                    </form>
                    <form method="post" action="?controller=donchuyenloptruong&action=reject" onsubmit="return confirmReject(${d.maDon});">
                        <input type="hidden" name="maDon" value="${d.maDon}">
                        <input type="hidden" name="reason" id="reason${d.maDon}">
                        <input type="hidden" name="side" value="${actionRole}">
                        <button class="btn btn-reject" type="submit"><i class="fas fa-times"></i> Từ chối</button>
                    </form>
                    <button id="btnCloseDetail" class="btn btn-cancel" type="button"><i class="fas fa-times-circle"></i> Đóng</button>
                </div>
            ` : `<p class="p-3 text-center text-muted">Đơn đã được xử lý hoặc chưa đến lượt bạn duyệt.</p>
                  <div class="action-buttons-single">
                    <button id="btnCloseDetail" class="btn btn-cancel" type="button"><i class="fas fa-times-circle"></i> Đóng</button>
                  </div>`;

            // Hiển thị lý do từ chối
            let rejectionReasonHTML = '';
            if (isTruong) {
                if (statusDi === 'Từ chối' && lyDoTuChoiDi) {
                    rejectionReasonHTML += `<div><span class="detail-label">${unitType} hiện tại từ chối:</span> ${lyDoTuChoiDi}</div>`;
                }
                if (statusDen === 'Từ chối' && lyDoTuChoiDen) {
                    rejectionReasonHTML += `<div><span class="detail-label">${unitType} đến từ chối:</span> ${lyDoTuChoiDen}</div>`;
                }
            } else {
                if (statusDi === 'Từ chối' && lyDoTuChoiDi) {
                    rejectionReasonHTML += `<div><span class="detail-label">Lý do từ chối:</span> ${lyDoTuChoiDi}</div>`;
                }
            }

            if (rejectionReasonHTML) {
                rejectionReasonHTML = `<div class="p-3 status-rejected" style="border-radius:6px;margin-bottom:15px;color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;">
                    <strong style="font-size:16px;"><i class="fas fa-exclamation-triangle"></i> Lý do từ chối:</strong>
                    ${rejectionReasonHTML}
                </div>`;
            }

            // THÔNG TIN LIÊN HỆ
            const contactInfoHTML = `
                <h4 class="section-title mt-4" style="font-size: 1.1rem; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-top:20px;"><i class="fas fa-user-friends"></i> Thông tin liên hệ</h4>
                
                <div class="contact-info-section">
                    <div class="detail-grid" style="grid-template-columns: 1fr 1fr;">
                        <div style="border-right: 1px solid #eee; padding-right: 15px;">
                            <strong class="text-primary">PHỤ HUYNH</strong>
                            <div><span class="detail-label">Họ tên:</span> ${d.tenPhuHuynh || 'N/A'}</div>
                            <div><span class="detail-label">SĐT:</span> ${d.sdtPhuHuynh || 'N/A'}</div>
                            <div><span class="detail-label">Email:</span> ${d.emailPhuHuynh || 'N/A'}</div>
                        </div>
                        
                        <div style="padding-left: 15px;">
                            <strong class="text-primary">GV CHỦ NHIỆM (${unitType} hiện tại)</strong>
                            <div><span class="detail-label">Họ tên:</span> ${d.tenGVCN || 'N/A'}</div>
                            <div><span class="detail-label">SĐT:</span> ${d.sdtGVCN || 'N/A'}</div>
                            <div><span class="detail-label">Email:</span> ${d.emailGVCN || 'N/A'}</div>
                        </div>
                    </div>
                </div>
            `;

            // Hiển thị Chi tiết
            content.innerHTML = `
                <div class="detail-grid">
                    <div><span class="detail-label">Mã đơn:</span> ${d.maDon}</div>
                    <div><span class="detail-label">Ngày gửi:</span> ${d.ngayGui}</div>
                    <div><span class="detail-label">Loại đơn:</span> <strong>${isTruong ? 'Chuyển trường' : 'Chuyển lớp'}</strong></div>
                    <div><span class="detail-label">Học sinh:</span> ${d.tenHS || 'N/A'}</div>
                    <div><span class="detail-label">Mã học sinh:</span> ${d.maHocSinh || 'N/A'}</div>
                    ${isTruong ? `<div><span class="detail-label">Vai trò:</span> <strong>${displayRoleText}</strong></div>` : ''}
                </div>
                
                ${contactInfoHTML} ${rejectionReasonHTML}
                <div class="school-status">
                    <div class="school-status-item school-current">
                        <div class="detail-label">${unitType} hiện tại</div>
                        <div>${fromUnit}</div>
                        <div class="status ${currentStatus ==='Đã duyệt'?'status-approved':currentStatus==='Từ chối'?'status-rejected':'status-pending'}">${currentStatus}</div>
                    </div>
                    <div class="school-status-item school-destination">
                        <div class="detail-label">${unitType} chuyển đến</div>
                        <div>${toUnit}</div>
                        <div class="status ${destinationStatus ==='Đã duyệt'?'status-approved':destinationStatus==='Từ chối'?'status-rejected':'status-pending'}">${destinationStatus}</div>
                    </div>
                </div>
                <div><span class="detail-label">Lý do chuyển:</span> ${d.lyDoChuyen || 'N/A'}</div>
                ${actionButtonsHTML}
            `;
            
            // GÁN EVENT CHO NÚT ĐÓNG
            document.getElementById('btnCloseDetail').addEventListener('click', function() {
                // Ẩn container chi tiết
                document.getElementById('detailContainer').style.display = 'none';
                // Bỏ highlight đơn đã chọn
                document.querySelectorAll('.request-item').forEach(i => i.classList.remove('selected'));
            });

        } catch(e) {
            // Lỗi JavaScript phía client (ví dụ: không parse được JSON)
            console.error(e);
            content.innerHTML = `<p style='color:red;'>Lỗi tải chi tiết (Client-side): ${e.message}</p>`;
        }
    });
});

function confirmApprove() {
    return confirm('Bạn có chắc muốn DUYỆT đơn này không? Thao tác này sẽ không thể hoàn tác.');
}

function confirmReject(id) {
    const note = document.getElementById(`note${id}`).value.trim();
    if(!note) {
        alert('Bạn phải nhập lý do từ chối.');
        return false;
    }
    document.getElementById(`reason${id}`).value = note;
    return confirm('Bạn có chắc muốn TỪ CHỐI đơn này?');
}
</script>