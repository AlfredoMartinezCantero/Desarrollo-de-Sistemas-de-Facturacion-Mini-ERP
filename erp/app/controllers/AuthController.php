<?php

require_once __DIR__ . '/../models/User.php';

class AuthController {

    public static function login() {
        // 1. CREACIÓN DEL USUARIO ADMIN POR DEFECTO
        $pdo = Database::getConnection();
        $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        
        if ($count == 0) {
            // Creamos el usuario base
            User::create('Administrador', 'admin@erp.com', 'admin123');
            // Forzamos que tenga el rol 'admin'
            $pdo->exec("UPDATE users SET role = 'admin' WHERE id = 1");
        }

        // 2. PROCESAR EL LOGIN
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!csrf_check($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }

            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $user = User::findByEmail($email);

            // Verificamos si existe el usuario y si la contraseña coincide
            if ($user && password_verify($password, $user['password'])) {
                // Guardamos la sesión
                $_SESSION['user'] = [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'role'  => $user['role']
                ];
                
                // Redirigimos al dashboard (sin usar echo)
                header('Location: index.php?action=dashboard');
                exit;
            }

            $error = "🔴 Credenciales incorrectas";
        }

        // 3. VISTA HTML PURA (Sin Bootstrap)
        require __DIR__ . '/../views/layout/header.php';
        ?>
        
        <div style="max-width: 400px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2 style="text-align: center; margin-bottom: 20px;">Iniciar Sesión</h2>
            
            <?php if (!empty($error)): ?>
                <div style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($count == 0): ?>
                <div style="color: #155724; background-color: #d4edda; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                    <strong>¡Base de datos lista!</strong><br>
                    Se ha creado tu usuario de acceso:<br><br>
                    Email: <b>admin@erp.com</b><br>
                    Contraseña: <b>admin123</b>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Email</label>
                    <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Contraseña</label>
                    <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                </div>
                
                <button type="submit" style="width: 100%; padding: 12px; background: #333; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
                    Entrar al ERP
                </button>
            </form>
        </div>

        <?php
        require __DIR__ . '/../views/layout/footer.php';
    }

    public static function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}