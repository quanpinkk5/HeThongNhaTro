<?php
header('Content-Type: application/json');
ob_start();
session_start();

require_once '../../../../config/database.php';
require_once '../../../../app/controllers/customer/ExtensionController.php';

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập']);
    exit;
}

$controller = new ExtensionController($con);
$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $data = $controller->getContractInfo($id, $user_id);
    ob_clean();
    echo json_encode($data ? ['status' => 'success', 'data' => $data] : ['status' => 'error', 'message' => 'Không tìm thấy hợp đồng']);
} 
elseif ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $result = $controller->handleRequest($input, $user_id);
    ob_clean();
    echo json_encode($result);
}
exit;