<?php
class ContractModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getActiveContracts($user_id) {
        $sql = "SELECT c.id, c.room_id, c.start_date, c.end_date, c.status, 
                       r.title, r.price, b.address, 
                       (SELECT image_url FROM room_images WHERE room_id = r.id LIMIT 1) as room_img
                FROM contracts c
                INNER JOIN rooms r ON c.room_id = r.id
                INNER JOIN buildings b ON r.building_id = b.id
                WHERE c.user_id = ? AND c.status = 'ACTIVE'
                ORDER BY c.id DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}