<?php

require_once __DIR__ . '/../models/Budget.php';
require_once __DIR__ . '/../models/Client.php';

class BudgetController {

    public static function index() {
        require_auth();

        $budgets = Budget::allByUser(auth_user()['id']);

        echo "<h2>Presupuestos</h2>";
        echo "index.php?action=budgets_create➕ Nuevo presupuesto</a><br><br>";

        if (!$budgets) {
            echo "No hay presupuestos.";
            return;
        }

        echo "<table border='1' cellpadding='5'>
                <tr>
                    <th>Nº</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>";

        foreach ($budgets as $b) {
            echo "<tr>
                    <td>{$b['number']}</td>
                    <td>{$b['client_name']}</td>
                    <td>{$b['total']} €</td>
                    <td>{$b['status']}</td>
                  </tr>";
        }

        echo "</table>";
    }

    public static function create() {
        require_auth();

        $clients = Client::allByUser(auth_user()['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!csrf_check($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }

            $items = $_POST['items'];

            $subtotal = 0;
            $vat_total = 0;

            foreach ($items as $item) {
                $line = $item['qty'] * $item['price'];
                $vat  = $line * ($item['vat'] / 100);

                $subtotal += $line;
                $vat_total += $vat;
            }

            $data = [
                'user_id'   => auth_user()['id'],
                'client_id' => $_POST['client_id'],
                'number'    => 'PRE-' . date('Y') . '-' . rand(1000, 9999),
                'subtotal'  => $subtotal,
                'vat_total' => $vat_total,
                'total'     => $subtotal + $vat_total
            ];

            $budget_id = Budget::create($data);

            foreach ($items as $item) {
                Budget::addItem($budget_id, [
                    'name'  => $item['name'],
                    'qty'   => $item['qty'],
                    'price' => $item['price'],
                    'vat'   => $item['vat'],
                    'total' => $item['qty'] * $item['price']
                ]);
            }

            header('Location: index.php?action=budgets');
            exit;
        }

        // Formulario técnico
        echo "
        <h2>Nuevo presupuesto</h2>
        <form method='POST'>
            <input type='hidden' name='csrf_token' value='".csrf_token()."'>

            <select name='client_id' required>
                <option value=''>-- Cliente --</option>";

        foreach ($clients as $c) {
            echo "<option value='{$c['id']}'>{$c['name']}</option>";
        }

        echo "
            </select><br><br>

            <h4>Líneas</h4>

            <input name='items[0][name]' placeholder='Concepto'><br>
            <input type='number' step='0.01' name='items[0][qty]' placeholder='Cantidad'><br>
            <input type='number' step='0.01' name='items[0][price]' placeholder='Precio'><br>
            <input type='number' step='0.01' name='items[0][vat]' value='21'><br><br>

            <button type='submit'>Guardar presupuesto</button>
        </form>
        ";
    }
}
