<?php
header('Content-Type: application/json');
require_once '../../../../config/database.php'; 
require_once '../../../../app/controllers/customer/MaintenanceController.php';

session_start();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$controller = new MaintenanceController($con);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $data = $controller->getRoomInfo($room_id, $user_id);
    echo json_encode($data ? ['status' => 'success', 'data' => $data] : ['status' => 'error', 'message' => 'Không tìm thấy phòng']);
} 
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_SESSION['user_name'] ?? 'Khách thuê';
    $result = $controller->handleReport($_POST, $_FILES, $user_id, $user_name);
    echo json_encode($result);
}