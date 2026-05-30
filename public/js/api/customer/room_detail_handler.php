<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../app/controllers/customer/RoomDetailController.php';

$room_id = $_GET['id'] ?? null;
$controller = new RoomDetailController($con);
$result = $controller->getDetails($room_id);

echo json_encode($result ?? ['status' => 'error', 'message' => 'Không tìm thấy phòng']);