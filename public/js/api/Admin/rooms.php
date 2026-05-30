<?php
header("Content-Type: application/json");

require_once "../../../../app/controllers/admin/RoomController.php";

$controller = new RoomController();
$method = $_SERVER['REQUEST_METHOD'];

/* =====================================================
    GET
    - Lấy danh sách phòng chờ duyệt
    - Hoặc lấy chi tiết 1 phòng
===================================================== */
if ($method === "GET") {

    if (isset($_GET['id'])) {
        echo json_encode(
            $controller->show($_GET['id'])
        );
    } else {
        echo json_encode(
            $controller->index($_GET)
        );
    }
}

/* =====================================================
    PATCH
    - approve
    - reject
===================================================== */
if ($method === "PATCH") {

    $data = json_decode(file_get_contents("php://input"), true);

    if ($data['action'] == "approve")
        $result = $controller->approve($data['id']);

    else if ($data['action'] == "reject")
        $result = $controller->reject($data['id'],$data['reason']);

    echo json_encode([
        "success" => $result ? true : false
    ]);
}