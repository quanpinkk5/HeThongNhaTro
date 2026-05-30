<?php
require_once __DIR__ . '/../../models/customer/RoomDetailModel.php';

class RoomDetailController {
    private $model;

    public function __construct($db) {
        $this->model = new RoomDetailModel($db);
    }

    public function getDetails($room_id) {
        $room = $this->model->getRoomById($room_id);
        if (!$room) return null;

        return [
            'status' => 'success',
            'data' => [
                'info' => $room,
                'services' => $this->model->getRoomServices($room_id),
                'images' => $this->model->getRoomImages($room_id)
            ]
        ];
    }
}