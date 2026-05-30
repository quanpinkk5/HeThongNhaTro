<?php
require_once __DIR__ . '/../../models/customer/RequestModel.php';

class RequestController {
    private $model;
    public function __construct($db) { $this->model = new RequestModel($db); }

    public function handleRequest($room_id, $user_id, $user_name) {
        if (!$room_id) return ['status' => 'error', 'message' => 'Mã phòng không hợp lệ.'];
        if ($this->model->hasPendingRequest($room_id, $user_id)) {
            return ['status' => 'error', 'message' => 'Bạn đã gửi yêu cầu cho phòng này rồi!'];
        }
        if ($this->model->createRentRequest($room_id, $user_id, $user_name)) {
            return ['status' => 'success', 'message' => 'Gửi yêu cầu thành công!'];
        }
        return ['status' => 'error', 'message' => 'Lỗi hệ thống.'];
    }
}