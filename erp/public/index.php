<?php

session_start();

// Autoload muy simple
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/helpers/functions.php';

// Controladores
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ClientController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/BudgetController.php';


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

    default:
        echo "Ruta no encontrada";
}