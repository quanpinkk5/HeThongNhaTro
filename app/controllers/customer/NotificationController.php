<?php
require_once __DIR__ . '/../../models/customer/NotificationModel.php';

class NotificationController {
    private $model;

    public function __construct($db) {
        $this->model = new NotificationModel($db);
    }

    public function index($user_id) {
        $data = $this->model->getNotifications($user_id);
        return ['status' => 'success', 'data' => $data];
    }

    public function handleRead($read_id, $user_id) {
        if ($this->model->markAsRead($read_id, $user_id)) {
            return ['status' => 'success'];
        }
        return ['status' => 'error', 'message' => 'Không thể cập nhật'];
    }

    public function handleMarkAll($user_id) {
        if ($this->model->markAllRead($user_id)) {
            return ['status' => 'success'];
        }
        return ['status' => 'error', 'message' => 'Lỗi hệ thống'];
    }
    public function getUnreadCount($user_id) {
        return $this->model->countUnread($user_id);
    }
}