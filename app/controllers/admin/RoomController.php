<?php
session_start();
require_once __DIR__ . "/../../models/Admin/Room.php";
require_once __DIR__ . '/../../models/Admin/log.php';
require_once __DIR__ . '/../../models/Admin/Notification.php';
class RoomController
{
    private $roomModel;
    private $log;
    private $notify;

    public function __construct()
    {
        $this->roomModel = new Room();
        $this->log  = new Log();
        $this->notify = new Notification();
    }

    /* =====================================================
        GET LIST + FILTER + PAGINATION
    ===================================================== */
    public function index($params)
    {
        return $this->roomModel->getAll($params);
    }

    /* =====================================================
        GET DETAIL
    ===================================================== */
    public function show($id)
    {
        return $this->roomModel->findByid($id);
    }

    /* =====================================================
        APPROVE ROOM
    ===================================================== */
    public function approve($id)
    {
        $room = $this->roomModel->findByid($id);
        if (!$room) return false;
        $admin_id = $_SESSION['user_id'];
        // $admin_id = 5;
        $updated = $this->roomModel->approve($id, "APPROVED");
        if (!$updated) {
            return false;
        } else {
            $this->log->write(
                $admin_id,
                "APPROVE_ROOM",
                $id,
                "ROOM",
                "LANDLORD",
                "Duyệt phòng \"{$room['title']}\""
            );
        }
        // gửi notification
        $this->notify->create(
            $room['landlord_id'],
            "Phòng đã được duyệt",
            "Phòng \"{$room['title']}\" đã được admin duyệt và hiển thị.",
            "SYSTEM",
            "room.php"
        );
        return true;
    }

    /* =====================================================
        REJECT ROOM
    ===================================================== */
    public function reject($id, $reason)
    {
        $room = $this->roomModel->findByid($id);
        if (!$room) return false;
        $admin_id = $_SESSION['user_id'];
        // $admin_id = 5;
        $updated = $this->roomModel->reject($id, "REJECTED");
        if (!$updated) return false;
        else {
            $this->log->write(
                $admin_id,
                "REJECT_ROOM",
                $id,
                "ROOM",
                "LANDLORD",
                "Từ chối phòng \"{$room['title']}\" vì lí do : {$reason}"
            );
        }


        $this->notify->create(
            $room['landlord_id'],
            "Phòng bị từ chối",
            "Phòng \"{$room['title']}\" đã bị admin từ chối vì lí do {$reason} ",
            "SYSTEM",
            "room.php"
        );


        return true;
    }
}
