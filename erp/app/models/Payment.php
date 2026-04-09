<?php

class Payment {

    public static function create($data) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            INSERT INTO payments (invoice_id, amount, payment_date, method)
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['invoice_id'],
            $data['amount'],
            $data['payment_date'],
            $data['method']
        ]);
    }

    public static function totalPaid($invoice_id) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT SUM(amount) FROM payments
            WHERE invoice_id = ?
        ");
        $stmt->execute([$invoice_id]);

        return (float) $stmt->fetchColumn();
    }

    public static function allByInvoice($invoice_id) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT * FROM payments
            WHERE invoice_id = ?
            ORDER BY payment_date DESC
        ");
        $stmt->execute([$invoice_id]);

        return $stmt->fetchAll();
    }
}