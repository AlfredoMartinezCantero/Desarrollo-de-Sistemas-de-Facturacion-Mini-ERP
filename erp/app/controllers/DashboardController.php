<?php

class DashboardController {

    public static function index() {
        require_auth();

        $user_id = auth_user()['id'];
        $pdo = Database::getConnection();

        // FACTURACIÓN TOTAL
        $stmt = $pdo->prepare("
            SELECT 
                SUM(total) AS total,
                SUM(CASE WHEN status = 'paid' THEN total ELSE 0 END) AS paid,
                SUM(CASE WHEN status = 'pending' THEN total ELSE 0 END) AS pending
            FROM invoices
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        $totals = $stmt->fetch();

        // CLIENTES ACTIVOS
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT client_id) 
            FROM invoices
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        $clients = $stmt->fetchColumn();

        // FACTURACIÓN MENSUAL
        $stmt = $pdo->prepare("
            SELECT 
                DATE_FORMAT(issue_date, '%Y-%m') AS month,
                SUM(total) AS total
            FROM invoices
            WHERE user_id = ?
            GROUP BY month
            ORDER BY month
        ");
        $stmt->execute([$user_id]);
        $monthly = $stmt->fetchAll();

        // Preparar datos para Chart.js
        $months = [];
        $amounts = [];

        foreach ($monthly as $m) {
            $months[] = $m['month'];
            $amounts[] = $m['total'];
        }

        // VISTA TÉCNICA
        echo "
        <h2>Dashboard</h2>

        <p><strong>Facturación total:</strong> {$totals['total']} €</p>
        <p><strong>Total cobrado:</strong> {$totals['paid']} €</p>
        <p><strong>Pendiente de cobro:</strong> {$totals['pending']} €</p>
        <p><strong>Clientes activos:</strong> {$clients}</p>

        <hr>

        <h3>Facturación mensual</h3>

        <canvas id='chart' width='400' height='200'></canvas>

        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>

        <script>
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ".json_encode($months).",
                datasets: [{
                    label: 'Facturación (€)',
                    data: ".json_encode($amounts).",
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            }
        });
        </script>
        ";
    }
}