<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '../../../../../config/database.php'; 
require_once __DIR__ . '/../../../../app/controllers/customer/FavoriteController.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$controller = new FavoriteController($con);
echo json_encode($controller->getFavorites($_SESSION['user_id']));

exit;