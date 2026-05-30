<?php
class RequestModel {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function hasPendingRequest($room_id, $user_id) {
        $sql = "SELECT id FROM rent_requests WHERE room_id = ? AND user_id = ? AND status = 'PENDING'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $room_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function createRentRequest($room_id, $user_id, $user_name) {
        $this->conn->begin_transaction();
        try {
            $sql_req = "INSERT INTO rent_requests (room_id, user_id, note, status, created_at) VALUES (?, ?, 'Yêu cầu từ web', 'PENDING', NOW())";
            $stmt_req = $this->conn->prepare($sql_req);
            $stmt_req->bind_param("ii", $room_id, $user_id);
            $stmt_req->execute();

            $sql_room = "SELECT landlord_id, title FROM rooms WHERE id = ?";
            $stmt_room = $this->conn->prepare($sql_room);
            $stmt_room->bind_param("i", $room_id);
            $stmt_room->execute();
            $room = $stmt_room->get_result()->fetch_assoc();

            if ($room) {
                $sql_noti = "INSERT INTO notifications (user_id, title, content, type, link, is_read, created_at) VALUES (?, ?, ?, 'REQUEST', ?, 0, NOW())";
                $stmt_noti = $this->conn->prepare($sql_noti);
                $title = "Yêu cầu thuê mới";
                $content = "Khách $user_name muốn thuê: " . $room['title'];
                $link = "rentrequest.php";
                $stmt_noti->bind_param("isss", $room['landlord_id'], $title, $content, $link);
                $stmt_noti->execute();
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}