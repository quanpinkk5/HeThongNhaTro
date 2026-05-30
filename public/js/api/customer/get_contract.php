<?php
header('Content-Type: application/json');
require_once '../../config/database.php'; 
require_once '../../app/controllers/customer/ContractController.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$contract_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

$controller = new Contract_detailController($con);
$data = $controller->getDetailJSON($contract_id, $user_id);

if ($data) {
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Hợp đồng không tồn tại']);
}