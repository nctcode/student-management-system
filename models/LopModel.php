<?php
class LopModel {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách tất cả lớp học
    public function getTatCaLop() {
        $stmt = $this->conn->query("SELECT maLop, tenLop FROM lophoc ORDER BY tenLop");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
