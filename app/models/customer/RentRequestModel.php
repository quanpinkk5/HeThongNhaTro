<?php
class RentRequestModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getRequestsByUser($userId) {
        $sql = "SELECT rr.*, r.title, r.price,
                (SELECT image_url FROM room_images WHERE room_id = r.id LIMIT 1) as room_img
                FROM rent_requests rr
                JOIN rooms r ON rr.room_id = r.id
                WHERE rr.user_id = ?
                ORDER BY rr.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}