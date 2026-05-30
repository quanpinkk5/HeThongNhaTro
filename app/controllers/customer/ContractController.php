<?php
require_once __DIR__ . '/../../models/customer/ContractModel.php';

class ContractController {
    private $model;

    public function __construct($db) {
        $this->model = new ContractModel($db);
    }

    public function getRentingRooms($user_id) {
        return $this->model->getActiveContracts($user_id);
    }
}