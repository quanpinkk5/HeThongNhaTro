<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../../config/database.php';
require_once '../../models/landlord/MaintenanceModel.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$model = new MaintenanceModel($con);
$landlord_id = $_SESSION['user_id'];
// $landlord_id = 2;
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_all':
        $incidents = $model->getAll($landlord_id);
        echo json_encode(["status" => "success", "data" => $incidents]);
        break;

    case 'get_images':
        $id = $_GET['id'] ?? 0;
        $images = $model->getImages($id);
        echo json_encode(["status" => "success", "data" => $images]);
        break;

    case 'update':
        $id = $_POST['id'] ?? 0;
        $level = $_POST['level'] ?? 'MEDIUM';
        $status = $_POST['status'] ?? 'PENDING';
        $cost = !empty($_POST['cost']) ? $_POST['cost'] : 0;

        $oldInfo = $model->getIncidentInfo($id);

        if ($model->update($id, $level, $status, $cost)) {
            // Nếu trạng thái thay đổi, gửi thông báo cho khách đang thuê
            if ($oldInfo && $oldInfo['status'] != $status) {
                $tenantId = $model->getActiveTenantByRoom($oldInfo['room_id']);
                if ($tenantId) {
                    $title = "Cập nhật yêu cầu bảo trì #" . $id;
                    $content = "";
                    if ($status == 'PROCESSING') $content = "Yêu cầu của bạn đang được nhân viên xử lý.";
                    if ($status == 'DONE') $content = "Sự cố đã được khắc phục xong.";

                    if ($content != "") $model->notifyTenant($tenantId, $title, $content);
                }
            }
            echo json_encode(["status" => "success", "message" => "Đã cập nhật thông tin bảo trì!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi cập nhật."]);
        }
        break;

    case 'delete':
        $id = $_POST['id'] ?? 0;
        if ($model->delete($id)) {
            echo json_encode(["status" => "success", "message" => "Đã xóa bản ghi sự cố!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi xóa."]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Hành động không hợp lệ."]);
        break;
}
