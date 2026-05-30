<?php
require_once __DIR__ . '/../../models/customer/RentRequestModel.php';

class RentRequestController {
    private $model;

    public function __construct($db) {
        $this->model = new RentRequestModel($db);
    }

    public function getMyRequests($userId) {
        
        return $this->model->getRequestsByUser($userId);
    }
}