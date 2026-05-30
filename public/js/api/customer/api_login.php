<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../app/controllers/customer/LoginController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $controller = new LoginController($con);
    echo json_encode($controller->handleLogin($email, $password));
}