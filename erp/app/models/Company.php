<?php

class Company {

    public static function findByUser($user_id) {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT * FROM company_settings
            WHERE user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    public static function save($user_id, $data) {
        $pdo = Database::getConnection();

        // Si existe -> UPDATE
        if (self::findByUser($user_id)) {
            $stmt = $pdo->prepare("
                UPDATE company_settings
                SET company_name = ?, tax_id = ?, address = ?, email = ?, phone = ?
                WHERE user_id = ?
            ");
            return $stmt->execute([
                $data['company_name'],
                $data['tax_id'],
                $data['address'],
                $data['email'],
                $data['phone'],
                $user_id
            ]);
        }

        // Si no existe -> INSERT
        $stmt = $pdo->prepare("
            INSERT INTO company_settings
            (user_id, company_name, tax_id, address, email, phone)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $user_id,
            $data['company_name'],
            $data['tax_id'],
            $data['address'],
            $data['email'],
            $data['phone']
        ]);
    }
}