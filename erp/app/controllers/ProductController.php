<?php

require_once __DIR__ . '/../models/Product.php';

class ProductController {

    public static function index() {
        require_auth();
        $products = Product::allByUser(auth_user()['id']);

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/products/index.php';
        require __DIR__ . '/../views/layout/footer.php';
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
                die("🔴 Nombre y precio son obligatorios");
            }

            Product::create($data);
            header('Location: index.php?action=products');
            exit;
        }

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/products/create.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public static function edit() {
        require_auth();
        $id = $_GET['id'] ?? null;
        $product = Product::find($id, auth_user()['id']);

        if (!$product) {
            die("Producto no encontrado");
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

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/products/edit.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public static function delete() {
        require_auth();
        $id = $_GET['id'] ?? null;
        Product::delete($id, auth_user()['id']);

        header('Location: index.php?action=products');
        exit;
    }
}