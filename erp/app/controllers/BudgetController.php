<?php

require_once __DIR__ . '/../models/Budget.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Invoice.php';

class BudgetController {

    public static function index() {
        require_auth();
        $budgets = Budget::allByUser(auth_user()['id']);

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/budgets/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public static function create() {
        require_auth();
        $user = auth_user();
        $clients  = Client::allByUser($user['id']);
        $products = Product::allByUser($user['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_check($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }

            $items = $_POST['items'] ?? [];
            $subtotal  = 0;
            $vat_total = 0;
            $lines     = [];

            foreach ($items as $item) {
                if (empty($item['product_id']) || empty($item['qty'])) continue;

                $product = Product::find($item['product_id'], $user['id']);
                if (!$product) continue;

                $qty = (float) $item['qty'];
                $price = (float) $product['price'];
                $vat = (float) $product['vat_percent'];

                $line_total = $qty * $price;
                $line_vat = $line_total * ($vat / 100);

                $subtotal += $line_total;
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
                die("🔴 El presupuesto debe tener al menos una línea válida.");
            }

            $budget_id = Budget::create([
                'user_id'   => $user['id'],
                'client_id' => $_POST['client_id'],
                'number'    => 'PRE-' . date('Y') . '-' . rand(1000, 9999),
                'subtotal'  => $subtotal,
                'vat_total' => $vat_total,
                'total'     => $subtotal + $vat_total
            ]);

            foreach ($lines as $line) {
                Budget::addItem($budget_id, $line);
            }

            header('Location: index.php?action=budgets');
            exit;
        }

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/budgets/create.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public static function show() {
        require_auth();
        $user = auth_user();
        $id = $_GET['id'] ?? null;

        $budget = Budget::findWithClient($id, $user['id']);
        if (!$budget) die("Presupuesto no encontrado");

        $items = Budget::items($id);

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/budgets/show.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public static function toInvoice() {
        require_auth();
        $user = auth_user();
        $id = $_GET['id'] ?? null;

        $budget = Budget::findWithClient($id, $user['id']);
        if (!$budget || $budget['status'] !== 'approved') {
            die("Solo se pueden facturar presupuestos aprobados.");
        }

        $items = Budget::items($id);
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