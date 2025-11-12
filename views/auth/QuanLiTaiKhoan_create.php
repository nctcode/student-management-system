<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Tài Khoản Mới</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        .form-section {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid #2196F3;
        }

        .form-section h4 {
            color: #2196F3;
            margin-bottom: 15px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #555;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        select:focus {
            border-color: #2196F3;
            outline: none;
            box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
        }

        .role-fields {
            margin-top: 20px;
            padding: 20px;
            background-color: #f0f7ff;
            border-radius: 8px;
            border: 1px solid #e1f0ff;
            transition: all 0.3s ease;
        }

        .role-fields h4 {
            color: #1976D2;
            margin-bottom: 15px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .role-fields h4::before {
            content: "▶";
            font-size: 12px;
            color: #1976D2;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2196F3;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background-color: #1976D2;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
            padding: 8px;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #2196F3;
        }

        /* Animation cho các field xuất hiện */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .role-fields[style*="display: block"] {
            animation: fadeIn 0.3s ease-out;
        }

        /* Style riêng cho từng loại role */
        #giaovienFields {
            border-left: 4px solid #4CAF50;
        }

        #giaovienFields h4 {
            color: #4CAF50;
        }

        #hocsinhFields {
            border-left: 4px solid #FF9800;
        }

        #hocsinhFields h4 {
            color: #FF9800;
        }

        #phuhuynhFields {
            border-left: 4px solid #9C27B0;
        }

        #phuhuynhFields h4 {
            color: #9C27B0;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 20px;
            }
            
            .form-section {
                padding: 15px;
            }
            
            .role-fields {
                padding: 15px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Thêm Tài Khoản Mới</h2>
    
    <form action="index.php?controller=QuanLyTaiKhoan&action=store" method="post">
        <!-- Thông tin chung -->
        <div class="form-section">
            <h4>Thông tin đăng nhập</h4>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tenDangNhap">Tên đăng nhập:</label>
                    <input type="text" id="tenDangNhap" name="tenDangNhap" required placeholder="Nhập tên đăng nhập">
                </div>
                <div class="form-group">
                    <label for="matKhau">Mật khẩu:</label>
                    <input type="password" id="matKhau" name="matKhau" required placeholder="Nhập mật khẩu">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="hoTen">Họ tên:</label>
                    <input type="text" id="hoTen" name="hoTen" required placeholder="Nhập họ tên đầy đủ">
                </div>
                <div class="form-group">
                    <label for="vaiTro">Vai trò:</label>
                    <select id="vaiTro" name="vaiTro" required onchange="showRoleFields()">
                        <option value="">-- Chọn vai trò --</option>
                        <option value="QTV">Quản trị viên</option>
                        <option value="BGH">Ban giám hiệu</option>
                        <option value="GIAOVIEN">Giáo viên</option>
                        <option value="HOCSINH">Học sinh</option>
                        <option value="PHUHUYNH">Phụ huynh</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Fields for GIAOVIEN -->
        <div id="giaovienFields" class="role-fields" style="display: none;">
            <h4>Thông tin giáo viên</h4>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="chuyenMon">Chuyên môn:</label>
                    <input type="text" id="chuyenMon" name="chuyenMon" placeholder="Ví dụ: Toán, Văn, Anh...">
                </div>
                <div class="form-group">
                    <label for="loaiGiaoVien">Loại giáo viên:</label>
                    <select id="loaiGiaoVien" name="loaiGiaoVien">
                        <option value="">-- Chọn loại giáo viên --</option>
                        <option value="GV_BO_MON">Giáo viên bộ môn</option>
                        <option value="GV_CHU_NHIEM">Giáo viên chủ nhiệm</option>
                    </select>
                </div>
            </div>

            <label for="maToTruong">Mã tổ trưởng (nếu có):</label>
            <input type="number" id="maToTruong" name="maToTruong" placeholder="Để trống nếu không có">
        </div>

        <!-- Fields for HOCSINH -->
        <div id="hocsinhFields" class="role-fields" style="display: none;">
            <h4>Thông tin học sinh</h4>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="maLop">Mã lớp:</label>
                    <input type="number" id="maLop" name="maLop" placeholder="Nhập mã lớp">
                </div>
                <div class="form-group">
                    <label for="maPhuHuynh">Mã phụ huynh:</label>
                    <input type="number" id="maPhuHuynh" name="maPhuHuynh" placeholder="Nhập mã phụ huynh">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="maHoSo">Mã hồ sơ:</label>
                    <input type="number" id="maHoSo" name="maHoSo" placeholder="Nhập mã hồ sơ">
                </div>
                <div class="form-group">
                    <label for="ngayNhapHoc">Ngày nhập học:</label>
                    <input type="date" id="ngayNhapHoc" name="ngayNhapHoc">
                </div>
            </div>

            <label for="trangThai">Trạng thái:</label>
            <select id="trangThai" name="trangThai">
                <option value="DANG_HOC">Đang học</option>
                <option value="DA_TOT_NGHIEP">Đã tốt nghiệp</option>
                <option value="CHUYEN_TRUONG">Chuyển trường</option>
                <option value="THOI_HOC">Thôi học</option>
            </select>
        </div>

        <!-- Fields for PHUHUYNH -->
        <div id="phuhuynhFields" class="role-fields" style="display: none;">
            <h4>Thông tin phụ huynh</h4>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="ngheNghiep">Nghề nghiệp:</label>
                    <input type="text" id="ngheNghiep" name="ngheNghiep" placeholder="Ví dụ: Kỹ sư, Bác sĩ, Giáo viên...">
                </div>
                <div class="form-group">
                    <label for="moiQuanHe">Mối quan hệ:</label>
                    <select id="moiQuanHe" name="moiQuanHe">
                        <option value="">-- Chọn mối quan hệ --</option>
                        <option value="CHA">Cha</option>
                        <option value="ME">Mẹ</option>
                        <option value="NGUOI_GIAM_HO">Người giám hộ</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit">Tạo tài khoản</button>
    </form>
    
    <a class="back-link" href="index.php?controller=QuanLyTaiKhoan&action=index">← Quay lại danh sách tài khoản</a>
</div>

<script>
function showRoleFields() {
    // Ẩn tất cả các field role
    const roleFields = document.querySelectorAll('.role-fields');
    roleFields.forEach(field => {
        field.style.display = 'none';
    });
    
    // Hiện field tương ứng với vai trò được chọn
    const role = document.getElementById('vaiTro').value;
    let targetField = null;
    
    switch(role) {
        case 'GIAOVIEN':
            targetField = document.getElementById('giaovienFields');
            break;
        case 'HOCSINH':
            targetField = document.getElementById('hocsinhFields');
            break;
        case 'PHUHUYNH':
            targetField = document.getElementById('phuhuynhFields');
            break;
    }
    
    if (targetField) {
        targetField.style.display = 'block';
    }
}

// Gọi hàm khi trang load để xử lý trạng thái ban đầu
document.addEventListener('DOMContentLoaded', function() {
    showRoleFields();
    
    // Thêm sự kiện change cho select vai trò
    document.getElementById('vaiTro').addEventListener('change', showRoleFields);
});
</script>
</body>
</html>