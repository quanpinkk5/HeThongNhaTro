<?php
session_start();
header('Content-Type: application/json');
ob_start();
require_once '../../../../config/database.php';
require_once '../../../../app/controllers/customer/Contract_detailController.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or missing ID']);
    exit;
}

$controller = new Contract_detailController($con);
$data = $controller->getDetailData(intval($_GET['id']), $_SESSION['user_id']);

ob_clean();
if ($data) {
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Not found']);
}