<?php
require_once __DIR__ . '/../../../config/database.php';

class CustomerModel {
    private $conn;

    public function __construct() {
        global $con; 
        $this->conn = $con;
    }

    
    public function getRooms($user_id, $search, $district) {
        $sql = "SELECT r.*, b.address, b.name as building_name,
                (SELECT image_url FROM room_images WHERE room_id = r.id LIMIT 1) as main_image,
                (SELECT COUNT(*) FROM favorites f WHERE f.room_id = r.id AND f.user_id = ?) as is_fav 
                FROM rooms r 
                INNER JOIN buildings b ON r.building_id = b.id
                WHERE r.status_room = 'EMPTY' AND r.status = 'APPROVED'";

        $params = [$user_id];
        $types = "i"; 

        if (!empty($search)) {
            $sql .= " AND (r.title LIKE ? OR b.address LIKE ? OR b.name LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= "sss"; 
        }

        if (!empty($district)) {
            $sql .= " AND b.address LIKE ?";
            $district_param = "%$district%";
            $params[] = $district_param;
            $types .= "s";
        }

        $sql .= " ORDER BY r.id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params); 
        $stmt->execute();
        
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getAreas() {
        $sql = "SELECT DISTINCT name FROM areas WHERE status = 'ACTIVE' ORDER BY name ASC";
        $result = $this->conn->query($sql);
        
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}