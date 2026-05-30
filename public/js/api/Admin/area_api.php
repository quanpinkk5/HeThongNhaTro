<?php
header("Content-Type: application/json");
require_once "../../../../app/controllers/admin/AreaController.php";

$controller = new AreaController();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    $keyword = $_GET['keyword'] ?? '';
    $status  = $_GET['status'] ?? '';

    echo json_encode([
        "success" => true,
        "data" => $controller->index($keyword, $status)
    ]);
}

if ($method === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    $result = null;

    if ($data['action'] === 'add') {
        $result = $controller->store($data['name']);
    }

    if ($data['action'] === 'edit') {
        $result = $controller->update($data['id'], $data['name']);
    }

    if ($data['action'] === 'toggle') {
        $result = $controller->toggle($data['id']);
    }

    echo json_encode(
        is_array($result)
            ? $result
            : ["success" => $result ? true : false]
    );
}