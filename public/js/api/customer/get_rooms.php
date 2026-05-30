<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . '/../../../../app/controllers/customer/CustomerController.php';

$user_id = $_SESSION['user_id'] ?? 0;
$search = $_GET['search'] ?? '';
$district = $_GET['district'] ?? '';

$controller = new CustomerController();
echo json_encode($controller->getRooms($user_id, $search, $district));