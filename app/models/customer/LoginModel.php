<?php
class LoginModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, name, password, status, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}