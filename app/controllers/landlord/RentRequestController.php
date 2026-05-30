<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../../config/database.php';
require_once '../../models/landlord/RentRequestModel.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}
$model = new RentRequestModel($con);
$landlord_id = $_SESSION['user_id'];
// $landlord_id = 2;
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    // 1. Lấy danh sách (Có tìm kiếm, lọc)
    case 'get_all':
        $keyword = $_GET['keyword'] ?? '';
        $status = $_GET['status'] ?? '';
        $requests = $model->getRequests($landlord_id, $keyword, $status);
        echo json_encode(["status" => "success", "data" => $requests]);
        break;

    // 2. Duyệt yêu cầu
    case 'approve':
        $req_id = $_POST['request_id'] ?? 0;
        if ($req_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Yêu cầu không hợp lệ."]);
            exit;
        }

        $request = $model->getRequestById($req_id);
        if (!$request) {
            echo json_encode(["status" => "error", "message" => "Không tìm thấy yêu cầu thuê."]);
            exit;
        }

        $success = $model->approveRequest($req_id, $request['room_id'], $request['user_id'], $request['room_name']);
        if ($success) {
            echo json_encode(["status" => "success", "message" => "Đã duyệt yêu cầu. Bạn có thể tạo hợp đồng sau khi ký kết."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi duyệt."]);
        }
        break;

    // 3. Từ chối yêu cầu
    case 'reject':
        $req_id = $_POST['request_id'] ?? 0;
        $reason = trim($_POST['reason'] ?? '');

        if ($req_id <= 0) {
            echo json_encode(["status" => "error", "message" => "Yêu cầu không hợp lệ."]);
            exit;
        }

        $request = $model->getRequestById($req_id);
        if (!$request) {
            echo json_encode(["status" => "error", "message" => "Không tìm thấy yêu cầu thuê."]);
            exit;
        }

        $success = $model->rejectRequest($req_id, $request['user_id'], $request['room_name'], $request['note'], $reason);
        if ($success) {
            echo json_encode(["status" => "success", "message" => "Đã từ chối yêu cầu thuê."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi từ chối."]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Hành động không hợp lệ."]);
        break;
}
