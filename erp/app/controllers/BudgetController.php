<?php

require_once __DIR__ . '/../models/Budget.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Invoice.php'; // Asegurarse de importar Invoice

class BudgetController {

    public static function index() {
        require_auth();

        $budgets = Budget::allByUser(auth_user()['id']);

        echo "<h2>Presupuestos</h2>";
        echo "<a href='index.php?action=budgets_create'>➕ Nuevo presupuesto</a><br><br>";

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
                    <td>
                        <a href='index.php?action=budget_show&id={$b['id']}'>
                            {$b['number']}
                        </a>
                    </td>
                    <td>{$b['client_name']}</td>
                    <td>{$b['total']} €</td>
                    <td>{$b['status']}</td>
                </tr>";
        }
        echo "</table>";
    }

    public static function create() {
        require_auth();

        $user = auth_user();

        // Cargamos clientes y productos del usuario
        $clients  = Client::allByUser($user['id']);
        $products = Product::allByUser($user['id']);

        // POST -> guardar presupuesto
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!csrf_check($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }

            $items = $_POST['items'] ?? [];

            $subtotal  = 0;
            $vat_total = 0;
            $lines     = [];

            foreach ($items as $item) {

                if (empty($item['product_id']) || empty($item['qty'])) {
                    continue;
                }

                $product = Product::find($item['product_id'], $user['id']);
                if (!$product) {
                    continue;
                }

                $qty   = (float) $item['qty'];
                $price = (float) $product['price'];
                $vat   = (float) $product['vat_percent'];

                $line_total = $qty * $price;
                $line_vat   = $line_total * ($vat / 100);

                $subtotal  += $line_total;
                $vat_total += $line_vat;

                $lines[] = [
                    'name'  => $product['name'],
                    'qty'   => $qty,
                    'price' => $price,
                    'vat'   => $vat,
                    'total' => $line_total
                ];
            }

            if (!$lines) {
                echo "🔴 El presupuesto debe tener al menos una línea";
                return;
            }

            $data = [
                'user_id'   => $user['id'],
                'client_id' => $_POST['client_id'],
                'number'    => 'PRE-' . date('Y') . '-' . rand(1000, 9999),
                'subtotal'  => $subtotal,
                'vat_total' => $vat_total,
                'total'     => $subtotal + $vat_total
            ];

            $budget_id = Budget::create($data);

            foreach ($lines as $line) {
                Budget::addItem($budget_id, $line);
            }

            header('Location: index.php?action=budgets');
            exit;
        }

        // GET -> formulario técnico
        echo "
        <h2>Nuevo presupuesto</h2>

        <form method='POST'>
            <input type='hidden' name='csrf_token' value='".csrf_token()."'>

            <label>Cliente</label><br>
            <select name='client_id' required>
                <option value=''>-- Cliente --</option>";

        foreach ($clients as $c) {
            echo "<option value='{$c['id']}'>{$c['name']}</option>";
        }

        echo "
            </select>
            <br><br>

            <h4>Líneas del presupuesto</h4>";

        // 3 líneas fijas (backend puro, sin JS todavía)
        for ($i = 0; $i < 3; $i++) {
            echo "
            <div style='margin-bottom:10px'>
                <select name='items[$i][product_id]'>
                    <option value=''>-- Producto --</option>";
            foreach ($products as $p) {
                echo "<option value='{$p['id']}'>{$p['name']} ({$p['price']} €)</option>";
            }
            echo "
                </select>

                <input 
                    type='number' 
                    step='0.01' 
                    name='items[$i][qty]' 
                    placeholder='Cantidad'
                >
            </div>";
        }

        echo "
            <br>
            <button type='submit'>Guardar presupuesto</button>
        </form>

        <br>
        <a href='index.php?action=budgets'>⬅ Volver</a>
        ";
    }

    public static function show() {
        require_auth();

        $user = auth_user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "Presupuesto no válido";
            return;
        }

        $budget = Budget::findWithClient($id, $user['id']);
        if (!$budget) {
            echo "Presupuesto no encontrado";
            return;
        }

        $items = Budget::items($id);

        echo "<h2>Presupuesto {$budget['number']}</h2>";

        echo "
        <p>
            <strong>Cliente:</strong> {$budget['client_name']}<br>
            <strong>CIF/NIF:</strong> {$budget['tax_id']}<br>
            <strong>Email:</strong> {$budget['email']}<br>
            <strong>Estado:</strong> {$budget['status']}
        </p>
        ";

        echo "
        <table border='1' cellpadding='5'>
            <tr>
                <th>Concepto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>IVA %</th>
                <th>Total</th>
            </tr>";

        foreach ($items as $item) {
            echo "
            <tr>
                <td>{$item['product_name']}</td>
                <td>{$item['quantity']}</td>
                <td>{$item['unit_price']} €</td>
                <td>{$item['vat_percent']}%</td>
                <td>{$item['line_total']} €</td>
            </tr>";
        }

        echo "
            <tr>
                <td colspan='4' align='right'><strong>Subtotal</strong></td>
                <td>{$budget['subtotal']} €</td>
            </tr>
            <tr>
                <td colspan='4' align='right'><strong>IVA</strong></td>
                <td>{$budget['vat_total']} €</td>
            </tr>
            <tr>
                <td colspan='4' align='right'><strong>Total</strong></td>
                <td><strong>{$budget['total']} €</strong></td>
            </tr>
        </table>
        ";

        // Botón Facturar
        if ($budget['status'] === 'approved') {
            echo "
            <br>
            <a href='index.php?action=budget_to_invoice&id={$budget['id']}'>🧾 Convertir en factura</a>
            ";
        }

        echo "
        <br><br>
        <a href='index.php?action=budgets'>⬅ Volver</a>
        ";
    }

    public static function toInvoice() {
        require_auth();

        $user = auth_user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "Presupuesto no válido";
            return;
        }

        $budget = Budget::findWithClient($id, $user['id']);
        if (!$budget) {
            echo "Presupuesto no encontrado";
            return;
        }

        if ($budget['status'] !== 'approved') {
            echo "🔴 Solo se pueden facturar presupuestos aprobados";
            return;
        }

        $items = Budget::items($id);

        // Generar número de factura
        $invoice_number = Invoice::nextNumber($user['id']);

        $invoice_id = Invoice::create([
            'user_id'       => $user['id'],
            'client_id'     => $budget['client_id'],
            'budget_id'     => $budget['id'],
            'invoice_number'=> $invoice_number,
            'subtotal'      => $budget['subtotal'],
            'vat_total'     => $budget['vat_total'],
            'total'         => $budget['total']
        ]);

        foreach ($items as $item) {
            Invoice::addItem($invoice_id, [
                'product_name' => $item['product_name'],
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['unit_price'],
                'vat_percent'  => $item['vat_percent'],
                'line_total'   => $item['line_total']
            ]);
        }

        header('Location: index.php?action=invoices_show&id=' . $invoice_id);
        exit;
    }

}