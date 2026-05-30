<?php
class RentRequestModel {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // --- LẤY DANH SÁCH YÊU CẦU (CÓ LỌC) ---
    public function getRequests($landlord_id, $keyword = '', $status = '') {
        $sql = "
            SELECT 
                rr.*,
                u.name AS full_name, u.phone, u.email, u.cccd,
                r.title AS room_name, r.price,
                b.name AS building_name
            FROM rent_requests rr
            JOIN users u ON rr.user_id = u.id
            JOIN rooms r ON rr.room_id = r.id
            JOIN buildings b ON r.building_id = b.id
            WHERE r.landlord_id = ?";

        $types = "i";
        $params = [$landlord_id];

        if (!empty($keyword)) {
            $sql .= " AND (u.name LIKE ? OR u.phone LIKE ?)";
            $types .= "ss";
            $kw = "%$keyword%";
            $params[] = $kw;
            $params[] = $kw;
        }

        if (!empty($status)) {
            $sql .= " AND rr.status = ?";
            $types .= "s";
            $params[] = $status;
        }

        $sql .= " ORDER BY rr.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        
        // Binding parameters động cho MySQLi
        $bind_names[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] = &$params[$i];
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_names);

        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    // --- LẤY CHI TIẾT 1 YÊU CẦU ---
    public function getRequestById($req_id) {
        $sql = "
            SELECT rr.*, r.id AS room_id, r.title AS room_name
            FROM rent_requests rr
            JOIN rooms r ON rr.room_id = r.id
            WHERE rr.id = ? FOR UPDATE";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $req_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }

    // --- DUYỆT YÊU CẦU ---
    public function approveRequest($req_id, $room_id, $user_id, $room_name) {
        $this->conn->begin_transaction();
        try {
            // 1. Duyệt yêu cầu hiện tại
            $stmt1 = $this->conn->prepare("UPDATE rent_requests SET status = 'APPROVED' WHERE id = ?");
            $stmt1->bind_param("i", $req_id);
            $stmt1->execute();

            // 2. Hủy các yêu cầu khác của cùng phòng đó
            $stmt2 = $this->conn->prepare("UPDATE rent_requests SET status = 'REJECTED' WHERE room_id = ? AND id != ? AND status = 'PENDING'");
            $stmt2->bind_param("ii", $room_id, $req_id);
            $stmt2->execute();

            // 3. Gửi thông báo
            $notif_title = "Yêu cầu thuê phòng đã được chấp nhận";
            $notif_content = "Chúc mừng! Yêu cầu thuê phòng [$room_name] của bạn đã được chấp nhận. Vui lòng liên hệ chủ trọ để tiến hành ký hợp đồng.";
            $stmt3 = $this->conn->prepare("INSERT INTO notifications (user_id, title, content, type, link) VALUES (?, ?, ?, 'REQUEST', 'yeucauthue.php')");
            $stmt3->bind_param("iss", $user_id, $notif_title, $notif_content);
            $stmt3->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // --- TỪ CHỐI YÊU CẦU ---
    public function rejectRequest($req_id, $user_id, $room_name, $current_note, $reason) {
        $this->conn->begin_transaction();
        try {
            $note = trim($current_note . " | Lý do: " . $reason);
            
            // 1. Cập nhật trạng thái
            $stmt1 = $this->conn->prepare("UPDATE rent_requests SET status = 'REJECTED', note = ? WHERE id = ?");
            $stmt1->bind_param("si", $note, $req_id);
            $stmt1->execute();

            // 2. Gửi thông báo
            $notif_title = "Yêu cầu thuê phòng bị TỪ CHỐI";
            $notif_content = "Yêu cầu thuê phòng [$room_name] của bạn không được chấp nhận. Lý do: $reason";
            $stmt2 = $this->conn->prepare("INSERT INTO notifications (user_id, title, content, type, link) VALUES (?, ?, ?, 'REQUEST', 'yeucauthue.php')");
            $stmt2->bind_param("iss", $user_id, $notif_title, $notif_content);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>