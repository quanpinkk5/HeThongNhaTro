<?php
session_start();
header("Content-Type: application/json");

$response = [
    'is_logged_in' => isset($_SESSION['user_id']),
    'user_name' => $_SESSION['user_name'] ?? '',
    'unread_count' => 0 
];

echo json_encode($response);