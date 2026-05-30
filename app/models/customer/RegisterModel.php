<?php
require_once __DIR__ . '/../../../config/database.php';

class UserModel {
    private $conn;
    public function __construct() { global $con; $this->conn = $con; }

    public function checkEmail($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function createUser($data) {
        $sql = "INSERT INTO users (code, name, email, phone, cccd, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'USER', 'ACTIVE', NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $data['code'], $data['name'], $data['email'], $data['phone'], $data['cccd'], $data['password']);
        return $stmt->execute();
    }
    public function checkCCCD($cccd) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE cccd = ?");
        $stmt->bind_param("s", $cccd);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

}