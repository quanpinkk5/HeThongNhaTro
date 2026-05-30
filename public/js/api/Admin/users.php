<?php
header("Content-Type: application/json");
require_once "../../../../app/controllers/admin/UserController.php";

$controller = new UserController();
$method = $_SERVER['REQUEST_METHOD'];

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

if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $result = $controller->store($data);
    echo json_encode($result);
}

if ($method === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);
    $result = $controller->update($data['id'], $data);
    echo json_encode($result);
}

if ($method === "PATCH") {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data['action'] == "lock")
        $result = $controller->lock($data['id']);

    else if ($data['action'] == "unlock")
        $result = $controller->unlock($data['id']);

    else if ($data['action'] == "reset")
        $result = $controller->reset($data['id']);

    else if ($data['action'] == "send_mail")
        $result = $controller->sendMail($data['id']);

    echo json_encode([
        "success" => $result ? true : false,
        "data"    => $data['action'] == "reset" ? $result : null
    ]);
}

if ($method === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode([
        "success" => $controller->lock($data['id'])
    ]);
}
