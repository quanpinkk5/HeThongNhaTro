<?php
require_once __DIR__ . '/../../../config/database.php';

class Log
{
    private $conn;
    private $table = "activity_logss"; 

    public function __construct()
    {
        $db = new database();
        $this->conn = $db->connect();
    }

    public function write(
        $actor_id,
        $action,
        $target_id = null,
        $target_type = null,
        $target_role = null,
        $description = null
    ) {

        // Lấy role từ session nếu có
        // $actor_role = $_SESSION['role'] ?? 'UNKNOWN';
        $actor_role = 'ADMIN';

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        if ($ip === '::1') {
            $ip = '127.0.0.1 (localhost)';
        }

        $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $sql = "INSERT INTO {$this->table}
            (actor_id, actor_role, action, target_id, target_type, target_role, description, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "ississsss",
            $actor_id,
            $actor_role,
            $action,
            $target_id,
            $target_type,
            $target_role,
            $description,
            $ip,
            $agent
        );

        return $stmt->execute();
    }
}