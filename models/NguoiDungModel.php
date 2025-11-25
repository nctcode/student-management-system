<?php
require_once __DIR__ . '/Database.php';

class NguoiDungModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả thông tin của 1 người dùng bằng ID
    public function getUserById($id) {
        $sql = "SELECT * FROM nguoidung WHERE maNguoiDung = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>