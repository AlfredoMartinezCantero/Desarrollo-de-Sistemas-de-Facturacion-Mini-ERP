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

        // Estadísticas
        $stats = $pdo->query("
            SELECT
                (SELECT COUNT(*) FROM users) AS users,
                (SELECT COUNT(*) FROM invoices) AS invoices,
                (SELECT SUM(total) FROM invoices WHERE status='paid') AS revenue
        ")->fetch();

        // Cargar Vistas con CSS
        require __DIR__ . '/../views/layout/header.php';
        ?>
        <div class="card shadow-sm p-4">
            <h2 class="mb-4">Panel de Administración - Usuarios</h2>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-bg-light p-3">
                        <small class="text-muted">Usuarios Totales</small>
                        <h4 class="mb-0"><?= $stats['users'] ?></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-bg-light p-3">
                        <small class="text-muted">Facturas</small>
                        <h4 class="mb-0"><?= $stats['invoices'] ?></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-bg-success p-3">
                        <small class="text-white-50">Ingresos Totales</small>
                        <h4 class="mb-0 text-white"><?= number_format($stats['revenue'] ?? 0, 2) ?> €</h4>
                    </div>
                </div>
            </div>

            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): 
                        $newRole = $u['role'] == 'admin' ? 'user' : 'admin';
                        $newStatus = $u['active'] ? 0 : 1;
                        $statusText = $u['active'] ? 'Desactivar' : 'Activar';
                        $statusClass = $u['active'] ? 'bg-success' : 'bg-danger';
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= $u['role'] ?></span></td>
                        <td><span class="badge <?= $statusClass ?>"><?= $u['active'] ? 'Activo' : 'Inactivo' ?></span></td>
                        <td class="text-end">
                            <a href="index.php?action=admin_role&id=<?= $u['id'] ?>&role=<?= $newRole ?>" class="btn btn-sm btn-outline-primary">Cambiar rol</a>
                            <a href="index.php?action=admin_status&id=<?= $u['id'] ?>&active=<?= $newStatus ?>" class="btn btn-sm btn-outline-warning"><?= $statusText ?></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        require __DIR__ . '/../views/layout/footer.php';
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