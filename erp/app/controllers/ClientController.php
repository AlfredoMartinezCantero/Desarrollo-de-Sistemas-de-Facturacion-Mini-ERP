<?php
require_once __DIR__ . '/../models/Client.php';

class ClientController {

    public static function index() {
        require_auth();
        $user = auth_user();
        $clients = Client::allByUser($user['id']);

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/clients/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public static function create() {
        require_auth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_check($_POST['csrf_token'] ?? '')) {
                die('CSRF inválido');
            }

            $data = [
                'user_id' => auth_user()['id'],
                'name'    => trim($_POST['name']),
                'tax_id'  => trim($_POST['tax_id']),
                'email'   => trim($_POST['email']),
                'phone'   => trim($_POST['phone']),
                'address' => trim($_POST['address']),
            ];

            if ($data['name'] === '') {
                die("🔴 El nombre es obligatorio");
            }

            Client::create($data);
            header('Location: index.php?action=clients');
            exit;
        }

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/clients/create.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public static function edit() {
        require_auth();
        $user = auth_user();
        $id = $_GET['id'] ?? null;

        $client = Client::find($id, $user['id']);
        if (!$client) { die("Cliente no encontrado"); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'    => trim($_POST['name']),
                'tax_id'  => trim($_POST['tax_id']),
                'email'   => trim($_POST['email']),
                'phone'   => trim($_POST['phone']),
                'address' => trim($_POST['address']),
            ];
            Client::update($id, $user['id'], $data);
            header('Location: index.php?action=clients');
            exit;
        }

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/clients/edit.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}