<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../app/controllers/customer/ProfileController.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Hết phiên làm việc']);
    exit;
}

$controller = new ProfileController($con);
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['full_name'])) {
        echo json_encode($controller->handleUpdateName($userId, $_POST['full_name']));
    } 

    else if (isset($_POST['current_password'])) {
        echo json_encode($controller->handleChangePassword(
            $userId, 
            $_POST['current_password'], 
            $_POST['new_password'], 
            $_POST['confirm_password']
        ));
    }
}