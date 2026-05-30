<?php
class BuildingModel {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    private function escape($str) {
        return $this->conn->real_escape_string($str);
    }

    // Lấy danh sách kèm phân trang
    public function getAll($landlord_id, $limit, $offset) {
        $lid = (int)$landlord_id;
        $lim = (int)$limit;
        $off = (int)$offset;

        $sql = "SELECT b.*, 
                (SELECT COUNT(*) FROM rooms r WHERE r.building_id = b.id) as total_rooms,
                (SELECT COUNT(*) FROM rooms r JOIN contracts c ON r.id = c.room_id WHERE r.building_id = b.id AND c.status = 'ACTIVE') as rented_rooms
                FROM buildings b 
                WHERE b.landlord_id = $lid 
                ORDER BY b.created_at DESC 
                LIMIT $lim OFFSET $off";
        
        $res = $this->conn->query($sql);
        $data = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Đếm tổng số tòa nhà
    public function getTotal($landlord_id) {
        $lid = (int)$landlord_id;
        $res = $this->conn->query("SELECT COUNT(*) as total FROM buildings WHERE landlord_id = $lid");
        if ($res && $row = $res->fetch_assoc()) {
            return $row['total'];
        }
        return 0;
    }

    // Kiểm tra mã tòa nhà
    public function checkCodeExists($code, $landlord_id) {
        $c = $this->escape($code);
        $lid = (int)$landlord_id;
        $res = $this->conn->query("SELECT id FROM buildings WHERE code = '$c' AND landlord_id = $lid");
        return ($res && $res->num_rows > 0);
    }

    // Thêm tòa nhà
    public function add($landlord_id, $code, $name, $address, $floors) {
        $lid = (int)$landlord_id;
        $c = $this->escape($code);
        $n = $this->escape($name);
        $a = $this->escape($address);
        $f = (int)$floors;
        return $this->conn->query("INSERT INTO buildings (landlord_id, code, name, address, floors) VALUES ($lid, '$c', '$n', '$a', $f)");
    }

    // Sửa tòa nhà
    public function update($id, $landlord_id, $name, $address, $floors) {
        $id = (int)$id;
        $lid = (int)$landlord_id;
        $n = $this->escape($name);
        $a = $this->escape($address);
        $f = (int)$floors;
        return $this->conn->query("UPDATE buildings SET name='$n', address='$a', floors=$f WHERE id=$id AND landlord_id=$lid");
    }

    // Xóa tòa nhà
    public function delete($id, $landlord_id) {
        $id = (int)$id;
        $lid = (int)$landlord_id;
        
        $check = $this->conn->query("SELECT COUNT(*) as count FROM rooms WHERE building_id = $id");
        if ($check && $row = $check->fetch_assoc()) {
            if ($row['count'] > 0) return 'has_rooms';
        }

        if ($this->conn->query("DELETE FROM buildings WHERE id=$id AND landlord_id=$lid")) {
            return 'success';
        }
        return 'error';
    }
}
?>