<?php
header('Content-Type: application/json');
ob_start();
require_once '../../../../config/database.php'; 
require_once '../../../../app/controllers/customer/InvoiceDetailController.php';

$invoice_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($invoice_id <= 0) {
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => 'ID hóa đơn không hợp lệ']);
    exit;
}

$controller = new InvoiceDetailController($con);
$data = $controller->getFullData($invoice_id);

ob_clean();
if ($data) {
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy dữ liệu hóa đơn']);
}
exit;