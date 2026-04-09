<?php

require_once __DIR__ . '/../models/Company.php';

class CompanyController {

    public static function edit() {
        require_auth();

        $user_id = auth_user()['id'];
        $company = Company::findByUser($user_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!csrf_check($_POST['csrf_token'])) {
                die('CSRF inválido');
            }

            Company::save($user_id, [
                'company_name' => trim($_POST['company_name']),
                'tax_id'       => trim($_POST['tax_id']),
                'address'      => trim($_POST['address']),
                'email'        => trim($_POST['email']),
                'phone'        => trim($_POST['phone'])
            ]);

            echo "✅ Datos fiscales guardados";
        }

        // Formulario técnico
        echo "
        <h2>Datos fiscales del emisor</h2>

        <form method='POST'>
            <input type='hidden' name='csrf_token' value='".csrf_token()."'>

            <input name='company_name' placeholder='Nombre fiscal'
                value='".($company['company_name'] ?? '')."' required><br><br>

            <input name='tax_id' placeholder='CIF / NIF'
                value='".($company['tax_id'] ?? '')."' required><br><br>

            <textarea name='address' placeholder='Dirección' required>"
                .($company['address'] ?? '')."</textarea><br><br>

            <input type='email' name='email' placeholder='Email'
                value='".($company['email'] ?? '')."'><br><br>

            <input name='phone' placeholder='Teléfono'
                value='".($company['phone'] ?? '')."'><br><br>

            <button type='submit'>Guardar</button>
        </form>
        ";
    }
}