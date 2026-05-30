<?php
ob_start(); 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['action' => 'not_logged_in', 'message' => 'Vui lòng đăng nhập']);
    exit;
}
header('Content-Type: application/json');

require_once __DIR__ . '../../../../../config/database.php'; 
require_once __DIR__ . '/../../../../app/controllers/customer/FavoriteController.php';

ob_clean(); 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'not_logged_in']);
    exit;
}

$db_connection = $con ?? $conn; 
$controller = new FavoriteController($db_connection);
$roomId = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;

$result = $controller->handleToggle($_SESSION['user_id'], $roomId);
echo json_encode($result);
exit;