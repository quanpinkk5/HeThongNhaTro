<?php
require_once __DIR__ . '/../../models/customer/RegisterModel.php';

class AuthController {
    private $model;
    public function __construct() { $this->model = new UserModel(); }

    public function register($req) {
        if ($req['password'] !== $req['confirm_password']) 
            return ['status' => 'error', 'message' => 'Mật khẩu không khớp!'];
        
        // Kiểm tra Email trùng
        if ($this->model->checkEmail($req['email']))
            return ['status' => 'error', 'message' => 'Email đã tồn tại!'];
    
        // KIỂM TRA CCCD TRÙNG (Thêm đoạn này)
        if ($this->model->checkCCCD($req['cccd']))
            return ['status' => 'error', 'message' => 'Số CCCD này đã được đăng ký!'];
    
        $data = [
            'code' => 'USR'.rand(10000,99999),
            'name' => trim($req['fullname']),
            'email' => trim($req['email']),
            'phone' => trim($req['phone']),
            'cccd' => trim($req['cccd']),
            'password' => password_hash($req['password'], PASSWORD_DEFAULT)
        ];
    
        return $this->model->createUser($data) ? ['status' => 'success'] : ['status' => 'error', 'message' => 'Lỗi hệ thống!'];
    }
}