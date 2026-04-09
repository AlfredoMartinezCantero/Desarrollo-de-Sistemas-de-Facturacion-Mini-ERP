<?php
require_once __DIR__ . '/../models/Company.php';

class CompanyController {

    public static function edit() {
        require_auth();
        $user_id = auth_user()['id'];
        $company = Company::findByUser($user_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Company::save($user_id, [
                'company_name' => trim($_POST['company_name']),
                'tax_id'       => trim($_POST['tax_id']),
                'address'      => trim($_POST['address']),
                'email'        => trim($_POST['email']),
                'phone'        => trim($_POST['phone'])
            ]);
            header('Location: index.php?action=company&success=1');
            exit;
        }

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/company/edit.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}