<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../../config/database.php';
require_once '../../models/landlord/ContractModel.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Đăng nhập để tiếp tục."]);
    exit;
}

$model = new ContractModel($con);
$landlord_id = $_SESSION['user_id'];
// $landlord_id = 2;
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_all_data':
        $limit = 100; // Có thể làm phân trang sâu hơn nếu cần
        $offset = 0;
        $f_building = $_GET['building'] ?? '';
        $f_status = $_GET['status'] ?? '';
        $f_keyword = $_GET['keyword'] ?? '';

        echo json_encode([
            "status" => "success",
            "buildings" => $model->getBuildings($landlord_id),
            "tenants" => $model->getTenants($landlord_id),
            "requests" => $model->getRenewalRequests($landlord_id),
            "contracts" => $model->getContracts($landlord_id, $f_building, $f_status, $f_keyword, $limit, $offset)
        ]);
        break;

    case 'get_valid_rooms':
        $bid = $_GET['building_id'] ?? 0;
        echo json_encode(["status" => "success", "data" => $model->getValidRooms($bid)]);
        break;

    case 'add':
        if ($model->addContract($_POST['room_id'], $_POST['user_id'], $_POST['start_date'], $_POST['duration'])) {
            echo json_encode(["status" => "success", "message" => "Đã tạo hợp đồng mới!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi tạo hợp đồng."]);
        }
        break;

    case 'renew':
        if ($model->renewContract($_POST['id'], $_POST['months'])) {
            echo json_encode(["status" => "success", "message" => "Gia hạn thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi gia hạn."]);
        }
        break;

    case 'cancel':
        if ($model->cancelContract($_POST['id'])) {
            echo json_encode(["status" => "success", "message" => "Đã thanh lý hợp đồng!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi thanh lý."]);
        }
        break;

    case 'approve_request':
        if ($model->approveExtension($_POST['id'])) {
            echo json_encode(["status" => "success", "message" => "Đã duyệt gia hạn!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi duyệt."]);
        }
        break;

    case 'reject_request':
        if ($model->rejectExtension($_POST['id'])) {
            echo json_encode(["status" => "success", "message" => "Đã từ chối gia hạn."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi từ chối."]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action."]);
        break;
}
