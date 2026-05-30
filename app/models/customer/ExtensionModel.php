<?php
class ExtensionModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getActiveContract($contract_id, $user_id) {
        $sql = "SELECT c.*, r.title as room_name, r.landlord_id, b.name as building_name 
                FROM contracts c
                JOIN rooms r ON c.room_id = r.id
                JOIN buildings b ON r.building_id = b.id
                WHERE c.id = ? AND c.user_id = ? AND c.status = 'ACTIVE' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $contract_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createRequest($contract_id, $months, $note, $landlord_id, $room_name) {
        $this->db->begin_transaction();
        try {
            
            $sql_req = "INSERT INTO extension_requests (contract_id, months, note, status) VALUES (?, ?, ?, 'PENDING')";
            $stmt_req = $this->db->prepare($sql_req);
            $stmt_req->bind_param("iis", $contract_id, $months, $note);
            $stmt_req->execute();

            
            $noti_title = "Yêu cầu gia hạn hợp đồng mới";
            $noti_content = "Khách thuê phòng $room_name muốn gia hạn thêm $months tháng.";
            $noti_link = "contract.php";
            
            $sql_noti = "INSERT INTO notifications (user_id, title, content, type, link) VALUES (?, ?, ?, 'REQUEST', ?)";
            $stmt_noti = $this->db->prepare($sql_noti);
            $stmt_noti->bind_param("isss", $landlord_id, $noti_title, $noti_content, $noti_link);
            $stmt_noti->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
}