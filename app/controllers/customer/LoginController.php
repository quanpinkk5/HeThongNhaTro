<?php
require_once __DIR__ . '/../../models/customer/LoginModel.php';

class LoginController {
    private $model;

    public function __construct($db) {
        $this->model = new LoginModel($db);
    }

    public function handleLogin($email, $password) {
        $user = $this->model->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'BLOCKED') {
                return ['status' => 'error', 'message' => 'Tài khoản của bạn đã bị khóa!'];
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            return ['status' => 'success',
            "role" => $user['role']];
        }

        return ['status' => 'error', 'message' => 'Email hoặc mật khẩu không chính xác!'];
    }
}