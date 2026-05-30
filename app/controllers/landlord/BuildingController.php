<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../../config/database.php';
require_once '../../models/landlord/BuildingModel.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Vui lòng đăng nhập."]);
    exit;
}

$model = new BuildingModel($con);
$landlord_id = $_SESSION['user_id'];
// $landlord_id = 2;

$action = $_REQUEST['action'] ?? 'get_all';

switch ($action) {
    case 'get_all':
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $totalRows = $model->getTotal($landlord_id);
        $totalPages = ceil($totalRows / $limit);
        $buildings = $model->getAll($landlord_id, $limit, $offset);

        echo json_encode([
            "status" => "success",
            "data" => $buildings,
            "pagination" => [
                "current_page" => $page,
                "total_pages" => $totalPages
            ]
        ]);
        break;

    case 'add':
        $code = $_POST['code'] ?? '';
        $name = $_POST['name'] ?? '';
        $address = $_POST['address'] ?? '';
        $floors = $_POST['floors'] ?? 0;

        if ($model->checkCodeExists($code, $landlord_id)) {
            echo json_encode(["status" => "error", "message" => "Mã tòa nhà '$code' đã tồn tại! Vui lòng chọn mã khác."]);
            exit;
        }

        if ($model->add($landlord_id, $code, $name, $address, $floors)) {
            echo json_encode(["status" => "success", "message" => "Thêm tòa nhà thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi thêm."]);
        }
        break;

    case 'edit':
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $address = $_POST['address'] ?? '';
        $floors = $_POST['floors'] ?? 0;

        if ($model->update($id, $landlord_id, $name, $address, $floors)) {
            echo json_encode(["status" => "success", "message" => "Cập nhật thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi cập nhật."]);
        }
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;
        $result = $model->delete($id, $landlord_id);

        if ($result === 'has_rooms') {
            echo json_encode(["status" => "error", "message" => "Không thể xóa: Tòa nhà này đang chứa phòng trọ!"]);
        } elseif ($result === 'success') {
            echo json_encode(["status" => "success", "message" => "Đã xóa tòa nhà!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi xóa."]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Hành động không hợp lệ."]);
        break;
}
