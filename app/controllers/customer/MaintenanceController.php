<?php
require_once __DIR__ . '/../../models/customer/MaintenanceModel.php';

class MaintenanceController {
    private $model;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new MaintenanceModel($db);
    }

    public function getRoomInfo($room_id, $user_id) {
        $room = $this->model->checkRoomAccess($room_id, $user_id);
        if ($room) {
            // Phải trả về mảng có key 'data' để khớp với maintenance.js
            return ['status' => 'success', 'data' => $room];
        }
        return ['status' => 'error', 'message' => 'Không tìm thấy phòng'];
    }

    public function handleReport($data, $files, $user_id, $user_name) {
        $id_input = intval($data['room_id']); 
        $content = trim($data['content'] ?? 'Báo cáo sự cố');
        $level = $data['level'] ?? 'LOW';

        $room = $this->model->checkRoomAccess($id_input, $user_id);
        if (!$room) return ['status' => 'error', 'message' => 'Lỗi xác thực phòng'];

        $this->db->begin_transaction();
        try {
            // Dùng true_room_id đã lấy từ Model
            $m_id = $this->model->createMaintenance($room['true_room_id'], $user_name, $content, $level);
            if (!$m_id) throw new Exception("Lỗi Database");

            if (!empty($files['images']['name'][0])) {
                $dir = __DIR__ . '/../../../public/uploads/maintenance/';
                if (!file_exists($dir)) mkdir($dir, 0777, true);
                foreach ($files['images']['tmp_name'] as $k => $tmp) {
                    if (!$tmp) continue;
                    $fn = time() . '_' . uniqid() . '.jpg';
                    if (move_uploaded_file($tmp, $dir . $fn)) {
                        $this->model->saveImage($m_id, $fn);
                    }
                }
            }

            $this->model->createNotification($room['landlord_id'], "Phòng " . $room['title'], "Khách gửi báo cáo sự cố", "maintenance.php");

            $this->db->commit();
            return ['status' => 'success', 'message' => 'Gửi báo cáo thành công!'];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}