<?php
require_once __DIR__ . '/../../models/customer/InvoiceDetailModel.php';

class InvoiceDetailController {
    private $model;

    public function __construct($db) {
        $this->model = new InvoiceDetailModel($db);
    }

    public function getFullData($invoice_id) {
        $invoice = $this->model->getInvoiceDetail($invoice_id);
        if (!$invoice) return null;

        $items = $this->model->getInvoiceItems($invoice_id);
        return [
            'invoice' => $invoice,
            'items' => $items
        ];
    }
}