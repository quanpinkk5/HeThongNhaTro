<?php
class NotificationModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getNotifications($user_id) {
        $sql = "SELECT id, title, content, type, is_read, link, created_at 
                FROM notifications WHERE user_id = ? 
                ORDER BY created_at DESC, id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function markAsRead($read_id, $user_id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $read_id, $user_id);
        return $stmt->execute();
    }

    public function markAllRead($user_id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
    public function countUnread($user_id) {
        $sql = "SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['unread_count'] ?? 0;
    }
}