<?php
class MaintenanceModel {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function checkRoomAccess($room_id, $user_id) {
        
        $sql = "SELECT r.id as true_room_id, r.title, r.landlord_id 
                FROM rooms r 
                LEFT JOIN contracts c ON r.id = c.room_id 
                WHERE (r.id = ? OR c.id = ?) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $room_id, $room_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createMaintenance($room_id, $reporter_name, $content, $level) {
        $sql = "INSERT INTO maintenance (room_id, reporter_name, content, level, status, created_at) 
                VALUES (?, ?, ?, ?, 'PENDING', NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isss", $room_id, $reporter_name, $content, $level);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    public function saveImage($m_id, $path) {
        $sql = "INSERT INTO maintenance_images (maintenance_id, image_url) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $m_id, $path);
        return $stmt->execute();
    }

    public function createNotification($u_id, $title, $msg, $link) {
        $sql = "INSERT INTO notifications (user_id, title, content, type, link, is_read, created_at) 
                VALUES (?, ?, ?, 'MAINTENANCE', ?, 0, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isss", $u_id, $title, $msg, $link);
        return $stmt->execute();
    }
}