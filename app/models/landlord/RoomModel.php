<?php
class RoomModel
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    private function esc($str)
    {
        return $this->conn->real_escape_string($str);
    }

    public function getBuildings($landlord_id)
    {
        $lid = (int)$landlord_id;
        $res = $this->conn->query("SELECT id, name FROM buildings WHERE landlord_id = $lid ORDER BY name ASC");
        $data = [];
        if ($res) while ($r = $res->fetch_assoc()) $data[] = $r;
        return $data;
    }

    public function getServices($landlord_id)
    {
        $lid = (int)$landlord_id;
        $res = $this->conn->query("SELECT * FROM services WHERE landlord_id = $lid");
        $data = [];
        if ($res) while ($r = $res->fetch_assoc()) $data[] = $r;
        return $data;
    }

    public function getAllRooms($landlord_id)
    {
        $lid = (int)$landlord_id;
        $sql = "SELECT r.*, b.name AS building_name, b.address AS building_address,
                (SELECT image_url FROM room_images ri WHERE ri.room_id = r.id LIMIT 1) AS thumbnail,
                (SELECT GROUP_CONCAT(service_id) FROM room_services rs WHERE rs.room_id = r.id) AS service_ids,
                (SELECT GROUP_CONCAT(CONCAT(s.name, ' (', FORMAT(s.price, 0), ' đ/', s.unit, ')') SEPARATOR ', ')
                 FROM room_services rs JOIN services s ON rs.service_id = s.id WHERE rs.room_id = r.id) AS service_details
                FROM rooms r JOIN buildings b ON r.building_id = b.id
                WHERE r.landlord_id = $lid AND r.status != 'HIDDEN' ORDER BY r.created_at DESC";

        $res = $this->conn->query($sql);
        $data = [];
        if ($res) while ($r = $res->fetch_assoc()) $data[] = $r;
        return $data;
    }

    public function addRoom($building_id, $landlord_id, $title, $price, $area, $desc)
    {
        $bid = (int)$building_id;
        $lid = (int)$landlord_id;
        $t = $this->esc($title);
        $p = (float)$price;
        $a = (float)$area;
        $d = $this->esc($desc);

        $sql = "INSERT INTO rooms (building_id, landlord_id, title, price, area, description, status_room, status) 
                VALUES ($bid, $lid, '$t', $p, $a, '$d', 'EMPTY', 'PENDING')";

        if ($this->conn->query($sql))
            return $this->conn->insert_id;
        return 0;
    }

    public function updateRoom($id, $landlord_id, $title, $price, $area, $desc)
    {
        $rid = (int)$id;
        $lid = (int)$landlord_id;
        $t = $this->esc($title);
        $p = (float)$price;
        $a = (float)$area;
        $d = $this->esc($desc);

        $sql = "UPDATE rooms SET title='$t', price=$p, area=$a, description='$d' WHERE id=$rid AND landlord_id=$lid";
        return $this->conn->query($sql);
    }

    public function deleteRoomServices($room_id)
    {
        $rid = (int)$room_id;
        return $this->conn->query("DELETE FROM room_services WHERE room_id = $rid");
    }

    public function addRoomServices($room_id, $services)
    {
        $rid = (int)$room_id;
        if (empty($services)) return;
        foreach ($services as $sid) {
            $s = (int)$sid;
            $this->conn->query("INSERT INTO room_services (room_id, service_id, calculation) VALUES ($rid, $s, 'PER_ROOM')");
        }
    }

    public function notifyAdmins($title, $content, $link)
    {
        $t = $this->esc($title);
        $c = $this->esc($content);
        $l = $this->esc($link);
        $res = $this->conn->query("SELECT id FROM users WHERE role = 'ADMIN' AND status = 'ACTIVE'");
        if ($res) {
            while ($admin = $res->fetch_assoc()) {
                $aid = (int)$admin['id'];
                $this->conn->query("INSERT INTO notifications (user_id, title, content, type, link) VALUES ($aid, '$t', '$c', 'SYSTEM', '$l')");
            }
        }
    }

    public function getRoomStatus($id)
    {
        $rid = (int)$id;
        $res = $this->conn->query("SELECT status_room FROM rooms WHERE id = $rid");
        return ($res && $row = $res->fetch_assoc()) ? $row['status_room'] : null;
    }

    public function getRoomImages($id)
    {
        $rid = (int)$id;
        $res = $this->conn->query("SELECT image_url FROM room_images WHERE room_id = $rid");
        $data = [];
        if ($res) while ($r = $res->fetch_assoc()) $data[] = $r['image_url'];
        return $data;
    }

    public function deleteRoomImagesDb($id)
    {
        $rid = (int)$id;
        return $this->conn->query("DELETE FROM room_images WHERE room_id = $rid");
    }

    public function deleteRoom($id, $landlord_id)
    {
        $rid = (int)$id;
        $lid = (int)$landlord_id;
        return $this->conn->query("UPDATE rooms SET status = 'HIDDEN' WHERE id = $rid AND landlord_id = $lid");
    }

    public function saveImage($room_id, $image_url)
    {
        $rid = (int)$room_id;
        $url = $this->esc($image_url);
        return $this->conn->query("INSERT INTO room_images (room_id, image_url) VALUES ($rid, '$url')");
    }

    public function getRoomForPost($id, $landlord_id)
    {
        $rid = (int)$id;
        $lid = (int)$landlord_id;
        $res = $this->conn->query("SELECT r.id, r.status_room, r.status, (SELECT COUNT(*) FROM room_images ri WHERE ri.room_id = r.id) AS img_count FROM rooms r WHERE r.id = $rid AND r.landlord_id = $lid");
        return ($res) ? $res->fetch_assoc() : null;
    }

    public function updateRoomPostStatus($id)
    {
        $rid = (int)$id;
        return $this->conn->query("UPDATE rooms SET status = 'PENDING', created_at = NOW() WHERE id = $rid");
    }
}
