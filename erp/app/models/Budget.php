<?php

class Budget {

    public static function allByUser($user_id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT b.*, c.name AS client_name
            FROM budgets b
            JOIN clients c ON c.id = b.client_id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public static function create($data) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            INSERT INTO budgets
            (user_id, client_id, number, status, subtotal, vat_total, total)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['user_id'],
            $data['client_id'],
            $data['number'],
            'draft',
            $data['subtotal'],
            $data['vat_total'],
            $data['total']
        ]);

        return $pdo->lastInsertId();
    }

    public static function addItem($budget_id, $item) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO budget_items
            (budget_id, product_name, quantity, unit_price, vat_percent, line_total)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $budget_id,
            $item['name'],
            $item['qty'],
            $item['price'],
            $item['vat'],
            $item['total']
        ]);
    }
    public static function find($id, $user_id) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM budgets
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$id, $user_id]);
    return $stmt->fetch();
}

    public static function items($budget_id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM budget_items
            WHERE budget_id = ?
        ");
        $stmt->execute([$budget_id]);
        return $stmt->fetchAll();
    }

    public static function updateTotals($id, $subtotal, $vat_total, $total) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE budgets
            SET subtotal = ?, vat_total = ?, total = ?
            WHERE id = ?
        ");
        return $stmt->execute([$subtotal, $vat_total, $total, $id]);
    }

    public static function changeStatus($id, $user_id, $status) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE budgets
            SET status = ?
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$status, $id, $user_id]);
    }

    public static function findWithClient($id, $user_id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT b.*, c.name AS client_name, c.email, c.tax_id
            FROM budgets b
            JOIN clients c ON c.id = b.client_id
            WHERE b.id = ? AND b.user_id = ?
        ");
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch();
    }
}