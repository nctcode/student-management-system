# 🎓 HỆ THỐNG QUẢN LÝ HỌC SINH THCS-THPT

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.0-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

## 📋 Giới thiệu dự án

**Hệ thống Quản lý Học sinh THCS-THPT** là một ứng dụng web được phát triển nhằm số hóa và quản lý toàn diện các hoạt động trong nhà trường. Hệ thống hỗ trợ đa vai trò với các chức năng chuyên biệt cho từng đối tượng sử dụng.

### 🎯 Môn học: Phát triển ứng dụng

---

## ✨ Tính năng nổi bật

### 👨‍💼 Quản trị viên (Admin)
- **Dashboard** thống kê tổng quan
- Quản lý **người dùng và phân quyền**
- Quản lý **tuyển sinh và hồ sơ**
- Duyệt **đơn chuyển trường/lớp**
- Quản lý **học phí và thanh toán**
- **Báo cáo thống kê** toàn hệ thống

### 👩‍🏫 Giáo viên
- Quản lý **lớp chủ nhiệm**
- Nhập **đểm và kết quả học tập**
- Theo dõi **chuyên cần học sinh**
- Giao và chấm **bài tập**
- **Thời khóa biểu** giảng dạy

### 👨‍🎓 Học sinh
- Xem **thời khóa biểu**
- Theo dõi **điểm và kết quả học tập**
- Nộp **bài tập trực tuyến**
- Xem **thông báo từ nhà trường**
- Đăng ký **chuyển lớp** (nếu cần)

### 👨‍👩‍👧‍👦 Phụ huynh
- Theo dõi **kết quả học tập** của con
- Xem **lịch học và chuyên cần**
- **Thanh toán học phí** online
- Gửi **đơn chuyển trường** cho con
- Nhận **thông báo** từ nhà trường

### 🎓 Ban giám hiệu
- **Báo cáo** tổng quan toàn trường
- Quản lý **chất lượng giảng dạy**
- Theo dõi **kết quả học tập** các lớp
- **Phê duyệt** các đơn quan trọng

---

## 🛠 Công nghệ sử dụng

### Frontend
- **HTML5, CSS3, JavaScript**
- **Bootstrap 5** - Framework CSS
- **Font Awesome** - Icons
- **Chart.js** - Thống kê đồ họa

### Backend
- **PHP 8.0+** - Ngôn ngữ server
- **MySQL 8.0** - Hệ quản trị cơ sở dữ liệu
- **PDO** - Kết nối database an toàn
- **MVC Pattern** - Kiến trúc ứng dụng

### Development Tools
- **WAMP/XAMPP** - Local development
- **phpMyAdmin** - Quản lý database
- **VS Code** - Code editor

---

## 📁 Cấu trúc dự án
```
qlhs/
├── controllers/ # Điều khiển ứng dụng
│ ├── HomeController.php
│ ├── AuthController.php
│ └── DonChuyenLopTruongController.php
├── models/ # Xử lý dữ liệu
│ ├── Database.php
│ ├── HomeModel.php
│ └── DonChuyenLopTruongModel.php
├── views/ # Giao diện người dùng
│ ├── layouts/
│ │ ├── header.php
│ │ ├── footer.php
│ │ └── sidebar/
│ ├── home/
│ └── donchuyenloptruong/
├── assets/ # Tài nguyên tĩnh
│ ├── css/
│ ├── js/
│ └── images/
├── index.php # Router chính
└── qlhs.sql # Database schema
```

---

## 🚀 Hướng dẫn cài đặt

### Yêu cầu hệ thống
- **PHP**: 8.0 hoặc cao hơn
- **MySQL**: 8.0 hoặc cao hơn
- **Web server**: Apache với mod_rewrite
- **Extensions**: PDO, MySQLi, MBstring

### Các bước cài đặt

