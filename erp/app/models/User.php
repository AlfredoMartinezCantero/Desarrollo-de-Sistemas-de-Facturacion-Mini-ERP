<?php

class User {
    public static function findByEmail($email) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]); // ¡ESTA ES LA LÍNEA QUE FALTABA!
        return $stmt->fetch();
    }

    public static function create($name, $email, $password) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password) VALUES (?,?,?)
        ");
        // ¡CORREGIDO: era $stmt, no $stms!
        return $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
    
    public static function all() {
        $pdo = Database::getConnection();
        return $pdo->query("
            SELECT id, name, email, role, active, created_at
            FROM users
            ORDER BY created_at DESC
        ")->fetchAll();
    }

    public static function changeRole($id, $role) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE users SET role = ? WHERE id = ?
        ");
        return $stmt->execute([$role, $id]);
    }

    public static function changeStatus($id, $active) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE users SET active = ? WHERE id = ?
        ");
        return $stmt->execute([$active, $id]);
    }
}