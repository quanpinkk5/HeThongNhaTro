<?php
class ContractModel {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    private function escape($str) {
        return $this->conn->real_escape_string($str);
    }

    public function getBuildings($landlord_id) {
        $lid = (int)$landlord_id;
        $res = $this->conn->query("SELECT id, name FROM buildings WHERE landlord_id = $lid");
        $data = [];
        if($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function getTenants($landlord_id) {
        $lid = (int)$landlord_id;
        $sql = "SELECT DISTINCT u.id, u.name, u.cccd 
                FROM users u 
                JOIN rent_requests rr ON u.id = rr.user_id 
                JOIN rooms r ON rr.room_id = r.id 
                WHERE rr.status = 'APPROVED' AND r.landlord_id = $lid";
        $res = $this->conn->query($sql);
        $data = [];
        if($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function getValidRooms($building_id) {
        $bid = (int)$building_id;
        $sql = "SELECT id, title, price FROM rooms WHERE building_id = $bid AND status_room = 'EMPTY' AND status = 'APPROVED'";
        $res = $this->conn->query($sql);
        $data = [];
        if($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function getContracts($landlord_id, $f_building, $f_status, $f_keyword, $limit, $offset) {
        $lid = (int)$landlord_id;
        $sql = "SELECT c.*, u.name as user_name, r.title as room_name, b.name as building_name, 
                       u.cccd, u.phone, u.email, b.address as building_address
                FROM contracts c
                JOIN users u ON c.user_id = u.id 
                JOIN rooms r ON c.room_id = r.id 
                JOIN buildings b ON r.building_id = b.id
                WHERE r.landlord_id = $lid";

        if (!empty($f_building)) {
            $bid = (int)$f_building;
            $sql .= " AND b.id = $bid";
        }
        if (!empty($f_keyword)) {
            $kw = $this->escape($f_keyword);
            $sql .= " AND (u.name LIKE '%$kw%' OR r.title LIKE '%$kw%' OR c.id LIKE '%$kw%')";
        }

        $today = date('Y-m-d');
        if ($f_status == 'active') {
            $sql .= " AND c.status='ACTIVE' AND c.end_date >= '$today'";
        } elseif ($f_status == 'warning') {
            $sql .= " AND c.status='ACTIVE' AND c.end_date >= '$today' AND c.end_date <= DATE_ADD('$today', INTERVAL 30 DAY)";
        } elseif ($f_status == 'expired') {
            $sql .= " AND c.status='ACTIVE' AND c.end_date < '$today'";
        } elseif ($f_status == 'ended') {
            $sql .= " AND c.status='ENDED'";
        }

        $sql .= " ORDER BY c.status ASC, c.end_date ASC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        $res = $this->conn->query($sql);
        $data = [];
        if($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function getRenewalRequests($landlord_id) {
        $lid = (int)$landlord_id;
        $sql = "SELECT er.*, r.title as room_name, u.name as user_name 
                FROM extension_requests er 
                JOIN contracts c ON er.contract_id = c.id 
                JOIN rooms r ON c.room_id = r.id 
                JOIN users u ON c.user_id = u.id 
                WHERE r.landlord_id = $lid AND er.status = 'PENDING'";
        $res = $this->conn->query($sql);
        $data = [];
        if($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function addContract($room_id, $user_id, $start_date, $duration) {
        $rid = (int)$room_id;
        $uid = (int)$user_id;
        $sd = $this->escape($start_date);
        $end_date = date('Y-m-d', strtotime("+$duration months", strtotime($sd)));

        $this->conn->begin_transaction();
        try {
            $this->conn->query("INSERT INTO contracts (room_id, user_id, start_date, end_date, status) VALUES ($rid, $uid, '$sd', '$end_date', 'ACTIVE')");
            $this->conn->query("UPDATE rooms SET status_room = 'RENTED' WHERE id = $rid");
            $this->conn->query("INSERT INTO notifications (user_id, title, content, type, link) VALUES ($uid, 'Hợp đồng mới', 'Hợp đồng thuê phòng của bạn đã được tạo.', 'SYSTEM', 'hopdong.php')");
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function renewContract($id, $months) {
        $cid = (int)$id;
        $m = (int)$months;
        
        $res = $this->conn->query("SELECT end_date FROM contracts WHERE id = $cid");
        if ($res && $row = $res->fetch_assoc()) {
            $new_end = date('Y-m-d', strtotime("+$m months", strtotime($row['end_date'])));
            return $this->conn->query("UPDATE contracts SET end_date = '$new_end', status = 'ACTIVE' WHERE id = $cid");
        }
        return false;
    }

    public function cancelContract($id) {
        $cid = (int)$id;
        $res = $this->conn->query("SELECT room_id FROM contracts WHERE id = $cid");
        $rid = ($res && $row = $res->fetch_assoc()) ? (int)$row['room_id'] : 0;

        $this->conn->begin_transaction();
        try {
            $this->conn->query("UPDATE contracts SET status = 'ENDED', end_date = CURDATE() WHERE id = $cid");
            if ($rid > 0) $this->conn->query("UPDATE rooms SET status_room = 'EMPTY' WHERE id = $rid");
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function approveExtension($req_id) {
        $rid = (int)$req_id;
        $res = $this->conn->query("SELECT contract_id, months FROM extension_requests WHERE id = $rid");
        if ($res && $req = $res->fetch_assoc()) {
            $cid = (int)$req['contract_id'];
            $m = (int)$req['months'];
            
            $resC = $this->conn->query("SELECT end_date, user_id FROM contracts WHERE id = $cid");
            if ($resC && $contract = $resC->fetch_assoc()) {
                $uid = (int)$contract['user_id'];
                $new_end = date('Y-m-d', strtotime("+$m months", strtotime($contract['end_date'])));

                $this->conn->begin_transaction();
                try {
                    $this->conn->query("UPDATE contracts SET end_date = '$new_end', status = 'ACTIVE' WHERE id = $cid");
                    $this->conn->query("UPDATE extension_requests SET status = 'APPROVED' WHERE id = $rid");
                    $this->conn->query("INSERT INTO notifications (user_id, title, content, type, link) VALUES ($uid, 'Gia hạn thành công', 'Yêu cầu gia hạn của bạn đã được duyệt.', 'SYSTEM', 'phongdangthue.php')");
                    $this->conn->commit();
                    return true;
                } catch (Exception $e) {
                    $this->conn->rollback();
                    return false;
                }
            }
        }
        return false;
    }

    public function rejectExtension($req_id) {
        $rid = (int)$req_id;
        $res = $this->conn->query("SELECT c.user_id FROM extension_requests er JOIN contracts c ON er.contract_id = c.id WHERE er.id = $rid");
        if ($res && $row = $res->fetch_assoc()) {
            $uid = (int)$row['user_id'];
            $this->conn->query("UPDATE extension_requests SET status = 'REJECTED' WHERE id = $rid");
            $this->conn->query("INSERT INTO notifications (user_id, title, content, type, link) VALUES ($uid, 'Yêu cầu bị từ chối', 'Chủ trọ đã từ chối yêu cầu gia hạn.', 'SYSTEM', 'phongdangthue.php')");
            return true;
        }
        return false;
    }
}
?>