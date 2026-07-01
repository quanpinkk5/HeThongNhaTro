<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../../config/database.php';
require_once '../../models/landlord/TenantModel.php';

// Kiểm tra quyền
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'LANDLORD')) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$model = new TenantModel($con);
$landlord_id = $_SESSION['user_id'];
// $landlord_id = 2;

$action = $_REQUEST['action'] ?? 'get_list';

if ($action === 'get_list') {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 8;
    $keyword = trim($_GET['keyword'] ?? '');
    $offset = ($page - 1) * $limit;

    $totalRows = $model->getTotal($landlord_id, $keyword);
    $totalPages = ceil($totalRows / $limit);
    $customers = $model->getList($landlord_id, $limit, $offset, $keyword);

    echo json_encode([
        "status" => "success",
        "data" => $customers,
        "pagination" => [
            "current_page" => $page,
            "total_pages" => $totalPages,
            "total_rows" => $totalRows
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Hành động không hợp lệ."]);
}
