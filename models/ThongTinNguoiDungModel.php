<?php
require_once 'models/Database.php';

class ThongTinNguoiDungModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy thông tin người dùng theo mã
    public function getUserById($maNguoiDung)
    {
        $sql = "SELECT 
                    n.maNguoiDung,
                    n.hoTen,
                    n.ngaySinh,
                    n.gioiTinh,
                    n.soDienThoai,
                    n.email,
                    n.diaChi,
                    n.cccd,
                    n.loaiNguoiDung,
                    l.tenLop,
                    l.maLop
                FROM nguoidung n
                LEFT JOIN hocsinh hs ON n.maNguoiDung = hs.maNguoiDung
                LEFT JOIN lophoc l ON hs.maLop = l.maLop
                WHERE n.maNguoiDung = :maNguoiDung";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['maNguoiDung' => $maNguoiDung]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách người dùng với bộ lọc
    public function getUsersWithFilters($filters)
    {
        $whereConditions = [];
        $params = [];

        // Lọc theo vai trò
        if (!empty($filters['vaiTro'])) {
            $whereConditions[] = "n.loaiNguoiDung = :vaiTro";
            $params[':vaiTro'] = $filters['vaiTro'];
        }

        // Lọc theo tên
        if (!empty($filters['ten'])) {
            $whereConditions[] = "n.hoTen LIKE :ten";
            $params[':ten'] = '%' . $filters['ten'] . '%';
        }

        // Lọc theo lớp (chỉ cho học sinh)
        if (!empty($filters['maLop']) && ($filters['vaiTro'] == 'HOCSINH' || empty($filters['vaiTro']))) {
            $whereConditions[] = "hs.maLop = :maLop";
            $params[':maLop'] = $filters['maLop'];
            // Đảm bảo chỉ lấy học sinh
            $whereConditions[] = "n.loaiNguoiDung = 'HOCSINH'";
        }

        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }

        // Đếm tổng số bản ghi
        $countSql = "SELECT COUNT(DISTINCT n.maNguoiDung) as total
                     FROM nguoidung n
                     LEFT JOIN hocsinh hs ON n.maNguoiDung = hs.maNguoiDung
                     LEFT JOIN lophoc l ON hs.maLop = l.maLop
                     $whereClause";

        $countStmt = $this->conn->prepare($countSql);
        $countStmt->execute($params);
        $totalResult = $countStmt->fetch(PDO::FETCH_ASSOC);
        $total = $totalResult['total'];

        // Lấy dữ liệu với phân trang
        $perPage = 10;
        $offset = ($filters['page'] - 1) * $perPage;

        $sql = "SELECT DISTINCT
                    n.maNguoiDung,
                    n.hoTen,
                    n.ngaySinh,
                    n.gioiTinh,
                    n.soDienThoai,
                    n.email,
                    n.diaChi,
                    n.cccd,
                    n.loaiNguoiDung,
                    l.tenLop,
                    l.maLop
                FROM nguoidung n
                LEFT JOIN hocsinh hs ON n.maNguoiDung = hs.maNguoiDung
                LEFT JOIN lophoc l ON hs.maLop = l.maLop
                $whereClause
                ORDER BY n.maNguoiDung
                LIMIT :offset, :limit";

        $stmt = $this->conn->prepare($sql);
        
        // Bind các tham số
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    // Cập nhật thông tin người dùng
    public function updateUser($maNguoiDung, $data)
    {
        $sql = "UPDATE nguoidung SET
                    hoTen = :hoTen,
                    ngaySinh = :ngaySinh,
                    gioiTinh = :gioiTinh,
                    soDienThoai = :soDienThoai,
                    email = :email,
                    diaChi = :diaChi,
                    cccd = :cccd
                WHERE maNguoiDung = :maNguoiDung";

        $stmt = $this->conn->prepare($sql);

        $params = [
            ':hoTen' => $data['hoTen'],
            ':ngaySinh' => $data['ngaySinh'],
            ':gioiTinh' => $data['gioiTinh'],
            ':soDienThoai' => $data['soDienThoai'],
            ':email' => $data['email'],
            ':diaChi' => $data['diaChi'],
            ':cccd' => $data['cccd'],
            ':maNguoiDung' => $maNguoiDung
        ];

        return $stmt->execute($params);
    }
}