<?php

require_once __DIR__ . '/../models/Product.php';

class ProductController {

    public static function index() {
        require_auth();

        $products = Product::allByUser(auth_user()['id']);

        echo "<h2>Productos / Servicios</h2>";
        echo "index.php?action=products_create➕ Nuevo producto</a><br><br>";

        if (!$products) {
            echo "No hay productos todavía.";
            return;
        }

        echo "<table border='1' cellpadding='5'>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>IVA %</th>
                    <th>Unidad</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>";

        foreach ($products as $p) {
            echo "<tr>
                    <td>{$p['name']}</td>
                    <td>{$p['price']} €</td>
                    <td>{$p['vat_percent']}%</td>
                    <td>{$p['unit']}</td>
                    <td>".($p['stock'] ?? '—')."</td>
                    <td>
                        index.php?action=products_edit&id={$p['id']}✏️</a>
                        |
                        index.php?action=products_delete&id={$p['id']} onclick=\"return confirm('¿Eliminar producto?')\">🗑️</a>
                    </td>
                  </tr>";
        }

        echo "</table>";
    }

    public static function create() {
        require_auth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!csrf_check($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }

            $data = [
                'user_id'     => auth_user()['id'],
                'name'        => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price'       => (float) $_POST['price'],
                'vat_percent' => (float) $_POST['vat_percent'],
                'unit'        => trim($_POST['unit']),
                'stock'       => $_POST['stock'] !== '' ? (int) $_POST['stock'] : null
            ];

            if ($data['name'] === '' || $data['price'] <= 0) {
                echo "🔴 Nombre y precio son obligatorios";
                return;
            }

            Product::create($data);
            header('Location: index.php?action=products');
            exit;
        }

        echo "
        <h2>Nuevo producto</h2>
        <form method='POST'>
            <input type='hidden' name='csrf_token' value='".csrf_token()."'>

            <input name='name' placeholder='Nombre' required><br><br>
            <textarea name='description' placeholder='Descripción'></textarea><br><br>
            <input type='number' step='0.01' name='price' placeholder='Precio' required><br><br>
            <input type='number' step='0.01' name='vat_percent' value='21'><br><br>
            <input name='unit' value='ud'><br><br>
            <input type='number' name='stock' placeholder='Stock (opcional)'><br><br>

            <button type='submit'>Guardar</button>
        </form>
        index.php?action=products⬅ Volver</a>
        ";
    }

    public static function edit() {
        require_auth();

        $id = $_GET['id'] ?? null;
        $product = Product::find($id, auth_user()['id']);

        if (!$product) {
            echo "Producto no encontrado";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!csrf_check($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }

            $data = [
                'name'        => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price'       => (float) $_POST['price'],
                'vat_percent' => (float) $_POST['vat_percent'],
                'unit'        => trim($_POST['unit']),
                'stock'       => $_POST['stock'] !== '' ? (int) $_POST['stock'] : null
            ];

            Product::update($id, auth_user()['id'], $data);
            header('Location: index.php?action=products');
            exit;
        }

        echo "
        <h2>Editar producto</h2>
        <form method='POST'>
            <input type='hidden' name='csrf_token' value='".csrf_token()."'>

            <input name='name' value='{$product['name']}' required><br><br>
            <textarea name='description'>{$product['description']}</textarea><br><br>
            <input type='number' step='0.01' name='price' value='{$product['price']}' required><br><br>
            <input type='number' step='0.01' name='vat_percent' value='{$product['vat_percent']}'><br><br>
            <input name='unit' value='{$product['unit']}'><br><br>
            <input type='number' name='stock' value='{$product['stock']}'><br><br>

            <button type='submit'>Actualizar</button>
        </form>
        ";
    }

    public static function delete() {
        require_auth();

        $id = $_GET['id'] ?? null;
        Product::delete($id, auth_user()['id']);

        header('Location: index.php?action=products');
        exit;
    }
}