<?php
class InvoiceModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getInvoicesByUser($user_id) {
        $sql = "SELECT i.*, 
                (SELECT SUM(amount) FROM invoice_items WHERE invoice_id = i.id AND description LIKE '%phòng%') as room_price,
                (SELECT SUM(amount) FROM invoice_items WHERE invoice_id = i.id AND description LIKE '%điện%') as electric,
                (SELECT SUM(amount) FROM invoice_items WHERE invoice_id = i.id AND description LIKE '%nước%') as water
                FROM invoices i
                INNER JOIN contracts c ON i.contract_id = c.id
                WHERE c.user_id = ?
                ORDER BY i.year DESC, i.month DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}