<?php
require_once __DIR__ . '/../../../config/database.php';

class ActivityLog
{
    private $conn;
    private $table = "activity_logss";

    public function __construct()
    {
        $db = new database();
        $this->conn = $db->connect();
    }

    /* =============================
       LẤY DANH SÁCH + FILTER + PAGINATION
    ============================= */
public function getAll($params)
{
    $limit  = $params['limit'] ?? 6;
    $page   = $params['page'] ?? 1;
    $offset = ($page - 1) * $limit;

    $where = "WHERE 1=1";

    if (!empty($params['keyword'])) {
        $keyword = $this->conn->real_escape_string($params['keyword']);
        $where .= " AND (
            admin.name LIKE '%$keyword%' OR
            l.action LIKE '%$keyword%' OR
            target.name LIKE '%$keyword%'
        )";
    }

    if (!empty($params['action'])) {
        $action = $this->conn->real_escape_string($params['action']);
        $where .= " AND l.action='$action'";
    }

    if (!empty($params['target_role'])) {
        $role = $this->conn->real_escape_string($params['target_role']);
        $where .= " AND l.target_role='$role'";
    }

    if (!empty($params['date_from']) && !empty($params['date_to'])) {
        $from = $this->conn->real_escape_string($params['date_from']);
        $to   = $this->conn->real_escape_string($params['date_to']);
        $where .= " AND DATE(l.created_at) BETWEEN '$from' AND '$to'";
    }

    /* ===== COUNT ===== */
    $countQuery = "
        SELECT COUNT(*) as total
        FROM {$this->table} l
        JOIN users admin ON l.actor_id = admin.id
        LEFT JOIN users target ON l.target_id = target.id
        $where
    ";

    $countResult = $this->conn->query($countQuery);
    $total = $countResult->fetch_assoc()['total'];

    /* ===== SELECT ===== */
    $sql = "
        SELECT 
            l.*,
            admin.name AS admin_name,
            target.name AS target_name
        FROM {$this->table} l
        JOIN users admin ON l.actor_id = admin.id
        LEFT JOIN users target ON l.target_id = target.id
        $where
        ORDER BY l.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

    $result = $this->conn->query($sql);

    return [
        "data"  => $result->fetch_all(MYSQLI_ASSOC),
        "total" => $total
    ];
}

public function findById($id)
{
    $stmt = $this->conn->prepare("
        SELECT 
            l.*,
            admin.name AS admin_name,
            target.name AS target_name
        FROM {$this->table} l
        JOIN users admin 
            ON l.actor_id = admin.id
        LEFT JOIN users target 
            ON l.target_id = target.id
        WHERE l.id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
public function getAllNoLimit($params)
{
    $where = "WHERE 1=1";

    if (!empty($params['keyword'])) {
        $kw = $this->conn->real_escape_string($params['keyword']);
        $where .= " AND (
            admin.name LIKE '%$kw%' OR
            l.action LIKE '%$kw%' OR
            target.name LIKE '%$kw%'
        )";
    }

    if (!empty($params['action'])) {
        $action = $this->conn->real_escape_string($params['action']);
        $where .= " AND l.action = '$action'";
    }

    if (!empty($params['target_role'])) {
        $role = $this->conn->real_escape_string($params['target_role']);
        $where .= " AND l.target_role = '$role'";
    }

    if (!empty($params['date_from'])) {
        $df = $this->conn->real_escape_string($params['date_from']);
        $where .= " AND DATE(l.created_at) >= '$df'";
    }

    if (!empty($params['date_to'])) {
        $dt = $this->conn->real_escape_string($params['date_to']);
        $where .= " AND DATE(l.created_at) <= '$dt'";
    }

    $sql = "
    SELECT 
        l.*,
        admin.name AS admin_name,
        target.name AS target_name
    FROM {$this->table} l
    JOIN users admin ON l.actor_id = admin.id
    LEFT JOIN users target ON l.target_id = target.id
    $where
    ORDER BY l.created_at DESC
    ";

    $result = $this->conn->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC);
}

    // public function delete($id)
    // {
    //     $stmt = $this->conn->prepare(
    //         "DELETE FROM {$this->table} WHERE id=?"
    //     );
    //     $stmt->bind_param("i", $id);
    //     return $stmt->execute();
    // }
}