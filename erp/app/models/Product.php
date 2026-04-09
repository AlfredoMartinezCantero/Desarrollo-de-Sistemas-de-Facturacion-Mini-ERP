<?php

class Product {

    public static function allByUser($user_id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM products
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public static function find($id, $user_id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM products
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO products 
            (user_id, name, description, price, vat_percent, unit, stock)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['user_id'],
            $data['name'],
            $data['description'],
            $data['price'],
            $data['vat_percent'],
            $data['unit'],
            $data['stock']
        ]);
    }

    public static function update($id, $user_id, $data) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE products
            SET name = ?, description = ?, price = ?, vat_percent = ?, unit = ?, stock = ?
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['vat_percent'],
            $data['unit'],
            $data['stock'],
            $id,
            $user_id
        ]);
    }

    public static function delete($id, $user_id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            DELETE FROM products
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$id, $user_id]);
    }
}