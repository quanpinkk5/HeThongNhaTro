<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../../../app/controllers/customer/NotificationController.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]); // Trả về mảng rỗng nếu chưa login
    exit;
}

try {
    $controller = new NotificationController($con);
    $userId = $_SESSION['user_id'];
    $action = $_GET['action'] ?? 'fetch';

    if ($action === 'fetch') {
        $data = $controller->index($userId);
        // Đảm bảo xuất ra mảng sạch
        echo json_encode($data ?: []);
        exit;
    }
    // Các action khác...
} catch (Exception $e) {
    echo json_encode([]);
    exit;
}
// ... đoạn nạp database và controller giữ nguyên ...

try {
    $controller = new NotificationController($con);
    $userId = $_SESSION['user_id'];
    $action = $_GET['action'] ?? 'fetch';

    if ($action === 'fetch') {
        $data = $controller->index($userId);
        $unreadCount = $controller->getUnreadCount($userId);
        echo json_encode(['status' => 'success', 'data' => $data, 'unread_count' => $unreadCount]);
        exit;
    }

    if ($action === 'read' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $readId = $_POST['read_id'] ?? 0;
        $redirect = $controller->handleRead(intval($readId), $userId);
        echo json_encode(['status' => 'success', 'redirect' => $redirect]);
        exit;
    }

    if ($action === 'mark_all' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->handleMarkAll($userId);
        echo json_encode(['status' => 'success']);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error']);
}