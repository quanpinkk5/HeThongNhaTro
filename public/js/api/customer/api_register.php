<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../app/controllers/customer/RegisterController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AuthController(); 
    echo json_encode($controller->register($_POST));
}