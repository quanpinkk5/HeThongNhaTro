<?php
header("Content-Type: application/json");

require_once "../../../../app/controllers/admin/ActivityLogController.php";

$controller = new ActivityLogController();
$method = $_SERVER['REQUEST_METHOD'];

/* =======================
   HÀM CLEAN DATA
======================= */
function clean($value) {

    $value = str_replace(["\r", "\n"], ' ', $value);
    $value = trim($value);

    return $value;
}

/* =======================
   GET
======================= */
if ($method === "GET") {

    // ================= EXPORT CSV =================
    if (isset($_GET['export']) && $_GET['export'] == 1) {

        $result = $controller->export($_GET);

        if (!$result['success']) {
            echo $result['message'];
            exit;
        }

        $data = $result['data'];

        $filename = 'activity_logs_' . date('Ymd_His') . '.csv';
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        // BOM để hiển thị tiếng Việt
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'ID',
            'Admin',
            'Hành động',
            'Target ID',
            'Target Type',
            'Tên Target',
            'Role',
            'IP',
            'User Agent',
            'Thời gian'
        ], ';');

        foreach ($data as $row) {

            fputcsv($output, [
                $row['id'],
                clean($row['admin_name']),
                clean($row['action']),
                $row['target_id'],
                clean($row['target_type']),
                clean($row['target_name']),
                clean($row['target_role']),
                $row['ip_address'],


                substr(clean($row['user_agent']), 0, 100),

                $row['created_at']
            ], ';');
        }

        fclose($output);
        exit;
    }

    // ================= DETAIL =================
    if (isset($_GET['id'])) {
        header("Content-Type: application/json");
        echo json_encode($controller->show($_GET['id']));
        exit;
    }

    // ================= LIST =================
    header("Content-Type: application/json");
    echo json_encode($controller->index($_GET));
}
