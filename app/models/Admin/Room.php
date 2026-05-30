<?php
require_once __DIR__ . '/../../../config/database.php';
class Room
{
    private $conn;
    private $table = "rooms";

    public function __construct()
    {
        $db = new database();
        $this->conn = $db->connect();
    }

    /* =====================================================
        GET ALL ROOM CHỜ DUYỆT + FILTER + PAGINATION
    ====================================================== */
    public function getAll($params)
    {
        $limit  = isset($params['limit']) ? (int)$params['limit'] : 5;
        $page   = isset($params['page']) ? (int)$params['page'] : 1;
        $page   = max($page, 1);
        $offset = ($page - 1) * $limit;

        $where = [];

        // chỉ lấy phòng của landlord
        $where[] = "u.role = 'LANDLORD'";

        /* =========================
       🔎 FILTER KEYWORD
    ========================= */
        if (!empty($params['keyword'])) {
            $keyword = $this->conn->real_escape_string($params['keyword']);
            $where[] = "(r.title LIKE '%$keyword%' 
                     OR u.name LIKE '%$keyword%')";
        }

        /* =========================
       📂 FILTER STATUS
    ========================= */
        if (!empty($params['status'])) {
            $status = $this->conn->real_escape_string($params['status']);
            $where[] = "r.status = '$status'";
        }

        $where_sql = "";
        if (!empty($where)) {
            $where_sql = "WHERE " . implode(" AND ", $where);
        }

        /* =========================
       🔢 COUNT TOTAL
    ========================= */
        $countQuery = "
        SELECT COUNT(*) as total
        FROM {$this->table} r
        JOIN users u ON r.landlord_id = u.id
        $where_sql
    ";

        $countResult = $this->conn->query($countQuery);
        $total = $countResult->fetch_assoc()['total'];

        /* =========================
       SELECT DATA
    ========================= */
        $sql = "
        SELECT 
            r.id,
            r.title,
            r.price,
            r.status,
            r.created_at,
            u.name AS landlord_name,
            u.id   AS landlord_id
        FROM {$this->table} r
        JOIN users u ON r.landlord_id = u.id
        $where_sql
        ORDER BY r.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

        $result = $this->conn->query($sql);

        return [
            "data"  => $result->fetch_all(MYSQLI_ASSOC),
            "total" => (int)$total
        ];
    }

    /* =====================================================
        FIND ROOM BY ID
    ====================================================== */
    public function findById($id)
    {
        $id = (int)$id;

        /* =========================
       LẤY THÔNG TIN PHÒNG
    ========================= */
        $sql = "
        SELECT 
            r.*,
            u.name  AS landlord_name,
            u.email AS landlord_email,
            u.phone AS landlord_phone
        FROM {$this->table} r
        JOIN users u ON r.landlord_id = u.id
        WHERE r.id = ?
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $room = $result->fetch_assoc();

        if (!$room) return null;

        /* =========================
       LẤY ẢNH PHÒNG
    ========================= */
        $imgStmt = $this->conn->prepare("
        SELECT image_url 
        FROM room_images 
        WHERE room_id = ?
    ");

        $imgStmt->bind_param("i", $id);
        $imgStmt->execute();

        $imgResult = $imgStmt->get_result();
        $images = [];

        while ($row = $imgResult->fetch_assoc()) {
            $images[] = $row['image_url'];
        }

        $room['images'] = $images;

        return $room;
    }

    /* =====================================================
        APPROVE ROOM
    ====================================================== */
    public function approve($id)
    {
        $id = (int)$id;
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET status = 'APPROVED' WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }

        return false;
    }

    /* =====================================================
        REJECT ROOM
    ====================================================== */
    public function reject($id)
    {
        $id = (int)$id;
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET status = 'REJECTED' WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        }

        return false;
    }

    /* =====================================================
        SEND NOTIFICATION CHO CHỦ PHÒNG
    ====================================================== */
    public function sendNotification($user_id, $message)
    {
        $user_id = (int)$user_id;
        $message = $this->conn->real_escape_string($message);

        $sql = "
            INSERT INTO notifications (user_id, message, created_at)
            VALUES ($user_id, '$message', NOW())
        ";

        return $this->conn->query($sql);
    }
}
