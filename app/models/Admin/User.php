<?php
require_once __DIR__ . '/../../../config/database.php';

class User
{

    private $conn;
    private $table = "users";

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
        $limit  = $params['limit'] ?? 5;
        $page   = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $where = "WHERE 1=1";

        if (!empty($params['keyword'])) {
            $keyword = $this->conn->real_escape_string($params['keyword']);
            $where .= " AND (u.name LIKE '%$keyword%' 
                         OR u.email LIKE '%$keyword%' 
                         OR u.phone LIKE '%$keyword%')";
        }

        if (!empty($params['role'])) {
            $role = $this->conn->real_escape_string($params['role']);
            $where .= " AND u.role='$role'";
        }

        if (!empty($params['status'])) {
            $status = $this->conn->real_escape_string($params['status']);
            $where .= " AND u.status='$status'";
        }

        //  COUNT
        $countQuery = "
        SELECT COUNT(*) as total
        FROM {$this->table} u
        $where
    ";

        $countResult = $this->conn->query($countQuery);
        $total = $countResult->fetch_assoc()['total'];

        //  SELECT có check password_resets
        $sql = "
        SELECT u.*,
               CASE 
                   WHEN pr.user_id IS NOT NULL THEN 1
                   ELSE 0
               END AS can_send_mail
        FROM {$this->table} u
        LEFT JOIN password_resets pr 
            ON u.id = pr.user_id
        $where
        ORDER BY u.id DESC
        LIMIT $limit OFFSET $offset
    ";

        $result = $this->conn->query($sql);

        return [
            "data" => $result->fetch_all(MYSQLI_ASSOC),
            "total" => $total
        ];
    }

    public function create($data)
    {
        if ($this->isDuplicate($data['email'], $data['cccd'])) {
            return [
                "success" => false,
                "message" => "Email hoặc CCCD đã tồn tại"
            ];
        }

        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $code = "CT" . rand(1000, 9999);

        $sql = "INSERT INTO $this->table
                (code,name,password,email,phone,cccd,role,status,created_at)
                VALUES (?,?,?,?,?,?,?,'ACTIVE',NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssss",
            $code,
            $data['name'],
            $password,
            $data['email'],
            $data['phone'],
            $data['cccd'],
            $data['role']
        );

        // return $stmt->execute();
        return [
            "success" => $stmt->execute()
        ];
    }

    public function update($id, $data)
    {
        if ($this->isDuplicate($data['email'], $data['cccd'], $id)) {
            return [
                "success" => false,
                "message" => "Email hoặc CCCD đã tồn tại"
            ];
        }
        $sql = "UPDATE $this->table
              SET name=?,email=?,phone=?,cccd=?,role=?,status=?
              WHERE id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['cccd'],
            $data['role'],
            $data['status'],
            $id
        );

        // return $stmt->execute();
        return [
            "success" => $stmt->execute()
        ];
    }

    public function changeStatus($id, $status)
    {
        $stmt = $this->conn->prepare(
            "UPDATE $this->table SET status=? WHERE id=?"
        );
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function resetPassword($id)
    {

        $temp = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8);
        $hash = password_hash($temp, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "UPDATE $this->table 
             SET password=?,must_change_password=1 
             WHERE id=?"
        );

        $stmt->bind_param("si", $hash, $id);
        $stmt->execute();
        $stmtDelete = $this->conn->prepare(
            "DELETE FROM password_resets WHERE user_id = ?"
        );
        $stmtDelete->bind_param("i", $id);
        $stmtDelete->execute();
        $stmtInsert = $this->conn->prepare(
            "INSERT INTO password_resets (user_id, temp_password)
         VALUES (?, ?)"
        );
        $stmtInsert->bind_param("is", $id, $temp);
        $stmtInsert->execute();

        return $temp;
    }
    public function findById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function sendResetMail($user_id)
    {
        $stmt = $this->conn->prepare("
        SELECT pr.temp_password, u.email, u.name
        FROM password_resets pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.user_id = ?
        ORDER BY pr.created_at DESC
        LIMIT 1
    ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function clearReset($user_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
    private function isDuplicate($email, $cccd, $excludeId = null)
    {
        if ($excludeId) {
            $sql = "SELECT id FROM users 
                WHERE (email = ? OR cccd = ?) 
                AND id != ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssi", $email, $cccd, $excludeId);
        } else {
            $sql = "SELECT id FROM users 
                WHERE email = ? OR cccd = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $email, $cccd);
        }

        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }
}
