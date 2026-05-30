<?php
require_once __DIR__ . '/../../models/customer/Contract_detailModel.php';

class Contract_detailController {
    private $model;

    public function __construct($db) {
        $this->model = new Contract_detailModel($db);
    }

    public function getDetailData($contract_id, $user_id) {
        $contract = $this->model->getContractDetail($contract_id, $user_id);
        if (!$contract) return null;

        $services = $this->model->getServices($contract['room_id']);
        return [
            'contract' => $contract,
            'services' => $services
        ];
    }
}