<?php
header("Content-Type: application/json");

require_once '../../../../app/controllers/admin/NotificationController.php';

session_start();
$userId = $_SESSION['user_id'];
// $userId = 5;

$controller = new NotificationController();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    if (isset($_GET['count'])) {
        echo json_encode([
            "success" => true,
            "count" => $controller->getUnreadCount($userId)
        ]);
        exit;
    }

    $controller->index($userId);
}

if ($method === 'DELETE') {

    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['id'])) {
        $controller->deleteOne((int)$input['id'], $userId);
    }

    if (isset($input['ids'])) {
        $ids = array_map('intval', $input['ids']);
        $controller->deleteMultiple($ids, $userId);
    }
}
if ($method === 'PATCH') {

    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['mark_read'])) {
        $controller->markOneAsRead((int)$input['mark_read'], $userId);
    }
}