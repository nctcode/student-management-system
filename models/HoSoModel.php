<?php
require_once 'models/Database.php'; // file kết nối MySQL của bạn

class HoSoModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection(); // PDO connection
    }

    public function getHoSoByMa($maHoSo)
    {
        $sql = "SELECT * FROM hosotuyensinh WHERE maHoSo = :maHoSo";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['maHoSo' => $maHoSo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>
