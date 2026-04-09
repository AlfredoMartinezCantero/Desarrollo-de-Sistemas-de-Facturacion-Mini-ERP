<?php

require_once __DIR__ . '/../models/User.php';

class AdminController {

    private static function requireAdmin() {
        if (!auth_user() || auth_user()['role'] !== 'admin') {
            die('Acceso restringido');
        }
    }

    public static function users() {
        require_auth();
        self::requireAdmin();

        $users = User::all();
        
        $pdo = Database::getConnection();

        $stats = $pdo->query("
            SELECT
                (SELECT COUNT(*) FROM users) AS users,
                (SELECT COUNT(*) FROM invoices) AS invoices,
                (SELECT SUM(total) FROM invoices WHERE status='paid') AS revenue
        ")->fetch();

        echo "<h2>Panel de administración - Usuarios</h2>";
        
        echo "
        <p><strong>Usuarios:</strong> {$stats['users']}</p>
        <p><strong>Facturas:</strong> {$stats['invoices']}</p>
        <p><strong>Ingresos totales:</strong> {$stats['revenue']} €</p>
        <hr>
        ";

        echo "
        <table border='1' cellpadding='5'>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>";

        foreach ($users as $u) {
            $newRole = $u['role'] == 'admin' ? 'user' : 'admin';
            $newStatus = $u['active'] ? 0 : 1;
            $statusText = $u['active'] ? 'Desactivar' : 'Activar';

            echo "
            <tr>
                <td>{$u['name']}</td>
                <td>{$u['email']}</td>
                <td>{$u['role']}</td>
                <td>".($u['active'] ? 'Activo' : 'Inactivo')."</td>
                <td>
                    <a href='index.php?action=admin_role&id={$u['id']}&role={$newRole}'>Cambiar rol</a>
                    |
                    <a href='index.php?action=admin_status&id={$u['id']}&active={$newStatus}'>{$statusText}</a>
                </td>
            </tr>";
        }

        echo "</table>";
    }

    public static function role() {
        require_auth();
        self::requireAdmin();

        User::changeRole($_GET['id'], $_GET['role']);
        header('Location: index.php?action=admin_users');
        exit;
    }

    public static function status() {
        require_auth();
        self::requireAdmin();

        User::changeStatus($_GET['id'], $_GET['active']);
        header('Location: index.php?action=admin_users');
        exit;
    }
}