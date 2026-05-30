<?php
require_once __DIR__ . '/../../models/Admin/ActivityLog.php';

class ActivityLogController
{
    private $model;

    public function __construct()
    {
        $this->model = new ActivityLog();
    }

    public function index($params)
    {
        $result = $this->model->getAll($params);
        return $result;
    }

    public function show($id)
    {
        $log = $this->model->findById($id);
        return $log;
    }
 public function export($params)
{
           if (empty($params['date_from']) || empty($params['date_to'])) {
            return [
                "success" => false,
                "message" => "Vui lòng chọn khoảng thời gian"
            ];
        }

        $data = $this->model->getAllNoLimit($params);

        return [
            "success" => true,
            "data" => $data
        ];
}

    // public function delete($id)
    // {
    //     return $this->model->delete($id);
    // }
}
