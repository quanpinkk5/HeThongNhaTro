<?php
class ProfileModel {
    private $con;

    public function __construct($db) {
        $this->con = $db;
    }

    public function getUserById($id) {
        $stmt = $this->con->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateFullName($userId, $name) {
        $stmt = $this->con->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $userId);
        return $stmt->execute();
    }

    public function updatePassword($id, $hashed) {
        $stmt = $this->con->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $id);
        return $stmt->execute();
    }
}