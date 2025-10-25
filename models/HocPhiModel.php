<?php
require_once 'Database.php';

class HocPhiModel {
    private $db;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
    }
    
    // Tạm thời dùng mock data để test
    public function getAll() {
        return [
            [
                'id' => 1,
                'hoc_sinh' => 'Nguyễn Văn A',
                'thang' => 11,
                'so_tien' => 500000,
                'trang_thai' => 'CHUA_NOP'
            ],
            [
                'id' => 2, 
                'hoc_sinh' => 'Trần Thị B',
                'thang' => 11,
                'so_tien' => 500000,
                'trang_thai' => 'DA_NOP'
            ]
        ];
    }
}
?>