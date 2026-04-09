<?php

class Invoice {

    /* CREAR FACTURA */
    public static function create($data) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            INSERT INTO invoices
            (user_id, client_id, budget_id, invoice_number, issue_date, subtotal, vat_total, total, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");

        $stmt->execute([
            $data['user_id'],
            $data['client_id'],
            $data['budget_id'],
            $data['invoice_number'],
            date('Y-m-d'),
            $data['subtotal'],
            $data['vat_total'],
            $data['total']
        ]);

        return $pdo->lastInsertId();
    }

    /* AÑADIR LÍNEA DE FACTURA */
    public static function addItem($invoice_id, $item) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            INSERT INTO invoice_items
            (invoice_id, product_name, quantity, unit_price, vat_percent, line_total)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $invoice_id,
            $item['product_name'],
            $item['quantity'],
            $item['unit_price'],
            $item['vat_percent'],
            $item['line_total']
        ]);
    }

    /* NUMERACIÓN AUTOMÁTICA */
    public static function nextNumber($user_id) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT invoice_number
            FROM invoices
            WHERE user_id = ?
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute([$user_id]);
        $last = $stmt->fetchColumn();

        $year = date('Y');

        if (!$last) {
            return "FAC-$year-0001";
        }

        $num = (int) substr($last, -4) + 1;
        return "FAC-$year-" . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /* OBTENER FACTURA + CLIENTE */
    public static function findWithClient($id, $user_id) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT i.*, c.name AS client_name, c.tax_id, c.email
            FROM invoices i
            JOIN clients c ON c.id = i.client_id
            WHERE i.id = ? AND i.user_id = ?
        ");
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch();
    }

    /* LÍNEAS DE FACTURA */
    public static function items($invoice_id) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT *
            FROM invoice_items
            WHERE invoice_id = ?
        ");
        $stmt->execute([$invoice_id]);
        return $stmt->fetchAll();
    }

    public static function changeStatus($id, $user_id, $status) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            UPDATE invoices
            SET status = ?
            WHERE id = ? AND user_id = ?
        ");

        return $stmt->execute([$status, $id, $user_id]);
    }

}