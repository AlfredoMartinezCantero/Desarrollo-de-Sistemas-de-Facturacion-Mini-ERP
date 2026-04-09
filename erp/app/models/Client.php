<?php

class Client {
    public static function allByUser($user_id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM clients
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public static function create($data) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO clients (user_id, name, tax_id, email, phone, adress)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['user_id'],
            $data['name'],
            $data['tax_id'],
            $data['email'],
            $data['phone'],
            $data['adress']
        ]);
    }

    public static function find($id, $user_id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM clients
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch();
    }

    public static function update($id, $user_id, $data) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE clients 
            SET name = ?, tax_id = ?, email = ?, phone = ?, address = ?
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['tax_id'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $id,
            $user_id
        ]);
}

    public static function delete($id, $user_id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            DELETE FROM clients 
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$id, $user_id]);
    }
}