<?php
require_once __DIR__ . '/../../models/customer/InvoiceModel.php';

class InvoiceController {
    private $model;

    public function __construct($db) {
        $this->model = new InvoiceModel($db);
    }

    public function listInvoices($user_id) {
        return $this->model->getInvoicesByUser($user_id);
    }
}