<?php
class TenantModel {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Đếm tổng số khách hàng (Hỗ trợ tìm kiếm)
    public function getTotal($landlord_id, $keyword = '') {
        $sql = "SELECT COUNT(DISTINCT u.id) 
                FROM rent_requests rr
                JOIN users u ON rr.user_id = u.id
                JOIN rooms r ON rr.room_id = r.id
                WHERE rr.status = 'APPROVED' AND r.landlord_id = ?";
        
        $params = [$landlord_id];
        $types = "i";

        if (!empty($keyword)) {
            $sql .= " AND (u.name LIKE ? OR u.phone LIKE ? OR u.cccd LIKE ?)";
            $kw = "%$keyword%";
            array_push($params, $kw, $kw, $kw);
            $types .= "sss";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];
        $stmt->close();
        
        return $count;
    }

    // Lấy danh sách khách hàng (Có phân trang và tìm kiếm)
    public function getList($landlord_id, $limit, $offset, $keyword = '') {
        $sql = "SELECT DISTINCT
                    u.id, u.code, u.name, u.phone, u.email, u.cccd,
                    rr.room_id, r.title AS room_name, rr.created_at AS approved_at
                FROM rent_requests rr
                JOIN users u ON rr.user_id = u.id
                JOIN rooms r ON rr.room_id = r.id
                WHERE rr.status = 'APPROVED' AND r.landlord_id = ?";
        
        $params = [$landlord_id];
        $types = "i";

        if (!empty($keyword)) {
            $sql .= " AND (u.name LIKE ? OR u.phone LIKE ? OR u.cccd LIKE ?)";
            $kw = "%$keyword%";
            array_push($params, $kw, $kw, $kw);
            $types .= "sss";
        }

        $sql .= " ORDER BY rr.created_at DESC LIMIT ? OFFSET ?";
        array_push($params, $limit, $offset);
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        
        return $data;
    }
}
?>