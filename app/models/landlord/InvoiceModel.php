<?php
class InvoiceModel {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    private function escape($str) {
        return $this->conn->real_escape_string($str);
    }

    // Lấy tòa nhà
    public function getBuildings($landlord_id) {
        $lid = (int)$landlord_id;
        $res = $this->conn->query("SELECT id, name FROM buildings WHERE landlord_id = $lid");
        $data = [];
        if ($res) while ($r = $res->fetch_assoc()) $data[] = $r;
        return $data;
    }

    // Lấy danh sách phòng đang thuê
    public function getRentedRooms($building_id) {
        $bid = (int)$building_id;
        $sql = "SELECT c.id as contract_id, r.title as room_name, r.price, u.name as user_name 
                FROM contracts c    
                JOIN rooms r ON c.room_id = r.id 
                JOIN users u ON c.user_id = u.id 
                WHERE r.building_id = $bid AND c.status = 'ACTIVE' AND r.status_room = 'RENTED'";
        $res = $this->conn->query($sql);
        $data = [];
        if ($res) while ($r = $res->fetch_assoc()) $data[] = $r;
        return $data;
    }

    // Lấy dịch vụ của phòng theo hợp đồng
    public function getRoomServices($contract_id) {
        $cid = (int)$contract_id;
        $res = $this->conn->query("SELECT room_id FROM contracts WHERE id = $cid");
        if ($res && $row = $res->fetch_assoc()) {
            $rid = (int)$row['room_id'];
            $sql = "SELECT s.id, s.name, s.price, s.unit, s.type 
                    FROM room_services rs JOIN services s ON rs.service_id = s.id 
                    WHERE rs.room_id = $rid";
            $res_svc = $this->conn->query($sql);
            $data = [];
            if ($res_svc) while ($s = $res_svc->fetch_assoc()) $data[] = $s;
            return $data;
        }
        return [];
    }

    // Lấy danh sách hóa đơn (Có phân trang và lọc)
    public function getInvoices($landlord_id, $f_building, $month, $year, $f_status, $keyword, $limit, $offset) {
        $lid = (int)$landlord_id;
        $m = (int)$month;
        $y = (int)$year;
        
        $sql = "SELECT i.*, u.name as user_name, r.title as room_name, b.name as building_name 
                FROM invoices i JOIN contracts c ON i.contract_id = c.id 
                JOIN users u ON c.user_id = u.id JOIN rooms r ON c.room_id = r.id 
                JOIN buildings b ON r.building_id = b.id 
                WHERE r.landlord_id = $lid AND i.month = $m AND i.year = $y";

        if (!empty($f_building)) {
            $bid = (int)$f_building;
            $sql .= " AND b.id = $bid";
        }
        if (!empty($f_status)) {
            $st = $this->escape(strtoupper($f_status));
            $sql .= " AND i.status = '$st'";
        }
        if (!empty($keyword)) {
            $kw = $this->escape($keyword);
            $sql .= " AND (u.name LIKE '%$kw%' OR r.title LIKE '%$kw%')";
        }

        $sql .= " ORDER BY i.created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        $res = $this->conn->query($sql);
        $data = [];
        if ($res) while ($r = $res->fetch_assoc()) $data[] = $r;
        return $data;
    }

    public function getTotalCount($landlord_id, $f_building, $month, $year, $f_status, $keyword) {
        $lid = (int)$landlord_id; $m = (int)$month; $y = (int)$year;
        $sql = "SELECT COUNT(*) as total FROM invoices i JOIN contracts c ON i.contract_id = c.id 
                JOIN rooms r ON c.room_id = r.id JOIN buildings b ON r.building_id = b.id JOIN users u ON c.user_id = u.id 
                WHERE r.landlord_id = $lid AND i.month = $m AND i.year = $y";
        if (!empty($f_building)) $sql .= " AND b.id = " . (int)$f_building;
        if (!empty($f_status)) $sql .= " AND i.status = '" . $this->escape(strtoupper($f_status)) . "'";
        if (!empty($keyword)) {
            $kw = $this->escape($keyword);
            $sql .= " AND (u.name LIKE '%$kw%' OR r.title LIKE '%$kw%')";
        }
        $res = $this->conn->query($sql);
        return ($res && $row = $res->fetch_assoc()) ? $row['total'] : 0;
    }

    // Xem chi tiết Hóa đơn
    public function getInvoiceDetails($inv_id) {
        $id = (int)$inv_id;
        $sqlH = "SELECT i.*, r.title as room_name, b.name as building_name, u.name as user_name, u.phone
                 FROM invoices i JOIN contracts c ON i.contract_id = c.id
                 JOIN rooms r ON c.room_id = r.id JOIN buildings b ON r.building_id = b.id JOIN users u ON c.user_id = u.id
                 WHERE i.id = $id";
        
        $resH = $this->conn->query($sqlH);
        $header = ($resH) ? $resH->fetch_assoc() : null;

        if ($header) {
            $resI = $this->conn->query("SELECT * FROM invoice_items WHERE invoice_id = $id");
            $items = [];
            if ($resI) while ($it = $resI->fetch_assoc()) $items[] = $it;
            return ['header' => $header, 'items' => $items];
        }
        return null;
    }

