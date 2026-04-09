<?php

require_once __DIR__ . '/../models/Client.php';

class ClientController {


    public static function index() {
        require_auth();

    $user = auth_user();
    $clients = Client::allByUser($user['id']);

    echo "<h2>Clientes</h2>";
    echo "<a href='index.php?action=clients_create'>➕ Nuevo cliente</a><br><br>";

    if (!$clients) {
        echo "No hay clientes todavía.";
        return;
    }

    echo "<table border='1' cellpadding='5'>
            <tr>
                <th>Nombre</th>
                <th>CIF/NIF</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>";

    foreach ($clients as $client) {
        echo "<tr>
                <td>{$client['name']}</td>
                <td>{$client['tax_id']}</td>
                <td>{$client['email']}</td>
                <td>{$client['phone']}</td>
                <td>
                    <a href='index.php?action=clients_edit&id={$client['id']}'>✏️ Editar</a>
                    |
                    <a href='index.php?action=clients_delete&id={$client['id']}'
                       onclick=\"return confirm('¿Seguro que deseas eliminar este cliente?')\">
                       🗑️ Borrar
                    </a>
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
                'user_id' => auth_user()['id'],
                'name'    => trim($_POST['name']),
                'tax_id'  => trim($_POST['tax_id']),
                'email'   => trim($_POST['email']),
                'phone'   => trim($_POST['phone']),
                'address' => trim($_POST['address']),
            ];

            if ($data['name'] === '') {
                echo "🔴 El nombre es obligatorio";
                return;
            }

            Client::create($data);
            header('Location: index.php?action=clients');
            exit;
        }

        // Formulario
        echo "
        <h2>Nuevo cliente</h2>
        <form method='POST'>
            <input type='hidden' name='csrf_token' value='".csrf_token()."'>

            <input type='text' name='name' placeholder='Nombre' required><br><br>
            <input type='text' name='tax_id' placeholder='CIF / NIF'><br><br>
            <input type='email' name='email' placeholder='Email'><br><br>
            <input type='text' name='phone' placeholder='Teléfono'><br><br>
            <textarea name='address' placeholder='Dirección'></textarea><br><br>

            <button type='submit'>Guardar</button>
        </form>
        <br>
        <a href='index.php?action=clients'>⬅ Volver</a>
        ";
    }

    public static function edit() {
    require_auth();

    $user = auth_user();
    $id = $_GET['id'] ?? null;

    if (!$id) {
        echo "Cliente no válido";
        return;
    }

    $client = Client::find($id, $user['id']);

    if (!$client) {
        echo "Cliente no encontrado";
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (!csrf_check($_POST['csrf_token'] ?? '')) {
            die('CSRF inválido');
        }

        $data = [
            'name'    => trim($_POST['name']),
            'tax_id'  => trim($_POST['tax_id']),
            'email'   => trim($_POST['email']),
            'phone'   => trim($_POST['phone']),
            'address' => trim($_POST['address']),
        ];

        if ($data['name'] === '') {
            echo "🔴 El nombre es obligatorio";
            return;
        }

        Client::update($id, $user['id'], $data);
        header('Location: index.php?action=clients');
        exit;
    }

        // Formulario con datos cargados
        echo "
        <h2>Editar cliente</h2>
        <form method='POST'>
            <input type='hidden' name='csrf_token' value='".csrf_token()."'>

            <input type='text' name='name' value='{$client['name']}' required><br><br>
            <input type='text' name='tax_id' value='{$client['tax_id']}'><br><br>
            <input type='email' name='email' value='{$client['email']}'><br><br>
            <input type='text' name='phone' value='{$client['phone']}'><br><br>
            <textarea name='address'>{$client['address']}</textarea><br><br>

            <button type='submit'>Actualizar</button>
        </form>
        <br>
        <a href='index.php?action=clients'>⬅ Volver</a>
        ";
    }

    public static function delete() {
    require_auth();

        $user = auth_user();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "Cliente no válido";
            return;
        }

        Client::delete($id, $user['id']);
        header('Location: index.php?action=clients');
        exit;
    }
}