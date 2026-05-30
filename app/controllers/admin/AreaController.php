<?php
require_once __DIR__ . '/../../models/Admin/Area.php';

class AreaController {

    private $areaModel;

    public function __construct() {
        $this->areaModel = new Area();
    }

    public function index($keyword, $status) {
        return $this->areaModel->getAll($keyword, $status);
    }

    public function store($name) {
        return $this->areaModel->create($name);
    }

    public function update($id, $name) {
        return $this->areaModel->update($id, $name);
    }

    public function toggle($id) {
        return $this->areaModel->toggle($id);
    }
}