<?php
require_once __DIR__ . '/../../models/customer/ExtensionModel.php';

class ExtensionController {
    private $model;

    public function __construct($db) {
        $this->model = new ExtensionModel($db);
    }

    public function getContractInfo($contract_id, $user_id) {
        return $this->model->getActiveContract($contract_id, $user_id);
    }

    public function handleRequest($data, $user_id) {
        $contract_id = intval($data['id']);
        $months = intval($data['months']);
        $note = trim($data['note'] ?? '');

        if ($months <= 0) return ['status' => 'error', 'message' => 'Vui lòng chọn số tháng gia hạn'];

        $contract = $this->model->getActiveContract($contract_id, $user_id);
        if (!$contract) return ['status' => 'error', 'message' => 'Hợp đồng không tồn tại hoặc đã hết hiệu lực'];

        $result = $this->model->createRequest(
            $contract_id, 
            $months, 
            $note, 
            $contract['landlord_id'], 
            $contract['room_name']
        );

        return $result 
            ? ['status' => 'success', 'message' => 'Gửi yêu cầu gia hạn thành công! Vui lòng chờ chủ trọ duyệt.']
            : ['status' => 'error', 'message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'];
    }
}