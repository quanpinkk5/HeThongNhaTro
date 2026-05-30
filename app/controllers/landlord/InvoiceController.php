<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../../config/database.php';
require_once '../../models/landlord/InvoiceModel.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$model = new InvoiceModel($con);
$landlord_id = $_SESSION['user_id'];
// $landlord_id = 2;
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_all_data':
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 9;
        $offset = ($page - 1) * $limit;

        $filter_month = $_GET['month'] ?? date('Y-m');
        $parts = explode('-', $filter_month);
        $year = $parts[0] ?? date('Y');
        $month = $parts[1] ?? date('m');

        $f_building = $_GET['building'] ?? '';
        $f_status = $_GET['status'] ?? '';
        $keyword = $_GET['keyword'] ?? '';

        $totalRows = $model->getTotalCount($landlord_id, $f_building, $month, $year, $f_status, $keyword);
        $totalPages = ceil($totalRows / $limit);

        echo json_encode([
            "status" => "success",
            "buildings" => $model->getBuildings($landlord_id),
            "invoices" => $model->getInvoices($landlord_id, $f_building, $month, $year, $f_status, $keyword, $limit, $offset),
            "pagination" => ["current_page" => $page, "total_pages" => $totalPages]
        ]);
        break;

    case 'get_rented_rooms':
        echo json_encode(["status" => "success", "data" => $model->getRentedRooms($_GET['building_id'] ?? 0)]);
        break;

    case 'get_services':
        $data = $model->getRoomServices($_GET['contract_id'] ?? 0);
        if (!empty($data)) echo json_encode(["status" => "success", "data" => $data]);
        else echo json_encode(["status" => "error", "message" => "Không tìm thấy phòng/dịch vụ"]);
        break;

    case 'get_invoice_details':
        $data = $model->getInvoiceDetails($_GET['id'] ?? 0);
        if ($data) echo json_encode(["status" => "success", "data" => $data]);
        else echo json_encode(["status" => "error", "message" => "Hóa đơn không tồn tại."]);
        break;

    case 'create':
        echo json_encode($model->createInvoice($_POST['contract_id'], $_POST['month'], $_POST['svc_qty'] ?? []));
        break;

    case 'pay':
        echo json_encode($model->payInvoice($_POST['id']));
        break;

    case 'delete':
        echo json_encode($model->deleteInvoice($_POST['id']));
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action."]);
        break;
}
