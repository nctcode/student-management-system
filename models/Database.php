<?php
class Database {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=localhost;dbname=qlhs;charset=utf8mb4",
                "root", 
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch(PDOException $e) {
            die("Lỗi kết nối database: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn; // Đảm bảo trả về đối tượng PDO, không phải true
    }
}
?>