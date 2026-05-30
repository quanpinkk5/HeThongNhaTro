<?php
class Contract_detailModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getContractDetail($contract_id, $user_id) {
        $sql = "SELECT c.*, 
                       r.title as room_name, r.price as room_price, r.area, r.id as room_id,
                       b.name as building_name, b.address,
                       u.name as landlord_name, u.phone as landlord_phone, u.email as landlord_email,
                       tenant.name as tenant_name, tenant.phone as tenant_phone, tenant.cccd as tenant_cccd
                FROM contracts c
                JOIN rooms r ON c.room_id = r.id
                JOIN buildings b ON r.building_id = b.id
                JOIN users u ON r.landlord_id = u.id
                JOIN users tenant ON c.user_id = tenant.id
                WHERE c.id = ? AND c.user_id = ? LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $contract_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getServices($room_id) {
        $sql = "SELECT s.name, s.price, s.unit, rs.calculation 
                FROM room_services rs
                JOIN services s ON rs.service_id = s.id
                WHERE rs.room_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}