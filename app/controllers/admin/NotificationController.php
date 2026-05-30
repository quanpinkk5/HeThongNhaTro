<?php
require_once __DIR__ . '/../../models/Admin/Notification.php';

class NotificationController
{

    private $model;

    public function __construct()
    {
        $this->model = new Notification();
    }

    public function index($userId)
    {
        $data = $this->model->getByUser($userId);
        // $this->model->markAsRead($userId);

        echo json_encode([
            "success" => true,
            "data" => $data
        ]);
    }

    public function deleteOne($id, $userId)
    {
        $this->model->deleteOne($id, $userId);
        echo json_encode(["success" => true]);
    }

    public function deleteMultiple($ids, $userId)
    {
        $this->model->deleteMultiple($ids, $userId);
        echo json_encode(["success" => true]);
    }
    public function getUnreadCount($userId)
    {
        return $this->model->getUnreadCount($userId);
    }
        public function markOneAsRead($id, $userId)
    {
        $this->model->markOneAsRead($id, $userId);
        echo json_encode(["success" => true]);
    }
}