1. **Clone/Copy dự án**
   ```bash
   git clone [repository-url]
   cd qlhs

# 🏫 Hệ thống Quản lý Học sinh (QLHS)

## ⚙️ Cấu hình Database

1. **Tạo database `qlhs` trong MySQL**
2. **Import file:** `qlhs.sql`
3. **Cập nhật thông tin kết nối** trong file `models/Database.php`

---

## 🌐 Cấu hình Web Server

- Đặt **thư mục gốc (Document Root)** thành `public/` *(nếu có)*
- **Bật `mod_rewrite`** để hỗ trợ **URL thân thiện**

---

## 🚀 Truy cập Ứng dụng

**URL:**  
👉 [http://localhost/qlhs](http://localhost/qlhs)

**Tài khoản mặc định:**

| Vai trò   | Tên đăng nhập | Mật khẩu  |
|------------|----------------|------------|
| Admin      | `admin`        | `password` |
| Giáo viên  | `gvcn01`       | `password` |
| Học sinh   | `hs01`         | `password` |
| Phụ huynh  | `ph01`         | `password` |

---

## 📊 Cơ sở dữ liệu

Hệ thống sử dụng **30+ bảng** được thiết kế **chuẩn hóa**, bao gồm:

### 🗂 Các bảng chính

| Bảng | Mô tả |
|------|--------|
| `nguoidung`, `taikhoan` | Quản lý người dùng |
| `hocsinh`, `giaovien`, `phuhuynh` | Thông tin các vai trò |
| `lophoc`, `monhoc`, `khoi` | Quản lý học tập |
| `diem`, `ketquahoctap` | Kết quả học tập |
| `hocphi`, `thanhtoan` | Quản lý tài chính |
| `donchuyenloptruong` | Đơn chuyển trường |

---

## 👥 Phân vai trò và quyền hạn

| Vai trò | Quyền truy cập | Chức năng chính |
|----------|----------------|------------------|
| QTV | Toàn quyền | Quản lý hệ thống, người dùng |
| BGH | Cao cấp | Giám sát, báo cáo, phê duyệt |
| GIAOVIEN | Giáo viên | Quản lý lớp, nhập điểm |
| HOCSINH | Học sinh | Xem điểm, TKB, nộp bài |
| PHUHUYNH | Phụ huynh | Theo dõi con, thanh toán |

---

## 📱 Giao diện người dùng

### 🎨 Thiết kế

- **Responsive:** Tương thích mọi thiết bị  
- **User-friendly:** Dễ sử dụng, trực quan  
- **Professional:** Giao diện chuyên nghiệp  
- **Accessible:** Tuân thủ tiêu chuẩn web  

---

## 🖼 Ảnh chụp màn hình
*(Thêm ảnh chụp các trang chính tại đây)*

---

## 🔒 Bảo mật

- Xác thực đa tầng với **session management**
- Phân quyền chi tiết theo **vai trò**
- **SQL Injection Protection** với *PDO prepared statements*
- **XSS Prevention** với *htmlspecialchars()*
- **CSRF Protection** trong các form quan trọng

---

## 📈 Hướng phát triển

### Phiên bản tiếp theo

- 📱 **Mobile App** cho phụ huynh / học sinh  
- 🔗 **API RESTful** cho tích hợp hệ thống  
- 🔔 **Real-time notifications**  
- 🧠 **Machine Learning** dự đoán kết quả học tập  
- ✉️ **Tích hợp SMS/Email notifications**

---

## 👨‍💻 Thành viên phát triển

| Họ tên | Vai trò | Công việc chính |
|--------|----------|------------------|
| [Nguyễn Chí Thuận] | Project Manager | UI/UX, Bootstrap, JavaScript |
| [Đỗ Phan Bảo lộc] | Backend Developer | UI/UX, Bootstrap, JavaScript  |
| [Lý Thị Yến] | Frontend Developer | UI/UX, Bootstrap, JavaScript |
| [Lê Thị Thu Hằng] | Frontend Developer | UI/UX, Bootstrap, JavaScript |
| [Cao Hoàng Minh Cơ] | Frontend Developer | UI/UX, Bootstrap, JavaScript |
| [Phan Quốc Kiệt] | Frontend Developer | UI/UX, Bootstrap, JavaScript |
| [Trần Lê Phương Khánh] | Frontend Developer | UI/UX, Bootstrap, JavaScript |

---

## 📞 Hỗ trợ và liên hệ

**Giáo viên hướng dẫn:** ThS. Lê Thùy Trang 
**Liên hệ phát triển:**

| Thông tin | Nội dung |
|------------|-----------|
| 📧 Email | [email] |
| 🌐 Website | [website] |
| 📱 SĐT | [số điện thoại] |

---

## 📄 Giấy phép

Dự án được phát triển cho **mục đích học tập và nghiên cứu**.  
Mọi sự sao chép hoặc sử dụng cho **mục đích thương mại** cần có sự cho phép của nhóm phát triển.

---

<div align="center">

🎉 **Cảm ơn đã quan tâm đến dự án!**  
*"Ứng dụng công nghệ để nâng cao chất lượng giáo dục"*

</div>

