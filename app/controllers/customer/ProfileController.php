<?php
require_once __DIR__ . '/../../models/customer/ProfileModel.php';

class ProfileController {
    private $model;

    public function __construct($db) {
        $this->model = new ProfileModel($db);
    }
    public function getUserData($userId) {
 
        return $this->model->getUserById($userId);
    }
    public function handleUpdateName($userId, $name) {
        $name = trim($name);
        if (empty($name)) return ['status' => 'error', 'message' => 'Tên không được để trống'];

        if ($this->model->updateFullName($userId, $name)) {
            $_SESSION['user_name'] = $name;
            return ['status' => 'success', 'message' => 'Cập nhật thành công!', 'new_name' => $name];
        }
        return ['status' => 'error', 'message' => 'Không thể cập nhật'];
    }

    public function handleChangePassword($userId, $current, $new, $confirm) {
        $user = $this->model->getUserById($userId);
        if (!password_verify($current, $user['password'])) {
            return ['status' => 'error', 'message' => 'Mật khẩu hiện tại sai'];
        }
        if (strlen($new) < 6) return ['status' => 'error', 'message' => 'Mật khẩu mới quá ngắn'];
        if ($new !== $confirm) return ['status' => 'error', 'message' => 'Xác nhận mật khẩu không khớp'];

        $hashed = password_hash($new, PASSWORD_DEFAULT);
        return $this->model->updatePassword($userId, $hashed) 
            ? ['status' => 'success', 'message' => 'Đổi mật khẩu thành công!']
            : ['status' => 'error', 'message' => 'Lỗi hệ thống'];
    }
}