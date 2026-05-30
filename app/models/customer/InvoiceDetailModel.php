<?php
class InvoiceDetailModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getInvoiceDetail($invoice_id) {
        $sql = "SELECT i.*, c.start_date, c.end_date, r.title as room_name, r.price as room_base_price,
                       u.name as tenant_name, u.phone as tenant_phone, u.email as tenant_email,
                       b.name as building_name, b.address as building_address
                FROM invoices i
                JOIN contracts c ON i.contract_id = c.id
                JOIN rooms r ON c.room_id = r.id
                JOIN buildings b ON r.building_id = b.id
                JOIN users u ON c.user_id = u.id
                WHERE i.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getInvoiceItems($invoice_id) {
        $sql = "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}