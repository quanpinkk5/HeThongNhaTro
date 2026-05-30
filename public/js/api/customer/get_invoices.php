<?php
session_start();
ob_start(); 
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../app/controllers/customer/InvoiceController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $controller = new InvoiceController($con);
    $invoices = $controller->listInvoices($_SESSION['user_id']);
    ob_clean();
    echo json_encode($invoices);
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['error' => $e->getMessage()]);
}
exit;