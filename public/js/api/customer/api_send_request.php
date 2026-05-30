<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../app/controllers/customer/RequestController.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập!']);
    exit;
}

$room_id = $_POST['room_id'] ?? null;
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? "Khách hàng";

$controller = new RequestController($con);
echo json_encode($controller->handleRequest($room_id, $user_id, $user_name));