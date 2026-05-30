<?php
require_once __DIR__ . '/../../models/customer/CustomerModel.php';

class CustomerController {
    private $model;

    public function __construct() {
        $this->model = new CustomerModel();
    }

    public function getRooms($user_id, $search, $district) {
        return $this->model->getRooms($user_id, $search, $district);
    }

    public function getAreas() {
        return $this->model->getAreas();
    }
}