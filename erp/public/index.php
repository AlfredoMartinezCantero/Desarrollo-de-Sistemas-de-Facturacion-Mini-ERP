<?php
// Cuando tenga el login acceder manualmente a index.php?action=dashboard

// Para acceder a Company.php index.php?action=company

// Acceso Admin index.php?action=admin_users
session_start();

// Autoload muy simple
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Invoice.php';

// Controladores
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ClientController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/BudgetController.php';
require_once __DIR__ . '/../app/controllers/InvoiceController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/PaymentController.php';
require_once __DIR__ . '/../app/controllers/CompanyController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';


// Router básico por GET
$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        AuthController::login();
        break;

    case 'logout':
        AuthController::logout();
        break;

    case 'clients':
        ClientController::index();
        break;

    case 'clients_create':
        ClientController::create();
        break;

    case 'clients_edit':
        ClientController::edit();
        break;

    case 'clients_delete':
        ClientController::delete();
        break;

    case 'products':
        ProductController::index();
        break;

    case 'products_create':
        ProductController::create();
        break;

    case 'products_edit':
        ProductController::edit();
        break;

    case 'products_delete':
        ProductController::delete();
        break;
    
    case 'budgets':
        BudgetController::index();
        break;

    case 'budgets_create':
        BudgetController::create();
        break;

    case 'budget_status':
        BudgetController::status();
        break;

    case 'budget_show':
        BudgetController::show();
        break;

    case 'budget_to_invoice':
        BudgetController::toInvoice();
        break;

    case 'invoices':
        InvoiceController::index();
        break;

    case 'invoices_show':
        InvoiceController::show();
        break;

    case 'invoice_status':
        InvoiceController::status();
        break;

    case 'dashboard':
        DashboardController::index();
        break;

    case 'invoice_pdf':
        InvoiceController::pdf();
        break;

    case 'invoice_email':
        InvoiceController::sendEmail();
        break;

    case 'payment_store':
        PaymentController::store();
        break;

    case 'company':
        CompanyController::edit();
        break;

    case 'admin_users':
        AdminController::users();
        break;

    case 'admin_role':
        AdminController::role();
        break;

    case 'admin_status':
        AdminController::status();
        break;
    default:
        echo "Ruta no encontrada";
}