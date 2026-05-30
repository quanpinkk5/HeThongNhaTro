<?php
session_start();
ob_start(); // Chặn mọi output lạ
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../app/controllers/customer/ContractController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

try {
    $controller = new ContractController($con);
    $data = $controller->getRentingRooms($_SESSION['user_id']);
    ob_clean(); 
    echo json_encode($data);
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['error' => $e->getMessage()]);
}
exit;