    // Lập Hóa đơn
    public function createInvoice($contract_id, $month_input, $svc_qtys) {
        $cid = (int)$contract_id;
        $parts = explode('-', $month_input);
        if (count($parts) != 2) return ['status' => 'error', 'message' => 'Tháng không hợp lệ'];
        $year = (int)$parts[0]; $month = (int)$parts[1];

        $chk = $this->conn->query("SELECT id FROM invoices WHERE contract_id=$cid AND month=$month AND year=$year");
        if ($chk && $chk->num_rows > 0) return ['status' => 'error', 'message' => "Tháng $month/$year đã lập hóa đơn rồi!"];

        $resInfo = $this->conn->query("SELECT c.room_id, r.price FROM contracts c JOIN rooms r ON c.room_id = r.id WHERE c.id=$cid");
        if (!$resInfo || !($info = $resInfo->fetch_assoc())) return ['status' => 'error', 'message' => 'Lỗi hợp đồng'];
        
        $rid = (int)$info['room_id'];
        $room_price = (float)$info['price'];
        $total = $room_price;

        $this->conn->begin_transaction();
        try {
            $this->conn->query("INSERT INTO invoices (contract_id, month, year, total, status) VALUES ($cid, $month, $year, 0, 'UNPAID')");
            $inv_id = $this->conn->insert_id;

            $descR = $this->escape("Tiền phòng T$month/$year");
            $this->conn->query("INSERT INTO invoice_items (invoice_id, description, unit_price, quantity, amount) VALUES ($inv_id, '$descR', $room_price, 1, $room_price)");

            $resSvc = $this->conn->query("SELECT s.id, s.name, s.price, s.type, s.unit FROM room_services rs JOIN services s ON rs.service_id = s.id WHERE rs.room_id=$rid");
            if ($resSvc) {
                while ($s = $resSvc->fetch_assoc()) {
                    $sid = $s['id'];
                    $qty = isset($svc_qtys[$sid]) ? (float)$svc_qtys[$sid] : ($s['type'] == 'FIXED' ? 1 : 0);
                    $amount = (float)$s['price'] * $qty;
                    
                    if ($amount > 0) {
                        $descS = $s['name'];
                        if ($s['type'] == 'METERED') $descS .= " (SD: $qty {$s['unit']})";
                        $descS = $this->escape($descS);
                        $sprice = (float)$s['price'];
                        $this->conn->query("INSERT INTO invoice_items (invoice_id, description, unit_price, quantity, amount) VALUES ($inv_id, '$descS', $sprice, $qty, $amount)");
                        $total += $amount;
                    }
                }
            }
            $this->conn->query("UPDATE invoices SET total=$total WHERE id=$inv_id");
            $this->conn->commit();
            return ['status' => 'success', 'message' => 'Lập hóa đơn thành công!'];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['status' => 'error', 'message' => 'Lỗi hệ thống lập HĐ.'];
        }
    }

    // Thu tiền
    public function payInvoice($inv_id) {
        $id = (int)$inv_id;
        $res = $this->conn->query("SELECT c.user_id, i.month, i.year, i.total FROM invoices i JOIN contracts c ON i.contract_id = c.id WHERE i.id = $id");
        if ($res && $data = $res->fetch_assoc()) {
            $this->conn->begin_transaction();
            try {
                $this->conn->query("UPDATE invoices SET status='PAID' WHERE id=$id");
                
                // Gửi thông báo
                $uid = (int)$data['user_id'];
                $m = $data['month']; $y = $data['year']; $t = number_format($data['total']);
                $content = $this->escape("Hóa đơn tháng $m/$y với số tiền $t đ đã được xác nhận thanh toán.");
                $this->conn->query("INSERT INTO notifications (user_id, title, content, type, link) VALUES ($uid, 'Thanh toán thành công', '$content', 'INVOICE', 'hoadon.php')");
                
                $this->conn->commit();
                return ['status' => 'success', 'message' => 'Xác nhận thu tiền thành công!'];
            } catch (Exception $e) {
                $this->conn->rollback();
                return ['status' => 'error', 'message' => 'Lỗi hệ thống.'];
            }
        }
        return ['status' => 'error', 'message' => 'Không tìm thấy hóa đơn.'];
    }

    // Xóa hóa đơn
    public function deleteInvoice($inv_id) {
        $id = (int)$inv_id;
        $res = $this->conn->query("SELECT status FROM invoices WHERE id = $id");
        if ($res && $data = $res->fetch_assoc()) {
            if ($data['status'] === 'PAID') return ['status' => 'error', 'message' => 'Không thể xóa hóa đơn đã thu tiền!'];
            
            $this->conn->begin_transaction();
            try {
                $this->conn->query("DELETE FROM invoice_items WHERE invoice_id=$id");
                $this->conn->query("DELETE FROM invoices WHERE id=$id");
                $this->conn->commit();
                return ['status' => 'success', 'message' => 'Đã xóa hóa đơn.'];
            } catch (Exception $e) {
                $this->conn->rollback();
                return ['status' => 'error', 'message' => 'Lỗi hệ thống.'];
            }
        }
        return ['status' => 'error', 'message' => 'Không tìm thấy hóa đơn.'];
    }
}
?>