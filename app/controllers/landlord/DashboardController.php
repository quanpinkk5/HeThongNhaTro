<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../../config/database.php';
require_once '../../models/landlord/DashboardModel.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$model = new DashboardModel($con);
$landlord_id = $_SESSION['user_id'];
// $landlord_id = 2;
$action = $_REQUEST['action'] ?? 'get_dashboard_data';

if ($action === 'get_dashboard_data') {
    $total_rooms = $model->getTotalRooms($landlord_id);
    $rented_rooms = $model->getRentedRooms($landlord_id);
    $empty_rooms = $total_rooms - $rented_rooms;
    $total_debt = $model->getTotalDebt($landlord_id);

    $revenue_data = $model->getRevenueLast6Months($landlord_id);
    $revenue_labels = array_column($revenue_data, 'label');
    $revenue_values = array_map('floatval', array_column($revenue_data, 'revenue'));

    echo json_encode([
        "status" => "success",
        "stats" => [
            "total_rooms" => $total_rooms,
            "rented_rooms" => $rented_rooms,
            "empty_rooms" => $empty_rooms,
            "total_debt" => $total_debt
        ],
        "lists" => [
            "unpaid_invoices" => $model->getUnpaidInvoices($landlord_id),
            "expiring_contracts" => $model->getExpiringContracts($landlord_id)
        ],
        "charts" => [
            "revenue" => [
                "labels" => $revenue_labels,
                "values" => $revenue_values
            ]
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid action"]);
}
