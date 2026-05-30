<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../../../../app/controllers/customer/CustomerController.php';

$controller = new CustomerController();

echo json_encode($controller->getAreas());