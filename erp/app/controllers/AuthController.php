<?php

require_once __DIR__ . '/../models/User.php';

class AuthController {

    public static function login() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!csrf_check($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }

            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $user = User::findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'role'  => $user['role']
                ];

                echo "🟢 Login correcto. Usuario autenticado.";
                exit;
            }

            echo "🔴 Credenciales incorrectas";
        }

        // Formulario simple
        echo '
        <h2>Login</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="'.csrf_token().'">
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Contraseña" required><br><br>
            <button type="submit">Entrar</button>
        </form>';
    }

    public static function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}