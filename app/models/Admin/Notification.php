<?php
require_once __DIR__ . '/../../../config/database.php';

class Notification
{
    private $conn;
    private $table = "notifications";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    /* =====================================================
        CREATE NOTIFICATION
    ====================================================== */
    public function create($user_id, $title, $content, $type = "SYSTEM", $link = null)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (user_id, title, content, type, link, is_read, created_at)
            VALUES (?, ?, ?, ?, ?, 0, NOW())
        ");

        if (!$stmt) return false;

        $stmt->bind_param(
            "issss",
            $user_id,
            $title,
            $content,
            $type,
            $link
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /* =====================================================
        GET NOTIFICATION BY USER
    ====================================================== */
    public function getByUser($userId)
    {
        $stmt = $this->conn->prepare("
            SELECT id, title, content, type, is_read, link, created_at
            FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function markAsRead($userId)
    {
        $stmt = $this->conn->prepare("
            UPDATE notifications 
            SET is_read = 1 
            WHERE user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }

    public function deleteOne($id, $userId)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM notifications 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $id, $userId);
        return $stmt->execute();
    }

    public function deleteMultiple($ids, $userId)
    {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids) + 1);

        $sql = "DELETE FROM notifications 
                WHERE id IN ($placeholders) 
                AND user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $params = array_merge($ids, [$userId]);
        $stmt->bind_param($types, ...$params);

        return $stmt->execute();
    }
    public function getUnreadCount($userId)
    {
        $count = 0;
        $stmt = $this->conn->prepare("
        SELECT COUNT(*) 
        FROM notifications 
        WHERE user_id = ? AND is_read = 0
    ");

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count;
    }
        public function markOneAsRead($id, $userId)
    {

        $stmt = $this->conn->prepare("
        UPDATE notifications 
        SET is_read = 1
        WHERE id = ? AND user_id = ?
    ");

        $stmt->bind_param("ii", $id, $userId);
        return $stmt->execute();
    }
}
