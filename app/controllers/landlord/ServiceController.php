<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../../config/database.php';
require_once '../../models/landlord/ServiceModel.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Vui lòng đăng nhập."]);
    exit;
}

$model = new ServiceModel($con);
$landlord_id = $_SESSION['user_id'];
// $landlord_id = 2;
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_all':
        $services = $model->getAll($landlord_id);
        echo json_encode(["status" => "success", "data" => $services]);
        break;

    case 'add':
        $code = $_POST['code'] ?? '';
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $unit = $_POST['unit'] ?? '';
        $type = $_POST['type'] ?? 'variable';
        $description = $_POST['description'] ?? '';

        if ($model->checkCodeExists($code, $landlord_id)) {
            echo json_encode(["status" => "error", "message" => "Mã dịch vụ '$code' đã tồn tại!"]);
            exit;
        }

        $success = $model->addService($landlord_id, $code, $name, $price, $unit, $type, $description);
        if ($success) {
            echo json_encode(["status" => "success", "message" => "Thêm dịch vụ thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi thêm."]);
        }
        break;

    case 'edit':
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $unit = $_POST['unit'] ?? '';
        $type = $_POST['type'] ?? 'variable';
        $description = $_POST['description'] ?? '';

        $success = $model->updateService($id, $landlord_id, $name, $price, $unit, $type, $description);
        if ($success) {
            echo json_encode(["status" => "success", "message" => "Cập nhật dịch vụ thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi cập nhật."]);
        }
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;
        $success = $model->deleteService($id, $landlord_id);
        if ($success) {
            echo json_encode(["status" => "success", "message" => "Đã xóa dịch vụ!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi xóa."]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Hành động không hợp lệ."]);
        break;
}
