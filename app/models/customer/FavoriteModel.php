<?php
class FavoriteModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getFavoritesByUser($userId) {
        $sql = "SELECT r.*, b.name as building_name,
                (SELECT image_url FROM room_images WHERE room_id = r.id LIMIT 1) as main_image 
                FROM rooms r 
                INNER JOIN favorites f ON r.id = f.room_id 
                INNER JOIN buildings b ON r.building_id = b.id
                WHERE f.user_id = ? 
                ORDER BY r.id DESC"; 
        
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
             
             return $this->getFavoritesBackup($userId);
        }

        $stmt->bind_param("i", $userId); 
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    
    private function getFavoritesBackup($userId) {
        $sql = "SELECT r.*, 
                (SELECT image_url FROM room_images WHERE room_id = r.id LIMIT 1) as main_image 
                FROM rooms r 
                INNER JOIN favorites f ON r.id = f.room_id 
                WHERE f.user_id = ? 
                ORDER BY r.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function toggleFavorite($userId, $roomId) {
        $stmt = $this->db->prepare("SELECT user_id FROM favorites WHERE user_id = ? AND room_id = ?");
        $stmt->bind_param("ii", $userId, $roomId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $del = $this->db->prepare("DELETE FROM favorites WHERE user_id = ? AND room_id = ?");
            $del->bind_param("ii", $userId, $roomId);
            $del->execute();
            
            return ['status' => 'success', 'action' => 'removed'];
        } else {
            $ins = $this->db->prepare("INSERT INTO favorites (user_id, room_id) VALUES (?, ?)");
            $ins->bind_param("ii", $userId, $roomId);
            $ins->execute();
            return ['status' => 'success', 'action' => 'added'];
        }
    }
}