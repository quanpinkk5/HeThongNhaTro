<?php
class MaintenanceModel
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    private function escape($str)
    {
        return $this->conn->real_escape_string($str);
    }

    // Lấy toàn bộ sự cố của chủ trọ
    public function getAll($landlord_id)
    {
        $lid = (int)$landlord_id;
        $sql = "SELECT m.*, r.title as room_name, b.name as building_name 
                FROM maintenance m
                JOIN rooms r ON m.room_id = r.id
                JOIN buildings b ON r.building_id = b.id
                WHERE r.landlord_id = $lid
                ORDER BY m.created_at DESC";
        $res = $this->conn->query($sql);
        $data = [];
        if ($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        return $data;
    }

    // Lấy ảnh đính kèm của sự cố
    public function getImages($maintenance_id)
    {
        $mid = (int)$maintenance_id;
        $res = $this->conn->query("SELECT image_url FROM maintenance_images WHERE maintenance_id = $mid");
        $images = [];
        if ($res) while ($row = $res->fetch_assoc()) $images[] = $row['image_url'];
        return $images;
    }

    // Lấy thông tin cơ bản của sự cố (dùng để check trước khi update)
    public function getIncidentInfo($id)
    {
        $mid = (int)$id;
        $res = $this->conn->query("SELECT status, room_id FROM maintenance WHERE id = $mid");
        return ($res) ? $res->fetch_assoc() : null;
    }

    // Tìm khách đang thuê phòng để gửi thông báo
    public function getActiveTenantByRoom($room_id)
    {
        $rid = (int)$room_id;
        $res = $this->conn->query("SELECT user_id FROM contracts WHERE room_id = $rid AND status = 'ACTIVE' LIMIT 1");
        return ($res && $row = $res->fetch_assoc()) ? $row['user_id'] : null;
    }

    // Cập nhật sự cố
    public function update($id, $level, $status, $cost)
    {
        $mid = (int)$id;
        $lvl = $this->escape($level);
        $st = $this->escape($status);
        $c = (float)$cost;
        return $this->conn->query("UPDATE maintenance SET level = '$lvl', status = '$st', cost = $c WHERE id = $mid");
    }

    // Gửi thông báo hệ thống
    public function notifyTenant($user_id, $title, $content)
    {
        $uid = (int)$user_id;
        $t = $this->escape($title);
        $c = $this->escape($content);
        $link = "baotri.php"; // Link phía khách hàng
        return $this->conn->query("INSERT INTO notifications (user_id, title, content, type, link) VALUES ($uid, '$t', '$c', 'MAINTENANCE', '$link')");
    }

    // Xóa sự cố
    public function delete($id)
    {
        $mid = (int)$id;
        $this->conn->begin_transaction();
        try {
            $this->conn->query("DELETE FROM maintenance_images WHERE maintenance_id = $mid");
            $this->conn->query("DELETE FROM maintenance WHERE id = $mid");
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
