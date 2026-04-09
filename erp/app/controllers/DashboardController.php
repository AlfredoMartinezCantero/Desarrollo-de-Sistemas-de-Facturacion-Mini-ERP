<?php

class DashboardController {

    public static function index() {
        require_auth();
        $user_id = auth_user()['id'];
        $pdo = Database::getConnection();

        // FACTURACIÓN TOTAL
        $stmt = $pdo->prepare("SELECT SUM(total) AS total, SUM(CASE WHEN status = 'paid' THEN total ELSE 0 END) AS paid, SUM(CASE WHEN status = 'pending' THEN total ELSE 0 END) AS pending FROM invoices WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $totals = $stmt->fetch();

        // CLIENTES ACTIVOS
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT client_id) FROM invoices WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $clients = $stmt->fetchColumn();

        // FACTURACIÓN MENSUAL
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(issue_date, '%Y-%m') AS month, SUM(total) AS total FROM invoices WHERE user_id = ? GROUP BY month ORDER BY month");
        $stmt->execute([$user_id]);
        $monthly = $stmt->fetchAll();

        $months = [];
        $amounts = [];
        foreach ($monthly as $m) {
            $months[] = $m['month'];
            $amounts[] = $m['total'];
        }

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/dashboard/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}