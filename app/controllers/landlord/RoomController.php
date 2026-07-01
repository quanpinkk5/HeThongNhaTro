<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once '../../../config/database.php';
require_once '../../models/landlord/RoomModel.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Vui lòng đăng nhập."]);
    exit;
}

$model = new RoomModel($con);
$landlord_id = $_SESSION['user_id'];
// $landlord_id = 3;
$action = $_REQUEST['action'] ?? 'get_all';

// Thư mục lưu ảnh ở root public/images
$base_dir = dirname(__DIR__, 3) . '/public/images/';

function handleImageUpload($file, $base_dir)
{
    if (!file_exists($base_dir)) mkdir($base_dir, 0777, true);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = time() . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $base_dir . $fileName)) return $fileName;
    return false;
}

switch ($action) {
    case 'get_all':
        echo json_encode([
            "status" => "success",
            "buildings" => $model->getBuildings($landlord_id),
            "services" => $model->getServices($landlord_id),
            "rooms" => $model->getAllRooms($landlord_id)
        ]);
        break;

    case 'add':
        $title = trim($_POST['title']);
        $price = (float)$_POST['price'];
        $area = (float)$_POST['area'];

        if ($price <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Giá phòng phải lớn hơn 0."
            ]);
            exit;
        }

        if ($area <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "Diện tích phải lớn hơn 0."
            ]);
            exit;
        }

        if (empty($title)) {
            echo json_encode([
                "status" => "error",
                "message" => "Tên phòng không được để trống."
            ]);
            exit;
        }
        $room_id = $model->addRoom($_POST['building_id'], $landlord_id, $_POST['title'], $_POST['price'], $_POST['area'], $_POST['description']);
        if ($room_id) {
            if (isset($_POST['services'])) $model->addRoomServices($room_id, $_POST['services']);

            // Upload ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileName = handleImageUpload($_FILES['image'], $base_dir);
                if ($fileName) $model->saveImage($room_id, $fileName);
            }

            // Thông báo Admin
            $model->notifyAdmins("Có phòng mới cần duyệt", "Chủ trọ vừa thêm phòng mới: [{$_POST['title']}]. Vui lòng duyệt.", "quanlyduyetphong.php");

            echo json_encode(["status" => "success", "message" => "Thêm phòng thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi hệ thống khi thêm."]);
        }
        break;

    case 'edit':
        $id = $_POST['id'];
        if ($model->updateRoom($id, $landlord_id, $_POST['title'], $_POST['price'], $_POST['area'], $_POST['description'])) {
            $model->deleteRoomServices($id);
            if (isset($_POST['services'])) $model->addRoomServices($id, $_POST['services']);

            // Đổi ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $old_images = $model->getRoomImages($id);
                foreach ($old_images as $img) {
                    if ($img && file_exists($base_dir . $img)) unlink($base_dir . $img);
                }
                $model->deleteRoomImagesDb($id);
                $fileName = handleImageUpload($_FILES['image'], $base_dir);
                if ($fileName) $model->saveImage($id, $fileName);
            }
            echo json_encode(["status" => "success", "message" => "Cập nhật phòng thành công!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi cập nhật."]);
        }
        break;

    case 'delete':
        $id = $_POST['id'];
        if ($model->getRoomStatus($id) === 'RENTED') {
            echo json_encode(["status" => "error", "message" => "Không thể xóa: Phòng đang có người thuê."]);
        } else {
            if ($model->deleteRoom($id, $landlord_id)) {
                echo json_encode(["status" => "success", "message" => "Đã ẩn phòng thành công!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Không thể ẩn phòng. Vui lòng thử lại."]);
            }
        }
        break;

    case 'request_post':
        $id = $_POST['id'];
        $room = $model->getRoomForPost($id, $landlord_id);

        if (!$room) {
            echo json_encode(["status" => "error", "message" => "Phòng không tồn tại."]);
        } elseif ((int)$room['img_count'] === 0) {
            echo json_encode(["status" => "error", "message" => "Vui lòng cập nhật ít nhất 1 hình ảnh trước khi đăng tin."]);
        } elseif ($room['status_room'] === 'RENTED') {
            echo json_encode(["status" => "error", "message" => "Phòng đang thuê không thể đăng."]);
        } elseif ($room['status'] === 'PENDING' || $room['status'] === 'APPROVED') {
            echo json_encode(["status" => "error", "message" => "Phòng đã gửi duyệt hoặc đã đăng."]);
        } else {
            $model->updateRoomPostStatus($id);
            $model->notifyAdmins("Có phòng mới cần duyệt", "Chủ trọ yêu cầu đăng tin phòng (ID: $id).", "quanlyduyetphong.php");
            echo json_encode(["status" => "success", "message" => "Đã gửi yêu cầu đăng tin!"]);
        }
        break;
}
