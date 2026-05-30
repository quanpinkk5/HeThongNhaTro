<?php
require_once __DIR__ . '/../../../config/database.php';

class Area
{
    private $conn;

    public function __construct()
    {
        $db = new database();
        $this->conn = $db->connect();
    }

    public function getAll($keyword = '', $status = '')
    {
        $sql = "SELECT * FROM areas WHERE 1";

        if ($keyword !== '') {
            $sql .= " AND name LIKE ?";
        }

        if ($status !== '') {
            $sql .= " AND status = ?";
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->conn->prepare($sql);

        if ($keyword !== '' && $status !== '') {
            $kw = "%$keyword%";
            $stmt->bind_param("ss", $kw, $status);
        } elseif ($keyword !== '') {
            $kw = "%$keyword%";
            $stmt->bind_param("s", $kw);
        } elseif ($status !== '') {
            $stmt->bind_param("s", $status);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function create($name)
    {
        if ($this->isDuplicate($name)) {
            return [
                "success" => false,
                "message" => "Tên khu vực đã tồn tại"
            ];
        }
        $stmt = $this->conn->prepare(
            "INSERT INTO areas(name,status) VALUES (?, 'ACTIVE')"
        );
        $stmt->bind_param("s", $name);
        // return $stmt->execute();
        return [
            "success" => $stmt->execute()
        ];
    }

    public function update($id, $name)
    {
        if ($this->isDuplicate($name, $id)) {
            return [
                "success" => false,
                "message" => "Tên khu vực đã tồn tại"
            ];
        }
        $stmt = $this->conn->prepare(
            "UPDATE areas SET name=? WHERE id=?"
        );
        $stmt->bind_param("si", $name, $id);
        // return $stmt->execute();
        return [
            "success" => $stmt->execute()
        ];
    }

    public function toggle($id)
    {
        $res = $this->conn->query("SELECT status FROM areas WHERE id=$id");
        $row = $res->fetch_assoc();

        $newStatus = $row['status'] === 'ACTIVE' ? 'HIDDEN' : 'ACTIVE';

        $stmt = $this->conn->prepare(
            "UPDATE areas SET status=? WHERE id=?"
        );
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();

        return $newStatus;
    }
    private function isDuplicate($name, $excludeId = null)
    {
        if ($excludeId) {
            $sql = "SELECT id FROM areas 
                WHERE LOWER(name) = LOWER(?) 
                AND id != ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $name, $excludeId);
        } else {
            $sql = "SELECT id FROM areas 
                WHERE LOWER(name) = LOWER(?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $name);
        }

        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }
}
