<?php
class RoomDetailModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getRoomById($room_id) {
        $sql = "SELECT r.*, b.address, u.name as owner_name, u.phone as owner_phone 
                FROM rooms r 
                INNER JOIN buildings b ON r.building_id = b.id
                INNER JOIN users u ON r.landlord_id = u.id 
                WHERE r.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getRoomServices($room_id) {
        $sql = "SELECT s.name, s.price FROM room_services rs 
                INNER JOIN services s ON rs.service_id = s.id 
                WHERE rs.room_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getRoomImages($room_id) {
        $sql = "SELECT image_url FROM room_images WHERE room_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}