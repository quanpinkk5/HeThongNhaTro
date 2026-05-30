<?php
class ServiceModel {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Lấy tất cả dịch vụ
    public function getAll($landlord_id) {
        $sql = "SELECT * FROM services WHERE landlord_id = ? ORDER BY code ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $landlord_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $services = [];
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        $stmt->close();
        return $services;
    }

    // Kiểm tra trùng mã dịch vụ
    public function checkCodeExists($code, $landlord_id) {
        $stmt = $this->conn->prepare("SELECT id FROM services WHERE code = ? AND landlord_id = ?");
        $stmt->bind_param("si", $code, $landlord_id);
        $stmt->execute();
        $stmt->store_result();
        $count = $stmt->num_rows;
        $stmt->close();
        return $count > 0;
    }

    // Thêm mới
    public function addService($landlord_id, $code, $name, $price, $unit, $type, $description) {
        $db_type = ($type == 'fixed') ? 'FIXED' : 'METERED';
        $sql = "INSERT INTO services (landlord_id, code, name, price, unit, type, description) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issdsss", $landlord_id, $code, $name, $price, $unit, $db_type, $description);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Cập nhật
    public function updateService($id, $landlord_id, $name, $price, $unit, $type, $description) {
        $db_type = ($type == 'fixed') ? 'FIXED' : 'METERED';
        $sql = "UPDATE services SET name=?, price=?, unit=?, type=?, description=? WHERE id=? AND landlord_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdsssii", $name, $price, $unit, $db_type, $description, $id, $landlord_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Xóa
    public function deleteService($id, $landlord_id) {
        // Lưu ý: Trong thực tế, có thể bạn cần kiểm tra xem dịch vụ này có đang được dùng ở phòng nào không (bảng room_services) trước khi cho phép xóa.
        $stmt = $this->conn->prepare("DELETE FROM services WHERE id=? AND landlord_id=?");
        $stmt->bind_param("ii", $id, $landlord_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>