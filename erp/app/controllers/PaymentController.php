<?php

require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Invoice.php';

class PaymentController {

    public static function store() {
        require_auth();

        $invoice_id = $_POST['invoice_id'];
        $amount     = (float) $_POST['amount'];
        $method     = trim($_POST['method']);
        $date       = $_POST['payment_date'];

        if ($amount <= 0) {
            echo "Importe inválido";
            return;
        }

        Payment::create([
            'invoice_id'  => $invoice_id,
            'amount'      => $amount,
            'payment_date'=> $date,
            'method'      => $method
        ]);

        // Recalcular estado de la factura
        $invoice = Invoice::findWithClient($invoice_id, auth_user()['id']);
        $paid    = Payment::totalPaid($invoice_id);

        if ($paid >= $invoice['total']) {
            Invoice::changeStatus($invoice_id, auth_user()['id'], 'paid');
        }

        header('Location: index.php?action=invoices_show&id=' . $invoice_id);
        exit;
    }
}