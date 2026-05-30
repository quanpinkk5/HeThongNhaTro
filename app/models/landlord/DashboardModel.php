<?php
class DashboardModel
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function getTotalRooms($landlord_id)
    {
        $lid = (int)$landlord_id;
        $res = $this->conn->query("SELECT COUNT(*) as total FROM rooms WHERE landlord_id = $lid");
        return ($res && $row = $res->fetch_assoc()) ? (int)$row['total'] : 0;
    }

    public function getRentedRooms($landlord_id)
    {
        $lid = (int)$landlord_id;
        $sql = "SELECT COUNT(DISTINCT room_id) as total FROM contracts 
                JOIN rooms ON contracts.room_id = rooms.id 
                WHERE rooms.landlord_id = $lid AND contracts.status = 'ACTIVE'";
        $res = $this->conn->query($sql);
        return ($res && $row = $res->fetch_assoc()) ? (int)$row['total'] : 0;
    }

    public function getTotalDebt($landlord_id)
    {
        $lid = (int)$landlord_id;
        $sql = "SELECT SUM(total) as total FROM invoices 
                JOIN contracts ON invoices.contract_id = contracts.id
                JOIN rooms ON contracts.room_id = rooms.id
                WHERE rooms.landlord_id = $lid AND invoices.status = 'UNPAID'";
        $res = $this->conn->query($sql);
        return ($res && ($row = $res->fetch_assoc()) && $row['total']) ? (float)$row['total'] : 0;
    }

    public function getUnpaidInvoices($landlord_id, $limit = 5)
    {
        $lid = (int)$landlord_id;
        $sql = "SELECT r.title as room_name, i.total, i.month, i.year 
                FROM invoices i
                JOIN contracts c ON i.contract_id = c.id
                JOIN rooms r ON c.room_id = r.id
                WHERE r.landlord_id = $lid AND i.status = 'UNPAID'
                ORDER BY i.created_at DESC LIMIT $limit";
        $res = $this->conn->query($sql);
        $data = [];
        if ($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function getExpiringContracts($landlord_id, $limit = 5)
    {
        $lid = (int)$landlord_id;
        $sql = "SELECT u.name as tenant_name, r.title as room_name, DATEDIFF(c.end_date, NOW()) as days_left
                FROM contracts c
                JOIN users u ON c.user_id = u.id
                JOIN rooms r ON c.room_id = r.id
                WHERE r.landlord_id = $lid AND c.status = 'ACTIVE' 
                AND c.end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)
                ORDER BY days_left ASC LIMIT $limit";
        $res = $this->conn->query($sql);
        $data = [];
        if ($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        return $data;
    }

    public function getRevenueLast6Months($landlord_id)
    {
        $lid = (int)$landlord_id;
        $sql = "SELECT CONCAT(month, '/', year) AS label, SUM(total) AS revenue
                FROM invoices i
                JOIN contracts c ON i.contract_id = c.id
                JOIN rooms r ON c.room_id = r.id
                WHERE r.landlord_id = $lid AND i.status = 'PAID'
                AND (year * 12 + month) >= (YEAR(CURDATE()) * 12 + MONTH(CURDATE()) - 5)
                GROUP BY year, month
                ORDER BY year, month";
        $res = $this->conn->query($sql);
        $data = [];
        if ($res) while ($row = $res->fetch_assoc()) $data[] = $row;
        return $data;
    }
}
